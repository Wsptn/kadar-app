@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h2 class="fw-bold mb-0">Edit Entitas Pengurus</h2>
            </div>
            <a href="{{ route('master.jabatan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i data-feather="arrow-left" style="width: 16px;"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('master.jabatan.entitas.update', $entitas->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Entitas</label>
                        <input type="text" name="nama_entitas"
                            class="form-control @error('nama_entitas') is-invalid @enderror"
                            value="{{ old('nama_entitas', $entitas->nama_entitas) }}" required autofocus>
                        @error('nama_entitas')
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
