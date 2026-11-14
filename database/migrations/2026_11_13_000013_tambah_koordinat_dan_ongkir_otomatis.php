<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $t) {
            if (!Schema::hasColumn('user', 'Latitude')) {
                $t->decimal('Latitude', 10, 8)->nullable()->after('AlamatToko');
            }
            if (!Schema::hasColumn('user', 'Longitude')) {
                $t->decimal('Longitude', 11, 8)->nullable()->after('Latitude');
            }
        });

        Schema::table('pengiriman', function (Blueprint $t) {
            if (!Schema::hasColumn('pengiriman', 'Latitude')) {
                $t->decimal('Latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('pengiriman', 'Longitude')) {
                $t->decimal('Longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('pengiriman', 'JarakKM')) {
                $t->decimal('JarakKM', 6, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengiriman', function (Blueprint $t) {
            if (Schema::hasColumn('pengiriman', 'JarakKM')) $t->dropColumn('JarakKM');
            if (Schema::hasColumn('pengiriman', 'Longitude')) $t->dropColumn('Longitude');
            if (Schema::hasColumn('pengiriman', 'Latitude')) $t->dropColumn('Latitude');
        });

        Schema::table('user', function (Blueprint $t) {
            if (Schema::hasColumn('user', 'Longitude')) $t->dropColumn('Longitude');
            if (Schema::hasColumn('user', 'Latitude')) $t->dropColumn('Latitude');
        });
    }
};
