<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran', 'SnapToken')) {
                $table->string('SnapToken', 255)->nullable()->after('StatusPembayaran');
            }
            if (!Schema::hasColumn('pembayaran', 'TransactionID')) {
                $table->string('TransactionID', 100)->nullable()->after('SnapToken');
            }
            if (!Schema::hasColumn('pembayaran', 'PaymentType')) {
                $table->string('PaymentType', 50)->nullable()->after('TransactionID');
            }
            if (!Schema::hasColumn('pembayaran', 'PaymentDetails')) {
                $table->text('PaymentDetails')->nullable()->after('PaymentType');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['SnapToken', 'TransactionID', 'PaymentType', 'PaymentDetails']);
        });
    }
};