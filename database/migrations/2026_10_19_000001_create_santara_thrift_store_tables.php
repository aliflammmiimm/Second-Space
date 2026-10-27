<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel User
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->integer('UserID')->autoIncrement();
                $table->string('Nama', 100);
                $table->string('NoHP_Email', 100);
                $table->string('Password', 255);
                $table->enum('Role', ['Pelanggan', 'Admin', 'Pemilik'])->default('Pelanggan');
            });
        }

        // 2. Tabel Jenis Produk
        if (!Schema::hasTable('jenis_produk')) {
            Schema::create('jenis_produk', function (Blueprint $table) {
                $table->integer('JenisID')->autoIncrement();
                $table->string('NamaJenis', 100);
            });
        }

        // 3. Tabel Produk
        if (!Schema::hasTable('produk')) {
            Schema::create('produk', function (Blueprint $table) {
                $table->integer('ProdukID')->autoIncrement();
                $table->string('NamaProduk', 150);
                $table->string('Jenis', 50)->nullable();
                $table->string('Ukuran', 10)->nullable();
                $table->enum('Grade', ['A', 'B', 'C'])->nullable();
                $table->string('Brand', 100)->nullable();
                $table->integer('Harga')->nullable();
                $table->integer('Stok')->nullable();
                $table->enum('Status', ['Tersedia', 'Terjual'])->default('Tersedia');
                $table->string('Gambar', 255)->nullable();
                $table->integer('JenisID')->nullable();

                $table->foreign('JenisID', 'fk_jenis_produk')
                    ->references('JenisID')->on('jenis_produk')
                    ->onDelete('set null');
            });
        }

        // 4. Tabel Pesanan
        if (!Schema::hasTable('pesanan')) {
            Schema::create('pesanan', function (Blueprint $table) {
                $table->integer('PesananID')->autoIncrement();
                $table->integer('UserID')->nullable();
                $table->dateTime('TanggalPesanan')->useCurrent();
                $table->enum('StatusPesanan', ['Diproses', 'Dikemas', 'Dikirim', 'Sampai Tujuan', 'Selesai'])->nullable();
                $table->enum('MetodePembayaran', ['Transfer', 'COD'])->nullable();
                $table->integer('TotalHarga')->nullable();

                $table->foreign('UserID', 'fk_pesanan_user')
                    ->references('UserID')->on('user')
                    ->onDelete('set null');
            });
        }

        // 5. Tabel Pesanan Detail
        if (!Schema::hasTable('pesanandetail')) {
            Schema::create('pesanandetail', function (Blueprint $table) {
                $table->integer('DetailID')->autoIncrement();
                $table->integer('PesananID')->nullable();
                $table->integer('ProdukID')->nullable();
                $table->integer('Jumlah')->nullable();
                $table->integer('HargaSatuan')->nullable();
                $table->integer('SubTotal')->nullable();

                $table->foreign('PesananID', 'fk_pesanandetail_pesanan')
                    ->references('PesananID')->on('pesanan')
                    ->onDelete('cascade');

                $table->foreign('ProdukID', 'fk_pesanandetail_produk')
                    ->references('ProdukID')->on('produk')
                    ->onDelete('set null');
            });
        }

        // 6. Tabel Pembayaran
        if (!Schema::hasTable('pembayaran')) {
            Schema::create('pembayaran', function (Blueprint $table) {
                $table->integer('PembayaranID')->autoIncrement();
                $table->integer('PesananID')->nullable();
                $table->enum('Metode', ['Transfer', 'COD'])->nullable();
                $table->string('StatusPembayaran', 50)->nullable();

                $table->foreign('PesananID', 'fk_pembayaran_pesanan')
                    ->references('PesananID')->on('pesanan')
                    ->onDelete('cascade');
            });
        }

        // 7. Tabel Pengiriman
        if (!Schema::hasTable('pengiriman')) {
            Schema::create('pengiriman', function (Blueprint $table) {
                $table->integer('PengirimanID')->autoIncrement();
                $table->integer('PesananID')->nullable();
                $table->enum('StatusPengiriman', ['Dikemas', 'Dikirim', 'Sampai Tujuan'])->nullable();
                $table->dateTime('TanggalUpdate')->useCurrent();
                $table->string('AlamatPengiriman', 255)->nullable();
                $table->string('NoResi', 100)->nullable();

                $table->foreign('PesananID', 'fk_pengiriman_pesanan')
                    ->references('PesananID')->on('pesanan')
                    ->onDelete('cascade');
            });
        }

        // 8. Tabel Laporan Keuangan
        if (!Schema::hasTable('laporankeuangan')) {
            Schema::create('laporankeuangan', function (Blueprint $table) {
                $table->integer('LaporanID')->autoIncrement();
                $table->enum('Periode', ['Harian', 'Mingguan', 'Bulanan'])->nullable();
                $table->integer('TotalPemasukan')->nullable();
                $table->integer('TotalPengeluaran')->nullable();
                $table->integer('Keuntungan')->nullable();
                $table->string('ProdukTerlaris', 150)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pesanandetail');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('jenis_produk');
        Schema::dropIfExists('user');
        Schema::dropIfExists('laporankeuangan');
    }
};