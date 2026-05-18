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

// Module incidents — routes de diagnostic : routes/debug.php (local uniquement, bootstrap/app.php)
Route::middleware(['auth'])->group(function () {
    Route::resource('incidents', IncidentFicheController::class);

    Route::post('/incidents/{incident}/traiter-n1', [IncidentFicheController::class, 'traiterN1'])->name('incidents.traiter-n1');
    Route::post('/incidents/{incident}/traiter-n2', [IncidentFicheController::class, 'traiterN2'])->name('incidents.traiter-n2');
    Route::post('/incidents/{incident}/traiter-n3', [IncidentFicheController::class, 'traiterN3'])->name('incidents.traiter-n3');
    Route::post('/incidents/{incident}/upload-pdf', [IncidentFicheController::class, 'uploadPdf'])->name('incidents.upload-pdf');
    Route::get('/incidents/{incident}/generer-pdf', [IncidentFicheController::class, 'genererPdf'])->name('incidents.generer-pdf');
    Route::get('/incidents/{incident}/view-pdf', [IncidentFicheController::class, 'viewPdf'])->name('incidents.view-pdf');
});
