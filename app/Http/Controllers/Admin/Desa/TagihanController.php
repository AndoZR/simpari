<?php
namespace App\Http\Controllers\Admin\Desa;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use App\Exports\TagihanExport;
use App\Imports\TagihanImport;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()){
            $village_id = Auth::user()->adminDesa->village_id;
            $tagihan = Tagihan::with(['masyarakat.user', 'masyarakat.pemungut.user'])
            ->whereHas('masyarakat', function($q) use ($village_id) {
                $q->where('village_id', $village_id);
            })->get();

            return ResponseFormatter::success($tagihan,"Berhasil mengambil data tagihan");
        }
        return view('Admin.Desa.Tagihan.Index');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tagihan,id',
        ]);

        $tagihan = Tagihan::find($request->id);
        $tagihan->status = "didesa";
        $tagihan->uang_didesa += $tagihan->uang_dipemungut;
        $tagihan->uang_dipemungut = 0;
        $tagihan->save();

        return ResponseFormatter::success($tagihan, "Status tagihan berhasil diperbarui");
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors(),422);
        }
        
        try {

            Excel::import(new TagihanImport, $request->file('file'));
            return ResponseFormatter::success(null, "Import excel berhasil");
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), "Import excel gagal", 500);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new TagihanExport, 'Tagihan.xlsx');
            // return ResponseFormatter::success(null, "Export excel berhasil");
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), "Import excel gagal", 500);
        }
    }
}
