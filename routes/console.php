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
