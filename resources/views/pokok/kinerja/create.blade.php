@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="card-title mb-4 fw-bold text-success">Form Penilaian Kinerja Pengurus</h5>
                <form action="{{ route('pokok.kinerja.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Pengurus <span class="text-danger">*</span></label>
                            {{-- Dropdown otomatis terkunci jika $selected_id dikirim dari controller --}}
                            <select name="pengurus_id" class="form-select" required
                                {{ isset($selected_id) ? 'style=pointer-events:none;background-color:#e9ecef;' : '' }}>
                                <option value="">-- Pilih Pengurus --</option>
                                @foreach ($pengurus as $p)
                                    <option value="{{ $p->id }}"
                                        {{ isset($selected_id) && $selected_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} {{ $p->niup ? '(' . $p->niup . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if (isset($selected_id))
                                <small class="text-success"><i data-feather="check-circle" style="width: 12px;"></i>
                                    Pengurus otomatis terpilih.</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Penilaian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penilaian" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                    </div>

                    {{-- Tabel Skor Sesuai SOP --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Aspek Penilaian</th>
                                    <th style="width: 45%;">Indikator & Keterangan</th>
                                    <th style="width: 10%;">Bobot</th>
                                    <th style="width: 15%;">Skor (1-100)</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- ASPEK 1 --}}
                                <tr>
                                    <td rowspan="2" class="text-center fw-bold fs-5">1</td>
                                    <td rowspan="2" class="fw-bold bg-light">Kedisiplinan dan Kehadiran</td>
                                    <td>
                                        <strong>Kedisiplinan Waktu</strong><br>
                                        <small class="text-muted">Tepat waktu dalam mengikuti dan melaksanakan tugas sesuai
                                            dengan kalender kegiatan.</small>
                                    </td>
                                    <td class="text-center fw-bold">13%</td>
                                    <td><input type="number" name="skor_disiplin_waktu" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Kehadiran (Tanggung Jawab)</strong><br>
                                        <small class="text-muted">Tidak meninggalkan tanggung jawab tanpa izin.</small>
                                    </td>
                                    <td class="text-center fw-bold">11%</td>
                                    <td><input type="number" name="skor_tanggung_jawab_izin"
                                            class="form-control text-center" min="1" max="100" placeholder="0"
                                            required></td>
                                </tr>

                                {{-- ASPEK 2 --}}
                                <tr>
                                    <td rowspan="2" class="text-center fw-bold fs-5">2</td>
                                    <td rowspan="2" class="fw-bold bg-light">Tanggung Jawab dan Loyalitas</td>
                                    <td>
                                        <strong>Penyelesaian Tugas</strong><br>
                                        <small class="text-muted">Menyelesaikan tugas sesuai waktu yang telah
                                            direncanakan.</small>
                                    </td>
                                    <td class="text-center fw-bold">12%</td>
                                    <td><input type="number" name="skor_selesai_tugas" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Loyalitas</strong><br>
                                        <small class="text-muted">Menunjukkan loyalitas terhadap kebutuhan daerah, wilayah
                                            dan pesantren.</small>
                                    </td>
                                    <td class="text-center fw-bold">8%</td>
                                    <td><input type="number" name="skor_loyalitas" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>

                                {{-- ASPEK 3 --}}
                                <tr>
                                    <td rowspan="2" class="text-center fw-bold fs-5">3</td>
                                    <td rowspan="2" class="fw-bold bg-light">Akhlak dan Keteladanan</td>
                                    <td>
                                        <strong>Akhlak</strong><br>
                                        <small class="text-muted">Berperilaku sopan, berakhlak baik dan mengikuti semua
                                            bentuk aturan pesantren.</small>
                                    </td>
                                    <td class="text-center fw-bold">14%</td>
                                    <td><input type="number" name="skor_akhlak" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Keteladanan</strong><br>
                                        <small class="text-muted">Menjadi contoh bagi santri dan sesama pengurus.</small>
                                    </td>
                                    <td class="text-center fw-bold">12%</td>
                                    <td><input type="number" name="skor_contoh" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>

                                {{-- ASPEK 4 --}}
                                <tr>
                                    <td rowspan="2" class="text-center fw-bold fs-5">4</td>
                                    <td rowspan="2" class="fw-bold bg-light">Kinerja dan Inisiatif</td>
                                    <td>
                                        <strong>Tupoksi</strong><br>
                                        <small class="text-muted">Bekerja sesuai tupoksi yang telah di tentukan oleh Kepala
                                            Biro Kepesantrenan.</small>
                                    </td>
                                    <td class="text-center fw-bold">11%</td>
                                    <td><input type="number" name="skor_tupoksi" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Komunikasi</strong><br>
                                        <small class="text-muted">Mampu mengkomunikasikan ide secara lisan maupun tertulis
                                            sehingga dipahami pengurus lain.</small>
                                    </td>
                                    <td class="text-center fw-bold">7%</td>
                                    <td><input type="number" name="skor_komunikasi" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>

                                {{-- ASPEK 5 --}}
                                <tr>
                                    <td rowspan="2" class="text-center fw-bold fs-5">5</td>
                                    <td rowspan="2" class="fw-bold bg-light">Kepemimpinan dan Kerja Sama</td>
                                    <td>
                                        <strong>Koordinasi</strong><br>
                                        <small class="text-muted">Mampu mengatur diri dan berkoordinasi secara utuh dengan
                                            divisi lain dan satuan kerja terkait.</small>
                                    </td>
                                    <td class="text-center fw-bold">7%</td>
                                    <td><input type="number" name="skor_koordinasi" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Kebersamaan</strong><br>
                                        <small class="text-muted">Membangun kebersamaan sesama pengurus
                                            Wilayah/Daerah.</small>
                                    </td>
                                    <td class="text-center fw-bold">5%</td>
                                    <td><input type="number" name="skor_kebersamaan" class="form-control text-center"
                                            min="1" max="100" placeholder="0" required></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 border rounded bg-light">
                        <label class="form-label fw-bold">Catatan / Uraian Penilai (Opsional)</label>
                        <p class="small text-muted mb-2">Uraikan tanggapan dan penilaian Saudara mengenai yang bersangkutan
                            selama melaksanakan tugas.</p>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tulis catatan di sini..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-secondary me-2 shadow-sm">Batal</a>
                        <button type="submit" class="btn btn-success shadow-sm">
                            <i data-feather="save" style="width: 16px;"></i> Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather) feather.replace();
        });
    </script>
@endsection
