<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index'); // Sesuaikan nama file view Anda
    }

    // Update Nama dan Foto
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // Max 4MB
        ]);

        // Update Nama
        $user->name = $request->name;

        // Update Foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika bukan default/kosong
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Simpan foto baru
            $path = $request->file('foto')->store('uploads/users', 'public');
            $user->foto = $path;
        }

        $user->save();

        return back()->with('success', 'Profil dan foto berhasil diperbarui.');
    }

    // Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // 'confirmed' akan mengecek field new_password_confirmation
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Cek Password Lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        // 2. Simpan Password Baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
