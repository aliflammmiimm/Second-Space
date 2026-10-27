<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    use HasFactory;

    protected $table = 'pesanandetail';
    protected $primaryKey = 'DetailID';
    public $timestamps = false;

    protected $fillable = [
        'PesananID',
        'ProdukID',
        'Jumlah',
        'HargaSatuan',
        'SubTotal',
        'SellerID',
        'Komisi',
        'PendapatanSeller',
    ];

    /** Hitung komisi website (dibulatkan) dari sebuah subtotal. */
    public static function hitungKomisi(int $subTotal): int
    {
        return (int) round($subTotal * config('santara.komisi_persen', 10) / 100);
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'PesananID', 'PesananID');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ProdukID', 'ProdukID');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'SellerID', 'UserID');
    }
}