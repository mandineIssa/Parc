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

Route::middleware(['auth'])->prefix('controls')->name('controls.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [ControlTaskController::class, 'dashboard'])->name('dashboard');

        // =============================================
    // ROUTES POUR LES TÂCHES
    // =============================================
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [ControlTaskController::class, 'index'])->name('index');
        Route::get('/{task}', [ControlTaskController::class, 'show'])->name('show');
        Route::put('/{task}/status', [ControlTaskController::class, 'updateStatus'])->name('update-status');
        Route::post('/{task}/validate', [ControlTaskController::class, 'validateTask'])->name('validate');
        Route::post('/{task}/attachments', [ControlTaskController::class, 'uploadAttachment'])->name('upload-attachment');
        Route::delete('/attachments/{attachment}', [ControlTaskController::class, 'deleteAttachment'])->name('delete-attachment');
    });
    
    // =============================================
    // ROUTES POUR LES CONTRÔLES
    // =============================================
    Route::get('/', [ControlController::class, 'index'])->name('index');
    Route::get('/create', [ControlController::class, 'create'])->name('create');
    Route::post('/', [ControlController::class, 'store'])->name('store');
    Route::get('/{control}', [ControlController::class, 'show'])->name('show');
    Route::get('/{control}/edit', [ControlController::class, 'edit'])->name('edit');
    Route::put('/{control}', [ControlController::class, 'update'])->name('update');
    Route::delete('/{control}', [ControlController::class, 'destroy'])->name('destroy');
    Route::post('/{control}/generate-tasks', [ControlController::class, 'generateTasks'])->name('generate-tasks');
    

    
    // =============================================
    // ROUTES POUR LES TEMPLATES (Admin uniquement)
    // =============================================
    Route::prefix('templates')->name('templates.')->middleware('admin')->group(function () {
        Route::get('/', [ControlTemplateController::class, 'index'])->name('index');
        Route::get('/create', [ControlTemplateController::class, 'create'])->name('create');
        Route::post('/', [ControlTemplateController::class, 'store'])->name('store');
        Route::get('/{template}/edit', [ControlTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [ControlTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [ControlTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{template}/details', [ControlTemplateController::class, 'details'])->name('details');
    });
});
