<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $t) {
            if (!Schema::hasColumn('user', 'Kota')) {
                $t->string('Kota', 60)->nullable();
            }
            if (!Schema::hasColumn('user', 'AlamatToko')) {
                $t->string('AlamatToko', 255)->nullable();
            }
            if (!Schema::hasColumn('user', 'melayani_ambil')) {
                $t->boolean('melayani_ambil')->default(true);
            }
            if (!Schema::hasColumn('user', 'melayani_antar')) {
                $t->boolean('melayani_antar')->default(false);
            }
            if (!Schema::hasColumn('user', 'tarif_antar')) {
                $t->integer('tarif_antar')->default(0);
            }
        });

        Schema::table('pesanan', function (Blueprint $t) {
            if (!Schema::hasColumn('pesanan', 'CaraTerima')) {
                $t->string('CaraTerima', 20)->default('Ambil Sendiri');
            }
            if (!Schema::hasColumn('pesanan', 'Ongkir')) {
                $t->integer('Ongkir')->default(0);
            }
        });

        Schema::table('pengiriman', function (Blueprint $t) {
            if (!Schema::hasColumn('pengiriman', 'NoHPPenerima')) {
                $t->string('NoHPPenerima', 30)->nullable();
            }
        });

        // Pesanan lama dianggap diantar karena sudah memiliki alamat pengiriman lengkap
        DB::table('pesanan')->update(['CaraTerima' => 'Diantar']);

        // Backfill data toko seller dari pengajuan seller yang sudah disetujui
        if (Schema::hasTable('pengajuan_seller')) {
            DB::table('pengajuan_seller')->where('Status', 'approved')->orderBy('PengajuanID')->each(function ($p) {
                $bagian = array_map('trim', explode(',', $p->Alamat));
                $update = [];
                
                // Isi AlamatToko jika belum ada
                $update['AlamatToko'] = $p->Alamat;

                // Coba ambil kota dari format "Alamat, Kota, Provinsi"
                if (count($bagian) >= 3) {
                    $update['Kota'] = $bagian[count($bagian) - 2];
                }

                DB::table('user')->where('UserID', $p->UserID)->whereNull('AlamatToko')->update($update);
            });
        }
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $t) {
            $t->dropColumn(['Kota', 'AlamatToko', 'melayani_ambil', 'melayani_antar', 'tarif_antar']);
        });

        Schema::table('pesanan', function (Blueprint $t) {
            $t->dropColumn(['CaraTerima', 'Ongkir']);
        });

        Schema::table('pengiriman', function (Blueprint $t) {
            $t->dropColumn('NoHPPenerima');
        });
    }
};

