<?php

namespace Tests\Feature;

use App\Models\JenisProduk;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\ProdukGambar;
use App\Models\User;
use App\Services\ReservasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecondSpaceMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    private function createBuyer(): User
    {
        $u = new User([
            'Nama' => 'Buyer Test',
            'NoHP_Email' => 'buyertest@test.com',
            'Password' => Hash::make('secret123'),
        ]);
        $u->Role = 'Pelanggan';
        $u->save();
        return $u;
    }

    private function createSeller(string $name, string $email): User
    {
        $s = new User([
            'Nama' => $name,
            'NoHP_Email' => $email,
            'Password' => Hash::make('secret123'),
        ]);
        $s->Role = 'Pelanggan';
        $s->is_seller = 1;
        $s->save();
        return $s;
    }

    private function createCategory(): JenisProduk
    {
        return JenisProduk::firstOrCreate(['NamaJenis' => 'Fashion'], ['Aktif' => 1]);
    }

    private function createProduct(User $seller, string $nama = 'Jaket Vintage'): Produk
    {
        $cat = $this->createCategory();
        $p = new Produk([
            'NamaProduk' => $nama,
            'JenisID' => $cat->JenisID,
            'Deskripsi' => 'Jaket thrift kondisi mulus.',
            'Harga' => 150000,
            'Kondisi' => 'baik',
            'Gambar' => 'default.jpg',
        ]);
        $p->SellerID = $seller->UserID;
        $p->Status = 'Tersedia';
        $p->Stok = 1;
        $p->save();
        return $p;
    }

    /** @test */
    public function test_user_role_mass_assignment_protection(): void
    {
        // Mencoba inject Role => Admin via mass assignment
        $user = new User([
            'Nama' => 'Attacker',
            'NoHP_Email' => 'attacker@test.com',
            'Password' => Hash::make('secret123'),
            'Role' => 'Admin',
        ]);
        $user->save();

        // Role tidak boleh menjadi Admin karena Role dihilangkan dari $fillable
        $this->assertNotEquals('Admin', $user->fresh()->Role);
    }

    /** @test */
    public function test_multi_image_gallery_attribute(): void
    {
        $seller = $this->createSeller('Seller Gallery', 'sellergal@test.com');
        $product = $this->createProduct($seller, 'Baju Thrift Multi Image');

        ProdukGambar::create([
            'ProdukID' => $product->ProdukID,
            'PathGambar' => 'detail1.jpg',
            'Urutan' => 1,
        ]);

        ProdukGambar::create([
            'ProdukID' => $product->ProdukID,
            'PathGambar' => 'detail2.jpg',
            'Urutan' => 2,
        ]);

        $this->assertCount(2, $product->gambarTambahan);
        $this->assertCount(3, $product->semua_gambar); // 1 gambar utama + 2 tambahan
    }

    /** @test */
    public function test_single_stock_reservation_and_expiry_release(): void
    {
        $seller = $this->createSeller('Seller Reservasi', 'sellerres@test.com');
        $product = $this->createProduct($seller, 'Celana Corduroy');

        $buyer = $this->createBuyer();

        $pesanan = Pesanan::create([
            'UserID' => $buyer->UserID,
            'TanggalPesanan' => now()->subHours(25), // Melewati batas 24 jam
            'TotalHarga' => $product->Harga,
            'StatusPesanan' => 'Menunggu Pembayaran',
            'MetodePembayaran' => 'Transfer',
            'BatasBayar' => now()->subHour(),
        ]);

        PesananDetail::create([
            'PesananID' => $pesanan->PesananID,
            'ProdukID' => $product->ProdukID,
            'Jumlah' => 1,
            'HargaSatuan' => $product->Harga,
            'SubTotal' => $product->Harga,
            'SellerID' => $seller->UserID,
            'Komisi' => 15000,
            'PendapatanSeller' => 135000,
        ]);

        $product->Status = 'Dipesan';
        $product->save();

        Pembayaran::create([
            'PesananID' => $pesanan->PesananID,
            'Metode' => 'Transfer',
            'StatusPembayaran' => 'Menunggu',
        ]);

        // Jalankan service lepas kedaluwarsa
        $dilepas = ReservasiService::lepasKedaluwarsa();

        $this->assertEquals(1, $dilepas);
        $this->assertEquals('Kedaluwarsa', $pesanan->fresh()->StatusPesanan);
        $this->assertEquals('Tersedia', $product->fresh()->Status);
    }
}

