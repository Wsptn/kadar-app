<?php

namespace App\Imports;

use App\Models\Pengurus;
use App\Models\Kamar;
use App\Models\MasterStrukturJabatan;
use App\Models\Pendidikan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;

class PengurusImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;
    private $kamars;
    private $jabatans;
    private $pendidikans;

    public $failedRows = 0;
    public $successRows = 0;

    public function __construct()
    {
        // Cache master data for fast lookup (case-insensitive)
        $this->kamars = Kamar::all()->mapWithKeys(function($item) {
            return [strtolower(trim($item->nomor_kamar)) => $item->id];
        })->toArray();
        
        $this->jabatans = MasterStrukturJabatan::all()->mapWithKeys(function($item) {
            return [strtolower(trim($item->jabatan)) => $item->id];
        })->toArray();
        
        $this->pendidikans = Pendidikan::all()->mapWithKeys(function($item) {
            return [strtolower(trim($item->jenjang)) => $item->id];
        })->toArray();
    }

    public function model(array $row)
    {
        // Jika kolom wajib kosong, skip
        if (empty($row['niup']) || empty($row['nama_lengkap'])) {
            return null;
        }

        // Lookup IDs based on string from Excel (case-insensitive)
        $namaKamar = strtolower(trim($row['nama_kamar'] ?? ''));
        $namaJabatan = strtolower(trim($row['nama_jabatan'] ?? ''));
        $namaPendidikan = strtolower(trim($row['pendidikan_terakhir'] ?? ''));

        $kamarId = $this->kamars[$namaKamar] ?? null;
        $jabatanId = $this->jabatans[$namaJabatan] ?? null;
        $pendidikanId = $this->pendidikans[$namaPendidikan] ?? null;

        // Skip row if essential master data is not found (agar tidak error constraint)
        if (!$kamarId || !$jabatanId) {
            $this->failedRows++;
            return null; 
        }

        $this->successRows++;

        return new Pengurus([
            'niup'             => $row['niup'],
            'nama'             => $row['nama_lengkap'],
            'kamar_id'         => $kamarId,
            'entitas_daerah'   => $row['entitas_daerah'] ?? 'Daerah', // Default entitas jika kosong
            'jabatan_id'       => $jabatanId,
            'sk_kepengurusan'  => $row['sk_kepengurusan'] ?? '-',
            'status'           => 'aktif',
            'pendidikan_id'    => $pendidikanId,
            'tgl_mulai_tugas'  => $this->parseDate($row['tanggal_mulai_tugas'] ?? null),
        ]);
    }

    public function rules(): array
    {
        return [
            'niup' => ['required', 'unique:pengurus,niup'],
            'nama_lengkap' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'niup.unique' => 'NIUP sudah terdaftar di sistem.',
            'niup.required' => 'NIUP wajib diisi.',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
        ];
    }

    private function parseDate($value)
    {
        if (empty($value)) return date('Y-m-d');
        
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return date('Y-m-d');
            }
        }
        
        return date('Y-m-d', strtotime($value));
    }
}
