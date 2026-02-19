@extends('layouts.app')

{{-- ========================================== --}}
{{-- BAGIAN 1: STYLE & CSS                      --}}
{{-- ========================================== --}}
@section('this-page-style')
    <style>
        :root {
            --green-primary: #059669;
            --green-soft: #D1FAE5;
            --card-radius: 15px;
        }

        body {
            background-color: #f8fafc;
        }

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

        .card-header i {
            margin-right: 10px;
            font-size: 1.1rem;
            color: #10B981;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: var(--card-radius);
            color: white;
            min-height: 140px;
        }

        .stat-card .card-body {
            position: relative;
            z-index: 2;
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

        .chart-container-lg {
            position: relative;
            margin: auto;
            width: 100%;
            height: 400px;
        }

        .chart-container {
            position: relative;
            margin: auto;
            width: 100%;
            height: 300px;
        }
    </style>
@endsection

{{-- ========================================== --}}
{{-- BAGIAN 2: KONTEN UTAMA (HTML)              --}}
{{-- ========================================== --}}
@section('this-page-contain')
    <div class="container-fluid px-4 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Dashboard</h2>
            </div>
        </div>

        {{-- BARIS 1: 4 Kartu Statistik --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-emerald shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengurus</h5>
                        <h2 class="mb-0">{{ $totalPengurus ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-teal shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Wali Asuh</h5>
                        <h2 class="mb-0">{{ $totalWaliAsuh ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-lime shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengajar</h5>
                        <h2 class="mb-0">{{ $totalPengajar ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bg-gradient-green shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Mu'allim</h5>
                        <h2 class="mb-0">{{ $totalMuallim ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 2: Grafik Utama (Wilayah) --}}
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

        {{-- BARIS 3: Grafik Rangkap Tugas (Pie) --}}
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

        {{-- BARIS 4: Fungsional & Status Keaktifan --}}
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

{{-- ========================================== --}}
{{-- BAGIAN 3: JAVASCRIPT (Chart.js Logic)      --}}
{{-- ========================================== --}}
@section('this-page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Konfigurasi khusus untuk Chart.js Versi 2
            // Ini akan memaksa sumbu nilai (value axis) mutlak mulai dari 0
            const v2TicksConfig = {
                beginAtZero: true,
                min: 0,
                suggestedMax: 5,
                stepSize: 1,
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
                            backgroundColor: '#10B981',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{ // <-- Penulisan khusus versi 2
                                ticks: v2TicksConfig,
                                gridLines: {
                                    display: true
                                }
                            }],
                            xAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }]
                        }
                    }
                });
            }

            // 2. Chart Fungsional (Horizontal Bar)
            const ctxFungsional = document.getElementById("chartFungsional");
            if (ctxFungsional) {
                new Chart(ctxFungsional, {
                    type: "horizontalBar", // <-- Tipe khusus versi 2 untuk grafik tidur
                    data: {
                        labels: @json($labelFungsional),
                        datasets: [{
                            label: "Jumlah",
                            data: @json($dataFungsional),
                            backgroundColor: "#059669",
                            borderRadius: 5
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{ // <-- Karena grafiknya tidur, sumbu X yang dipaksa mulai dari 0
                                ticks: v2TicksConfig,
                                gridLines: {
                                    display: true
                                }
                            }],
                            yAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }]
                        }
                    }
                });
            }

            // 3. Chart Rangkap Tugas Internal (Pie)
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
                        legend: {
                            position: "bottom"
                        }
                    }
                });
            }

            // 4. Chart Rangkap Tugas Eksternal (Pie)
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
                        legend: {
                            position: "bottom"
                        }
                    }
                });
            }

            // 5. Chart Status Keaktifan (Doughnut)
            const ctxInternal = document.getElementById("chartInternal");
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
                        cutoutPercentage: 70, // <-- Penulisan khusus versi 2
                        legend: {
                            position: "bottom"
                        }
                    }
                });
            }

        });
    </script>
@endsection
