<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. User: penanda Seller (hanya diubah Admin lewat approval)
        if (!Schema::hasColumn('user', 'is_seller')) {
            Schema::table('user', function (Blueprint $t) {
                $t->boolean('is_seller')->default(false);
            });
        }

        // 2. Pengajuan Seller
        if (!Schema::hasTable('pengajuan_seller')) {
            Schema::create('pengajuan_seller', function (Blueprint $t) {
                $t->integer('PengajuanID')->autoIncrement();
                $t->integer('UserID');
                $t->string('Nama', 100);
                $t->string('NoHP', 30);
                $t->string('Alamat', 255);
                $t->text('Alasan');
                $t->text('InfoTambahan')->nullable();
                $t->enum('Status', ['pending', 'approved', 'rejected'])->default('pending');
                $t->text('AlasanPenolakan')->nullable();
                $t->integer('ReviewedBy')->nullable();
                $t->timestamps();

                $t->foreign('UserID', 'fk_pengajuan_user')->references('UserID')->on('user')->onDelete('cascade');
                $t->index(['UserID', 'Status']);
            });
        }

        // 3. Kategori: 4 kategori utama, kategori lama dinonaktifkan (TIDAK dihapus)
        if (!Schema::hasColumn('jenis_produk', 'Aktif')) {
            Schema::table('jenis_produk', function (Blueprint $t) {
                $t->boolean('Aktif')->default(true);
            });
        }
        $empat = ['Elektronik', 'Fashion', 'Peralatan Rumah Tangga', 'ATK'];
        foreach ($empat as $nama) {
            $ada = DB::table('jenis_produk')->where('NamaJenis', $nama)->first();
            if ($ada) {
                DB::table('jenis_produk')->where('JenisID', $ada->JenisID)->update(['Aktif' => 1]);
            } else {
                DB::table('jenis_produk')->insert(['NamaJenis' => $nama, 'Aktif' => 1]);
            }
        }
        DB::table('jenis_produk')->whereNotIn('NamaJenis', $empat)->update(['Aktif' => 0]);

        // 4. Produk
        Schema::table('produk', function (Blueprint $t) {
            if (!Schema::hasColumn('produk', 'SellerID')) {
                $t->integer('SellerID')->nullable();
                $t->foreign('SellerID', 'fk_produk_seller')->references('UserID')->on('user')->onDelete('set null');
            }
            if (!Schema::hasColumn('produk', 'Deskripsi')) {
                $t->text('Deskripsi')->nullable();
            }
            if (!Schema::hasColumn('produk', 'Kondisi')) {
                $t->string('Kondisi', 20)->default('baik');
            }
        });

        // 5. Pesanan & detail
        if (!Schema::hasColumn('pesanan', 'BatasBayar')) {
            Schema::table('pesanan', function (Blueprint $t) {
                $t->dateTime('BatasBayar')->nullable();
            });
        }
        if (!Schema::hasColumn('pesanandetail', 'SellerID')) {
            Schema::table('pesanandetail', function (Blueprint $t) {
                $t->integer('SellerID')->nullable();
                $t->foreign('SellerID', 'fk_detail_seller')->references('UserID')->on('user')->onDelete('set null');
            });
        }

        // 6. Keranjang (1 listing = 1 baris, tanpa kuantitas)
        if (!Schema::hasTable('keranjang')) {
            Schema::create('keranjang', function (Blueprint $t) {
                $t->integer('KeranjangID')->autoIncrement();
                $t->integer('UserID');
                $t->integer('ProdukID');
                $t->timestamps();

                $t->foreign('UserID', 'fk_keranjang_user')->references('UserID')->on('user')->onDelete('cascade');
                $t->foreign('ProdukID', 'fk_keranjang_produk')->references('ProdukID')->on('produk')->onDelete('cascade');
                $t->unique(['UserID', 'ProdukID'], 'uq_keranjang_user_produk');
            });
        }

        // 7. Perluas enum status (data lama tetap valid)
        Schema::table('produk', function (Blueprint $t) {
            $t->enum('Status', ['Tersedia', 'Dipesan', 'Terjual', 'Disembunyikan'])->default('Tersedia')->change();
        });
        Schema::table('pesanan', function (Blueprint $t) {
            $t->enum('StatusPesanan', ['Menunggu Pembayaran', 'Diproses', 'Dikemas', 'Dikirim', 'Sampai Tujuan', 'Selesai', 'Dibatalkan', 'Kedaluwarsa'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
        Schema::dropIfExists('pengajuan_seller');
    }
};