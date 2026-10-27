<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class ReservasiService
{
    /** Batalkan pesanan Transfer yang lewat batas bayar, lalu bebaskan produknya. */
    public static function lepasKedaluwarsa(): int
    {
        $ids = Pesanan::where('StatusPesanan', 'Menunggu Pembayaran')
            ->where('BatasBayar', '<', now())
            ->pluck('PesananID');

        foreach ($ids as $id) {
            DB::transaction(function () use ($id) {
                $p = Pesanan::where('PesananID', $id)->lockForUpdate()->first();
                if ($p && $p->StatusPesanan === 'Menunggu Pembayaran') {
                    self::batalkan($p, 'Kedaluwarsa');
                }
            });
        }

        return $ids->count();
    }

    /** Harus dipanggil di dalam DB::transaction dengan baris pesanan sudah di-lock. */
    public static function batalkan(Pesanan $pesanan, string $status = 'Dibatalkan'): void
    {
        Produk::whereIn('ProdukID', $pesanan->detail()->pluck('ProdukID'))
            ->whereIn('Status', ['Dipesan', 'Terjual'])
            ->update(['Status' => 'Tersedia']);

        $pesanan->StatusPesanan = $status;
        $pesanan->BatasBayar = null;
        $pesanan->save();

        Pembayaran::where('PesananID', $pesanan->PesananID)
            ->update(['StatusPembayaran' => 'Tidak Valid']);
    }
}