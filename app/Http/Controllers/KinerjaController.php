<?php

namespace App\Http\Controllers;

use App\Models\Kinerja;
use App\Models\Pengurus;
use App\Models\MasterInstrumen;
use App\Models\KinerjaDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KinerjaController extends Controller
{
    // 1. HALAMAN RIWAYAT (INDEX)
    public function index(\Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = \App\Models\Pengurus::with(['kinerja', 'strukturJabatan', 'wilayah', 'daerah']);

        if ($user->isAdmin() || $user->isBiktren()) {
        } elseif ($user->isWilayah()) {
            $query->where('wilayah_id', $user->wilayah_id);
        } elseif ($user->isDaerah()) {
            $query->where('daerah_id', $user->daerah_id);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status_penilaian')) {
            if ($request->status_penilaian == 'sudah') {
                $query->whereHas('kinerja');
            } elseif ($request->status_penilaian == 'belum') {
                $query->doesntHave('kinerja');
            }
        }

        $pengurus = $query->paginate(12)->withQueryString();

        return view('pokok.kinerja.index', compact('pengurus'));
    }

    // 2. HALAMAN INPUT NILAI (CREATE)
    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $selected_id = $request->query('pengurus_id');

        if ($selected_id) {
            $target = Pengurus::find($selected_id);
            if ($target && ($target->id == $user->pengurus_id || $target->niup == $user->niup)) {
                return redirect()->route('pokok.kinerja.index')
                    ->with('error', 'Tidak bisa input nilai diri sendiri, penilaian harus sesuai dengan struktur pesantren.');
            }
            if ($target && $target->status == 'non_aktif') {
                return redirect()->route('pokok.kinerja.index')
                    ->with('error', 'Akses ditolak! Pengurus berstatus Non-Aktif tidak dapat dinilai kinerjanya.');
            }
        }

        $query = Pengurus::query()->where('status', 'aktif');

        if ($user->isAdmin()) {
        } elseif ($user->isBiktren()) {
            $query->whereHas('strukturJabatan', function ($q) {
                $q->where('jabatan', 'like', '%Kepala Wilayah%');
            });
        } elseif ($user->isWilayah()) {
            $query->where('wilayah_id', $user->wilayah_id)
                ->where('id', '!=', $user->pengurus_id) // Tidak bisa menilai diri sendiri
                ->whereHas('strukturJabatan', function ($q) {
                    $q->where('jabatan', 'not like', '%Kepala Wilayah%');
                });
        } elseif ($user->isDaerah()) {
            $query->where('daerah_id', $user->daerah_id)
                ->where('id', '!=', $user->pengurus_id)
                ->whereHas('strukturJabatan', function ($q) {
                    $q->where('jabatan', 'like', '%Daerah%')
                        ->where('jabatan', 'not like', '%Kepala Daerah%');
                });
        }

        $pengurus = $query->get();

        // Validasi Akses via URL
        if ($selected_id && !$pengurus->contains('id', $selected_id)) {
            return redirect()->route('pokok.kinerja.index')
                ->with('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk menilai pengurus ini.');
        }

        $instrumens = MasterInstrumen::where('status', 'aktif')->orderBy('aspek')->orderBy('id')->get();

        return view('pokok.kinerja.create', compact('pengurus', 'selected_id', 'instrumens'));
    }

    // 3. PROSES SIMPAN NILAI
    public function store(Request $request)
    {
        $user = auth()->user();
        $targetPengurus = \App\Models\Pengurus::find($request->pengurus_id);

        if ($targetPengurus && $targetPengurus->niup == $user->niup) {
            return redirect()->route('pokok.kinerja.index')
                ->with('error', 'Gagal Menyimpan: Tidak bisa input nilai diri sendiri, tindakan mencurigakan terdeteksi!');
        }

        $instrumens = MasterInstrumen::where('status', 'aktif')->get();
        $rules = [
            'pengurus_id' => 'required',
            'tanggal_penilaian' => 'required|date',
            'triwulan' => 'required|integer|min:1|max:4',
            'tahun' => 'required|integer|min:2000',
        ];
        
        foreach ($instrumens as $instrumen) {
            $rules['skor_' . $instrumen->id] = 'required|numeric|min:0|max:100';
        }

        $request->validate($rules);

        // Hitung Bobot
        $total = 0;
        foreach ($instrumens as $instrumen) {
            $skorInput = $request->input('skor_' . $instrumen->id);
            $total += ($skorInput * ($instrumen->bobot / 100));
        }

        // Tentukan Predikat
        if ($total >= 90) {
            $huruf = 'A';
            $rekomendasi = 'Apresiasi & kaderisasi';
        } elseif ($total >= 75) {
            $huruf = 'B';
            $rekomendasi = 'Bimbingan ringan';
        } elseif ($total >= 70) {
            $huruf = 'C';
            $rekomendasi = 'Pembinaan sedang';
        } elseif ($total >= 50) {
            $huruf = 'D';
            $rekomendasi = 'Pembinaan intensif';
        } else {
            $huruf = 'E';
            $rekomendasi = 'Penanganan khusus (Merujuk ke SOP)';
        }

        // Simpan ke Database Induk
        $kinerja = Kinerja::create([
            'pengurus_id' => $request->pengurus_id,
            'tanggal_penilaian' => $request->tanggal_penilaian,
            'triwulan' => $request->triwulan,
            'tahun' => $request->tahun,
            'nilai_total' => $total,
            'huruf_mutu' => $huruf,
            'rekomendasi' => $rekomendasi,
            'catatan' => $request->catatan,
            'status_tindak_lanjut' => 'belum',
            'tanggal_tindak_lanjut' => null,
        ]);

        // Simpan Detail Skor
        foreach ($instrumens as $instrumen) {
            $skorInput = $request->input('skor_' . $instrumen->id);
            KinerjaDetail::create([
                'kinerja_id' => $kinerja->id,
                'instrumen_id' => $instrumen->id,
                'skor' => $skorInput,
            ]);
        }

        return redirect()->route('pokok.kinerja.index')->with('success', 'Penilaian Berhasil Disimpan!');
    }

    // 4. HALAMAN RIWAYAT (DETAIL)
    public function show(Request $request, $id)
    {
        $pengurus = Pengurus::with(['kinerja' => function ($q) use ($request) {
            if ($request->filled('triwulan')) {
                $q->where('triwulan', $request->triwulan);
            }
            if ($request->filled('tahun')) {
                $q->where('tahun', $request->tahun);
            }
            $q->with(['kinerjaDetails.instrumen'])->latest();
        }])->findOrFail($id);

        return view('pokok.kinerja.show', compact('pengurus'));
    }

    // 5. PROSES TANDAI SUDAH DITANGANI (PEMBINAAN OFFLINE)
    public function markAsHandled(Request $request, $id)
    {
        $request->validate(['deskripsi_tindak_lanjut' => 'required|string|min:5']);

        $user = auth()->user();
        $kinerja = Kinerja::with(['pengurus'])->findOrFail($id);
        $target = $kinerja->pengurus;

        $bolehUpdate = false;

        if ($user->isAdmin() || $user->isBiktren()) {
            $bolehUpdate = true;
        } elseif ($user->isWilayah()) {
            if ($target->entitas_id == 2 && $target->wilayah_id == $user->wilayah_id) {
                $bolehUpdate = true;
            } else {
                return redirect()->back()->with('error', 'Wewenang Wilayah hanya untuk level Daerah.');
            }
        }

        if ($bolehUpdate) {
            $kinerja->update([
                'status_tindak_lanjut' => 'sudah',
                'deskripsi_tindak_lanjut' => $request->deskripsi_tindak_lanjut,
                'tanggal_tindak_lanjut' => now(),
            ]);
            return redirect()->back()->with('success', 'Berhasil mencatat tindak lanjut.');
        }

        return redirect()->back()->with('error', 'Akses Ditolak.');
    }
    public function exportPdf(Request $request, $id)
    {
        // 1. Ambil data pengurus beserta riwayat kinerjanya, dengan filter
        $pengurus = \App\Models\Pengurus::with(['kinerja' => function($q) use ($request) {
            if ($request->filled('triwulan')) {
                $q->where('triwulan', $request->triwulan);
            }
            if ($request->filled('tahun')) {
                $q->where('tahun', $request->tahun);
            }
            $q->with(['kinerjaDetails.instrumen'])->latest();
        }])->findOrFail($id);

        // 2. Load view khusus PDF (kita buat di langkah 4)
        $pdf = Pdf::loadView('pokok.kinerja.pdf', compact('pengurus'))
            ->setPaper('a4', 'landscape'); // Menggunakan format Landscape agar tabel luas

        // 3. Download filenya
        return $pdf->download('Raport_Kinerja_' . str_replace(' ', '_', $pengurus->nama) . '.pdf');
    }
}
