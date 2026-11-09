<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengiriman') && !Schema::hasColumn('pengiriman', 'NoResi')) {
            Schema::table('pengiriman', function (Blueprint $table) {
                $table->string('NoResi', 100)->nullable()->after('AlamatPengiriman');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengiriman') && Schema::hasColumn('pengiriman', 'NoResi')) {
            Schema::table('pengiriman', function (Blueprint $table) {
                $table->dropColumn('NoResi');
            });
        }
    }
};

