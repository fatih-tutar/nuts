<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only create the admin once — never overwrite an existing password on deploy.
        User::query()->firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@nuts.test')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'email_verified_at' => now(),
            ],
        );

        $this->call(OrdersCsvSeeder::class);
    }
}
