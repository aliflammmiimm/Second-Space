<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = null;
        if ($request->is('admin*')) {
            $user = Auth::guard('admin')->user();
        } elseif ($request->is('seller*')) {
            $user = Auth::guard('seller')->user();
        } else {
            $user = Auth::guard('web')->user() ?? Auth::user();
        }

        if (!$user) {
            if ($request->is('admin*')) {
                return redirect()->route('admin.login')->with('error', 'Silakan login sebagai administrator terlebih dahulu.');
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = strtolower($user->Role ?? '');
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles, true)) {
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Akses dibatasi untuk admin.');
            } elseif ($user->isSeller()) {
                return redirect()->route('seller.produk.index')->with('error', 'Akses halaman tidak diizinkan untuk peran Anda.');
            } else {
                return redirect()->route('pelanggan.katalog')->with('error', 'Akses halaman tidak diizinkan untuk peran Anda.');
            }
        }

        return $next($request);
    }
}