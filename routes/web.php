<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JenisProdukController as AdminJenisProdukController;
use App\Http\Controllers\Admin\ModerasiProdukController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\PengajuanSellerController as AdminPengajuanSellerController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\PengirimanController as AdminPengirimanController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Pelanggan\CheckoutController;
use App\Http\Controllers\Pelanggan\KatalogController;
use App\Http\Controllers\Pelanggan\KeranjangController;
use App\Http\Controllers\Pelanggan\PengajuanSellerController;
use App\Http\Controllers\Pelanggan\PesananController as PelangganPesananController;
use App\Http\Controllers\Pelanggan\ProfilController;
use App\Http\Controllers\Admin\PencairanController as AdminPencairanController;
use App\Http\Controllers\Seller\PencairanController as SellerPencairanController;
use App\Http\Controllers\Seller\PesananController as SellerPesananController;
use App\Http\Controllers\Seller\ProdukController as SellerProdukController;
use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Route;

// Midtrans Webhook Notification Callback (No Auth, No CSRF)
Route::post('/midtrans/notification', [MidtransController::class, 'notificationHandler'])->name('midtrans.notification');

// Public Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Katalog publik (tanpa login)
Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
    Route::get('/produk/{id}', [KatalogController::class, 'detail'])->name('produk.detail');
});

// Auth Routes untuk Publik (Pelanggan & Seller)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/daftar-seller', [AuthController::class, 'showRegisterSeller'])->name('register.seller');
Route::post('/daftar-seller', [AuthController::class, 'registerSeller']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Buyer (Guard: web, Role: Pelanggan)
Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth:web', 'role:Pelanggan'])->group(function () {
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    Route::post('/keranjang/{id}', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');

    // {id?} opsional: dengan id = "Beli Sekarang" 1 produk, tanpa id = checkout isi keranjang
    Route::get('/checkout/{id?}', [CheckoutController::class, 'show'])->whereNumber('id')->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/seller', [PengajuanSellerController::class, 'status'])->name('seller.status');
    Route::post('/seller', [PengajuanSellerController::class, 'store'])->name('seller.store');

    Route::get('/pesanan-aktif', [PelangganPesananController::class, 'pesananAktif'])->name('pesanan_aktif');
    Route::get('/pesanan', [PelangganPesananController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{id}', [PelangganPesananController::class, 'detail'])->name('pesanan.detail');
    Route::put('/pesanan/{id}/terima', [PelangganPesananController::class, 'terima'])->name('pesanan.terima');
});

// Admin (Guard: admin, Role: Admin)
Route::prefix('admin')->name('admin.')->group(function () {
    // Portal Login Admin (Mandiri & Terpisah)
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Rute Terproteksi Khusus Admin
    Route::middleware(['auth:admin', 'role:Admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Produk: Admin hanya melihat & memoderasi
        Route::get('/produk', [AdminProdukController::class, 'index'])->name('produk.index');
        Route::put('/produk/{id}/sembunyikan', [ModerasiProdukController::class, 'sembunyikan'])->name('produk.sembunyikan');
        Route::put('/produk/{id}/tampilkan', [ModerasiProdukController::class, 'tampilkan'])->name('produk.tampilkan');

        // Pengajuan Seller
        Route::get('/seller', [AdminPengajuanSellerController::class, 'index'])->name('seller.index');
        Route::put('/seller/{id}/approve', [AdminPengajuanSellerController::class, 'approve'])->name('seller.approve');
        Route::put('/seller/{id}/reject', [AdminPengajuanSellerController::class, 'reject'])->name('seller.reject');

        // Daftar seluruh Seller + hapus akun Seller
        Route::get('/daftar-seller', [AdminSellerController::class, 'index'])->name('seller_aktif.index');
        Route::delete('/daftar-seller/{id}', [AdminSellerController::class, 'destroy'])->name('seller_aktif.destroy');

        // Pengguna
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');

        // Kategori (4 kategori utama)
        Route::get('/jenis-produk', [AdminJenisProdukController::class, 'index'])->name('jenis.index');
        Route::put('/jenis-produk/{id}', [AdminJenisProdukController::class, 'update'])->name('jenis.update');

        // Pesanan (riwayat tidak dihapus, hanya dibatalkan)
        Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{id}', [AdminPesananController::class, 'detail'])->name('pesanan.detail');
        Route::put('/pesanan/{id}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.update_status');
        Route::put('/pesanan/{id}/batalkan', [AdminPesananController::class, 'batalkan'])->name('pesanan.batalkan');

        // Pembayaran
        Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran.index');
        Route::put('/pembayaran/{id}/status', [AdminPembayaranController::class, 'updateStatus'])->name('pembayaran.update_status');

        // Pengiriman
        Route::get('/pengiriman', [AdminPengirimanController::class, 'index'])->name('pengiriman.index');
        Route::put('/pengiriman/{id}/status', [AdminPengirimanController::class, 'updateStatus'])->name('pengiriman.update_status');

        // Pencairan dana seller
        Route::get('/pencairan', [AdminPencairanController::class, 'index'])->name('pencairan.index');
        Route::put('/pencairan/{id}/selesai', [AdminPencairanController::class, 'selesai'])->name('pencairan.selesai');
        Route::put('/pencairan/{id}/tolak', [AdminPencairanController::class, 'tolak'])->name('pencairan.tolak');
    });
});

// Seller (Guard: seller, Pelanggan dengan pengajuan approved)
Route::prefix('seller')->name('seller.')->middleware(['auth:seller', 'seller'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logoutSeller'])->name('logout');
    Route::get('/produk', [SellerProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/create', [SellerProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [SellerProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{id}/edit', [SellerProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{id}', [SellerProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [SellerProdukController::class, 'tarik'])->name('produk.tarik');
    Route::get('/pesanan', [SellerPesananController::class, 'index'])->name('pesanan.index');
    Route::put('/pesanan/{id}/kirim', [SellerPesananController::class, 'kirim'])->whereNumber('id')->name('pesanan.kirim');

    // Saldo & pencairan
    Route::get('/pencairan', [SellerPencairanController::class, 'index'])->name('pencairan.index');
    Route::put('/pencairan/rekening', [SellerPencairanController::class, 'simpanRekening'])->name('pencairan.rekening');
    Route::post('/pencairan', [SellerPencairanController::class, 'store'])->name('pencairan.store');
});