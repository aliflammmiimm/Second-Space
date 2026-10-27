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
        $user = Auth::user();

        if (!$user || !$user->isSeller()) {
            return redirect()->route('pelanggan.seller.status')
                ->with('error', 'Anda belum menjadi Seller. Ajukan permohonan terlebih dahulu.');
        }

        return $next($request);
    }
}