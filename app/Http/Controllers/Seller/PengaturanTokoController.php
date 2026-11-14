<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanTokoController extends Controller
{
    public function index()
    {
        return view('seller.pengaturan.index', ['user' => Auth::user(), 'maks' => User::ONGKIR_MAKSIMAL]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'Kota' => 'required|string|max:60',
            'AlamatToko' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tarif_antar' => 'nullable|integer|min:0|max:' . User::ONGKIR_MAKSIMAL,
            'is_toko_buka' => 'nullable|boolean',
            'CatatanLibur' => 'nullable|string|max:255',
            'JamBuka' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'JamTutup' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'hari_operasional' => 'nullable|array',
            'hari_operasional.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
        ], [
            'tarif_antar.max' => 'Estimasi tarif antar maksimal Rp 500.000.',
            'JamBuka.regex' => 'Format jam buka harus JJ:MM (contoh: 08:00).',
            'JamTutup.regex' => 'Format jam tutup harus JJ:MM (contoh: 21:00).',
        ]);

        $ambil = $request->boolean('melayani_ambil');
        $antar = $request->boolean('melayani_antar');

        if (!$ambil && !$antar) {
            return back()->withInput()->with('error', 'Aktifkan minimal satu cara terima (ambil sendiri atau antar).');
        }

        if ($ambil && trim((string) ($data['AlamatToko'] ?? '')) === '') {
            return back()->withInput()->with('error', 'Isi alamat pengambilan barang karena Anda melayani ambil sendiri.');
        }

        /** @var User $user */
        $user = Auth::user();
        $user->Kota = trim($data['Kota']);
        $user->AlamatToko = isset($data['AlamatToko']) ? trim($data['AlamatToko']) : null;

        if ($antar && !$user->isMakassar()) {
            return back()->withInput()->with('error', 'Layanan antar hanya untuk seller di Kota Makassar.');
        }

        $user->melayani_ambil = $ambil;
        $user->melayani_antar = $antar;
        $user->tarif_antar = $antar ? (int) ($data['tarif_antar'] ?? 0) : 0;

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $user->Latitude = (float) $request->input('latitude');
            $user->Longitude = (float) $request->input('longitude');
        }

        // Pengaturan Jam Operasional & Status Toko
        $user->is_toko_buka = $request->boolean('is_toko_buka', true);
        $user->CatatanLibur = $request->filled('CatatanLibur') ? trim($request->input('CatatanLibur')) : null;
        $user->JamBuka = $request->input('JamBuka') ?: '08:00';
        $user->JamTutup = $request->input('JamTutup') ?: '21:00';

        $hariPilihan = $request->input('hari_operasional');
        if (is_array($hariPilihan) && !empty($hariPilihan)) {
            $user->HariOperasional = implode(',', $hariPilihan);
        } else {
            $user->HariOperasional = implode(',', User::DAFTAR_HARI);
        }

        $user->save();

        return back()->with('success', 'Pengaturan toko dan jam operasional berhasil disimpan.');
    }
}

