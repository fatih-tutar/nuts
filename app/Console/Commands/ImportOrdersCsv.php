<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Season;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportOrdersCsv extends Command
{
    protected $signature = 'orders:import-csv
                            {path? : CSV dosya yolu}
                            {--fresh : Mevcut müşteri/sezon/sipariş kayıtlarını silip yeniden yükle}';

    protected $description = 'Ceviz/badem sipariş CSV dosyasını içe aktarır';

    /** @var array<int, string> */
    private array $monthMap = [
        'ocak' => 1,
        'şubat' => 2,
        'subat' => 2,
        'mart' => 3,
        'nisan' => 4,
        'mayıs' => 5,
        'mayis' => 5,
        'haziran' => 6,
        'temmuz' => 7,
        'ağustos' => 8,
        'agustos' => 8,
        'eylül' => 9,
        'eylul' => 9,
        'ekim' => 10,
        'kasım' => 11,
        'kasim' => 11,
        'aralık' => 12,
        'aralik' => 12,
    ];

    public function handle(): int
    {
        $path = $this->argument('path')
            ?? database_path('data/ceviz-badem-siparis.csv');

        if (! is_readable($path)) {
            $this->error("CSV okunamadı: {$path}");

            return self::FAILURE;
        }

        $rows = $this->readCsv($path);
        if (count($rows) < 14) {
            $this->error('CSV beklenen yapıda değil (yetersiz satır).');

            return self::FAILURE;
        }

        DB::transaction(function () use ($rows): void {
            if ($this->option('fresh')) {
                Order::query()->delete();
                Season::query()->delete();
                Customer::query()->delete();
                $this->warn('Mevcut sipariş, sezon ve müşteri kayıtları silindi.');
            }

            $products = $this->ensureProducts();
            $seasonColumns = $this->parseSeasonColumns($rows, $products);

            $customersCreated = 0;
            $ordersCreated = 0;
            $ordersUpdated = 0;

            for ($i = 13; $i < count($rows); $i++) {
                $row = $rows[$i];
                $name = trim((string) ($row[0] ?? ''));
                if ($name === '') {
                    continue;
                }

                $phone = $this->nullableTrim($row[1] ?? null);
                $address = $this->nullableTrim($row[2] ?? null);

                $customer = Customer::query()->firstOrCreate(
                    ['name' => $name, 'phone' => $phone],
                    ['address' => $address, 'city' => null],
                );

                if ($customer->wasRecentlyCreated) {
                    $customersCreated++;
                } elseif ($address && blank($customer->address)) {
                    $customer->update(['address' => $address]);
                }

                foreach ($seasonColumns as $colIndex => $season) {
                    $qtyRaw = $row[$colIndex] ?? '';
                    $quantity = $this->parseNumber($qtyRaw);
                    if ($quantity === null || $quantity <= 0) {
                        continue;
                    }

                    $status = $this->statusForSeason($season);

                    $order = Order::query()->updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'season_id' => $season->id,
                        ],
                        [
                            'quantity_kg' => $quantity,
                            'status' => $status,
                            'ordered_on' => $season->order_start_date,
                        ],
                    );

                    if ($order->wasRecentlyCreated) {
                        $ordersCreated++;
                    } else {
                        $ordersUpdated++;
                    }
                }
            }

            $this->info("Ürünler: Ceviz, Badem hazır.");
            $this->info('Sezonlar: '.count($seasonColumns));
            $this->info("Müşteri (yeni): {$customersCreated}");
            $this->info("Sipariş (yeni/güncellenen): {$ordersCreated}/{$ordersUpdated}");
            $this->line('Durum kuralı: 2026 Ceviz → Sipariş verdi; diğerleri → Ödeme yapıldı.');
        });

        return self::SUCCESS;
    }

    /** @return array<int, array<int, string|null>> */
    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = $data;
        }
        fclose($handle);

        return $rows;
    }

    /** @return array{ceviz: Product, badem: Product} */
    private function ensureProducts(): array
    {
        $ceviz = Product::query()->firstOrCreate(
            ['slug' => 'ceviz'],
            ['name' => 'Ceviz', 'unit' => 'kg', 'is_active' => true, 'sort_order' => 1],
        );

        $badem = Product::query()->firstOrCreate(
            ['slug' => 'badem'],
            ['name' => 'Badem', 'unit' => 'kg', 'is_active' => true, 'sort_order' => 2],
        );

        return ['ceviz' => $ceviz, 'badem' => $badem];
    }

    /**
     * @param  array<int, array<int, string|null>>  $rows
     * @param  array{ceviz: Product, badem: Product}  $products
     * @return array<int, Season>
     */
    private function parseSeasonColumns(array $rows, array $products): array
    {
        $goldRow = $rows[0];
        $usdRow = $rows[1];
        $unitPriceRow = $rows[8];
        $startDateRow = $rows[10];
        $yearRow = $rows[11];
        $productRow = $rows[12];

        $seasons = [];

        for ($col = 3; $col <= 9; $col++) {
            $year = (int) $this->parseNumber($yearRow[$col] ?? null);
            $productName = trim((string) ($productRow[$col] ?? ''));
            if ($year < 2000 || $productName === '') {
                continue;
            }

            $productKey = Str::lower(Str::ascii($productName));
            $product = match (true) {
                str_contains($productKey, 'badem') => $products['badem'],
                str_contains($productKey, 'ceviz') => $products['ceviz'],
                default => null,
            };

            if (! $product) {
                $this->warn("Bilinmeyen ürün sütunu ({$col}): {$productName}");

                continue;
            }

            $season = Season::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'year' => $year,
                ],
                [
                    'order_start_date' => $this->parseTurkishDate($startDateRow[$col] ?? null),
                    'unit_price' => $this->parseNumber($unitPriceRow[$col] ?? null) ?? 0,
                    'gold_price' => $this->parseNumber($goldRow[$col] ?? null),
                    'usd_rate' => $this->parseNumber($usdRow[$col] ?? null),
                ],
            );
            $season->setRelation('product', $product);

            $seasons[$col] = $season;
        }

        return $seasons;
    }

    private function statusForSeason(Season $season): OrderStatus
    {
        if ((int) $season->year === 2026 && $season->product?->slug === 'ceviz') {
            return OrderStatus::Ordered;
        }

        return OrderStatus::Paid;
    }

    private function parseNumber(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $raw = str_replace(["\u{00A0}", ' '], '', $raw);

        if (str_contains($raw, ',') && str_contains($raw, '.')) {
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        } elseif (str_contains($raw, ',')) {
            $raw = str_replace(',', '.', $raw);
        }

        if (! is_numeric($raw)) {
            return null;
        }

        return (float) $raw;
    }

    private function parseTurkishDate(mixed $value): ?Carbon
    {
        $raw = trim((string) ($value ?? ''));
        if ($raw === '') {
            return null;
        }

        if (preg_match('/^(\d{1,2})\s+([A-Za-zÇĞİÖŞÜçğıöşü]+)\s+(\d{4})$/u', $raw, $m)) {
            $monthKey = Str::lower(Str::ascii($m[2]));
            // ascii strips diacritics; also try original lower
            $month = $this->monthMap[$monthKey]
                ?? $this->monthMap[mb_strtolower($m[2], 'UTF-8')]
                ?? null;

            if ($month) {
                return Carbon::createFromDate((int) $m[3], $month, (int) $m[1])->startOfDay();
            }
        }

        try {
            return Carbon::parse($raw);
        } catch (\Throwable) {
            $this->warn("Tarih parse edilemedi: {$raw}");

            return null;
        }
    }

    private function nullableTrim(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed === '' ? null : $trimmed;
    }
}
