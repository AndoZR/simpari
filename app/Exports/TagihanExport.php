<?php

namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TagihanExport implements FromCollection, WithHeadings,  WithColumnWidths, WithStyles
{
    /**
     * Ambil data untuk diexport
     */
    public function collection()
    {
        $data = Tagihan::join('masyarakat', 'masyarakat.id', '=', 'tagihan.masyarakat_id')
            ->where('masyarakat.village_id', auth()->user()->adminDesa->village_id)
            ->select(
                'tagihan.id',
                'tagihan.nop',
                'masyarakat.nama as nama_masyarakat',
                'tagihan.jumlah',
                'tagihan.sisa_tagihan',
                'tagihan.uang_dipemungut',
                'tagihan.uang_didesa',
                'tagihan.status',
                'tagihan.tanggal_tagihan'
            )
            ->get();

        // dd($data);
        return $data;
    }

    /**
     * Judul kolom di Excel
     */
    public function headings(): array
    {
        return ['ID', 'NOP', 'Nama Masyarakat', 'Jumlah', 'Sisa Tagihan', 'Uang Di pemungut', 'Uang Di desa', 'Status', 'Tanggal Tagihan'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 25,
            'D' => 20,
            'D' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 10,
            'I' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Atur tinggi row heading
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Atur tinggi semua row (misal 20)
        foreach (range(1, $sheet->getHighestRow()) as $row) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        return [];
    }
}
