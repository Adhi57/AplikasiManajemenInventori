<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Jika user memiliki salah satu role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk halaman ini.');
    }
}