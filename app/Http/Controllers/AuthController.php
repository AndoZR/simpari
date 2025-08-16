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
            $validated = $request->validate([
                'nik' => 'required|string',
                'password' => 'required|string',
            ], [
                'nik.required' => 'NIK wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);
            // 🔹 Ambil credentials
            $credentials = $request->only('nik', 'password');

            // 🔹 Cek login
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login success',
                    'user' => $user,
                    'token' => $token,
                ], 200);
            }
            // 🔹 Jika gagal login
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login',
                'error' => 'Invalid credentials',
            ], 401);
        } catch (Exception $e) {
            // 🔹 Catat error ke log
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage(), // bisa dihapus kalau tidak mau expose error
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

        return view('Dashborad.Main')->with(['message' => 'Register & login success', 'user' => $user]);
    }

    // Login
    public function loginWeb(Request $request)
    {
        $credentials = $request->only('nik', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return view('Dashborad.Main')->with(['message' => 'Login success', 'user' => $user]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // Logout
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        return view('Auth.Login');
    }

    public function test()
    {
        $var = ["1", "2", "3"];
        $var[1] = null; 
        dd($var);
    }
}