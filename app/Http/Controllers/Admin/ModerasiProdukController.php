<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;

class ModerasiProdukController extends Controller
{
    public function sembunyikan($id)
    {
        $p = Produk::findOrFail($id);
        if ($p->Status !== 'Tersedia') {
            return back()->with('error', 'Hanya produk berstatus Tersedia yang bisa disembunyikan.');
        }
        $p->Status = 'Disembunyikan';
        $p->save();
        return back()->with('success', 'Produk disembunyikan.');
    }

    public function tampilkan($id)
    {
        $p = Produk::findOrFail($id);
        if ($p->Status !== 'Disembunyikan') {
            return back()->with('error', 'Produk tidak dalam status tersembunyi.');
        }
        $p->Status = 'Tersedia';
        $p->save();
        return back()->with('success', 'Produk ditampilkan kembali.');
    }
}