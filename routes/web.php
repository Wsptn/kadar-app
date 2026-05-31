<?php

use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaerahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomisiliController;
use App\Http\Controllers\EntitasController;
use App\Http\Controllers\GradeJabatanController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JenisBerkasController;
use App\Http\Controllers\JenisJabatanController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\MuallimController;
use App\Http\Controllers\PendidikanController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\PengurusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliAsuhController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\MasterTugasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KinerjaController;

/*
|--------------------------------------------------------------------------
| ROUTE KHUSUS TAMU (BELUM LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

/*

| ROUTE AUTH (WAJIB LOGIN)

*/
Route::middleware('auth')->group(function () {

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // ==========================================================
    // ==================== 1. DATA POKOK =======================
    // ==========================================================

    // PENGURUS
    Route::prefix('pengurus')->name('pokok.pengurus.')->group(function () {
        Route::get('/', [PengurusController::class, 'index'])->name('index');
        Route::get('/create', [PengurusController::class, 'create'])->name('create');
        Route::post('/store', [PengurusController::class, 'store'])->name('store');
        Route::get('/export', [PengurusController::class, 'export'])->name('export');

        Route::get('/{id}/edit', [PengurusController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PengurusController::class, 'update'])->name('update');
        Route::delete('/{id}', [PengurusController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [PengurusController::class, 'show'])->name('show');
    });

    // WALI ASUH
    Route::get('/waliasuh', [WaliAsuhController::class, 'index'])->name('pokok.waliasuh.index');

    // PENGAJAR
    Route::get('/pengajar', [PengajarController::class, 'index'])->name('pokok.pengajar.index');

    // MU'ALLIM
    Route::get('/muallim', [MuallimController::class, 'index'])->name('pokok.muallim.index');

    // KINERJA DAN REKOMENDASI
    Route::prefix('kinerja')->name('pokok.kinerja.')->group(function () {
        Route::get('/', [KinerjaController::class, 'index'])->name('index');
        Route::get('/create', [KinerjaController::class, 'create'])->name('create');
        Route::post('/store', [KinerjaController::class, 'store'])->name('store');
        Route::get('/{id}/riwayat', [KinerjaController::class, 'show'])->name('show');
        Route::put('/{id}/tandai-sudah', [KinerjaController::class, 'markAsHandled'])->name('mark_handled');
        Route::get('/{id}/export-pdf', [KinerjaController::class, 'exportPdf'])->name('export_pdf');
    });


    // ==========================================================
    // ==================== 2. MASTER DATA ======================
    // ==========================================================

    Route::prefix('master')->name('master.')->group(function () {

        // --- DOMISILI ---
        Route::prefix('domisili')->name('domisili.')->group(function () {
            Route::get('/', [DomisiliController::class, 'index'])->name('index');
            Route::get('/create', [DomisiliController::class, 'create'])->name('create');
            Route::post('/store', [DomisiliController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DomisiliController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [DomisiliController::class, 'update'])->name('update');
            Route::delete('/{id}/destroy', [DomisiliController::class, 'destroy'])->name('destroy');

            // Ajax Routes Domisili
            Route::get('/get-daerah/{wilayah}', function ($wilayah) {
                return \App\Models\Domisili::where('wilayah', $wilayah)->select('daerah')->distinct()->get();
            })->name('ajax.daerah');

            Route::get('/get-kamar/{wilayah}/{daerah}', function ($wilayah, $daerah) {
                return \App\Models\Domisili::where('wilayah', $wilayah)->where('daerah', $daerah)->get();
            })->name('ajax.kamar');
        });

        // --- INSTRUMEN PENILAIAN ---
        Route::prefix('instrumen')->name('instrumen.')->group(function () {
            Route::get('/', [\App\Http\Controllers\MasterInstrumenController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\MasterInstrumenController::class, 'create'])->name('create');
            Route::post('/store', [\App\Http\Controllers\MasterInstrumenController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\MasterInstrumenController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [\App\Http\Controllers\MasterInstrumenController::class, 'update'])->name('update');
            Route::delete('/{id}/destroy', [\App\Http\Controllers\MasterInstrumenController::class, 'destroy'])->name('destroy');
        });

        // --- STRUKTUR JABATAN (FLAT/PIPIH) ---
        Route::prefix('struktur_jabatan')->name('struktur_jabatan.')->group(function () {
            Route::get('/', [\App\Http\Controllers\MasterStrukturJabatanController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\MasterStrukturJabatanController::class, 'create'])->name('create');
            Route::post('/store', [\App\Http\Controllers\MasterStrukturJabatanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\MasterStrukturJabatanController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [\App\Http\Controllers\MasterStrukturJabatanController::class, 'update'])->name('update');
            Route::delete('/{id}/destroy', [\App\Http\Controllers\MasterStrukturJabatanController::class, 'destroy'])->name('destroy');

            // Ajax Routes Struktur Jabatan
            Route::get('/get-jabatan/{entitas}', function ($entitas) {
                return \App\Models\MasterStrukturJabatan::where('entitas', $entitas)->select('jabatan')->distinct()->get();
            })->name('ajax.jabatan');

            Route::get('/get-jenis/{entitas}/{jabatan}', function ($entitas, $jabatan) {
                return \App\Models\MasterStrukturJabatan::where('entitas', $entitas)->where('jabatan', $jabatan)->select('jenis_jabatan')->distinct()->get();
            })->name('ajax.jenis');

            Route::get('/get-grade/{entitas}/{jabatan}/{jenis}', function ($entitas, $jabatan, $jenis) {
                return \App\Models\MasterStrukturJabatan::where('entitas', $entitas)->where('jabatan', $jabatan)->where('jenis_jabatan', $jenis)->select('id', 'grade')->get();
            })->name('ajax.grade');
        });

        // --- TUGAS (FUNGSIONAL, INTERNAL, EKSTERNAL) ---
        Route::prefix('tugas')->name('tugas.')->group(function () {
            Route::get('/', [MasterTugasController::class, 'index'])->name('index');
            
            Route::middleware(['role:Admin,Biktren,Wilayah'])->group(function () {
                Route::get('/create', [MasterTugasController::class, 'create'])->name('create');
                Route::post('/store', [MasterTugasController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [MasterTugasController::class, 'edit'])->name('edit');
                Route::put('/{id}', [MasterTugasController::class, 'update'])->name('update');
                Route::delete('/{id}', [MasterTugasController::class, 'destroy'])->name('destroy');
            });
        });

        // --- PENDIDIKAN & ANGKATAN ---
        Route::prefix('pendidikan')->name('pendidikan.')->group(function () {
            Route::get('/', [PendidikanController::class, 'index'])->name('index');
            Route::get('/create', [PendidikanController::class, 'create'])->name('create');
            Route::post('/store', [PendidikanController::class, 'store'])->name('store');
            Route::get('/{id_pendidikan}/edit', [PendidikanController::class, 'edit'])->name('edit');
            Route::put('/{id_pendidikan}', [PendidikanController::class, 'update'])->name('update');
            Route::delete('/{id_pendidikan}', [PendidikanController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('angkatan')->name('angkatan.')->group(function () {
            Route::get('/', [AngkatanController::class, 'index'])->name('index');
            Route::get('/create', [AngkatanController::class, 'create'])->name('create');
            Route::post('/store', [AngkatanController::class, 'store'])->name('store');
            Route::get('/{id_angkatan}/edit', [AngkatanController::class, 'edit'])->name('edit');
            Route::put('/{id_angkatan}', [AngkatanController::class, 'update'])->name('update');
            Route::delete('/{id_angkatan}', [AngkatanController::class, 'destroy'])->name('destroy');
        });

        // --- BERKAS ---
        // Route::get('/berkas', [JenisBerkasController::class, 'index'])->name('berkas.index');
    });


    // ==========================================================
    // ==================== 3. MANAJEMEN AKUN ===================
    // ==========================================================

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::middleware(['role:Admin,Biktren'])->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/{id}/reset-password', [UserController::class, 'resetPasswordForm'])
                ->name('reset-password');
            Route::put('/{id}/reset-password', [UserController::class, 'processResetPassword'])
                ->name('reset-password.process');
        });

        Route::middleware(['role:Admin'])->group(function () {
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('toggle-status');
        });
    });

    // ==========================================================
    // ==================== 4. PROFILE SAYA =====================
    // ==========================================================

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});
