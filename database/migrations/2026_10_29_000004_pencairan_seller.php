<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rekening tujuan pencairan milik seller
        Schema::table('user', function (Blueprint $t) {
            if (!Schema::hasColumn('user', 'Bank')) {
                $t->string('Bank', 50)->nullable();
            }
            if (!Schema::hasColumn('user', 'NoRekening')) {
                $t->string('NoRekening', 40)->nullable();
            }
            if (!Schema::hasColumn('user', 'AtasNama')) {
                $t->string('AtasNama', 100)->nullable();
            }
        });

        if (!Schema::hasTable('pencairan')) {
            Schema::create('pencairan', function (Blueprint $t) {
                $t->integer('PencairanID')->autoIncrement();
                $t->integer('SellerID')->nullable();
                $t->integer('Jumlah');
                // Snapshot rekening saat diajukan (tidak berubah walau seller ganti rekening)
                $t->string('Bank', 50);
                $t->string('NoRekening', 40);
                $t->string('AtasNama', 100);
                $t->enum('Status', ['Menunggu', 'Selesai', 'Ditolak'])->default('Menunggu');
                $t->string('Catatan', 255)->nullable();
                $t->integer('DiprosesOleh')->nullable();
                $t->dateTime('DiprosesPada')->nullable();
                $t->timestamps();

                $t->foreign('SellerID', 'fk_pencairan_seller')
                    ->references('UserID')->on('user')->onDelete('set null');
                $t->index(['SellerID', 'Status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairan');
        Schema::table('user', function (Blueprint $t) {
            $t->dropColumn(['Bank', 'NoRekening', 'AtasNama']);
        });
    }
};