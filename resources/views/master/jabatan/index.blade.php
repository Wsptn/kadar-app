@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4 ">Master Jabatan</h1>

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-4 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Jabatan</span></span>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="jabatanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="entitas-tab" data-bs-toggle="tab" data-bs-target="#entitas"
                    type="button">
                    Entitas Pengurus
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan" type="button">
                    Jabatan
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="jenis-tab" data-bs-toggle="tab" data-bs-target="#jenis" type="button">
                    Jenis Jabatan
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="grade-tab" data-bs-toggle="tab" data-bs-target="#grade" type="button">
                    Grade Jabatan
                </button>
            </li>
        </ul>

        <div class="tab-content" id="jabatanTabContent">

            {{-- ============================ TAB ENTITAS PENGURUS ============================ --}}
            <div class="tab-pane fade show active" id="entitas" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Data Entitas Pengurus</h5>
                            @if (!Auth::user()->isDaerah())
                                <a href="{{ route('master.jabatan.entitas.create') }}" class="btn btn-success btn-sm">
                                    <i data-feather="plus-circle" class="me-1"></i>Tambah Entitas
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-success text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Entitas Pengurus</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($entitas as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->nama_entitas }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('master.jabatan.entitas.edit', $item->id) }}"
                                                        class="btn btn-outline-primary btn-sm"><i data-feather="edit-2"
                                                            style="width: 14px;"></i></a>
                                                    <form action="{{ route('master.jabatan.entitas.destroy', $item->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus entitas ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i
                                                                data-feather="trash-2" style="width: 14px;"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================ TAB JABATAN ============================ --}}
            <div class="tab-pane fade" id="jabatan" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Data Jabatan</h5>
                            @if (!Auth::user()->isDaerah())
                                <a href="{{ route('master.jabatan.jabatan.create') }}" class="btn btn-success btn-sm">
                                    <i data-feather="plus-circle" class="me-1"></i>Tambah Jabatan
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-success text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Entitas</th>
                                        <th>Jabatan</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jabatan as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->entitas->nama_entitas ?? '-' }}</td>
                                            <td>{{ $item->nama_jabatan }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('master.jabatan.jabatan.edit', $item->id) }}"
                                                        class="btn btn-outline-primary btn-sm"><i data-feather="edit-2"
                                                            style="width: 14px;"></i></a>
                                                    <form action="{{ route('master.jabatan.jabatan.destroy', $item->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus jabatan ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i
                                                                data-feather="trash-2" style="width: 14px;"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================ TAB JENIS JABATAN ============================ --}}
            <div class="tab-pane fade" id="jenis" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Data Jenis Jabatan</h5>
                            @if (!Auth::user()->isDaerah())
                                <a href="{{ route('master.jabatan.jenis.create') }}" class="btn btn-success btn-sm">
                                    <i data-feather="plus-circle" class="me-1"></i>Tambah Jenis
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-success text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Jabatan</th>
                                        <th>Jenis Jabatan</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jenis as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                                            <td>{{ $item->jenis_jabatan }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('master.jabatan.jenis.edit', $item->id) }}"
                                                        class="btn btn-outline-primary btn-sm"><i data-feather="edit-2"
                                                            style="width: 14px;"></i></a>
                                                    <form action="{{ route('master.jabatan.jenis.destroy', $item->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus jenis ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i
                                                                data-feather="trash-2" style="width: 14px;"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================ TAB GRADE JABATAN ============================ --}}
            <div class="tab-pane fade" id="grade" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Data Grade Jabatan</h5>
                            @if (!Auth::user()->isDaerah())
                                <a href="{{ route('master.jabatan.grade.create') }}" class="btn btn-success btn-sm">
                                    <i data-feather="plus-circle" class="me-1"></i>Tambah Grade
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-success text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Jenis</th>
                                        <th>Grade</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($grade as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->jenis->jenis_jabatan ?? '-' }}</td>
                                            <td>{{ $item->grade }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('master.jabatan.grade.edit', $item->id) }}"
                                                        class="btn btn-outline-primary btn-sm"><i data-feather="edit-2"
                                                            style="width: 14px;"></i></a>
                                                    <form action="{{ route('master.jabatan.grade.destroy', $item->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus grade ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i
                                                                data-feather="trash-2" style="width: 14px;"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3">Belum ada data.</td>
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
        .nav-tabs .nav-link.active {
            color: #198754 !important;
            font-weight: 600;
            border-bottom: 3px solid #198754;
        }

        .nav-tabs .nav-link {
            color: #000 !important;
        }
    </style>
@endpush
