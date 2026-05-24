@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Tambah Entitas Daerah</h1>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('master.entitas_daerah.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Entitas Daerah</label>
                        <input type="text" name="nama_entitas_daerah" class="form-control @error('nama_entitas_daerah') is-invalid @enderror" value="{{ old('nama_entitas_daerah') }}" required>
                        @error('nama_entitas_daerah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('master.entitas_daerah.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
