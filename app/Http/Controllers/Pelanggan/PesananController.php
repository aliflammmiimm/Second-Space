<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
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

        // Pastikan pesanan COD memiliki PIN Serah-Terima
        foreach ($pesananAktif as $p) {
            if ($p->MetodePembayaran === 'COD' && empty($p->KodePinCod)) {
                $p->KodePinCod = sprintf('%06d', mt_rand(100000, 999999));
                $p->save();
            }
        }

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

        // Pastikan PIN COD tersedia jika pesanan COD belum memiliki PIN
        if ($pesanan->MetodePembayaran === 'COD' && empty($pesanan->KodePinCod)) {
            $pesanan->KodePinCod = sprintf('%06d', mt_rand(100000, 999999));
            $pesanan->save();
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

            // COD: pembayaran dianggap valid setelah barang diterima & daftarkan tagihan komisi COD 4 hari
            if ($pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran !== 'Valid') {
                $pesanan->pembayaran->StatusPembayaran = 'Valid';
                $pesanan->pembayaran->save();
            }

            \App\Services\KomisiService::buatTagihanCodJikaPerlu($pesanan);

            // Notifikasi ke pembeli untuk memberi ulasan
            Notifikasi::kirim(
                (int) $pesanan->UserID,
                'pesanan_selesai',
                "Pesanan #{$pesanan->PesananID} Selesai! Beri Ulasan ⭐",
                "Terima kasih telah berbelanja di SecondSpace. Mohon luangkan waktu untuk memberikan rating & ulasan produk.",
                route('pelanggan.pesanan.detail', $pesanan->PesananID),
                'bi-star-fill',
                '#f59e0b'
            );

            // Notifikasi ke seller bahwa barang telah diterima
            $firstDetail = $pesanan->detail ? $pesanan->detail->first() : null;
            if ($firstDetail && $firstDetail->SellerID) {
                Notifikasi::kirim(
                    (int) $firstDetail->SellerID,
                    'pesanan_selesai',
                    "Pesanan #{$pesanan->PesananID} Telah Diterima Pembeli",
                    "Pembeli telah mengonfirmasi penerimaan barang. Transaksi dinyatakan selesai.",
                    route('seller.pesanan.index'),
                    'bi-check2-circle',
                    '#10b981'
                );
            }

            return ['success', "Pesanan #{$pesanan->PesananID} selesai. Terima kasih sudah berbelanja!"];
        });

        return back()->with($pesan[0], $pesan[1]);
    }

    /** Pembeli mengunggah bukti pembayaran QRIS */
    public function uploadBukti(\Illuminate\Http\Request $request, $id)
    {
        $pesanan = Pesanan::with('pembayaran')
            ->where('UserID', Auth::id())
            ->where('PesananID', $id)
            ->firstOrFail();

        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'bukti_bayar.required' => 'Pilih file bukti pembayaran terlebih dahulu.',
            'bukti_bayar.image' => 'File harus berupa gambar.',
            'bukti_bayar.mimes' => 'Format file yang didukung: JPG, PNG, WEBP.',
            'bukti_bayar.max' => 'Ukuran file bukti pembayaran maksimal 3 MB.',
        ]);

        $file = $request->file('bukti_bayar');
        $namaFile = 'bukti_' . $pesanan->PesananID . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('upload/pembayaran'), $namaFile);

        $pembayaran = $pesanan->pembayaran ?: new \App\Models\Pembayaran([
            'PesananID' => $pesanan->PesananID,
            'Metode' => $pesanan->MetodePembayaran,
        ]);

        // Hapus file lama jika ada
        if ($pembayaran->BuktiBayar && file_exists(public_path('upload/pembayaran/' . $pembayaran->BuktiBayar))) {
            @unlink(public_path('upload/pembayaran/' . $pembayaran->BuktiBayar));
        }

        $pembayaran->BuktiBayar = $namaFile;
        $pembayaran->DiunggahPada = now();
        $pembayaran->StatusPembayaran = 'Menunggu';
        $pembayaran->save();

        return back()->with('success', 'Bukti pembayaran QRIS berhasil diunggah. Mohon tunggu verifikasi admin.');
    }
}