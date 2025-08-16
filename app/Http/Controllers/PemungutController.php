<?php

namespace App\Http\Controllers;

use App\Models\Cicilan;
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

    public function showTagihan()
    {
        $user = Auth::user();
        // Ambil user pemungut
        $pemungut = User::with([
            'masyarakatPlotting.masyarakat.tagihan'
        ])->findOrFail($user->id);

        // Ambil daftar masyarakat yg di-plotting
        $masyarakatList = $pemungut->masyarakatPlotting->map(function ($plot) {
            return [
                'masyarakat_id' => $plot->masyarakat->id,
                'nama'          => $plot->masyarakat->name,
                'alamat'        => $plot->masyarakat->alamat ?? null,
                'tagihan'       => $plot->masyarakat->tagihan->map(function ($tagih) {
                    return [
                        'id'              => $tagih->id,
                        'jumlah'          => $tagih->jumlah,
                        'status'          => $tagih->status,
                        'sisa_tagihan'    => $tagih->sisa_tagihan,
                        'keterangan'      => $tagih->keterangan,
                        'tanggal_tagihan' => $tagih->tanggal_tagihan,
                        'tanggal_lunas'   => $tagih->tanggal_lunas,
                        // tambahkan detail cicilan
                        'cicilan'         => $tagih->cicilan->map(function ($c) {
                            return [
                                'id'           => $c->id,
                                'jumlah_bayar' => $c->jumlah_bayar,
                                'tanggal_bayar'=> $c->tanggal_bayar,
                                'keterangan'   => $c->keterangan,
                            ];
                        })
                    ];
                })
            ];
        });


        return response()->json([
            'pemungut' => [
                'id' => $pemungut->id,
                'nama' => $pemungut->name,
            ],
            'masyarakat' => $masyarakatList
        ]);
    }

    public function updateStatus(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'status' => 'required',
            ]);

            // Cari tagihan
            $tagihan = Tagihan::findOrFail($request->id);

            // Update status
            $tagihan->status = $request->status;
            $tagihan->keterangan = $request->keterangan ?? $tagihan->keterangan;
            $tagihan->tanggal_lunas = $request->tanggal_lunas ?? $tagihan->tanggal_lunas;

            // Update tanggal_lunas jika ada
            if ($request->status === 'lunas' && $request->tanggal_lunas) {
                $tagihan->tanggal_lunas = $request->tanggal_lunas;
            }

            $tagihan->save();

            return response()->json([
                'message' => 'Status tagihan berhasil diupdate',
                'tagihan' => $tagihan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat update status',
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
}