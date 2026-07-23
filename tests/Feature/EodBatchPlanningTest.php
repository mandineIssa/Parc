<?php

namespace Tests\Feature;

use App\Mail\GpiNotificationMail;
use App\Models\EodBatchAssignment;
use App\Models\EodBatchWeek;
use App\Models\EodPlanningSetting;
use App\Models\User;
use App\Services\EodBatchPlanningNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EodBatchPlanningTest extends TestCase
{
    use RefreshDatabase;

    public function test_planning_index_requires_auth(): void
    {
        $this->get('/eod/planning')->assertRedirect('/login');
    }

    public function test_n3_user_can_access_planning(): void
    {
        $user = User::factory()->create(['role_change' => 'N3', 'role' => 'user']);

        $this->actingAs($user)
            ->get('/eod/planning')
            ->assertOk()
            ->assertSee('Planning traitement batch');
    }

    public function test_publish_sends_notification_to_assignee(): void
    {
        Mail::fake();

        $manager = User::factory()->create(['role_change' => 'N3']);
        $assignee = User::factory()->create(['email' => 'batch@test.local', 'prenom' => 'Moussa', 'name' => 'BEYE']);

        $week = EodBatchWeek::create([
            'week_start' => now()->startOfWeek()->toDateString(),
            'status' => 'draft',
            'created_by' => $manager->id,
        ]);

        EodBatchAssignment::create([
            'week_id' => $week->id,
            'scheduled_date' => now()->startOfWeek()->toDateString(),
            'day_of_week' => 1,
            'assignee_user_id' => $assignee->id,
            'supervisor_name' => 'NDICK/MAR',
        ]);

        EodPlanningSetting::current();

        $notifier = app(EodBatchPlanningNotifier::class);
        $week->update(['status' => 'published', 'published_at' => now()]);
        $sent = $notifier->notifyWeekPublished($week->fresh(['assignments.assignee']), EodPlanningSetting::current());

        $this->assertGreaterThan(0, $sent);
        Mail::assertQueued(GpiNotificationMail::class);
        $this->assertDatabaseHas('gpi_user_notifications', [
            'user_id' => $assignee->id,
        ]);
    }
}
