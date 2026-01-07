@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Data</h1>
            <a href="{{ route('master.tugas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Fungsional Tugas</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.tugas.store') }}" method="POST">
                    @csrf

                    {{-- Fungsional Tugas --}}
                    <div class="mb-3">
                        <label class="form-label">Fungsional Tugas</label>
                        <input type="text" name="tugas" class="form-control @error('tugas') is-invalid @enderror"
                            value="{{ old('tugas') }}" required>

                        @error('tugas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="4" class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Tambahkan keterangan jika ada">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-end pt-2">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
