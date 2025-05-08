<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SapoController;
use App\Http\Controllers\DotdigitalController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\SettingIntegrationController;

Route::get('/', function () {
    return view('login');
});

Route::prefix('auth')->group(function () {
    // Đăng nhập - Đăng xuất
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Tạo mới tài khoản
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
});

Route::prefix('/')->middleware('auth')->group(function () {
    Route::get('dashboard', function () {   return view('dashboard.dashboard');   })->name('dashboard');

    // Cấu hình kết nối SAPO với Dotdigital
    Route::get('setting-integration', [SettingIntegrationController::class, 'index'])->name('setting-integration');
    Route::post('/sapo/check-connection', [SapoController::class, 'checkConnection']);
    Route::post('/dotdigital/check-connection', [DotdigitalController::class, 'checkConnection']);
    Route::post('/save-connection', [ConnectionController::class, 'saveConnection']);
    Route::post('/update-status', [ConnectionController::class, 'updateStatus']);
    Route::get('/connection/{id}', [ConnectionController::class, 'show']);
    Route::post('/connection/update', [ConnectionController::class, 'update'])->name('connection.update');

});