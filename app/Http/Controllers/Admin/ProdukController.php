<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisProduk;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    // Admin hanya melihat & memoderasi (sembunyikan/tampilkan ada di ModerasiProdukController)
    public function index(Request $request)
    {
        $categories = JenisProduk::orderBy('NamaJenis')->get();
        $query = Produk::with(['jenisProduk', 'seller:UserID,Nama']);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('NamaProduk', 'like', "%{$s}%");
        }

        if ($request->filled('jenis_id')) {
            $query->where('JenisID', $request->input('jenis_id'));
        }

        if ($request->filled('status')) {
            $query->where('Status', $request->input('status'));
        }

        $produk = $query->latest('ProdukID')->paginate(10)->withQueryString();

        return view('admin.produk.index', compact('produk', 'categories'));
    }
}