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
       Route::prefix('passwords')->name('passwords.')->group(function () {
        Route::get('/',                                   [PasswordController::class, 'index'])         ->name('index');
        Route::get('/create',                             [PasswordController::class, 'create'])        ->name('create');
        Route::post('/',                                  [PasswordController::class, 'store'])         ->name('store');
        Route::get('/{password}',                         [PasswordController::class, 'show'])          ->name('show');
        Route::get('/{password}/edit',                    [PasswordController::class, 'edit'])          ->name('edit');
        Route::put('/{password}',                         [PasswordController::class, 'update'])        ->name('update');
        Route::delete('/{password}',                      [PasswordController::class, 'destroy'])       ->name('destroy');
        // OTP
        Route::post('/{password}/otp/send',               [PasswordController::class, 'sendOtp'])       ->name('otp.send');
        Route::post('/{password}/otp/verify',             [PasswordController::class, 'verifyOtp'])     ->name('otp.verify');
        // Partages
        Route::post('/{password}/share',                  [PasswordController::class, 'share'])         ->name('share');
        Route::patch('/{password}/share/{share}',         [PasswordController::class, 'updateShare'])   ->name('share.update');
        Route::delete('/{password}/share/{share}',        [PasswordController::class, 'revokeShare'])   ->name('share.revoke');
        // Fichiers
        Route::get('/{password}/fichier/{id}/download',   [PasswordController::class, 'downloadFichier'])->name('fichier.download');
        Route::delete('/{password}/fichier/{id}',         [PasswordController::class, 'deleteFichier']) ->name('fichier.delete');
    });
    Route::prefix('network')->name('network.')->group(function () {
        Route::get('/',               [NetworkAddressController::class, 'index'])  ->name('index');
        Route::get('/create',         [NetworkAddressController::class, 'create']) ->name('create');
        Route::post('/',              [NetworkAddressController::class, 'store'])  ->name('store');
        Route::get('/{network}/edit', [NetworkAddressController::class, 'edit'])   ->name('edit');
        Route::put('/{network}',      [NetworkAddressController::class, 'update']) ->name('update');
        Route::delete('/{network}',   [NetworkAddressController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('licences')->name('licences.')->group(function () {
        Route::get('/',               [LicenceController::class, 'index'])  ->name('index');
        Route::get('/create',         [LicenceController::class, 'create']) ->name('create');
        Route::post('/',              [LicenceController::class, 'store'])  ->name('store');
        Route::get('/{licence}/edit', [LicenceController::class, 'edit'])   ->name('edit');
        Route::put('/{licence}',      [LicenceController::class, 'update']) ->name('update');
        Route::delete('/{licence}',   [LicenceController::class, 'destroy'])->name('destroy');
    });
});
