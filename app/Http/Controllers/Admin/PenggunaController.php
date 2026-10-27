<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $tipe = $request->input('tipe');

        $users = User::withCount('produk')
            ->when($tipe === 'seller', fn ($q) => $q->where('is_seller', 1))
            ->when($tipe === 'buyer', fn ($q) => $q->where('Role', 'Pelanggan')->where('is_seller', 0))
            ->when($tipe === 'admin', fn ($q) => $q->where('Role', 'Admin'))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('Nama', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('NoHP_Email', 'like', '%' . $request->input('search') . '%');
            }))
            ->orderByDesc('UserID')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pengguna.index', compact('users', 'tipe'));
    }
}