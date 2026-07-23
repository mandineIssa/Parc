<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentifie les scripts PowerShell via X-API-Key ou Authorization: Bearer.
 * Accepte la clé courante et, pendant une rotation, la clé précédente.
 */
class VerifyAuditApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $this->extractKey($request);
        $validKeys = array_values(array_filter([
            config('audit_collecte.api_key'),
            config('audit_collecte.api_key_previous'),
        ], fn ($key) => is_string($key) && $key !== ''));

        if ($provided === null || $provided === '' || $validKeys === [] || ! $this->isValidKey($provided, $validKeys)) {
            return response()->json([
                'message' => 'Non autorisé. Clé API manquante ou invalide.',
                'error' => 'unauthorized',
            ], 401);
        }

        return $next($request);
    }

    private function extractKey(Request $request): ?string
    {
        $headerName = config('audit_collecte.header', 'X-API-Key');
        $fromHeader = $request->header($headerName);

        if (is_string($fromHeader) && $fromHeader !== '') {
            return trim($fromHeader);
        }

        $bearer = $request->bearerToken();
        if (is_string($bearer) && $bearer !== '') {
            return trim($bearer);
        }

        return null;
    }

    /**
     * @param  list<string>  $validKeys
     */
    private function isValidKey(string $provided, array $validKeys): bool
    {
        foreach ($validKeys as $key) {
            if (hash_equals($key, $provided)) {
                return true;
            }
        }

        return false;
    }
}
