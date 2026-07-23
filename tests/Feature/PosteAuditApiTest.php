<?php

namespace Tests\Feature;

use App\Models\Poste;
use App\Models\PosteAudit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosteAuditApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_KEY = 'test-audit-api-key-32chars-minimum!!';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'audit_collecte.api_key' => self::API_KEY,
            'audit_collecte.api_key_previous' => null,
            'audit_collecte.header' => 'X-API-Key',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'hostname' => 'PC-COMPTA-01',
            'utilisateurSession' => 'COFINA\\jdupont',
            'fabricant' => 'Dell Inc.',
            'modele' => 'Latitude 5540',
            'numeroSerie' => 'SN-ABC-123',
            'os' => 'Microsoft Windows 11 Pro',
            'versionOS' => '10.0.22631',
            'antivirusDefender' => true,
            'firewall' => 'Domain:True;Private:True;Public:False',
            'bitlocker' => 'C::On;D::Off',
            'usbStockageBloque' => true,
            'adresseMAC' => 'AA:BB:CC:DD:EE:FF',
            'adresseIP' => '10.10.1.42',
            'dateAudit' => '2026-07-23T10:00:00+00:00',
        ], $overrides);
    }

    public function test_post_audit_requires_api_key(): void
    {
        $this->postJson('/api/audit', $this->validPayload())
            ->assertUnauthorized()
            ->assertJsonPath('error', 'unauthorized');
    }

    public function test_post_audit_rejects_invalid_api_key(): void
    {
        $this->withHeader('X-API-Key', 'wrong-key')
            ->postJson('/api/audit', $this->validPayload())
            ->assertUnauthorized();
    }

    public function test_post_audit_nominal_creates_poste_and_history(): void
    {
        $response = $this->withHeader('X-API-Key', self::API_KEY)
            ->postJson('/api/audit', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('data.created', true)
            ->assertJsonPath('data.hostname', 'PC-COMPTA-01')
            ->assertJsonPath('data.utilisateur_session', 'COFINA\\jdupont');

        $this->assertDatabaseHas('postes', [
            'hostname' => 'PC-COMPTA-01',
            'numero_serie' => 'SN-ABC-123',
            'utilisateur_session' => 'COFINA\\jdupont',
            'antivirus_defender' => 1,
        ]);

        $this->assertDatabaseCount('poste_audits', 1);
    }

    public function test_post_audit_accepts_bearer_token(): void
    {
        $this->withToken(self::API_KEY)
            ->postJson('/api/audit', $this->validPayload([
                'hostname' => 'PC-BEARER',
                'numeroSerie' => 'SN-BEARER',
            ]))
            ->assertCreated();
    }

    public function test_post_audit_validation_error_returns_400(): void
    {
        $payload = $this->validPayload();
        unset($payload['utilisateurSession'], $payload['hostname']);

        $this->withHeader('X-API-Key', self::API_KEY)
            ->postJson('/api/audit', $payload)
            ->assertStatus(400)
            ->assertJsonPath('error', 'validation_error')
            ->assertJsonValidationErrors(['hostname', 'utilisateurSession'], 'errors');
    }

    public function test_post_audit_upsert_same_poste_adds_history(): void
    {
        $this->withHeader('X-API-Key', self::API_KEY)
            ->postJson('/api/audit', $this->validPayload())
            ->assertCreated();

        $response = $this->withHeader('X-API-Key', self::API_KEY)
            ->postJson('/api/audit', $this->validPayload([
                'adresseIP' => '10.10.1.99',
                'dateAudit' => '2026-07-23T12:00:00+00:00',
            ]));

        $response->assertOk()
            ->assertJsonPath('data.created', false)
            ->assertJsonPath('data.utilisateur_change', false);

        $this->assertDatabaseCount('postes', 1);
        $this->assertDatabaseCount('poste_audits', 2);
        $this->assertDatabaseHas('postes', [
            'hostname' => 'PC-COMPTA-01',
            'adresse_ip' => '10.10.1.99',
        ]);
    }

    public function test_post_audit_tracks_utilisateur_session_change(): void
    {
        $this->withHeader('X-API-Key', self::API_KEY)
            ->postJson('/api/audit', $this->validPayload())
            ->assertCreated();

        $response = $this->withHeader('X-API-Key', self::API_KEY)
            ->postJson('/api/audit', $this->validPayload([
                'utilisateurSession' => 'COFINA\\mmartin',
                'dateAudit' => '2026-07-24T09:00:00+00:00',
            ]));

        $response->assertOk()
            ->assertJsonPath('data.utilisateur_change', true)
            ->assertJsonPath('data.utilisateur_session', 'COFINA\\mmartin');

        $poste = Poste::query()->first();
        $this->assertNotNull($poste);
        $this->assertSame('COFINA\\mmartin', $poste->utilisateur_session);

        $users = PosteAudit::query()
            ->where('poste_id', $poste->id)
            ->pluck('utilisateur_session')
            ->all();

        $this->assertSame(['COFINA\\jdupont', 'COFINA\\mmartin'], $users);
        $this->assertCount(2, $poste->historiqueUtilisateurs());
    }
}
