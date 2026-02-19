@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h2 class="fw-bold mb-0">Edit Grade Jabatan</h2>
            </div>
            <a href="{{ route('master.jabatan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i data-feather="arrow-left" style="width: 16px;"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('master.jabatan.grade.update', $grade->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Entitas Pengurus</label>
                        <select name="entitas_id" id="entitas_id" class="form-select" required>
                            <option value="">-- Pilih Entitas --</option>
                            @foreach ($entitas as $e)
                                <option value="{{ $e->id }}"
                                    {{ old('entitas_id', $grade->entitas_id) == $e->id ? 'selected' : '' }}>
                                    {{ $e->nama_entitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jabatan</label>
                        <select name="jabatan_id" id="jabatan_id" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatans as $j)
                                <option value="{{ $j->id }}"
                                    {{ old('jabatan_id', $grade->jabatan_id) == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Jabatan</label>
                        <select name="jenis_jabatan_id" id="jenis_jabatan_id" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach ($jenis as $js)
                                <option value="{{ $js->id }}"
                                    {{ old('jenis_jabatan_id', $grade->jenis_jabatan_id) == $js->id ? 'selected' : '' }}>
                                    {{ $js->jenis_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Grade</label>
                        <input type="text" name="grade" class="form-control" value="{{ old('grade', $grade->grade) }}"
                            required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i data-feather="save" class="me-1" style="width: 16px;"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather) feather.replace();

            const entitasSelect = document.getElementById('entitas_id');
            const jabatanSelect = document.getElementById('jabatan_id');
            const jenisSelect = document.getElementById('jenis_jabatan_id');

            // AJAX Entitas -> Jabatan
            entitasSelect.addEventListener('change', function() {
                const id = this.value;
                jabatanSelect.innerHTML = '<option value="">Memuat...</option>';
                jenisSelect.innerHTML = '<option value="">-- Pilih Jabatan Dulu --</option>';

                if (id) {
                    fetch(`/master/jabatan/get-jabatan/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            jabatanSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                            data.forEach(i => {
                                jabatanSelect.innerHTML +=
                                    `<option value="${i.id}">${i.nama_jabatan}</option>`;
                            });
                        });
                }
            });

            // AJAX Jabatan -> Jenis
            jabatanSelect.addEventListener('change', function() {
                const id = this.value;
                jenisSelect.innerHTML = '<option value="">Memuat...</option>';

                if (id) {
                    fetch(`/master/jabatan/get-jenis/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            jenisSelect.innerHTML = '<option value="">-- Pilih Jenis --</option>';
                            data.forEach(i => {
                                jenisSelect.innerHTML +=
                                    `<option value="${i.id}">${i.jenis_jabatan}</option>`;
                            });
                        });
                }
            });
        });
    </script>
@endsection
