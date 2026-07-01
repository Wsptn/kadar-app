<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class UserExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        $user = Auth::user();
        $query = User::query();

        if ($user->isBiktren()) {
            $query->whereIn('level', ['Wilayah', 'Daerah']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Username',
            'Level/Role',
            'Wilayah',
            'Daerah',
            'Status',
            'Password Default (Khusus Auto Generate)'
        ];
    }

    public function map($user): array
    {
        static $no = 1;
        return [
            $no++,
            $user->name,
            $user->username,
            $user->level,
            $user->wilayah ?? '-',
            $user->daerah ?? '-',
            $user->status,
            'nuruljadid123'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
