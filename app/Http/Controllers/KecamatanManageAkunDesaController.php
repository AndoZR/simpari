<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\AdminDesa;
use App\Models\Desa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KecamatanManageAkunDesaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $adminKecamatan = auth()->user();
            $kecamatanId = $adminKecamatan->id;

            $data = AdminDesa::with('desa')->get();

            return ResponseFormatter::success($data, 'Data Akun Desa Berhasil Diambil');
        }

        $villages = Desa::where('district_id', 3511080)->get();

        return view('Admin.Kecamatan.ManageAkunDesa.Index', compact('villages'));
    }

    public function tambahAkunDesa(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'nik' => 'required|string|unique:users,nik|max:16',
        //     'password' => 'required',
        //     'nama' => 'required|string|max:255',
        //     'telepon' => 'required|numeric',
        //     'alamat' => 'required|string',
        // ]);

        // if ($validator->fails()) {
        //     return ResponseFormatter::error(null,$validator->errors(),422);
        // };

        try {
            $dataUser = User::create([
                'nik' => $request->nik,
                'password' => bcrypt($request->password),
                'role' => 'admin_desa',
            ]);

            $data = AdminDesa::create([
                'user_id' => $dataUser->id,  // WAJIB ADA
                'village_id' => $request->village_id,
                'telepon' => $request->telepon,
                'tagihan' => $request->tagihan,
                // 'sisa_tagihan' => $request->sisa_tagihan,
                // 'diterima_kec' => $request->diterima_kec,
            ]);



            return ResponseFormatter::success($data, "Data Admin Desa Berhasil Dibuat!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal disimpan. Kesalahan Server", 500);
        }
    }

    public function updateAkunDesa(Request $request, $id) {
        // $validator = Validator::make($request->all(), [
        //     'nik' => 'string|max:16|unique:users,nik,'. $id,
        //     'nama' => 'string|max:255',
        //     'telepon' => 'numeric',
        //     'alamat' => 'string',
        // ]);

        // if ($validator->fails()) {
        //     return ResponseFormatter::error(null,$validator->errors(),422);
        // };

        try {
            // 1. Ambil data AdminDesa berdasarkan ID
            $adminDesa = AdminDesa::findOrFail($id);

            // 2. Ambil data user dari relasi admin_desa
            $user = User::findOrFail($adminDesa->user_id);

            // 3. Update User
            $user->update([
                'nik' => $request->nik,
                // Update password hanya jika diisi
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'role' => 'admin_desa',
            ]);

            // 4. Update AdminDesa
            $adminDesa->update([
                'village_id' => $request->village_id,
                'telepon' => $request->telepon,
                'tagihan' => $request->tagihan,
                // 'sisa_tagihan' => $request->sisa_tagihan,
                // 'diterima_kec' => $request->diterima_kec,
            ]);

            return ResponseFormatter::success($adminDesa, "Data Akun Desa Berhasil Diubah!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal disimpan. Kesalahan Server", 500);
        }
    }

    public function hapusAkunDesa($id){
        try{
            $data = User::find($id);
            $data->delete();
            return ResponseFormatter::success("Data Pemunugut Berhasil Dihapus!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal dihapus. Kesalahan Server", 500);
        }
    }
}
