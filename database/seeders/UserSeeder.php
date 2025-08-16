<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Masyarakat;
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
            'password' => Hash::make('123123123'),
            'role' => 'masyarakat',
        ]);

        User::create([
            'name' => 'John Smith',
            'nik' => '3509200904020001',
            'password' => Hash::make('123123123'),
            'role' => 'masyarakat',
        ]);

        Masyarakat::create([
            'user_id'  => 1,
            'telepon' => '081234567890',
            'alamat' => 'Jl. Raya No. 123, Desa Sukamaju',
            
        ]);

        Masyarakat::create([
            'user_id'  => 2,
            'telepon' => '081234567891',
            'alamat' => 'Jl. Raya No. 1321, Desa Sukamundur',
            
        ]);

        User::create([
            'name' => 'Siti Aminah',
            'nik' => '3201010101010002',
            'password' => Hash::make('123123123'),
            'role' => 'pemungut',
        ]);

        User::create([
            'name' => 'Admin Bendahara',
            'nik' => '3201010101010003',
            'password' => Hash::make('123123123'),
            'role' => 'admin_kecamatan',
        ]);

        User::create([
            'name' => 'Admin Kecamatan',
            'nik' => '3201010101010004',
            'password' => Hash::make('123123123'),
            'role' => 'admin_desa',
        ]);
    }
}
