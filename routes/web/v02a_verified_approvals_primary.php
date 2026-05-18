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
    
         // ================== MAINTENANCE ÉQUIPEMENT ==================
    Route::get(
        '/equipment/{equipment}/maintenance/create',
        [MaintenanceController::class, 'create']
    )->name('equipment.maintenance.create');

       Route::post('/equipment/{equipment}/transition/execute', [TransitionController::class, 'executeTransition'])
        ->name('transitions.execute');
        // Route pour soumettre une demande d'approbation
Route::post('/transitions/{equipment}/submit-approval', [TransitionController::class, 'submitApproval'])
    ->name('transitions.submit');
    // Liste des approbations en attente
    Route::get('/admin/approvals', [TransitionController::class, 'pendingApprovals'])
        ->name('admin.approvals');
    
        Route::delete('/admin/approvals/{approval}', [SuperAdminController::class, 'destroyApproval'])->name('admin.approvals.destroy');

    // Afficher une approbation - UTILISEZ showApproval qui existe déjà
    Route::get('/transitions/approval/{approval}', [TransitionController::class, 'showApproval'])
        ->name('transitions.approval.show');
    
    // Approuver la transition
    Route::post('/transitions/approval/{approval}/approve', [TransitionController::class, 'approveTransition'])
        ->name('transitions.approve');
    
    // Rejeter la transition
    Route::post('/transitions/approval/{approval}/reject', [TransitionController::class, 'rejectTransition'])
        ->name('transitions.reject');
    
    // Dashboard super admin
    Route::get('/admin/dashboard', [SuperAdminController::class, 'index'])
        ->name('admin.dashboard');
    
    // Statistiques super admin
    Route::get('/admin/stats', [SuperAdminController::class, 'stats'])
        ->name('admin.stats');
        // Routes pour les attachments
// Routes pour les attachments

});
