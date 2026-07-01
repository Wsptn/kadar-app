<?php

namespace App\Imports;

use App\Models\Wilayah;
use App\Models\Daerah;
use App\Models\Kamar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DomisiliImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public $successRows = 0;

    public function model(array $row)
    {
        // Pastikan kolom mandatory ada
        if (empty($row['wilayah']) || empty($row['daerah']) || empty($row['kamar'])) {
            return null;
        }

        $namaWilayah = trim($row['wilayah']);
        $namaDaerah = trim($row['daerah']);
        $entitasDaerah = trim($row['entitas_daerah'] ?? '');
        $namaKamar = trim($row['kamar']);

        // 1. Wilayah
        $wilayah = Wilayah::firstOrCreate(['nama_wilayah' => $namaWilayah]);

        // 2. Daerah
        $daerah = Daerah::where('nama_daerah', $namaDaerah)
            ->where('wilayah_id', $wilayah->id)
            ->first();

        if (!$daerah) {
            $daerah = Daerah::create([
                'nama_daerah' => $namaDaerah,
                'entitas_daerah' => $entitasDaerah ?: null,
                'wilayah_id' => $wilayah->id
            ]);
        } elseif ($entitasDaerah && !$daerah->entitas_daerah) {
            // Update entitas_daerah if it was empty
            $daerah->update(['entitas_daerah' => $entitasDaerah]);
        }

        // 3. Kamar
        $kamar = Kamar::where('nomor_kamar', $namaKamar)
            ->where('daerah_id', $daerah->id)
            ->first();

        if (!$kamar) {
            Kamar::create([
                'nomor_kamar' => $namaKamar,
                'daerah_id' => $daerah->id
            ]);
            $this->successRows++;
        }

        // Return null karena kita handle insert manual untuk relasi (Kamar, Daerah, Wilayah)
        // Maatwebsite ToModel biasanya return model tunggal, tapi karena kita insert relasi kompleks,
        // kita lakukan manual di atas dan return null.
        return null; 
    }
}
