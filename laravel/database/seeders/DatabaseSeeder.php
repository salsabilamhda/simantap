<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            UnitLayananSeeder::class,
            PerusahaanSeeder::class,
            TenagaKerjaSeeder::class,
        ]);
    }
}
