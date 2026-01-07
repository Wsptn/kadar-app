@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4 ">Master Domisili</h1>

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-4 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Domisili</span></span>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="domisiliTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="wilayah-tab" data-bs-toggle="tab" data-bs-target="#wilayah"
                    type="button">Wilayah</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="daerah-tab" data-bs-toggle="tab" data-bs-target="#daerah"
                    type="button">Daerah</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kamar-tab" data-bs-toggle="tab" data-bs-target="#kamar"
                    type="button">Kamar</button>
            </li>
        </ul>

        <div class="tab-content" id="domisiliTabContent">

            {{-- ============================ TAB WILAYAH ============================ --}}
            <div class="tab-pane fade show active" id="wilayah" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Data Wilayah</h5>

                            {{-- ➜ Pindah ke halaman create --}}
                            @if (!Auth::user()->isDaerah())
                                <a href="{{ route('master.domisili.wilayah.create') }}" class="btn btn-success btn-sm">
                                    <i data-feather="plus-circle" class="me-1"></i>Tambah Wilayah
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-success text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Nama Wilayah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($wilayah as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->nama_wilayah }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">Belum ada data wilayah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ============================ TAB DAERAH ============================ --}}
            <div class="tab-pane fade" id="daerah" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Data Daerah</h5>

                            {{-- ➜ Pindah ke halaman create --}}
                            @if (!Auth::user()->isDaerah())
                                <a href="{{ route('master.domisili.daerah.create') }}" class="btn btn-success btn-sm">
                                    <i data-feather="plus-circle" class="me-1"></i>Tambah Daerah
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-success text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Nama Wilayah</th>
                                        <th>Nama Daerah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($daerah as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->wilayah->nama_wilayah ?? '-' }}</td>
                                            <td>{{ $item->nama_daerah }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Belum ada data daerah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ============================ TAB KAMAR ============================ --}}
            <div class="tab-pane fade" id="kamar" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Data Kamar</h5>

                            {{-- ➜ Pindah ke halaman create --}}
                            @if (!Auth::user()->isDaerah())
                                <a href="{{ route('master.domisili.kamar.create') }}" class="btn btn-success btn-sm">
                                    <i data-feather="plus-circle" class="me-1"></i>Tambah Kamar
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-success text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Nama Wilayah</th>
                                        <th>Nama Daerah</th>
                                        <th>Nomor Kamar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kamar as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->wilayah->nama_wilayah ?? '-' }}</td>
                                            <td>{{ $item->daerah->nama_daerah ?? '-' }}</td>
                                            <td>{{ $item->nomor_kamar }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Belum ada data kamar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('page-scripts')
    <script>
        $(document).ready(function() {
            feather.replace();
        });
    </script>
@endpush

@push('styles')
    <style>
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #343a40;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 0.55rem 0.75rem;
        }

        .nav-tabs .nav-link.active {
            color: #198754;
            font-weight: 600;
            border-bottom: 3px solid #198754;
        }

        /* Warna teks tab */
        .nav-tabs .nav-link {
            color: #000 !important;
            /* Hitam */
        }

        /* Warna teks saat tab aktif */
        .nav-tabs .nav-link.active {
            color: #198754 !important;
            font-weight: 600;
            /* Opsional: sedikit ditebalkan */
        }
    </style>
@endpush
