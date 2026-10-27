<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Pencairan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    private const MINIMAL = 10000;

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $riwayat = $user->pencairan()->latest('PencairanID')->paginate(10);

        return view('seller.pencairan.index', [
            'user' => $user,
            'totalPendapatan' => $user->totalPendapatan(),
            'saldo' => $user->saldoTersedia(),
            'minimal' => self::MINIMAL,
            'riwayat' => $riwayat,
        ]);
    }

    public function simpanRekening(Request $request)
    {
        $data = $request->validate([
            'Bank' => 'required|string|max:50',
            'NoRekening' => ['required', 'string', 'max:40', 'regex:/^[0-9\-\s]{5,40}$/'],
            'AtasNama' => 'required|string|max:100',
        ], [
            'NoRekening.regex' => 'Nomor rekening/e-wallet hanya boleh berisi angka.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->Bank = trim($data['Bank']);
        $user->NoRekening = trim($data['NoRekening']);
        $user->AtasNama = trim($data['AtasNama']);
        $user->save();

        return back()->with('success', 'Rekening pencairan disimpan.');
    }

    public function store(Request $request)
    {
        $request->validate(['Jumlah' => 'required|integer|min:' . self::MINIMAL]);

        $jumlah = (int) $request->input('Jumlah');

        $pesan = DB::transaction(function () use ($jumlah) {
            // Kunci baris seller agar dua permintaan bersamaan tidak melebihi saldo
            /** @var User $user */
            $user = User::where('UserID', Auth::id())->lockForUpdate()->firstOrFail();

            if (!$user->Bank || !$user->NoRekening || !$user->AtasNama) {
                return ['error', 'Lengkapi rekening pencairan terlebih dahulu.'];
            }

            if ($user->pencairan()->where('Status', 'Menunggu')->exists()) {
                return ['error', 'Masih ada permintaan pencairan yang menunggu diproses admin.'];
            }

            if ($jumlah > $user->saldoTersedia()) {
                return ['error', 'Jumlah melebihi saldo yang tersedia.'];
            }

            Pencairan::create([
                'SellerID' => $user->UserID,
                'Jumlah' => $jumlah,
                'Bank' => $user->Bank,
                'NoRekening' => $user->NoRekening,
                'AtasNama' => $user->AtasNama,
            ]);

            return ['success', 'Permintaan pencairan terkirim. Admin akan mentransfer ke rekening Anda.'];
        });

        return back()->with($pesan[0], $pesan[1]);
    }
}