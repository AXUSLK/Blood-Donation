<?php

use App\Http\Controllers\Backend\DashboardController as BackendDashboardController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\Admin\RecipientController as BackendRecipientController;
use App\Http\Controllers\Backend\Admin\DonorController as BackendDonorController;
use App\Http\Controllers\Backend\Admin\BloodInventoryController as BackendBloodInventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('backend.')->group(function () {
    Route::group(['middleware' => ['auth', 'verified']], function () {
        // Admin
        Route::name('admin.')->group(function () {
            Route::get('/dashboard', [BackendDashboardController::class, 'adminDashboard'])->name('dashboard');

            Route::resource('recipients', BackendRecipientController::class);
            Route::resource('donors', BackendDonorController::class);
            Route::resource('blood-inventory', BackendBloodInventoryController::class);
            Route::post('blood-inventory/{bloodInventory}/toggle-status', [BackendBloodInventoryController::class, 'toggleStatus'])->name('blood-inventory.toggle-status');

            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
            Route::resource('permissions', PermissionController::class);
        });
        // Donor
        Route::name('donor.')->group(function () {
            Route::get('/dashboard2', [BackendDashboardController::class, 'donorDashboard'])->name('dashboard');
        });
    });
});

require __DIR__ . '/auth.php';
