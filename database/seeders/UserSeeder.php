<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ando Royan',
            'nik' => '3509200904020002',
            'password' => Hash::make('password123'),
            'role' => '1',
        ]);

        User::create([
            'name' => 'Siti Aminah',
            'nik' => '3201010101010002',
            'password' => Hash::make('password123'),
            'role' => '2',
        ]);

        User::create([
            'name' => 'Admin Bendahara',
            'nik' => '3201010101010003',
            'password' => Hash::make('password123'),
            'role' => '3',
        ]);

        User::create([
            'name' => 'Admin Kecamatan',
            'nik' => '3201010101010004',
            'password' => Hash::make('password123'),
            'role' => '4',
        ]);
    }
}
