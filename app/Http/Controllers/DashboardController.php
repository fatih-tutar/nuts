<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Season;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $rows = Order::query()
            ->join('seasons', 'orders.season_id', '=', 'seasons.id')
            ->join('products', 'seasons.product_id', '=', 'products.id')
            ->whereIn('products.slug', ['ceviz', 'badem'])
            ->select([
                'seasons.year',
                'products.slug',
                DB::raw('COALESCE(SUM(orders.quantity_kg), 0) as total_kg'),
            ])
            ->groupBy('seasons.year', 'products.slug')
            ->orderBy('seasons.year')
            ->get();

        $years = $rows->pluck('year')->unique()->sort()->values()->map(fn ($y) => (int) $y)->all();

        $bySlug = $rows->groupBy('slug');

        $seriesFor = function (string $slug) use ($years, $bySlug): array {
            $byYear = ($bySlug->get($slug) ?? collect())->keyBy(fn ($row) => (int) $row->year);

            return collect($years)->map(function (int $year) use ($byYear) {
                $row = $byYear->get($year);

                return $row ? round((float) $row->total_kg, 2) : 0;
            })->all();
        };

        return view('dashboard', [
            'stats' => [
                'products' => Product::query()->count(),
                'customers' => Customer::query()->count(),
                'seasons' => Season::query()->count(),
                'orders' => Order::query()->count(),
                'open_orders' => Order::query()->where('status', OrderStatus::Ordered)->count(),
            ],
            'chart' => [
                'years' => $years,
                'ceviz' => $seriesFor('ceviz'),
                'badem' => $seriesFor('badem'),
            ],
            'seasonDetails' => $this->seasonDetails(),
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function seasonDetails(): array
    {
        $seasons = Season::query()
            ->with('product')
            ->whereHas('product', fn ($query) => $query->whereIn('slug', ['ceviz', 'badem']))
            ->withSum('orders as orders_quantity_kg', 'quantity_kg')
            ->withCount('orders')
            ->withAvg('orders as orders_avg_kg', 'quantity_kg')
            ->orderBy('year')
            ->get();

        $details = [];

        foreach ($seasons as $season) {
            $slug = $season->product?->slug;
            if (! $slug) {
                continue;
            }

            $key = $season->year.'-'.$slug;
            $kg = round((float) ($season->orders_quantity_kg ?? 0), 2);
            $orderCount = (int) $season->orders_count;
            $avgKg = $orderCount > 0 ? round((float) ($season->orders_avg_kg ?? 0), 2) : null;
            $unitPrice = (float) $season->unit_price;
            $goldPrice = $season->gold_price !== null ? (float) $season->gold_price : null;
            $usdRate = $season->usd_rate !== null ? (float) $season->usd_rate : null;
            $revenueTry = round($kg * $unitPrice, 2);
            $revenueUsd = $usdRate && $usdRate > 0 ? round($revenueTry / $usdRate, 2) : null;
            $revenueGold = $goldPrice && $goldPrice > 0 ? round($revenueTry / $goldPrice, 2) : null;

            if (isset($details[$key])) {
                $existing = $details[$key];
                $mergedKg = round($existing['total_kg'] + $kg, 2);
                $mergedOrders = $existing['order_count'] + $orderCount;
                $mergedTry = round($existing['revenue_try'] + $revenueTry, 2);

                $details[$key] = array_merge($existing, [
                    'total_kg' => $mergedKg,
                    'order_count' => $mergedOrders,
                    'avg_order_kg' => $mergedOrders > 0 ? round($mergedKg / $mergedOrders, 2) : null,
                    'revenue_try' => $mergedTry,
                    'revenue_usd' => $usdRate && $usdRate > 0
                        ? round($mergedTry / $usdRate, 2)
                        : $existing['revenue_usd'],
                    'revenue_gold' => $goldPrice && $goldPrice > 0
                        ? round($mergedTry / $goldPrice, 2)
                        : $existing['revenue_gold'],
                    'season_ids' => array_values(array_unique([...$existing['season_ids'], $season->id])),
                ]);

                continue;
            }

            $details[$key] = [
                'key' => $key,
                'title' => $season->displayName(),
                'year' => (int) $season->year,
                'product' => $season->product?->name ?? $slug,
                'slug' => $slug,
                'unit_price' => $unitPrice,
                'gold_price' => $goldPrice,
                'usd_rate' => $usdRate,
                'total_kg' => $kg,
                'order_count' => $orderCount,
                'avg_order_kg' => $avgKg,
                'revenue_try' => $revenueTry,
                'revenue_usd' => $revenueUsd,
                'revenue_gold' => $revenueGold,
                'season_ids' => [$season->id],
                'orders_url' => route('orders.index', ['season_id' => $season->id]),
            ];
        }

        return $details;
    }
}
