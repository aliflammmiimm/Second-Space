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

    // Role & is_seller sengaja TIDAK fillable.
    protected $fillable = [
        'Nama',
        'NoHP_Email',
        'Password',
        'Role',
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

    /** Total pendapatan bersih dari pesanan yang sudah Selesai. */
    public function totalPendapatan(): int
    {
        return (int) $this->itemPesanan()
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Selesai'))
            ->sum('PendapatanSeller');
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