<?php

namespace Tests\Feature;

use App\Models\TransitionApproval;
use App\Models\User;
use App\Services\TransitionAuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransitionAuditLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_rejection_creates_audit_entries(): void
    {
        $user = User::factory()->create();

        $equipmentId = DB::table('equipment')->insertGetId([
            'numero_serie' => 'SN-AUDIT-01',
            'nom' => 'PC Audit',
            'type' => 'Informatique',
            'modele' => 'Pro',
            'marque' => 'Test',
            'date_livraison' => now()->toDateString(),
            'prix' => 0,
            'etat' => 'neuf',
            'statut' => 'stock',
            'localisation' => 'Dakar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $approval = TransitionApproval::create([
            'equipment_id' => $equipmentId,
            'type' => 'stock_to_parc',
            'from_status' => 'stock',
            'to_status' => 'parc',
            'status' => 'rejected',
            'submitted_by' => $user->id,
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'rejection_reason' => 'Données incomplètes',
        ]);

        app(TransitionAuditLogger::class)->logRejection($approval, 'Données incomplètes');

        $this->assertDatabaseHas('audits', [
            'model_type' => TransitionApproval::class,
            'model_id' => $approval->id,
        ]);
    }
}
