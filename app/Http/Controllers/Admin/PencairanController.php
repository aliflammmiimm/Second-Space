<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pencairan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pencairan::with('seller:UserID,Nama,NoHP_Email');

        if ($request->filled('status')) {
            $query->where('Status', $request->input('status'));
        }

        $pencairan = $query->latest('PencairanID')->paginate(10)->withQueryString();

        return view('admin.pencairan.index', compact('pencairan'));
    }

    /** Admin sudah mentransfer manual ke rekening seller. */
    public function selesai($id)
    {
        return $this->proses($id, 'Selesai', null);
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['Catatan' => 'required|string|max:255']);

        return $this->proses($id, 'Ditolak', trim($request->input('Catatan')));
    }

    private function proses($id, string $status, ?string $catatan)
    {
        $ok = DB::transaction(function () use ($id, $status, $catatan) {
            $p = Pencairan::where('PencairanID', $id)->lockForUpdate()->firstOrFail();

            if ($p->Status !== 'Menunggu') {
                return false;
            }

            $p->Status = $status;
            $p->Catatan = $catatan;
            $p->DiprosesOleh = Auth::id();
            $p->DiprosesPada = now();
            $p->save();

            return true;
        });

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? "Pencairan #{$id} ditandai {$status}." : 'Pencairan sudah diproses sebelumnya.'
        );
    }
}