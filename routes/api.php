<?php

use App\Http\Controllers\Api\MobileController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::get('ping', [MobileController::class, 'ping']);
    Route::get('stats', [MobileController::class, 'stats']);
    Route::get('mahasiswa/search', [MobileController::class, 'searchMahasiswa']);
    Route::post('login', [MobileController::class, 'login']);
    Route::get('dashboard', [MobileController::class, 'dashboard']);

    Route::get('mahasiswa', [MobileController::class, 'getMahasiswas']);
    Route::post('mahasiswa', [MobileController::class, 'storeMahasiswa']);
    Route::put('mahasiswa/{id}', [MobileController::class, 'updateMahasiswa']);
    Route::delete('mahasiswa/{id}', [MobileController::class, 'deleteMahasiswa']);
    Route::get('dynamic/{table}', [MobileController::class, 'getDynamicTable']);
    Route::get('dynamic/{table}/schema', [MobileController::class, 'getDynamicSchema']);
    Route::post('dynamic/{table}', [MobileController::class, 'storeDynamicTable']);
    Route::put('dynamic/{table}/{id}', [MobileController::class, 'updateDynamicTable']);
    Route::delete('dynamic/{table}/{id}', [MobileController::class, 'deleteDynamicTable']);
    Route::post('admin/pencairan', [MobileController::class, 'storeAdminPencairan']);

    // Verifikasi Pencairan (Admin)
    Route::get('verifikasi-pencairan', [MobileController::class, 'getVerifikasiPencairan']);
    Route::post('verifikasi-pencairan/{id}/accept', [MobileController::class, 'verifikasiAccept']);
    Route::post('verifikasi-pencairan/{id}/reject', [MobileController::class, 'verifikasiReject']);

    // Laporan
    Route::get('laporan', [MobileController::class, 'getLaporan']);

    // Notifikasi
    Route::get('notifikasi', [MobileController::class, 'getNotifikasi']);

});
