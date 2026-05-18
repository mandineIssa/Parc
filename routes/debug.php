<?php

/**
 * Routes de diagnostic — chargées uniquement en environnement local
 * via bootstrap/app.php. Ne jamais activer en production.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/debug', function () {
    return view('debug');
})->name('debug.app');

Route::get('/routes', function () {
    $routes = Route::getRoutes();
    echo "<style>body{font-family:monospace;}</style>";
    echo "<h1>Routes disponibles</h1>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th></tr>";

    foreach ($routes as $route) {
        echo '<tr>';
        echo '<td>'.implode('|', $route->methods()).'</td>';
        echo '<td>'.$route->uri().'</td>';
        echo '<td>'.($route->getName() ?? '-').'</td>';
        echo '<td>'.$route->getActionName().'</td>';
        echo '</tr>';
    }

    echo '</table>';
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
        ],
      ],
    ]);
})->middleware('auth');

Route::get('/fix-super-admin', function () {
    if (! auth()->check()) {
        return redirect('/login');
    }

    $user = auth()->user();
    $allowed = config('cofina.super_admin_emails', []);

    if (! in_array($user->email, $allowed, true)) {
        return response()->json(['error' => 'Non autorisé'], 403);
    }

    $oldRole = $user->role;
    $user->role = 'super_admin';
    $user->save();

    return response()->json([
        'message' => 'Rôle corrigé!',
        'old_role' => $oldRole,
        'new_role' => $user->role,
        'is_now_super_admin' => $user->role === 'super_admin',
    ]);
})->middleware('auth');

Route::get('/test-gate-debug', function () {
    $user = auth()->user();

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
        'tests' => [
            'direct' => $user->role === 'super_admin',
            'trim' => trim($user->role ?? '') === 'super_admin',
            'gate_allows' => Gate::allows('super_admin'),
        ],
    ]);
})->middleware('auth');

Route::get('/admin/test-access', function () {
    $user = auth()->user();
    $allowed = config('cofina.super_admin_emails', []);
    $isSuperAdmin = $user->role === 'super_admin' || in_array($user->email, $allowed, true);

    if (! $isSuperAdmin) {
        return response()->json([
            'error' => 'Accès refusé',
            'debug' => [
                'role' => $user->role,
                'email' => $user->email,
                'is_super_admin' => $isSuperAdmin,
            ],
        ], 403);
    }

    $pendingApprovals = \App\Models\TransitionApproval::query()
        ->where('status', 'pending')
        ->with(['equipment', 'submitter'])
        ->orderByDesc('created_at')
        ->get();

    return view('admin.dashboard', compact('pendingApprovals'));
})->middleware('auth');

Route::post('/test-form', function (Request $request) {
    Log::info('=== TEST FORM DATA (local) ===', ['keys' => array_keys($request->except(['password', 'password_confirmation']))]);

    echo '<h1>Données reçues:</h1>';
    echo '<pre>';
    print_r($request->except(['password', 'password_confirmation']));
    echo '</pre>';

    echo '<h2>Champs importants:</h2>';
    echo 'type: '.$request->type.'<br>';
    echo 'numero_serie: '.$request->numero_serie.'<br>';

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

        echo '<h3 style="color: green">✅ Création réussie! ID: '.$equipment->id.'</h3>';
    } catch (\Exception $e) {
        echo '<h3 style="color: red">❌ Erreur: '.e($e->getMessage()).'</h3>';
    }
});

Route::post('/test-submit', function (Request $request) {
    Log::info('TEST ROUTE HIT (local)', ['keys' => array_keys($request->except(['password', 'password_confirmation']))]);

    return response()->json([
        'success' => true,
        'message' => 'Route test fonctionnelle',
        'data' => $request->except(['password', 'password_confirmation']),
    ]);
});

Route::get('/debug-profile-routes', function () {
    $routes = collect(Route::getRoutes())->filter(function ($route) {
        return str_contains($route->uri(), 'profile') || str_contains($route->uri(), 'users');
    });

    echo "<h1>Routes PROFILE et USERS</h1>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th><th>Middleware</th></tr>";

    foreach ($routes as $route) {
        echo '<tr>';
        echo '<td>'.implode('|', $route->methods()).'</td>';
        echo '<td>'.$route->uri().'</td>';
        echo '<td>'.($route->getName() ?? '-').'</td>';
        echo '<td>'.$route->getActionName().'</td>';
        echo '<td>'.json_encode($route->gatherMiddleware()).'</td>';
        echo '</tr>';
    }

    echo '</table>';
});

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('equipment')
    ->name('equipment.')
    ->group(function () {
        Route::get('/debug-csv', [\App\Http\Controllers\EquipmentController::class, 'debugCsv'])
            ->name('debug.csv');

        Route::get('/import-test', [\App\Http\Controllers\EquipmentController::class, 'importTest'])
            ->name('import.test');
    });

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::post('equipment/parc/debug-csv', [\App\Http\Controllers\ParcController::class, 'debugCsv'])
        ->name('parc.debug.csv');

    Route::prefix('parc')->name('parc.')->group(function () {
        Route::post('/equipment/debug-csv', [\App\Http\Controllers\EquipmentController::class, 'debugCsv'])
            ->name('equipment.debug.csv');
    });
});
