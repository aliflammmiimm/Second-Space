<?php

namespace App\Http\Controllers;

use App\Models\JenisProduk;
use App\Models\Produk;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Produk::tersedia()
            ->with('jenisProduk')
            ->latest('ProdukID')
            ->take(10)
            ->get();

        // Agar carousel Swiper loop: true dapat berputar terus-menerus (infinite loop) tanpa pernah macet di slide terakhir,
        // Swiper membutuhkan minimal slide berlebih (> 10-12 slide) agar buffer kloning kanan-kiri tidak pernah habis.
        // Berapa pun jumlah produk di DB (misal 1, 2, 6, dll), kita perbanyak siklusnya hingga minimal 12 slide untuk showcase.
        $sliderProducts = collect($featuredProducts);
        if ($sliderProducts->isNotEmpty() && $sliderProducts->count() < 12) {
            $duplicated = collect();
            while ($duplicated->count() < 12) {
                $duplicated = $duplicated->concat($featuredProducts);
            }
            $sliderProducts = $duplicated;
        }

        $categories = JenisProduk::aktif()
            ->withCount(['produk' => fn ($q) => $q->where('Status', 'Tersedia')])
            ->orderBy('NamaJenis')
            ->get();

        return view('home', [
            'featuredProducts' => $sliderProducts,
            'categories' => $categories,
        ]);
    }
}