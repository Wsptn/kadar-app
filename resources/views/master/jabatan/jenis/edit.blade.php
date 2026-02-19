@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h2 class="fw-bold mb-0">Edit Jenis Jabatan</h2>
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

                <form action="{{ route('master.jabatan.jenis.update', $jenis->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Entitas Pengurus</label>
                        <select name="entitas_id" id="entitas_id"
                            class="form-select @error('entitas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Entitas --</option>
                            @foreach ($entitas as $e)
                                <option value="{{ $e->id }}"
                                    {{ old('entitas_id', $jenis->entitas_id) == $e->id ? 'selected' : '' }}>
                                    {{ $e->nama_entitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jabatan</label>
                        <select name="jabatan_id" id="jabatan_id"
                            class="form-select @error('jabatan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatans as $j)
                                <option value="{{ $j->id }}"
                                    {{ old('jabatan_id', $jenis->jabatan_id) == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Jabatan</label>
                        <input type="text" name="jenis_jabatan"
                            class="form-control @error('jenis_jabatan') is-invalid @enderror"
                            value="{{ old('jenis_jabatan', $jenis->jenis_jabatan) }}" required>
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

            entitasSelect.addEventListener('change', function() {
                const entitasId = this.value;
                jabatanSelect.innerHTML = '<option value="">Memuat...</option>';
                jabatanSelect.disabled = true;

                if (entitasId) {
                    fetch(`/master/jabatan/get-jabatan/${entitasId}`)
                        .then(response => response.json())
                        .then(data => {
                            jabatanSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                            data.forEach(item => {
                                jabatanSelect.innerHTML +=
                                    `<option value="${item.id}">${item.nama_jabatan}</option>`;
                            });
                            jabatanSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching jabatan:', error);
                            jabatanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                        });
                } else {
                    jabatanSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                    jabatanSelect.disabled = false;
                }
            });
        });
    </script>
@endsection
