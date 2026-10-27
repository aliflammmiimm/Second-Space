<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSeller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProdukTersedia = Produk::where('Status', 'Tersedia')->count();
        $totalProduk = Produk::count();
        $totalTerjual = Produk::where('Status', 'Terjual')->count();

        $totalPesanan = Pesanan::count();
        $totalPesananAktif = Pesanan::whereNotIn('StatusPesanan', ['Selesai', 'Dibatalkan', 'Kedaluwarsa'])->count();

        $totalPelanggan = User::where('Role', 'Pelanggan')->count();
        $totalSeller = User::where('Role', 'Pelanggan')->where('is_seller', 1)->count();
        $totalBuyer = $totalPelanggan - $totalSeller;
        $pengajuanPending = PengajuanSeller::where('Status', 'pending')->count();

        // Pendapatan website = total komisi dari pesanan yang sudah Selesai
        $totalKomisi = \App\Models\PesananDetail::whereHas(
            'pesanan', fn ($q) => $q->where('StatusPesanan', 'Selesai')
        )->sum('Komisi');

        $recentOrders = Pesanan::with(['user', 'pembayaran', 'pengiriman'])
            ->latest('PesananID')
            ->take(5)
            ->get();

        $latestProducts = Produk::with('jenisProduk')
            ->latest('ProdukID')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProdukTersedia',
            'totalProduk',
            'totalTerjual',
            'totalPesananAktif',
            'totalPesanan',
            'totalBuyer',
            'totalSeller',
            'pengajuanPending',
            'totalKomisi',
            'recentOrders',
            'latestProducts'
        ));
    }
}