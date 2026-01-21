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
            'wilayah',
            'daerah',
            'kamar',
            'entitas',
            'jabatan',
            'jenisJabatan',
            'gradeJabatan',
            'fungsionalTugas',
            'rangkapInternal',
            'rangkapEksternal',
            'pendidikan',
            'angkatan',
        ])->orderBy('nama');

        // === FILTER BERDASARKAN REQUEST ===

        if ($this->request->filled('wilayah')) {
            $query->where('wilayah_id', $this->request->wilayah);
        }
        if ($this->request->filled('daerah')) {
            $query->where('daerah_id', $this->request->daerah);
        }
        if ($this->request->filled('entitas_daerah')) {
            $query->where('entitas_daerah', $this->request->entitas_daerah);
        }
        if ($this->request->filled('entitas')) {
            $query->where('entitas_id', $this->request->entitas);
        }
        if ($this->request->filled('jabatan')) {
            $query->where('jabatan_id', $this->request->jabatan);
        }
        // Filter Tugas (Mencari orang yang PERNAH punya tugas ini, walau statusnya non-aktif)
        if ($this->request->filled('tugas')) {
            $query->whereHas('fungsionalTugas', function ($q) {
                $q->where('master_fungsional_tugas_id', $this->request->tugas);
            });
        }
        if ($this->request->filled('internal')) {
            $query->where('rangkap_internal_id', $this->request->internal);
        }
        if ($this->request->filled('eksternal')) {
            $query->where('rangkap_eksternal_id', $this->request->eksternal);
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
            $listTugas = $p->fungsionalTugas
                ->filter(function ($tugas) {
                    return $tugas->pivot->status == 'aktif';
                })
                ->pluck('tugas')
                ->join(', ');

            return [
                'niup'                => $p->niup,
                'nama'                => $p->nama,

                'wilayah'             => $p->wilayah->nama_wilayah ?? '',
                'daerah'              => $p->daerah->nama_daerah ?? '',
                'entitas_daerah'      => $p->entitas_daerah ?? '',
                'kamar'               => $p->kamar->nomor_kamar ?? '',
                'entitas'             => $p->entitas->nama_entitas ?? '',
                'jabatan'             => $p->jabatan->nama_jabatan ?? '',
                'jenis_jabatan'       => $p->jenisJabatan->jenis_jabatan ?? '',
                'grade_jabatan'       => $p->gradeJabatan->grade ?? '',
                'sk_kepengurusan'     => $p->sk_kepengurusan,

                // Di sini hanya muncul tugas yang aktif saja
                'fungsional_tugas'    => $listTugas,

                'rangkap_internal'    => $p->rangkapInternal->internal ?? '',
                'rangkap_eksternal'   => $p->rangkapEksternal->eksternal ?? '',
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
            'Rangkap Internal',
            'Rangkap Eksternal',
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
