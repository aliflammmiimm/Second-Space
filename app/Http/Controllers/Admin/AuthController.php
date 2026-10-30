<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login khusus Administrator.
     */
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Proses autentikasi khusus Administrator.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Nomor HP atau Email admin wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

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
                ->withInput($request->only('email'))
                ->with('error', 'Login gagal. Kredensial yang dimasukkan tidak cocok.');
        }

        // Pastikan hanya role Admin yang diizinkan masuk portal ini
        if (!$user->isAdmin()) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Akses ditolak. Akun ini bukan Administrator SecondSpace.');
        }

        // Login ke guard admin secara terisolasi
        Auth::guard('admin')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Selamat datang kembali di Panel Admin, ' . $user->Nama . '!');
    }

    /**
     * Logout khusus guard admin.
     * Tidak mematikan sesi guard seller atau web di tab lain.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda telah berhasil keluar dari Panel Admin.');
    }
}