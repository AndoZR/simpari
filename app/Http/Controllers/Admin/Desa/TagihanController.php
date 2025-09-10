<?php
namespace App\Http\Controllers\Admin\Desa;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()){
            $tagihan = Tagihan::with('masyarakat.user','masyarakat.pemungut.user')->get();

            return ResponseFormatter::success($tagihan,"Berhasil mengambil data tagihan");
        }
        return view('Admin.Desa.Tagihan.Index');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tagihan,id',
            'status' => 'required|'
        ]);

        $tagihan = Tagihan::find($request->id);
        $tagihan->status = $request->status;
        $tagihan->uang_didesa = $tagihan->uang_dipemungut;
        $tagihan->uang_dipemungut = 0;
        $tagihan->save();

        return ResponseFormatter::success($tagihan, "Status tagihan berhasil diperbarui");
    }
}
