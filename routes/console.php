<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan pesanan Transfer yang lewat batas bayar dan buka kembali barangnya
Artisan::command('pesanan:lepas-kedaluwarsa', function () {
    $n = \App\Services\ReservasiService::lepasKedaluwarsa();
    $this->info("{$n} pesanan kedaluwarsa dilepas.");
})->purpose('Lepas pesanan yang melewati batas pembayaran');

\Illuminate\Support\Facades\Schedule::command('pesanan:lepas-kedaluwarsa')->everyFiveMinutes();

// Cek tagihan komisi COD yang melewati batas 4 hari dan blokir seller secara otomatis
Artisan::command('komisi:cek-tunggakan', function () {
    $n = \App\Services\KomisiService::cekTunggakanDanBlokir();
    $this->info("{$n} seller berhasil diblokir karena tunggakan komisi COD.");
})->purpose('Cek tunggakan komisi COD lewat 4 hari dan blokir toko seller otomatis');

\Illuminate\Support\Facades\Schedule::command('komisi:cek-tunggakan')->hourly();
