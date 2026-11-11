<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('percakapan')) {
            Schema::create('percakapan', function (Blueprint $t) {
                $t->integer('PercakapanID')->autoIncrement();
                $t->integer('PembeliID');
                $t->integer('SellerID');
                $t->dateTime('TerakhirPada')->nullable();
                $t->unique(['PembeliID', 'SellerID']);
                $t->index('SellerID');
            });
        }

        if (!Schema::hasTable('pesan_chat')) {
            Schema::create('pesan_chat', function (Blueprint $t) {
                $t->integer('PesanID')->autoIncrement();
                $t->integer('PercakapanID')->index();
                $t->integer('PengirimID');
                $t->integer('ProdukID')->nullable();
                $t->text('Isi');
                $t->dateTime('DibacaPada')->nullable();
                $t->dateTime('DibuatPada')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_chat');
        Schema::dropIfExists('percakapan');
    }
};

