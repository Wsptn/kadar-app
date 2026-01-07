@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Master Angkatan</h1>

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Angkatan</span></span>
        </div>
        @if (!Auth::user()->isDaerah())
            <div class="d-flex align-items-center mb-4 flex-wrap gap-2">
                <a href="{{ route('master.angkatan.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> + Tambah
                </a>
            </div>
        @endif

        {{-- Tabel Data Jabatan --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-hover mb-0">
                        <thead class="table-success">
                            <tr class="text-center">
                                <th style="width: 60px;">No</th>
                                <th>Angkatan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($angkatan as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->angkatan }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        {{-- Cek apakah user Admin atau Biktren --}}
                                        @if (!Auth::user()->isDaerah())
                                            <a href="{{ route('master.angkatan.edit', $item->id_angkatan) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                        @else
                                            {{-- Jika bukan Admin/Biktren, tampilkan pesan --}}
                                            <span class="badge bg-secondary" style="font-size: 0.7rem; cursor: not-allowed;"
                                                title="Hanya Admin & Biktren yang memiliki akses">
                                                <i class="bi bi-lock-fill"></i> Restricted
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada data.
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
