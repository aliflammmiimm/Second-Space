<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisProduk extends Model
{
    use HasFactory;

    protected $table = 'jenis_produk';
    protected $primaryKey = 'JenisID';
    public $timestamps = false;

    protected $fillable = [
        'NamaJenis',
        'Aktif',
    ];

    public function scopeAktif($q)
    {
        return $q->where('Aktif', 1);
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'JenisID', 'JenisID');
    }
}