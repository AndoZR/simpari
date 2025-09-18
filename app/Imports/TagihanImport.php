<?php

namespace App\Imports;

use App\Models\Masyarakat;
use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class TagihanImport implements WithStartRow, ToCollection
{
    public function collection(Collection $rows)
    {
        $lastIndex = count($rows) - 1; // index terakhir

        foreach ($rows as $index => $row) {
            if ($index === $lastIndex) {
                continue; // skip baris terakhir karena cuma totalan
            }
            $masyarakat = Masyarakat::create([
                'nama'       => (string) $row[2], // isi kolom ke-3 di Excel
                'alamat'     => (string) $row[3], // isi kolom ke-4 di Excel
                'village_id' => Auth()->user()?->adminDesa?->village_id,
            ]);

            Tagihan::create([
                'nop'            => (string) $row[1],  // kolom ke-2 di Excel
                'jumlah'         => (float) $row[4],  // kolom ke-5 di Excel
                'sisa_tagihan'   => (float) $row[4],
                'masyarakat_id'  => $masyarakat->id,
                'tanggal_tagihan'=> now(),
            ]);
        }
    }

    public function startRow(): int
    {
        return 6; // langsung mulai baris ke-6
    }

}