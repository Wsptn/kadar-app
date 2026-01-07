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

        // === FILTER ===

        // 1. Wilayah
        if ($this->request->filled('wilayah')) {
            $query->where('wilayah_id', $this->request->wilayah);
        }

        // 2. Daerah
        if ($this->request->filled('daerah')) {
            $query->where('daerah_id', $this->request->daerah);
        }

        // 3. Entitas Daerah
        if ($this->request->filled('entitas_daerah')) {
            $query->where('entitas_daerah', $this->request->entitas_daerah);
        }

        // 4. Entitas
        if ($this->request->filled('entitas')) {
            $query->where('entitas_id', $this->request->entitas);
        }

        // 5. Jabatan
        if ($this->request->filled('jabatan')) {
            $query->where('jabatan_id', $this->request->jabatan);
        }

        // 6. Fungsional Tugas
        if ($this->request->filled('tugas')) {
            $query->where('fungsional_tugas_id', $this->request->tugas);
        }

        // 7. Tugas Internal
        if ($this->request->filled('internal')) {
            $query->where('rangkap_internal_id', $this->request->internal);
        }

        // 8. Tugas Eksternal
        if ($this->request->filled('eksternal')) {
            $query->where('rangkap_eksternal_id', $this->request->eksternal);
        }

        // 9. Pendidikan
        if ($this->request->filled('pendidikan')) {
            $query->where('pendidikan_id', $this->request->pendidikan);
        }

        // 10. Angkatan
        if ($this->request->filled('angkatan')) {
            $query->where('angkatan_id', $this->request->angkatan);
        }

        // 11. Status
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        // 12. Search
        if ($this->request->filled('search')) {
            $s = $this->request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%$s%")
                    ->orWhere('niup', 'like', "%$s%");
            });
        }

        $data = $query->get();

        return $data->map(function ($p) {

            // URL file jika ada
            $file = function ($path) {
                return $path ? url('storage/' . $path) : null;
            };

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

                'fungsional_tugas'    => $p->fungsionalTugas->tugas ?? '',
                'rangkap_internal'    => $p->rangkapInternal->internal ?? '',
                'rangkap_eksternal'   => $p->rangkapEksternal->eksternal ?? '',
                'status'              => $p->status,
                'pendidikan'          => $p->pendidikan->nama_pendidikan ?? '',
                'angkatan'            => $p->angkatan->angkatan ?? '',

                // FILE → URL
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
            'Status',
            'Pendidikan',
            'Angkatan',
            'Berkas SK Pengurus',
            'Berkas Surat Tugas',
            'Berkas PLT',
            'Berkas Lain',
        ];
    }
}
