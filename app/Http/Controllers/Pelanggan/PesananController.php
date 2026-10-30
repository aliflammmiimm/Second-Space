<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /** Pesanan aktif (belum selesai / dibatalkan / kedaluwarsa) */
    public function pesananAktif()
    {
        $pesananAktif = Pesanan::with(['detail.produk', 'pembayaran', 'pengiriman'])
            ->where('UserID', Auth::id())
            ->whereNotIn('StatusPesanan', ['Selesai', 'Dibatalkan', 'Kedaluwarsa'])
            ->latest('PesananID')
            ->paginate(10);

        return view('pelanggan.pesanan_aktif', compact('pesananAktif'));
    }

    /** Riwayat seluruh pesanan milik user yang login */
    public function index()
    {
        $pesanan = Pesanan::with(['detail.produk', 'pembayaran', 'pengiriman'])
            ->where('UserID', Auth::id())
            ->latest('PesananID')
            ->paginate(10);

        return view('pelanggan.pesanan', compact('pesanan'));
    }

    /** Detail pesanan; pesanan milik orang lain otomatis 404 (anti IDOR) */
        public function detail($id)
    {
        $pesanan = Pesanan::with(['detail.produk.jenisProduk', 'detail.seller:UserID,Nama', 'pembayaran', 'pengiriman'])
            ->where('UserID', Auth::id())
            ->where('PesananID', $id)
            ->firstOrFail();

        // Pastikan Snap Token Midtrans tersedia jika pesanan transfer masih menunggu bayar
        if ($pesanan->MetodePembayaran === 'Transfer' && 
            $pesanan->pembayaran && 
            $pesanan->pembayaran->StatusPembayaran === 'Menunggu' && 
            in_array($pesanan->StatusPesanan, ['Menunggu Pembayaran', 'Diproses'])) {
            
            if (!$pesanan->pembayaran->SnapToken) {
                $token = \App\Services\MidtransService::createSnapToken($pesanan);
                if ($token) {
                    $pesanan->pembayaran->SnapToken = $token;
                    $pesanan->pembayaran->save();
                }
            }
        }

        return view('pelanggan.pesanan_detail', compact('pesanan'));
    }

    /** Pembeli mengonfirmasi barang sudah diterima -> pesanan Selesai. */
    public function terima($id)
    {
        $pesan = DB::transaction(function () use ($id) {
            // Hanya pesanan milik sendiri (pesanan orang lain = 404)
            $pesanan = Pesanan::with(['pengiriman', 'pembayaran'])
                ->where('UserID', Auth::id())
                ->where('PesananID', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($pesanan->StatusPesanan, ['Dikirim', 'Sampai Tujuan'], true)) {
                return ['error', 'Pesanan hanya bisa dikonfirmasi setelah dikirim.'];
            }

            $pesanan->StatusPesanan = 'Selesai';
            $pesanan->save();

            if ($pesanan->pengiriman) {
                $pesanan->pengiriman->StatusPengiriman = 'Sampai Tujuan';
                $pesanan->pengiriman->TanggalUpdate = now();
                $pesanan->pengiriman->save();
            }

            // COD: pembayaran dianggap valid setelah barang diterima
            if ($pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran !== 'Valid') {
                $pesanan->pembayaran->StatusPembayaran = 'Valid';
                $pesanan->pembayaran->save();
            }

            return ['success', "Pesanan #{$pesanan->PesananID} selesai. Terima kasih sudah berbelanja!"];
        });

        return back()->with($pesan[0], $pesan[1]);
    }
}