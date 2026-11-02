<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukGambar extends Model
{
    use HasFactory;

    protected $table = 'produk_gambar';
    protected $primaryKey = 'GambarID';

    protected $fillable = [
        'ProdukID',
        'PathGambar',
        'Urutan',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ProdukID', 'ProdukID');
    }

    public function getGambarUrlAttribute(): string
    {
        if (!$this->PathGambar) {
            return asset('upload/default.jpg');
        }

        if (str_starts_with($this->PathGambar, 'http://') || str_starts_with($this->PathGambar, 'https://')) {
            return $this->PathGambar;
        }

        $filePath = public_path('upload/' . $this->PathGambar);
        if (!file_exists($filePath)) {
            return asset('upload/default.jpg');
        }

        $encoded = implode('/', array_map('rawurlencode', explode('/', $this->PathGambar)));
        return asset('upload/' . $encoded);
    }
}

