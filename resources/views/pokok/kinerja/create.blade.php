@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-4">Form Penilaian Kinerja</h5>
                <form action="{{ route('pokok.kinerja.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Pengurus</label>
                            <select name="pengurus_id" class="form-select" required>
                                <option value="">-- Pilih Pengurus --</option>
                                @foreach ($pengurus as $p)
                                    <option value="{{ $p->id }}"
                                        {{ isset($selected_id) && $selected_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Penilaian</label>
                            <input type="date" name="tanggal_penilaian" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                    </div>

                    {{-- Tabel Skor (Singkat) --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Indikator</th>
                                    <th>Bobot</th>
                                    <th>Skor (0-100)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Kedisiplinan Waktu</td>
                                    <td class="text-center">13%</td>
                                    <td><input type="number" name="skor_disiplin_waktu" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Tanggung Jawab (Izin/Absen)</td>
                                    <td class="text-center">11%</td>
                                    <td><input type="number" name="skor_tanggung_jawab_izin" class="form-control"
                                            min="0" max="100" required></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Penyelesaian Tugas</td>
                                    <td class="text-center">12%</td>
                                    <td><input type="number" name="skor_selesai_tugas" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Loyalitas Pesantren</td>
                                    <td class="text-center">8%</td>
                                    <td><input type="number" name="skor_loyalitas" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Akhlak & Sopan Santun</td>
                                    <td class="text-center">14%</td>
                                    <td><input type="number" name="skor_akhlak" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Keteladanan (Contoh)</td>
                                    <td class="text-center">12%</td>
                                    <td><input type="number" name="skor_contoh" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Pemahaman Tupoksi</td>
                                    <td class="text-center">11%</td>
                                    <td><input type="number" name="skor_tupoksi" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Komunikasi</td>
                                    <td class="text-center">7%</td>
                                    <td><input type="number" name="skor_komunikasi" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>Koordinasi & Kerjasama</td>
                                    <td class="text-center">7%</td>
                                    <td><input type="number" name="skor_koordinasi" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>Kebersamaan Pengurus</td>
                                    <td class="text-center">5%</td>
                                    <td><input type="number" name="skor_kebersamaan" class="form-control" min="0"
                                            max="100" required></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Catatan Tambahan:</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
