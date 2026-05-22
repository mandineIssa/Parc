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

// ===========================================================================
// ROUTES DE DEBUG/DEVELOPPEMENT (à retirer en production)
// ===========================================================================

// Routes des rapports
Route::prefix('reports')->name('reports.')->middleware(['auth'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    
    // Exports
    Route::get('/export/equipment', [ReportController::class, 'exportEquipment'])->name('export.equipment');
    Route::get('/export/agencies', [ReportController::class, 'exportAgencies'])->name('export.agencies');
    Route::get('/export/suppliers', [ReportController::class, 'exportSuppliers'])->name('export.suppliers');
    Route::get('/export/categories', [ReportController::class, 'exportCategories'])->name('export.categories');
    
    // Imports
    Route::get('/import/equipment', [ReportController::class, 'importEquipment'])->name('import.equipment');
    Route::post('/import/process', [ReportController::class, 'processImport'])->name('import.process');
    
    // Rapports spécifiques
    Route::get('/equipment', [ReportController::class, 'equipmentReport'])->name('equipment');
    Route::get('/financial', [ReportController::class, 'financialReport'])->name('financial');
    Route::get('/maintenance', [ReportController::class, 'maintenanceReport'])->name('maintenance');
    Route::get('/categories', [ReportController::class, 'categoriesReport'])->name('categories');
    
    // API pour graphiques
    Route::get('/api/chart-data', [ReportController::class, 'apiChartData'])->name('api.chart-data');
    
    // PDF (optionnel)
    Route::get('/generate-pdf', [ReportController::class, 'generatePdf'])->name('generate.pdf');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // ... vos autres routes ...
    
});

// Documentation : routes définies dans routes/web.php
