<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `pesanan` MODIFY `StatusPesanan` VARCHAR(50) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `pesanan` MODIFY `StatusPesanan` ENUM('Menunggu Pembayaran', 'Diproses', 'Dikemas', 'Dikirim', 'Sampai Tujuan', 'Selesai', 'Dibatalkan', 'Kedaluwarsa') NULL");
    }
};

