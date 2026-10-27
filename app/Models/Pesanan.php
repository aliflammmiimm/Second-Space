<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'PesananID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'TanggalPesanan',
        'StatusPesanan',
        'MetodePembayaran',
        'TotalHarga',
        'BatasBayar',
    ];

    protected $casts = [
        'TanggalPesanan' => 'datetime',
        'BatasBayar' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function detail()
    {
        return $this->hasMany(PesananDetail::class, 'PesananID', 'PesananID');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'PesananID', 'PesananID');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'PesananID', 'PesananID');
    }
}