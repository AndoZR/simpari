<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Cicilan;
use App\Models\Masyarakat;
use App\Models\Pemungut;
use App\Models\User;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemungutController extends Controller
{
    // Add your methods here for handling requests related to Pemungut
    public function index()
    {
        // Logic to return a list of Pemungut
    }

    // public function showTagihan() // ver old
    // {
    //     try {
    //         $user = Auth::user(); 

    //         // Ambil pemungut berdasarkan user login + relasi lengkap
    //         $pemungut = Pemungut::with([
    //             'user',
    //             'masyarakat.tagihan'
    //         ])->firstOrFail();

    //         $masyarakatList = $pemungut->masyarakat->map(function ($m) use (&$target_nominal, &$totalSisa, &$totalCapaian) {
    //             $tagihanList = $m->tagihan->map(function ($tagih) use (&$target_nominal, &$totalSisa, &$totalCapaian) {
    //                 $target_nominal += $tagih->jumlah ?? 0;
    //                 $totalSisa    += $tagih->sisa_tagihan ?? 0;
    //                 $totalCapaian += ($tagih->jumlah ?? 0) - ($tagih->sisa_tagihan ?? 0);

    //                 return [
    //                     'id_tagihan'              => $tagih->id,
    //                     'nop'          => $tagih->nop,
    //                     'jumlah'          => $tagih->jumlah,
    //                     'status'          => $tagih->status,
    //                     'sisa_tagihan'    => $tagih->sisa_tagihan,
    //                     'keterangan'      => $tagih->keterangan,
    //                     'tanggal_tagihan' => $tagih->tanggal_tagihan,
    //                     'tanggal_lunas'   => $tagih->tanggal_lunas,
    //                     'cicilan'         => $tagih->cicilan
    //                 ];
    //             });

    //             return [
    //                 'masyarakat_id' => $m->id,
    //                 'nama'          => $m->nama,
    //                 'alamat'        => $m->alamat,
    //                 'status_lunas'  => intval($m->status_lunas),
    //                 'tagihan'       => $tagihanList
    //             ];
    //         });

    //         // Hitung persentase capaian
    //         $persentase = $target_nominal > 0 
    //             ? round(($totalCapaian / $target_nominal) * 100, 2) 
    //             : 0;

    //         return response()->json([
    //             'success' => true,
    //             'pemungut' => [
    //                 'id' => $pemungut->id,
    //                 'nama' => $pemungut->nama // ✅ pakai nama dari tabel pemungut
    //                         ?? $pemungut->user->name // fallback ke user->name
    //                         ?? null,
    //                 'target_nominal' => $target_nominal,
    //                 'sisa_tagihan' => $totalSisa,
    //                 'target_tercapai' => $totalCapaian,
    //                 'persentase' => $persentase,
    //             ],
    //             'masyarakat' => $masyarakatList
    //         ], 200);

    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data pemungut tidak ditemukan.'
    //         ], 404);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan pada server.',
    //             'error'   => $e->getMessage() // 🚨 hapus di production
    //         ], 500);
    //     }
    // }

    public function showTagihan()
    {
        try {
            $user = Auth::user();
            
            $pemungut = Pemungut::where('user_id', $user->id)
                ->with(['user', 'masyarakat.tagihan'])
                ->first();

            // Inisialisasi total
            $target_nominal = 0;
            $totalSisa      = 0;
            $totalCapaian   = 0;

            $masyarakatList = $pemungut->masyarakat->map(function ($m) use (&$target_nominal, &$totalSisa, &$totalCapaian) {
                $tagihanList = $m->tagihan->map(function ($tagih) use (&$target_nominal, &$totalSisa, &$totalCapaian, $m) {
                    // Hitung total-target, sisa, dan capaian
                    $target_nominal += $tagih->jumlah ?? 0;
                    $totalSisa      += $tagih->sisa_tagihan ?? 0;
                    $totalCapaian   += ($tagih->jumlah ?? 0) - ($tagih->sisa_tagihan ?? 0);

                    if ($tagih->status === 'lunas') {
                        $statusTagihan = 1; // sudah lunas
                    } else {
                        $statusTagihan = 0; // belum lunas
                    }

                    return [
                        'tagihan_id'      => $tagih->id,
                        'nop'             => $tagih->nop,
                        'jumlah'          => $tagih->jumlah,
                        'sisa_tagihan'    => $tagih->sisa_tagihan,
                        'keterangan'      => $tagih->keterangan,
                        'tanggal_tagihan' => $tagih->tanggal_tagihan,
                        'tanggal_lunas'   => $tagih->tanggal_lunas,
                        'nama'            => $m->nama,
                        'alamat'          => $m->alamat,
                        'status_lunas'    => $statusTagihan,
                    ];
                });

                return $tagihanList; // return semua tagihan milik masyarakat ini
            })->flatten(1); // gabung jadi satu list besar

            // Hitung persentase capaian
            $persentase = $target_nominal > 0 
                ? round(($totalCapaian / $target_nominal) * 100, 2) 
                : 0;

            

            return response()->json([
                'success' => true,
                'pemungut' => [
                    'id' => $pemungut->id,
                    'nama' => $pemungut->nama // ✅ pakai nama dari tabel pemungut
                            ?? $pemungut->user->name // fallback ke user->name
                            ?? null,
                    'target_nominal' => $target_nominal,
                    'sisa_tagihan' => $totalSisa,
                    'target_tercapai' => $totalCapaian,
                    'persentase' => $persentase,
                ],
                'masyarakat' => $masyarakatList
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pemungut tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage() // 🚨 hapus di production
            ], 500);
        }
    }

    // public function updateTagihan(Request $request)
    // {
    //     try {
    //         // Validasi input
    //         $request->validate([
    //             'nominal' => 'required',
    //             'tanggal_lunas' => 'required',
    //         ]);
    //         // Cari tagihan
    //         $tagihan = Tagihan::findOrFail($request->tagihan_id);

    //         // Update status
    //         $tagihan->status = $request->status;
    //         $tagihan->keterangan = $request->keterangan ?? $tagihan->keterangan;
            
    //         // Update tanggal_lunas jika ada
    //         if ($request->status === 'lunas') {
    //             $tagihan->tanggal_lunas = now();
    //         } elseif ($request->status === 'cicilan') {
    //             $tagihan->tanggal_lunas = null;
    //             $tagihan->cicilan = $request->cicilan ?? $tagihan->cicilan;
    //             $tagihan->sisa_tagihan = $tagihan->jumlah - ($tagihan->cicilan ?? 0);
    //         } elseif ($request->status === 'belum') {
    //             $tagihan->cicilan = 0;
    //             $tagihan->sisa_tagihan = $tagihan->jumlah;
    //             $tagihan->tanggal_lunas = null;
    //         }elseif ($request->cicilan == $tagihan->jumlah) {
    //             $tagihan->status = 'lunas';
    //             $tagihan->tanggal_lunas = now();
    //             $tagihan->sisa_tagihan = 0;
    //         }

    //         $tagihan->save();

    //         return response()->json([
    //             'message' => 'Status tagihan berhasil diupdate',
    //             'tagihan' => $tagihan
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Terjadi kesalahan saat update status',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function bayarTagihan(Request $request)
    { // jadi kondisi titan, misal kemarin sinkronize ke API 50000/100000, lalu hari ini kriim data lagi 100000/100000. jadi intinya update bayar tagihan bukan penjumlahan. Maka kalo bayar tagihan yang terupdate == total tagihan == lunas
        try {
            // Validasi cuma riwayat_tagihan aja
            $request->validate([
                'riwayat_tagihan' => 'required',
            ]);

            // Decode JSON dari request
            $riwayat = json_decode($request->riwayat_tagihan, true);

            foreach($riwayat as $item){
                $nop = $item["nop"];
                $nominal = $item["bayar"];

                // Cari tagihan berdasarkan NOP
                $tagihan = Tagihan::where('nop', $nop)->first();
                if (!$tagihan) {
                    continue; // kalau tagihan nggak ada, skip loop ini
                }
                
                $dataCicilan = Cicilan::where('tagihan_id', $tagihan->id)->first();

                // Logika update status
                if ($tagihan->status != "lunas"){
                    if ($nominal >= $tagihan->jumlah) { // langsung bayar lunas atau cicilan telah lunas
                        if ($dataCicilan) {
                            $dataCicilan->update([
                                'total_cicilan_now' => $nominal
                            ]);
                        }
    
                        $tagihan->update([
                            'tanggal_lunas' => $request->tanggal_lunas ?? now(),
                            'status' => 'lunas',
                            'sisa_tagihan' => 0,
                            'uang_dipemungut' => $tagihan->jumlah
                        ]);
                    } else { // lagi cicilan atau belum lunas
                        if ($dataCicilan) {
                            $dataCicilan->update([
                                'total_cicilan_now' => $nominal
                            ]);
                        } else {
                            Cicilan::create([
                                'tagihan_id' => $tagihan->id,
                                'total_cicilan_now' => $nominal,
                            ]);
    
                        }
                        $tagihan->increment('uang_dipemungut', $nominal); // update kolom uang_dipemungut karena masyarakat udah bayar cicilan
                        $tagihan->update([
                            'status' => 'cicilan',
                            'sisa_tagihan' => $tagihan->jumlah - $nominal,
                        ]);
                    }
                } else {
                    //do nothing
                }
            }

            return ResponseFormatter::success(null, "Berhasil bayar tagihan");
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat bayar tagihan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function formatNop($nop) {
        // hapus semua karakter non-digit kecuali terakhir yang mungkin ada - dan . jika sudah ada
        $nop = preg_replace('/\D/', '', $nop); // hanya ambil angka
        // pastikan panjangnya sesuai, misal 18 digit + 1 kode
        // contoh implementasi sesuai format nop: 24.52.891.153.727-8434.1
        $formatted = substr($nop,0,2) . '.' .
                    substr($nop,2,2) . '.' .
                    substr($nop,4,3) . '.' .
                    substr($nop,7,3) . '.' .
                    substr($nop,10,3) . '-' .
                    substr($nop,13,4) . '.' .
                    substr($nop,17); // sesuaikan panjang
        return $formatted;
    }

    // public function getTagihanByNop(Request $request)
    // {
    //     $nop = $request->nop;
    //     // $nop = "18.42.882.557.877.7381-8";

    //     // jika input tidak ada titik, coba format
    //     if (strpos($nop, '.') === false) {
    //         // hapus semua karakter non-digit
    //         $digits = preg_replace('/\D/', '', $nop);

    //         // cek panjang minimal (misal 18-19 digit sesuai format)
    //         if (strlen($digits) < 18) {
    //             return ResponseFormatter::error(null, "NOP tidak valid", 422);
    //         }

    //         // format NOP
    //         $nop = $this->formatNop($nop);
    //     } else {
    //         // jika input ada titik, validasi dengan regex
    //         $pattern = '/^\d{2}\.\d{2}\.\d{3}\.\d{3}\.\d{3}.\d{4}\-\d$/';
    //         if (!preg_match($pattern, $nop)) {
    //             return ResponseFormatter::error(null, "Format NOP tidak valid, hilangkan tanda baca atau gunakan format xx.xx.xxx.xxx.xxx-xxxx.x", 422);
    //         }
    //     }

    //     // query tagihan
    //     $tagihan = Tagihan::where('nop', $nop)
    //         ->select('id','masyarakat_id', 'nop', 'jumlah', 'sisa_tagihan', 'status', 'tanggal_lunas')
    //         ->with(['masyarakat:id,nama'])
    //         ->first();

    //     if (!$tagihan) {
    //         return ResponseFormatter::error(null, "Tagihan tidak ditemukan", 404);
    //     }

    //     return ResponseFormatter::success($tagihan, "Berhasil mendapatkan data tagihan");
    // }

    public function getTagihanByNop(Request $request)
    {
        try {
            $rawNop = $request->nop;

            // Hapus semua selain angka
            $digits = preg_replace('/\D/', '', $rawNop);

            // Format: 2.2.3.3.3.4-1
            $nop =
                substr($digits, 0, 2) . "." .
                substr($digits, 2, 2) . "." .
                substr($digits, 4, 3) . "." .
                substr($digits, 7, 3) . "." .
                substr($digits, 10, 3) . "." .
                substr($digits, 13, 4) . "-" .
                substr($digits, 17, 1);

            // Query tagihan
            $tagihan = Tagihan::where('nop', $nop)
                ->select('id', 'masyarakat_id', 'nop', 'jumlah', 'sisa_tagihan', 'status', 'tanggal_lunas')
                ->with(['masyarakat:id,nama'])
                ->first();

            if (!$tagihan) {
                return ResponseFormatter::error(null, "Tagihan tidak ditemukan", 404);
            }

            return ResponseFormatter::success($tagihan, "Berhasil mendapatkan data tagihan");

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, $e->getMessage(), 500);
        }
    }



}