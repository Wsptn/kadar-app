@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Data User</h1>
            <a href="{{ route('user.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <form action="{{ route('user.store') }}" method="POST">
                    @csrf

                    {{-- Nama Pengguna (Diubah name="pengguna" jadi name="name") --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="Masukkan Nama Lengkap" required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="mb-3">
                        <label class="form-label">Username (untuk Login)</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username') }}" placeholder="Masukkan Username" required>

                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimal 6 karakter" required>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Level Selection --}}
                    <div class="mb-3">
                        <label class="form-label">Level User</label>
                        <div class="input-group dropdown">
                            <input id="levelInput" type="text" class="form-control @error('level') is-invalid @enderror"
                                readonly name="level" value="{{ old('level') }}" placeholder="Pilih Level..." required>

                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">Pilih</button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                {{-- Logic Opsi Level berdasarkan User Login --}}
                                @if (Auth::user()->isAdmin())
                                    <li><a class="dropdown-item level-option" href="#" data-value="Admin">Admin</a>
                                    </li>
                                    <li><a class="dropdown-item level-option" href="#"
                                            data-value="Biktren">Biktren</a></li>
                                @endif
                                <li><a class="dropdown-item level-option" href="#" data-value="Wilayah">Wilayah</a>
                                </li>
                                <li><a class="dropdown-item level-option" href="#" data-value="Daerah">Daerah</a></li>
                            </ul>
                        </div>
                        @error('level')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Container Select Wilayah (Hidden by Default) --}}
                    <div class="mb-3 d-none" id="wilayahContainer">
                        <label class="form-label">Pilih Wilayah</label>
                        <select name="wilayah_id" id="selectWilayah"
                            class="form-select @error('wilayah_id') is-invalid @enderror">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach (\App\Models\Wilayah::all() as $w)
                                <option value="{{ $w->id }}">{{ $w->nama_wilayah }}</option>
                            @endforeach
                        </select>
                        @error('wilayah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Container Select Daerah (Hidden by Default) --}}
                    <div class="mb-3 d-none" id="daerahContainer">
                        <label class="form-label">Pilih Daerah</label>
                        <select name="daerah_id" id="selectDaerah"
                            class="form-select @error('daerah_id') is-invalid @enderror">
                            <option value="">-- Pilih Daerah --</option>
                            {{-- Data Daerah akan di-load via AJAX atau load semua jika data sedikit --}}
                            @foreach (\App\Models\Daerah::all() as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_daerah }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">*Pastikan Wilayah Daerah sesuai</small>
                        @error('daerah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- User Aktif Checkbox --}}
                    <div class="input-group mb-3 mt-4">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="checkbox" name="aktif" value="1" checked>
                        </div>
                        <input type="text" class="form-control" value="Status Akun Aktif" readonly>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="d-flex justify-content-end pt-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save"></i> Simpan User
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- Script JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const levelInput = document.getElementById('levelInput');
            const wilayahContainer = document.getElementById('wilayahContainer');
            const daerahContainer = document.getElementById('daerahContainer');

            // Function untuk handle perubahan tampilan input
            function handleLevelChange(level) {
                wilayahContainer.classList.add('d-none');
                daerahContainer.classList.add('d-none');
                document.getElementById('selectWilayah').required = false;
                document.getElementById('selectDaerah').required = false;

                if (level === 'Wilayah') {
                    wilayahContainer.classList.remove('d-none');
                    document.getElementById('selectWilayah').required = true;
                } else if (level === 'Daerah') {
                    daerahContainer.classList.remove('d-none');
                    document.getElementById('selectDaerah').required = true;
                    wilayahContainer.classList.remove('d-none');
                }
            }

            // Event Listener Klik Dropdown
            document.querySelectorAll('.level-option').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    let selectedValue = this.dataset.value;
                    levelInput.value = selectedValue;
                    handleLevelChange(selectedValue);
                });
            });

            // Handle Old Input (Jika validasi gagal, kembalikan state input)
            let oldLevel = "{{ old('level') }}";
            if (oldLevel) {
                handleLevelChange(oldLevel);
            }
        });
    </script>
@endsection
