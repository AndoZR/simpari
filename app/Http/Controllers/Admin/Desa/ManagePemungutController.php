<?php

namespace App\Http\Controllers\Admin\Desa;

use Exception;
use App\Models\Desa;
use App\Models\User;
use App\Models\Pemungut;
use App\Models\Masyarakat;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ManagePemungutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $adminDesaId = auth()->user()->adminDesa->id;

            // $adminDesaId = auth()->user()->id;
            
            $data = User::where('role', 'pemungut')
                ->whereHas('pemungutData', function ($q) use ($adminDesaId) {
                    $q->where('admin_desa_id', $adminDesaId);
                })
                ->with('pemungutData')
                ->get();


            return ResponseFormatter::success($data, 'Data Pemungut Berhasil Diambil');
        }
        return view('Admin.Desa.ManagePemungut.Index');
    }

    public function tambahPemungut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|unique:users,nik|max:16',
            'password' => 'required',
            'nama' => 'required|string|max:255',
            'telepon' => 'required|numeric',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors(),422);
        };

        try {
            $user = User::create([
                'nik' => $request->nik,
                'password' => bcrypt($request->password),
                'role' => 'pemungut',
            ]);

            $adminDesaId = auth()->user()->adminDesa->id;

            // $adminDesaId = auth()->user()->id;

            $data = Pemungut::create([
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'user_id' => $user->id,
                'admin_desa_id' => $adminDesaId
            ]);

            return ResponseFormatter::success($data, "Data Pemungut Berhasil Dibuat!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal disimpan. Kesalahan Server", 500);
        }
    }

    public function updatePemungut(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'nik' => 'string|max:16|unique:users,nik,'. $id,
            'password' => 'nullable',
            'nama' => 'string|max:255',
            'telepon' => 'numeric',
            'alamat' => 'string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors(),422);
        };

        try {
            $user = User::find($id);
            $updateUser = [
                'nik' => $request->nik,
            ];

            // Update data artikel
            $user->update($updateUser);

            $data = Pemungut::where('user_id', $id)->first();
            $updateData = [
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
            ];

            // Update data artikel
            $data->update($updateData);

            return ResponseFormatter::success($data, "Data Pemungut Berhasil Diubah!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal disimpan. Kesalahan Server", 500);
        }
    }

    public function hapusPemungut($id){
        try{
            $data = User::find($id);
            $data->delete();
            return ResponseFormatter::success("Data Pemunugut Berhasil Dihapus!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal dihapus. Kesalahan Server", 500);
        }
    }

    public function getPlotting(Request $request, $idPemungut)
    {
        // Ambil desa_id dari adminDesa yang sedang login
        $desaId = auth()->user()->adminDesa->village_id;

        // Ambil semua masyarakat sesuai desa + relasi user
        $dataPlotting = Masyarakat::with('tagihan')
            ->where('village_id', $desaId)
            ->get();

        // Tambahin kolom 'is_plotted' → true/false
        $dataPlotting->map(function ($item) use ($idPemungut) {
            $item->is_plotted = $item->pemungut_id == $idPemungut;
            return $item;
        });

        if ($request->ajax()) {
            return ResponseFormatter::success($dataPlotting, 'Data Plotting Berhasil Diambil');
        }
    }

    // public function toggle(Request $request)
    // {
    //     $request->validate([
    //         'masyarakat_id' => 'required|exists:masyarakat,id',
    //         'pemungut_id'   => 'required|integer',
    //         'checked'       => 'required|boolean',
    //     ]);

    //     $masyarakat = Masyarakat::find($request->masyarakat_id);

    //     if ($request->checked) {
    //         // Assign ke pemungut
    //         $masyarakat->pemungut_id = $request->pemungut_id;
    //     } else {
    //         // Hapus plotting
    //         $masyarakat->pemungut_id = null;
    //     }

    //     $masyarakat->save();

    //     return ResponseFormatter::success($masyarakat, 'Plotting berhasil diperbarui');
    // }

    // Desa/ManagePemungutController.php
    public function toggleAll(Request $request)
    {
        $request->validate([
            'pemungut_id'   => 'required|exists:pemungut,id',
            'checked'       => 'required|boolean',
            'masyarakat_ids'=> 'required|array'
        ]);

        $ids = $request->masyarakat_ids;

        if ($request->checked) {
            // Assign semua masyarakat ke pemungut
            Masyarakat::whereIn('id', $ids)
                ->update(['pemungut_id' => $request->pemungut_id]);
        } else {
            // Hapus plotting (set pemungut_id null)
            Masyarakat::whereIn('id', $ids)
                ->update(['pemungut_id' => null]);
        }

        return ResponseFormatter::success(null, 'Plotting berhasil diperbarui massal');
    }

}
