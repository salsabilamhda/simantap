<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenagaKerjaController;
use App\Http\Controllers\SertifikasiController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PengaturanAdminController;
use App\Http\Controllers\ImportExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIMANTAP Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', function() {
    return redirect()->route('dashboard');
});

// Tenaga Kerja CRUD
Route::prefix('tenaga-kerja')->name('tenaga-kerja.')->group(function () {
    Route::get('/', [TenagaKerjaController::class, 'index'])->name('index');
    Route::post('/', [TenagaKerjaController::class, 'store'])->name('store');
    Route::put('/{id}', [TenagaKerjaController::class, 'update'])->name('update');
    Route::delete('/{id}', [TenagaKerjaController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/sertifikasi', [SertifikasiController::class, 'store'])->name('sertifikasi.store');
});

// Sertifikasi Delete
Route::delete('/sertifikasi/{id}', [SertifikasiController::class, 'destroy'])->name('sertifikasi.destroy');

// Master Data
Route::prefix('master-data')->name('master-data.')->group(function () {
    Route::get('/unit-layanan', [MasterDataController::class, 'unitLayanan'])->name('unit-layanan');
    Route::post('/unit-layanan', [MasterDataController::class, 'storeUnitLayanan'])->name('unit-layanan.store');
    Route::get('/perusahaan', [MasterDataController::class, 'perusahaan'])->name('perusahaan');
    Route::post('/perusahaan', [MasterDataController::class, 'storePerusahaan'])->name('perusahaan.store');
});

// Sistem & Akses
Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
    Route::get('/admin', [PengaturanAdminController::class, 'index'])->name('admin');
    Route::post('/admin', [PengaturanAdminController::class, 'store'])->name('admin.store');
});

// Import & Export
Route::get('/import-export', [ImportExportController::class, 'index'])->name('import-export');
Route::get('/import-export/export', [ImportExportController::class, 'exportCsv'])->name('import-export.export');
