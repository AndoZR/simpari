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
            $dataStatus = ['belum', 'cicilan', 'didesa', 'dipemungut', 'lunas'];
            // Ambil pemungut beserta relasi masyarakat + tagihan
            $pemungut = Pemungut::with(['user', 'masyarakat.tagihan'])->firstOrFail();

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
    {
        try {
            // Validasi cuma riwayat_tagihan aja
            $request->validate([
                'riwayat_tagihan' => 'required',
            ]);

            // Decode JSON dari request
            $riwayat = json_decode($request->riwayat_tagihan, true);
            $nop = $riwayat[0]["nop"];
            $nominal = $riwayat[0]["bayar"]["0"]["nominal"];
            $tanggal_bayar = $riwayat[0]["bayar"]["0"]["timestamp"];

            // Cari tagihan berdasarkan NOP
            $tagihan = Tagihan::where('nop', $nop)->firstOrFail();

            // Logika update status
            if ($request->nominal == $tagihan->jumlah) {
                $tagihan->tanggal_lunas = $request->tanggal_lunas ?? now();
                $tagihan->status = 'lunas';
                $tagihan->sisa_tagihan = 0;
            } else {
                $tagihan->sisa_tagihan = $tagihan->sisa_tagihan - $nominal;
                $tagihan->status = 'cicilan';
                Cicilan::create([
                    'tagihan_id' => $tagihan->id,
                    'jumlah_bayar' => $nominal,
                    'tanggal_bayar' => $tanggal_bayar,
                ]);
                if ($tagihan->sisa_tagihan <= 0) {
                    $tagihan->status = 'lunas';
                    $tagihan->sisa_tagihan = 0;
                    $tagihan->tanggal_lunas = now();
                }
            }

            $tagihan->save();

            return response()->json([
                'message' => 'Bayar tagihan berhasil',
                'tagihan' => $tagihan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat bayar tagihan',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function showCicilan(Request $request)
    {
        try{
            $request->validate([
            'user_id' => 'required|exists:users,id',
            ]);

            $userId = $request->user_id;

            // Ambil cicilan untuk tagihan milik user tertentu
            $cicilan = Cicilan::get();

            return response()->json([
                'status' => 'success',
                'data' => $cicilan
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeCicilan(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $tagihan = Tagihan::findOrFail($request->tagihan_id);

            // Cek sisa_tagihan
            if ($request->jumlah_bayar > $tagihan->sisa_tagihan) {
                return response()->json(['error' => 'Jumlah bayar melebihi sisa tagihan'], 422);
            }

            // Simpan cicilan
            $cicilan = Cicilan::create([
                'tagihan_id' => $tagihan->id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'keterangan' => $request->keterangan ?? null,
            ]);

            // Kurangi sisa_tagihan
            $tagihan->sisa_tagihan -= $request->jumlah_bayar;

            // Update status jika lunas
            if ($tagihan->sisa_tagihan <= 0) {
                $tagihan->status = 'lunas';
            } else {
                $tagihan->status = 'cicilan';
            }

            $tagihan->save();

            return response()->json(['message' => 'Cicilan berhasil disimpan', 'cicilan' => $cicilan]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateCicilan(Request $request)
    {
        $request->validate([
            'jumlah_bayar' => 'nullable|numeric|min:1',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $cicilan = Cicilan::findOrFail($request->cicilan_id);
            $tagihan = $cicilan->tagihan;

            // Jika jumlah_bayar diubah, update sisa_tagihan
            if ($request->filled('jumlah_bayar')) {
                $selisih = $request->jumlah_bayar - $cicilan->jumlah_bayar;
                if ($selisih > $tagihan->sisa_tagihan) {
                    return response()->json(['error' => 'Jumlah bayar melebihi sisa tagihan'], 422);
                }

                $tagihan->sisa_tagihan -= $selisih;
                $cicilan->jumlah_bayar = $request->jumlah_bayar;
            }

            if ($request->filled('tanggal_bayar')) {
                $cicilan->tanggal_bayar = $request->tanggal_bayar;
            }

            if ($request->filled('keterangan')) {
                $cicilan->keterangan = $request->keterangan;
            }

            // Update status tagihan
            $tagihan->status = $tagihan->sisa_tagihan <= 0 ? 'lunas' : 'cicilan';
            $tagihan->save();
            $cicilan->save();

            return response()->json(['message' => 'Cicilan berhasil diupdate', 'cicilan' => $cicilan]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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

    public function getTagihanByNop(Request $request)
    {
        $nop = $request->nop;

        // jika input tidak ada titik, coba format
        if (strpos($nop, '.') === false) {
            // hapus semua karakter non-digit
            $digits = preg_replace('/\D/', '', $nop);

            // cek panjang minimal (misal 18-19 digit sesuai format)
            if (strlen($digits) < 18) {
                return ResponseFormatter::error(null, "NOP tidak valid", 422);
            }

            // format NOP
            $nop = $this->formatNop($nop);
        } else {
            // jika input ada titik, validasi dengan regex
            $pattern = '/^\d{2}\.\d{2}\.\d{3}\.\d{3}\.\d{3}-\d{4}\.\d$/';
            if (!preg_match($pattern, $nop)) {
                return ResponseFormatter::error(null, "Format NOP tidak valid, hilangkan tanda baca atau gunakan format xx.xx.xxx.xxx.xxx-xxxx.x", 422);
            }
        }

        // query tagihan
        $tagihan = Tagihan::where('nop', $nop)
            ->select('id','masyarakat_id', 'nop', 'jumlah', 'sisa_tagihan', 'status', 'tanggal_lunas')
            ->with(['masyarakat:id,nama'])
            ->first();

        if (!$tagihan) {
            return ResponseFormatter::error(null, "Tagihan tidak ditemukan", 404);
        }

        return ResponseFormatter::success($tagihan, "Berhasil mendapatkan data tagihan");
    }




}