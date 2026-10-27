<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pencairan extends Model
{
    protected $table = 'pencairan';
    protected $primaryKey = 'PencairanID';

    // Status & pemroses sengaja tidak fillable: hanya diubah Admin lewat controller
    protected $fillable = ['SellerID', 'Jumlah', 'Bank', 'NoRekening', 'AtasNama'];

    protected $casts = ['DiprosesPada' => 'datetime'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'SellerID', 'UserID');
    }
}