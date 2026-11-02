<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\TagihanKomisiCod;

class KomisiService
{
    /**
     * Daftarkan tagihan komisi COD 10% dengan batas 4 hari setelah pesanan COD selesai.
     */
    public static function buatTagihanCodJikaPerlu(Pesanan $pesanan): ?TagihanKomisiCod
    {
        if ($pesanan->MetodePembayaran !== 'COD') {
            return null;
        }

        if ($pesanan->StatusPesanan !== 'Selesai') {
            return null;
        }

        // Cek apakah sudah pernah dibuat tagihannya
        $ada = TagihanKomisiCod::where('PesananID', $pesanan->PesananID)->first();
        if ($ada) {
            return $ada;
        }

        $sellerId = $pesanan->detail()->first()?->SellerID;
        $komisiTotal = (int) $pesanan->detail()->sum('Komisi');

        if (!$sellerId || $komisiTotal <= 0) {
            return null;
        }

        return TagihanKomisiCod::create([
            'PesananID' => $pesanan->PesananID,
            'SellerID' => $sellerId,
            'JumlahKomisi' => $komisiTotal,
            'BatasBayar' => now()->addDays(4),
            'Status' => 'Belum Dibayar',
        ]);
    }

    /**
     * Memeriksa tunggakan komisi yang melewati batas 4 hari dan memblokir seller terkait secara otomatis.
     */
    public static function cekTunggakanDanBlokir(): int
    {
        $overdues = TagihanKomisiCod::with('seller')
            ->where('Status', 'Belum Dibayar')
            ->where('BatasBayar', '<', now())
            ->get();

        $count = 0;
        foreach ($overdues as $tagihan) {
            $seller = $tagihan->seller;
            if ($seller && empty($seller->is_blocked)) {
                $seller->is_blocked = 1;
                $seller->AlasanBlokir = "Tunggakan komisi COD pesanan #{$tagihan->PesananID} sebesar Rp " . number_format($tagihan->JumlahKomisi, 0, ',', '.') . " melewati batas waktu 4 hari.";
                $seller->DiblokirPada = now();
                $seller->save();
                $count++;
            }
        }

        return $count;
    }
}

