<?php

namespace App\Exports;

use App\Models\Pengurus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class PengurusExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // 1. QUERY UTAMA (Tampilkan Semua Status Pengurus)
        $query = Pengurus::with([
            'domisili',
            'strukturJabatan',
            'fungsionalTugas',
            'internalTugas',
            'eksternalTugas',
            'pendidikan',
            'angkatan',
        ])->orderBy('nama');

        // === FILTER BERDASARKAN REQUEST ===

        if ($this->request->filled('wilayah')) {
            $query->whereHas('domisili', function($q) { $q->where('wilayah', $this->request->wilayah); });
        }
        if ($this->request->filled('daerah')) {
            $query->whereHas('domisili', function($q) { $q->where('daerah', $this->request->daerah); });
        }
        if ($this->request->filled('entitas_daerah_id')) {
            $query->where('entitas_daerah', $this->request->entitas_daerah_id);
        }
        if ($this->request->filled('entitas')) {
            $query->whereHas('strukturJabatan', function($q) {
                $q->where('entitas', $this->request->entitas);
            });
        }
        if ($this->request->filled('jabatan')) {
            $query->whereHas('strukturJabatan', function($q) {
                $q->where('jabatan', $this->request->jabatan);
            });
        }

        // Filter Tugas
        if ($this->request->filled('jenis_tugas')) {
            $query->whereHas('tugas', function ($q) {
                $q->where('jenis_tugas', $this->request->jenis_tugas);
            });
        }
        if ($this->request->filled('tugas')) {
            $query->whereHas('fungsionalTugas', function ($q) {
                $q->where('master_tugas.id_tugas', $this->request->tugas);
            });
        }
        if ($this->request->filled('internal')) {
            $query->whereHas('internalTugas', function ($q) {
                $q->where('master_tugas.id_tugas', $this->request->internal);
            });
        }
        if ($this->request->filled('eksternal')) {
            $query->whereHas('eksternalTugas', function ($q) {
                $q->where('master_tugas.id_tugas', $this->request->eksternal);
            });
        }

        if ($this->request->filled('pendidikan')) {
            $query->where('pendidikan_id', $this->request->pendidikan);
        }
        if ($this->request->filled('angkatan')) {
            $query->where('angkatan_id', $this->request->angkatan);
        }

        // Filter Status Pengurus (Hanya jika user memilih di dropdown)
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('search')) {
            $s = $this->request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%$s%")
                    ->orWhere('niup', 'like', "%$s%");
            });
        }

        $data = $query->get();

        // === MAPPING DATA ===
        return $data->map(function ($p) {

            // Helper URL file
            $file = function ($path) {
                return $path ? url('storage/' . $path) : null;
            };

            // LOGIKA UTAMA: Hanya ambil tugas yang status PIVOT-nya 'aktif'
            // Walaupun pengurusnya non-aktif, kita cek history tugasnya yg statusnya aktif (jika ada)
            $fungsionalAktif = $p->fungsionalTugas
                ->filter(fn ($tugas) => $tugas->pivot->status == 'aktif')
                ->pluck('nama_tugas')
                ->join(', ');

            $internalAktif = $p->internalTugas
                ->filter(fn ($tugas) => $tugas->pivot->status == 'aktif')
                ->pluck('nama_tugas')
                ->join(', ');

            $eksternalAktif = $p->eksternalTugas
                ->filter(fn ($tugas) => $tugas->pivot->status == 'aktif')
                ->pluck('nama_tugas')
                ->join(', ');

            return [
                'niup'                => $p->niup,
                'nama'                => $p->nama,

                'wilayah'             => $p->domisili->wilayah ?? '',
                'daerah'              => $p->domisili->daerah ?? '',
                'entitas_daerah'      => $p->entitas_daerah ?? '',
                'kamar'               => $p->domisili->kamar ?? '',
                'entitas'             => $p->strukturJabatan->entitas ?? '',
                'jabatan'             => $p->strukturJabatan->jabatan ?? '',
                'jenis_jabatan'       => $p->strukturJabatan->jenis_jabatan ?? '',
                'grade_jabatan'       => $p->strukturJabatan->grade ?? '',
                'sk_kepengurusan'     => $p->sk_kepengurusan,

                // Di sini hanya muncul tugas yang aktif saja
                'fungsional_tugas'    => $fungsionalAktif,
                'tugas_internal'      => $internalAktif,
                'tugas_eksternal'     => $eksternalAktif,
                
                'status'              => $p->status,
                'pendidikan'          => $p->pendidikan->nama_pendidikan ?? '',
                'angkatan'            => $p->angkatan->angkatan ?? '',

                // FILE
                'berkas_sk_pengurus'  => $file($p->berkas_sk_pengurus),
                'berkas_surat_tugas'  => $file($p->berkas_surat_tugas),
                'berkas_plt'          => $file($p->berkas_plt),
                'berkas_lain'         => $file($p->berkas_lain),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NIUP',
            'Nama',
            'Wilayah',
            'Daerah',
            'Entitas Daerah',
            'Kamar',
            'Entitas Pengurus',
            'Jabatan',
            'Jenis Jabatan',
            'Grade Jabatan',
            'SK Kepengurusan',
            'Fungsional Tugas',
            'Tugas Internal',
            'Tugas Eksternal',
            'Status Pengurus',
            'Pendidikan',
            'Angkatan',
            'Berkas SK Pengurus',
            'Berkas Surat Tugas',
            'Berkas PLT',
            'Berkas Lain',
        ];
    }
}
