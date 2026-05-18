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

Route::middleware(['auth', 'verified'])->group(function () {
    // Page web d'affichage
    Route::get('/approvals/{approval}/attachments', [TransitionController::class, 'showAttachments'])
        ->name('approvals.attachments.show');
    
    // API endpoints
    Route::get('/transitions/{approval}/attachments', [TransitionController::class, 'listAttachments'])
        ->name('transitions.attachments.list');
    
    Route::get('/transitions/{approval}/attachments/info', [TransitionController::class, 'getAttachmentInfo'])
        ->name('transitions.attachments.info');
    
    Route::post('/transitions/{approval}/attachments', [TransitionController::class, 'storeAttachment'])
        ->name('transitions.attachments.store');
    
    Route::delete('/transitions/{approval}/attachments', [TransitionController::class, 'destroyAttachment'])
        ->name('transitions.attachments.destroy');
    
    Route::get('/transitions/{approval}/attachments/download', [TransitionController::class, 'downloadAttachment'])
        ->name('transitions.attachments.download');
    
    Route::post('/transitions/{approval}/attachments/sync', [TransitionController::class, 'syncAttachments'])
        ->name('transitions.attachments.sync');
    
    Route::post('/transitions/{approval}/attachments/cleanup', [TransitionController::class, 'cleanupDuplicates'])
        ->name('transitions.attachments.cleanup');
});
