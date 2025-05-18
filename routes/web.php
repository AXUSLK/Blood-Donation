<?php

use App\Http\Controllers\Backend\DashboardController as BackendDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('backend.')->group(function () {
    // Route::group(['middleware' => ['auth', 'verified']], function () {
    // Admin
    Route::name('admin.')->group(function () {
        Route::get('/dashboard', [BackendDashboardController::class, 'adminDashboard'])->name('dashboard');
    });
    // Donor
    Route::name('donor.')->group(function () {
        Route::get('/dashboard2', [BackendDashboardController::class, 'donorDashboard'])->name('dashboard');
    });
    // });
});
