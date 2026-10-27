<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'PengirimanID';
    public $timestamps = false;

    protected $fillable = [
        'PesananID',
        'StatusPengiriman',
        'TanggalUpdate',
        'AlamatPengiriman',
        'NoResi',
    ];

    protected $casts = [
        'TanggalUpdate' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'PesananID', 'PesananID');
    }
}