<?php

namespace App\Services;

use App\Models\Poste;
use App\Models\PosteAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosteAuditIngestService
{
    /**
     * Upsert du poste (hostname + numero_serie) + ajout d'une ligne d'historique.
     *
     * @param  array<string, mixed>  $attributes
     * @return array{poste: Poste, audit: PosteAudit, created: bool, utilisateur_change: bool}
     */
    public function ingest(array $attributes): array
    {
        return DB::transaction(function () use ($attributes) {
            /** @var Poste|null $existing */
            $existing = Poste::query()
                ->where('hostname', $attributes['hostname'])
                ->where('numero_serie', $attributes['numero_serie'])
                ->lockForUpdate()
                ->first();

            $created = $existing === null;
            $utilisateurChange = false;

            if ($existing) {
                $utilisateurChange = ($existing->utilisateur_session ?? '') !== ($attributes['utilisateur_session'] ?? '');
                $existing->fill($attributes);
                $existing->save();
                $poste = $existing;
            } else {
                $poste = Poste::create($attributes);
            }

            $audit = $poste->audits()->create($attributes);

            // Log d'accès RGPD : pas de login en clair, empreinte + contexte technique
            Log::info('poste_audit.ingested', [
                'poste_id' => $poste->id,
                'audit_id' => $audit->id,
                'hostname' => $poste->hostname,
                'numero_serie' => $poste->numero_serie,
                'utilisateur_session_hash' => hash('sha256', (string) ($attributes['utilisateur_session'] ?? '')),
                'utilisateur_change' => $utilisateurChange,
                'created' => $created,
                'ip' => request()->ip(),
            ]);

            return [
                'poste' => $poste->fresh(),
                'audit' => $audit,
                'created' => $created,
                'utilisateur_change' => $utilisateurChange,
            ];
        });
    }
}
