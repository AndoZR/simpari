<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Masyarakat;
use App\Models\Pemungut;
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
        // Buat 5 user dengan role 'pemungut'
        for ($i = 1; $i <= 5; $i++) {
            $nik = '320101010101' . str_pad($i, 4, '0', STR_PAD_LEFT);

            User::create([
                'nik' => $nik,
                'password' => Hash::make('123123123'),
                'role' => 'pemungut',
            ]);
        }

        // Buat 50 user dengan role 'masyarakat'
        for ($i = 1; $i <= 50; $i++) {
            $nik = '350920090402' . str_pad($i, 4, '0', STR_PAD_LEFT);

            User::create([
                'nik' => $nik,
                'password' => Hash::make('123123123'),
                'role' => 'masyarakat',
            ]);
        }


        // === Buat data Pemungut ===
        $pemungutUsers = User::where('role', 'pemungut')->get();
        foreach ($pemungutUsers as $index => $user) {
            Pemungut::create([
                'nama' => 'Pemungut ' . ($index + 1),
                'user_id' => $user->id,
                'telepon' => '08123' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Pemungut No. ' . ($index + 1) . ', Desa Sukamundur',
            ]);
        }

        // Ambil pemungut yg sudah dibuat
        $pemungutList = Pemungut::all();

        // === Buat data Masyarakat ===
        $masyarakatUsers = User::where('role', 'masyarakat')->get();
        foreach ($masyarakatUsers as $index => $user) {
            // Hitung pemungut_id → setiap 10 masyarakat pindah ke pemungut berikutnya
            $pemungutIndex = floor($index / 10); 
            $pemungut_id = $pemungutList[$pemungutIndex]->id;

            Masyarakat::create([
                'nama' => 'Masyarakat ' . ($index + 1),
                'user_id' => $user->id,
                'telepon' => '0821' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Masyarakat No. ' . ($index + 1) . ', Desa Sukamaju',
                'pemungut_id' => $pemungut_id,
            ]);
        }

        User::create([
            // 'name' => 'Admin Bendahara',
            'nik' => '3201010101011113',
            'password' => Hash::make('123123123'),
            'role' => 'admin_kecamatan',
        ]);

        User::create([
            // 'name' => 'Admin Kecamatan',
            'nik' => '3201010101011114',
            'password' => Hash::make('123123123'),
            'role' => 'admin_desa',
        ]);
    }
}
