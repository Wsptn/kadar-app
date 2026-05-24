@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Master Struktur Jabatan</h1>

        <div class="bg-light p-2 mb-4 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Struktur Jabatan</span></span>
        </div>

        {{-- Logika Hak Akses --}}
        @php
            $userLevel = Auth::user()->level;
            $hasAccess = in_array($userLevel, ['Admin', 'Wilayah', 'Biktren']);
        @endphp

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Data Struktur Jabatan (Flat)</h5>
                    @if ($hasAccess)
                        <a href="{{ route('master.struktur_jabatan.create') }}" class="btn btn-success btn-sm">
                            <i data-feather="plus-circle" class="me-1"></i>Tambah Struktur Jabatan
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
                                <th>Jenis Jabatan</th>
                                <th>Grade</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jabatans as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->entitas }}</td>
                                    <td>{{ $item->jabatan }}</td>
                                    <td>{{ $item->jenis_jabatan }}</td>
                                    <td class="text-center">{{ $item->grade }}</td>
                                    <td class="text-center">
                                        @if ($hasAccess)
                                            <div class="btn-group">
                                                <a href="{{ route('master.struktur_jabatan.edit', $item->id) }}"
                                                    class="btn btn-outline-warning btn-sm">
                                                    <i data-feather="edit-2" style="width: 14px;"></i>
                                                </a>
                                                <form action="{{ route('master.struktur_jabatan.destroy', $item->id) }}"
                                                    method="POST" onsubmit="return confirm('Hapus struktur jabatan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i data-feather="trash-2" style="width: 14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="badge"
                                                style="background-color: #6c757d; color: white; font-weight: 500; padding: 5px 10px;">
                                                <i class="bi bi-lock-fill"></i> Restricted
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Belum ada data struktur jabatan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
    <script>
        $(document).ready(function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endpush
