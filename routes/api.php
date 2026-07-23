<?php

use App\Http\Controllers\Api\AuditCollecteController;
use App\Http\Controllers\Api\V1\ParcApiController;
use App\Http\Controllers\Api\V1\StockApiController;
use Illuminate\Support\Facades\Route;

/*
| Collecte audits postes (script PowerShell) — clé API + rate limit.
| Consultation / export — session authentifiée (comme /api/v1).
*/
Route::post('/audit', [AuditCollecteController::class, 'store'])
    ->middleware(['audit.api_key', 'throttle:audit-collecte'])
    ->name('api.audit.store');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/audit/export', [AuditCollecteController::class, 'export'])->name('api.audit.export');
    Route::get('/audit', [AuditCollecteController::class, 'index'])->name('api.audit.index');
    Route::get('/audit/{id}', [AuditCollecteController::class, 'show'])
        ->whereNumber('id')
        ->name('api.audit.show');
});

Route::middleware(['auth'])->prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/parc', [ParcApiController::class, 'index'])->name('parc.index');
    Route::get('/parc/{numeroSerie}', [ParcApiController::class, 'show'])->name('parc.show');
    Route::get('/stock', [StockApiController::class, 'index'])->name('stock.index');
});
