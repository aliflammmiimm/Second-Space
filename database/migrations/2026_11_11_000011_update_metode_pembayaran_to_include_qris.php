<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `pesanan` MODIFY `MetodePembayaran` VARCHAR(50) NULL DEFAULT 'QRIS'");
        DB::statement("ALTER TABLE `pembayaran` MODIFY `Metode` VARCHAR(50) NULL DEFAULT 'QRIS'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `pesanan` MODIFY `MetodePembayaran` ENUM('Transfer', 'COD') NULL");
        DB::statement("ALTER TABLE `pembayaran` MODIFY `Metode` ENUM('Transfer', 'COD') NULL");
    }
};
