<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    EquipmentController,
    TransitionController,
    AuditController,
    DashboardController,
    ParcController,
    PerduController,
    HorsServiceController,
    MaintenanceController,
    CelerDashboardController,
    CelerReseauDashboardController,
    CelerElectroniqueDashboardController,
    DecelerDashboardController,
    DecelerReseauDashboardController,
    DecelerElectroniqueDashboardController,
    AgencyController,
    CategoryController,
    SupplierController,
    SuperAdminController,
    UserController,
    ReportController
};

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| DASHBOARD PRINCIPAL (Tous les utilisateurs authentifiés)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| GESTION DES UTILISATEURS (Super Admin uniquement)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-users'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

/*
|--------------------------------------------------------------------------
| GESTION DES ÉQUIPEMENTS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Consultation (Tous les utilisateurs)
    Route::get('/equipment', [EquipmentController::class, 'index'])
        ->name('equipment.index');
    
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])
        ->name('equipment.show');
    
    Route::get('/equipment/stock', [EquipmentController::class, 'stock'])
        ->name('equipment.stock');
    
    Route::get('/equipment/{equipment}/audit', [AuditController::class, 'equipmentHistory'])
        ->name('equipment.audit');
    
    // Gestion (Super Admin + Agent IT)
    Route::middleware('can:manage-equipment')->group(function () {
        Route::get('/equipment/create', [EquipmentController::class, 'create'])
            ->name('equipment.create');
        
        Route::post('/equipment', [EquipmentController::class, 'store'])
            ->name('equipment.store');
        
        Route::get('/equipment/{equipment}/edit', [EquipmentController::class, 'edit'])
            ->name('equipment.edit');
        
        Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])
            ->name('equipment.update');
        
        Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])
            ->name('equipment.destroy');
        
        // Import/Export
        Route::get('/equipment/import', [EquipmentController::class, 'showImportForm'])
            ->name('equipment.import');
        
        Route::post('/equipment/import', [EquipmentController::class, 'import'])
            ->name('equipment.import.process');
        
        Route::get('/equipment/import-template', [EquipmentController::class, 'downloadTemplate'])
            ->name('equipment.import.template');
        
        Route::get('/equipment/export-template', [EquipmentController::class, 'downloadTemplate'])
            ->name('equipment.export.template');
        
        Route::get('/equipment/export', [EquipmentController::class, 'export'])
            ->name('equipment.export');
        
        Route::get('/equipment/export/full', [EquipmentController::class, 'exportFull'])
            ->name('equipment.export.full');
    });
});

/*
|--------------------------------------------------------------------------
| TRANSITIONS D'ÉTAT DES ÉQUIPEMENTS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-equipment'])->group(function () {
    Route::prefix('equipment/{equipment}/transitions')->name('equipment.transitions.')->group(function () {
        Route::get('/', [TransitionController::class, 'showTransitionForm'])->name('');
        Route::post('/simple-affectation', [TransitionController::class, 'simpleAffectation'])->name('simpleAffectation');
        Route::post('/submit-all', [TransitionController::class, 'submitAllForms'])->name('submitAll');
        Route::post('/stock-to-parc', [TransitionController::class, 'stockToParc'])->name('stock-to-parc');
        Route::post('/parc-to-maintenance', [TransitionController::class, 'parcToMaintenance'])->name('parc-to-maintenance');
        Route::post('/maintenance-to-stock', [TransitionController::class, 'maintenanceToStock'])->name('maintenance-to-stock');
        Route::post('/parc-to-hors-service', [TransitionController::class, 'parcToHorsService'])->name('parc-to-hors-service');
        Route::post('/parc-to-perdu', [TransitionController::class, 'parcToPerdu'])->name('parc-to-perdu');
        Route::post('/stock-to-hors-service', [TransitionController::class, 'stockToHorsService'])->name('stock-to-hors-service');
        Route::post('/maintenance-to-hors-service', [TransitionController::class, 'maintenanceToHorsService'])->name('maintenance-to-hors-service');
    });
    
    Route::post('/equipment/{equipment}/transition/execute', [TransitionController::class, 'executeTransition'])
        ->name('transitions.execute');
    
    Route::post('/transitions/{equipment}/submit-approval', [TransitionController::class, 'submitApproval'])
        ->name('transitions.submit');
});

/*
|--------------------------------------------------------------------------
| APPROBATIONS (Super Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-all'])->group(function () {
    Route::get('/admin/approvals', [TransitionController::class, 'pendingApprovals'])
        ->name('admin.approvals');
    
    Route::get('/admin/approvals/{approval}', [TransitionController::class, 'show'])
        ->name('admin.approvals.show');
    
    Route::delete('/admin/approvals/{approval}', [SuperAdminController::class, 'destroyApproval'])
        ->name('admin.approvals.destroy');
    
    Route::get('/transitions/approval/{approval}', [TransitionController::class, 'showApproval'])
        ->name('transitions.approval.show');
    
    Route::post('/transitions/approval/{approval}/approve', [TransitionController::class, 'approveTransition'])
        ->name('transitions.approve');
    
    Route::post('/transitions/approval/{approval}/reject', [TransitionController::class, 'rejectTransition'])
        ->name('transitions.reject');
    
    // Téléchargements
    Route::get('/transitions/fiche-installation/{id}/download', [TransitionController::class, 'downloadFicheInstallation'])
        ->name('transitions.fiche-installation.download');
    
    Route::get('/transitions/fiche-mouvement/{id}/download', [TransitionController::class, 'downloadFicheMouvement'])
        ->name('transitions.fiche-mouvement.download');
    
    Route::get('/transitions/approval/{id}/download', [TransitionController::class, 'downloadApproval'])
        ->name('transitions.approval.download');
    
    // Attachments
    Route::get('/approvals/{approval}/attachments', [TransitionController::class, 'showAttachments'])
        ->name('approvals.attachments.show');
    
    Route::get('/transitions/{approval}/attachments', [TransitionController::class, 'listAttachments'])
        ->name('transitions.attachments.list');
    
    Route::post('/transitions/{approval}/attachments', [TransitionController::class, 'storeAttachment'])
        ->name('transitions.attachments.store');
    
    Route::delete('/transitions/{approval}/attachments', [TransitionController::class, 'destroyAttachment'])
        ->name('transitions.attachments.destroy');
    
    Route::get('/transitions/{approval}/attachments/download', [TransitionController::class, 'downloadAttachment'])
        ->name('transitions.attachments.download');
});

/*
|--------------------------------------------------------------------------
| AUDIT (Super Admin + Agent IT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-equipment'])->prefix('audits')->name('audits.')->group(function () {
    Route::get('/', [AuditController::class, 'index'])->name('index');
    Route::get('/export', [AuditController::class, 'export'])->name('export');
});

/*
|--------------------------------------------------------------------------
| PARC, PERDU, HORS SERVICE, MAINTENANCE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-equipment'])->group(function () {
    Route::resource('parc', ParcController::class);
    Route::resource('perdu', PerduController::class);
    Route::resource('hors-service', HorsServiceController::class);
    Route::resource('maintenance', MaintenanceController::class);
    
    Route::post('/perdu/{perdu}/retrouver', [PerduController::class, 'retrouver'])
        ->name('perdu.retrouver');
    
    Route::post('/hors-service/{hors_service}/traiter', [HorsServiceController::class, 'traiter'])
        ->name('hors-service.traiter');
    
    Route::post('/maintenance/{maintenance}/terminer', [MaintenanceController::class, 'terminer'])
        ->name('maintenance.terminer');
    
    Route::post('/maintenance/{maintenance}/annuler', [MaintenanceController::class, 'annuler'])
        ->name('maintenance.annuler');
});

/*
|--------------------------------------------------------------------------
| FOURNISSEURS (Super Admin + Agent IT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-suppliers'])->group(function () {
    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers/list', [SupplierController::class, 'getSuppliers'])
        ->name('suppliers.list');
});

/*
|--------------------------------------------------------------------------
| CATÉGORIES (Super Admin + Agent IT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-categories'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::get('/categories/trash', [CategoryController::class, 'trash'])->name('categories.trash');
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');
    Route::get('/categories-by-type/{type}', [CategoryController::class, 'categoriesByType']);
});

/*
|--------------------------------------------------------------------------
| AGENCES (Super Admin + Agent IT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-agencies'])->group(function () {
    Route::resource('agencies', AgencyController::class);
});

/*
|--------------------------------------------------------------------------
| DASHBOARDS STOCK (Super Admin + Agent IT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:view-stock-deceler'])->prefix('dashboard')->group(function () {
    // Céler
    Route::get('/celer-informatique', [CelerDashboardController::class, 'index'])->name('dashboard.celer-informatique');
    Route::get('/celer-reseau', [CelerReseauDashboardController::class, 'index'])->name('dashboard.celer-reseau');
    Route::get('/celer-electronique', [CelerElectroniqueDashboardController::class, 'index'])->name('dashboard.celer-electronique');
    
    // Décéler
    Route::get('/deceler-informatique', [DecelerDashboardController::class, 'index'])->name('dashboard.deceler-informatique');
    Route::get('/deceler-reseau', [DecelerReseauDashboardController::class, 'index'])->name('dashboard.deceler-reseau');
    Route::get('/deceler-electronique', [DecelerElectroniqueDashboardController::class, 'index'])->name('dashboard.deceler-electronique');
});

/*
|--------------------------------------------------------------------------
| RAPPORTS (Super Admin + Agent IT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:view-reports'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/equipment', [ReportController::class, 'equipmentReport'])->name('equipment');
    Route::get('/financial', [ReportController::class, 'financialReport'])->name('financial');
    Route::get('/maintenance', [ReportController::class, 'maintenanceReport'])->name('maintenance');
    Route::get('/categories', [ReportController::class, 'categoriesReport'])->name('categories');
    
    // Imports
    Route::get('/import/equipment', [ReportController::class, 'importEquipment'])->name('import.equipment');
    Route::post('/import/process', [ReportController::class, 'processImport'])->name('import.process');
    
    // Exports
    Route::get('/export/equipment', [ReportController::class, 'exportEquipment'])->name('export.equipment');
    Route::get('/export/agencies', [ReportController::class, 'exportAgencies'])->name('export.agencies');
    Route::get('/export/suppliers', [ReportController::class, 'exportSuppliers'])->name('export.suppliers');
    Route::get('/export/categories', [ReportController::class, 'exportCategories'])->name('export.categories');
});

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD (Super Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:manage-all'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/stats', [SuperAdminController::class, 'stats'])->name('admin.stats');
});

/*
|--------------------------------------------------------------------------
| ROUTES DE DEBUG (Environnement local uniquement)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/routes', function () {
        $routes = Route::getRoutes();
        echo "<style>body{font-family:monospace;font-size:12px;}</style>";
        echo "<h1>Routes disponibles</h1>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr style='background:#eee'><th>Method</th><th>URI</th><th>Name</th><th>Middleware</th></tr>";
        
        foreach ($routes as $route) {
            echo "<tr>";
            echo "<td>" . implode('|', $route->methods()) . "</td>";
            echo "<td>" . $route->uri() . "</td>";
            echo "<td>" . ($route->getName() ?? '-') . "</td>";
            echo "<td><small>" . implode(', ', $route->gatherMiddleware()) . "</small></td>";
            echo "</tr>";
        }
        echo "</table>";
    })->name('debug.routes');
}