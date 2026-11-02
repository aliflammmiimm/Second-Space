<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kolom status pemblokiran pada tabel user
        Schema::table('user', function (Blueprint $table) {
            if (!Schema::hasColumn('user', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('is_seller');
            }
            if (!Schema::hasColumn('user', 'AlasanBlokir')) {
                $table->string('AlasanBlokir', 255)->nullable()->after('is_blocked');
            }
            if (!Schema::hasColumn('user', 'DiblokirPada')) {
                $table->dateTime('DiblokirPada')->nullable()->after('AlasanBlokir');
            }
        });

        // 2. Kode PIN Serah-Terima COD pada pesanan
        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan', 'KodePinCod')) {
                $table->string('KodePinCod', 10)->nullable()->after('MetodePembayaran');
            }
        });

        // 3. Tabel Tagihan Komisi COD (10% dalam 4 hari)
        if (!Schema::hasTable('tagihan_komisi_cod')) {
            Schema::create('tagihan_komisi_cod', function (Blueprint $table) {
                $table->integer('TagihanID')->autoIncrement();
                $table->integer('PesananID');
                $table->integer('SellerID');
                $table->integer('JumlahKomisi');
                $table->dateTime('BatasBayar');
                $table->enum('Status', ['Belum Dibayar', 'Menunggu Verifikasi', 'Lunas', 'Dibatalkan'])->default('Belum Dibayar');
                $table->string('BuktiTransfer', 255)->nullable();
                $table->dateTime('DiunggahPada')->nullable();
                $table->string('CatatanAdmin', 255)->nullable();
                $table->integer('DiverifikasiOleh')->nullable();
                $table->dateTime('DiverifikasiPada')->nullable();
                $table->timestamps();

                $table->foreign('PesananID', 'fk_tagihan_pesanan')
                    ->references('PesananID')->on('pesanan')
                    ->onDelete('cascade');

                $table->foreign('SellerID', 'fk_tagihan_seller')
                    ->references('UserID')->on('user')
                    ->onDelete('cascade');

                $table->index(['SellerID', 'Status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_komisi_cod');

        Schema::table('pesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pesanan', 'KodePinCod')) {
                $table->dropColumn('KodePinCod');
            }
        });

        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'is_blocked')) {
                $table->dropColumn(['is_blocked', 'AlasanBlokir', 'DiblokirPada']);
            }
        });
    }
};

