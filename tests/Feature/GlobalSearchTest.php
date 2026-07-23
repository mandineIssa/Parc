<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_search_requires_auth(): void
    {
        $this->getJson('/search/global?q=test')->assertUnauthorized();
    }

    public function test_global_search_returns_equipment_results(): void
    {
        $user = User::factory()->create();

        DB::table('equipment')->insert([
            'numero_serie' => 'SN-SEARCH-001',
            'nom' => 'Laptop Test',
            'type' => 'Informatique',
            'modele' => 'X1',
            'marque' => 'Test',
            'date_livraison' => now()->toDateString(),
            'prix' => 0,
            'etat' => 'neuf',
            'statut' => 'stock',
            'localisation' => 'Dakar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/search/global?q=SEARCH-001');

        $response->assertOk()
            ->assertJsonPath('results.0.type', 'Équipement')
            ->assertJsonFragment(['label' => 'Laptop Test — SN-SEARCH-001']);
    }

    public function test_global_search_requires_minimum_query_length(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/search/global?q=a')
            ->assertOk()
            ->assertJson(['results' => []]);
    }
}
