<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\UlasanProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UlasanController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'pesanan_id' => 'required|integer',
            'produk_id' => 'required|integer',
            'bintang' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'bintang.required' => 'Pilih rating bintang (1 sampai 5).',
            'bintang.min' => 'Rating bintang minimal 1.',
            'bintang.max' => 'Rating bintang maksimal 5.',
            'foto.image' => 'File ulasan harus berupa foto gambar.',
            'foto.max' => 'Ukuran foto ulasan maksimal 2 MB.',
        ]);

        $userId = (int) Auth::id();
        $pesanan = Pesanan::where('PesananID', (int) $data['pesanan_id'])
            ->where('UserID', $userId)
            ->where('StatusPesanan', 'Selesai')
            ->firstOrFail();

        $detail = PesananDetail::where('PesananID', $pesanan->PesananID)
            ->where('ProdukID', (int) $data['produk_id'])
            ->firstOrFail();

        // Cek apakah produk dalam pesanan ini sudah pernah diulas
        $ada = UlasanProduk::where('PesananID', $pesanan->PesananID)
            ->where('ProdukID', $detail->ProdukID)
            ->exists();

        if ($ada) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini pada pesanan tersebut.');
        }

        $fotoNama = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $folder = public_path('upload/ulasan');
            if (!file_exists($folder)) {
                @mkdir($folder, 0755, true);
            }
            $ext = $request->file('foto')->getClientOriginalExtension();
            $fotoNama = 'ulasan_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $request->file('foto')->move($folder, $fotoNama);
        }

        $ulasan = UlasanProduk::create([
            'ProdukID' => $detail->ProdukID,
            'UserID' => $userId,
            'PesananID' => $pesanan->PesananID,
            'Bintang' => (int) $data['bintang'],
            'Komentar' => $data['komentar'] ? trim($data['komentar']) : null,
            'Foto' => $fotoNama,
            'DibuatPada' => now('Asia/Makassar'),
        ]);

        // Kirimkan notifikasi otomatis ke Seller
        $produk = Produk::find($detail->ProdukID);
        $sellerId = $detail->SellerID ?: ($produk ? $produk->SellerID : null);
        if ($sellerId) {
            Notifikasi::kirim(
                (int) $sellerId,
                'ulasan_baru',
                'Ulasan Baru Diterima (' . $ulasan->Bintang . '★)',
                'Pembeli memberikan ulasan ' . $ulasan->Bintang . ' bintang untuk produk "' . ($produk->NamaProduk ?? 'Produk') . '".',
                route('pelanggan.produk.detail', $detail->ProdukID),
                'bi-star-fill',
                '#f59e0b'
            );
        }

        return back()->with('success', 'Terima kasih! Ulasan dan rating produk Anda berhasil dikirim.');
    }
}

