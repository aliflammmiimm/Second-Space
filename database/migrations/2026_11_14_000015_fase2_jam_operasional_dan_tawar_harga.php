<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kolom Jam Operasional & Status Buka/Tutup Toko Seller pada tabel 'user'
        Schema::table('user', function (Blueprint $t) {
            if (!Schema::hasColumn('user', 'JamBuka')) {
                $t->string('JamBuka', 5)->default('08:00')->nullable();
            }
            if (!Schema::hasColumn('user', 'JamTutup')) {
                $t->string('JamTutup', 5)->default('21:00')->nullable();
            }
            if (!Schema::hasColumn('user', 'HariOperasional')) {
                $t->string('HariOperasional', 150)->default('Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu')->nullable();
            }
            if (!Schema::hasColumn('user', 'is_toko_buka')) {
                $t->boolean('is_toko_buka')->default(true);
            }
            if (!Schema::hasColumn('user', 'CatatanLibur')) {
                $t->string('CatatanLibur', 255)->nullable();
            }
        });

        // 2. Tabel Tawaran Harga Interaktif di Room Chat
        if (!Schema::hasTable('tawaran_harga')) {
            Schema::create('tawaran_harga', function (Blueprint $t) {
                $t->integer('TawaranID')->autoIncrement();
                $t->integer('PercakapanID')->index();
                $t->integer('PesanID')->nullable()->index();
                $t->integer('PembeliID')->index();
                $t->integer('SellerID')->index();
                $t->integer('ProdukID')->index();
                $t->integer('HargaAsli');
                $t->integer('HargaTawaran');
                $t->string('Status', 20)->default('Menunggu'); // Menunggu, Diterima, Ditolak, Dibatalkan, Selesai
                $t->string('Catatan', 255)->nullable();
                $t->dateTime('DibuatPada')->useCurrent();
                $t->dateTime('DiresponPada')->nullable();
            });
        }

        // 3. Tambahan kolom TipePesan dan TawaranID pada pesan_chat
        Schema::table('pesan_chat', function (Blueprint $t) {
            if (!Schema::hasColumn('pesan_chat', 'TipePesan')) {
                $t->string('TipePesan', 20)->default('teks'); // teks, tawaran
            }
            if (!Schema::hasColumn('pesan_chat', 'TawaranID')) {
                $t->integer('TawaranID')->nullable()->index();
            }
        });

        // 4. Catat TawaranID pada item pesanan jika dibeli via kesepakatan harga tawaran
        if (Schema::hasTable('pesanandetail')) {
            Schema::table('pesanandetail', function (Blueprint $t) {
                if (!Schema::hasColumn('pesanandetail', 'TawaranID')) {
                    $t->integer('TawaranID')->nullable()->index();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pesanandetail')) {
            Schema::table('pesanandetail', function (Blueprint $t) {
                if (Schema::hasColumn('pesanandetail', 'TawaranID')) {
                    $t->dropColumn('TawaranID');
                }
            });
        }

        Schema::table('pesan_chat', function (Blueprint $t) {
            if (Schema::hasColumn('pesan_chat', 'TawaranID')) {
                $t->dropColumn('TawaranID');
            }
            if (Schema::hasColumn('pesan_chat', 'TipePesan')) {
                $t->dropColumn('TipePesan');
            }
        });

        Schema::dropIfExists('tawaran_harga');

        Schema::table('user', function (Blueprint $t) {
            $cols = ['JamBuka', 'JamTutup', 'HariOperasional', 'is_toko_buka', 'CatatanLibur'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('user', $c)) {
                    $t->dropColumn($c);
                }
            }
        });
    }
};
