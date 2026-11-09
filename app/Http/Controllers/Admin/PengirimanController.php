<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengiriman::with(['pesanan.user', 'pesanan.detail.seller:UserID,Nama']);

        if ($request->filled('status')) {
            $query->where('StatusPengiriman', $request->input('status'));
        }

        $pengiriman = $query->latest('PengirimanID')->paginate(10)->withQueryString();

        return view('admin.pengiriman.index', compact('pengiriman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'StatusPengiriman' => 'required|in:Dikemas,Dikirim,Sampai Tujuan',
            'NoResi' => 'nullable|string|max:100',
        ]);

        $status = $request->input('StatusPengiriman');

        DB::transaction(function () use ($request, $id, $status) {
            $pengiriman = Pengiriman::with(['pesanan.pembayaran'])->findOrFail($id);

            $pengiriman->StatusPengiriman = $status;
            if ($request->filled('NoResi')) {
                $pengiriman->NoResi = trim($request->input('NoResi'));
            }
            $pengiriman->TanggalUpdate = now();
            $pengiriman->save();

            $pesanan = $pengiriman->pesanan;
            if ($pesanan && !in_array($pesanan->StatusPesanan, ['Dibatalkan', 'Kedaluwarsa'], true)) {
                // Sinkronkan status pesanan dengan status pengiriman
                if ($status === 'Dikirim' && in_array($pesanan->StatusPesanan, ['Diproses', 'Dikemas'], true)) {
                    $pesanan->StatusPesanan = 'Dikirim';
                    $pesanan->save();
                } elseif ($status === 'Sampai Tujuan') {
                    $pesanan->StatusPesanan = 'Sampai Tujuan';
                    $pesanan->save();

                    // Pada pesanan COD, jika barang sampai tujuan/selesai, pembayaran dianggap valid
                    if ($pesanan->MetodePembayaran === 'COD' && $pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran !== 'Valid') {
                        $pesanan->pembayaran->StatusPembayaran = 'Valid';
                        $pesanan->pembayaran->save();
                    }
                }
            }
        });

        return back()->with('success', "Status pengiriman #{$id} dan pesanan terkait berhasil diperbarui.");
    }
}
