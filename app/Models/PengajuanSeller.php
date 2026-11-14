<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanSeller extends Model
{
    protected $table = 'pengajuan_seller';
    protected $primaryKey = 'PengajuanID';

    // Status & ReviewedBy tidak fillable: hanya diubah oleh Admin lewat controller.
    protected $fillable = ['UserID', 'Nama', 'NoHP', 'Alamat', 'Alasan', 'InfoTambahan'];

    /** Daftar 38 Provinsi di Indonesia (Sulawesi Selatan diutamakan untuk SecondSpace) */
    public const DAFTAR_PROVINSI = [
        'Sulawesi Selatan',
        'Sulawesi Barat',
        'Sulawesi Tengah',
        'Sulawesi Tenggara',
        'Sulawesi Utara',
        'Gorontalo',
        'DKI Jakarta',
        'Jawa Barat',
        'Jawa Tengah',
        'DI Yogyakarta',
        'Jawa Timur',
        'Banten',
        'Bali',
        'Nusa Tenggara Barat',
        'Nusa Tenggara Timur',
        'Kalimantan Barat',
        'Kalimantan Tengah',
        'Kalimantan Selatan',
        'Kalimantan Timur',
        'Kalimantan Utara',
        'Aceh',
        'Sumatera Utara',
        'Sumatera Barat',
        'Riau',
        'Kepulauan Riau',
        'Jambi',
        'Sumatera Selatan',
        'Kepulauan Bangka Belitung',
        'Bengkulu',
        'Lampung',
        'Maluku',
        'Maluku Utara',
        'Papua',
        'Papua Barat',
        'Papua Selatan',
        'Papua Tengah',
        'Papua Pegunungan',
        'Papua Barat Daya',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}