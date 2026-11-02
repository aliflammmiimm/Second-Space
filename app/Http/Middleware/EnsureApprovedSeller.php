<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || !$user->isSeller()) {
            return redirect()->route('pelanggan.seller.status')
                ->with('error', 'Anda belum menjadi Seller. Ajukan permohonan terlebih dahulu.');
        }

        // Jika seller sedang diblokir, larang menambah atau mengubah produk
        if (!empty($user->is_blocked) && $request->routeIs('seller.produk.create', 'seller.produk.store', 'seller.produk.edit', 'seller.produk.update')) {
            return redirect()->route('seller.komisi.index')
                ->with('error', 'Toko Anda saat ini sedang diblokir (' . ($user->AlasanBlokir ?? 'Tunggakan komisi COD') . '). Selesaikan kewajiban tagihan komisi atau hubungi admin.');
        }

        return $next($request);
    }
}