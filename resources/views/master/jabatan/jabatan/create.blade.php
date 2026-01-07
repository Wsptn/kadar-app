@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Jabatan</h1>
            <a href="{{ route('master.jabatan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Jabatan</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.jabatan.jabatan.store') }}" method="POST">
                    @csrf

                    {{-- Pilih Entitas --}}
                    <div class="mb-3">
                        <label class="form-label">Entitas Pengurus</label>
                        <select name="entitas_id" class="form-select @error('entitas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Entitas --</option>

                            @foreach ($entitas as $e)
                                <option value="{{ $e->id }}" {{ old('entitas_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->nama_entitas }}
                                </option>
                            @endforeach
                        </select>

                        @error('entitas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama Jabatan --}}
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="nama_jabatan"
                            class="form-control @error('nama_jabatan') is-invalid @enderror" placeholder="Masukkan jabatan"
                            value="{{ old('nama_jabatan') }}" required>

                        @error('nama_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
