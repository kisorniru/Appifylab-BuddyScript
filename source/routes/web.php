<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->controller(AppUserController::class)->middleware(['inertia'])->group(function ($route) {
    $route->get('/login', 'userLogin')->name('user.login');
    $route->post('/login', 'userLoginPost')->name('user.login.post');
});

Route::controller(HomeController::class)->middleware('inertia')->group(function ($route) {
    $route->get('/', 'landingPage')->name('landing');
    $route->get('/login', 'login')->name('login');
    $route->post('/login', 'loginPost')->name('login.post');
});

Route::prefix('/admin')->middleware(['auth', 'inertia'])->group(function () {
    Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

    Route::controller(AdminController::class)->group(function ($route) {
        $route->get('/', 'home')->name('dashboard');
    });

    Route::controller(UserController::class)->group(function ($route) {
        $route->get('/users', 'index');
        $route->patch('/user/{user}/active-status', 'userIdActiveStatus');
    });
});

Route::get('/health', function () {
    return response()->json(['code' => 200, 'status' => 'success']);
});

Route::get('/redis-session', function () {
    session(['test' => 'Hello from Redis session']);

    return session('test');
});

Route::get('/redis-cache', function () {
    Cache::put('foo', 'bar', 600);

    return Cache::get('foo');
});

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/dashboard', fn () => view('admin.dashboard'));
});
