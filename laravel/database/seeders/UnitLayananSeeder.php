<?php

namespace Database\Seeders;

use App\Models\UnitLayanan;
use Illuminate\Database\Seeder;

class UnitLayananSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'kode' => 'TRENGGALEK',
                'nama' => 'ULP Trenggalek',
                'unit_induk' => 'UP3 Ponorogo',
                'color_hex' => '#2BA8A2',
            ],
            [
                'kode' => 'PACITAN',
                'nama' => 'ULP Pacitan',
                'unit_induk' => 'UP3 Ponorogo',
                'color_hex' => '#5DADE2',
            ],
            [
                'kode' => 'BALONG',
                'nama' => 'ULP Balong',
                'unit_induk' => 'UP3 Ponorogo',
                'color_hex' => '#FFD23F',
            ],
            [
                'kode' => 'PONOROGO',
                'nama' => 'ULP Ponorogo',
                'unit_induk' => 'UP3 Ponorogo',
                'color_hex' => '#EF6C4A',
            ],
            [
                'kode' => 'UP3_PONOROGO',
                'nama' => 'UP3 Ponorogo',
                'unit_induk' => 'UID Jawa Timur',
                'color_hex' => '#6A5ACD',
            ],
        ];

        foreach ($units as $u) {
            UnitLayanan::updateOrCreate(['kode' => $u['kode']], $u);
        }
    }
}
