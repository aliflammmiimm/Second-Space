<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanKomisiCod extends Model
{
    use HasFactory;

    protected $table = 'tagihan_komisi_cod';
    protected $primaryKey = 'TagihanID';

    protected $fillable = [
        'PesananID',
        'SellerID',
        'JumlahKomisi',
        'BatasBayar',
        'Status',
        'BuktiTransfer',
        'DiunggahPada',
        'CatatanAdmin',
        'DiverifikasiOleh',
        'DiverifikasiPada',
    ];

    protected $casts = [
        'BatasBayar' => 'datetime',
        'DiunggahPada' => 'datetime',
        'DiverifikasiPada' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'PesananID', 'PesananID');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'SellerID', 'UserID');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'DiverifikasiOleh', 'UserID');
    }

    public function isLewatBatas(): bool
    {
        return !in_array($this->Status, ['Lunas', 'Dibatalkan'], true) && now()->greaterThan($this->BatasBayar);
    }

    public function sisaWaktu(): string
    {
        if ($this->Status === 'Lunas') {
            return 'Lunas';
        }

        if (now()->greaterThan($this->BatasBayar)) {
            return 'Lewat Batas (Menunggak)';
        }

        $diffHours = (int) now()->diffInHours($this->BatasBayar, false);
        if ($diffHours > 24) {
            $days = (int) ceil($diffHours / 24);
            return "{$days} hari lagi";
        }

        return "{$diffHours} jam lagi";
    }

    public function getBuktiTransferUrlAttribute(): ?string
    {
        if (!$this->BuktiTransfer) {
            return null;
        }

        if (str_starts_with($this->BuktiTransfer, 'http://') || str_starts_with($this->BuktiTransfer, 'https://')) {
            return $this->BuktiTransfer;
        }

        return asset('upload/komisi/' . rawurlencode($this->BuktiTransfer));
    }
}

