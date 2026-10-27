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
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Nomor HP atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = trim($request->input('email'));
        $password = $request->input('password');

        // Login hanya lewat NoHP_Email (kolom ini unik; Nama tidak unik)
        $user = User::where('NoHP_Email', $identifier)->first();

        if ($user && (Hash::check($password, $user->Password) || password_verify($password, $user->Password))) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return $this->redirectByRole($user)->with('success', 'Selamat datang kembali, ' . $user->Nama . '!');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Login gagal. Nomor HP / Email atau password salah.');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
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

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('pelanggan.katalog')
            ->with('success', 'Registrasi berhasil! Selamat datang di SecondSpace.');
    }
        public function showRegisterSeller()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
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

        // Akun dibuat sebagai Pelanggan; hak Seller baru aktif setelah Admin menyetujui.
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

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('pelanggan.seller.status')
            ->with('success', 'Akun dibuat dan pengajuan Seller terkirim. Mohon tunggu persetujuan Admin.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }

    protected function redirectByRole($user)
    {
        $role = strtolower($user->Role ?? '');

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isSeller()) {
            return redirect()->route('seller.produk.index');
        } else {
            return redirect()->route('pelanggan.katalog');
        }
    }
}
