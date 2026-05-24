@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Tambah Struktur Jabatan</h1>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('master.struktur_jabatan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="entitas" class="form-label fw-bold">Entitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="entitas" name="entitas" value="{{ old('entitas') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jabatan" class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_jabatan" class="form-label fw-bold">Jenis Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="jenis_jabatan" name="jenis_jabatan" value="{{ old('jenis_jabatan') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="grade" class="form-label fw-bold">Grade <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="grade" name="grade" value="{{ old('grade') }}" required>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('master.struktur_jabatan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
