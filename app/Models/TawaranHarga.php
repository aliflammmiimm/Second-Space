<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TawaranHarga extends Model
{
    protected $table = 'tawaran_harga';
    protected $primaryKey = 'TawaranID';
    public $timestamps = false;

    protected $fillable = [
        'PercakapanID',
        'PesanID',
        'PembeliID',
        'SellerID',
        'ProdukID',
        'HargaAsli',
        'HargaTawaran',
        'Status', // Menunggu, Diterima, Ditolak, Dibatalkan, Selesai
        'Catatan',
        'DibuatPada',
        'DiresponPada',
    ];

    protected $casts = [
        'HargaAsli' => 'integer',
        'HargaTawaran' => 'integer',
        'DibuatPada' => 'datetime',
        'DiresponPada' => 'datetime',
    ];

    public function percakapan()
    {
        return $this->belongsTo(Percakapan::class, 'PercakapanID', 'PercakapanID');
    }

    public function pesan()
    {
        return $this->belongsTo(Pesan::class, 'PesanID', 'PesanID');
    }

    public function pembeli()
    {
        return $this->belongsTo(User::class, 'PembeliID', 'UserID');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'SellerID', 'UserID');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ProdukID', 'ProdukID');
    }

    public function isMenunggu(): bool
    {
        return $this->Status === 'Menunggu';
    }

    public function isDiterima(): bool
    {
        return $this->Status === 'Diterima';
    }

    public function isDitolak(): bool
    {
        return $this->Status === 'Ditolak';
    }

    public function isDibatalkan(): bool
    {
        return $this->Status === 'Dibatalkan';
    }

    public function isSelesai(): bool
    {
        return $this->Status === 'Selesai';
    }
}

