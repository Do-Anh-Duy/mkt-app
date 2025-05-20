<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SapoController;
use App\Http\Controllers\DotdigitalController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\SettingIntegrationController;
use App\Http\Controllers\SettingCampaignController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\OrdersController;

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

    // Cấu hình liên kết campaign
    Route::get('campaign', [SettingCampaignController::class, 'index'])->name('campaign');
    Route::post('/sync-campaign', [SettingCampaignController::class, 'sync'])->name('campaign.sync');
    Route::get('/api/campaign/{id}', [SettingCampaignController::class, 'show']);
    Route::post('/api/campaign', [SettingCampaignController::class, 'update']);
    Route::get('/api/storeName', [SettingCampaignController::class, 'showStoreName']);

    //Danh sách khách hàng
    Route::get('contacts', [ContactsController::class, 'index'])->name('contacts');
    Route::get('contacts/search', [ContactsController::class, 'searchIndex'])->name('contacts.searchIndex');
    //Danh sách đơn hàng
    Route::get('orders', [OrdersController::class, 'index'])->name('orders');
    Route::get('orders/search', [OrdersController::class, 'searchIndex'])->name('orders.searchIndex');

    //Test data
    Route::get('test', [TestController::class, 'test'])->name('test');
});

