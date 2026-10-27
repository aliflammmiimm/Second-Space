<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'ProdukID';
    public $timestamps = false;

    // Status & SellerID sengaja TIDAK fillable: selalu di-set eksplisit di server.
    protected $fillable = [
        'NamaProduk',
        'Deskripsi',
        'Harga',
        'Kondisi',
        'Gambar',
        'JenisID',
    ];

    public const KONDISI = [
        'seperti_baru' => 'Seperti baru',
        'baik'         => 'Baik',
        'cukup'        => 'Cukup',
    ];

    public function jenisProduk()
    {
        return $this->belongsTo(JenisProduk::class, 'JenisID', 'JenisID');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'SellerID', 'UserID');
    }

    public function pesananDetail()
    {
        return $this->hasMany(PesananDetail::class, 'ProdukID', 'ProdukID');
    }

    // Hanya barang yang boleh dibeli: status Tersedia + Seller masih approved
    public function scopeTersedia($q)
    {
        return $q->where('Status', 'Tersedia')
            ->whereHas('seller', fn ($s) => $s->where('is_seller', 1));
    }

    public function bisaDibeli(): bool
    {
        return $this->Status === 'Tersedia' && $this->seller && $this->seller->is_seller;
    }

    public function getGambarUrlAttribute(): string
    {
        if (!$this->Gambar) {
            return asset('upload/default.jpg');
        }

        if (str_starts_with($this->Gambar, 'http://') || str_starts_with($this->Gambar, 'https://')) {
            return $this->Gambar;
        }

        $filePath = public_path('upload/' . $this->Gambar);
        if (!file_exists($filePath)) {
            return asset('upload/default.jpg');
        }

        $encoded = implode('/', array_map('rawurlencode', explode('/', $this->Gambar)));
        return asset('upload/' . $encoded);
    }
}