<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    private function currentUser()
    {
        $wantsSeller = request()->routeIs('seller.*') || request()->is('seller*') || request()->input('as') === 'seller';
        if ($wantsSeller) {
            return Auth::guard('seller')->user() ?? Auth::guard('web')->user();
        }
        return Auth::guard('web')->user() ?? Auth::guard('seller')->user();
    }

    public function index(Request $request)
    {
        $user = $this->currentUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $isSeller = request()->routeIs('seller.*');
        $filter = $request->query('filter', 'semua');

        $query = Notifikasi::where('UserID', $user->UserID)->orderByDesc('DibuatPada');
        if ($filter === 'belum_dibaca') {
            $query->whereNull('DibacaPada');
        }

        $daftar = $query->paginate(15);
        $unreadCount = (int) Notifikasi::where('UserID', $user->UserID)->whereNull('DibacaPada')->count();

        $layout = $isSeller ? 'layouts.seller' : 'layouts.app';
        $section = $isSeller ? 'seller_content' : 'content';
        $rp = $isSeller ? 'seller.notifikasi' : 'pelanggan.notifikasi';

        return view('notifikasi.index', compact('daftar', 'filter', 'unreadCount', 'layout', 'section', 'rp', 'isSeller'));
    }

    /** Dipanggil AJAX oleh navbar untuk update badge & preview dropdown */
    public function feed(Request $request)
    {
        $user = $this->currentUser();
        if (!$user) {
            return response()->json(['unread' => 0, 'items' => []]);
        }

        $unreadCount = (int) Notifikasi::where('UserID', $user->UserID)->whereNull('DibacaPada')->count();
        $items = Notifikasi::where('UserID', $user->UserID)
            ->orderByDesc('DibuatPada')
            ->limit(5)
            ->get()
            ->map(function ($n) {
                $waktu = \Carbon\Carbon::parse($n->DibuatPada)->timezone('Asia/Makassar');
                return [
                    'id' => $n->NotifikasiID,
                    'judul' => $n->Judul,
                    'pesan' => $n->Pesan,
                    'icon' => $n->Icon ?: 'bi-bell-fill',
                    'icon_color' => $n->IconColor ?: '#00B8BE',
                    'url' => route('notifikasi.baca', $n->NotifikasiID),
                    'is_read' => !is_null($n->DibacaPada),
                    'waktu' => $waktu->diffForHumans(),
                ];
            });

        return response()->json([
            'unread' => $unreadCount,
            'items' => $items,
        ]);
    }

    /** Klik notifikasi: tandai dibaca lalu redirect ke tujuan */
    public function baca(Request $request, $id)
    {
        $user = $this->currentUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $notif = Notifikasi::where('NotifikasiID', (int) $id)
            ->where('UserID', $user->UserID)
            ->firstOrFail();

        if (is_null($notif->DibacaPada)) {
            $notif->update(['DibacaPada' => now('Asia/Makassar')]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect($notif->Url ?: route(request()->routeIs('seller.*') ? 'seller.notifikasi' : 'pelanggan.notifikasi'));
    }

    /** Tandai semua notifikasi pengguna sebagai dibaca */
    public function tandaiSemuaDibaca(Request $request)
    {
        $user = $this->currentUser();
        if (!$user) {
            return response()->json(['ok' => false], 401);
        }

        Notifikasi::where('UserID', $user->UserID)
            ->whereNull('DibacaPada')
            ->update(['DibacaPada' => now('Asia/Makassar')]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
