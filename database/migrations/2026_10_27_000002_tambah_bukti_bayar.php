<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $t) {
            if (!Schema::hasColumn('pembayaran', 'BuktiBayar')) {
                $t->string('BuktiBayar', 255)->nullable();
            }
            if (!Schema::hasColumn('pembayaran', 'DiunggahPada')) {
                $t->dateTime('DiunggahPada')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $t) {
            $t->dropColumn(['BuktiBayar', 'DiunggahPada']);
        });
    }
};