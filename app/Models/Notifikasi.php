<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'NotifikasiID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'Tipe',
        'Judul',
        'Pesan',
        'Icon',
        'IconColor',
        'Url',
        'DibacaPada',
        'DibuatPada',
    ];

    protected $casts = [
        'DibacaPada' => 'datetime',
        'DibuatPada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('DibacaPada');
    }

    public function isRead(): bool
    {
        return !is_null($this->DibacaPada);
    }

    /**
     * Helper cepat untuk mengirimkan notifikasi ke pengguna manapun
     */
    public static function kirim(
        int $userId,
        string $tipe,
        string $judul,
        string $pesan,
        ?string $url = null,
        string $icon = 'bi-bell-fill',
        string $iconColor = '#00B8BE'
    ): self {
        return self::create([
            'UserID' => $userId,
            'Tipe' => $tipe,
            'Judul' => $judul,
            'Pesan' => $pesan,
            'Icon' => $icon,
            'IconColor' => $iconColor,
            'Url' => $url,
            'DibuatPada' => now('Asia/Makassar'),
        ]);
    }
}

