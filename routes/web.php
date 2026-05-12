<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ImportPegawaiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\JadwalPegawaiController;
use App\Http\Controllers\ImportRuanganController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DataCutiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ruangan
    Route::middleware(['permission:manage-ruangan'])->group(function () {
        Route::get('/ruangan/search', [RuanganController::class, 'search'])->name('ruangan.search');
        Route::get('/ruangan/search-kepala', [RuanganController::class, 'searchKepala'])->name('ruangan.search-kepala');
        Route::resource('ruangan', RuanganController::class);
        Route::get('/import-ruangan', [ImportRuanganController::class, 'index'])->name('ruangan.import.index');
        Route::post('/import-ruangan', [ImportRuanganController::class, 'import'])->name('ruangan.import.store');
        Route::get('/download-template-ruangan', [ImportRuanganController::class, 'downloadTemplate'])->name('ruangan.template.download');
    });

    // Shift
    Route::middleware(['permission:manage-shift'])->group(function () {
        Route::resource('shift', ShiftController::class);
    });

    // Pegawai
    Route::middleware(['permission:manage-pegawai'])->group(function () {
        Route::resource('pegawai', PegawaiController::class);
        Route::get('/import-pegawai', [ImportPegawaiController::class, 'index'])->name('pegawai.import.index');
        Route::post('/import-pegawai', [ImportPegawaiController::class, 'import'])->name('pegawai.import.store');
        Route::get('/download-template', [ImportPegawaiController::class, 'downloadTemplate'])->name('pegawai.template.download');
    });

    // User & Role Management
    Route::middleware(['permission:manage-users'])->group(function () {
        Route::resource('user', UserController::class);
    });
    
    Route::middleware(['permission:manage-roles'])->group(function () {
        Route::resource('role', RoleController::class);
    });

    // Jadwal Kerja
    Route::middleware(['permission:manage-jadwal'])->group(function () {
        Route::get('/jadwal', [JadwalPegawaiController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/create', [JadwalPegawaiController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalPegawaiController::class, 'store'])->name('jadwal.store');
        
        // AJAX Routes for Calendar
        Route::get('/jadwal/events/{pegawai_id}', [JadwalPegawaiController::class, 'getEvents'])->name('jadwal.events');
        Route::post('/jadwal/save-single', [JadwalPegawaiController::class, 'saveSingle'])->name('jadwal.save-single');
        Route::delete('/jadwal/delete-single', [JadwalPegawaiController::class, 'deleteSingle'])->name('jadwal.delete-single');
        Route::post('/jadwal/auto-fill', [JadwalPegawaiController::class, 'autoFill'])->name('jadwal.auto-fill');
        Route::post('/jadwal/reset', [JadwalPegawaiController::class, 'resetJadwal'])->name('jadwal.reset');
    });

    // Reports
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/report/{type}', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report-preview', [ReportController::class, 'preview'])->name('report.preview');
        Route::get('/report-export', [ReportController::class, 'export'])->name('report.export');
        Route::get('/report-pegawai', [ReportController::class, 'getPegawaiByRuangan'])->name('report.pegawai');
    });

    // User/General Routes
    Route::get('/view-pegawai', [PegawaiController::class, 'index'])->name('pegawai.view');

    // Data Cuti Detail
    Route::get('/data-cuti', [DataCutiController::class, 'index'])->name('cuti.index');
    Route::get('/data-cuti/export', [DataCutiController::class, 'export'])->name('cuti.export');
});
