<?php

namespace Database\Seeders;

use App\Models\AdminDesa;
use App\Models\AdminKecamatan;
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
        // // Buat 5 user dengan role 'pemungut'
        // for ($i = 1; $i <= 5; $i++) {
        //     $nik = '320101010101' . str_pad($i, 4, '0', STR_PAD_LEFT);

        //     User::create([
        //         'nik' => $nik,
        //         'password' => Hash::make('123123123'),
        //         'role' => 'pemungut',
        //     ]);
        // }

        // // Buat 50 user dengan role 'masyarakat'
        // for ($i = 1; $i <= 50; $i++) {
        //     $nik = '350920090402' . str_pad($i, 4, '0', STR_PAD_LEFT);

        //     User::create([
        //         'nik' => $nik,
        //         'password' => Hash::make('123123123'),
        //         'role' => 'masyarakat',
        //     ]);
        // }


        // // === Buat data Pemungut ===
        // $pemungutUsers = User::where('role', 'pemungut')->get();
        // foreach ($pemungutUsers as $index => $user) {
        //     Pemungut::create([
        //         'nama' => 'Pemungut ' . ($index + 1),
        //         'user_id' => $user->id,
        //         'telepon' => '08123' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
        //         'alamat' => 'Jl. Pemungut No. ' . ($index + 1) . ', Desa Sukamundur',
        //     ]);
        // }

        // // Ambil pemungut yg sudah dibuat
        // $pemungutList = Pemungut::all();

        // // === Buat data Masyarakat ===
        // $masyarakatUsers = User::where('role', 'masyarakat')->get();
        // foreach ($masyarakatUsers as $index => $user) {
        //     // Hitung pemungut_id → setiap 10 masyarakat pindah ke pemungut berikutnya
        //     $pemungutIndex = floor($index / 10); 
        //     $pemungut_id = $pemungutList[$pemungutIndex]->id;

        //     Masyarakat::create([
        //         'nama' => 'Masyarakat ' . ($index + 1),
        //         'user_id' => $user->id,
        //         'telepon' => '0821' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
        //         'alamat' => 'Jl. Masyarakat No. ' . ($index + 1) . ', Desa Sukamaju',
        //         'pemungut_id' => $pemungut_id,
        //         'village_id' => '3511080011',
        //     ]);
        // }


        // SESSION ADMIN DESA SEED
        // $villageIds = [
        //     '3511080001',
        //     '3511080002',
        //     '3511080004',
        //     '3511080005',
        //     '3511080006',
        //     '3511080007',
        //     '3511080008',
        //     '3511080009',
        //     '3511080010',
        //     '3511080011',
        //     '3511080012',
        // ];

        // foreach ($villageIds as $index => $villageId) {
        //     // bikin user
        //     $user = User::create([
        //         'nik' => '351101010101' . str_pad($index+1, 4, '0', STR_PAD_LEFT), // unik tiap user
        //         'password' => Hash::make('123123123'),
        //         'role' => 'admin_desa',
        //     ]);

        //     // bikin admin desa relasi ke user
        //     AdminDesa::create([
        //         'user_id' => $user->id, // relasi 1-1
        //         'tagihan' => 1000000000,
        //         'sisa_tagihan' => 500000000,
        //         'diterima_kec' => 500000000,
        //         'telepon' => '08' . rand(1000000000, 9999999999),
        //         'village_id' => $villageId,
        //     ]);
        // }


        // SESSION ADMIN KECAMATAN
        $dataUser = User::create([
            'nik' => '3511012345678901',
            'password' => Hash::make('123123123'),
            'role' => 'admin_kecamatan',
        ]);

        AdminKecamatan::create([
            'user_id' => $dataUser->id,
            'kecamatan_id' => '3511080',
        ]);



    }
}
