<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProgresController;
use App\Http\Controllers\Api\AbsensiController;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPendaftarController;
use App\Http\Controllers\AdminMahasiswaController;
use App\Http\Controllers\AdminPenilaianController;
use App\Http\Controllers\AdminKelompokController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminLaporanController;
use App\Http\Controllers\KuotaController;

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Login - Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | MAHASISWA
    |--------------------------------------------------------------------------
    */
    Route::post('/register-peserta', [MahasiswaController::class, 'registerPeserta']);

    Route::get('/profile', [MahasiswaController::class, 'getProfile']);
    Route::post('/profile/update', [MahasiswaController::class, 'updateProfile']);
    Route::post('/profile/upload-berkas', [MahasiswaController::class, 'uploadBerkas']);
    Route::post('/tugas-akhir/upload', [MahasiswaController::class, 'uploadTugas']);

    Route::get('/progres', [ProgresController::class, 'index']);
    Route::post('/progres/simpan', [ProgresController::class, 'store']);
    Route::get('/kuota-magang', [KuotaController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | ABSENSI
    |--------------------------------------------------------------------------
    */
    Route::post('/absensi/verify-face', [AbsensiController::class, 'verifyFace']);
    Route::get('/absensi/hari-ini', [AbsensiController::class, 'cekAbsenHariIni']);
    Route::get('/absensi/riwayat', [AbsensiController::class, 'getRiwayat']);

    Route::get('/download-surat', [MahasiswaController::class, 'downloadFile']);
    Route::get('/download/{folder}/{filename}', [MahasiswaController::class, 'downloadFile']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (Login + Middleware Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum','admin'])->group(function () {
        Route::put('/admin/mahasiswa/{id}/approve', [AdminMahasiswaController::class, 'approve']);
        Route::put('/admin/mahasiswa/{id}/reject', [AdminMahasiswaController::class, 'reject']);
    });

    Route::middleware('admin')->prefix('admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        /*
        |--------------------------------------------------------------------------
        | PENDAFTAR
        |--------------------------------------------------------------------------
        */
        Route::get('/pendaftar', [AdminPendaftarController::class, 'index']);
        Route::put('/pendaftar/{id}/status', [AdminPendaftarController::class, 'updateStatus']);
        Route::put('/pendaftar/{id}/approve', [AdminPendaftarController::class, 'approve']);
        Route::put('/pendaftar/{id}/reject', [AdminPendaftarController::class, 'reject']);
        Route::put('/pendaftar/{id}/konfirmasi-berkas', [AdminPendaftarController::class, 'konfirmasiBerkas']);
        Route::put('/pendaftar/{id}/terima', [AdminPendaftarController::class, 'terima']);

        /*
        |--------------------------------------------------------------------------
        | MAHASISWA
        |--------------------------------------------------------------------------
        */
        Route::get('/mahasiswa', [AdminMahasiswaController::class, 'index']);
        Route::get('/mahasiswa/{id}', [AdminMahasiswaController::class, 'show']);
        Route::put('/mahasiswa/{id}/approve', [AdminMahasiswaController::class, 'approve']);
        Route::put('/mahasiswa/{id}/reject', [AdminMahasiswaController::class, 'reject']);
        Route::put('/mahasiswa/{id}/konfirmasi-berkas', [AdminMahasiswaController::class, 'konfirmasiBerkas']);
        Route::put('/mahasiswa/{id}/terima', [AdminMahasiswaController::class, 'terima']);
        Route::get('/mahasiswa/{id}/download/{jenis}', [AdminMahasiswaController::class, 'download']);
        Route::delete('/mahasiswa/{id}', [AdminMahasiswaController::class, 'destroy']);
        Route::put('/mahasiswa/{id}', [AdminMahasiswaController::class, 'update']);

        /*
        |--------------------------------------------------------------------------
        | PENILAIAN
        |--------------------------------------------------------------------------
        */
        Route::get('/penilaian', [AdminPenilaianController::class, 'index']);     
        Route::get('/penilaian/{id}', [AdminPenilaianController::class, 'show']); 
        Route::put('/penilaian/{id}', [AdminPenilaianController::class, 'update']);

        /*
        |--------------------------------------------------------------------------
        | KELOMPOK
        |--------------------------------------------------------------------------
        */
        Route::get('/kelompok', [AdminKelompokController::class, 'index']);
        Route::post('/kelompok', [AdminKelompokController::class, 'store']);
        Route::put('/kelompok/{id}', [AdminKelompokController::class, 'update']);
        Route::delete('/kelompok/{id}', [AdminKelompokController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | DIVISI
        |--------------------------------------------------------------------------
        */
        Route::get('/divisi', [KuotaController::class, 'adminIndex']);

        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI
        |--------------------------------------------------------------------------
        */
        Route::get('/notifications', [AdminNotificationController::class, 'index']);

        /*
        |--------------------------------------------------------------------------
        | LAPORAN
        |--------------------------------------------------------------------------
        */
        Route::get('/laporan', [AdminLaporanController::class, 'index']);
        Route::get('/hasil-magang', [AdminPenilaianController::class, 'hasilMagang']);
    });
});