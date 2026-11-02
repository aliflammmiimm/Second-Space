<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanKomisiCod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class KomisiController extends Controller
{
    public function index(Request $request)
    {
        $query = TagihanKomisiCod::with(['seller:UserID,Nama,NoHP_Email,is_blocked', 'pesanan.detail.produk']);

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'menunggak') {
                $query->where('Status', 'Belum Dibayar')->where('BatasBayar', '<', now());
            } else {
                $query->where('Status', $status);
            }
        }

        $tagihan = $query->latest('TagihanID')->paginate(15)->withQueryString();

        $totalLunas = TagihanKomisiCod::where('Status', 'Lunas')->sum('JumlahKomisi');
        $totalMenungguVerifikasi = TagihanKomisiCod::where('Status', 'Menunggu Verifikasi')->sum('JumlahKomisi');
        $totalMenunggak = TagihanKomisiCod::where('Status', 'Belum Dibayar')->where('BatasBayar', '<', now())->sum('JumlahKomisi');

        return view('admin.komisi.index', compact(
            'tagihan',
            'totalLunas',
            'totalMenungguVerifikasi',
            'totalMenunggak'
        ));
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'aksi' => 'required|in:setujui,tolak',
            'catatan' => 'nullable|string|max:255',
        ]);

        $t = TagihanKomisiCod::with('seller')->findOrFail($id);
        $aksi = $request->input('aksi');

        if ($aksi === 'setujui') {
            $t->Status = 'Lunas';
            $t->DiverifikasiOleh = Auth::id();
            $t->DiverifikasiPada = now();
            $t->save();

            // Cek jika seller diblokir karena tunggakan dan sudah tidak memiliki tunggakan lain, buka blokir otomatis
            $seller = $t->seller;
            if ($seller && $seller->is_blocked) {
                $masihAdaTunggakan = TagihanKomisiCod::where('SellerID', $seller->UserID)
                    ->where('Status', 'Belum Dibayar')
                    ->where('BatasBayar', '<', now())
                    ->exists();

                if (!$masihAdaTunggakan && str_contains($seller->AlasanBlokir ?? '', 'Tunggakan komisi COD')) {
                    $seller->is_blocked = 0;
                    $seller->AlasanBlokir = null;
                    $seller->DiblokirPada = null;
                    $seller->save();
                }
            }

            return back()->with('success', "Tagihan komisi COD #{$t->TagihanID} dinyatakan LUNAS.");
        }

        if ($aksi === 'tolak') {
            $request->validate([
                'catatan' => 'required|string|max:255',
            ], [
                'catatan.required' => 'Mohon berikan alasan penolakan bukti transfer.',
            ]);

            // Hapus file bukti yang ditolak
            if ($t->BuktiTransfer && !str_starts_with($t->BuktiTransfer, 'http')) {
                $path = public_path('upload/komisi/' . $t->BuktiTransfer);
                if (File::exists($path)) {
                    @File::delete($path);
                }
            }

            $t->BuktiTransfer = null;
            $t->DiunggahPada = null;
            $t->Status = 'Belum Dibayar';
            $t->CatatanAdmin = trim($request->input('catatan'));
            $t->save();

            return back()->with('success', "Bukti transfer tagihan #{$t->TagihanID} ditolak dan status dikembalikan ke Belum Dibayar.");
        }

        return back();
    }
}

