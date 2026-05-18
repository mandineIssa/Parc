<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Comptes super-admin bootstrap (accès Gates « équivalent super_admin »)
    |--------------------------------------------------------------------------
    |
    | Liste configurable pour éviter l’email codé en dur dans plusieurs fichiers.
    | En production : utilisez des rôles/permissions Spatie et réduisez cette liste.
    |
    */
    'super_admin_emails' => array_values(array_filter(array_map(
        static fn (string $email): string => strtolower(trim($email)),
        explode(',', (string) env('SUPER_ADMIN_EMAILS', 'superadmin@cofina.sn'))
    ))),
];
