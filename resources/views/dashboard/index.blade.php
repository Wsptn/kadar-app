@extends('layouts.app')

@section('this-page-style')
    {{-- Additional styles for dashboard if needed --}}
    <style>
        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .chart-container-lg {
            position: relative;
            height: 400px;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Dashboard</h1>

        {{-- ====== BARIS 1: Statistik singkat ====== --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card text-white bg-success mb-3 shadow stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengurus</h5>
                        <h2 class="mb-0">{{ $totalPengurus ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card text-white bg-primary mb-3 shadow stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Wali Asuh</h5>
                        <h2 class="mb-0">{{ $totalWaliAsuh ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card text-white bg-warning mb-3 shadow stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengajar</h5>
                        <h2 class="mb-0">{{ $totalPengajar ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card text-white bg-info mb-3 shadow stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Mu'allim</h5>
                        <h2 class="mb-0">{{ $totalMuallim ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== BARIS 2: Grafik Utama (Rincian Data Pengurus per Daerah) ====== --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
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

@section('this-page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chart Pengurus per Wilayah (Bar Chart)
            const ctxPengurus = document.getElementById("chartPengurus");
            if (!ctxPengurus) return;

            new Chart(ctxPengurus, {
                type: 'bar',
                data: {
                    labels: @json($labelsWilayah),
                    datasets: [{
                        label: 'Jumlah Pengurus',
                        data: @json($dataWilayah),
                        backgroundColor: '#2563eb',
                        borderRadius: 6
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

            // Chart Rangkap Tugas Internal (Pie Chart)
            const ctxEksternal = document.getElementById("chartEksternal");
            if (ctxEksternal) {
                new Chart(ctxEksternal, {
                    type: "pie",
                    data: {
                        labels: ["Rangkap Internal", "Tidak Rangkap"],
                        datasets: [{
                            data: [
                                {{ $rangkapInternal }},
                                {{ $tidakInternal }}
                            ],
                            backgroundColor: ["#2563eb", "#e5e7eb"],
                            borderWidth: 2,
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

            // Chart Rangkap Tugas Eksternal (Pie Chart)
            const ctxAktif = document.getElementById("chartAktif");
            if (ctxAktif) {
                new Chart(ctxAktif, {
                    type: "pie",
                    data: {
                        labels: ["Rangkap Eksternal", "Tidak Rangkap"],
                        datasets: [{
                            data: [
                                {{ $rangkapEksternal }},
                                {{ $tidakEksternal }}
                            ],
                            backgroundColor: ["#ef4444", "#e5e7eb"],
                            borderWidth: 2,
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

            // Chart Fungsional (Horizontal Bar Chart)
            const ctxFungsional = document.getElementById("chartFungsional");
            if (ctxFungsional) {
                new Chart(ctxFungsional, {
                    type: "bar",
                    data: {
                        labels: @json($labelFungsional),
                        datasets: [{
                            label: "Jumlah Pengurus",
                            data: @json($dataFungsional),
                            backgroundColor: "#2563eb",
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y', // ✅ horizontal bar
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

            // Chart Status Keaktifan (Doughnut Chart)
            var ctxInternal = document.getElementById("chartInternal");
            if (ctxInternal) {
                new Chart(ctxInternal, {
                    type: "doughnut",
                    data: {
                        labels: ["Aktif", "Tidak Aktif"],
                        datasets: [{
                            data: [{{ $jumlahAktif }}, {{ $jumlahNonAktif }}],
                            backgroundColor: [
                                window.theme ? window.theme.success : "#22c55e",
                                window.theme ? window.theme.danger : "#ef4444"
                            ],
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
