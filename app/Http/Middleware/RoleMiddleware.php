<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        if (!in_array($request->user()->role, $roles)) {
            // Arahkan ke dashboard sesuai role yang sah
            $dashboardRoute = match ($request->user()->role) {
                'mahasiswa' => 'mahasiswa.dashboard',
                'dosen' => 'dosen.dashboard',
                'admin' => 'admin.dashboard',
                default => 'login',
            };

            return redirect()->route($dashboardRoute)->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
