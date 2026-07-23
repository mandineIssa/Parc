<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

final class SecureLog
{
    private static array $extraSensitiveKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'access_token',
        'refresh_token',
        'authorization',
        'secret',
        'api_key',
        'csrf_token',
        // Données nominatives (RGPD) — traçabilité via hash côté ingest
        'utilisateursession',
        'utilisateur_session',
    ];

    /**
     * Données de requête allégées pour les journaux (jamais de secrets ni payloads complets bruts).
     *
     * @return array<string, mixed>
     */
    public static function requestPayload(Request $request): array
    {
        $except = ['_token', ...self::$extraSensitiveKeys];

        $data = $request->except($except);

        foreach (array_keys($data) as $key) {
            $lk = strtolower((string) $key);
            foreach (self::$extraSensitiveKeys as $sensitive) {
                if (str_contains($lk, $sensitive)) {
                    $data[$key] = '[REDACTED]';

                    break;
                }
            }
        }

        return [
            'path' => $request->path(),
            'method' => $request->method(),
            'keys' => array_keys($data),
            'payload' => $data,
        ];
    }
}
