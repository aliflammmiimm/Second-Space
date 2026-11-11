<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $table = 'pesan_chat';
    protected $primaryKey = 'PesanID';
    public $timestamps = false;

    protected $fillable = ['PercakapanID', 'PengirimID', 'ProdukID', 'Isi', 'DibacaPada', 'DibuatPada'];

    protected $casts = [
        'DibacaPada' => 'datetime',
        'DibuatPada' => 'datetime',
    ];

    public function percakapan()
    {
        return $this->belongsTo(Percakapan::class, 'PercakapanID', 'PercakapanID');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'PengirimID', 'UserID');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ProdukID', 'ProdukID');
    }
}

