<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\ReservasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with(['pesanan.user']);

        if ($request->filled('status')) {
            $query->where('StatusPembayaran', $request->input('status'));
        }

        $pembayaran = $query->latest('PembayaranID')->paginate(10)->withQueryString();

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'StatusPembayaran' => 'required|in:Valid,Tidak Valid,Menunggu',
        ]);

        $status = $request->input('StatusPembayaran');

        $pesan = DB::transaction(function () use ($pembayaran, $status) {
            $pesanan = Pesanan::where('PesananID', $pembayaran->PesananID)->lockForUpdate()->first();

            if ($pesanan && in_array($pesanan->StatusPesanan, ['Dibatalkan', 'Kedaluwarsa'], true)) {
                return ['error', 'Pesanan sudah dibatalkan/kedaluwarsa, status pembayaran tidak dapat diubah.'];
            }

            if ($pesanan && $pesanan->StatusPesanan === 'Menunggu Pembayaran') {
                if ($status === 'Valid') {
                    $pesanan->StatusPesanan = 'Diproses';
                    $pesanan->BatasBayar = null;
                    $pesanan->save();

                    Produk::whereIn('ProdukID', $pesanan->detail()->pluck('ProdukID'))
                        ->where('Status', 'Dipesan')
                        ->update(['Status' => 'Terjual']);
                } elseif ($status === 'Tidak Valid') {
                    ReservasiService::batalkan($pesanan, 'Dibatalkan');
                    return ['success', 'Pembayaran tidak valid: pesanan dibatalkan dan barang dibuka kembali.'];
                }
            }

            $pembayaran->StatusPembayaran = $status;
            $pembayaran->save();

            return ['success', "Status pembayaran #{$pembayaran->PembayaranID} diubah menjadi {$status}."];
        });

        return back()->with($pesan[0], $pesan[1]);
    }
}