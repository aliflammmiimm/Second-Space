<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Services\ReservasiService;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public function index()
    {
        ReservasiService::lepasKedaluwarsa();

        $item = Keranjang::where('UserID', Auth::id())
            ->with(['produk.seller:UserID,Nama,is_seller', 'produk.jenisProduk'])
            ->get();

        // Harga selalu dari DB (produk), bukan dari keranjang
        $item->each(fn ($k) => $k->tersedia = $k->produk && $k->produk->bisaDibeli());
        $total = $item->where('tersedia', true)->sum(fn ($k) => $k->produk->Harga);

        return view('pelanggan.keranjang', compact('item', 'total'));
    }

    public function tambah($id)
    {
        $produk = Produk::tersedia()->find($id);

        if (!$produk) {
            return back()->with('error', 'Produk sudah tidak tersedia.');
        }
        if ($produk->SellerID === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat membeli barang milik sendiri.');
        }

        $k = Keranjang::firstOrCreate(['UserID' => Auth::id(), 'ProdukID' => $produk->ProdukID]);

        return back()->with($k->wasRecentlyCreated ? 'success' : 'error',
            $k->wasRecentlyCreated ? 'Ditambahkan ke keranjang.' : 'Produk sudah ada di keranjang (setiap barang hanya 1 unit).');
    }

    public function hapus($id)
    {
        Keranjang::where('UserID', Auth::id())->where('ProdukID', $id)->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}