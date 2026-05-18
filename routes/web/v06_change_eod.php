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

Route::middleware(['auth'])->group(function () {
    // ==================== CHANGE MANAGEMENT ====================
    
    // Route principale qui redirige automatiquement selon le rôle
    Route::get('/change', [ChangeTicketController::class, 'redirectToRolePage'])
        ->name('change.index');
    
    // Route pour la sélection manuelle de rôle (si pas de rôle assigné)
    Route::get('/change/role', [ChangeTicketController::class, 'selectRole'])
        ->name('change.role');
    Route::post('/change/role', [ChangeTicketController::class, 'setRole'])
        ->name('change.role.set');
    Route::post('/change/role/clear', [ChangeTicketController::class, 'clearRole'])
        ->name('change.role.clear');
    
    // Dashboard (redirection)
    Route::get('/change/dashboard', [ChangeTicketController::class, 'dashboard'])
        ->name('change.dashboard');

    // N+1 Routes
    Route::prefix('change/n1')->name('change.n1.')->group(function () {
        Route::get('/', [ChangeTicketController::class, 'n1Index'])->name('index');
        Route::get('/create', [ChangeTicketController::class, 'n1Create'])->name('create');
        Route::post('/', [ChangeTicketController::class, 'n1Store'])->name('store');
        Route::get('/{ticket}', [ChangeTicketController::class, 'n1Edit'])->name('edit');
        Route::put('/{ticket}', [ChangeTicketController::class, 'n1Update'])->name('update');
        Route::post('/{ticket}/submit-n2', [ChangeTicketController::class, 'n1SubmitToN2'])->name('submit-n2');
        Route::post('/{ticket}/close', [ChangeTicketController::class, 'n1Close'])->name('close');
        Route::post('/{ticket}/return-n2', [ChangeTicketController::class, 'n1ReturnToN2'])->name('return-n2');
        Route::delete('/{ticket}/file/{fileIndex}', [ChangeTicketController::class, 'deleteFile'])->name('delete-file');
        Route::delete('/{ticket}/file/{fileIndex}/{type}', [ChangeTicketController::class, 'deleteFile'])->name('delete-file-type');
    });

    // N+2 Routes
    Route::prefix('change/n2')->name('change.n2.')->group(function () {
        Route::get('/', [ChangeTicketController::class, 'n2Index'])->name('index');
        Route::get('/{ticket}', [ChangeTicketController::class, 'n2Edit'])->name('edit');
        Route::put('/{ticket}', [ChangeTicketController::class, 'n2Update'])->name('update');
        Route::post('/{ticket}/submit-n3', [ChangeTicketController::class, 'n2SubmitToN3'])->name('submit-n3');
        Route::post('/{ticket}/submit-n1', [ChangeTicketController::class, 'n2SubmitToN1'])->name('submit-n1');
        Route::post('/{ticket}/reject', [ChangeTicketController::class, 'n2Reject'])->name('reject');
    });

    // N+3 Routes
    Route::prefix('change/n3')->name('change.n3.')->group(function () {
        Route::get('/', [ChangeTicketController::class, 'n3Index'])->name('index');
        Route::get('/{ticket}', [ChangeTicketController::class, 'n3Edit'])->name('edit');
        Route::post('/{ticket}/approve-n2', [ChangeTicketController::class, 'n3ApproveReturnToN2'])->name('approve-n2');
    });

    Route::get('/change/ticket/{ticket}/pdf', [ChangeTicketController::class, 'downloadClosedPdf'])
        ->name('change.ticket.pdf');

    // File download
    Route::get('/change/ticket/{ticketId}/file/{fileIndex}', [ChangeTicketController::class, 'downloadFile'])
        ->name('change.file.download');
    Route::get('/change/ticket/{ticketId}/file/{fileIndex}/{type}', [ChangeTicketController::class, 'downloadFile'])
        ->name('change.file.download-type');
});


Route::middleware(['auth'])->group(function () {
    // ==================== EOD Suivi ====================
    
    // N+1 Routes
    Route::prefix('eod/n1')->name('eod.n1.')->group(function () {
        Route::get('/', [EodSuiviController::class, 'n1Index'])->name('index');
        Route::get('/create', [EodSuiviController::class, 'n1Create'])->name('create');
        Route::post('/', [EodSuiviController::class, 'n1Store'])->name('store');
        Route::get('/{fiche}', [EodSuiviController::class, 'n1Edit'])->name('edit');
        Route::put('/{fiche}', [EodSuiviController::class, 'n1Update'])->name('update');
        Route::post('/{fiche}/submit', [EodSuiviController::class, 'n1SubmitToN3Controller'])->name('submit');
    });

    // N+2 Routes (création + fiches perso ; validation N+2 réservée à l’ancien flux PENDING_N2)
    Route::prefix('eod/n2')->name('eod.n2.')->group(function () {
        Route::get('/', [EodSuiviController::class, 'n2Index'])->name('index');
        Route::get('/create', [EodSuiviController::class, 'n2Create'])->name('create');
        Route::post('/', [EodSuiviController::class, 'n2Store'])->name('store');
        Route::get('/{fiche}/pdf', [EodSuiviController::class, 'generatePdf'])->name('pdf');
        Route::get('/{fiche}', [EodSuiviController::class, 'n2Edit'])->name('edit');
        Route::put('/{fiche}', [EodSuiviController::class, 'n2Update'])->name('update');
        Route::post('/{fiche}/submit', [EodSuiviController::class, 'n2SubmitToN3Controller'])->name('submit');
        Route::post('/{fiche}/validate', [EodSuiviController::class, 'n2Validate'])->name('validate');
        Route::post('/{fiche}/reject', [EodSuiviController::class, 'n2Reject'])->name('reject');
    });

    // CONTROLLER Routes (validation et signature batch EOD)
    Route::prefix('eod/controller')->name('eod.controller.')->group(function () {
        Route::get('/', [EodSuiviController::class, 'controllerIndex'])->name('index');
        Route::get('/{fiche}', [EodSuiviController::class, 'controllerEdit'])->name('edit');
        Route::post('/{fiche}/sign', [EodSuiviController::class, 'controllerSign'])->name('sign');
    });

    // N+3 Routes (NOUVEAU)
    Route::prefix('eod/n3')->name('eod.n3.')->group(function () {
        Route::get('/', [EodSuiviController::class, 'n3Index'])->name('index');
        Route::get('/fiches-en-attente', [EodSuiviController::class, 'n3PendingList'])->name('pending');
        Route::get('/statistiques', [EodSuiviController::class, 'n3Statistiques'])->name('statistiques');
        Route::get('/export/{format}', [EodSuiviController::class, 'n3Export'])->name('export');
        Route::post('/{fiche}/sign', [EodSuiviController::class, 'n3Sign'])->name('sign');
        Route::get('/{fiche}', [EodSuiviController::class, 'n3Show'])->name('show');
    });
});
