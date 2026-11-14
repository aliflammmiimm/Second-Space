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

    public function gambarTambahan()
    {
        return $this->hasMany(ProdukGambar::class, 'ProdukID', 'ProdukID')->orderBy('Urutan', 'asc');
    }

    public function ulasan()
    {
        return $this->hasMany(UlasanProduk::class, 'ProdukID', 'ProdukID')->orderByDesc('DibuatPada');
    }

    public function ratingRataRata(): float
    {
        $avg = $this->ulasan()->avg('Bintang');
        return $avg !== null ? round((float) $avg, 1) : 5.0;
    }

    public function totalUlasan(): int
    {
        return (int) $this->ulasan()->count();
    }

    /**
     * Mengembalikan daftar semua gambar produk (foto utama + foto pendukung).
     */
    public function getSemuaGambarAttribute(): array
    {
        $list = [];

        if ($this->gambar_url) {
            $list[] = [
                'id' => null,
                'url' => $this->gambar_url,
                'is_primary' => true,
            ];
        }

        $tambahan = $this->relationLoaded('gambarTambahan')
            ? $this->gambarTambahan
            : $this->gambarTambahan()->get();

        foreach ($tambahan as $g) {
            $list[] = [
                'id' => $g->GambarID,
                'url' => $g->gambar_url,
                'is_primary' => false,
            ];
        }

        return $list;
    }

    // Hanya barang yang boleh dibeli: status Tersedia + Seller masih approved & tidak diblokir
    public function scopeTersedia($q)
    {
        return $q->where('Status', 'Tersedia')
            ->whereHas('seller', fn ($s) => $s->where('is_seller', 1)->where('is_blocked', 0));
    }

    public function bisaDibeli(): bool
    {
        return $this->Status === 'Tersedia' && $this->seller && $this->seller->is_seller && empty($this->seller->is_blocked);
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