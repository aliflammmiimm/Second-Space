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
            'tarif_antar' => 'nullable|integer|min:0|max:' . User::ONGKIR_MAKSIMAL,
        ], [
            'tarif_antar.max' => 'Tarif antar maksimal Rp10.000.',
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
        $user->save();

        return back()->with('success', 'Pengaturan toko disimpan.');
    }
}

