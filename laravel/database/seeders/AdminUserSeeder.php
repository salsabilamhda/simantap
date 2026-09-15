<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@simantap.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'status' => 'Aktif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin.ponorogo@simantap.id'],
            [
                'name' => 'Admin HR ULP Ponorogo',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'Aktif',
            ]
        );
    }
}
