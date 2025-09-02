<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()){
            $tagihan = Tagihan::with('masyarakat.user')->get();

            return ResponseFormatter::success($tagihan,"Berhasil mengambil data tagihan");
        }
        return view('Admin.Tagihan.Index');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tagihan,id',
            'status' => 'required|'
        ]);

        $tagihan = Tagihan::find($request->id);
        $tagihan->status = $request->status;
        $tagihan->save();

        return ResponseFormatter::success($tagihan, "Status tagihan berhasil diperbarui");
    }
}
