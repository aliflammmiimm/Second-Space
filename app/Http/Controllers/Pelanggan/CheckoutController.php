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

    /** Id produk yang di-checkout: 1 produk (Beli Sekarang) atau isi keranjang dari 1 seller. */
    private function idTerpilih($langsungId, $sellerId = null): array
    {
        if ($langsungId) {
            return [(int) $langsungId];
        }

        $query = Keranjang::where('UserID', Auth::id());
        if ($sellerId) {
            $query->whereHas('produk', fn ($q) => $q->where('SellerID', (int) $sellerId));
        }

        return $query->pluck('ProdukID')
            ->map(fn ($i) => (int) $i)
            ->all();
    }

    private function layak($produk)
    {
        return $produk->filter(
            fn ($p) => $p->bisaDibeli() && (int) $p->SellerID !== (int) Auth::id()
        )->values();
    }

    public function show(Request $request, $id = null)
    {
        ReservasiService::lepasKedaluwarsa();

        $sellerId = $request->query('seller_id');
        $ids = $this->idTerpilih($id, $sellerId);

        $items = $this->layak(
            Produk::with(['seller:UserID,Nama,is_seller', 'jenisProduk'])
                ->whereIn('ProdukID', $ids)
                ->get()
        );

        if ($items->isEmpty()) {
            return redirect()->route('pelanggan.keranjang')
                ->with('error', 'Tidak ada barang yang bisa di-checkout.');
        }

        // Jika dalam keranjang ada barang dari beberapa seller berbeda dan belum difilter
        $sellerCount = $items->pluck('SellerID')->unique()->count();
        if ($sellerCount > 1) {
            return redirect()->route('pelanggan.keranjang')
                ->with('error', 'Keranjang Anda berisi produk dari beberapa toko berbeda. Silakan gunakan tombol "Checkout Toko Ini" pada toko yang ingin Anda proses.');
        }

        $sellerId = $items->first()->SellerID;
        $total = $items->sum('Harga');
        $langsung = (bool) $id;
        $produk = $langsung ? $items->first() : null;
        $seller = $items->first()->seller;

        return view('pelanggan.checkout', compact('items', 'total', 'langsung', 'produk', 'sellerId', 'seller'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'produk_id' => 'nullable|integer',
            'seller_id' => 'nullable|integer',
            'cara_terima' => 'required|in:Ambil Sendiri,Diantar',
            'alamat' => 'required_if:cara_terima,Diantar|nullable|string|min:5|max:255',
            'no_hp' => ['required_if:cara_terima,Diantar', 'nullable', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'di_makassar' => 'accepted_if:cara_terima,Diantar',
            'metode' => 'required|in:Transfer,COD',
        ], [
            'cara_terima.required' => 'Pilih cara terima barang.',
            'no_hp.required_if' => 'Nomor HP wajib diisi untuk pengantaran.',
            'no_hp.regex' => 'Nomor HP tidak valid.',
            'di_makassar.accepted_if' => 'Centang untuk menyatakan alamat Anda berada di Kota Makassar.',
            'alamat.required_if' => 'Alamat pengantaran wajib diisi.',
            'alamat.required' => 'Alamat pengiriman wajib diisi.',
            'alamat.min' => 'Alamat terlalu pendek, mohon isi dengan lengkap.',
            'metode.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $userId = (int) Auth::id();
        $langsung = $request->filled('produk_id');
        $sellerId = $request->input('seller_id');
        $ids = $this->idTerpilih($langsung ? $request->input('produk_id') : null, $sellerId);
        $cara = $request->input('cara_terima');
        $alamat = $cara === 'Diantar' ? trim($request->input('alamat')) : 'Ambil sendiri di toko seller';
        $noHp = $cara === 'Diantar' ? trim($request->input('no_hp')) : null;
        $metode = $request->input('metode');

        if (empty($ids)) {
            return redirect()->route('pelanggan.keranjang')->with('error', 'Keranjang Anda kosong.');
        }

        try {
            $pesanan = DB::transaction(function () use ($userId, $ids, $langsung, $alamat, $metode, $cara, $noHp) {
                // Kunci baris produk agar tidak ada dua pembeli yang mengambil barang yang sama
                $produk = Produk::with('seller')
                    ->whereIn('ProdukID', $ids)
                    ->lockForUpdate()
                    ->get();

                $layak = $this->layak($produk);

                if ($layak->isEmpty() || ($langsung && $layak->count() !== 1)) {
                    throw new \RuntimeException('Barang sudah tidak tersedia atau tidak dapat Anda beli.');
                }

                if ($layak->pluck('SellerID')->unique()->count() > 1) {
                    throw new \RuntimeException('Satu pesanan hanya boleh memproses barang dari satu toko yang sama.');
                }

                $transfer = $metode === 'Transfer';
                $seller = $layak->first()->seller;

                if ($cara === 'Diantar' && !$seller->bisaAntar()) {
                    throw new \RuntimeException('Toko ini tidak melayani antar. Pilih ambil sendiri.');
                }
                if ($cara === 'Ambil Sendiri' && !$seller->bisaAmbil()) {
                    throw new \RuntimeException('Toko ini tidak melayani ambil sendiri. Pilih diantar.');
                }

                $ongkir = $cara === 'Diantar' ? $seller->ongkirAntar() : 0;
                $total = $layak->sum('Harga') + $ongkir;
                $pinCod = $metode === 'COD' ? sprintf('%06d', mt_rand(100000, 999999)) : null;

                $pesanan = Pesanan::create([
                    'UserID' => $userId,
                    'TanggalPesanan' => now(),
                    'TotalHarga' => $total,
                    'StatusPesanan' => $transfer ? 'Menunggu Pembayaran' : 'Diproses',
                    'MetodePembayaran' => $metode,
                    'KodePinCod' => $pinCod,
                    'CaraTerima' => $cara,
                    'Ongkir' => $ongkir,
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
                    'StatusPengiriman' => $transfer ? null : 'Dikemas',
                    'AlamatPengiriman' => $alamat,
                    'NoHPPenerima' => $noHp,
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