@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Master Fungsional Tugas</h1>

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Fungsional Tugas</span></span>
        </div>

        {{-- Tombol + Search --}}
        @if (!Auth::user()->isDaerah())
            <div class="d-flex align-items-center mb-4 flex-wrap gap-2">
                <a href="{{ route('master.tugas.create') }}" class="btn btn-success btn-sm">+ Tambah</a>
            </div>
        @endif

        {{-- Tabel --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-hover mb-0" id="data-table">
                        <thead class="table-success text-center">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Fungsional Tugas</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($FungsionalTugas as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->tugas }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        @if (!Auth::user()->isDaerah())
                                            <a href="{{ route('master.tugas.edit', $item->id_tugas) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                        @else
                                            <span class="badge bg-secondary" style="font-size: 0.7rem; cursor: not-allowed;"
                                                title="Hanya Admin & Biktren yang memiliki akses">
                                                <i class="bi bi-lock-fill"></i> Restricted
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Script pencarian --}}
@section('scripts')
    <script>
        document.getElementById("search-input").addEventListener("keyup", function() {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll("#data-table tbody tr");

            rows.forEach(row => {
                let rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(value) ? "" : "none";
            });
        });
    </script>
@endsection
