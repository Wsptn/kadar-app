<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wilayah;
use App\Models\Daerah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        // Mulai Query Dasar
        $query = User::query();

        // LOGIKA PENCARIAN
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        // LOGIKA HAK AKSES DATA (Admin vs Biktren)
        if ($user->isAdmin()) {
            // Admin bisa melihat semua data (tidak ada filter tambahan)
        } elseif ($user->isBiktren()) {
            // Biktren hanya bisa melihat user level 'Wilayah' dan 'Daerah'
            $query->whereIn('level', ['Wilayah', 'Daerah']);
        } else {
            abort(403, 'Anda tidak memiliki akses ke halaman manajemen user.');
        }

        // Eksekusi Query dengan Pagination
        $users = $query->latest()
            ->paginate(10)
            ->appends($request->all());

        return view('user.index', compact('users'));
    }

    /**
     * Form tambah user
     */
    public function create()
    {
        $user = Auth::user();

        // Hanya Admin dan Biktren yang boleh akses form ini
        if ($user->isAdmin() || $user->isBiktren()) {
            // KIRIM DATA WILAYAH & DAERAH UNTUK DROPDOWN
            $wilayahs = Wilayah::all();
            $daerahs  = Daerah::all();

            return view('user.create', compact('wilayahs', 'daerahs'));
        }

        abort(403, 'Anda tidak memiliki akses untuk menambahkan user');
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:100|unique:users,username',
            'password'   => 'required|string|min:4',
            'level'      => 'required|in:Admin,Biktren,Wilayah,Daerah',
            'aktif'      => 'nullable',
            // Validasi Kondisional: Wajib isi wilayah jika level Wilayah
            'wilayah_id' => 'nullable|required_if:level,Wilayah|exists:wilayahs,id',
            // Validasi Kondisional: Wajib isi daerah jika level Daerah
            'daerah_id'  => 'nullable|required_if:level,Daerah|exists:daerahs,id',
        ]);

        $currentUser = Auth::user();

        // 2. LOGIC PENGAWASAN ROLE
        if ($currentUser->isBiktren()) {
            if (in_array($validated['level'], ['Admin', 'Biktren'])) {
                abort(403, 'Biktren hanya diizinkan membuat akun level Wilayah atau Daerah.');
            }
        }

        // Mencegah akses langsung via Postman/API oleh user yang tidak berhak
        if (!$currentUser->isAdmin() && !$currentUser->isBiktren()) {
            abort(403, 'Anda tidak memiliki izin membuat user.');
        }

        // 3. Simpan ke Database
        User::create([
            'name'       => $validated['name'],
            'username'   => $validated['username'],
            'password'   => $validated['password'], // Password otomatis di-hash oleh Model (casts)
            'level'      => $validated['level'],
            'status'     => $request->has('aktif') ? 'aktif' : 'nonaktif',
            // Simpan ID lokasi jika ada
            'wilayah_id' => $request->wilayah_id ?? null,
            'daerah_id'  => $request->daerah_id ?? null,
        ]);

        return redirect()->route('user.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Toggle status user aktif/nonaktif
     */
    public function toggleStatus(User $user)
    {
        $currentUser = Auth::user();

        // Keamanan: Hanya Admin yang boleh
        if (!$currentUser->isAdmin()) {
            abort(403, 'Hanya Admin Utama yang berhak mengubah status aktif user.');
        }

        // Keamanan: Admin tidak bisa menonaktifkan diri sendiri
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        // Toggle Status
        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        $statusMsg = $user->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun user berhasil {$statusMsg}.");
    }
    public function resetPasswordForm($id)
    {
        $currentUser = Auth::user();

        // Cek Hak Akses
        if (!$currentUser->isAdmin() && !$currentUser->isBiktren()) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        $user = User::findOrFail($id);

        return view('user.reset-password', compact('user'));
    }

    /**
     * PROSES SIMPAN PASSWORD BARU (PUT)
     */
    public function processResetPassword(Request $request, $id)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin() && !$currentUser->isBiktren()) {
            abort(403);
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $userToReset = User::findOrFail($id);

        $userToReset->update([
            'password' => $request->password,
        ]);
        return redirect()->route('user.index')
            ->with('success', 'Password untuk pengguna "' . $userToReset->name . '" berhasil diubah.');
    }
}
