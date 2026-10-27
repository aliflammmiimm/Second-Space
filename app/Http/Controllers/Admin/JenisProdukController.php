<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JenisProdukController extends Controller
{
    public function index()
    {
        $categories = JenisProduk::withCount('produk')
            ->orderByDesc('Aktif')
            ->orderBy('NamaJenis')
            ->get();

        return view('admin.jenis.index', compact('categories'));
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisProduk::findOrFail($id);

        $data = $request->validate([
            'NamaJenis' => ['required', 'string', 'max:100', Rule::unique('jenis_produk', 'NamaJenis')->ignore($jenis->JenisID, 'JenisID')],
            'Aktif' => 'required|boolean',
        ]);

        // Maksimal 4 kategori aktif (empat kategori utama)
        if ($data['Aktif'] && !$jenis->Aktif && JenisProduk::where('Aktif', 1)->count() >= 4) {
            return back()->with('error', 'Hanya boleh ada 4 kategori utama yang aktif.');
        }

        $jenis->NamaJenis = trim($data['NamaJenis']);
        $jenis->Aktif = (bool) $data['Aktif'];
        $jenis->save();

        return back()->with('success', 'Kategori diperbarui.');
    }
}