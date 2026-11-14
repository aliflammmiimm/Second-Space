<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\TawaranHarga;
use App\Services\OngkirService;
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
            Produk::with(['seller', 'jenisProduk'])
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

        $tawaran = null;
        if ($request->filled('tawaran_id') && $langsung && $produk) {
            $tawaran = TawaranHarga::where('TawaranID', (int) $request->input('tawaran_id'))
                ->where('PembeliID', Auth::id())
                ->where('ProdukID', $produk->ProdukID)
                ->where('Status', 'Diterima')
                ->first();

            if ($tawaran) {
                $total = (int) $tawaran->HargaTawaran;
            }
        }

        $sellerLat = $seller ? $seller->getLatToko() : OngkirService::DEFAULT_LAT;
        $sellerLon = $seller ? $seller->getLonToko() : OngkirService::DEFAULT_LON;

        return view('pelanggan.checkout', compact('items', 'total', 'langsung', 'produk', 'sellerId', 'seller', 'sellerLat', 'sellerLon', 'tawaran'));
    }

    /** Endpoint AJAX hitung ongkir real-time dari frontend */
    public function hitungOngkir(Request $request)
    {
        $latSeller = (float) $request->input('lat_seller', OngkirService::DEFAULT_LAT);
        $lonSeller = (float) $request->input('lon_seller', OngkirService::DEFAULT_LON);
        $latBuyer = (float) $request->input('lat_buyer', OngkirService::DEFAULT_LAT);
        $lonBuyer = (float) $request->input('lon_buyer', OngkirService::DEFAULT_LON);

        $kalkulasi = OngkirService::kalkulasi($latSeller, $lonSeller, $latBuyer, $lonBuyer);

        return response()->json($kalkulasi);
    }

    public function process(Request $request)
    {
        $request->validate([
            'produk_id' => 'nullable|integer',
            'seller_id' => 'nullable|integer',
            'tawaran_id' => 'nullable|integer',
            'cara_terima' => 'required|in:Ambil Sendiri,Diantar',
            'alamat' => 'exclude_unless:cara_terima,Diantar|required|string|min:5|max:255',
            'no_hp' => ['exclude_unless:cara_terima,Diantar', 'required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'di_makassar' => 'exclude_unless:cara_terima,Diantar|accepted',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'metode' => 'required|in:QRIS,COD',
        ], [
            'cara_terima.required' => 'Pilih cara terima barang.',
            'no_hp.required' => 'Nomor HP penerima wajib diisi untuk pengantaran.',
            'no_hp.regex' => 'Nomor HP tidak valid. Masukkan nomor telepon yang benar (contoh: 081234567890).',
            'di_makassar.accepted' => 'Centang untuk menyatakan alamat Anda berada di Kota Makassar.',
            'alamat.required' => 'Alamat pengantaran di Kota Makassar wajib diisi.',
            'alamat.min' => 'Alamat terlalu pendek, mohon isi dengan lengkap.',
            'metode.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $userId = (int) Auth::id();
        $langsung = $request->filled('produk_id');
        $sellerId = $request->input('seller_id');
        $tawaranId = $request->input('tawaran_id');
        $ids = $this->idTerpilih($langsung ? $request->input('produk_id') : null, $sellerId);
        $cara = $request->input('cara_terima');
        $alamat = $cara === 'Diantar' ? trim($request->input('alamat')) : 'Ambil sendiri di toko seller';
        $noHp = $cara === 'Diantar' ? trim($request->input('no_hp')) : null;
        $metode = $request->input('metode');

        $buyerLat = $request->filled('latitude') ? (float) $request->input('latitude') : null;
        $buyerLon = $request->filled('longitude') ? (float) $request->input('longitude') : null;

        if (empty($ids)) {
            return redirect()->route('pelanggan.keranjang')->with('error', 'Keranjang Anda kosong.');
        }

        try {
            $pesanan = DB::transaction(function () use ($userId, $ids, $langsung, $alamat, $metode, $cara, $noHp, $buyerLat, $buyerLon, $tawaranId) {
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

                $isOnline = in_array($metode, ['QRIS', 'Transfer']);
                $seller = $layak->first()->seller;

                if ($cara === 'Diantar' && !$seller->bisaAntar()) {
                    throw new \RuntimeException('Toko ini tidak melayani antar. Pilih ambil sendiri.');
                }
                if ($cara === 'Ambil Sendiri' && !$seller->bisaAmbil()) {
                    throw new \RuntimeException('Toko ini tidak melayani ambil sendiri. Pilih diantar.');
                }

                // Cek apakah ada tawaran harga yang disepakati untuk produk ini
                $tawaran = null;
                if ($tawaranId && $langsung && $layak->count() === 1) {
                    $tawaran = TawaranHarga::where('TawaranID', (int) $tawaranId)
                        ->where('PembeliID', $userId)
                        ->where('ProdukID', $layak->first()->ProdukID)
                        ->where('Status', 'Diterima')
                        ->first();
                }

                $subtotal = 0;
                foreach ($layak as $p) {
                    $hargaSatuan = ($tawaran && (int) $p->ProdukID === (int) $tawaran->ProdukID)
                        ? (int) $tawaran->HargaTawaran
                        : (int) $p->Harga;
                    $subtotal += $hargaSatuan;
                }

                $ongkir = 0;
                $jarakKM = null;

                // Hitung ongkir otomatis berdasarkan jarak KM jika diantar
                if ($cara === 'Diantar') {
                    $sellerLat = $seller->getLatToko();
                    $sellerLon = $seller->getLonToko();

                    $kalkulasi = OngkirService::kalkulasi($sellerLat, $sellerLon, $buyerLat, $buyerLon);
                    $ongkir = (int) $kalkulasi['ongkir'];
                    $jarakKM = (float) $kalkulasi['jarak_km'];
                }

                $total = $subtotal + $ongkir;
                $pinCod = $metode === 'COD' ? sprintf('%06d', mt_rand(100000, 999999)) : null;

                // Status pesanan: Langsung 'Menunggu Pembayaran' (QRIS) atau 'Diproses' (COD)
                $statusPesanan = $isOnline ? 'Menunggu Pembayaran' : 'Diproses';
                $batasBayar = $isOnline ? now()->addHours(self::BATAS_BAYAR_JAM) : null;

                $pesanan = Pesanan::create([
                    'UserID' => $userId,
                    'TanggalPesanan' => now(),
                    'TotalHarga' => $total,
                    'StatusPesanan' => $statusPesanan,
                    'MetodePembayaran' => $metode,
                    'KodePinCod' => $pinCod,
                    'CaraTerima' => $cara,
                    'Ongkir' => $ongkir,
                    'BatasBayar' => $batasBayar,
                ]);

                foreach ($layak as $p) {
                    $hargaSatuan = ($tawaran && (int) $p->ProdukID === (int) $tawaran->ProdukID)
                        ? (int) $tawaran->HargaTawaran
                        : (int) $p->Harga;

                    // Komisi website dipotong otomatis dari harga; sisanya untuk seller
                    $komisi = PesananDetail::hitungKomisi($hargaSatuan);

                    PesananDetail::create([
                        'PesananID' => $pesanan->PesananID,
                        'ProdukID' => $p->ProdukID,
                        'Jumlah' => 1,
                        'HargaSatuan' => $hargaSatuan,
                        'SubTotal' => $hargaSatuan,
                        'SellerID' => $p->SellerID,
                        'Komisi' => $komisi,
                        'PendapatanSeller' => $hargaSatuan - $komisi,
                        'TawaranID' => ($tawaran && (int) $p->ProdukID === (int) $tawaran->ProdukID) ? $tawaran->TawaranID : null,
                    ]);

                    // Jika online: dipesan dulu. COD: dipesan (akan diantar/diambil).
                    $p->Status = 'Dipesan';
                    $p->save();
                }

                // Tandai tawaran selesai jika sudah digunakan
                if ($tawaran) {
                    $tawaran->update(['Status' => 'Selesai']);
                }

                Pengiriman::create([
                    'PesananID' => $pesanan->PesananID,
                    'StatusPengiriman' => $isOnline ? null : 'Dikemas',
                    'AlamatPengiriman' => $alamat,
                    'NoHPPenerima' => $noHp,
                    'Latitude' => $buyerLat,
                    'Longitude' => $buyerLon,
                    'JarakKM' => $jarakKM,
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

                // Notifikasi ke Seller
                if ($seller && $seller->UserID) {
                    Notifikasi::kirim(
                        (int) $seller->UserID,
                        'pesanan_baru',
                        "Pesanan Baru Masuk #{$pesanan->PesananID}!",
                        "Pesanan baru masuk dari " . (Auth::user()->Nama ?? 'Pembeli') . " (" . $pesanan->CaraTerima . ", " . $pesanan->MetodePembayaran . "). Total: Rp " . number_format($pesanan->TotalHarga, 0, ',', '.') . ". Segera proses pesanan ini.",
                        route('seller.pesanan.index'),
                        'bi-bag-check-fill',
                        '#00B8BE'
                    );
                }

                return $pesanan;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('pelanggan.keranjang')->with('error', $e->getMessage());
        }

        if (in_array($metode, ['QRIS', 'Transfer'])) {
            return redirect()->route('pelanggan.pesanan.detail', $pesanan->PesananID)
                ->with('success', "Pesanan #{$pesanan->PesananID} berhasil dibuat. Total tagihan otomatis dihitung (termasuk ongkir). Silakan scan kode QRIS dan unggah bukti transfer.");
        }

        return redirect()->route('pelanggan.pesanan.detail', $pesanan->PesananID)
            ->with('success', "Pesanan COD #{$pesanan->PesananID} berhasil dibuat. Seller akan segera menyiapkan dan mengantar pesanan Anda.");
    }
}
