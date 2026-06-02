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

try {
    Illuminate\Support\Facades\Mail::raw(
        'Test envoi GPI ' . date('Y-m-d H:i:s'),
        fn ($m) => $m->to($to)->subject('Test SMTP GPI')
    );
    echo "SEND OK to {$to}" . PHP_EOL;
} catch (Throwable $e) {
    echo 'SEND FAIL: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
