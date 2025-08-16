<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Tagihan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class tagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
            // Cari user masyarakat
            // $user = User::where('nik', '3509200904020002')
            //             ->where('role', 'masyarakat')
            //             ->first();

            Tagihan::create([
                'masyarakat_id' => 1,
                'jumlah' => 500000, // contoh Rp500.000
                'status' => 'lunas', // sesuai enum baru
                'sisa_tagihan' => 500000, // awalnya sisa tagihan sama dengan jumlah
                'keterangan' => 'Tagihan pajak pertama',
                'tanggal_tagihan' => now(),
                'tanggal_lunas' => null,
            ]);

            Tagihan::create([
                'masyarakat_id' => 1,
                'jumlah' => 500000, // contoh Rp500.000
                'status' => 'cicilan', // sesuai enum baru
                'sisa_tagihan' => 300000, // awalnya sisa tagihan sama dengan jumlah
                'keterangan' => 'Tagihan pajak pertama',
                'tanggal_tagihan' => now(),
                'tanggal_lunas' => null,
            ]);

            // Contoh: bikin 2 cicilan per tagihan
            $cicilan1 = [
                'tagihan_id' => 2,
                'jumlah_bayar' => round(100000, 2),
                'tanggal_bayar' => Carbon::now()->subDays(10),
                'keterangan' => 'Cicilan pertama',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $cicilan2 = [
                'tagihan_id' => 2,
                'jumlah_bayar' => round(100000 / 2, 2),
                'tanggal_bayar' => Carbon::now(),
                'keterangan' => 'Cicilan kedua',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('cicilan_tagihan')->insert([$cicilan1, $cicilan2]);

            Tagihan::create([
                'masyarakat_id' => 2,
                'jumlah' => 100000, // contoh Rp500.000
                'status' => 'belum', // sesuai enum baru
                'sisa_tagihan' => 100000, // awalnya sisa tagihan sama dengan jumlah
                'keterangan' => 'Tagihan pajak pertama',
                'tanggal_tagihan' => now(),
                'tanggal_lunas' => null,
            ]);
        }
    }
}
