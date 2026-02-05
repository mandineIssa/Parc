<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
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


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route d'accueil publique
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ===========================================================================
// ROUTES D'AUTHENTIFICATION (gérées par Breeze)
// ===========================================================================

// ✅ MODIFICATION 1 : Suppression des lignes en doublon pour /dashboard
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

/* Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php'; */
// ===========================================================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Route temporaire pour tester
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

Route::post('/register', function (Request $request) {
    // Logique d'inscription temporaire
    return redirect('/login');
})->middleware('guest');

// ✅ MODIFICATION 2 : Routes utilisateurs avec préfixe /admin
// Routes pour la gestion des utilisateurs (protégées par authentification)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class);
    });
});

// ===========================================================================
// ROUTES PROTÉGÉES - GESTION DU PARC
// ===========================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ============================================
    // GESTION DES ÉQUIPEMENTS (CRUD complet)
    // ============================================
    Route::resource('equipment', EquipmentController::class);
    
    Route::get('/equipment/{equipment}/audit', [AuditController::class, 'equipmentHistory'])
        ->name('equipment.audit');
    
    Route::get('/equipment/stock', [EquipmentController::class, 'stock'])
        ->name('equipment.stock');
    
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

// ============================================
// IMPORT / EXPORT / PAGES SPÉCIALES ÉQUIPEMENT
// ⚠️ TOUJOURS AVANT resource
// ============================================


// routes/web.php
Route::post('/parc', [ParcController::class, 'store'])->name('parc.store');

// Utiliser "imports" au pluriel
Route::prefix('equipment')->name('equipment.')->group(function () {
    Route::get('/imports', [EquipmentImportController::class, 'showImportForm'])        // Au lieu de /import
        ->name('imports.form');        // Au lieu de import.form
});
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
    Route::get('/debug-csv', [EquipmentController::class, 'debugCsv'])->name('debug.csv');
    Route::get('/import-test', [EquipmentController::class, 'importTest'])->name('import.test');
    
});

// Routes pour l'import CSV vers le parc
Route::get('equipment/parc/import', [ParcController::class, 'showImportForm'])->name('parc.import.form');
Route::post('equipment/parc/import', [ParcController::class, 'import'])->name('parc.import');
Route::get('equipment/parc/import/template', [ParcController::class, 'downloadTemplate'])->name('parc.import.template');
Route::get('equipment/parc/export', [ParcController::class, 'export'])->name('parc.export');
Route::post('equipment/parc/debug-csv', [ParcController::class, 'debugCsv'])->name('parc.debug.csv');
// ============================================
// CRUD ÉQUIPEMENTS (APRÈS)
// ============================================

Route::resource('equipment', EquipmentController::class)
    ->whereNumber('equipment'); // ✅ SÉCURITÉ EN PLUS

    // Routes pour l'import du parc
Route::prefix('parc')->name('parc.')->group(function () {
    // ... autres routes existantes ...
    
    Route::get('/import', [ParcController::class, 'showImportForm'])->name('import');
    Route::post('/import', [ParcController::class, 'processImport'])->name('import.process');
    Route::get('/import/template', [ParcController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/equipment/debug-csv', [EquipmentController::class, 'debugCsv'])->name('equipment.debug.csv');
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
});

// =========================================================================
// ROUTES D'APPROBATION - SUPER ADMIN / RESPONSABLE APPROBATION
// ✅ AUTORISATION GÉRÉE DANS LE CONTROLLER
// =========================================================================

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

// Routes pour les fichiers attachés
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

// =========================================================================
// ROUTES D'APPROBATION - SUPER ADMIN / RESPONSABLE APPROBATION
// =========================================================================
Route::post('/equipment/{equipment}/transition/reject', 
    [TransitionController::class, 'rejectTransition']
);

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

// Routes pour les transitions
Route::prefix('equipment/{equipment}/transition')->name('transitions.')->group(function () {
    Route::get('/', [TransitionController::class, 'showTransitionForm'])->name('show');
    Route::post('/execute', [TransitionController::class, 'executeTransition'])->name('execute');
    Route::post('/submit', [TransitionController::class, 'submitForm'])->name('submit');
    Route::post('/submitAll', [TransitionController::class, 'submitAllForms'])->name('submitAll'); // AJOUTER CETTE LIGNE
});

 /*
    |--------------------------------------------------------------------------
    | ADMIN – Gestion des utilisateurs
    |--------------------------------------------------------------------------
    */
Route::middleware(['can:manage-users'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index'); // Sans 'admin.' préfixe
    
    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create');
    
    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store');
    
    Route::get('/users/{user}', [UserController::class, 'show']) // AJOUTÉE
        ->name('users.show');
    
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
    
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');
    
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');
});
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
    
    // ✅ AJOUTEZ CETTE ROUTE POUR LA DOCUMENTATION
    Route::get('/documentation', function () {
        return view('documentation');
    })->name('documentation.index');
    
    // OU si vous préférez utiliser un contrôleur (recommandé) :
    /* Route::get('/documentation', [App\Http\Controllers\DocumentationController::class, 'index'])
        ->name('documentation.index'); */
});

Route::middleware(['auth', 'verified'])->prefix('documentation')->name('documentation.')->group(function () {
    Route::get('/', [App\Http\Controllers\DocumentationController::class, 'index'])->name('index');
    Route::get('/{section}', [App\Http\Controllers\DocumentationController::class, 'show'])->name('show');
    Route::get('/download/{format}', [App\Http\Controllers\DocumentationController::class, 'download'])->name('download');
});

// Routes pour les approbations hors service
Route::get('/admin/approvals/{approval}/hors-service', 
    [TransitionController::class, 'showHorsServiceApproval']
)->name('admin.hors-service-approval')
 ->middleware('auth');

Route::post('/transitions/submit-hors-service', [TransitionController::class, 'submitHorsService'])
    ->name('transitions.submit-hors-service');
Route::post('/admin/approvals/{approval}/approve-hors-service', [TransitionController::class, 'approveHorsService'])
    ->name('transitions.approve-hors-service')
    ->middleware('auth');

Route::post('/admin/approvals/{approval}/reject-hors-service', [TransitionController::class, 'rejectHorsService'])
    ->name('transitions.reject-hors-service')
    ->middleware('auth');

    // routes/web.php
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
Route::get('/admin/maintenance-approvals', [TransitionController::class, 'listMaintenanceApprovals'])
    ->name('admin.maintenance-approvals.list')
    ->middleware('auth');
// Cette route devrait déjà exister
Route::get('/approvals/{approval}', [TransitionController::class, 'showApprovalDetails'])
    ->name('transitions.approval.show')
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
   
        // Routes pour le rejet des demandes hors service
Route::post('/approvals/{approval}/reject-hors-service', [TransitionController::class, 'rejectHorsService'])
    ->name('transitions.reject-hors-service')
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

// Routes pour le DASHBOARD DES SOUMISSIONS (nouveau)
Route::prefix('dashboards')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboards.index');
    Route::get('/stats', [DashboardController::class, 'getStats'])->name('dashboards.stats');
    Route::get('/submissions', [DashboardController::class, 'getSubmissions'])->name('dashboards.submissions');
    Route::get('/charts', [DashboardController::class, 'getCharts'])->name('dashboards.charts');
    Route::get('/export', [DashboardController::class, 'export'])->name('dashboards.export');
    Route::get('/mobile', [DashboardController::class, 'mobileDashboard'])->name('dashboards.mobile');
})->middleware('auth');

// Dashboard routes
Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/redirect', [DashboardController::class, 'redirect'])->name('dashboard.redirect');
    Route::get('/super-admin', [DashboardController::class, 'superAdmin'])->name('dashboard.super-admin');
    Route::get('/agent', [DashboardController::class, 'agent'])->name('dashboard.agent');
    Route::get('/user', [DashboardController::class, 'user'])->name('dashboard.user');
    Route::get('/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/submissions', [DashboardController::class, 'getSubmissions'])->name('dashboard.submissions');
    Route::get('/charts', [DashboardController::class, 'getCharts'])->name('dashboard.charts');
    Route::get('/export', [DashboardController::class, 'export'])->name('dashboard.export');
})->middleware('auth');

// Garder vos routes dashboard existantes (si elles existent)
Route::get('/dashboard', [DashboardsController::class, 'index'])->name('dashboard');





if (app()->environment('local')) {
    
    Route::get('/debug', function () {
        return view('debug');
    });
     Route::get('/routes', function () {
        $routes = Route::getRoutes();
        echo "<style>body{font-family:monospace;}</style>";
        echo "<h1>Routes disponibles</h1>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th></tr>";
        
        foreach ($routes as $route) {
            echo "<tr>";
            echo "<td>" . implode('|', $route->methods()) . "</td>";
            echo "<td>" . $route->uri() . "</td>";
            echo "<td>" . ($route->getName() ?? '-') . "</td>";
            echo "<td>" . $route->getActionName() . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    })->name('debug.routes');

    Route::get('/test-admin', function () {
        $user = auth()->user();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_exact' => var_export($user->role, true),
                    'all_user_data' => $user->toArray(),
                ],
                'gates' => [
                    'super_admin' => Gate::allows('super_admin'),
                ]
            ]
        ]);
    })->middleware('auth');

    Route::get('/fix-super-admin', function () {
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        $user = auth()->user();
        
        if ($user->email === 'superadmin@cofina.sn') {
            $oldRole = $user->role;
            $user->role = 'super_admin';
            $user->save();
            
            return response()->json([
                'message' => 'Rôle corrigé!',
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'is_now_super_admin' => $user->role === 'super_admin',
            ]);
        }
        
        return response()->json(['error' => 'Non autorisé'], 403);
    })->middleware('auth');

    Route::get('/test-gate-debug', function () {
        $user = auth()->user();
        
        $directTest = $user->role === 'super_admin';
        $trimTest = trim($user->role ?? '') === 'super_admin';
        $gateTest = Gate::allows('super_admin');
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'tests' => [
                'direct' => $directTest,
                'trim' => $trimTest,
                'gate_allows' => $gateTest,
            ]
        ]);
    })->middleware('auth');

    Route::get('/admin/test-access', function () {
        $user = auth()->user();
        
        $isSuperAdmin = $user->role === 'super_admin' || $user->email === 'superadmin@cofina.sn';
        
        if (!$isSuperAdmin) {
            return response()->json([
                'error' => 'Accès refusé',
                'debug' => [
                    'role' => $user->role,
                    'email' => $user->email,
                    'is_super_admin' => $isSuperAdmin
                ]
            ], 403);
        }
        
        $pendingApprovals = \App\Models\TransitionApproval::where('status', 'pending')
            ->with(['equipment', 'submitter'])
            ->orderBy('created_at', 'desc')->get();
            return view('admin.dashboard', compact('pendingApprovals'));
})->middleware('auth');

Route::post('/test-form', function(Request $request) {
    Log::info('=== TEST FORM DATA ===');
    Log::info($request->all());
    
    echo "<h1>Données reçues:</h1>";
    echo "<pre>";
    print_r($request->all());
    echo "</pre>";
    
    echo "<h2>Champs importants:</h2>";
    echo "type: " . $request->type . "<br>";
    echo "numero_serie: " . $request->numero_serie . "<br>";
    
    try {
        $equipment = \App\Models\Equipment::create([
            'type' => $request->type,
            'numero_serie' => $request->numero_serie,
            'marque' => $request->marque,
            'modele' => $request->modele,
            'localisation' => $request->localisation,
            'date_livraison' => $request->date_livraison,
            'prix' => $request->prix,
            'garantie' => $request->garantie,
            'etat' => $request->etat,
            'statut' => 'stock',
        ]);
        
        echo "<h3 style='color: green'>✅ Création réussie! ID: " . $equipment->id . "</h3>";
    } catch (\Exception $e) {
        echo "<h3 style='color: red'>❌ Erreur: " . $e->getMessage() . "</h3>";
    }
});

Route::post('/test-submit', function(Request $request) {
Log::info('TEST ROUTE HIT', $request->all());
return response()->json([
    'success' => true,
    'message' => 'Route test fonctionnelle',
    'data' => $request->all()
]);
});
Route::get('/debug-profile-routes', function() {
    $routes = collect(Route::getRoutes())->filter(function($route) {
        return str_contains($route->uri(), 'profile') || str_contains($route->uri(), 'users');
    });
    
    echo "<h1>Routes PROFILE et USERS</h1>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th><th>Middleware</th></tr>";
    
    foreach ($routes as $route) {
        echo "<tr>";
        echo "<td>" . implode('|', $route->methods()) . "</td>";
        echo "<td>" . $route->uri() . "</td>";
        echo "<td>" . ($route->getName() ?? '-') . "</td>";
        echo "<td>" . $route->getActionName() . "</td>";
        echo "<td>" . json_encode($route->gatherMiddleware()) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
});
}   // Fin des routes de debug/developpement