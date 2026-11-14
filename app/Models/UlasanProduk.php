<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UlasanProduk extends Model
{
    protected $table = 'ulasan_produk';
    protected $primaryKey = 'UlasanID';
    public $timestamps = false;

    protected $fillable = [
        'ProdukID',
        'UserID',
        'PesananID',
        'Bintang',
        'Komentar',
        'Foto',
        'DibalasOlehSeller',
        'DibalasPada',
        'DibuatPada',
    ];

    protected $casts = [
        'Bintang' => 'integer',
        'DibalasPada' => 'datetime',
        'DibuatPada' => 'datetime',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ProdukID', 'ProdukID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'PesananID', 'PesananID');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->Foto) {
            return null;
        }

        return asset('upload/ulasan/' . $this->Foto);
    }

    /** Nama pembeli disamarkan sebagian untuk privasi */
    public function getNamaSamarAttribute(): string
    {
        $nama = $this->user->Nama ?? 'Pembeli';
        $panjang = mb_strlen($nama);

        if ($panjang <= 3) {
            return $nama . '***';
        }

        return mb_substr($nama, 0, 2) . str_repeat('*', max(3, $panjang - 3)) . mb_substr($nama, -1);
    }
}

