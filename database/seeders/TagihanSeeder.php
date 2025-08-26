<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Cicilan;
use App\Models\Tagihan;
use App\Models\Masyarakat;
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
        $masyarakats = Masyarakat::all();

        foreach ($masyarakats as $masyarakat) {
            $jumlahTagihan = rand(1, 3);

            for ($i = 0; $i < $jumlahTagihan; $i++) {
                $jumlah = rand(200000, 1000000); 
                $status = collect(['lunas', 'cicilan', 'belum'])->random();

                // generate NOP 18 digit
                $prov = str_pad(rand(1, 34), 2, '0', STR_PAD_LEFT);
                $kab = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
                $kecKel = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $urut = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $jenis = rand(0, 9);
                $nop = $prov.$kab.$kecKel.$urut.$jenis;

                $nopFormatted = substr($nop,0,2).".".
                                substr($nop,2,2).".".
                                substr($nop,4,3).".".
                                substr($nop,7,3).".".
                                substr($nop,10,3)."-".
                                substr($nop,13,4).".".
                                substr($nop,17,1);

                // logika pembayaran
                $cicilan = null;
                $sisa = $jumlah;

                if ($status == 'cicilan') {
                    $cicilan = rand(10000, (int)($jumlah * 0.7)); // cicilan antara 10rb – 70% dari jumlah
                    $sisa = $jumlah - $cicilan;
                } elseif ($status == 'lunas') {
                    $cicilan = $jumlah;
                    $sisa = 0;
                }

                Tagihan::create([
                    'masyarakat_id' => $masyarakat->id,
                    'jumlah' => $jumlah,
                    'status' => $status,
                    'sisa_tagihan' => $sisa,
                    'cicilan' => $cicilan,
                    'keterangan' => 'Tagihan pajak ' . ($i + 1),
                    'tanggal_tagihan' => Carbon::now()->subDays(rand(0, 60)),
                    'tanggal_lunas' => $status == 'lunas' ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'nop' => $nopFormatted,
                ]);
            }
        }
    }
}
