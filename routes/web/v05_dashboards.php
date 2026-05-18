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
