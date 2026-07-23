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

// Routes pour les approbations hors service (GET liste conservée ci-dessous)
Route::get('/admin/hors-service-approvals', 
    [TransitionController::class, 'listHorsServiceApprovals']
)->name('admin.hors-service-approvals.list')
 ->middleware('auth');

 // Routes pour les transitions maintenance
Route::post('/transitions/submit-maintenance', [TransitionController::class, 'submitMaintenance'])
    ->name('transitions.submit-maintenance');

Route::post('/approvals/{approval}/approve-maintenance', [TransitionController::class, 'approveMaintenance'])
    ->name('transitions.approve-maintenance')
    ->middleware('auth');

Route::post('/approvals/{approval}/reject-maintenance', [TransitionController::class, 'rejectMaintenance'])
    ->name('transitions.reject-maintenance')
    ->middleware('auth');

// Route pour la liste des approbations maintenance
Route::get('/admin/maintenance-approvals', [TransitionController::class, 'listMaintenanceApprovals'])
    ->name('admin.maintenance-approvals.list')
    ->middleware('auth');
// Cette route devrait déjà exister
Route::get('/approvals/{approval}', [TransitionController::class, 'showApprovalDetails'])
    ->name('transitions.approval.details')
    ->middleware('auth');

    Route::get('/admin/approvals/{approval}/maintenance', 
    [TransitionController::class, 'showMaintenanceApproval']
)->name('admin.maintenance-approval')
 ->middleware('auth');

 // ----------------------------------------------------
// MODULE PARC → PERDU
// ----------------------------------------------------
Route::post('/transitions/submit-perdu', [TransitionController::class, 'submitPerdu'])
    ->name('transitions.submit-perdu')
    ->middleware('auth');

Route::post('/approvals/{approval}/approve-perdu', [TransitionController::class, 'approvePerdu'])
    ->name('transitions.approve-perdu')
    ->middleware('auth');

Route::post('/approvals/{approval}/reject-perdu', [TransitionController::class, 'rejectPerdu'])
    ->name('transitions.reject-perdu')
    ->middleware('auth');

Route::get('/admin/perdu-approvals', [TransitionController::class, 'listPerduApprovals'])
    ->name('admin.perdu-approvals.list')
    ->middleware('auth');

Route::get('/admin/approvals/{approval}/perdu', 
    [TransitionController::class, 'showPerduApproval']
)->name('admin.perdu-approval')
 ->middleware('auth');
 // Routes pour les transitions hors service
Route::post('/transitions/submit-hors-service', [TransitionController::class, 'submitHorsService'])
    ->name('transitions.submit-hors-service')
    ->middleware('auth');

// Nouvelle route pour parc → hors service
Route::post('/transitions/submit-parc-hors-service', [TransitionController::class, 'submitParcHorsService'])
    ->name('transitions.submit-parc-hors-service')
    ->middleware('auth');

//parc stockdecele
Route::post(
    '/equipment/{equipment}/transitions/submit-decele',
    [TransitionController::class, 'submitDecele']   // ou TransitionDeceleController::class
)->name('transitions.submitDecele');

// Routes pour l'approbation
Route::post('/approvals/{approval}/approve-hors-service', [TransitionController::class, 'approveHorsService'])
    ->name('transitions.approve-hors-service')
    ->middleware('auth');

Route::post('/approvals/{approval}/approve-parc-hors-service', [TransitionController::class, 'approveParcHorsService'])
    ->name('transitions.approve-parc-hors-service')
    ->middleware('auth');

Route::post('/approvals/{approval}/reject-hors-service', [TransitionController::class, 'rejectHorsService'])
    ->name('transitions.reject-hors-service')
    ->middleware('auth');

      // Routes pour visualiser les approbations hors service par type
Route::get('/admin/hors-service-approvals/{approval}', [TransitionController::class, 'showHorsServiceApproval'])
    ->name('admin.hors-service-approval')
    ->middleware('auth');

Route::get('/admin/parc-hors-service-approvals/{approval}', [TransitionController::class, 'showParcHorsServiceApproval'])
    ->name('admin.parc-hors-service-approval')
    ->middleware('auth');

Route::get('/admin/maintenance-hors-service-approvals/{approval}', [TransitionController::class, 'showMaintenanceHorsServiceApproval'])
    ->name('admin.maintenance-hors-service-approval')
    ->middleware('auth');
   
Route::post('/approvals/{approval}/reject-parc-hors-service', [TransitionController::class, 'rejectParcHorsService'])
    ->name('transitions.reject-parc-hors-service')
    ->middleware('auth');

Route::post('/approvals/{approval}/reject-maintenance-hors-service', [TransitionController::class, 'rejectMaintenanceHorsService'])
    ->name('transitions.reject-maintenance-hors-service')
    ->middleware('auth');
    
// Liste des approbations
Route::get('/admin/maintenance-to-stock-approvals', [TransitionController::class, 'listMaintenanceToStockApprovals'])
    ->name('admin.maintenance-to-stock-approvals.list')
    ->middleware('auth');

// Détails d'une approbation
Route::get('/admin/maintenance-to-stock-approvals/{approval}', [TransitionController::class, 'showApprovalMaintenanceToStock'])
    ->name('admin.maintenance-to-stock-approval')
    ->middleware('auth');



// Groupe des transitions
Route::prefix('transitions')->name('transitions.')->group(function () {
    
    // Routes pour les soumissions
    Route::post('/submit-maintenance-to-stock', [TransitionController::class, 'submitMaintenanceToStock'])
        ->name('submit-maintenance-to-stock')
        ->middleware('auth');
    
    // Routes pour les approbations (déjà existantes)
    Route::post('/approvals/{approval}/approve-maintenance-to-stock', [TransitionController::class, 'approveMaintenanceToStock'])
        ->name('approve-maintenance-to-stock')
        ->middleware('auth');
        
    Route::post('/approvals/{approval}/reject-maintenance-to-stock', [TransitionController::class, 'rejectMaintenanceToStock'])
        ->name('reject-maintenance-to-stock')
        ->middleware('auth');
        
    // Autres routes de transition...
});

    // Routes pour les transitions maintenance → hors service
Route::post('/transitions/submit-maintenance-hors-service', [TransitionController::class, 'submitMaintenanceHorsService'])
    ->name('transitions.submit-maintenance-hors-service');

Route::post('/approvals/{approval}/approve-maintenance-hors-service', [TransitionController::class, 'approveMaintenanceHorsService'])
    ->name('transitions.approve-maintenance-hors-service')
    ->middleware('auth');

Route::post('/approvals/{approval}/reject-maintenance-hors-service', [TransitionController::class, 'rejectMaintenanceToHorsService'])
    ->name('transitions.reject-maintenance-hors-service')
    ->middleware('auth');

Route::get('/admin/maintenance-hors-service-approvals', [TransitionController::class, 'listMaintenanceHorsServiceApprovals'])
    ->name('admin.maintenance-hors-service-approvals.list')
    ->middleware('auth');
