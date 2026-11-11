<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'UserID';
    public $timestamps = false;

    // Role & is_seller sengaja TIDAK fillable demi keamanan (anti mass-assignment).
    protected $fillable = [
        'Nama',
        'NoHP_Email',
        'Password',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function getAuthIdentifierName()
    {
        return 'UserID';
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'UserID', 'UserID');
    }

    public function pengajuanSeller()
    {
        return $this->hasMany(PengajuanSeller::class, 'UserID', 'UserID');
    }

       public function produk()
    {
        return $this->hasMany(Produk::class, 'SellerID', 'UserID');
    }

    // Item pesanan yang dijual oleh user ini (sebagai Seller)
    public function itemPesanan()
    {
        return $this->hasMany(PesananDetail::class, 'SellerID', 'UserID');
    }

    public function pencairan()
    {
        return $this->hasMany(Pencairan::class, 'SellerID', 'UserID');
    }

    public function tagihanKomisiCod()
    {
        return $this->hasMany(TagihanKomisiCod::class, 'SellerID', 'UserID');
    }

    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
    }

    public const ONGKIR_MAKSIMAL = 10000;

    public function isMakassar(): bool
    {
        return stripos((string) $this->Kota, 'makassar') !== false;
    }

    public function bisaAntar(): bool
    {
        return (bool) $this->melayani_antar && $this->isMakassar();
    }

    public function bisaAmbil(): bool
    {
        return (bool) $this->melayani_ambil;
    }

    /** Ongkir toko, selalu dibatasi maksimal Rp10.000. */
    public function ongkirAntar(): int
    {
        return $this->bisaAntar() ? min(max(0, (int) $this->tarif_antar), self::ONGKIR_MAKSIMAL) : 0;
    }

    /** Teks info untuk katalog & detail produk. */
    public function infoPengantaran(): string
    {
        $bagian = [];
        if ($this->bisaAntar()) {
            $bagian[] = 'Antar khusus Kota Makassar, ongkir Rp' . number_format($this->ongkirAntar(), 0, ',', '.');
        }
        if ($this->bisaAmbil()) {
            $bagian[] = 'Ambil sendiri gratis';
        }

        return $bagian ? implode(' · ', $bagian) : 'Hubungi seller untuk cara terima barang';
    }

    /** Ongkir milik seller dari pesanan Selesai (ongkir 100% milik seller, tanpa komisi). */
    public function totalOngkir(string $metode): int
    {
        return (int) Pesanan::where('StatusPesanan', 'Selesai')
            ->where('MetodePembayaran', $metode)
            ->whereHas('detail', fn ($q) => $q->where('SellerID', $this->UserID))
            ->sum('Ongkir');
    }

    /** Total pendapatan bersih + ongkir dari pesanan yang sudah Selesai (uang dipegang platform via Transfer/Midtrans). */
    public function totalPendapatan(): int
    {
        return (int) $this->itemPesanan()
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Selesai')->where('MetodePembayaran', 'Transfer'))
            ->sum('PendapatanSeller') + $this->totalOngkir('Transfer');
    }

    /** Total penjualan COD yang sudah Selesai (uang tunai sudah diterima langsung oleh seller/kurir), termasuk ongkir. */
    public function totalPendapatanCod(): int
    {
        return (int) $this->itemPesanan()
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Selesai')->where('MetodePembayaran', 'COD'))
            ->sum('PendapatanSeller') + $this->totalOngkir('COD');
    }

    /** Pendapatan dikurangi pencairan yang menunggu / sudah ditransfer. */
    public function saldoTersedia(): int
    {
        $ditarik = (int) $this->pencairan()
            ->whereIn('Status', ['Menunggu', 'Selesai'])
            ->sum('Jumlah');

        return $this->totalPendapatan() - $ditarik;
    }

    public function isAdmin(): bool
    {
        return strtolower($this->Role) === 'admin';
    }



    public function isPelanggan(): bool
    {
        return strtolower($this->Role) === 'pelanggan';
    }

    public function isSeller(): bool
    {
        return (bool) $this->is_seller && !$this->isAdmin();
    }
}