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
    
    // CORRECTION : UNE SEULE DÉFINITION DE CETTE ROUTE - SUPPRIMER L'AUTRE
    Route::get(
        '/transitions/approval/{approval}/download',
        [TransitionController::class, 'downloadApproval']
    )->name('transitions.approval.download');
    // Route pour télécharger uniquement la fiche de mouvement
/* Route::get(
    '/transitions/fiche-mouvement/{id}/download',
    [TransitionController::class, 'downloadFicheMouvement']
)->name('transitions.fiche-mouvement.download'); */
    // Liste des approbations en attente


    // Route pour télécharger uniquement la fiche d'installation
Route::get(
    '/transitions/fiche-installation/{id}/download',
    [TransitionController::class, 'downloadFicheInstallation']
)->name('transitions.fiche-installation.download')
->middleware(['auth', 'verified']);

// Route pour télécharger uniquement la fiche de mouvement
Route::get(
    '/transitions/fiche-mouvement/{id}/download',
    [TransitionController::class, 'downloadFicheMouvement']
)->name('transitions.fiche-mouvement.download')
->middleware(['auth', 'verified']);

// Route pour télécharger le document complet
Route::get(
    '/transitions/approval/{id}/download',
    [TransitionController::class, 'downloadApproval']
)->name('transitions.approval.download')
->middleware(['auth', 'verified']);
    Route::get('/admin/approvals', [TransitionController::class, 'pendingApprovals'])
        ->name('admin.approvals');
    
    // Afficher une approbation - Utilise admin.approvals.show
    Route::get('/admin/approvals/{approval}', [TransitionController::class, 'show'])
        ->name('admin.approvals.show');
    
    // Approuver la transition (toujours avec transitions.approve pour compatibilité)
    Route::post('/transitions/approval/{approval}/approve', [TransitionController::class, 'approveTransition'])
        ->name('transitions.approve');
        
    Route::get('/transitions/approval/{approval}', [TransitionController::class, 'show'])
    ->name('transitions.approval.show');

    // Rejeter la transition (toujours avec transitions.reject pour compatibilité)
    Route::post('/transitions/approval/{approval}/reject', [TransitionController::class, 'rejectTransition'])
        ->name('transitions.reject');
     
    
    // Dashboard super admin
    Route::get('/admin/dashboard', [SuperAdminController::class, 'index'])
        ->name('admin.dashboard');
        
    // ✅ CETTE LIGNE EST OK - Elle reste commentée car déjà définie plus haut
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ✅ MODIFICATION 3 : Suppression de la redirection en conflit
    // Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');
    
    // Statistiques super admin
    Route::get('/admin/stats', [SuperAdminController::class, 'stats'])
        ->name('admin.stats');
        
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});
