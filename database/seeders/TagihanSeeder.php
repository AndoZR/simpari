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
            // Random jumlah tagihan per masyarakat (1–3)
            $jumlahTagihan = rand(1, 3);

            for ($i = 0; $i < $jumlahTagihan; $i++) {
                $jumlah = rand(200000, 1000000); // nominal random
                $status = collect(['lunas', 'cicilan', 'belum'])->random();

                $tagihan = Tagihan::create([
                    'masyarakat_id' => $masyarakat->id,
                    'jumlah' => $jumlah,
                    'status' => $status,
                    'sisa_tagihan' => $status == 'lunas' ? 0 : $jumlah,
                    'keterangan' => 'Tagihan pajak ' . ($i + 1),
                    'tanggal_tagihan' => Carbon::now()->subDays(rand(0, 60)),
                    'tanggal_lunas' => $status == 'lunas' ? Carbon::now()->subDays(rand(1, 30)) : null,
                ]);

                // Jika status cicilan → buat cicilan random
                if ($status == 'cicilan') {
                    $totalBayar = 0;
                    $jumlahCicilan = rand(2, 4); // random 2–4 cicilan
                    for ($j = 1; $j <= $jumlahCicilan; $j++) {
                        $bayar = rand(50000, $jumlah / $jumlahCicilan);
                        $totalBayar += $bayar;

                        Cicilan::create([
                            'tagihan_id' => $tagihan->id,
                            'jumlah_bayar' => $bayar,
                            'tanggal_bayar' => Carbon::now()->subDays(rand(0, 30)),
                            'keterangan' => 'Cicilan ke-' . $j,
                        ]);
                    }

                    // update sisa tagihan
                    $tagihan->update([
                        'sisa_tagihan' => max($jumlah - $totalBayar, 0),
                        'status' => $totalBayar >= $jumlah ? 'lunas' : 'cicilan',
                        'tanggal_lunas' => $totalBayar >= $jumlah ? Carbon::now() : null,
                    ]);
                }
            }
        }
    }
}
