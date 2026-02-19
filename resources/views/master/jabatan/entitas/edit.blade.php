@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h2 class="fw-bold mb-0">Edit Jabatan</h2>
            </div>
            <a href="{{ route('master.jabatan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i data-feather="arrow-left" style="width: 16px;"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('master.jabatan.jabatan.update', $jabatan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Entitas Pengurus</label>
                        <select name="entitas_id" class="form-select @error('entitas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Entitas --</option>
                            @foreach ($entitas as $e)
                                <option value="{{ $e->id }}"
                                    {{ old('entitas_id', $jabatan->entitas_id) == $e->id ? 'selected' : '' }}>
                                    {{ $e->nama_entitas }}
                                </option>
                            @endforeach
                        </select>
                        @error('entitas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jabatan</label>
                        <input type="text" name="nama_jabatan"
                            class="form-control @error('nama_jabatan') is-invalid @enderror"
                            value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" required>
                        <small class="text-danger fst-italic">*Gunakan kata "Wilayah" atau "Daerah" agar filter
                            berfungsi.</small>
                        @error('nama_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
