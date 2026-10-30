<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'PembayaranID';
    public $timestamps = false;

    protected $fillable = [
        'PesananID',
        'Metode',
        'StatusPembayaran',
        'SnapToken',
        'TransactionID',
        'PaymentType',
        'PaymentDetails',
        'BuktiBayar',
        'DiunggahPada',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'PesananID', 'PesananID');
    }
}