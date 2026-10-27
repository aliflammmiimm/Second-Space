<?php

namespace Database\Seeders;

use App\Models\JenisProduk;
use App\Models\PengajuanSeller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 4 kategori utama
        foreach (['Elektronik', 'Fashion', 'Peralatan Rumah Tangga', 'ATK'] as $nama) {
            JenisProduk::firstOrCreate(['NamaJenis' => $nama], ['Aktif' => 1]);
        }

        // Admin (GANTI password ini setelah login pertama)
        $admin = User::firstOrNew(['NoHP_Email' => 'admin@secondspace.test']);
        if (!$admin->exists) {
            $admin->Nama = 'Admin SecondSpace';
            $admin->Password = Hash::make('admin12345');
            $admin->Role = 'Admin';
            $admin->save();
        }

        // Seller contoh (sudah disetujui)
        $seller = User::firstOrNew(['NoHP_Email' => 'seller@secondspace.test']);
        if (!$seller->exists) {
            $seller->Nama = 'Seller Contoh';
            $seller->Password = Hash::make('seller12345');
            $seller->Role = 'Pelanggan';
            $seller->is_seller = 1;
            $seller->save();

            PengajuanSeller::create([
                'UserID' => $seller->UserID,
                'Nama' => $seller->Nama,
                'NoHP' => '081234567890',
                'Alamat' => 'Jl. Contoh No. 1',
                'Alasan' => 'Akun contoh untuk pengujian.',
                'Status' => 'approved',
                'ReviewedBy' => $admin->UserID,
            ]);
        }

        // Pembeli contoh
        $buyer = User::firstOrNew(['NoHP_Email' => 'buyer@secondspace.test']);
        if (!$buyer->exists) {
            $buyer->Nama = 'Pembeli Contoh';
            $buyer->Password = Hash::make('buyer12345');
            $buyer->Role = 'Pelanggan';
            $buyer->save();
        }
    }
}
