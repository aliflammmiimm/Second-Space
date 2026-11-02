<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\TagihanKomisiCod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KomisiController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();

        $tagihan = TagihanKomisiCod::where('SellerID', $sellerId)
            ->with(['pesanan.detail.produk'])
            ->latest('TagihanID')
            ->paginate(10);

        $totalBelumBayar = TagihanKomisiCod::where('SellerID', $sellerId)
            ->where('Status', 'Belum Dibayar')
            ->sum('JumlahKomisi');

        $totalMenungguVerifikasi = TagihanKomisiCod::where('SellerID', $sellerId)
            ->where('Status', 'Menunggu Verifikasi')
            ->sum('JumlahKomisi');

        $totalLunas = TagihanKomisiCod::where('SellerID', $sellerId)
            ->where('Status', 'Lunas')
            ->sum('JumlahKomisi');

        return view('seller.komisi.index', compact(
            'tagihan',
            'totalBelumBayar',
            'totalMenungguVerifikasi',
            'totalLunas'
        ));
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'bukti.required' => 'File bukti transfer wajib diunggah.',
            'bukti.image' => 'File harus berupa gambar.',
            'bukti.max' => 'Ukuran file bukti transfer maksimal 2 MB.',
        ]);

        $sellerId = Auth::id();
        $t = TagihanKomisiCod::where('TagihanID', $id)
            ->where('SellerID', $sellerId)
            ->firstOrFail();

        if ($t->Status === 'Lunas') {
            return back()->with('error', 'Tagihan komisi ini sudah lunas.');
        }

        $file = $request->file('bukti');
        $namaFile = time() . '_komisi_' . Str::random(10) . '.' . $file->guessExtension();

        $dest = public_path('upload/komisi');
        if (!File::isDirectory($dest)) {
            File::makeDirectory($dest, 0755, true, true);
        }

        // Hapus file lama jika ada
        if ($t->BuktiTransfer && !str_starts_with($t->BuktiTransfer, 'http')) {
            $pathLama = public_path('upload/komisi/' . $t->BuktiTransfer);
            if (File::exists($pathLama)) {
                @File::delete($pathLama);
            }
        }

        $file->move($dest, $namaFile);

        $t->BuktiTransfer = $namaFile;
        $t->DiunggahPada = now();
        $t->Status = 'Menunggu Verifikasi';
        $t->CatatanAdmin = null; // reset catatan penolakan jika sebelumnya ditolak
        $t->save();

        return back()->with('success', "Bukti transfer komisi untuk tagihan #{$t->TagihanID} berhasil diunggah. Mohon tunggu verifikasi admin.");
    }
}

