<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Desa;
use App\Models\User;
use App\Models\Pemungut;
use App\Models\Masyarakat;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ManagePemungutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::where('role', 'pemungut')->with('pemungutData')->get();

            return ResponseFormatter::success($data, 'Data Pemungut Berhasil Diambil');
        }
        return view('Admin.ManagePemungut.Index');
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

            $data = Pemungut::create([
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'user_id' => $user->id,
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

    public function plottingGetDesa(Request $request){
        if ($request->ajax()) {
            $dataDesa = Desa::where('district_id',3511080)->get();

            return ResponseFormatter::success($dataDesa, 'Data Pemungut Berhasil Diambil');
        }
    }

    public function getMasyarakatByDesa($desaId)
    {
        $masyarakat = Masyarakat::where('village_id', $desaId)
            ->select('id', 'nama', 'telepon', 'alamat', 'user_id') // tambahkan user_id
            ->with('user')
            ->get();


        return ResponseFormatter::success($masyarakat, 'Data Masyarakat By Desa Berhasil Diambil');
    }

    public function getPlotting(Request $request, $idPemungut)
    {
        $dataPlotting = Masyarakat::where('pemungut_id', $idPemungut)->with('user')->get();
        if ($request->ajax()) {

            return ResponseFormatter::success($dataPlotting, 'Data Plotting Berhasil Diambil');
        }
    }

    public function sendPlot(Request $request, $idPemungut)
    {
        $validator = Validator::make($request->all(), [
            'masyarakat' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors(),422);
        };

        try {
            foreach ($request->masyarakat as $id) {
                $masyarakat = Masyarakat::find($id);
                if ($masyarakat) {
                    $masyarakat->update([
                        'pemungut_id' => $idPemungut,
                    ]);
                }
            }

            return ResponseFormatter::success(null, "Data Plotting Berhasil Ditambah!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal disimpan. Kesalahan Server", 500);
        }
    }

    public function hapusPlotting($id){
        try{
            $masyarakat = Masyarakat::find($id);
            $masyarakat->update([
                'pemungut_id' => null,
            ]);
            return ResponseFormatter::success("Data Plotting Berhasil Dihapus!");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error($e->getMessage(), "Data gagal dihapus. Kesalahan Server", 500);
        }
    }
}
