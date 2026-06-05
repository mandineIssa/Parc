<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'mail.default=' . config('mail.default') . PHP_EOL;
echo 'smtp.host=' . config('mail.mailers.smtp.host') . PHP_EOL;
echo 'smtp.port=' . config('mail.mailers.smtp.port') . PHP_EOL;
echo 'smtp.encryption=' . var_export(config('mail.mailers.smtp.encryption'), true) . PHP_EOL;
echo 'from=' . config('mail.from.address') . PHP_EOL;

$n3 = App\Models\User::all()->filter(fn ($u) => $u->canAccessEodAsN3());
$ctrl = App\Models\User::all()->filter(fn ($u) => $u->canSignEodControllerSlot());

echo 'N3 users: ' . $n3->count() . PHP_EOL;
foreach ($n3 as $u) {
    echo "  - #{$u->id} {$u->email} role={$u->role} role_change={$u->role_change}" . PHP_EOL;
}

echo 'Controller users: ' . $ctrl->count() . PHP_EOL;
foreach ($ctrl as $u) {
    echo "  - #{$u->id} {$u->email} role={$u->role} role_change={$u->role_change}" . PHP_EOL;
}

$to = $argv[1] ?? config('mail.from.address');
$user = App\Models\User::query()->where('email', $to)->first();
$recipientName = $user
    ? trim((string) $user->prenom . ' ' . (string) $user->name) ?: $to
    : 'Utilisateur test';

echo PHP_EOL . '--- Test 1 : SMTP brut ---' . PHP_EOL;
try {
    Illuminate\Support\Facades\Mail::raw(
        'Test envoi GPI ' . date('Y-m-d H:i:s'),
        fn ($m) => $m->to($to)->subject('Test SMTP GPI')
    );
    echo "SEND OK (raw) to {$to}" . PHP_EOL;
} catch (Throwable $e) {
    echo 'SEND FAIL (raw): ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL . '--- Test 2 : template GpiNotificationMail ---' . PHP_EOL;
try {
    Illuminate\Support\Facades\Mail::to($to, $recipientName)->send(
        new App\Mail\GpiNotificationMail(
            '[GPI] Test notification — ' . date('Y-m-d H:i:s'),
            'Test du template COFINA',
            "Ceci est un test d'envoi avec le template GPI.\nRéférence : TEST-" . date('Ymd-His'),
            $recipientName,
            url('/dashboard'),
            'Ouvrir GPI'
        )
    );
    echo "SEND OK (GpiNotificationMail) to {$to}" . PHP_EOL;
} catch (Throwable $e) {
    echo 'SEND FAIL (GpiNotificationMail): ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
