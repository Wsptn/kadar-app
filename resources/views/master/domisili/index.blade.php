@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4 ">Master Domisili</h1>

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-4 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Domisili</span></span>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i data-feather="check-circle" class="me-1" style="width: 18px; height: 18px;"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i data-feather="alert-circle" class="me-1" style="width: 18px; height: 18px;"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Data Domisili</h5>
                    @if ($hasAccess)
                        <a href="{{ route('master.domisili.create') }}" class="btn btn-success btn-sm">
                            <i data-feather="plus-circle" class="me-1"></i>Tambah Domisili
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="domisiliTable">
                        <thead class="table-success text-center">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Wilayah</th>
                                <th>Daerah</th>
                                <th>Entitas Daerah</th>
                                <th>Kamar</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($domisilis as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->wilayah }}</td>
                                    <td>{{ $item->daerah }}</td>
                                    <td>{{ $item->entitas_daerah ?? '-' }}</td>
                                    <td class="text-center">{{ $item->kamar }}</td>
                                    <td class="text-center">
                                        @if ($hasAccess)
                                            <div class="btn-group">
                                                <a href="{{ route('master.domisili.edit', $item->id) }}"
                                                    class="btn btn-outline-warning btn-sm">
                                                    <i data-feather="edit-2" style="width: 14px;"></i>
                                                </a>
                                                <form
                                                    action="{{ route('master.domisili.destroy', $item->id) }}"
                                                    method="POST" onsubmit="return confirm('Hapus domisili ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"><i
                                                            data-feather="trash-2"
                                                            style="width: 14px;"></i></button>
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
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada data domisili.
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

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#domisiliTable')) {
                $('#domisiliTable').DataTable().destroy();
            }
            $('#domisiliTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                pageLength: 25
            });
        });
    </script>
@endpush
