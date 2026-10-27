<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function edit()
    {
        return view('pelanggan.profil', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'Nama' => 'required|string|max:100',
            'NoHP_Email' => ['required', 'string', 'max:100', Rule::unique('user', 'NoHP_Email')->ignore($user->UserID, 'UserID')],
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($data['password']) && !Hash::check($data['current_password'], $user->Password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
        }

        $user->Nama = $data['Nama'];
        $user->NoHP_Email = trim($data['NoHP_Email']);
        if (!empty($data['password'])) {
            $user->Password = Hash::make($data['password']);
        }
        $user->save();

        return back()->with('success', 'Profil diperbarui.');
    }
}