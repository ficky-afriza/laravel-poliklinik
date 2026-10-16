<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('poli', AdminPoliController::class)
        ->except('show')
        ->names([
            'index' => 'admin.poli.index',
            'create' => 'polis.create',
            'store' => 'polis.store',
            'edit' => 'polis.edit',
            'update' => 'polis.update',
            'destroy' => 'polis.destroy',
        ]);
});


Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');

    Route::get('/poli', function () {
        return view('dokter.dashboard');
    })->name('dokter.poli.index');
});


Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', function () {
        return view('pasien.dashboard');
    })->name('pasien.dashboard');

    Route::get('/poli', function () {
        return view('pasien.dashboard');
    })->name('pasien.poli.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::resource('polis', AdminPoliController::class);
    Route::resource('dokter', DokterController::class)->except('show');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::resource('polis', AdminPoliController::class);
    Route::resource('dokter', DokterController::class);
    Route::resource('pasien', PasienController::class)->except('show');
});
