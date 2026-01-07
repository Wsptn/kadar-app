<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login (Jika belum ada)
     */
    public function index()
    {
        // Jika sudah login, lempar langsung ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Coba Login (Attempt)
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'username' => 'Username atau password salah',
            ])->withInput($request->only('username')); // Kembalikan input username agar user tidak ngetik ulang
        }

        // 3. Regenerasi Session (Keamanan Fixation)
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 4. 🔐 CEK STATUS USER
        // Jika status 'nonaktif', paksa logout lagi
        if ($user->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['username' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Admin.']);
        }

        // 5. 🔁 REDIRECT KE DASHBOARD
        // Gunakan 'intended' agar user dikembalikan ke halaman yang ingin mereka akses sebelumnya.
        // Jika login biasa, default-nya ke 'dashboard.index'
        return redirect()->intended(route('dashboard.index'));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }
}
