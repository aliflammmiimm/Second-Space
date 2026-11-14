<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'UserID';
    public $timestamps = false;

    // Role & is_seller sengaja TIDAK fillable demi keamanan (anti mass-assignment).
    protected $fillable = [
        'Nama',
        'NoHP_Email',
        'Password',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function getAuthIdentifierName()
    {
        return 'UserID';
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'UserID', 'UserID');
    }

    public function pengajuanSeller()
    {
        return $this->hasMany(PengajuanSeller::class, 'UserID', 'UserID');
    }

       public function produk()
    {
        return $this->hasMany(Produk::class, 'SellerID', 'UserID');
    }

    // Item pesanan yang dijual oleh user ini (sebagai Seller)
    public function itemPesanan()
    {
        return $this->hasMany(PesananDetail::class, 'SellerID', 'UserID');
    }

    public function pencairan()
    {
        return $this->hasMany(Pencairan::class, 'SellerID', 'UserID');
    }

    public function tagihanKomisiCod()
    {
        return $this->hasMany(TagihanKomisiCod::class, 'SellerID', 'UserID');
    }

    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
    }

    public const ONGKIR_MAKSIMAL = 500000;

    public function isMakassar(): bool
    {
        return stripos((string) $this->Kota, 'makassar') !== false;
    }

    public function bisaAntar(): bool
    {
        return (bool) $this->melayani_antar && $this->isMakassar();
    }

    public function bisaAmbil(): bool
    {
        return $this->melayani_ambil === null ? true : (bool) $this->melayani_ambil;
    }

    public function getLatToko(): float
    {
        return !is_null($this->Latitude) ? (float) $this->Latitude : \App\Services\OngkirService::DEFAULT_LAT;
    }

    public function getLonToko(): float
    {
        return !is_null($this->Longitude) ? (float) $this->Longitude : \App\Services\OngkirService::DEFAULT_LON;
    }

    public function punyaKoordinatToko(): bool
    {
        return !is_null($this->Latitude) && !is_null($this->Longitude);
    }

    /** Estimasi ongkir toko (default). Ongkir riil ditentukan seller per pesanan sesuai jarak. */
    public function ongkirAntar(): int
    {
        return $this->bisaAntar() ? max(0, (int) $this->tarif_antar) : 0;
    }

    /** Teks info untuk katalog & detail produk. */
    public function infoPengantaran(): string
    {
        $bagian = [];
        if ($this->bisaAntar()) {
            $bagian[] = 'Diantar seller (khusus Kota Makassar, ongkir sesuai jarak)';
        }
        if ($this->bisaAmbil()) {
            $bagian[] = 'Ambil sendiri di toko (gratis)';
        }

        return $bagian ? implode(' · ', $bagian) : 'Hubungi seller untuk cara terima barang';
    }

    public const DAFTAR_HARI = [
        'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
    ];

    public function hariOperasionalArray(): array
    {
        if (empty($this->HariOperasional)) {
            return self::DAFTAR_HARI;
        }

        $list = array_map('trim', explode(',', (string) $this->HariOperasional));
        return !empty($list) ? $list : self::DAFTAR_HARI;
    }

    public function hariOperasionalLabel(): string
    {
        $arr = $this->hariOperasionalArray();
        if (count($arr) === 7) {
            return 'Setiap Hari (Senin - Minggu)';
        }
        if (count($arr) === 6 && !in_array('Minggu', $arr, true)) {
            return 'Senin - Sabtu';
        }
        if (count($arr) === 5 && !in_array('Sabtu', $arr, true) && !in_array('Minggu', $arr, true)) {
            return 'Senin - Jumat';
        }
        return implode(', ', $arr);
    }

    public function statusOperasionalToko(): array
    {
        $jamBuka = $this->JamBuka ?: '08:00';
        $jamTutup = $this->JamTutup ?: '21:00';
        $jamLabel = $jamBuka . ' - ' . $jamTutup . ' WITA';
        $hariLabel = $this->hariOperasionalLabel();

        // 1. Cek jika seller sengaja menutup toko manual (libur sementara)
        if ($this->is_toko_buka !== null && !(bool) $this->is_toko_buka) {
            return [
                'buka' => false,
                'status' => 'Libur Sementara',
                'label' => 'Toko Sedang Tutup / Libur',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'jam' => $jamLabel,
                'hari' => $hariLabel,
                'pesan' => $this->CatatanLibur ?: 'Seller sedang rehat atau libur sementara.',
            ];
        }

        $now = now('Asia/Makassar');
        $namaHariMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariIni = $namaHariMap[$now->dayOfWeek];
        $hariAktif = $this->hariOperasionalArray();

        // 2. Cek apakah hari ini toko buka
        if (!in_array($hariIni, $hariAktif, true)) {
            return [
                'buka' => false,
                'status' => 'Tutup Hari Ini',
                'label' => 'Tutup Hari Ini (' . $hariIni . ')',
                'badge_class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                'jam' => $jamLabel,
                'hari' => $hariLabel,
                'pesan' => 'Toko libur setiap hari ' . $hariIni . '. Buka pada hari: ' . $hariLabel . '.',
            ];
        }

        // 3. Cek jam operasional saat ini
        $jamSekarang = $now->format('H:i');
        if ($jamSekarang >= $jamBuka && $jamSekarang <= $jamTutup) {
            return [
                'buka' => true,
                'status' => 'Buka',
                'label' => 'Buka Sekarang',
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                'jam' => $jamLabel,
                'hari' => $hariLabel,
                'pesan' => 'Melayani pesanan & chat sampai pukul ' . $jamTutup . ' WITA.',
            ];
        }

        return [
            'buka' => false,
            'status' => 'Di Luar Jam Kerja',
            'label' => 'Toko Tutup (Buka ' . $jamBuka . ' WITA)',
            'badge_class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'jam' => $jamLabel,
            'hari' => $hariLabel,
            'pesan' => 'Di luar jam operasional (' . $jamLabel . '). Respon chat mungkin lebih lambat.',
        ];
    }

    /** Ongkir milik seller dari pesanan Selesai (ongkir 100% milik seller, tanpa komisi). */
    public function totalOngkir(string $metode): int
    {
        $q = Pesanan::where('StatusPesanan', 'Selesai')
            ->whereHas('detail', fn ($q) => $q->where('SellerID', $this->UserID));

        if ($metode === 'Transfer' || $metode === 'QRIS') {
            $q->whereIn('MetodePembayaran', ['QRIS', 'Transfer']);
        } else {
            $q->where('MetodePembayaran', $metode);
        }

        return (int) $q->sum('Ongkir');
    }

    /** Total pendapatan bersih + ongkir dari pesanan yang sudah Selesai (uang dipegang platform via QRIS/Transfer). */
    public function totalPendapatan(): int
    {
        return (int) $this->itemPesanan()
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Selesai')->whereIn('MetodePembayaran', ['QRIS', 'Transfer']))
            ->sum('PendapatanSeller') + $this->totalOngkir('QRIS');
    }

    /** Total penjualan COD yang sudah Selesai (uang tunai sudah diterima langsung oleh seller/kurir), termasuk ongkir. */
    public function totalPendapatanCod(): int
    {
        return (int) $this->itemPesanan()
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Selesai')->where('MetodePembayaran', 'COD'))
            ->sum('PendapatanSeller') + $this->totalOngkir('COD');
    }

    /** Pendapatan dikurangi pencairan yang menunggu / sudah ditransfer. */
    public function saldoTersedia(): int
    {
        $ditarik = (int) $this->pencairan()
            ->whereIn('Status', ['Menunggu', 'Selesai'])
            ->sum('Jumlah');

        return $this->totalPendapatan() - $ditarik;
    }

    public function isAdmin(): bool
    {
        return strtolower($this->Role) === 'admin';
    }



    public function isPelanggan(): bool
    {
        return strtolower($this->Role) === 'pelanggan';
    }

    public function isSeller(): bool
    {
        return (bool) $this->is_seller && !$this->isAdmin();
    }

    /** Total pesan chat yang belum dibaca */
    public function pesanBelumDibaca(bool $sebagaiSeller = false): int
    {
        $kolom = $sebagaiSeller ? 'SellerID' : 'PembeliID';

        return (int) Pesan::whereHas('percakapan', fn ($q) => $q->where($kolom, $this->UserID))
            ->where('PengirimID', '!=', $this->UserID)
            ->whereNull('DibacaPada')
            ->count();
    }

    /** Pesanan masuk yang memerlukan tindakan seller (konfirmasi ongkir, kemas, kirim) */
    public function pesananMasukPerluTindakanCount(): int
    {
        return (int) PesananDetail::where('SellerID', $this->UserID)
            ->whereHas('pesanan', fn ($q) => $q->whereIn('StatusPesanan', [
                'Menunggu Konfirmasi Ongkir',
                'Diproses',
                'Dikemas',
            ]))
            ->count();
    }

    /** Tagihan komisi COD seller yang belum dibayar */
    public function tagihanKomisiBelumDibayarCount(): int
    {
        return (int) TagihanKomisiCod::where('SellerID', $this->UserID)
            ->where('Status', 'Belum Dibayar')
            ->count();
    }

    /** Jumlah barang di keranjang belanja pembeli */
    public function keranjangCount(): int
    {
        return (int) Keranjang::where('UserID', $this->UserID)->count();
    }

    public function ulasan()
    {
        return $this->hasMany(UlasanProduk::class, 'UserID', 'UserID');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'UserID', 'UserID')->orderByDesc('DibuatPada');
    }

    public function unreadNotifikasiCount(): int
    {
        return (int) $this->notifikasi()->unread()->count();
    }

    /** Jumlah pesanan aktif pembeli yang sedang berjalan */
    public function pesananAktifBuyerCount(): int
    {
        return (int) Pesanan::where('UserID', $this->UserID)
            ->whereIn('StatusPesanan', [
                'Menunggu Konfirmasi Ongkir',
                'Menunggu Pembayaran',
                'Diproses',
                'Dikemas',
                'Dikirim',
            ])
            ->count();
    }

    /** Rekap jumlah tugas operasional yang menunggu tindakan admin */
    public function adminPendingCounts(): array
    {
        if (!$this->isAdmin()) {
            return [
                'pengajuan_seller' => 0,
                'pembayaran' => 0,
                'pencairan' => 0,
                'komisi' => 0,
                'total' => 0,
            ];
        }

        $pengajuan = (int) PengajuanSeller::where('Status', 'pending')->count();
        $pembayaran = (int) Pembayaran::where('StatusPembayaran', 'Menunggu')
            ->whereNotNull('BuktiBayar')
            ->count();
        $pencairan = (int) Pencairan::where('Status', 'Menunggu')->count();
        $komisi = (int) TagihanKomisiCod::where('Status', 'Menunggu Verifikasi')->count();

        return [
            'pengajuan_seller' => $pengajuan,
            'pembayaran' => $pembayaran,
            'pencairan' => $pencairan,
            'komisi' => $komisi,
            'total' => $pengajuan + $pembayaran + $pencairan + $komisi,
        ];
    }
}