<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek authentication
        if (!Auth::check()) {
            return $this->handleUnauthenticated($request);
        }

        $user = Auth::user();

        // Cek role authorization
        if (!in_array($user->role, $roles)) {
            return $this->handleUnauthorized($request);
        }

        return $next($request);
    }

    /**
     * Handle unauthenticated user
     */
    private function handleUnauthenticated(Request $request)
    {
        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'error' => 'Token tidak valid atau sudah expired'
            ], 401);
        }

        return redirect('login');
    }

    /**
     * Handle unauthorized user (wrong role)
     */
    private function handleUnauthorized(Request $request)
    {
        if ($this->isApiRequest($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
                'error' => 'Anda tidak memiliki hak akses ke endpoint ini'
            ], 403);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Anda tidak memiliki hak akses ke halaman ini');
    }

    /**
     * Determine if the request is for API
     */
    private function isApiRequest(Request $request): bool
    {
        // Opsi 1: Berdasarkan prefix route
        return $request->is('api/*');
        
        // Opsi 2: Berdasarkan header Accept
        return $request->wantsJson();
        
        // Opsi 3: Berdasarkan route name prefix
        return str_starts_with($request->route()->getName() ?? '', 'api.');
        
        // Opsi 4: Kombinasi beberapa kondisi
        return $request->is('api/*') || 
            $request->wantsJson() || 
            $request->header('Content-Type') === 'application/json';
    }
}
