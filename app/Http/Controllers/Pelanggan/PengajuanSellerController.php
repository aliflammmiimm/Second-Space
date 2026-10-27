<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSeller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengajuanSellerController extends Controller
{
    public function status()
    {
        $user = Auth::user();
        $pengajuan = PengajuanSeller::where('UserID', $user->UserID)->latest('PengajuanID')->first();

        return view('pelanggan.seller_status', compact('user', 'pengajuan'));
    }

       public function store(Request $request)
    {
        $data = $request->validate([
            'NoHP' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'Alamat' => 'required|string|max:150',
            'Kota' => 'required|string|max:60',
            'Provinsi' => 'required|string|max:60',
            'Alasan' => 'required|string|min:10|max:1000',
            'setuju' => 'accepted',
        ], [
            'NoHP.required' => 'Nomor WhatsApp wajib diisi.',
            'NoHP.regex' => 'Nomor WhatsApp tidak valid.',
            'Alamat.required' => 'Alamat domisili wajib diisi.',
            'Kota.required' => 'Kota/Kabupaten wajib diisi.',
            'Provinsi.required' => 'Provinsi wajib diisi.',
            'Alasan.required' => 'Alasan wajib diisi.',
            'Alasan.min' => 'Alasan minimal 10 karakter.',
            'setuju.accepted' => 'Anda harus menyetujui pernyataan sebelum mengirim pengajuan.',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($userId, $data) {
            $user = User::where('UserID', $userId)->lockForUpdate()->firstOrFail();

            if ($user->is_seller) {
                throw ValidationException::withMessages(['Alasan' => 'Anda sudah menjadi Seller.']);
            }
            if (PengajuanSeller::where('UserID', $userId)->where('Status', 'pending')->exists()) {
                throw ValidationException::withMessages(['Alasan' => 'Pengajuan Anda masih diproses Admin.']);
            }

            PengajuanSeller::create([
                'UserID' => $userId,
                'Nama' => $user->Nama,                       // dari akun, bukan dari form
                'NoHP' => trim($data['NoHP']),
                'Alamat' => trim($data['Alamat']) . ', ' . trim($data['Kota']) . ', ' . trim($data['Provinsi']),
                'Alasan' => $data['Alasan'],
            ]);
        });

        return redirect()->route('pelanggan.seller.status')
            ->with('success', 'Pengajuan terkirim. Mohon tunggu persetujuan Admin.');
    }
}