<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hapus komentar atau tambahkan baris ini
        $this->call([
            ProductSeeder::class,
            StoreSeeder::class,
        ]);
    }
}