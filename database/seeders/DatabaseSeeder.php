<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Katalog game & produk tidak lagi di-seed manual.
        // Sumbernya sekarang toko top up Saweria: php artisan saweria:sync
        $this->call([
            reviewsTableSeeder::class,
        ]);
    }
}
