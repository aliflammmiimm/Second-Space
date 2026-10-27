<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSeller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanSellerController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $pengajuan = PengajuanSeller::with('user')
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($q) => $q->where('Status', $status))
            ->latest('PengajuanID')
            ->paginate(15)
            ->withQueryString();

        return view('admin.seller.pengajuan', compact('pengajuan', 'status'));
    }

    public function approve($id)
    {
        $ok = DB::transaction(function () use ($id) {
            $p = PengajuanSeller::where('PengajuanID', $id)->lockForUpdate()->firstOrFail();
            if ($p->Status !== 'pending') {
                return false;
            }
            $p->Status = 'approved';
            $p->AlasanPenolakan = null;
            $p->ReviewedBy = Auth::id();
            $p->save();

            User::where('UserID', $p->UserID)->update(['is_seller' => 1]);
            return true;
        });

        return back()->with($ok ? 'success' : 'error', $ok ? 'Pengajuan disetujui.' : 'Pengajuan sudah diproses sebelumnya.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['AlasanPenolakan' => 'required|string|max:500']);

        $ok = DB::transaction(function () use ($request, $id) {
            $p = PengajuanSeller::where('PengajuanID', $id)->lockForUpdate()->firstOrFail();
            if ($p->Status !== 'pending') {
                return false;
            }
            $p->Status = 'rejected';
            $p->AlasanPenolakan = $request->input('AlasanPenolakan');
            $p->ReviewedBy = Auth::id();
            $p->save();
            return true;
        });

        return back()->with($ok ? 'success' : 'error', $ok ? 'Pengajuan ditolak.' : 'Pengajuan sudah diproses sebelumnya.');
    }
}