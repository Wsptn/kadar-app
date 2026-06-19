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

            {{-- BAGIAN KIRI: Judul dan Nama Pengurus --}}
            <div>
                <h2 class="mb-1">Riwayat Kinerja</h2>
                <div class="text-muted">Pengurus: <strong class="text-success">{{ $pengurus->nama }}</strong></div>
            </div>

            {{-- BAGIAN KANAN: Tombol Aksi --}}
            <div class="d-flex gap-2">
                <a href="{{ route('pokok.kinerja.export_pdf', $pengurus->id) }}?triwulan={{ request('triwulan') }}&tahun={{ request('tahun') }}" class="btn btn-danger btn-sm shadow-sm"
                    target="_blank">
                    <i data-feather="printer" class="me-1" style="width: 16px;"></i> Cetak PDF
                </a>

                <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i data-feather="arrow-left" class="me-1" style="width: 16px;"></i> Kembali
                </a>
            </div>

        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i data-feather="check-circle" class="me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                
                {{-- FILTER FORM --}}
                <div class="mb-3 d-flex justify-content-end">
                    <form action="{{ route('pokok.kinerja.show', $pengurus->id) }}" method="GET" class="d-flex gap-2 align-items-center">
                        <select name="triwulan" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Semua Triwulan</option>
                            <option value="1" {{ request('triwulan') == '1' ? 'selected' : '' }}>Triwulan 1</option>
                            <option value="2" {{ request('triwulan') == '2' ? 'selected' : '' }}>Triwulan 2</option>
                            <option value="3" {{ request('triwulan') == '3' ? 'selected' : '' }}>Triwulan 3</option>
                            <option value="4" {{ request('triwulan') == '4' ? 'selected' : '' }}>Triwulan 4</option>
                        </select>
                        <select name="tahun" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Semua Tahun</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = 2024; 
                            @endphp
                            @for($i = $currentYear; $i >= $startYear; $i--)
                                <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-success btn-sm"><i data-feather="filter" style="width: 14px;"></i> Filter</button>
                        @if(request('triwulan') || request('tahun'))
                            <a href="{{ route('pokok.kinerja.show', $pengurus->id) }}" class="btn btn-secondary btn-sm"><i data-feather="x" style="width: 14px;"></i> Reset</a>
                        @endif
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center" style="font-size: 0.9rem;">
                        <thead class="table-dark">
                            <tr>
                                <th>Periode & Tgl Penilaian</th>
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
                                    <td>
                                        <div class="fw-bold mb-1" style="font-size: 0.9rem;">
                                            @if($k->triwulan && $k->tahun)
                                                Triwulan {{ $k->triwulan }} - {{ $k->tahun }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($k->tanggal_penilaian)->format('d M Y') }}</small>
                                    </td>

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
                                            <span class="badge bg-danger">Penanganan khusus (Merujuk ke SOP)</span>
                                        @endif
                                    </td>

                                    <td class="text-start"><small
                                            class="text-muted">{{ $k->catatan ?? 'Tidak ada catatan' }}</small></td>

                                    <td>
                                        @if ($k->status_tindak_lanjut == 'belum')
                                            @php
                                                $me = auth()->user();
                                                $target = $pengurus;
                                                $bolehTanganin = false;

                                                if ($me->isAdmin() || $me->isBiktren()) {
                                                    $bolehTanganin = true;
                                                } elseif ($me->isWilayah()) {
                                                    // Wilayah bisa tangani jika target adalah Entitas Daerah (ID 2)
                                                    if (
                                                        $target->entitas_id == 2 &&
                                                        $target->wilayah_id == $me->wilayah_id
                                                    ) {
                                                        $bolehTanganin = true;
                                                    }
                                                }
                                            @endphp

                                            @if ($bolehTanganin)
                                                {{-- Tombol muncul untuk semua mutu A-E selama belum ditangani --}}
                                                <button type="button" class="btn btn-danger btn-sm w-100 shadow-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalTangani{{ $k->id }}">
                                                    <i data-feather="edit-3" style="width:14px"></i> Beri Catatan
                                                </button>

                                                {{-- Modal Input Deskripsi --}}
                                                <div class="modal fade" id="modalTangani{{ $k->id }}" tabindex="-1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <form
                                                                action="{{ route('pokok.kinerja.mark_handled', $k->id) }}"
                                                                method="POST">
                                                                @csrf @method('PUT')
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">Respons Atasan</h5>
                                                                    <button type="button" class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="small text-muted">Berikan catatan pembinaan
                                                                        atau apresiasi untuk pengurus ini.</p>
                                                                    <textarea name="deskripsi_tindak_lanjut" class="form-control" rows="3" placeholder="Tuliskan catatan..." required></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-danger">Simpan
                                                                        Catatan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div
                                                    class="border border-secondary rounded p-2 bg-light text-muted text-center">
                                                    <i data-feather="lock" style="width:12px"></i>
                                                    <small class="fw-bold d-block mt-1">
                                                        {{ $target->entitas_id == 1 ? 'Wewenang Biktren' : 'Menunggu Atasan' }}
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            {{-- DESKRIPSI WAJIB MUNCUL JIKA SUDAH DITANGANI --}}
                                            <div
                                                class="border border-success rounded p-2 bg-success bg-opacity-10 text-success text-center">
                                                <i data-feather="check-circle" style="width:14px"></i>
                                                <strong>Selesai</strong><br>
                                                <div class="mt-2 bg-white rounded p-2 text-dark small fst-italic shadow-sm text-start"
                                                    style="border-left: 3px solid #28a745;">
                                                    "{{ $k->deskripsi_tindak_lanjut }}"
                                                </div>
                                                <small class="text-muted d-block mt-2 text-end">
                                                    <i data-feather="calendar" style="width: 12px;"></i>
                                                    Dicatat:
                                                    {{ \Carbon\Carbon::parse($k->tanggal_tindak_lanjut)->translatedFormat('d F Y') }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                {{-- BARIS TERSEMBUNYI (RINCIAN SKOR DIKELOMPOKKAN 5 ASPEK) --}}
                                <tr class="collapse detail-row" id="detailSkor{{ $k->id }}">
                                    <td colspan="6">
                                        <div class="detail-content text-start">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                                <div>
                                                    <h6 class="fw-bold text-success mb-1"><i data-feather="list"
                                                            style="width: 14px;"></i> Rincian Penilaian per Aspek</h6>
                                                    <small class="text-muted fw-bold" style="font-size: 0.8rem;">
                                                        <i data-feather="tag" style="width: 12px; margin-bottom: 2px;"></i> Kapasitas: 
                                                        @if($k->jabatan_id && $k->riwayatJabatan && $k->riwayatJabatan->strukturJabatan)
                                                            {{ $k->riwayatJabatan->strukturJabatan->jabatan }} ({{ $k->riwayatJabatan->strukturJabatan->entitas }})
                                                        @elseif($k->tugas_id && $k->riwayatTugas && $k->riwayatTugas->masterTugas)
                                                            {{ $k->riwayatTugas->masterTugas->nama_tugas }} ({{ ucfirst($k->riwayatTugas->masterTugas->jenis_tugas ?? 'Tugas') }})
                                                        @else
                                                            <span class="fst-italic text-secondary">Tidak Spesifik / Umum</span>
                                                        @endif
                                                    </small>
                                                </div>
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
                                                    @php
                                                        $groupedDetails = $k->kinerjaDetails->groupBy(function($detail) {
                                                            return $detail->instrumen->aspek ?? 'Lainnya';
                                                        });
                                                        $noAspek = 1;
                                                    @endphp

                                                    @forelse($groupedDetails as $aspek => $details)
                                                    <tbody style="page-break-inside: avoid;">
                                                        @foreach($details as $detail)
                                                            <tr>
                                                                @if($loop->first)
                                                                    <td class="fw-bold bg-light" style="vertical-align: top;">{{ $noAspek++ }}. {{ $aspek }}</td>
                                                                @else
                                                                    <td class="bg-light border-top-0 border-bottom-0"></td>
                                                                @endif
                                                                <td>{{ $detail->instrumen->indikator ?? '-' }} ({{ $detail->bobot_saat_dinilai ?? $detail->instrumen->bobot ?? 0 }}%)</td>
                                                                <td class="text-center fw-bold fs-6 {{ $detail->skor < 60 ? 'text-danger' : 'text-success' }}">
                                                                    {{ $detail->skor }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    @empty
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">Detail skor tidak tersedia.</td>
                                                        </tr>
                                                    </tbody>
                                                    @endforelse
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
