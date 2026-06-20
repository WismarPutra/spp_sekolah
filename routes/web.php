<?php

use Illuminate\Support\Facades\Route;

// Import Auth Controller
use App\Http\Controllers\AuthController;


// Import Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\SppController;
use App\Http\Controllers\Admin\TagihanController as AdminTagihan;
use App\Http\Controllers\indexController;
// Import User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\TagihanController as UserTagihan;
use App\Http\Controllers\User\PembayaranController as UserPembayaran;

/*
|--------------------------------------------------------------------------
| Public Routes (Login/Logout)
|--------------------------------------------------------------------------
*/

Route::get('/', [indexController::class, 'index']);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/export', [AdminDashboard::class, 'exportLaporan'])->name('admin.dashboard.export');

    Route::post('/admin/siswa/{id}/kirim-ulang-akun', [SiswaController::class, 'kirimUlangAkun'])->name('admin.siswa.kirimUlangAkun');
    Route::post('/siswa/reset-manual', [SiswaController::class, 'resetManual'])->name('admin.siswa.reset');

    Route::resource('/siswa', SiswaController::class);
    Route::resource('/spp', SppController::class);

    Route::get('/tagihan/{id}/reminder', [AdminTagihan::class, 'kirimReminder'])->name('tagihan.reminder');
    Route::resource('/tagihan', AdminTagihan::class);
});


/*
|--------------------------------------------------------------------------
| User (Siswa) Routes
|--------------------------------------------------------------------------
*/
Route::prefix('user')->middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('user.dashboard');
    Route::get('/pembayaran', [UserPembayaran::class, 'index'])->name('user.pembayaran');
    Route::get('/pembayaran/pay/{id}', [UserPembayaran::class, 'pay'])->name('user.pay');
    Route::get('/user/pembayaran/cancel/{id}', [UserPembayaran::class, 'cancel'])->name('user.pembayaran.cancel');
});