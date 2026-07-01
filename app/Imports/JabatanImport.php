<?php

namespace App\Imports;

use App\Models\MasterStrukturJabatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class JabatanImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public $successRows = 0;

    public function model(array $row)
    {
        // Pastikan kolom mandatory ada
        if (empty($row['entitas']) || empty($row['jabatan']) || empty($row['jenis_jabatan']) || empty($row['grade'])) {
            return null;
        }

        $entitas = trim($row['entitas']);
        $jabatan = trim($row['jabatan']);
        $jenisJabatan = trim($row['jenis_jabatan']);
        $grade = trim($row['grade']);

        // Cek apakah jabatan dengan entitas yang sama sudah ada
        $existing = MasterStrukturJabatan::where('entitas', $entitas)
            ->where('jabatan', $jabatan)
            ->first();

        if (!$existing) {
            $this->successRows++;
            return new MasterStrukturJabatan([
                'entitas' => $entitas,
                'jabatan' => $jabatan,
                'jenis_jabatan' => $jenisJabatan,
                'grade' => $grade,
            ]);
        }

        return null;
    }
}
