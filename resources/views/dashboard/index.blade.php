@extends('layouts.app')

{{-- BAGIAN 1: STYLE & CSS (Termasuk FontAwesome untuk Icon)      --}}
@section('this-page-style')
    <style>
        /* --- SETTING WARNA & FONT --- */
        :root {
            --green-primary: #059669;
            /* Emerald 600 */
            --green-soft: #D1FAE5;
            --card-radius: 15px;
        }

        body {
            background-color: #f8fafc;
            /* Latar sedikit abu-abu bersih */
        }

        /* --- CARD MODERN STYLE --- */
        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem;
            font-weight: 700;
            color: var(--green-primary);
            display: flex;
            align-items: center;
        }

        /* Ikon kecil di header card grafik */
        .card-header i {
            margin-right: 10px;
            font-size: 1.1rem;
            color: #10B981;
        }

        /* --- STAT CARDS (KARTU ATAS DENGAN ICON) --- */
        .stat-card {
            position: relative;
            overflow: hidden;
            /* Agar icon tidak keluar kotak */
            border: none;
            border-radius: var(--card-radius);
            color: white;
            min-height: 140px;
            /* Tinggi minimum agar proporsional */
        }

        .stat-card .card-body {
            position: relative;
            z-index: 2;
            /* Text di atas icon */
            padding: 1.5rem;
        }

        .stat-card .card-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stat-card h2 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* --- FLOATING ICONS (IKON BESAR DI KANAN) --- */
        .stat-icon-bg {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 4.5rem;
            opacity: 0.25;
            /* Transparan */
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            z-index: 1;
        }

        /* Efek saat hover: Icon sedikit membesar dan berputar */
        .stat-card:hover .stat-icon-bg {
            transform: translateY(-50%) scale(1.1) rotate(5deg);
            opacity: 0.4;
        }

        /* --- GRADIENTS MODERN --- */
        .bg-gradient-emerald {
            background: linear-gradient(135deg, #059669 0%, #34D399 100%);
        }

        .bg-gradient-teal {
            background: linear-gradient(135deg, #0f766e 0%, #2dd4bf 100%);
        }

        .bg-gradient-lime {
            background: linear-gradient(135deg, #4d7c0f 0%, #a3e635 100%);
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #15803d 0%, #4ade80 100%);
        }

        /* --- CHART CONTAINERS --- */
        .chart-container,
        .chart-container-lg {
            position: relative;
            margin: auto;
            width: 100%;
        }

        .chart-container {
            height: 300px;
        }

        .chart-container-lg {
            height: 400px;
        }
    </style>
@endsection

{{-- BAGIAN 2: KONTEN UTAMA HALAMAN (HTML)                        --}}
@section('this-page-contain')
    <div class="container-fluid px-4 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Dashboard</h2>
            </div>
        </div>

        <div class="row mb-4">
            {{-- 1. Total Pengurus --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-emerald shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengurus</h5>
                        <h2 class="mb-0">{{ $totalPengurus ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- 2. Total Wali Asuh --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-teal shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Wali Asuh</h5>
                        <h2 class="mb-0">{{ $totalWaliAsuh ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- 3. Total Pengajar --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-lime shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengajar</h5>
                        <h2 class="mb-0">{{ $totalPengajar ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- 4. Total Mu'allim --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-green shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Mu'allim</h5>
                        <h2 class="mb-0">{{ $totalMuallim ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== BARIS 2: Grafik Utama ====== --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i>
                        <h5 class="card-title mb-0">Grafik Rincian Data Pengurus per Wilayah</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container-lg">
                            <canvas id="chartPengurus"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== BARIS 3: Rangkap Tugas Internal & Eksternal ====== --}}
        <div class="row mb-4">
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-pie"></i>
                        <h5 class="card-title mb-0">Grafik Rangkap Tugas Internal</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartEksternal"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-pie"></i>
                        <h5 class="card-title mb-0">Grafik Rangkap Tugas Eksternal</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartAktif"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== BARIS 4: Fungsional & Status Keaktifan ====== --}}
        <div class="row mb-4">
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <i class="fas fa-tasks"></i>
                        <h5 class="card-title mb-0">Grafik Fungsional Tugas</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartFungsional"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <i class="fas fa-toggle-on"></i>
                        <h5 class="card-title mb-0">Status Keaktifan Pengurus</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartInternal"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- BAGIAN 3: JAVASCRIPT (Chart.js Logic)                        --}}
@section('this-page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chart Pengurus per Wilayah
            const ctxPengurus = document.getElementById("chartPengurus");
            if (ctxPengurus) {
                new Chart(ctxPengurus, {
                    type: 'bar',
                    data: {
                        labels: @json($labelsWilayah),
                        datasets: [{
                            label: 'Jumlah Pengurus',
                            data: @json($dataWilayah),
                            backgroundColor: '#10B981',
                            borderRadius: 6,
                            barPercentage: 0.7
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Chart Rangkap Tugas Internal
            const ctxEksternal = document.getElementById("chartEksternal");
            if (ctxEksternal) {
                new Chart(ctxEksternal, {
                    type: "pie",
                    data: {
                        labels: ["Rangkap Internal", "Tidak Rangkap"],
                        datasets: [{
                            data: [{{ $rangkapInternal }}, {{ $tidakInternal }}],
                            backgroundColor: ["#10B981", "#E5E7EB"],
                            borderWidth: 1,
                            borderColor: "#fff"
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: "bottom"
                            }
                        }
                    }
                });
            }

            // Chart Rangkap Tugas Eksternal
            const ctxAktif = document.getElementById("chartAktif");
            if (ctxAktif) {
                new Chart(ctxAktif, {
                    type: "pie",
                    data: {
                        labels: ["Rangkap Eksternal", "Tidak Rangkap"],
                        datasets: [{
                            data: [{{ $rangkapEksternal }}, {{ $tidakEksternal }}],
                            backgroundColor: ["#14B8A6", "#E5E7EB"],
                            borderWidth: 1,
                            borderColor: "#fff"
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: "bottom"
                            }
                        }
                    }
                });
            }

            // Chart Fungsional (Horizontal Bar)
            const ctxFungsional = document.getElementById("chartFungsional");
            if (ctxFungsional) {
                new Chart(ctxFungsional, {
                    type: "bar",
                    data: {
                        labels: @json($labelFungsional),
                        datasets: [{
                            label: "Jumlah Pengurus",
                            data: @json($dataFungsional),
                            backgroundColor: "#059669",
                            borderRadius: 5
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Chart Status Keaktifan (Doughnut)
            var ctxInternal = document.getElementById("chartInternal");
            if (ctxInternal) {
                new Chart(ctxInternal, {
                    type: "doughnut",
                    data: {
                        labels: ["Aktif", "Tidak Aktif"],
                        datasets: [{
                            data: [{{ $jumlahAktif }}, {{ $jumlahNonAktif }}],
                            backgroundColor: ["#22c55e", "#fca5a5"],
                            borderWidth: 2,
                            borderColor: "#fff"
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: !window.MSInputMethodContext,
                        cutoutPercentage: 70,
                        legend: {
                            display: true,
                            position: "bottom"
                        }
                    }
                });
            }
        });
    </script>
@endsection
