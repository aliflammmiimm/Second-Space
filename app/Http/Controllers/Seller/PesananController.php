<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    // Hanya item milik Seller ini; data pembeli dibatasi pada nama & alamat kirim
    public function index()
    {
        $sellerId = Auth::id();
        $item = PesananDetail::where('SellerID', $sellerId)
            ->with(['produk', 'pesanan.user:UserID,Nama', 'pesanan.pengiriman', 'pesanan.pembayaran'])
            ->latest('DetailID')
            ->paginate(15);

        $perluOngkirCount = PesananDetail::where('SellerID', $sellerId)
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Menunggu Konfirmasi Ongkir'))
            ->count();

        $perluKemasCount = PesananDetail::where('SellerID', $sellerId)
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Diproses'))
            ->count();

        return view('seller.pesanan.index', compact('item', 'perluOngkirCount', 'perluKemasCount'));
    }

    /**
     * Seller menandai pesanan Dikemas (siap diantar / siap diambil).
     */
    public function kemas($id)
    {
        $uid = (int) Auth::id();
        PesananDetail::where('PesananID', $id)->where('SellerID', $uid)->firstOrFail();

        $pesan = DB::transaction(function () use ($id, $uid) {
            $pesanan = Pesanan::with('pengiriman')->where('PesananID', $id)->lockForUpdate()->firstOrFail();

            if (PesananDetail::where('PesananID', $id)->where('SellerID', '!=', $uid)->exists()) {
                return ['error', 'Pesanan ini berisi barang dari beberapa seller dan diproses oleh admin.'];
            }

            if ($pesanan->StatusPesanan !== 'Diproses') {
                return ['error', 'Pesanan hanya bisa dikemas setelah pembayaran valid (status Diproses).'];
            }

            $pesanan->StatusPesanan = 'Dikemas';
            $pesanan->save();

            if ($pesanan->pengiriman) {
                $pesanan->pengiriman->StatusPengiriman = 'Dikemas';
                $pesanan->pengiriman->TanggalUpdate = now();
                $pesanan->pengiriman->save();
            }

            Notifikasi::kirim(
                (int) $pesanan->UserID,
                'pesanan_dikemas',
                "Pesanan #{$pesanan->PesananID} Sedang Dikemas",
                "Seller sedang mengemas pesanan Anda (" . ($pesanan->CaraTerima === 'Diantar' ? 'siap diantar' : 'siap diambil di toko') . ').',
                route('pelanggan.pesanan.detail', $pesanan->PesananID),
                'bi-box-seam-fill',
                '#3b82f6'
            );

            return ['success', "Pesanan #{$pesanan->PesananID} dikemas (" . ($pesanan->CaraTerima === 'Diantar' ? 'siap diantar' : 'siap diambil') . ').'];
        });

        return back()->with($pesan[0], $pesan[1]);
    }

    /**
     * Seller menandai pesanan sebagai dikirim (berangkat mengantar / input resi).
     * Hanya untuk pesanan yang SELURUH itemnya milik seller ini.
     * Pesanan campuran beberapa seller tetap diproses admin.
     */
    public function kirim(Request $request, $id)
    {
        $request->validate([
            'NoResi' => 'nullable|string|max:100',
        ]);

        $uid = (int) Auth::id();

        // Seller harus punya item di pesanan ini (selain itu 404)
        PesananDetail::where('PesananID', $id)->where('SellerID', $uid)->firstOrFail();

        $pesan = DB::transaction(function () use ($request, $id, $uid) {
            $pesanan = Pesanan::with('pengiriman')
                ->where('PesananID', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if (PesananDetail::where('PesananID', $id)->where('SellerID', '!=', $uid)->exists()) {
                return ['error', 'Pesanan ini berisi barang dari beberapa seller dan diproses oleh admin.'];
            }

            if (!in_array($pesanan->StatusPesanan, ['Diproses', 'Dikemas'], true)) {
                return ['error', 'Pesanan hanya bisa dikirim setelah pembayaran valid (status Diproses/Dikemas).'];
            }

            if ($pesanan->CaraTerima !== 'Diantar') {
                return ['error', 'Pesanan ambil sendiri tidak perlu dikirim. Tunggu pembeli datang ke toko lalu verifikasi.'];
            }

            $pesanan->StatusPesanan = 'Dikirim';
            $pesanan->save();

            if ($pesanan->pengiriman) {
                $pesanan->pengiriman->StatusPengiriman = 'Dikirim';
                if ($request->filled('NoResi')) {
                    $pesanan->pengiriman->NoResi = trim($request->input('NoResi'));
                }
                $pesanan->pengiriman->TanggalUpdate = now();
                $pesanan->pengiriman->save();
            }

            Notifikasi::kirim(
                (int) $pesanan->UserID,
                'pesanan_dikirim',
                "Pesanan #{$pesanan->PesananID} Sedang Diantar!",
                "Pesanan Anda sedang dalam perjalanan diantar oleh seller.",
                route('pelanggan.pesanan.detail', $pesanan->PesananID),
                'bi-truck',
                '#10b981'
            );

            return ['success', "Pesanan #{$pesanan->PesananID} ditandai dikirim."];
        });

        return back()->with($pesan[0], $pesan[1]);
    }

    /**
     * Seller menyelesaikan transaksi COD dengan memasukkan Kode PIN 6 digit yang diberikan pembeli saat serah terima.
     */
    public function selesaikanCod(Request $request, $id)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
        ], [
            'pin.required' => 'Kode PIN Serah-Terima COD wajib diisi.',
            'pin.size' => 'Kode PIN harus 6 digit angka.',
        ]);

        $uid = (int) Auth::id();
        PesananDetail::where('PesananID', $id)->where('SellerID', $uid)->firstOrFail();

        $pesan = DB::transaction(function () use ($request, $id) {
            $pesanan = Pesanan::with(['pengiriman', 'pembayaran'])
                ->where('PesananID', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pesanan->MetodePembayaran !== 'COD') {
                return ['error', 'Metode verifikasi PIN hanya berlaku untuk pesanan Cash on Delivery (COD).'];
            }

            if (!in_array($pesanan->StatusPesanan, ['Diproses', 'Dikemas', 'Dikirim', 'Sampai Tujuan'], true)) {
                return ['error', 'Status pesanan tidak valid untuk diselesaikan.'];
            }

            if (!$pesanan->KodePinCod || trim($request->input('pin')) !== trim($pesanan->KodePinCod)) {
                return ['error', 'Kode PIN Serah-Terima tidak cocok dengan data pembeli. Mohon pastikan kembali PIN dari pembeli.'];
            }

            $pesanan->StatusPesanan = 'Selesai';
            $pesanan->save();

            if ($pesanan->pengiriman) {
                $pesanan->pengiriman->StatusPengiriman = 'Sampai Tujuan';
                $pesanan->pengiriman->TanggalUpdate = now();
                $pesanan->pengiriman->save();
            }

            if ($pesanan->pembayaran) {
                $pesanan->pembayaran->StatusPembayaran = 'Valid';
                $pesanan->pembayaran->save();
            }

            // Daftarkan tagihan komisi COD 4 hari
            \App\Services\KomisiService::buatTagihanCodJikaPerlu($pesanan);

            Notifikasi::kirim(
                (int) $pesanan->UserID,
                'pesanan_selesai',
                "Pesanan COD #{$pesanan->PesananID} Selesai! ⭐",
                "Transaksi telah selesai diverifikasi. Mohon luangkan waktu untuk memberikan rating & ulasan bagi produk yang Anda beli.",
                route('pelanggan.pesanan.detail', $pesanan->PesananID),
                'bi-star-fill',
                '#f59e0b'
            );

            return ['success', "Pesanan COD #{$pesanan->PesananID} berhasil diverifikasi dan diselesaikan. Tagihan komisi 10% telah diterbitkan."];
        });

        return back()->with($pesan[0], $pesan[1]);
    }

    /**
     * Seller menentukan ongkos kirim berdasarkan jarak alamat pembeli di Makassar.
     */
    public function konfirmasiOngkir(Request $request, $id)
    {
        $request->validate([
            'ongkir' => 'required|integer|min:0|max:500000',
        ], [
            'ongkir.required' => 'Nominal ongkos kirim wajib diisi.',
            'ongkir.integer' => 'Nominal ongkos kirim harus berupa angka.',
            'ongkir.min' => 'Nominal ongkos kirim minimal Rp 0 (gratis).',
            'ongkir.max' => 'Nominal ongkos kirim maksimal Rp 500.000.',
        ]);

        $uid = (int) Auth::id();
        PesananDetail::where('PesananID', $id)->where('SellerID', $uid)->firstOrFail();

        $pesan = DB::transaction(function () use ($request, $id, $uid) {
            $pesanan = Pesanan::with(['detail', 'pengiriman', 'pembayaran'])
                ->where('PesananID', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if (PesananDetail::where('PesananID', $id)->where('SellerID', '!=', $uid)->exists()) {
                return ['error', 'Pesanan ini berisi barang dari beberapa seller dan diproses oleh admin.'];
            }

            if ($pesanan->StatusPesanan !== 'Menunggu Konfirmasi Ongkir') {
                return ['error', 'Pesanan ini sudah tidak dalam tahap konfirmasi ongkir.'];
            }

            $ongkir = (int) $request->input('ongkir');
            $subtotalBarang = (int) $pesanan->detail->sum('SubTotal');
            $totalBaru = $subtotalBarang + $ongkir;

            $pesanan->Ongkir = $ongkir;
            $pesanan->TotalHarga = $totalBaru;

            $isOnline = in_array($pesanan->MetodePembayaran, ['QRIS', 'Transfer']);
            if ($isOnline) {
                $pesanan->StatusPesanan = 'Menunggu Pembayaran';
                $pesanan->BatasBayar = now()->addHours(24);
            } else {
                // COD langsung diproses (seller siap mengemas)
                $pesanan->StatusPesanan = 'Diproses';
                if ($pesanan->pengiriman) {
                    $pesanan->pengiriman->StatusPengiriman = 'Dikemas';
                    $pesanan->pengiriman->TanggalUpdate = now();
                    $pesanan->pengiriman->save();
                }
            }

            $pesanan->save();

            return ['success', "Ongkos kirim Pesanan #{$pesanan->PesananID} berhasil ditentukan sebesar Rp " . number_format($ongkir, 0, ',', '.') . '. Total tagihan pesanan telah diperbarui.'];
        });

        return back()->with($pesan[0], $pesan[1]);
    }

    /**
     * Tampilan cetak label pengiriman paket (format thermal / label sticker / kertas A4).
     */
    public function cetakLabel($id)
    {
        $sellerId = (int) Auth::id();
        PesananDetail::where('PesananID', $id)->where('SellerID', $sellerId)->firstOrFail();

        $pesanan = Pesanan::with([
            'detail' => fn ($q) => $q->where('SellerID', $sellerId)->with('produk'),
            'user:UserID,Nama,NoHP_Email',
            'pengiriman',
            'pembayaran'
        ])->findOrFail($id);

        $seller = Auth::user();

        return view('seller.pesanan.label', compact('pesanan', 'seller'));
    }
}