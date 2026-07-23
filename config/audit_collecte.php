<?php

/**
 * Collecte automatique des audits postes (script PowerShell).
 *
 * Rotation de clé :
 * 1. Générer une nouvelle clé : php artisan tinker → Str::random(64)
 * 2. Déployer AUDIT_API_KEY_PREVIOUS = ancienne clé, AUDIT_API_KEY = nouvelle
 * 3. Mettre à jour les scripts / GPO sur le parc
 * 4. Retirer AUDIT_API_KEY_PREVIOUS une fois le déploiement terminé
 */
return [

    'api_key' => env('AUDIT_API_KEY'),

    /** Ancienne clé encore acceptée pendant une rotation. */
    'api_key_previous' => env('AUDIT_API_KEY_PREVIOUS'),

    /** Header attendu (alternative : Authorization Bearer). */
    'header' => env('AUDIT_API_HEADER', 'X-API-Key'),

    /** Limite POST /api/audit : max tentatives par minute et par IP. */
    'rate_limit_per_minute' => (int) env('AUDIT_API_RATE_LIMIT', 60),
];
