<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Gestion de la clé API de collecte audits postes (rotation sans coupure).
 *
 * Usage :
 *   php artisan audit:api-key status
 *   php artisan audit:api-key generate
 *   php artisan audit:api-key rotate
 *   php artisan audit:api-key finalize
 */
class AuditApiKeyCommand extends Command
{
    protected $signature = 'audit:api-key
                            {action=status : status|generate|rotate|finalize}
                            {--length=32 : Octets aléatoires pour generate/rotate (hex = 2x)}';

    protected $description = 'Gère la clé API audits postes (génération et rotation sans coupure)';

    public function handle(): int
    {
        return match ($this->argument('action')) {
            'status' => $this->status(),
            'generate' => $this->generate(),
            'rotate' => $this->rotate(),
            'finalize' => $this->finalize(),
            default => $this->invalidAction(),
        };
    }

    private function status(): int
    {
        $current = config('audit_collecte.api_key');
        $previous = config('audit_collecte.api_key_previous');
        $header = config('audit_collecte.header', 'X-API-Key');
        $limit = config('audit_collecte.rate_limit_per_minute', 60);

        $this->info('Collecte audits postes — état de la clé API');
        $this->line('  Header            : '.$header);
        $this->line('  Rate limit        : '.$limit.'/min/IP');
        $this->line('  AUDIT_API_KEY     : '.$this->mask($current));
        $this->line('  AUDIT_API_KEY_PREVIOUS : '.$this->mask($previous));

        if (empty($current)) {
            $this->warn('Aucune clé courante : POST /api/audit renverra 401.');
            $this->comment('Générer puis coller dans .env : php artisan audit:api-key generate');

            return self::FAILURE;
        }

        if (! empty($previous)) {
            $this->comment('Rotation en cours : les deux clés sont acceptées (sans coupure).');
            $this->comment('Quand le parc est à jour : php artisan audit:api-key finalize');
        } else {
            $this->info('Une seule clé active (pas de fenêtre de rotation).');
        }

        return self::SUCCESS;
    }

    private function generate(): int
    {
        $key = $this->newKey();
        $this->info('Nouvelle clé générée (à coller dans .env) :');
        $this->newLine();
        $this->line('AUDIT_API_KEY='.$key);
        $this->newLine();
        $this->comment('Puis : php artisan config:clear && php artisan config:cache');
        $this->comment('Et la même valeur dans scripts/audit-poste/config.json (ApiKey).');

        return self::SUCCESS;
    }

    private function rotate(): int
    {
        $current = (string) (config('audit_collecte.api_key') ?: env('AUDIT_API_KEY'));

        if ($current === '') {
            $this->error('Impossible de pivoter : AUDIT_API_KEY est vide.');
            $this->comment('Commence par : php artisan audit:api-key generate');

            return self::FAILURE;
        }

        $newKey = $this->newKey();

        $this->warn('Rotation sans coupure — copie ces lignes dans le .env PROD :');
        $this->newLine();
        $this->line('AUDIT_API_KEY='.$newKey);
        $this->line('AUDIT_API_KEY_PREVIOUS='.$current);
        $this->newLine();
        $this->info('Ensuite sur le serveur :');
        $this->line('  php artisan config:clear');
        $this->line('  php artisan config:cache');
        $this->newLine();
        $this->info('Puis mets à jour config.json sur le partage GPO :');
        $this->line('  "ApiUrl": "https://gpi.cofinaonline.com"');
        $this->line('  "ApiKey": "'.$newKey.'"');
        $this->newLine();
        $this->comment('Les postes encore sur l\'ancienne clé continueront de fonctionner.');
        $this->comment('Quand tout le parc est migré : php artisan audit:api-key finalize');

        return self::SUCCESS;
    }

    private function finalize(): int
    {
        $current = (string) (config('audit_collecte.api_key') ?: env('AUDIT_API_KEY'));
        $previous = config('audit_collecte.api_key_previous');

        if (empty($previous)) {
            $this->info('Rien à finaliser : AUDIT_API_KEY_PREVIOUS est déjà vide.');

            return self::SUCCESS;
        }

        if ($current === '') {
            $this->error('AUDIT_API_KEY est vide — ne finalise pas maintenant.');

            return self::FAILURE;
        }

        $this->warn('Finalisation — dans le .env PROD, garde uniquement :');
        $this->newLine();
        $this->line('AUDIT_API_KEY='.$current);
        $this->newLine();
        $this->line('Supprime (ou commente) la ligne :');
        $this->line('AUDIT_API_KEY_PREVIOUS=...');
        $this->newLine();
        $this->info('Puis :');
        $this->line('  php artisan config:clear');
        $this->line('  php artisan config:cache');
        $this->comment('Après cela, seule la nouvelle clé sera acceptée.');

        return self::SUCCESS;
    }

    private function invalidAction(): int
    {
        $this->error('Action inconnue. Utilise : status | generate | rotate | finalize');

        return self::FAILURE;
    }

    private function newKey(): string
    {
        $bytes = max(16, (int) $this->option('length'));

        return bin2hex(random_bytes($bytes));
    }

    private function mask(mixed $value): string
    {
        if (! is_string($value) || $value === '') {
            return '(vide)';
        }

        $len = strlen($value);
        if ($len <= 8) {
            return str_repeat('*', $len).' ('.$len.' car.)';
        }

        return substr($value, 0, 4).str_repeat('*', max(4, $len - 8)).substr($value, -4).' ('.$len.' car.)';
    }
}
