<?php

use App\Http\Controllers\Backend\DashboardController as BackendDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('backend.admin.')->group(function () {
    // Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::get('/', [BackendDashboardController::class, 'index'])->name('dashboard');

        // });
    });
