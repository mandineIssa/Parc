<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardsController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function (): void {
        Route::get('/super-admin', [DashboardController::class, 'superAdmin'])->name('super-admin');
        Route::get('/agent', [DashboardController::class, 'agent'])->name('agent');
        Route::get('/user', [DashboardController::class, 'user'])->name('user');
        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
        Route::get('/submissions', [DashboardController::class, 'getSubmissions'])->name('submissions');
        Route::get('/charts', [DashboardController::class, 'getCharts'])->name('charts');
        Route::get('/export', [DashboardController::class, 'export'])->name('export');
    });

    Route::prefix('dashboards')->name('dashboards.')->group(function (): void {
        Route::get('/', [DashboardsController::class, 'index'])->name('index');
        Route::get('/stats', [DashboardsController::class, 'getStats'])->name('stats');
        Route::get('/submissions', [DashboardsController::class, 'getSubmissions'])->name('submissions');
        Route::get('/charts', [DashboardsController::class, 'getCharts'])->name('charts');
        Route::get('/export', [DashboardsController::class, 'export'])->name('export');
        Route::get('/mobile', [DashboardsController::class, 'mobileDashboard'])->name('mobile');
    });
});
