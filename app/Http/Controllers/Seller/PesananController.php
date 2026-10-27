<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
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
        $item = PesananDetail::where('SellerID', Auth::id())
            ->with(['produk', 'pesanan.user:UserID,Nama', 'pesanan.pengiriman'])
            ->latest('DetailID')
            ->paginate(15);

        return view('seller.pesanan.index', compact('item'));
    }

    /**
     * Seller menandai pesanan sebagai dikirim + mengisi resi.
     * Hanya untuk pesanan yang SELURUH itemnya milik seller ini.
     * Pesanan campuran beberapa seller tetap diproses admin.
     */
    public function kirim(Request $request, $id)
    {
        $request->validate([
            'NoResi' => 'required|string|max:100',
        ], [
            'NoResi.required' => 'Nomor resi wajib diisi.',
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

            $pesanan->StatusPesanan = 'Dikirim';
            $pesanan->save();

            if ($pesanan->pengiriman) {
                $pesanan->pengiriman->StatusPengiriman = 'Dikirim';
                $pesanan->pengiriman->NoResi = trim($request->input('NoResi'));
                $pesanan->pengiriman->TanggalUpdate = now();
                $pesanan->pengiriman->save();
            }

            return ['success', "Pesanan #{$pesanan->PesananID} ditandai dikirim."];
        });

        return back()->with($pesan[0], $pesan[1]);
    }
}