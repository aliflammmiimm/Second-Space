<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use Illuminate\Http\Request;

class PengirimanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengiriman::with(['pesanan.user']);

        if ($request->filled('status')) {
            $query->where('StatusPengiriman', $request->input('status'));
        }

        $pengiriman = $query->latest('PengirimanID')->paginate(10)->withQueryString();

        return view('admin.pengiriman.index', compact('pengiriman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pengiriman = Pengiriman::findOrFail($id);

        $request->validate([
            'StatusPengiriman' => 'required|in:Dikemas,Dikirim,Sampai Tujuan',
            'NoResi' => 'nullable|string|max:100',
        ]);

        $pengiriman->StatusPengiriman = $request->input('StatusPengiriman');
        if ($request->filled('NoResi')) {
            $pengiriman->NoResi = trim($request->input('NoResi'));
        }
        $pengiriman->TanggalUpdate = now();
        $pengiriman->save();

        return back()->with('success', "Status pengiriman #{$pengiriman->PengirimanID} berhasil diperbarui.");
    }
}
