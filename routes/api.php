<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PackageController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/admin/login', [AdminController::class, 'login']);

Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::put('/', [AdminController::class, 'update']);
    Route::delete('/logout', [AdminController::class, 'logout']);

    Route::apiResource('package', PackageController::class);
});

Route::middleware(UserMiddleware::class)->prefix('user')->group(function () {
    Route::apiResource('package', PackageController::class);
});
