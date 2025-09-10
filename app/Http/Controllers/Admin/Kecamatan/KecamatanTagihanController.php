<?php

namespace App\Http\Controllers\Admin\Kecamatan;

use App\Models\Tagihan;
use App\Models\AdminDesa;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class KecamatanTagihanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()){
            $tagihan = AdminDesa::with('desa')->get();

            return ResponseFormatter::success($tagihan,"Berhasil mengambil data tagihan");
        }
        return view('Admin.Kecamatan.Tagihan.Index');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'nominal' => 'required'
        ]);

        $desa = AdminDesa::find($request->id);
        $desa->diterima_kec = $desa->diterima_kec + $request->nominal;
        $desa->save();

        return ResponseFormatter::success($desa, "Status tagihan berhasil diperbarui");
    }
}
