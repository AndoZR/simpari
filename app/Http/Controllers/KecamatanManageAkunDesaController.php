<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Desa;
use App\Models\User;
use App\Models\AdminDesa;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KecamatanManageAkunDesaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $adminKecamatan = auth()->user();
            $kecamatanId = $adminKecamatan->id;

            $data = AdminDesa::with(['desa','user'])->get();

            return ResponseFormatter::success($data, 'Data Akun Desa Berhasil Diambil');
        }

        $villages = Desa::where('district_id', 3511080)->get();

        return view('Admin.Kecamatan.ManageAkunDesa.Index', compact('villages'));
    }

    public function tambahAkunDesa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|unique:users,nik|digits:16',
            'password' => 'required',
            'village_id' => 'required|string|unique:admin_desa,village_id',
            'telepon' => 'required|numeric|unique:admin_desa,telepon',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors(),422);
        };

        try {
            DB::beginTransaction();  // MULAI TRANSAKSI

            $dataUser = User::create([
                'nik' => $request->nik,
                'password' => bcrypt($request->password),
                'role' => 'admin_desa',
            ]);

            $data = AdminDesa::create([
                'user_id' => $dataUser->id,
                'village_id' => $request->village_id,
                'telepon' => $request->telepon,
                'tagihan' => 0,
                'sisa_tagihan' => 0,
                'diterima_kec' => 0,
            ]);

            DB::commit();  // SIMPAN SEMUA JIKA BERHASIL

            return ResponseFormatter::success($data, "Data Admin Desa Berhasil Dibuat!");
        } catch (Exception $e) {

            DB::rollBack();  // BATALKAN SEMUA JIKA ADA ERROR

            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal disimpan. Kesalahan Server", 500);
        }
    }

    public function updateAkunDesa(Request $request, $id) {
        $adminDesa = AdminDesa::findOrFail($id);
        $user = $adminDesa->user;

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|digits:16|unique:users,nik,' . $user->id,
            'password' => 'nullable',
            'village_id' => 'required|string|unique:admin_desa,village_id,' . $adminDesa->id,
            'telepon' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors(),422);
        };

        try {
            // 1. Ambil data AdminDesa berdasarkan ID
            $adminDesa = AdminDesa::findOrFail($id);

            // 2. Ambil data user dari relasi admin_desa
            $user = User::findOrFail($adminDesa->user_id);

            // 3. Update User
            $user->update([
                'nik' => $request->nik,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'role' => 'admin_desa',
            ]);

            // 4. Update AdminDesa
            $adminDesa->update([
                'village_id' => $request->village_id,
                'telepon' => $request->telepon,
            ]);

            return ResponseFormatter::success($adminDesa, "Data Akun Desa Berhasil Diubah!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal disimpan. Kesalahan Server", 500);
        }
    }
}
