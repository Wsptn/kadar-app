<?php

use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaerahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomisiliController;
use App\Http\Controllers\EntitasController;
use App\Http\Controllers\FungsionalTugasController;
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
use App\Http\Controllers\TugasEksternalController;
use App\Http\Controllers\TugasInternalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliAsuhController;
use App\Http\Controllers\WilayahController;
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
    });


    // ==========================================================
    // ==================== 2. MASTER DATA ======================
    // ==========================================================

    Route::prefix('master')->name('master.')->group(function () {

        // --- DOMISILI ---
        Route::prefix('domisili')->name('domisili.')->group(function () {
            Route::get('/', [DomisiliController::class, 'index'])->name('index');

            // Wilayah
            Route::get('/wilayah/create', [WilayahController::class, 'create'])->name('wilayah.create');
            Route::post('/wilayah/store', [WilayahController::class, 'store'])->name('wilayah.store');

            // Daerah
            Route::get('/daerah/create', [DaerahController::class, 'create'])->name('daerah.create');
            Route::post('/daerah/store', [DaerahController::class, 'store'])->name('daerah.store');

            // Kamar
            Route::get('/kamar/create', [KamarController::class, 'create'])->name('kamar.create');
            Route::post('/kamar/store', [KamarController::class, 'store'])->name('kamar.store');

            // Ajax Routes Domisili
            Route::get('/get-daerah/{wilayah_id}', function ($wilayah_id) {
                return \App\Models\Daerah::where('wilayah_id', $wilayah_id)->get();
            })->name('ajax.daerah');

            Route::get('/get-kamar/{daerah_id}', function ($daerah_id) {
                return \App\Models\Kamar::where('daerah_id', $daerah_id)->get();
            })->name('ajax.kamar');
        });

        // --- JABATAN & KELEMBAGAAN ---
        Route::prefix('jabatan')->name('jabatan.')->group(function () {
            Route::get('/', [JabatanController::class, 'index'])->name('index');

            // ================= ENTITAS =================
            Route::get('/entitas/create', [EntitasController::class, 'create'])->name('entitas.create');
            Route::post('/entitas/store', [EntitasController::class, 'store'])->name('entitas.store');
            Route::get('/entitas/{id}/edit', [EntitasController::class, 'edit'])->name('entitas.edit');
            Route::put('/entitas/{id}/update', [EntitasController::class, 'update'])->name('entitas.update');
            Route::delete('/entitas/{id}', [EntitasController::class, 'destroy'])->name('entitas.destroy');

            // ================= JABATAN =================
            Route::get('/jabatan/create', [JabatanController::class, 'create'])->name('jabatan.create');
            Route::post('/jabatan/store', [JabatanController::class, 'store'])->name('jabatan.store');
            Route::get('/jabatan/{id}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
            Route::put('/jabatan/{id}/update', [JabatanController::class, 'update'])->name('jabatan.update');
            Route::delete('/jabatan/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');

            // ================= JENIS JABATAN =================
            Route::get('/jenis/create', [JenisJabatanController::class, 'create'])->name('jenis.create');
            Route::post('/jenis/store', [JenisJabatanController::class, 'store'])->name('jenis.store');
            Route::get('/jenis/{id}/edit', [JenisJabatanController::class, 'edit'])->name('jenis.edit');
            Route::put('/jenis/{id}/update', [JenisJabatanController::class, 'update'])->name('jenis.update');
            Route::delete('/jenis/{id}', [JenisJabatanController::class, 'destroy'])->name('jenis.destroy');

            // ================= GRADE JABATAN =================
            Route::get('/grade/create', [GradeJabatanController::class, 'create'])->name('grade.create');
            Route::post('/grade/store', [GradeJabatanController::class, 'store'])->name('grade.store');
            Route::get('/grade/{id}/edit', [GradeJabatanController::class, 'edit'])->name('grade.edit');
            Route::put('/grade/{id}/update', [GradeJabatanController::class, 'update'])->name('grade.update');
            Route::delete('/grade/{id}', [GradeJabatanController::class, 'destroy'])->name('grade.destroy');

            // Ajax Routes Jabatan (Tetap Sama)
            Route::get('/get-jabatan/{entitas_id}', function ($entitas_id) {
                return \App\Models\Jabatan::where('entitas_id', $entitas_id)->get();
            })->name('jabatan.byEntitas');

            Route::get('/get-jenis/{jabatan_id}', function ($jabatan_id) {
                return \App\Models\JenisJabatan::where('jabatan_id', $jabatan_id)->get();
            })->name('jenis.byJabatan');

            Route::get('/get-grade/{jenis_jabatan_id}', function ($jenis_jabatan_id) {
                return \App\Models\GradeJabatan::where('jenis_jabatan_id', $jenis_jabatan_id)->get();
            })->name('grade.byJenis');
        });

        // --- TUGAS & FUNGSIONAL ---
        Route::prefix('fungsional_tugas')->name('tugas.')->group(function () {
            Route::get('/', [FungsionalTugasController::class, 'index'])->name('index');
            Route::get('/create', [FungsionalTugasController::class, 'create'])->name('create');
            Route::post('/store', [FungsionalTugasController::class, 'store'])->name('store');
            Route::get('/{id_tugas}/edit', [FungsionalTugasController::class, 'edit'])->name('edit');
            Route::put('/{id_tugas}', [FungsionalTugasController::class, 'update'])->name('update');
        });

        Route::prefix('tugas_internal')->name('internal.')->group(function () {
            Route::get('/', [TugasInternalController::class, 'index'])->name('index');
            Route::get('/create', [TugasInternalController::class, 'create'])->name('create');
            Route::post('/store', [TugasInternalController::class, 'store'])->name('store');
            Route::get('/{id_internal}/edit', [TugasInternalController::class, 'edit'])->name('edit');
            Route::put('/{id_internal}', [TugasInternalController::class, 'update'])->name('update');
        });

        Route::prefix('tugas_eksternal')->name('eksternal.')->group(function () {
            Route::get('/', [TugasEksternalController::class, 'index'])->name('index');
            Route::get('/create', [TugasEksternalController::class, 'create'])->name('create');
            Route::post('/store', [TugasEksternalController::class, 'store'])->name('store');
            Route::get('/{id_eksternal}/edit', [TugasEksternalController::class, 'edit'])->name('edit');
            Route::put('/{id_eksternal}', [TugasEksternalController::class, 'update'])->name('update');
        });

        // --- PENDIDIKAN & ANGKATAN ---
        Route::prefix('pendidikan')->name('pendidikan.')->group(function () {
            Route::get('/', [PendidikanController::class, 'index'])->name('index');
            Route::get('/create', [PendidikanController::class, 'create'])->name('create');
            Route::post('/store', [PendidikanController::class, 'store'])->name('store');
            Route::get('/{id_pendidikan}/edit', [PendidikanController::class, 'edit'])->name('edit');
            Route::put('/{id_pendidikan}', [PendidikanController::class, 'update'])->name('update');
        });

        Route::prefix('angkatan')->name('angkatan.')->group(function () {
            Route::get('/', [AngkatanController::class, 'index'])->name('index');
            Route::get('/create', [AngkatanController::class, 'create'])->name('create');
            Route::post('/store', [AngkatanController::class, 'store'])->name('store');
            Route::get('/{id_angkatan}/edit', [AngkatanController::class, 'edit'])->name('edit');
            Route::put('/{id_angkatan}', [AngkatanController::class, 'update'])->name('update');
        });

        // --- BERKAS ---
        Route::get('/berkas', [JenisBerkasController::class, 'index'])->name('berkas.index');
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
