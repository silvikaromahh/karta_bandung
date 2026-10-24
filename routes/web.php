<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Peserta\PesertaController;

// ── HALAMAN PUBLIK ──────────────────────────────────────────────
Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/tentang', [PublicController::class, 'tentang'])->name('tentang');
Route::get('/jadwal', [PublicController::class, 'jadwal'])->name('jadwal');
Route::get('/hasil-klasemen', [PublicController::class, 'hasilKlasemen'])->name('hasil-klasemen');
Route::get('/berita', [PublicController::class, 'berita'])->name('berita');
Route::get('/berita/{slug}', [PublicController::class, 'beritaDetail'])->name('berita.detail');

// ── AUTH ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/masuk', [LoginController::class, 'login'])->name('login.post');
    Route::get('/daftar', function() {
        return redirect()->route('login');
    })->name('register');
    Route::post('/daftar', function() {
        return redirect()->route('login');
    })->name('register.post');
});

Route::post('/keluar', [LoginController::class, 'logout'])->name('logout');

// ── ADMIN ───────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Pendaftar
    Route::get('/pendaftar', [AdminController::class, 'pendaftar'])->name('pendaftar');
    Route::delete('/pendaftar/{id}', [AdminController::class, 'destroyPendaftar'])->name('pendaftar.destroy');

    // Verifikasi
    Route::get('/verifikasi', [AdminController::class, 'verifikasi'])->name('verifikasi');
    Route::get('/verifikasi/{id}', [AdminController::class, 'verifikasiDetail'])->name('verifikasi.detail');
    Route::get('/verifikasi/{id}/cetak', [AdminController::class, 'verifikasiCetak'])->name('verifikasi.cetak');
    Route::post('/verifikasi/{id}/terima', [AdminController::class, 'terima'])->name('verifikasi.terima');
    Route::post('/verifikasi/{id}/tolak', [AdminController::class, 'tolak'])->name('verifikasi.tolak');
    Route::delete('/verifikasi/{registrationId}/member/{memberId}', [AdminController::class, 'deleteMember'])->name('verifikasi.member.delete');

    // Pertandingan
    Route::get('/pertandingan', [AdminController::class, 'pertandingan'])->name('pertandingan');
    Route::post('/pertandingan', [AdminController::class, 'storePertandingan'])->name('pertandingan.store');
    Route::get('/pertandingan/{id}/edit', [AdminController::class, 'editPertandingan'])->name('pertandingan.edit');
    Route::put('/pertandingan/{id}', [AdminController::class, 'updatePertandingan'])->name('pertandingan.update');
    Route::delete('/pertandingan/{id}', [AdminController::class, 'deletePertandingan'])->name('pertandingan.delete');

    // Hasil & Klasemen
    Route::get('/hasil-klasemen', [AdminController::class, 'hasilKlasemen'])->name('hasil-klasemen');
    Route::post('/hasil/{id}', [AdminController::class, 'inputHasil'])->name('hasil.input');

    // Tim
    Route::get('/tim', [AdminController::class, 'tim'])->name('tim');
    Route::post('/tim', [AdminController::class, 'storeTim'])->name('tim.store');
    Route::delete('/tim/{id}', [AdminController::class, 'deleteTim'])->name('tim.delete');

    // Berita
    Route::get('/berita', [AdminController::class, 'berita'])->name('berita');
    Route::get('/berita/buat', [AdminController::class, 'createBerita'])->name('berita.create');
    Route::post('/berita', [AdminController::class, 'storeBerita'])->name('berita.store');
    Route::get('/berita/{id}/edit', [AdminController::class, 'editBerita'])->name('berita.edit');
    Route::put('/berita/{id}', [AdminController::class, 'updateBerita'])->name('berita.update');
    Route::delete('/berita/{id}', [AdminController::class, 'deleteBerita'])->name('berita.delete');

    // Pengaturan
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan', [AdminController::class, 'updatePengaturan'])->name('pengaturan.update');
});

// ── PESERTA ─────────────────────────────────────────────────────
Route::prefix('peserta')->name('peserta.')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [PesertaController::class, 'dashboard'])->name('dashboard');
    Route::get('/pendaftaran', [PesertaController::class, 'pendaftaran'])->name('pendaftaran');
    Route::get('/pendaftaran/cetak', [PesertaController::class, 'pendaftaranCetak'])->name('pendaftaran.cetak');
    Route::post('/pendaftaran', [PesertaController::class, 'storePendaftaran'])->name('pendaftaran.store');
    Route::get('/profil', [PesertaController::class, 'profil'])->name('profil');
    Route::post('/profil', [PesertaController::class, 'updateProfil'])->name('profil.update');
    Route::get('/jadwal', [PesertaController::class, 'jadwal'])->name('jadwal');
    Route::get('/hasil-klasemen', [PesertaController::class, 'hasilKlasemen'])->name('hasil-klasemen');
    Route::get('/pengumuman', [PesertaController::class, 'pengumuman'])->name('pengumuman');
    Route::get('/notifikasi', [PesertaController::class, 'notifikasi'])->name('notifikasi');
    Route::get('/pengaturan', [PesertaController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan/password', [PesertaController::class, 'updatePassword'])->name('pengaturan.password');
    Route::post('/notifikasi/baca-semua', [PesertaController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::delete('/notifikasi/{id}', [PesertaController::class, 'destroyNotifikasi'])->name('notifikasi.destroy');
    Route::delete('/notifikasi', [PesertaController::class, 'destroyAllNotifikasi'])->name('notifikasi.destroy-all');
});
