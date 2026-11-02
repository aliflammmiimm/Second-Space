<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produk_gambar')) {
            Schema::create('produk_gambar', function (Blueprint $table) {
                $table->integer('GambarID')->autoIncrement();
                $table->integer('ProdukID');
                $table->string('PathGambar', 255);
                $table->integer('Urutan')->default(0);
                $table->timestamps();

                $table->foreign('ProdukID', 'fk_produk_gambar')
                    ->references('ProdukID')->on('produk')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_gambar');
    }
};

