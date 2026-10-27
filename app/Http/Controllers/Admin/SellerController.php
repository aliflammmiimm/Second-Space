<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SellerController extends Controller
{
    // Status pesanan yang dianggap sudah tidak aktif
    private const STATUS_NONAKTIF = ['Selesai', 'Dibatalkan', 'Kedaluwarsa'];

    public function index(Request $request)
    {
        $nonaktif = self::STATUS_NONAKTIF;

        $sellers = User::where('is_seller', 1)
            ->where('Role', 'Pelanggan')
            ->withCount([
                'produk',
                'produk as produk_tersedia_count' => fn ($q) => $q->where('Status', 'Tersedia'),
                'produk as produk_terjual_count' => fn ($q) => $q->where('Status', 'Terjual'),
                'itemPesanan as pesanan_aktif_count' => fn ($q) => $q->whereHas(
                    'pesanan',
                    fn ($p) => $p->whereNotIn('StatusPesanan', $nonaktif)
                ),
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->input('search');
                $q->where(function ($w) use ($s) {
                    $w->where('Nama', 'like', "%{$s}%")
                      ->orWhere('NoHP_Email', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('UserID')
            ->paginate(15)
            ->withQueryString();

        return view('admin.seller.daftar', compact('sellers'));
    }

    public function destroy($id)
    {
        if ((int) $id === (int) Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $nonaktif = self::STATUS_NONAKTIF;
        $fileHapus = [];

        $hasil = DB::transaction(function () use ($id, $nonaktif, &$fileHapus) {
            $seller = User::where('UserID', $id)->lockForUpdate()->first();

            if (!$seller || !$seller->is_seller || $seller->isAdmin()) {
                return ['error', 'Akun seller tidak ditemukan.'];
            }

            // Blokir jika masih ada pesanan aktif (sebagai penjual maupun pembeli)
            $aktifSebagaiPenjual = PesananDetail::where('SellerID', $seller->UserID)
                ->whereHas('pesanan', fn ($q) => $q->whereNotIn('StatusPesanan', $nonaktif))
                ->exists();

            $aktifSebagaiPembeli = Pesanan::where('UserID', $seller->UserID)
                ->whereNotIn('StatusPesanan', $nonaktif)
                ->exists();

            if ($aktifSebagaiPenjual || $aktifSebagaiPembeli) {
                return ['error', 'Seller masih memiliki pesanan aktif. Batalkan atau selesaikan pesanan tersebut terlebih dahulu.'];
            }

            // Produk: yang punya riwayat pesanan disembunyikan, sisanya dihapus
            foreach ($seller->produk()->get() as $p) {
                if ($p->pesananDetail()->exists()) {
                    $p->Status = 'Disembunyikan';
                    $p->save();
                } else {
                    if ($p->Gambar && !str_starts_with($p->Gambar, 'http')) {
                        $fileHapus[] = $p->Gambar;
                    }
                    $p->delete();
                }
            }

            $nama = $seller->Nama;
            $seller->delete(); // pengajuan_seller & keranjang ikut terhapus (cascade); riwayat pesanan tetap

            return ['success', "Akun seller \"{$nama}\" berhasil dihapus."];
        });

        // Hapus file foto setelah transaksi berhasil
        foreach ($fileHapus as $f) {
            foreach ([public_path('upload/' . $f), storage_path('app/public/upload/' . $f)] as $path) {
                if ($f !== 'default.jpg' && File::exists($path)) {
                    @File::delete($path);
                }
            }
        }

        return back()->with($hasil[0], $hasil[1]);
    }
}