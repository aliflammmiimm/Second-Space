<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PengajuanSeller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login publik untuk Pelanggan & Seller.
     */
    public function showLogin()
    {
        // Hanya redirect jika kedua guard pembeli dan penjual sudah aktif di sesi ini
        if (Auth::guard('web')->check() && Auth::guard('seller')->check()) {
            return redirect()->route('pelanggan.katalog');
        }

        return view('auth.login');
    }

    /**
     * Proses login untuk Pelanggan atau Seller dengan pemilihan radio button.
     */
    public function login(Request $request)
    {
        $request->validate([
            'role' => 'required|in:pelanggan,seller',
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'role.required' => 'Pilih peran login Anda (Pelanggan atau Seller).',
            'role.in' => 'Pilihan peran tidak valid.',
            'email.required' => 'Nomor HP atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $role = $request->input('role');
        $identifier = trim($request->input('email'));
        $password = $request->input('password');

        $user = User::where('NoHP_Email', $identifier)->first();

        // Verifikasi kecocokan password
        $passwordMatches = $user && (
            Hash::check($password, $user->Password) || 
            password_verify($password, $user->Password)
        );

        if (!$user || !$passwordMatches) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->with('error', 'Login gagal. Nomor HP / Email atau password salah.');
        }

        // Jika akun adalah Admin, tolak dan arahkan ke portal admin
        if ($user->isAdmin()) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->with('error', 'Akun Administrator tidak dapat masuk melalui halaman ini. Silakan akses portal khusus di /admin/login.');
        }

        // Proses autentikasi sesuai peran yang dipilih pada radio button
        if ($role === 'seller') {
            if (!$user->isSeller()) {
                return back()
                    ->withInput($request->only('email', 'role'))
                    ->with('error', 'Akun ini belum disetujui sebagai Seller. Silakan pilih login sebagai "Pelanggan" untuk masuk atau memeriksa status pengajuan Seller Anda.');
            }

            Auth::guard('seller')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->route('seller.produk.index')
                ->with('success', 'Selamat datang di Toko Anda, ' . $user->Nama . '!');
        }

        // Peran Pelanggan (Buyer)
        Auth::guard('web')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('pelanggan.katalog')
            ->with('success', 'Selamat datang kembali, ' . $user->Nama . '!');
    }

    public function showRegister()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('pelanggan.katalog');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|max:100|unique:user,NoHP_Email',
            'password' => 'required|string|min:8',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Nomor HP atau Email wajib diisi.',
            'email.unique' => 'Nomor HP atau Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'Nama' => $request->input('nama'),
            'NoHP_Email' => trim($request->input('email')),
            'Password' => Hash::make($request->input('password')),
            'Role' => 'Pelanggan',
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('pelanggan.katalog')
            ->with('success', 'Registrasi berhasil! Selamat datang di SecondSpace.');
    }

    public function showRegisterSeller()
    {
        if (Auth::guard('seller')->check()) {
            return redirect()->route('seller.produk.index');
        }

        return view('auth.register_seller');
    }

    public function registerSeller(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|max:100|unique:user,NoHP_Email',
            'password' => 'required|string|min:8',
            'NoHP' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'Alamat' => 'required|string|max:150',
            'Kota' => 'required|string|max:60',
            'Provinsi' => 'required|string|max:60',
            'Alasan' => 'required|string|min:10|max:1000',
            'setuju' => 'accepted',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Nomor HP atau Email wajib diisi.',
            'email.unique' => 'Nomor HP atau Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'NoHP.required' => 'Nomor WhatsApp wajib diisi.',
            'NoHP.regex' => 'Nomor WhatsApp tidak valid.',
            'Alamat.required' => 'Alamat domisili wajib diisi.',
            'Kota.required' => 'Kota/Kabupaten wajib diisi.',
            'Provinsi.required' => 'Provinsi wajib diisi.',
            'Alasan.required' => 'Alasan wajib diisi.',
            'Alasan.min' => 'Alasan minimal 10 karakter.',
            'setuju.accepted' => 'Anda harus menyetujui pernyataan sebelum mendaftar.',
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'Nama' => trim($request->input('nama')),
                'NoHP_Email' => trim($request->input('email')),
                'Password' => Hash::make($request->input('password')),
                'Role' => 'Pelanggan',
            ]);

            PengajuanSeller::create([
                'UserID' => $user->UserID,
                'Nama' => $user->Nama,
                'NoHP' => trim($request->input('NoHP')),
                'Alamat' => trim($request->input('Alamat')) . ', ' . trim($request->input('Kota')) . ', ' . trim($request->input('Provinsi')),
                'Alasan' => $request->input('Alasan'),
            ]);

            return $user;
        });

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('pelanggan.seller.status')
            ->with('success', 'Akun dibuat dan pengajuan Seller terkirim. Mohon tunggu persetujuan Admin.');
    }

    /**
     * Logout untuk guard Pelanggan.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Logout untuk guard Seller.
     */
    public function logoutSeller(Request $request)
    {
        Auth::guard('seller')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Seller telah berhasil logout.');
    }
}