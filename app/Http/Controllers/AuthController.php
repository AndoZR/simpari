<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
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

        return response()->json(['message' => 'Register & login success', 'user' => $user]);
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->only('nik', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['message' => 'Login success', 'user' => $user, 'token' => $token]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
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