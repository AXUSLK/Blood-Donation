<?php

use App\Http\Controllers\Backend\DashboardController as BackendDashboardController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\Admin\RecipientController as BackendRecipientController;
use App\Http\Controllers\Backend\Admin\DonorController as BackendDonorController;
use App\Http\Controllers\Backend\Admin\BloodInventoryController as BackendBloodInventoryController;
use App\Http\Controllers\Backend\Admin\BloodUnitController as BackendBloodUnitController;
use App\Http\Controllers\Backend\Admin\BloodCollectionCampController as BackendBloodCollectionCampController;
use App\Http\Controllers\Backend\Admin\BloodTestController as BackendBloodTestController;
use App\Http\Controllers\Backend\Admin\DashboardController as BackendAdminDashboardController;
use App\Http\Controllers\Backend\Admin\AICompatibilityController as BackendAICompatibilityController;
use App\Http\Controllers\Backend\Admin\AIEligibilityController as BackendAIEligibilityController;
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

            // New Dashboard Routes
            Route::get('/analytics', [BackendAdminDashboardController::class, 'index'])->name('analytics');
            Route::get('/reports', [BackendAdminDashboardController::class, 'reports'])->name('reports');
            Route::post('/generate-report', [BackendAdminDashboardController::class, 'generateReport'])->name('generate-report');

            // AI Features Routes
            Route::get('/ai/compatibility', [BackendAICompatibilityController::class, 'index'])->name('ai.compatibility');
            Route::post('/ai/compatibility/check', [BackendAICompatibilityController::class, 'checkCompatibility'])->name('ai.compatibility.check');
            Route::get('/ai/eligibility', [BackendAIEligibilityController::class, 'index'])->name('ai.eligibility');
            Route::post('/ai/eligibility/predict', [BackendAIEligibilityController::class, 'predictEligibility'])->name('ai.eligibility.predict');

            // Debug route for testing AI
            Route::get('/ai/test', function() {
                return response()->json([
                    'message' => 'AI routes are working!',
                    'donors_count' => \App\Models\Donor::count(),
                    'compatibility_test' => [
                        'A+' => 'O+',
                        'result' => 'Compatible'
                    ]
                ]);
            })->name('ai.test');

            Route::resource('recipients', BackendRecipientController::class);
            Route::resource('donors', BackendDonorController::class);
            Route::resource('blood-inventory', BackendBloodInventoryController::class);
            Route::post('blood-inventory/{bloodInventory}/toggle-status', [BackendBloodInventoryController::class, 'toggleStatus'])->name('blood-inventory.toggle-status');
            Route::resource('blood-units', BackendBloodUnitController::class);
            Route::post('blood-units/{bloodUnit}/mark-used', [BackendBloodUnitController::class, 'markAsUsed'])->name('blood-units.mark-used');

            Route::resource('blood-collection-camps', BackendBloodCollectionCampController::class);
            Route::post('blood-collection-camps/{bloodCollectionCamp}/update-status', [BackendBloodCollectionCampController::class, 'updateStatus'])->name('blood-collection-camps.update-status');

            Route::resource('blood-tests', BackendBloodTestController::class);
            Route::post('blood-tests/{bloodTest}/quarantine', [BackendBloodTestController::class, 'quarantine'])->name('blood-tests.quarantine');
            Route::post('blood-tests/{bloodTest}/approve', [BackendBloodTestController::class, 'approve'])->name('blood-tests.approve');

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
