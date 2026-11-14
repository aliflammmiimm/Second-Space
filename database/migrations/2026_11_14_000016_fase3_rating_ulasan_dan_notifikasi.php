<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Ulasan & Rating Produk
        if (!Schema::hasTable('ulasan_produk')) {
            Schema::create('ulasan_produk', function (Blueprint $t) {
                $t->integer('UlasanID')->autoIncrement();
                $t->integer('ProdukID')->index();
                $t->integer('UserID')->index();
                $t->integer('PesananID')->index();
                $t->tinyInteger('Bintang')->default(5);
                $t->text('Komentar')->nullable();
                $t->string('Foto', 255)->nullable();
                $t->text('DibalasOlehSeller')->nullable();
                $t->dateTime('DibalasPada')->nullable();
                $t->dateTime('DibuatPada')->useCurrent();
                $t->unique(['PesananID', 'ProdukID']);
            });
        }

        // 2. Tabel Pusat Notifikasi Pengguna (Pembeli & Seller)
        if (!Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $t) {
                $t->integer('NotifikasiID')->autoIncrement();
                $t->integer('UserID')->index();
                $t->string('Tipe', 40)->default('info');
                $t->string('Judul', 120);
                $t->text('Pesan');
                $t->string('Icon', 50)->default('bi-bell-fill');
                $t->string('IconColor', 30)->default('#00B8BE');
                $t->string('Url', 255)->nullable();
                $t->dateTime('DibacaPada')->nullable();
                $t->dateTime('DibuatPada')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('ulasan_produk');
    }
};

