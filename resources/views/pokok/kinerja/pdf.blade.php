<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Raport Kinerja - {{ $pengurus->nama }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 9px;
            /* Ukuran font dikecilkan agar tabel muat */
            color: #333;
            margin: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            color: #28a745;
            font-size: 16px;
        }

        .header p {
            margin: 4px 0 0 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #999;
            padding: 5px;
            vertical-align: middle;
            text-align: center;
        }

        table th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }

        .bg-light {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-start {
            text-align: left;
        }

        .text-success {
            color: #28a745;
            font-weight: bold;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .info-box {
            margin-top: 5px;
            padding: 4px;
            background-color: #e9ecef;
            border-left: 3px solid #6c757d;
            text-align: left;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>RAPORT RIWAYAT KINERJA PENGURUS</h2>
        <p>Nama: <strong>{{ $pengurus->nama }}</strong> | NIUP: <strong>{{ $pengurus->niup ?? '-' }}</strong></p>
        @if(request('triwulan') || request('tahun'))
            <p style="color: #666; font-size: 11px;">
                Filter: 
                {{ request('triwulan') ? 'Triwulan ' . request('triwulan') : 'Semua Triwulan' }} 
                | 
                {{ request('tahun') ? 'Tahun ' . request('tahun') : 'Semua Tahun' }}
            </p>
        @endif
    </div>

    @forelse($pengurus->kinerja as $index => $k)
        <div
            style="background-color: #28a745; color: white; padding: 4px 8px; margin-top: 15px; font-weight: bold; font-size: 10px;">
            Penilaian ke-{{ $index + 1 }} 
            @if($k->triwulan && $k->tahun)
                | Periode: Triwulan {{ $k->triwulan }} - {{ $k->tahun }}
            @endif
            | Tanggal: {{ \Carbon\Carbon::parse($k->tanggal_penilaian)->translatedFormat('d F Y') }}
        </div>
        
        <div style="padding: 5px; background-color: #e9ecef; border: 1px solid #999; border-top: none; font-size: 10px;">
            <strong>Dievaluasi pada kapasitas sebagai:</strong>
            @if($k->jabatan_id && $k->riwayatJabatan && $k->riwayatJabatan->strukturJabatan)
                {{ $k->riwayatJabatan->strukturJabatan->jabatan }} ({{ $k->riwayatJabatan->strukturJabatan->entitas }})
            @elseif($k->tugas_id && $k->riwayatTugas && $k->riwayatTugas->masterTugas)
                {{ $k->riwayatTugas->masterTugas->nama_tugas }} ({{ ucfirst($k->riwayatTugas->masterTugas->jenis_tugas ?? 'Tugas') }})
            @else
                <span style="color: #666; font-style: italic;">Tidak Spesifik / Pengurus Umum</span>
            @endif
        </div>

        <table>
            <thead>
                <tr style="background-color: #343a40;">
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Aspek Utama</th>
                    <th style="width: 45%;">Indikator Penilaian (Bobot)</th>
                    <th style="width: 15%;">Skor</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedDetails = $k->kinerjaDetails->groupBy(function($detail) {
                        return $detail->instrumen->aspek ?? 'Lainnya';
                    });
                    $noAspek = 1;
                @endphp

                @forelse($groupedDetails as $aspek => $details)
                    @foreach($details as $index => $detail)
                        <tr>
                            @if($index == 0)
                                <td rowspan="{{ count($details) }}">{{ $noAspek++ }}</td>
                                <td rowspan="{{ count($details) }}" class="bg-light text-start">{{ $aspek }}</td>
                            @endif
                            <td class="text-start">{{ $detail->instrumen->indikator ?? '-' }} ({{ $detail->instrumen->bobot ?? 0 }}%)</td>
                            <td class="{{ $detail->skor < 60 ? 'text-danger' : 'text-success' }}">
                                {{ $detail->skor }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="4">Detail skor tidak tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table style="margin-top: 0; border-top: none;">
            <tbody>
                <tr>
                    <td class="bg-light" style="width: 15%;">TOTAL SKOR</td>
                    <td style="width: 10%; font-size: 12px;"
                        class="{{ $k->nilai_total >= 70 ? 'text-success' : 'text-danger' }}">
                        {{ $k->nilai_total }}
                    </td>
                    <td class="bg-light" style="width: 10%;">MUTU</td>
                    <td style="width: 10%; font-size: 12px; font-weight: bold;">{{ $k->huruf_mutu }}</td>
                    <td class="bg-light" style="width: 15%;">Rekomendasi</td>
                    <td class="text-start" style="width: 40%; font-weight: bold;">{{ $k->rekomendasi }}</td>
                </tr>
                <tr>
                    <td class="bg-light">Catatan Atasan</td>
                    <td colspan="5" class="text-start">{{ $k->catatan ?? 'Tidak ada catatan.' }}</td>
                </tr>
                <tr>
                    <td class="bg-light">Tindak Lanjut</td>
                    <td colspan="5" class="text-start">
                        @if ($k->status_tindak_lanjut == 'sudah')
                            <strong style="color: #28a745;">Sudah Ditangani</strong>
                            ({{ \Carbon\Carbon::parse($k->tanggal_tindak_lanjut)->translatedFormat('d M Y') }})
                            <div class="info-box">"{{ $k->deskripsi_tindak_lanjut }}"</div>
                        @else
                            <strong style="color: #dc3545;">Menunggu Respons Pimpinan</strong>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    @empty
        <div style="text-align: center; margin-top: 50px; color: #666; padding: 20px; border: 1px dashed #999;">
            Belum ada riwayat penilaian kinerja untuk pengurus ini.
        </div>
    @endforelse

</body>

</html>
