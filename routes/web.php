<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIPAFKA (Sistem Informasi Peminjaman Fasilitas Kampus)
| Program Studi Ilmu Komputer Universitas Halu Oleo
|--------------------------------------------------------------------------
*/

// Halaman Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/fasilitas', [HomeController::class, 'fasilitas'])->name('fasilitas.index');

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Lupa & Reset Kata Sandi
    Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'processForgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [AuthController::class, 'showProfile'])->name('profile.index');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/fasilitas', [MahasiswaController::class, 'fasilitas'])->name('fasilitas');
        Route::get('/pinjam/{jenis}/{id?}', [MahasiswaController::class, 'formPeminjaman'])->name('form-peminjaman');
        Route::post('/pinjam', [MahasiswaController::class, 'storePeminjaman'])->name('store-peminjaman');
        Route::get('/peminjaman-saya', [MahasiswaController::class, 'peminjamanSaya'])->name('peminjaman-saya');
        Route::get('/peminjaman/{id}', [MahasiswaController::class, 'detailPeminjaman'])->name('detail-peminjaman');
        Route::get('/peminjaman/{id}/qr', [MahasiswaController::class, 'showQrCode'])->name('qr-code');
        Route::post('/peminjaman/{id}/qr/generate', [MahasiswaController::class, 'generateQr'])->name('generate-qr');
        Route::post('/peminjaman/{id}/kembalikan', [MahasiswaController::class, 'ajukanPengembalian'])->name('ajukan-pengembalian');
        Route::get('/riwayat', [MahasiswaController::class, 'riwayat'])->name('riwayat');
    });

    // Dosen
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
        Route::get('/pengajuan', [DosenController::class, 'pengajuanMasuk'])->name('pengajuan-masuk');
        Route::get('/pengajuan/{id}', [DosenController::class, 'detailPengajuan'])->name('detail-pengajuan');
        Route::post('/pengajuan/{id}/putuskan', [DosenController::class, 'putuskanPengajuan'])->name('putuskan-pengajuan');
        Route::get('/pindai-qr', [DosenController::class, 'pindaiQr'])->name('pindai-qr');
        Route::post('/pindai-qr/validate', [DosenController::class, 'validateQrAjax'])->name('validate-qr-ajax');
        Route::post('/pindai-qr/penyerahan', [DosenController::class, 'prosesPenyerahan'])->name('proses-penyerahan');
        Route::post('/pindai-qr/pengembalian', [DosenController::class, 'prosesPengembalian'])->name('proses-pengembalian');
        Route::get('/riwayat', [DosenController::class, 'riwayatKeputusan'])->name('riwayat-keputusan');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/fasilitas', [AdminController::class, 'kelolaFasilitas'])->name('kelola-fasilitas');
        Route::post('/fasilitas/barang', [AdminController::class, 'storeBarang'])->name('store-barang');
        Route::put('/fasilitas/barang/{id}', [AdminController::class, 'updateBarang'])->name('update-barang');
        Route::delete('/fasilitas/barang/{id}', [AdminController::class, 'destroyBarang'])->name('destroy-barang');
        Route::post('/fasilitas/ruangan', [AdminController::class, 'storeRuangan'])->name('store-ruangan');
        Route::put('/fasilitas/ruangan/{id}', [AdminController::class, 'updateRuangan'])->name('update-ruangan');
        Route::delete('/fasilitas/ruangan/{id}', [AdminController::class, 'destroyRuangan'])->name('destroy-ruangan');
        Route::get('/transaksi', [AdminController::class, 'transaksiBerjalan'])->name('transaksi-berjalan');
        Route::get('/riwayat', [AdminController::class, 'riwayatTransaksi'])->name('riwayat-transaksi');
        Route::get('/pengguna', [AdminController::class, 'kelolaPengguna'])->name('kelola-pengguna');
    });
});
