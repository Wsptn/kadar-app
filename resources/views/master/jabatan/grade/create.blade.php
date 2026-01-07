@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Grade Jabatan</h1>
            <a href="{{ route('master.jabatan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Grade</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.jabatan.grade.store') }}" method="POST">
                    @csrf

                    {{-- Pilih Entitas --}}
                    <div class="mb-3">
                        <label class="form-label">Entitas</label>
                        <select id="entitasSelect" name="entitas_id"
                            class="form-select @error('entitas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Entitas --</option>
                            @foreach ($entitas as $e)
                                <option value="{{ $e->id }}">{{ $e->nama_entitas }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilih Jabatan --}}
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <select id="jabatanSelect" name="jabatan_id"
                            class="form-select @error('jabatan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Entitas Terlebih Dahulu --</option>
                        </select>
                    </div>

                    {{-- Pilih Jenis Jabatan --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Jabatan</label>
                        <select id="jenisSelect" name="jenis_jabatan_id"
                            class="form-select @error('jenis_jabatan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jabatan Terlebih Dahulu --</option>
                        </select>
                    </div>

                    {{-- Nama Grade --}}
                    <div class="mb-3">
                        <label class="form-label">Grade Jabatan</label>
                        <input type="text" name="grade" class="form-control @error('grade') is-invalid @enderror"
                            placeholder="Masukkan grade jabatan" value="{{ old('grade') }}" required>
                        @error('grade')
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
    @push('page-scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function() {

                // ==== Ketika Entitas Dipilih ====
                $('#entitasSelect').change(function() {
                    let entitasId = $(this).val();

                    // Kosongkan dropdown jabatan & jenis
                    $('#jabatanSelect').html('<option value="">-- Memuat jabatan... --</option>');
                    $('#jenisSelect').html('<option value="">-- Pilih Jabatan Terlebih Dahulu --</option>');

                    if (entitasId) {
                        $.ajax({
                            url: '/master/jabatan/get-jabatan/' + entitasId,
                            type: 'GET',
                            success: function(data) {

                                $('#jabatanSelect').empty().append(
                                    '<option value="">-- Pilih Jabatan --</option>');

                                $.each(data, function(key, item) {
                                    $('#jabatanSelect').append(
                                        '<option value="' + item.id + '">' + item
                                        .nama_jabatan + '</option>'
                                    );
                                });
                            }
                        });
                    }
                });

                // ==== Ketika Jabatan Dipilih ====
                $('#jabatanSelect').change(function() {
                    let jabatanId = $(this).val();

                    $('#jenisSelect').html('<option value="">-- Memuat jenis jabatan... --</option>');

                    if (jabatanId) {
                        $.ajax({
                            url: '/master/jabatan/get-jenis/' + jabatanId,
                            type: 'GET',
                            success: function(data) {

                                $('#jenisSelect').empty().append(
                                    '<option value="">-- Pilih Jenis Jabatan --</option>');

                                $.each(data, function(key, item) {
                                    $('#jenisSelect').append(
                                        '<option value="' + item.id + '">' + item
                                        .jenis_jabatan + '</option>'
                                    );
                                });
                            }
                        });
                    }
                });

            });
        </script>
    @endpush
@endsection
