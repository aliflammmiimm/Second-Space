<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\ReservasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['user', 'pembayaran', 'pengiriman']);

        if ($request->filled('status')) {
            $query->where('StatusPesanan', $request->input('status'));
        }

        $pesanan = $query->latest('PesananID')->paginate(10)->withQueryString();

        return view('admin.pesanan.index', compact('pesanan'));
    }

    public function detail($id)
    {
        $pesanan = Pesanan::with(['user', 'detail.produk', 'detail.seller:UserID,Nama', 'pembayaran', 'pengiriman'])
            ->findOrFail($id);

        return view('admin.pesanan.detail', compact('pesanan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'StatusPesanan' => 'required|in:Diproses,Dikemas,Dikirim,Sampai Tujuan,Selesai',
            'NoResi' => 'nullable|string|max:100',
        ]);

        $newStatus = $request->input('StatusPesanan');

        $pesan = DB::transaction(function () use ($request, $id, $newStatus) {
            $pesanan = Pesanan::with('pengiriman', 'pembayaran')->where('PesananID', $id)->lockForUpdate()->firstOrFail();

            if (in_array($pesanan->StatusPesanan, ['Menunggu Pembayaran', 'Dibatalkan', 'Kedaluwarsa'], true)) {
                return ['error', 'Status tidak dapat diubah: pesanan belum dibayar / sudah dibatalkan.'];
            }

            $pesanan->StatusPesanan = $newStatus;
            $pesanan->save();

            if ($pesanan->pengiriman) {
                if (in_array($newStatus, ['Dikemas', 'Dikirim'], true)) {
                    $pesanan->pengiriman->StatusPengiriman = $newStatus;
                } elseif (in_array($newStatus, ['Sampai Tujuan', 'Selesai'], true)) {
                    $pesanan->pengiriman->StatusPengiriman = 'Sampai Tujuan';
                }
                if ($request->filled('NoResi')) {
                    $pesanan->pengiriman->NoResi = trim($request->input('NoResi'));
                }
                $pesanan->pengiriman->TanggalUpdate = now();
                $pesanan->pengiriman->save();
            }

            // Pesanan selesai (mis. COD): pembayaran dianggap valid
            if ($newStatus === 'Selesai' && $pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran !== 'Valid') {
                $pesanan->pembayaran->StatusPembayaran = 'Valid';
                $pesanan->pembayaran->save();
            }

            return ['success', "Status pesanan #{$pesanan->PesananID} berhasil diubah menjadi {$newStatus}."];
        });

        return back()->with($pesan[0], $pesan[1]);
    }

    /** Riwayat transaksi tidak dihapus; pesanan hanya dibatalkan dan barang dibuka kembali. */
    public function batalkan($id)
    {
        $hasil = DB::transaction(function () use ($id) {
            $pesanan = Pesanan::where('PesananID', $id)->lockForUpdate()->firstOrFail();

            if (!in_array($pesanan->StatusPesanan, ['Menunggu Pembayaran', 'Diproses', 'Dikemas'], true)) {
                return false;
            }

            ReservasiService::batalkan($pesanan, 'Dibatalkan');
            return true;
        });

        return back()->with(
            $hasil ? 'success' : 'error',
            $hasil ? "Pesanan #{$id} dibatalkan dan barang dibuka kembali." : 'Pesanan ini tidak dapat dibatalkan pada status sekarang.'
        );
    }
}