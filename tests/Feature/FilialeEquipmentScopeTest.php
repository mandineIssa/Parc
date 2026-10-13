<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Equipment;
use App\Models\Filiale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilialeEquipmentScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_in_a_filiale_only_sees_that_filiale_equipment(): void
    {
        $senegal = Filiale::query()->where('nom', 'Sénégal')->first()
            ?? Filiale::create(['nom' => 'Sénégal', 'actif' => true]);
        $guinee = Filiale::create(['nom' => 'Guinée', 'actif' => true]);

        $agenceSn = Agency::create([
            'code' => 'DSN',
            'nom' => 'Dakar',
            'ville' => 'Dakar',
            'filiale_id' => $senegal->id,
        ]);
        $agenceGn = Agency::create([
            'code' => 'CKY',
            'nom' => 'Conakry',
            'ville' => 'Conakry',
            'filiale_id' => $guinee->id,
        ]);

        $eqSn = $this->makeEquipment('SN-TEST-001', $agenceSn->id);
        $eqGn = $this->makeEquipment('GN-TEST-001', $agenceGn->id);

        $adminSn = User::factory()->create([
            'role' => 'admin',
            'filiale_id' => $senegal->id,
            'email' => 'admin.sn@test.local',
        ]);
        $super = User::factory()->create([
            'role' => 'super_admin',
            'email' => 'super@test.local',
        ]);
        $userGn = User::factory()->create([
            'role' => 'user',
            'filiale_id' => $guinee->id,
            'email' => 'user.gn@test.local',
        ]);

        $this->actingAs($adminSn);
        $this->assertEquals([$eqSn->id], Equipment::query()->orderBy('id')->pluck('id')->all());

        $this->actingAs($userGn);
        $this->assertEquals([$eqGn->id], Equipment::query()->orderBy('id')->pluck('id')->all());

        $this->actingAs($super);
        $this->assertEqualsCanonicalizing([$eqSn->id, $eqGn->id], Equipment::query()->pluck('id')->all());
    }

    public function test_filiale_admin_only_lists_users_of_same_filiale(): void
    {
        $senegal = Filiale::query()->where('nom', 'Sénégal')->first()
            ?? Filiale::create(['nom' => 'Sénégal', 'actif' => true]);
        $guinee = Filiale::create(['nom' => 'Guinée', 'actif' => true]);

        $adminSn = User::factory()->create([
            'role' => 'admin',
            'filiale_id' => $senegal->id,
            'name' => 'AdminSN',
            'prenom' => 'Test',
            'email' => 'admin.sn@test.local',
        ]);
        User::factory()->create([
            'role' => 'user',
            'filiale_id' => $senegal->id,
            'name' => 'Visible',
            'prenom' => 'Local',
            'email' => 'visible@test.local',
        ]);
        User::factory()->create([
            'role' => 'user',
            'filiale_id' => $guinee->id,
            'name' => 'Hidden',
            'prenom' => 'Other',
            'email' => 'hidden@test.local',
        ]);

        $this->actingAs($adminSn)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Visible')
            ->assertDontSee('Hidden');
    }

    private function makeEquipment(string $serial, int $agencyId): Equipment
    {
        return Equipment::query()->create([
            'numero_serie' => $serial,
            'agency_id' => $agencyId,
            'localisation' => 'Bureau',
            'type' => 'Informatique',
            'modele' => 'Latitude',
            'marque' => 'Dell',
            'date_livraison' => now()->toDateString(),
            'prix' => 100,
            'etat' => 'bon',
            'statut' => 'stock',
        ]);
    }
}
