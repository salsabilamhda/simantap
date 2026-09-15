<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use Illuminate\Database\Seeder;

class PerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'nama' => 'PT ANUGERAH PUTRA PERMANA',
                'nomor_perjanjian' => '1211,Pj/DAN,00,07/F04000000/2024',
            ],
            [
                'nama' => 'PT HALEYORA POWERINDO',
                'nomor_perjanjian' => '0543.Pj/DAN.02.01/UP3-PNG/2023',
            ],
            [
                'nama' => 'PT PLN TARAKAN',
                'nomor_perjanjian' => '0821.Pj/DAN.01.03/F04000000/2024',
            ],
        ];

        foreach ($companies as $c) {
            Perusahaan::updateOrCreate(['nama' => $c['nama']], $c);
        }
    }
}
