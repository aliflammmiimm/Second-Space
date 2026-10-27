<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanSeller extends Model
{
    protected $table = 'pengajuan_seller';
    protected $primaryKey = 'PengajuanID';

    // Status & ReviewedBy tidak fillable: hanya diubah oleh Admin lewat controller.
    protected $fillable = ['UserID', 'Nama', 'NoHP', 'Alamat', 'Alasan', 'InfoTambahan'];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}