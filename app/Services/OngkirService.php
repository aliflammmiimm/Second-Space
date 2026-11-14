<?php

namespace App\Services;

class OngkirService
{
    /** Titik acuan default pusat kota Makassar */
    public const DEFAULT_LAT = -5.147665;
    public const DEFAULT_LON = 119.432731;

    /** Batas radius pengantaran maksimal (KM) di wilayah Makassar dan sekitarnya */
    public const MAKSIMAL_JARAK_KM = 35.0;

    /** Tarif dasar pengantaran (0 - 3 KM pertama) */
    public const TARIF_DASAR = 5000;
    public const KM_DASAR = 3.0;

    /** Tarif per KM tambahan setelah KM_DASAR */
    public const TARIF_PER_KM = 2500;

    /**
     * Hitung estimasi jarak rute jalan (KM) menggunakan Haversine dengan faktor kelokan rute perkotaan (1.25x).
     */
    public static function hitungJarakKM(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371.0; // KM

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $jarakLurus = $earthRadius * $c;

        // Faktor rute jalan perkotaan (1.25x jarak garis lurus)
        $jarakRute = $jarakLurus * 1.25;

        return max(0.5, round($jarakRute, 1));
    }

    /**
     * Hitung nominal ongkos kirim berdasarkan jarak (KM).
     */
    public static function hitungOngkir(float $jarakKM): int
    {
        if ($jarakKM <= 0) {
            return 0;
        }

        if ($jarakKM <= self::KM_DASAR) {
            return self::TARIF_DASAR;
        }

        $kmTambahan = $jarakKM - self::KM_DASAR;
        $ongkirKotor = self::TARIF_DASAR + ($kmTambahan * self::TARIF_PER_KM);

        // Pembulatan rapi ke kelipatan Rp 500 terdekat
        return (int) (ceil($ongkirKotor / 500) * 500);
    }

    /**
     * Hitung ringkasan jarak dan ongkir dari dua pasang koordinat.
     */
    public static function kalkulasi(?float $latSeller, ?float $lonSeller, ?float $latBuyer, ?float $lonBuyer): array
    {
        $lat1 = $latSeller ?? self::DEFAULT_LAT;
        $lon1 = $lonSeller ?? self::DEFAULT_LON;

        $lat2 = $latBuyer ?? self::DEFAULT_LAT;
        $lon2 = $lonBuyer ?? self::DEFAULT_LON;

        $jarak = self::hitungJarakKM($lat1, $lon1, $lat2, $lon2);
        $terlaluJauh = $jarak > self::MAKSIMAL_JARAK_KM;
        $ongkir = self::hitungOngkir($jarak);

        return [
            'jarak_km' => $jarak,
            'ongkir' => $ongkir,
            'ongkir_format' => 'Rp ' . number_format($ongkir, 0, ',', '.'),
            'terlalu_jauh' => $terlaluJauh,
            'pesan_jarak' => "Jarak pengantaran: {$jarak} KM",
        ];
    }
}
