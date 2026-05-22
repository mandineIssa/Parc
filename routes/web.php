<?php

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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Accueil : connexion (invité) ou tableau de bord (connecté)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
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

    Route::get('/profile/signature', [\App\Http\Controllers\ProfileSignatureController::class, 'show'])
        ->name('profile.signature.show');
    Route::post('/profile/signature', [\App\Http\Controllers\ProfileSignatureController::class, 'store'])
        ->name('profile.signature.store');
    Route::delete('/profile/signature', [\App\Http\Controllers\ProfileSignatureController::class, 'destroy'])
        ->name('profile.signature.destroy');
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

/*
|--------------------------------------------------------------------------
| Routes métier modulaires (vague 1 — fichiers dans routes/web/)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function (): void {
    require base_path('routes/web/v01_verified_equipment_parc.php');
});

// Documentation (auth seul — évite blocage si email non vérifié)
Route::middleware(['auth'])->prefix('documentation')->name('documentation.')->group(function (): void {
    Route::get('/', [\App\Http\Controllers\DocumentationController::class, 'index'])->name('index');
    Route::get('/pdf/manuel', [\App\Http\Controllers\DocumentationController::class, 'downloadManuelPdf'])->name('manuel.pdf');
    Route::get('/download/{format}', [\App\Http\Controllers\DocumentationController::class, 'download'])
        ->name('download')
        ->where('format', 'pdf|zip');
    Route::get('/{section}', [\App\Http\Controllers\DocumentationController::class, 'show'])
        ->name('show')
        ->where('section', 'utilisateur|admin|agent-it|manuel-complet|api|installation');
});

require base_path('routes/web/v02a_verified_approvals_primary.php');
require base_path('routes/web/v02b_verified_attachments.php');
require base_path('routes/web/v02c_transition_reject_post.php');
require base_path('routes/web/v02d_verified_approvals_secondary.php');
require base_path('routes/web/v02e_equipment_transition_prefix.php');
require base_path('routes/web/v02f_admin_users_manage.php');

require base_path('routes/web/v03_reports_documentation.php');
require base_path('routes/web/v04_workflow_transitions.php');
require base_path('routes/web/v05_dashboards.php');

require base_path('routes/web/v06_change_eod.php');
require base_path('routes/web/v07_passwords_network_licences.php');
require base_path('routes/web/v08_controls.php');
require base_path('routes/web/v09_incidents.php');
