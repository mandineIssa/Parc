<?php declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\TransitionController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParcController;
use App\Http\Controllers\PerduController;
use App\Http\Controllers\HorsServiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CelerDashboardController;
use App\Http\Controllers\CelerReseauDashboardController;
use App\Http\Controllers\CelerElectroniqueDashboardController;
use App\Http\Controllers\DecelerDashboardController;
use App\Http\Controllers\DecelerReseauDashboardController;
use App\Http\Controllers\DecelerElectroniqueDashboardController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileSignatureController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardsController;
use App\Http\Controllers\EquipmentImportController;
use App\Http\Controllers\AgencyImportController;
use App\Http\Controllers\ChangeTicketController;
use App\Http\Controllers\EodSuiviController;
use App\Http\Controllers\ReaffectationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\NetworkAddressController;
use App\Http\Controllers\LicenceController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\ControlTaskController;
use App\Http\Controllers\ControlTemplateController;
use App\Http\Controllers\IncidentFicheController;

 /*
    |--------------------------------------------------------------------------
    | ADMIN – Gestion des utilisateurs
    |--------------------------------------------------------------------------
    */
Route::middleware(['can:manage-users'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index'); // Sans 'admin.' préfixe
    
    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create');
    
    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store');
    
    Route::get('/users/{user}', [UserController::class, 'show']) // AJOUTÉE
        ->name('users.show');
    
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
    
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');
    
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');

    Route::post('/users/{user}/signature', [ProfileSignatureController::class, 'storeForUser'])
        ->name('users.signature.store');
    Route::delete('/users/{user}/signature', [ProfileSignatureController::class, 'destroyForUser'])
        ->name('users.signature.destroy');
});
