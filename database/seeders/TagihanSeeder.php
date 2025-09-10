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

            // generate NOP unik
            do {
                $prov  = str_pad(rand(1, 34),    2, '0', STR_PAD_LEFT);
                $kab   = str_pad(rand(1, 99),    2, '0', STR_PAD_LEFT);
                $kec   = str_pad(rand(1, 999),   3, '0', STR_PAD_LEFT);
                $kel   = str_pad(rand(1, 999),   3, '0', STR_PAD_LEFT);
                $blok  = str_pad(rand(1, 999),   3, '0', STR_PAD_LEFT);
                $urut  = str_pad(rand(1, 9999),  4, '0', STR_PAD_LEFT);
                $jenis = str_pad(rand(0, 9),     1, '0', STR_PAD_LEFT);

                $nop_raw = $prov.$kab.$kec.$kel.$blok.$urut.$jenis; // 18 digit

                $nopFormatted = substr($nop_raw,0,2) . "." .
                                substr($nop_raw,2,2) . "." .
                                substr($nop_raw,4,3) . "." .
                                substr($nop_raw,7,3) . "." .
                                substr($nop_raw,10,3) . "-" .
                                substr($nop_raw,13,4) . "." .
                                substr($nop_raw,17,1);
            } while (Tagihan::where('nop', $nopFormatted)->exists());

            $sisa = $jumlah;

            $tagihan = Tagihan::create([
                'masyarakat_id' => $masyarakat->id,
                'jumlah' => $jumlah,
                'status' => $status,
                'sisa_tagihan' => $sisa,
                'uang_dipemungut' => 0, // default
                'uang_didesa' => 0,     // default
                'keterangan' => 'Tagihan pajak ' . ($i + 1),
                'tanggal_tagihan' => Carbon::now()->subDays(rand(0, 60)),
                'tanggal_lunas' => null,
                'nop' => $nopFormatted,
            ]);

            if ($status === 'cicilan') {
                $totalBayar = 0;
                $jumlahCicilan = rand(1, 3);

                for ($j = 0; $j < $jumlahCicilan; $j++) {
                    $nominal = rand(10000, (int)($jumlah * 0.5));
                    $totalBayar += $nominal;

                    Cicilan::create([
                        'tagihan_id' => $tagihan->id,
                        'total_cicilan_now' => $nominal,
                    ]);
                }

                $tagihan->sisa_tagihan = max(0, $jumlah - $totalBayar);
                $tagihan->uang_dipemungut = $totalBayar; // masih di pemungut
                $tagihan->uang_didesa = 0; // belum masuk desa
                $tagihan->save();

            } elseif ($status === 'lunas') {
                Cicilan::create([
                    'tagihan_id' => $tagihan->id,
                    'total_cicilan_now' => $jumlah,
                ]);

                $tagihan->sisa_tagihan = 0;
                $tagihan->uang_dipemungut = $jumlah; // uang tetap di pemungut
                $tagihan->uang_didesa = 0; // desa belum terima
                $tagihan->tanggal_lunas = Carbon::now()->subDays(rand(1, 30));
                $tagihan->save();
            }
        }
    }
}

}
