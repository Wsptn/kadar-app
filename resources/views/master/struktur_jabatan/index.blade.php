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
            $hasAccess = $userLevel !== 'Daerah';
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
                        <div class="d-flex gap-2">
                            @if (Auth::user()->isAdmin())
                                <button type="button" id="openImportBtn" class="btn btn-warning btn-sm d-flex align-items-center fw-semibold shadow-sm text-dark">
                                    <i data-feather="upload" class="me-1"></i> Import Excel
                                </button>
                            @endif
                            <a href="{{ route('master.struktur_jabatan.create') }}" class="btn btn-success btn-sm">
                                <i data-feather="plus-circle" class="me-1"></i>Tambah Struktur Jabatan
                            </a>
                        </div>
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

    {{-- MODAL IMPORT EXCEL (VANILLA JS/CSS) --}}
    @if (Auth::user()->isAdmin())
    <style>
        @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes modalSlideDown { from { opacity: 0; transform: translateY(-40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .modal-animated-overlay { animation: modalFadeIn 0.25s ease-out forwards; }
        .modal-animated-content { animation: modalSlideDown 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    </style>

    <div id="importModal" class="custom-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="custom-modal-content bg-white rounded shadow-lg" style="width: 100%; max-width: 500px; padding: 20px; position: relative;">
            <form action="{{ route('master.struktur_jabatan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="mb-0 d-flex align-items-center"><i data-feather="upload" class="me-2 text-warning"></i> Import Struktur Jabatan</h5>
                    <button type="button" id="closeImportBtn" class="btn-close" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6c757d; transition: color 0.2s;" onmouseover="this.style.color='#dc3545'" onmouseout="this.style.color='#6c757d'">&times;</button>
                </div>
                
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Gunakan fitur ini untuk menambahkan data Master Jabatan secara massal. Sistem otomatis melewatkan data yang sudah ada di sistem.
                    </p>
                    
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-bold d-block">1. Unduh Template</label>
                        <a href="{{ route('master.struktur_jabatan.template_import') }}" class="btn btn-sm btn-outline-success">
                            <i data-feather="download" style="width: 14px;" class="me-1"></i> Download Template .xlsx
                        </a>
                    </div>

                    <div class="mb-4">
                        <label for="file" class="form-label fw-bold d-block">2. Unggah File</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text small mt-1">Maksimal 2MB. Format: .xlsx atau .csv</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <button type="button" id="cancelImportBtn" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-success">Proses Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('openImportBtn');
            const closeBtn = document.getElementById('closeImportBtn');
            const cancelBtn = document.getElementById('cancelImportBtn');
            const modal = document.getElementById('importModal');
            const modalContent = modal ? modal.querySelector('.custom-modal-content') : null;

            if (openBtn && modal && modalContent) {
                openBtn.addEventListener('click', function() {
                    modal.style.display = 'flex';
                    modal.classList.remove('modal-animated-overlay');
                    modalContent.classList.remove('modal-animated-content');
                    void modal.offsetWidth; // Trigger reflow
                    modal.classList.add('modal-animated-overlay');
                    modalContent.classList.add('modal-animated-content');
                });
            }

            function closeModal() {
                if(!modal) return;
                modal.style.display = 'none';
            }

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        });
    </script>
    @endif
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
