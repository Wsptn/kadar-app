@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Master Jenis Berkas</h1>

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Jenis Berkas</span></span>
        </div>

        {{-- Tombol tambah dan pencarian --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            <form action="{{ route('master.berkas.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm custom-search-input me-2"
                    placeholder="Cari berkas..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-success btn-sm">Cari</button>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-hover mb-0">
                        <thead class="table-success text-center">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th style="width: 100px;">Aksi</th>
                                <th>Jenis Berkas</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jenisBerkas as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-warning text-white">Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                                    </td>
                                    <td>{{ $item->nama_berkas }}</td>
                                    <td>{{ $item->deskripsi ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($item->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        Belum ada data berkas.
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
