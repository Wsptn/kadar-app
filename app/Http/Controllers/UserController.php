<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wilayah;
use App\Models\Daerah;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;

class UserController extends Controller
{
    public function export()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isBiktren()) {
            abort(403, 'Anda tidak diizinkan melakukan export data user.');
        }

        return Excel::download(new UserExport, 'Data_Akun_Pengurus_' . date('Ymd_His') . '.xlsx');
    }

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
            $wilayahs = \App\Models\Wilayah::select('nama_wilayah')->distinct()->orderBy('nama_wilayah')->pluck('nama_wilayah');
            $daerahs  = \App\Models\Daerah::select('nama_daerah')->distinct()->orderBy('nama_daerah')->pluck('nama_daerah');

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
            'wilayah'    => 'nullable|required_if:level,Wilayah|string',
            // Validasi Kondisional: Wajib isi daerah jika level Daerah
            'daerah'     => 'nullable|required_if:level,Daerah|string',
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
            // Simpan lokasi jika ada
            'wilayah'    => $request->wilayah ?? null,
            'daerah'     => $request->daerah ?? null,
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

    /**
     * Helper method to generate a readable username from a full name
     */
    private function generateReadableUsername($fullName)
    {
        $nameParts = explode(' ', trim($fullName));
        $skipWords = ['muhammad', 'mohammad', 'moh', 'moch', 'm.', 'm', 'ahmad', 'ustadz', 'ust.', 'ust', 'kh', 'kh.', 'k.', 'nyai', 'lor'];
        
        $username = '';
        foreach ($nameParts as $part) {
            $cleanPart = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $part));
            if (!empty($cleanPart) && !in_array($cleanPart, $skipWords)) {
                $username = $cleanPart;
                break;
            }
        }

        // Fallback jika semua kata ternyata skip word (misal namanya cuma "Muhammad")
        if (empty($username)) {
            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nameParts[0]));
        }

        // Pastikan username unik
        $originalUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generate otomatis akun user berdasarkan Pengurus yang menjabat sebagai Kepala Wilayah & Daerah
     */
    public function generateStruktur()
    {
        $currentUser = Auth::user();

        // Keamanan: Hanya Admin dan Biktren yang boleh menjalankan fitur ini
        if (!$currentUser->isAdmin() && !$currentUser->isBiktren()) {
            abort(403, 'Anda tidak memiliki izin untuk menggenerate akun struktur.');
        }

        // Ambil semua pengurus aktif dengan jabatan Kepala Wilayah atau Kepala Daerah
        $penguruses = Pengurus::where('status', 'aktif')
            ->whereHas('strukturJabatan', function($q) {
                $q->whereIn('jabatan', ['Kepala Wilayah', 'Kepala Daerah']);
            })
            ->with(['kamar.daerah.wilayah', 'strukturJabatan'])
            ->get();

        $countWilayah = 0;
        $countDaerah = 0;

        foreach ($penguruses as $p) {
            $jabatan = $p->strukturJabatan->jabatan;
            $username = $this->generateReadableUsername($p->nama);

            if ($jabatan === 'Kepala Wilayah') {
                $wilayahName = $p->kamar->daerah->wilayah->nama_wilayah ?? null;
                if ($wilayahName) {
                    // Cek apakah Wilayah ini sudah memiliki akun yang aktif
                    if (User::where('level', 'Wilayah')->where('wilayah', $wilayahName)->where('status', 'aktif')->exists()) {
                        continue;
                    }

                    User::create([
                        'name'     => $p->nama, // Menggunakan nama asli
                        'username' => $username,
                        'password' => 'nuruljadid123',
                        'level'    => 'Wilayah',
                        'status'   => 'aktif',
                        'wilayah'  => $wilayahName,
                    ]);
                    $countWilayah++;
                }
            } elseif ($jabatan === 'Kepala Daerah') {
                $daerahName = $p->kamar->daerah->nama_daerah ?? null;
                if ($daerahName) {
                    // Cek apakah Daerah ini sudah memiliki akun yang aktif
                    if (User::where('level', 'Daerah')->where('daerah', $daerahName)->where('status', 'aktif')->exists()) {
                        continue;
                    }

                    User::create([
                        'name'     => $p->nama, // Menggunakan nama asli
                        'username' => $username,
                        'password' => 'nuruljadid123',
                        'level'    => 'Daerah',
                        'status'   => 'aktif',
                        'daerah'   => $daerahName,
                    ]);
                    $countDaerah++;
                }
            }
        }

        return redirect()->route('user.index')
            ->with('success', "Sinkronisasi selesai! Berhasil men-generate {$countWilayah} Akun Wilayah dan {$countDaerah} Akun Daerah baru berdasarkan data Pengurus Aktif.");
    }
}
