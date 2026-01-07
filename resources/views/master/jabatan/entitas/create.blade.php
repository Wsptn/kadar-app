@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Entitas Pengurus</h1>
            <a href="{{ route('master.jabatan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Entitas Pengurus</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.jabatan.entitas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Entitas Pengurus</label>
                        <input type="text" name="nama_entitas"
                            class="form-control @error('nama_entitas') is-invalid @enderror"
                            placeholder="Masukkan entitas pengurus" value="{{ old('nama_entitas') }}" required>

                        @error('nama_entitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end pt-2">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection
