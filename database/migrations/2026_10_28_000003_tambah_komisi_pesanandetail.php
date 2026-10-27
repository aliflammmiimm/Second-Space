<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanandetail', function (Blueprint $t) {
            if (!Schema::hasColumn('pesanandetail', 'Komisi')) {
                $t->integer('Komisi')->default(0);
            }
            if (!Schema::hasColumn('pesanandetail', 'PendapatanSeller')) {
                $t->integer('PendapatanSeller')->default(0);
            }
        });

        // Data lama: hitung ulang dengan komisi 10%
        $persen = (int) env('KOMISI_PERSEN', 10);
        DB::table('pesanandetail')->orderBy('DetailID')->each(function ($d) use ($persen) {
            $komisi = (int) round($d->SubTotal * $persen / 100);
            DB::table('pesanandetail')->where('DetailID', $d->DetailID)->update([
                'Komisi' => $komisi,
                'PendapatanSeller' => $d->SubTotal - $komisi,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('pesanandetail', function (Blueprint $t) {
            $t->dropColumn(['Komisi', 'PendapatanSeller']);
        });
    }
};