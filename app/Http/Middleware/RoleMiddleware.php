<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Cek apakah role user ada di daftar role yang diperbolehkan
        if (!in_array($user->role, $roles)) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki hak akses ke halaman ini');
        }

        return $next($request);
    }
}
