<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersIndexFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_filter_users_by_search_and_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'email' => 'superadmin@cofina.sn',
            'name' => 'Admin',
            'prenom' => 'GPI',
            'matricule' => '000001',
        ]);

        User::factory()->create([
            'role' => 'agent_it',
            'name' => 'Diouf',
            'prenom' => 'Babacar',
            'email' => 'babacar.diouf@test.local',
            'matricule' => '123456',
            'departement' => 'EXPLOITATION',
        ]);

        User::factory()->create([
            'role' => 'user',
            'name' => 'Fall',
            'prenom' => 'Awa',
            'email' => 'awa.fall@test.local',
            'matricule' => '654321',
            'departement' => 'FACILITIES',
        ]);

        $this->actingAs($admin)
            ->get(route('users.index', ['search' => 'Diouf']))
            ->assertOk()
            ->assertSee('Diouf')
            ->assertDontSee('Fall');

        $this->actingAs($admin)
            ->get(route('users.index', ['role' => 'user']))
            ->assertOk()
            ->assertSee('Fall')
            ->assertDontSee('Diouf');

        $this->actingAs($admin)
            ->get(route('users.index', ['departement' => 'EXPLOITATION']))
            ->assertOk()
            ->assertSee('123456')
            ->assertDontSee('654321');
    }
}
