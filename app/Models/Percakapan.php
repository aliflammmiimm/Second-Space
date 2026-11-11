<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Percakapan extends Model
{
    protected $table = 'percakapan';
    protected $primaryKey = 'PercakapanID';
    public $timestamps = false;

    protected $fillable = ['PembeliID', 'SellerID', 'TerakhirPada'];

    protected $casts = [
        'TerakhirPada' => 'datetime',
    ];

    public function pembeli()
    {
        return $this->belongsTo(User::class, 'PembeliID', 'UserID');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'SellerID', 'UserID');
    }

    public function pesan()
    {
        return $this->hasMany(Pesan::class, 'PercakapanID', 'PercakapanID');
    }

    public function pesanTerakhir()
    {
        return $this->hasOne(Pesan::class, 'PercakapanID', 'PercakapanID')->latestOfMany('PesanID');
    }
}

