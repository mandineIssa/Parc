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

    // ============================================
    // GESTION DES ÉQUIPEMENTS (resource consolidé plus bas avec ->whereNumber)
    // ============================================

    Route::get('/equipment/{equipment}/audit', [AuditController::class, 'equipmentHistory'])
        ->name('equipment.audit');
    
    // ============================================
    // TRANSITIONS D'ÉTAT DES ÉQUIPEMENTS
    // ============================================
    // ============================================
// TRANSITIONS D'ÉTAT DES ÉQUIPEMENTS
// ============================================
Route::prefix('equipment/{equipment}/transitions')
    ->name('equipment.transitions.')
    ->group(function () {
        
        Route::get('/', [TransitionController::class, 'showTransitionForm'])
            ->name('');
        
        // Routes AJAX/API pour les transitions
        Route::post('/simple-affectation', [TransitionController::class, 'simpleAffectation'])
            ->name('simpleAffectation');
        
        Route::post('/submit-all', [TransitionController::class, 'submitAllForms'])
            ->name('submitAll');
        
        Route::post('/stock-to-parc', [TransitionController::class, 'stockToParc'])
            ->name('stock-to-parc');
            
        Route::post('/parc-to-maintenance', [TransitionController::class, 'parcToMaintenance'])
            ->name('parc-to-maintenance');
            
        Route::post('/maintenance-to-stock', [TransitionController::class, 'maintenanceToStock'])
            ->name('maintenance-to-stock');
            
        Route::post('/parc-to-hors-service', [TransitionController::class, 'parcToHorsService'])
            ->name('parc-to-hors-service');
            
        Route::post('/parc-to-perdu', [TransitionController::class, 'parcToPerdu'])
            ->name('parc-to-perdu');
            
        Route::post('/stock-to-hors-service', [TransitionController::class, 'stockToHorsService'])
            ->name('stock-to-hors-service');
            
        Route::post('/maintenance-to-hors-service', [TransitionController::class, 'maintenanceToHorsService'])
            ->name('maintenance-to-hors-service');
    });
    // ============================================
    // SYSTÈME D'AUDIT ET JOURNAL
    // ============================================
    Route::prefix('audits')
        ->name('audits.')
        ->group(function () {
            Route::get('/', [AuditController::class, 'index'])
                ->name('index');
                
            Route::get('/export', [AuditController::class, 'export'])
                ->name('export');
        });

// Routes d'importation (super_admin et agent_it seulement)
/* Route::middleware(['auth', 'can:import,App\Models\Agency'])->group(function () {
    Route::get('/agencies/import', [AgencyImportController::class, 'create'])
        ->name('agency.import.create');
    
    Route::post('/agencies/import', [AgencyImportController::class, 'store'])
        ->name('agency.import.store');
    
    Route::get('/agencies/import/template', [AgencyImportController::class, 'downloadTemplate'])
        ->name('agency.import.template');
}); */

 Route::get('/agencies/import', [AgencyImportController::class, 'create'])
        ->name('agency.import.create');
    
    Route::post('/agencies/import', [AgencyImportController::class, 'store'])
        ->name('agency.import.store');
    
    Route::get('/agencies/import/template', [AgencyImportController::class, 'downloadTemplate'])
        ->name('agency.import.template');

// ============================================
// IMPORT / EXPORT / PAGES SPÉCIALES ÉQUIPEMENT
// ⚠️ TOUJOURS AVANT resource
// ============================================


// routes/web.php
Route::post('/parc', [ParcController::class, 'store'])->name('parc.store');

// Routes pour l'importation d'équipements
Route::prefix('equipment')->name('equipment.')->group(function () {
    
    // Afficher le formulaire d'importation
    Route::get('/imports', [EquipmentImportController::class, 'showImportForm'])
        ->name('imports.form');
    
    // Télécharger le template Excel
    Route::get('/imports/template', [EquipmentImportController::class, 'downloadTemplate'])
        ->name('imports.template');
    
    // Traiter l'importation
    Route::post('/imports/process', [EquipmentImportController::class, 'import'])
        ->name('imports.process');
});

Route::get('/equipment/import', [EquipmentController::class, 'showImportForm'])
    ->name('equipment.import.form');

Route::post('/equipment/import', [EquipmentController::class, 'import'])
    ->name('equipment.import');

Route::get('/equipment/import-template', [EquipmentController::class, 'downloadTemplate'])
    ->name('equipment.import.template');
// Dans votre fichier routes/web.php

// Ajoutez cette route pour télécharger le template
Route::get('/equipment/export/template', [EquipmentController::class, 'exportTemplate'])
    ->name('equipment.export.template');
Route::get('/equipment/export', [EquipmentController::class, 'export'])
    ->name('equipment.export');

Route::get('/equipment/stock', [EquipmentController::class, 'stock'])
    ->name('equipment.stock');

Route::get('/equipment/renewal', [EquipmentController::class, 'renewalPlanning'])
    ->middleware(['auth', 'verified'])
    ->name('equipment.renewal');

Route::get('/equipment/renewal/export', [EquipmentController::class, 'exportRenewalToReplace'])
    ->middleware(['auth', 'verified'])
    ->name('equipment.renewal.export');

    // Routes pour l'équipement
Route::prefix('equipment')->name('equipment.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [EquipmentController::class, 'index'])->name('index');
    Route::get('/create', [EquipmentController::class, 'create'])->name('create');
    Route::post('/', [EquipmentController::class, 'store'])->name('store');
    Route::get('/{equipment}/edit', [EquipmentController::class, 'edit'])->name('edit');
    Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
    Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
    
    // Routes d'import/export
    Route::get('/import', [EquipmentController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [EquipmentController::class, 'import'])->name('import');
    Route::get('/export', [EquipmentController::class, 'export'])->name('export');
    Route::get('/export/full', [EquipmentController::class, 'exportFull'])->name('export.full'); // ← AJOUTEZ CETTE LIGNE
    Route::get('/template', [EquipmentController::class, 'downloadTemplate'])->name('template');

});

// Routes pour l'import CSV vers le parc
Route::get('equipment/parc/import', [ParcController::class, 'showImportForm'])->name('parc.import.form');
Route::post('equipment/parc/import', [ParcController::class, 'import'])->name('parc.import');
Route::get('equipment/parc/import/template', [ParcController::class, 'downloadTemplate'])->name('parc.import.template');
Route::get('equipment/parc/export', [ParcController::class, 'export'])->name('parc.export');
// ============================================
// CRUD ÉQUIPEMENTS (APRÈS)
// ============================================

Route::resource('equipment', EquipmentController::class)
    ->only(['show'])
    ->whereNumber('equipment'); // show uniquement : le CRUD est déjà défini plus haut

    // Routes pour l'import du parc
Route::prefix('parc')->name('parc.')->group(function () {
    // Import parc (chemins /parc/import*) — noms distincts de equipment/parc/import
    Route::get('/import', [ParcController::class, 'showImportForm'])->name('import.page');
    Route::post('/import', [ParcController::class, 'processImport'])->name('import.process');
    Route::get('/import/template', [ParcController::class, 'downloadTemplate'])->name('import.template.page');
});
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    // ============================================
    // DASHBOARD PRINCIPAL
    // ============================================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ============================================
    // GESTION DU PARC (CRUD complet)
    // ============================================
    /* Route::resource('parc', ParcController::class); */

    // =========================================================
// ✅ AJOUTER CE BLOC juste AVANT la ligne :
//    Route::resource('parc', ParcController::class);
// =========================================================
 
// --- Réaffectations (DOIT être avant resource parc) ---
Route::get('/parc/reaffectations', [ReaffectationController::class, 'index'])
    ->name('parc.reaffectations.index');
 
Route::get('/parc/{equipment}/reaffecter', [ReaffectationController::class, 'create'])
    ->name('parc.reaffecter');
 
Route::post('/parc/{equipment}/reaffecter', [ReaffectationController::class, 'store'])
    ->name('parc.reaffecter.store');
 
// --- Ensuite seulement le resource ---
Route::resource('parc', ParcController::class);

    // ============================================
    // GESTION DES ÉQUIPEMENTS PERDUS (CRUD complet)
    // ============================================
    Route::resource('perdu', PerduController::class);
    Route::post('/perdu/{perdu}/retrouver', [PerduController::class, 'retrouver'])
        ->name('perdu.retrouver');

    // ============================================
    // GESTION DES ÉQUIPEMENTS HORS SERVICE (CRUD complet)
    // ============================================
    Route::resource('hors-service', HorsServiceController::class);
    Route::post('/hors-service/{hors_service}/traiter', [HorsServiceController::class, 'traiter'])
        ->name('hors-service.traiter');
    Route::get('/hors-service/{hors_service}/download-justificatif', [HorsServiceController::class, 'downloadJustificatif'])
        ->name('hors-service.download-justificatif');
    Route::delete('/hors-service/{hors_service}/delete-justificatif', [HorsServiceController::class, 'deleteJustificatif'])
        ->name('hors-service.delete-justificatif');

    // ============================================
    // GESTION DES MAINTENANCES (CRUD complet)
    // ============================================
    Route::resource('maintenance', MaintenanceController::class);
    Route::post('/maintenance/{maintenance}/terminer', [MaintenanceController::class, 'terminer'])
        ->name('maintenance.terminer');
    Route::post('/maintenance/{maintenance}/annuler', [MaintenanceController::class, 'annuler'])
        ->name('maintenance.annuler');
    Route::get('/maintenance/retard', [MaintenanceController::class, 'retard'])
        ->name('maintenance.retard');

    // ============================================
    // DASHBOARD CELER INFORMATIQUE
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/celer-informatique', [CelerDashboardController::class, 'index'])
            ->name('dashboard.celer-informatique');
        Route::get('/celer-informatique/filter', [CelerDashboardController::class, 'filter'])
            ->name('dashboard.celer-informatique.filter');
        Route::get('/celer-informatique/export', [CelerDashboardController::class, 'export'])
            ->name('dashboard.celer-informatique.export');
        Route::get('/celer-informatique/{id}', [CelerDashboardController::class, 'show'])
            ->name('dashboard.celer-informatique.show');
    });

    // ============================================
    // DASHBOARD CELER RÉSEAU
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/celer-reseau', [CelerReseauDashboardController::class, 'index'])
            ->name('dashboard.celer-reseau');
        Route::get('/celer-reseau/filter', [CelerReseauDashboardController::class, 'filter'])
            ->name('dashboard.celer-reseau.filter');
        Route::get('/celer-reseau/export', [CelerReseauDashboardController::class, 'export'])
            ->name('dashboard.celer-reseau.export');
        Route::get('/celer-reseau/{id}', [CelerReseauDashboardController::class, 'show'])
            ->name('dashboard.celer-reseau.show');
        Route::get('/celer-reseau/type/{type}', [CelerReseauDashboardController::class, 'byType'])
            ->name('dashboard.celer-reseau.type');
    });

    // ============================================
    // DASHBOARD CELER ÉLECTRONIQUE
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/celer-electronique', [CelerElectroniqueDashboardController::class, 'index'])
            ->name('dashboard.celer-electronique');
        Route::get('/celer-electronique/filter', [CelerElectroniqueDashboardController::class, 'filter'])
            ->name('dashboard.celer-electronique.filter');
        Route::get('/celer-electronique/export', [CelerElectroniqueDashboardController::class, 'export'])
            ->name('dashboard.celer-electronique.export');
        Route::get('/celer-electronique/{id}', [CelerElectroniqueDashboardController::class, 'show'])
            ->name('dashboard.celer-electronique.show');
        Route::get('/celer-electronique/type/{type}', [CelerElectroniqueDashboardController::class, 'byType'])
            ->name('dashboard.celer-electronique.type');
    });
    
    // ============================================
    // DASHBOARD DECELER INFORMATIQUE
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/deceler-informatique', [DecelerDashboardController::class, 'index'])
            ->name('dashboard.deceler-informatique');
        Route::get('/deceler-informatique/filter', [DecelerDashboardController::class, 'filter'])
            ->name('dashboard.deceler-informatique.filter');
        Route::get('/deceler-informatique/export', [DecelerDashboardController::class, 'export'])
            ->name('dashboard.deceler-informatique.export');
        Route::get('/deceler-informatique/{id}', [DecelerDashboardController::class, 'show'])
            ->name('dashboard.deceler-informatique.show');
    });

    // ============================================
    // DASHBOARD DECELER RÉSEAU
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/deceler-reseau', [DecelerReseauDashboardController::class, 'index'])
            ->name('dashboard.deceler-reseau');
        Route::get('/deceler-reseau/filter', [DecelerReseauDashboardController::class, 'filter'])
            ->name('dashboard.deceler-reseau.filter');
        Route::get('/deceler-reseau/export', [DecelerReseauDashboardController::class, 'export'])
            ->name('dashboard.deceler-reseau.export');
        Route::get('/deceler-reseau/{id}', [DecelerReseauDashboardController::class, 'show'])
            ->name('dashboard.deceler-reseau.show');
    });

    // ============================================
    // DASHBOARD DECELER ÉLECTRONIQUE
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/deceler-electronique', [DecelerElectroniqueDashboardController::class, 'index'])
            ->name('dashboard.deceler-electronique');
        Route::get('/deceler-electronique/filter', [DecelerElectroniqueDashboardController::class, 'filter'])
            ->name('dashboard.deceler-electronique.filter');
        Route::get('/deceler-electronique/export', [DecelerElectroniqueDashboardController::class, 'export'])
            ->name('dashboard.deceler-electronique.export');
        Route::get('/deceler-electronique/{id}', [DecelerElectroniqueDashboardController::class, 'show'])
            ->name('dashboard.deceler-electronique.show');
    });

    // ============================================
    // GESTION DES AGENCES (CRUD complet)
    // ============================================
    Route::resource('agencies', AgencyController::class);

    // ============================================
    // GESTION DES CATÉGORIES
    // ============================================
    Route::resource('categories', CategoryController::class);
    
    Route::get('/categories/trash', [CategoryController::class, 'trash'])->name('categories.trash');
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');
    Route::post('/categories/restore-all', [CategoryController::class, 'restoreAll'])->name('categories.restoreAll');
    Route::delete('/categories/empty-trash', [CategoryController::class, 'emptyTrash'])->name('categories.emptyTrash');
    Route::get('/categories-by-type/{type}', [CategoryController::class, 'categoriesByType']);
    Route::get('/sous-categories-by-categorie/{categorieId}', [CategoryController::class, 'sousCategoriesByCategorie']);

    // ============================================
    // GESTION DES FOURNISSEURS
    // ============================================
    Route::resource('suppliers', SupplierController::class);
    
    Route::get('suppliers/list', [SupplierController::class, 'getSuppliers'])
        ->name('suppliers.list');
