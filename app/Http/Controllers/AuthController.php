<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        try {
            // 🔹 Validasi
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'nik' => 'required|string|max:20|unique:users,nik|regex:/^[0-9]+$/',
                'password' => 'required|string|min:8',
                'role' => 'required',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'nik.required' => 'NIK wajib diisi.',
                'nik.unique' => 'NIK sudah terdaftar.',
                'nik.regex' => 'NIK hanya boleh angka.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'role.required' => 'Role wajib dipilih.',
                'role.in' => 'Role tidak valid.',
            ]);

            // 🔹 Simpan user baru
            $user = User::create([
                'name' => $validated['name'],
                'nik' => $validated['nik'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // 🔹 Auto login setelah register
            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Register & login success',
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (Exception $e) {
            // 🔹 Catat error ke log
            Log::error('Register error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat register',
                'error' => $e->getMessage(), // bisa dihapus kalau tidak mau expose error ke user
            ], 500);
        }
    }

    // Login
    public function login(Request $request)
    {
        try {
            // 🔹 Validasi input
            $validated = $request->validate([
                'nik' => 'required|string',
                'password' => 'required|string',
            ], [
                'nik.required' => 'NIK wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            // 🔹 Ambil credentials dari input yang tervalidasi
            $credentials = [
                'nik' => $validated['nik'],
                'password' => $validated['password']
            ];

            // 🔹 Cek login
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'user' => $user,
                    'token' => $token,
                ], 200);
            }

            // 🔹 Jika gagal login
            return response()->json([
                'success' => false,
                'message' => 'NIK atau password salah',
            ], 401);

        } catch (Exception $e) {
            // 🔹 Catat error ke log
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat login',
            ], 500);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logout success']);
    }

    // WEB SESSION
    // Register
    public function registerWeb(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:users,nik',
            'password' => 'required|string|min:6',
            'role' => 'required|string', // sesuaikan jika ada pilihan khusus
        ]);

        $user = User::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);

        return redirect('dashboard')->with(['message' => 'Register & login success', 'user' => $user]);
    }

    // Login
    public function loginWeb(Request $request)
    {
        try {
            // 🔹 Validasi input
            $validated = $request->validate([
                'nik' => 'required|string',
                'password' => 'required|string',
            ], [
                'nik.required' => 'NIK wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            // 🔹 Ambil credentials dari input yang tervalidasi
            $credentials = [
                'nik' => $validated['nik'],
                'password' => $validated['password']
            ];

            // 🔹 Cek login
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                return redirect('dashboard')->with(['message' => 'Login success', 'user' => $user]);
            }

            // 🔹 Jika gagal login
            return back()->withErrors([
                'nik' => 'NIK atau password salah.',
            ]);

        } catch (Exception $e) {
            // 🔹 Catat error ke log
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat login',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Logout
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        return view('Auth.Login');
    }
}