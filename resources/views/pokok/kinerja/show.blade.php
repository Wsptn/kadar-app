@extends('layouts.app')

@section('this-page-style')
    <style>
        .detail-row td {
            background-color: #f8f9fa !important;
            border-top: none !important;
            padding: 0 !important;
        }

        .detail-content {
            padding: 1rem;
            border: 1px dashed #dee2e6;
            border-radius: 8px;
            margin: 0.5rem 1rem 1rem 1rem;
            background-color: #ffffff;
        }

        .table-aspek th,
        .table-aspek td {
            font-size: 0.85rem;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h2 class="mb-1">Riwayat Kinerja</h2>
                <div class="text-muted">Pengurus: <strong class="text-success">{{ $pengurus->nama }}</strong></div>
            </div>
            <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i data-feather="arrow-left" class="me-1" style="width: 16px;"></i> Kembali
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i data-feather="check-circle" class="me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center" style="font-size: 0.9rem;">
                        <thead class="table-dark">
                            <tr>
                                <th>Tgl Penilaian</th>
                                <th style="width: 130px;">Nilai & Detail</th>
                                <th style="width: 80px;">Mutu</th>
                                <th>Rekomendasi</th>
                                <th>Catatan</th>
                                <th style="width: 220px;">Status Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengurus->kinerja as $k)
                                {{-- BARIS UTAMA --}}
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($k->tanggal_penilaian)->format('d M Y') }}</td>

                                    <td>
                                        <span class="badge bg-success fs-6 mb-2">{{ $k->nilai_total }}</span><br>
                                        <button class="btn btn-sm btn-outline-success shadow-sm py-1 px-2" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#detailSkor{{ $k->id }}"
                                            aria-expanded="false" style="font-size: 0.75rem;">
                                            <i data-feather="chevron-down" style="width:12px;"></i> Lihat Skor
                                        </button>
                                    </td>

                                    <td><strong class="fs-5">{{ $k->huruf_mutu }}</strong></td>

                                    <td>
                                        @if ($k->huruf_mutu == 'A')
                                            <span class="badge bg-success">Apresiasi & Kaderisasi</span>
                                        @elseif($k->huruf_mutu == 'B')
                                            <span class="badge bg-info text-dark">Bimbingan ringan</span>
                                        @elseif($k->huruf_mutu == 'C')
                                            <span class="badge bg-warning text-dark">Pembinaan Sedang</span>
                                        @elseif($k->huruf_mutu == 'D')
                                            <span class="badge text-white" style="background-color: #fd7e14;">Pembinaan
                                                Intensif</span>
                                        @else
                                            <span class="badge bg-danger">Penanganan khusus/rujukan SOP bermasalah</span>
                                        @endif
                                    </td>

                                    <td class="text-start"><small
                                            class="text-muted">{{ $k->catatan ?? 'Tidak ada catatan' }}</small></td>

                                    <td>
                                        @if (in_array($k->huruf_mutu, ['C', 'D', 'E']))
                                            @if ($k->status_tindak_lanjut == 'belum')
                                                @php
                                                    $me = auth()->user();
                                                    $target = $pengurus;
                                                    $jabatanT = strtolower($target->jabatan->nama_jabatan ?? '');
                                                    $targetIsWilayah = str_contains($jabatanT, 'wilayah');
                                                    $targetIsDaerah = str_contains($jabatanT, 'daerah');
                                                    $isDiriSendiri = $target->id == $me->pengurus_id;

                                                    $bolehTanganin = false;
                                                    if ($me->isAdmin() || $me->isBiktren()) {
                                                        $bolehTanganin = !$isDiriSendiri; // Admin & Biktren bisa semua asal bukan diri sendiri
                                                    } elseif (
                                                        $me->isWilayah() &&
                                                        $targetIsDaerah &&
                                                        $target->wilayah_id == $me->wilayah_id
                                                    ) {
                                                        $bolehTanganin = true;
                                                    }
                                                @endphp

                                                @if ($bolehTanganin)
                                                    <button type="button" class="btn btn-danger btn-sm w-100 shadow-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalTangani{{ $k->id }}">
                                                        <i data-feather="edit-3" style="width:14px"></i> Tangani Sekarang
                                                    </button>

                                                    <div class="modal fade" id="modalTangani{{ $k->id }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content text-start">
                                                                <form
                                                                    action="{{ route('pokok.kinerja.mark_handled', $k->id) }}"
                                                                    method="POST">
                                                                    @csrf @method('PUT')
                                                                    <div class="modal-header bg-danger text-white">
                                                                        <h5 class="modal-title">Catatan Pembinaan</h5>
                                                                        <button type="button"
                                                                            class="btn-close btn-close-white"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <label class="fw-bold mb-2">Deskripsi
                                                                            Tindakan/Pembinaan:</label>
                                                                        <textarea name="deskripsi_tindak_lanjut" class="form-control" rows="3"
                                                                            placeholder="Contoh: Sudah dipanggil dan diberikan arahan terkait disiplin..." required></textarea>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-danger">Simpan
                                                                            & Selesaikan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted small">Menunggu Atasan</div>
                                                @endif
                                            @else
                                                <div class="text-success small">
                                                    <i data-feather="check-circle" style="width:14px"></i>
                                                    <strong>Selesai</strong><br>
                                                    <span
                                                        class="text-dark fst-italic">"{{ $k->deskripsi_tindak_lanjut }}"</span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small italic">Aman</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- BARIS TERSEMBUNYI (RINCIAN SKOR DIKELOMPOKKAN 5 ASPEK) --}}
                                <tr class="collapse detail-row" id="detailSkor{{ $k->id }}">
                                    <td colspan="6">
                                        <div class="detail-content text-start">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                                <h6 class="fw-bold text-success mb-0"><i data-feather="list"
                                                        style="width: 14px;"></i> Rincian Penilaian per Aspek</h6>
                                                <span class="badge bg-dark fs-6 px-3">Total Skor:
                                                    {{ $k->nilai_total }}</span>
                                            </div>

                                            <table class="table table-bordered table-sm align-middle table-aspek mb-0">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th style="width: 35%;">Aspek Utama</th>
                                                        <th style="width: 45%;">Indikator Penilaian</th>
                                                        <th style="width: 20%;">Skor Indikator</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Aspek 1 --}}
                                                    <tr>
                                                        <td rowspan="2" class="fw-bold bg-light">1. Kedisiplinan dan
                                                            Kehadiran</td>
                                                        <td>Disiplin Waktu (13%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_disiplin_waktu < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_disiplin_waktu }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kehadiran/Tanggung Jawab (11%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_tanggung_jawab_izin < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_tanggung_jawab_izin }}</td>
                                                    </tr>

                                                    {{-- Aspek 2 --}}
                                                    <tr>
                                                        <td rowspan="2" class="fw-bold bg-light">2. Tanggung Jawab dan
                                                            Loyalitas</td>
                                                        <td>Penyelesaian Tugas (12%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_selesai_tugas < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_selesai_tugas }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Loyalitas (8%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_loyalitas < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_loyalitas }}</td>
                                                    </tr>

                                                    {{-- Aspek 3 --}}
                                                    <tr>
                                                        <td rowspan="2" class="fw-bold bg-light">3. Akhlak dan
                                                            Keteladanan</td>
                                                        <td>Akhlak (14%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_akhlak < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_akhlak }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Keteladanan (12%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_contoh < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_contoh }}</td>
                                                    </tr>

                                                    {{-- Aspek 4 --}}
                                                    <tr>
                                                        <td rowspan="2" class="fw-bold bg-light">4. Kinerja dan
                                                            Inisiatif
                                                        </td>
                                                        <td>Tupoksi (11%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_tupoksi < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_tupoksi }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Komunikasi (7%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_komunikasi < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_komunikasi }}</td>
                                                    </tr>

                                                    {{-- Aspek 5 --}}
                                                    <tr>
                                                        <td rowspan="2" class="fw-bold bg-light">5. Kepemimpinan dan
                                                            Kerja Sama</td>
                                                        <td>Koordinasi (7%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_koordinasi < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_koordinasi }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kebersamaan (5%)</td>
                                                        <td
                                                            class="text-center fw-bold fs-6 {{ $k->skor_kebersamaan < 60 ? 'text-danger' : 'text-success' }}">
                                                            {{ $k->skor_kebersamaan }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat penilaian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather) feather.replace();
        });
    </script>
@endsection
