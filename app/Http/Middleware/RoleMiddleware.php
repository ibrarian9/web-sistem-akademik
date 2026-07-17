<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Safety check status user
        if ($user->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi administrator.');
        }

        $userRole = $user->role->nama ?? null;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect to their default dashboard if unauthorized for this route
        return match ($userRole) {
            'super_admin' => redirect()->route('super-admin.dashboard'),
            'tata_usaha' => redirect()->route('tata-usaha.dashboard'),
            'guru' => redirect()->route('guru.dashboard'),
            'murid' => redirect()->route('murid.dashboard'),
            'finance' => redirect()->route('finance.dashboard'),
            default => abort(403, 'Akses tidak sah.'),
        };
    }
}
