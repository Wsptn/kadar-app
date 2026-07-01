@extends('layouts.app')

@section('this-page-style')
<style>
    :root {
        --portal-bg: #f8fafc;
        --card-radius: 24px;
        --soft-shadow: 0 4px 20px rgba(0,0,0,0.04);
        --theme-green: #28a745;
        --theme-green-dark: #1e7e34;
    }

    body {
        background-color: var(--portal-bg);
        background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
        background-size: 24px 24px;
    }

    .portal-card {
        background: #ffffff;
        border-radius: var(--card-radius);
        border: none;
        box-shadow: var(--soft-shadow);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .portal-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    /* Welcome Card */
    .welcome-avatar {
        width: 80px;
        height: 80px;
        background: var(--theme-green-dark);
        color: white;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        position: relative;
    }
    .welcome-avatar-dot {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 20px;
        height: 20px;
        background: #10B981;
        border: 4px solid #fff;
        border-radius: 50%;
    }

    .pill-soft {
        background: #f1f5f9;
        color: #475569;
        border-radius: 12px;
        padding: 10px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .pill-icon {
        width: 28px;
        height: 28px;
        background: #e2e8f0;
        color: var(--theme-green);
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .pill-icon svg {
        width: 14px;
        height: 14px;
    }

    /* Time Card */
    .time-card {
        border-radius: var(--card-radius);
        overflow: hidden;
    }
    .time-header {
        background: linear-gradient(135deg, var(--theme-green-dark) 0%, var(--theme-green) 100%);
        color: white;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .time-body {
        padding: 24px 20px;
        text-align: center;
    }
    .time-clock {
        font-size: 2.8rem;
        font-weight: 800;
        color: #1f2937;
        line-height: 1;
        margin-bottom: 8px;
    }
    .date-pill {
        background: #1f2937;
        color: white;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 8px;
    }
    
    .prayer-times {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    .prayer-box {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 8px;
        min-width: 65px;
        text-align: center;
    }
    .prayer-box.active {
        background: var(--theme-green);
        color: white;
    }
    .prayer-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
    .prayer-time { font-size: 0.85rem; font-weight: 700; }

    /* Stat Box */
    .stat-box {
        padding: 20px;
        position: relative;
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
        margin-bottom: 15px;
    }
    .stat-arrow {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 32px;
        height: 32px;
        background: #eff6ff;
        color: var(--theme-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .stat-arrow svg {
        width: 16px;
        height: 16px;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 2px;
    }
    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Colors Harmonious to Green */
    .bg-stat-1 { background: #1e7e34; }
    .bg-stat-2 { background: #28a745; }
    .bg-stat-3 { background: #20c997; }
    .bg-stat-4 { background: #17a2b8; }
    
    .bg-soft-blue { background: #dbeafe; color: #1e40af; }
    .bg-soft-green { background: #dcfce3; color: #166534; }
    .bg-soft-yellow { background: #fef08a; color: #854d0e; }
    .bg-soft-orange { background: #ffedd5; color: #9a3412; }
    .bg-soft-red { background: #fee2e2; color: #991b1b; }

    /* Chart Containers */
    .chart-container-lg {
        position: relative;
        margin: auto;
        width: 100%;
        height: 350px;
    }
    .chart-container {
        position: relative;
        margin: auto;
        width: 100%;
        height: 250px;
    }

    /* Section Title */
    .section-title {
        font-weight: 800;
        color: #111827;
        font-size: 1.3rem;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-subtitle {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
    }
    .section-icon {
        width: 40px;
        height: 40px;
        background: #eaf7ea;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--theme-green);
    }
    .section-icon svg {
        width: 20px;
        height: 20px;
    }
</style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4 pt-4">
        
        {{-- HERO SECTION --}}
        <div class="row mb-5">
            {{-- Welcome Card --}}
            <div class="col-xl-7 col-lg-7 mb-4 mb-lg-0">
                <div class="portal-card h-100 p-4 d-flex flex-column justify-content-center border-0 shadow-sm">
                    <div class="d-flex align-items-center mb-4">
                        <div class="welcome-avatar me-4 shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            <div class="welcome-avatar-dot"></div>
                        </div>
                        <div>
                            <div class="text-success fw-bold d-flex align-items-center gap-1" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase;">
                                <i data-feather="check-circle" style="width: 12px; height: 12px;"></i> SELAMAT DATANG
                            </div>
                            <h2 class="fw-bolder mb-1 text-dark" style="font-size: 1.8rem; text-transform: uppercase;">
                                {{ Auth::user()->name }}
                            </h2>
                            <p class="text-muted mb-0 fw-medium">
                                Hak Akses: <strong class="text-dark">{{ Auth::user()->level }}</strong>
                                @if(Auth::user()->wilayah) | Wilayah: {{ Auth::user()->wilayah }} @endif
                                @if(Auth::user()->daerah) | Daerah: {{ Auth::user()->daerah }} @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-3">
                        <div class="pill-soft">
                            <div class="pill-icon"><i data-feather="file-text"></i></div>
                            <div>
                                <div style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase;">Masa Penilaian</div>
                                <div>{{ $masaPenilaian }}</div>
                            </div>
                        </div>
                        <div class="pill-soft">
                            <div class="pill-icon"><i data-feather="calendar"></i></div>
                            <div>
                                <div style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase;">Waktu Pengisian</div>
                                <div>{{ $masaPengisian }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Time Card --}}
            <div class="col-xl-5 col-lg-5">
                <div class="portal-card time-card h-100 shadow-sm border-0">
                    <div class="time-header">
                        <div class="d-flex align-items-center gap-2">
                            <i data-feather="cloud"></i>
                            <div>
                                <div class="fw-bold fs-6" id="greetingText">Selamat Sore</div>
                                <div style="font-size: 0.75rem; opacity: 0.9;">Pantau aktivitas hari ini</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-white bg-opacity-25 rounded-pill px-3 py-1 gap-1">
                            <i data-feather="sun" style="width: 14px; height: 14px;"></i> <span class="fw-bold fs-6">Cerah</span>
                        </div>
                    </div>
                    <div class="time-body">
                        <div class="time-clock" id="clockDisplay">00:00:00</div>
                        <div class="date-pill" id="dateDisplay">MEMUAT TANGGAL...</div>
                        <div class="text-muted fw-medium small mb-2 d-flex align-items-center justify-content-center gap-1">
                            <i data-feather="moon" class="text-success" style="width: 14px; height: 14px;"></i> Kalender Masehi
                        </div>
                        
                        <div class="text-start ms-2 mt-4 d-flex align-items-center gap-1" style="font-size: 0.75rem; color: #64748b; font-weight: 700;">
                            <i data-feather="activity" class="text-success" style="width: 12px; height: 12px;"></i> INFO PENGURUS
                        </div>
                        <div class="prayer-times">
                            <div class="prayer-box">
                                <div class="prayer-title">Aktif</div>
                                <div class="prayer-time">{{ $jumlahAktif ?? 0 }}</div>
                            </div>
                            <div class="prayer-box">
                                <div class="prayer-title">Non-Aktif</div>
                                <div class="prayer-time">{{ $jumlahNonAktif ?? 0 }}</div>
                            </div>
                            <div class="prayer-box">
                                <div class="prayer-title">Dinilai</div>
                                <div class="prayer-time">{{ $totalKinerja ?? 0 }}</div>
                            </div>
                            <div class="prayer-box active">
                                <div class="prayer-title">Total</div>
                                <div class="prayer-time">{{ $totalPengurus ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION: STATISTIK SISTEM --}}
        <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <div class="section-icon shadow-sm me-3"><i data-feather="grid"></i></div>
                <div>
                    <h3 class="section-title">Aplikasi & Sistem</h3>
                    <div class="section-subtitle">Layanan & Integrasi Data Pokok</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="portal-card stat-box">
                        <div class="stat-arrow"><i data-feather="arrow-right"></i></div>
                        <div class="stat-icon bg-stat-1"><i data-feather="users"></i></div>
                        <div class="stat-value">{{ $totalWaliAsuh ?? 0 }}</div>
                        <div class="stat-label">Total Wali Asuh</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="portal-card stat-box">
                        <div class="stat-arrow"><i data-feather="arrow-right"></i></div>
                        <div class="stat-icon bg-stat-2"><i data-feather="book-open"></i></div>
                        <div class="stat-value">{{ $totalMuallim ?? 0 }}</div>
                        <div class="stat-label">Total Mu'allim</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="portal-card stat-box">
                        <div class="stat-arrow"><i data-feather="arrow-right"></i></div>
                        <div class="stat-icon bg-stat-3"><i data-feather="edit-3"></i></div>
                        <div class="stat-value">{{ $totalPengajar ?? 0 }}</div>
                        <div class="stat-label">Total Pengajar</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="portal-card stat-box">
                        <div class="stat-arrow"><i data-feather="arrow-right"></i></div>
                        <div class="stat-icon bg-stat-4"><i data-feather="user-check"></i></div>
                        <div class="stat-value">{{ $totalPengurus ?? 0 }}</div>
                        <div class="stat-label">Total Pengurus Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION: PENCAPAIAN KINERJA --}}
        <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <div class="section-icon shadow-sm me-3"><i data-feather="trending-up"></i></div>
                <div>
                    <h3 class="section-title">Pencapaian Kinerja</h3>
                    <div class="section-subtitle">Rekapitulasi Mutu Penilaian Terakhir</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="portal-card stat-box px-3 text-center">
                        <div class="stat-value fs-1 text-success">{{ $kinerjaA ?? 0 }}</div>
                        <div class="stat-label fw-bold mt-1 bg-soft-green py-1 rounded">MUTU A</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="portal-card stat-box px-3 text-center">
                        <div class="stat-value fs-1 text-primary">{{ $kinerjaB ?? 0 }}</div>
                        <div class="stat-label fw-bold mt-1 bg-soft-blue py-1 rounded">MUTU B</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="portal-card stat-box px-3 text-center">
                        <div class="stat-value fs-1 text-warning">{{ $kinerjaC ?? 0 }}</div>
                        <div class="stat-label fw-bold mt-1 bg-soft-yellow py-1 rounded">MUTU C</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="portal-card stat-box px-3 text-center">
                        <div class="stat-value fs-1" style="color: #ea580c;">{{ $kinerjaD ?? 0 }}</div>
                        <div class="stat-label fw-bold mt-1 bg-soft-orange py-1 rounded">MUTU D</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="portal-card stat-box px-3 text-center">
                        <div class="stat-value fs-1 text-danger">{{ $kinerjaE ?? 0 }}</div>
                        <div class="stat-label fw-bold mt-1 bg-soft-red py-1 rounded">MUTU E</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-12">
                    <div class="portal-card stat-box px-3 text-center border border-success border-2">
                        <div class="stat-value fs-1">{{ $totalKinerja ?? 0 }}</div>
                        <div class="stat-label fw-bold mt-1 bg-success text-white py-1 rounded">TOTAL MASUK</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION: LEADERBOARD & GRAFIK --}}
        <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <div class="section-icon shadow-sm me-3"><i data-feather="award"></i></div>
                <div>
                    <h3 class="section-title">Papan Peringkat & Analitik</h3>
                    <div class="section-subtitle">Top Wilayah & Daerah Terbaik Berdasarkan Skor</div>
                </div>
            </div>

            <div class="row mb-4">
                {{-- Top Wilayah --}}
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="portal-card h-100 overflow-hidden">
                        <div class="text-white p-3 d-flex align-items-center" style="background: #1e7e34;">
                            <i data-feather="star" class="text-warning me-3"></i>
                            <h5 class="mb-0 fw-bold text-white">Top 5 Wilayah Terbaik</h5>
                        </div>
                        <div class="table-responsive p-2">
                            <table class="table table-borderless table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small border-bottom">
                                        <th width="15%" class="text-center">RANK</th>
                                        <th>NAMA WILAYAH</th>
                                        <th class="text-center">SKOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topWilayah as $index => $wil)
                                        <tr>
                                            <td class="text-center">
                                                @if($index == 0) <i data-feather="award" style="color: #FFD700;"></i>
                                                @elseif($index == 1) <i data-feather="award" style="color: #C0C0C0;"></i>
                                                @elseif($index == 2) <i data-feather="award" style="color: #CD7F32;"></i>
                                                @else <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold text-dark">{{ $wil->nama_wilayah }}</td>
                                            <td class="text-center"><span class="badge bg-soft-green text-success px-3 py-2 fs-6 rounded-pill border">{{ number_format($wil->rata_rata, 2) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data penilaian</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Top Daerah --}}
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="portal-card h-100 overflow-hidden">
                        <div class="text-white p-3 d-flex align-items-center" style="background: #20c997;">
                            <i data-feather="star" class="text-warning me-3"></i>
                            <h5 class="mb-0 fw-bold text-white">Top 5 Daerah Terbaik</h5>
                        </div>
                        <div class="table-responsive p-2">
                            <table class="table table-borderless table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small border-bottom">
                                        <th width="15%" class="text-center">RANK</th>
                                        <th>NAMA DAERAH</th>
                                        <th class="text-center">SKOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topDaerah as $index => $dae)
                                        <tr>
                                            <td class="text-center">
                                                @if($index == 0) <i data-feather="award" style="color: #FFD700;"></i>
                                                @elseif($index == 1) <i data-feather="award" style="color: #C0C0C0;"></i>
                                                @elseif($index == 2) <i data-feather="award" style="color: #CD7F32;"></i>
                                                @else <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold text-dark">{{ $dae->nama_daerah }}</td>
                                            <td class="text-center"><span class="badge bg-soft-green text-success px-3 py-2 fs-6 rounded-pill border">{{ number_format($dae->rata_rata, 2) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data penilaian</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik --}}
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="portal-card">
                        <div class="p-4 border-bottom d-flex align-items-center gap-2">
                            <i data-feather="bar-chart-2" class="text-success"></i>
                            <h5 class="mb-0 fw-bold text-dark">Grafik Rincian Data Pengurus per Wilayah</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="chart-container-lg">
                                <canvas id="chartPengurus"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xl-4 col-lg-4 mb-4">
                    <div class="portal-card h-100">
                        <div class="p-4 border-bottom d-flex align-items-center gap-2">
                            <i data-feather="pie-chart" class="text-success"></i>
                            <h6 class="mb-0 fw-bold text-dark">Rangkap Tugas Internal</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="chart-container">
                                <canvas id="chartEksternal"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 mb-4">
                    <div class="portal-card h-100">
                        <div class="p-4 border-bottom d-flex align-items-center gap-2">
                            <i data-feather="pie-chart" class="text-info"></i>
                            <h6 class="mb-0 fw-bold text-dark">Rangkap Tugas Eksternal</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="chart-container">
                                <canvas id="chartAktif"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 mb-4">
                    <div class="portal-card h-100">
                        <div class="p-4 border-bottom d-flex align-items-center gap-2">
                            <i data-feather="list" class="text-warning"></i>
                            <h6 class="mb-0 fw-bold text-dark">Fungsional Tugas</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="chart-container">
                                <canvas id="chartFungsional"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // Inisialisasi Feather Icons khusus untuk konten dashboard yang baru di-render
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // LOGIKA JAM REAL TIME
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('clockDisplay').textContent = `${hours}:${minutes}:${seconds}`;

                const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
                document.getElementById('dateDisplay').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

                let greeting = 'Selamat Malam';
                if(now.getHours() >= 4 && now.getHours() < 10) greeting = 'Selamat Pagi';
                else if(now.getHours() >= 10 && now.getHours() < 15) greeting = 'Selamat Siang';
                document.getElementById('greetingText').textContent = greeting;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // KONFIGURASI CHART.JS (Aesthetic Modern / iPhone Style)
            
            // --- HACK UNTUK CHART.JS V2: MEMBUAT FULL PILL-SHAPE (BENTUK KAPSUL/IPHONE) ---
            if (Chart && Chart.elements && Chart.elements.Rectangle) {
                Chart.elements.Rectangle.prototype.draw = function() {
                    var ctx = this._chart.ctx;
                    var vm = this._view;
                    var left, right, top, bottom, signX, signY, borderSkipped;
                    var borderWidth = vm.borderWidth;

                    if (!vm.horizontal) {
                        left = vm.x - vm.width / 2;
                        right = vm.x + vm.width / 2;
                        top = vm.y;
                        bottom = vm.base;
                        signX = 1;
                        signY = bottom > top ? 1 : -1;
                        borderSkipped = vm.borderSkipped || 'bottom';
                    } else {
                        left = vm.base;
                        right = vm.x;
                        top = vm.y - vm.height / 2;
                        bottom = vm.y + vm.height / 2;
                        signX = right > left ? 1 : -1;
                        signY = 1;
                        borderSkipped = vm.borderSkipped || 'left';
                    }

                    ctx.beginPath();
                    ctx.fillStyle = vm.backgroundColor;
                    ctx.strokeStyle = vm.borderColor;
                    ctx.lineWidth = borderWidth;

                    var cornersRadius = this._chart.config.options.cornerRadius || 20;

                    if (!vm.horizontal) {
                        var width = right - left;
                        var r = cornersRadius;
                        if (r > width/2) r = width/2;
                        
                        // Full pill vertical
                        ctx.moveTo(left, bottom - r);
                        ctx.lineTo(left, top + r);
                        ctx.quadraticCurveTo(left, top, left + r, top);
                        ctx.lineTo(right - r, top);
                        ctx.quadraticCurveTo(right, top, right, top + r);
                        ctx.lineTo(right, bottom - r);
                        ctx.quadraticCurveTo(right, bottom, right - r, bottom);
                        ctx.lineTo(left + r, bottom);
                        ctx.quadraticCurveTo(left, bottom, left, bottom - r);
                    } else {
                        var height = bottom - top;
                        var r = cornersRadius;
                        if (r > height/2) r = height/2;
                        
                        // Full pill horizontal
                        ctx.moveTo(left + r, top);
                        ctx.lineTo(right - r, top);
                        ctx.quadraticCurveTo(right, top, right, top + r);
                        ctx.lineTo(right, bottom - r);
                        ctx.quadraticCurveTo(right, bottom, right - r, bottom);
                        ctx.lineTo(left + r, bottom);
                        ctx.quadraticCurveTo(left, bottom, left, bottom - r);
                        ctx.lineTo(left, top + r);
                        ctx.quadraticCurveTo(left, top, left + r, top);
                    }
                    ctx.fill();
                    if (borderWidth) { ctx.stroke(); }
                };
            }
            // ---------------------------------------------------------------------------------

            const tooltipConfig = {
                backgroundColor: '#111827',
                titleFontColor: '#ffffff',
                bodyFontColor: '#ffffff',
                titleFontSize: 13,
                bodyFontSize: 13,
                cornerRadius: 10,
                displayColors: false,
                xPadding: 14,
                yPadding: 12,
                caretSize: 6,
                caretPadding: 10,
                shadowOffsetX: 0,
                shadowOffsetY: 4,
                shadowBlur: 10,
                shadowColor: 'rgba(0,0,0,0.1)'
            };

            const v2TicksConfig = {
                beginAtZero: true,
                min: 0,
                suggestedMax: 5,
                stepSize: 1,
                fontColor: '#94a3b8',
                callback: function(value) {
                    if (Number.isInteger(value)) {
                        if (value === 0 || value === 1 || value % 5 === 0) {
                            return value;
                        }
                    }
                }
            };

            // 1. Chart Pengurus per Wilayah (Vertikal Bar)
            const ctxPengurus = document.getElementById("chartPengurus");
            if (ctxPengurus) {
                new Chart(ctxPengurus, {
                    type: 'bar',
                    data: {
                        labels: @json($labelsWilayah),
                        datasets: [{
                            label: 'Jumlah Pengurus',
                            data: @json($dataWilayah),
                            backgroundColor: '#20c997', // Theme Green-Teal
                            hoverBackgroundColor: '#1e7e34', // Hover Green Dark
                            barThickness: 45, // Jauh lebih tebal (mirip iPhone)
                            borderSkipped: false,
                            borderRadius: 20 // Berfungsi jika Chart.js versi 3+ atau ada plugin
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: { display: false },
                        tooltips: tooltipConfig,
                        cornerRadius: 20,
                        scales: {
                            yAxes: [{ 
                                ticks: v2TicksConfig, 
                                gridLines: { display: true, color: 'rgba(150,150,150,0.1)', drawBorder: false } 
                            }],
                            xAxes: [{ 
                                ticks: { fontColor: '#94a3b8' },
                                gridLines: { display: false, drawBorder: false } 
                            }]
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

            // 2. Chart Fungsional (Horizontal Bar)
            const ctxFungsional = document.getElementById("chartFungsional");
            if (ctxFungsional) {
                new Chart(ctxFungsional, {
                    type: "horizontalBar",
                    data: {
                        labels: @json($labelFungsional),
                        datasets: [{
                            label: "Jumlah",
                            data: @json($dataFungsional),
                            backgroundColor: "#10b981", // Emerald green
                            hoverBackgroundColor: '#059669',
                            barThickness: 25 // Lebih tebal
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: { display: false },
                        tooltips: tooltipConfig,
                        scales: {
                            xAxes: [{ 
                                ticks: v2TicksConfig, 
                                gridLines: { display: true, color: 'rgba(150,150,150,0.1)', drawBorder: false } 
                            }],
                            yAxes: [{ 
                                ticks: { fontColor: '#94a3b8' },
                                gridLines: { display: false, drawBorder: false } 
                            }]
                        }
                    }
                });
            }

            // 3. Chart Rangkap Tugas Internal (Doughnut Gauge / Half Circle)
            const ctxEksternal = document.getElementById("chartEksternal");
            if (ctxEksternal) {
                new Chart(ctxEksternal, {
                    type: "doughnut",
                    data: {
                        labels: ["Rangkap Internal", "Tidak Rangkap"],
                        datasets: [{
                            data: [{{ $rangkapInternal }}, {{ $tidakInternal }}],
                            backgroundColor: ["#28a745", "rgba(150, 150, 150, 0.15)"], // Theme green & translucent track
                            hoverBackgroundColor: ["#1e7e34", "rgba(150, 150, 150, 0.25)"],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutoutPercentage: 60, // Jauh lebih tebal (dari 75)
                        rotation: 1 * Math.PI,
                        circumference: 1 * Math.PI,
                        legend: { 
                            position: "bottom",
                            labels: { usePointStyle: true, fontColor: '#94a3b8', padding: 20 }
                        },
                        tooltips: tooltipConfig,
                        animation: { animateScale: true, animateRotate: true }
                    }
                });
            }

            // 4. Chart Rangkap Tugas Eksternal (Doughnut Gauge / Half Circle)
            const ctxAktif = document.getElementById("chartAktif");
            if (ctxAktif) {
                new Chart(ctxAktif, {
                    type: "doughnut",
                    data: {
                        labels: ["Rangkap Eksternal", "Tidak Rangkap"],
                        datasets: [{
                            data: [{{ $rangkapEksternal }}, {{ $tidakEksternal }}],
                            backgroundColor: ["#0ea5e9", "rgba(150, 150, 150, 0.15)"], // iOS blue & translucent track
                            hoverBackgroundColor: ["#0284c7", "rgba(150, 150, 150, 0.25)"],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutoutPercentage: 60, // Jauh lebih tebal (dari 75)
                        rotation: 1 * Math.PI,
                        circumference: 1 * Math.PI,
                        legend: { 
                            position: "bottom",
                            labels: { usePointStyle: true, fontColor: '#94a3b8', padding: 20 }
                        },
                        tooltips: tooltipConfig,
                        animation: { animateScale: true, animateRotate: true }
                    }
                });
            }

        });
    </script>
@endsection
