<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicHomeController;

Route::get('/', [PublicHomeController::class, 'index'])->name('public.home');
Route::get('cari-mahasiswa', [PublicHomeController::class, 'search'])->name('public.search');
Route::get('cari-mahasiswa/{mahasiswa}', [PublicHomeController::class, 'detail'])->name('public.student-detail');
Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('notifications/open/{notification}', [\App\Http\Controllers\NotificationController::class, 'open'])->name('notifications.open');
Route::post('notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
Route::post('notifications/delete/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.delete');
Route::post('notifications/delete-all', [\App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('notifications.delete-all');

// Protected page (Operator)
Route::middleware(['operatorAuth'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Operator\DashboardController::class, 'index']);
    Route::post('password-update/{id}', [\App\Http\Controllers\Operator\DashboardController::class, 'update']);

    // Manajemen Perguruan Tinggi
    Route::get('pt-list', [\App\Http\Controllers\Operator\PtController::class, 'index']);
    Route::post('pt-upload', [\App\Http\Controllers\Operator\PtController::class, 'uploadExcel']);
    Route::get('pt-create', [\App\Http\Controllers\Operator\PtController::class, 'create']);
    Route::get('pt-edit/{id}', [\App\Http\Controllers\Operator\PtController::class, 'edit']);
    Route::get('pt-delete/{id}', [\App\Http\Controllers\Operator\PtController::class, 'delete']);
    Route::post('pt-store', [\App\Http\Controllers\Operator\PtController::class, 'store']);
    Route::post('pt-update/{id}', [\App\Http\Controllers\Operator\PtController::class, 'update']);

    // Manajemen User PT
    Route::get('userpt-list', [\App\Http\Controllers\Operator\UserptController::class, 'index']);
    Route::get('userpt-create', [\App\Http\Controllers\Operator\UserptController::class, 'create']);
    Route::get('userpt-edit/{id}', [\App\Http\Controllers\Operator\UserptController::class, 'edit']);
    Route::get('userpt-delete/{id}', [\App\Http\Controllers\Operator\UserptController::class, 'delete']);
    Route::get('userpt-show/{id}', [\App\Http\Controllers\Operator\UserptController::class, 'show']);
    Route::post('userpt-store', [\App\Http\Controllers\Operator\UserptController::class, 'store']);
    Route::post('userpt-update/{id}', [\App\Http\Controllers\Operator\UserptController::class, 'update']);
    Route::post('userpt-import', [\App\Http\Controllers\Operator\UserptController::class, 'import']);

    // Manajemen Informasi
    Route::get('informasi-list', [\App\Http\Controllers\Operator\InformasiController::class, 'index']);
    Route::get('informasi-create', [\App\Http\Controllers\Operator\InformasiController::class, 'create']);
    Route::get('informasi-edit/{id}', [\App\Http\Controllers\Operator\InformasiController::class, 'edit']);
    Route::get('informasi-delete/{id}', [\App\Http\Controllers\Operator\InformasiController::class, 'delete']);
    Route::get('informasi-show/{id}', [\App\Http\Controllers\Operator\InformasiController::class, 'show']);
    Route::post('informasi-store', [\App\Http\Controllers\Operator\InformasiController::class, 'store']);
    Route::post('informasi-update/{id}', [\App\Http\Controllers\Operator\InformasiController::class, 'update']);

    Route::get('activity-logs', [\App\Http\Controllers\Operator\ActivityLogController::class, 'index']);
    Route::get('operator/notifikasi', [\App\Http\Controllers\NotificationController::class, 'index']);

    // Pencairan
    Route::get('pencairan-list', [\App\Http\Controllers\Operator\PencairanController::class, 'index']);
    Route::get('operator/pencairan/unduh-excel', [\App\Http\Controllers\Operator\PencairanController::class, 'unduhExcel']);
    Route::get('pencairan-detail/{id}', [\App\Http\Controllers\Operator\PencairanController::class, 'detail']);
    Route::get('operator/pencairan/unduh-mahasiswa/{id}', [\App\Http\Controllers\Operator\PencairanController::class, 'unduhMahasiswa']);
    Route::post('pencairan/selesai/{id}', [\App\Http\Controllers\Operator\PencairanController::class, 'markSelesai']);
    Route::post('pencairan/ditolak/{id}', [\App\Http\Controllers\Operator\PencairanController::class, 'markDitolak']);
    Route::get('laporan-list', [\App\Http\Controllers\Operator\PencairanController::class, 'laporan']);
    Route::get('laporan-detail/{id}', [\App\Http\Controllers\Operator\PencairanController::class, 'detail']);
    Route::get('laporan', [\App\Http\Controllers\Operator\PencairanController::class, 'laporanHome']);
    Route::get('laporan-list/{id}', [\App\Http\Controllers\Operator\PencairanController::class, 'laporanByPt']);
    Route::get('Operator/pencairan/unduh-laporan', [\App\Http\Controllers\Operator\PencairanController::class, 'unduhLaporan']);
});

// Protected page (Admin)
Route::middleware(['adminAuth'])->group(function () {
    Route::get('home', [\App\Http\Controllers\Admin\HomeController::class, 'index']);
    Route::post('password-updates/{id}', [\App\Http\Controllers\Admin\HomeController::class, 'update']);

    // Manajemen Prodi
    Route::get('prodi-list', [\App\Http\Controllers\Admin\ProdiController::class, 'index']);
    Route::get('prodi-create', [\App\Http\Controllers\Admin\ProdiController::class, 'create']);
    Route::get('prodi-edit/{id}', [\App\Http\Controllers\Admin\ProdiController::class, 'edit']);
    Route::get('prodi-delete/{id}', [\App\Http\Controllers\Admin\ProdiController::class, 'delete']);
    Route::post('prodi-store', [\App\Http\Controllers\Admin\ProdiController::class, 'store']);
    Route::post('prodi-update/{id}', [\App\Http\Controllers\Admin\ProdiController::class, 'update']);
    Route::post('prodi-import', [\App\Http\Controllers\Admin\ProdiController::class, 'import']);
    Route::post('ajukan-mahasiswa-sync', [\App\Http\Controllers\Admin\PencairanController::class, 'sync_mahasiswa']);

    // Manajemen Mahasiswa
    Route::get('mahasiswa-list', [\App\Http\Controllers\Admin\MahasiswaController::class, 'index']);
    Route::get('mahasiswa-create', [\App\Http\Controllers\Admin\MahasiswaController::class, 'create']);
    Route::get('mahasiswa-edit/{id}', [\App\Http\Controllers\Admin\MahasiswaController::class, 'edit']);
    Route::get('mahasiswa-delete/{id}', [\App\Http\Controllers\Admin\MahasiswaController::class, 'delete']);
    Route::get('mahasiswa-show/{id}', [\App\Http\Controllers\Admin\MahasiswaController::class, 'show']);
    Route::post('mahasiswa-store', [\App\Http\Controllers\Admin\MahasiswaController::class, 'store']);
    Route::post('mahasiswa-update/{id}', [\App\Http\Controllers\Admin\MahasiswaController::class, 'update']);
    Route::post('mahasiswa/updateStatus', [\App\Http\Controllers\Admin\MahasiswaController::class, 'updateStatus']);
    Route::post('mahasiswa-import', [\App\Http\Controllers\Admin\MahasiswaController::class, 'import']);

    // 1
    Route::get('verifikasi-pembaharuan-status', [\App\Http\Controllers\Admin\PencairanController::class, 'index']);
    Route::get('permohonan-pencairan', [\App\Http\Controllers\Admin\PencairanController::class, 'permohonan']);
    Route::get('verifikasi-delete/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'delete']);
    Route::get('verifikasi-edit/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'edit']);
    Route::get('verifikasi-detail/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'detail']);
    Route::get('export-mahasiswa/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'export_mahasiswa']);
    Route::post('verifikasi-update/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'update']);
    Route::post('permohonan-store', [\App\Http\Controllers\Admin\PencairanController::class, 'store']);
    Route::get('admin/pencairan/unduh-excel', [\App\Http\Controllers\Admin\PencairanController::class, 'unduhExcel']);
    Route::get('admin/pencairan/unduh-mahasiswa/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'unduhMahasiswa']);
    Route::get('admin/laporan', [\App\Http\Controllers\Admin\PencairanController::class, 'laporanHome']);
    Route::get('admin/laporan-list', [\App\Http\Controllers\Admin\PencairanController::class, 'laporan']);
    Route::get('admin/laporan-list/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'laporanByPt']);
    Route::get('admin/laporan-detail/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'detail']);

    // 2
    Route::get('verifikasi-mahasiswa/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'verifikasi_mahasiswa']);
    Route::post('ajukan-mahasiswa', [\App\Http\Controllers\Admin\PencairanController::class, 'ajukanMahasiswa']);
    Route::get('admin/pencairan/draft', [\App\Http\Controllers\Admin\PencairanController::class, 'draft']);

    // 3
    Route::get('finalisasi-verifikasi/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'finalisasi_verifikasi']);
    Route::get('verifikasi-final/{id}', [\App\Http\Controllers\Admin\PencairanController::class, 'verifikasi_final']);

    // Informasi
    Route::get('papan-informasi', [\App\Http\Controllers\Admin\InformasiController::class, 'index']);
    Route::get('informasi-detail/{id}', [\App\Http\Controllers\Admin\InformasiController::class, 'show']);
    Route::get('admin/notifikasi', [\App\Http\Controllers\NotificationController::class, 'index']);
});
