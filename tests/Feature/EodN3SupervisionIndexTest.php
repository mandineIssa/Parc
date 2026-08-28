<?php

namespace Tests\Feature;

use App\Models\EodSuivi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EodN3SupervisionIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_sees_paginated_list_of_all_eod_fiches(): void
    {
        $creator = User::factory()->create();
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        for ($i = 1; $i <= 16; $i++) {
            EodSuivi::create([
                'reference' => 'EOD-TEST-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'status' => 'CLOSED',
                'date_traitement' => now()->subDays($i)->toDateString(),
                'created_by' => $creator->id,
            ]);
        }

        $this->actingAs($superAdmin)
            ->get('/eod/n3')
            ->assertOk()
            ->assertSee('16 fiche(s) au total')
            ->assertSee('EOD-TEST-01')
            ->assertSee('EOD-TEST-15')
            ->assertDontSee('EOD-TEST-16');
    }
}
