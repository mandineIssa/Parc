<?php

namespace Tests\Feature;

use App\Exports\ProfilTemplateExport;
use App\Models\User;
use App\Support\ProfilExcelMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProfilExcelTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_download_export_and_template(): void
    {
        Excel::fake();

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'email' => 'superadmin@cofina.sn',
        ]);

        $this->actingAs($admin)->get(route('users.export'))->assertOk();
        $this->actingAs($admin)->get(route('users.import.template'))->assertOk();
        Excel::assertDownloaded('modele_import_profils.xlsx', function ($export) {
            return $export instanceof ProfilTemplateExport
                && $export->headings() === ProfilExcelMapper::headings(false)
                && $export->collection()->count() === 2;
        });
    }

    public function test_import_does_not_create_new_roles(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertSee('super_admin')
            ->assertSee('agent_it')
            ->assertSee('eod_n3')
            ->assertDontSee('responsable_rh', false);
    }
}
