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

        $categories = JenisProduk::aktif()
            ->withCount(['produk' => fn ($q) => $q->where('Status', 'Tersedia')])
            ->orderBy('NamaJenis')
            ->get();

        return view('home', compact('featuredProducts', 'categories'));
    }
}