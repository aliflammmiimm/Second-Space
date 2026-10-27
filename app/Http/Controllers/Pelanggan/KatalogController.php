<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\JenisProduk;
use App\Models\PengajuanSeller;
use App\Models\Produk;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = JenisProduk::aktif()->orderBy('NamaJenis', 'asc')->get();

        $query = Produk::tersedia()->with(['jenisProduk', 'seller:UserID,Nama']);

        // Filter kategori (berdasarkan nama, agar URL lama tetap jalan)
        if ($request->filled('jenis')) {
            $jenisName = $request->input('jenis');
            $query->whereHas('jenisProduk', fn ($q) => $q->where('NamaJenis', $jenisName));
        }

        // Pencarian nama produk
        if ($request->filled('search')) {
            $query->where('NamaProduk', 'like', '%' . $request->input('search') . '%');
        }

        // Rentang harga
        if ($request->filled('min')) {
            $query->where('Harga', '>=', (int) $request->input('min'));
        }
        if ($request->filled('max')) {
            $query->where('Harga', '<=', (int) $request->input('max'));
        }

        // Kondisi barang
        if ($request->filled('kondisi') && array_key_exists($request->input('kondisi'), Produk::KONDISI)) {
            $query->where('Kondisi', $request->input('kondisi'));
        }

        // Pengurutan
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('Harga', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('Harga', 'desc');
        } else {
            $query->latest('ProdukID');
        }

        $produk = $query->paginate(12)->withQueryString();

        return view('pelanggan.katalog', compact('categories', 'produk'));
    }

    public function detail($id)
    {
        $produk = Produk::with(['jenisProduk', 'seller:UserID,Nama,is_seller'])
            ->where('Status', '!=', 'Disembunyikan')
            ->findOrFail($id);

        $relatedProducts = Produk::tersedia()
            ->where('JenisID', $produk->JenisID)
            ->where('ProdukID', '!=', $produk->ProdukID)
            ->take(4)
            ->get();

        // Link WhatsApp ke seller (nomor dari pengajuan seller yang disetujui)
        $waUrl = null;
        $hp = PengajuanSeller::where('UserID', $produk->SellerID)
            ->where('Status', 'approved')
            ->latest('PengajuanID')
            ->value('NoHP');

        if ($hp) {
            $nomor = preg_replace('/\D/', '', $hp);
            if (str_starts_with($nomor, '0')) {
                $nomor = '62' . substr($nomor, 1);
            }
            $waUrl = 'https://wa.me/' . $nomor . '?text='
                . rawurlencode('Halo, saya tertarik dengan "' . $produk->NamaProduk . '" di SecondSpace.');
        }

        return view('pelanggan.detail', compact('produk', 'relatedProducts', 'waUrl'));
    }
}