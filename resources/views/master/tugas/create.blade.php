@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Tambah Master Tugas</h1>

        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Master / <a href="{{ route('master.tugas.index') }}" class="text-success text-decoration-none">Master Tugas</a> / <span class="fw-semibold">Tambah</span></span>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('master.tugas.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tugas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_tugas" class="form-control" value="{{ old('nama_tugas') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tugas <span class="text-danger">*</span></label>
                        <select name="jenis_tugas" class="form-select" required>
                            <option value="">-- Pilih Jenis Tugas --</option>
                            <option value="fungsional" {{ old('jenis_tugas') == 'fungsional' ? 'selected' : '' }}>Fungsional</option>
                            <option value="internal" {{ old('jenis_tugas') == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="eksternal" {{ old('jenis_tugas') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                        </select>
                    </div>


                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('master.tugas.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
