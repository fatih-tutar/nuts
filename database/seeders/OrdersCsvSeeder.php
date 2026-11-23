<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class OrdersCsvSeeder extends Seeder
{
    public function run(): void
    {
        if (Customer::query()->exists()) {
            $this->command?->info('Müşteri kayıtları mevcut; CSV import atlandı.');

            return;
        }

        $path = database_path('data/ceviz-badem-siparis.csv');
        if (! is_readable($path)) {
            $this->command?->warn("CSV bulunamadı: {$path}");

            return;
        }

        Artisan::call('orders:import-csv', ['path' => $path]);
        $this->command?->info(Artisan::output());
    }
}
