<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gambar_storage')) {
            Schema::create('gambar_storage', function (Blueprint $table) {
                $table->string('nama_file', 255)->primary();
                $table->string('mime', 100);
                $table->unsignedInteger('ukuran')->default(0);
                $table->timestamps();
            });

            // Tambahkan kolom LONGBLOB secara manual via raw SQL agar kompatibel di MySQL/TiDB
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `gambar_storage` ADD `data` LONGBLOB NOT NULL AFTER `ukuran`");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gambar_storage');
    }
};
