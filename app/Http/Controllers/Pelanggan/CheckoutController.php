<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Services\ReservasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private const BATAS_BAYAR_JAM = 24;

    /** Id produk yang di-checkout: 1 produk (Beli Sekarang) atau seluruh isi keranjang. */
    private function idTerpilih($langsungId): array
    {
        if ($langsungId) {
            return [(int) $langsungId];
        }

        return Keranjang::where('UserID', Auth::id())
            ->pluck('ProdukID')
            ->map(fn ($i) => (int) $i)
            ->all();
    }

    private function layak($produk)
    {
        return $produk->filter(
            fn ($p) => $p->bisaDibeli() && (int) $p->SellerID !== (int) Auth::id()
        )->values();
    }

    public function show($id = null)
    {
        ReservasiService::lepasKedaluwarsa();

        $ids = $this->idTerpilih($id);

        $items = $this->layak(
            Produk::with(['seller:UserID,Nama,is_seller', 'jenisProduk'])
                ->whereIn('ProdukID', $ids)
                ->get()
        );

        if ($items->isEmpty()) {
            return redirect()->route('pelanggan.keranjang')
                ->with('error', 'Tidak ada barang yang bisa di-checkout.');
        }

        $total = $items->sum('Harga');
        $langsung = (bool) $id;
        $produk = $langsung ? $items->first() : null;

        return view('pelanggan.checkout', compact('items', 'total', 'langsung', 'produk'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'produk_id' => 'nullable|integer',
            'alamat' => 'required|string|min:5|max:255',
            'metode' => 'required|in:Transfer,COD',
        ], [
            'alamat.required' => 'Alamat pengiriman wajib diisi.',
            'alamat.min' => 'Alamat terlalu pendek, mohon isi dengan lengkap.',
            'metode.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $userId = (int) Auth::id();
        $langsung = $request->filled('produk_id');
        $ids = $this->idTerpilih($langsung ? $request->input('produk_id') : null);
        $alamat = trim($request->input('alamat'));
        $metode = $request->input('metode');

        if (empty($ids)) {
            return redirect()->route('pelanggan.keranjang')->with('error', 'Keranjang Anda kosong.');
        }

        try {
            $pesanan = DB::transaction(function () use ($userId, $ids, $langsung, $alamat, $metode) {
                // Kunci baris produk agar tidak ada dua pembeli yang mengambil barang yang sama
                $produk = Produk::with('seller')
                    ->whereIn('ProdukID', $ids)
                    ->lockForUpdate()
                    ->get();

                $layak = $this->layak($produk);

                if ($layak->isEmpty() || ($langsung && $layak->count() !== 1)) {
                    throw new \RuntimeException('Barang sudah tidak tersedia atau tidak dapat Anda beli.');
                }

                $transfer = $metode === 'Transfer';
                $total = $layak->sum('Harga');

                $pesanan = Pesanan::create([
                    'UserID' => $userId,
                    'TanggalPesanan' => now(),
                    'TotalHarga' => $total,
                    'StatusPesanan' => $transfer ? 'Menunggu Pembayaran' : 'Diproses',
                    'MetodePembayaran' => $metode,
                    'BatasBayar' => $transfer ? now()->addHours(self::BATAS_BAYAR_JAM) : null,
                ]);

                foreach ($layak as $p) {
                    // Komisi website dipotong otomatis dari harga; sisanya untuk seller
                    $komisi = PesananDetail::hitungKomisi((int) $p->Harga);

                    PesananDetail::create([
                        'PesananID' => $pesanan->PesananID,
                        'ProdukID' => $p->ProdukID,
                        'Jumlah' => 1,
                        'HargaSatuan' => $p->Harga,
                        'SubTotal' => $p->Harga,
                        'SellerID' => $p->SellerID,
                        'Komisi' => $komisi,
                        'PendapatanSeller' => (int) $p->Harga - $komisi,
                    ]);

                    // Transfer: dipesan dulu sampai pembayaran valid. COD: langsung terjual.
                    $p->Status = $transfer ? 'Dipesan' : 'Terjual';
                    $p->save();
                }

                Pengiriman::create([
                    'PesananID' => $pesanan->PesananID,
                    'StatusPengiriman' => 'Dikemas',
                    'AlamatPengiriman' => $alamat,
                    'TanggalUpdate' => now(),
                ]);

                Pembayaran::create([
                    'PesananID' => $pesanan->PesananID,
                    'Metode' => $metode,
                    'StatusPembayaran' => 'Menunggu',
                ]);

                Keranjang::where('UserID', $userId)
                    ->whereIn('ProdukID', $layak->pluck('ProdukID'))
                    ->delete();

                return $pesanan;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('pelanggan.keranjang')->with('error', $e->getMessage());
        }

                if ($metode === 'Transfer') {
            // Dapatkan Snap Token Midtrans untuk pembayaran transfer
            $snapToken = \App\Services\MidtransService::createSnapToken($pesanan);
            if ($snapToken) {
                $pesanan->pembayaran()->update(['SnapToken' => $snapToken]);
            }

            return redirect()->route('pelanggan.pesanan.detail', $pesanan->PesananID)
                ->with('success', "Pesanan #{$pesanan->PesananID} berhasil dibuat. Silakan selesaikan pembayaran.")
                ->with('auto_pay', true);
        }

        return redirect()->route('pelanggan.pesanan.detail', $pesanan->PesananID)
            ->with('success', "Pesanan #{$pesanan->PesananID} berhasil dibuat. Bayar tunai saat barang tiba.");
    }
}