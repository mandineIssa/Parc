<?php

namespace Tests\Feature;

use App\Models\GpiUserNotification;
use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GpiNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_index_requires_auth(): void
    {
        $this->get('/notifications')->assertRedirect('/login');
    }

    public function test_user_can_view_notifications(): void
    {
        $user = User::factory()->create();

        GpiUserNotification::create([
            'user_id' => $user->id,
            'title' => 'Test',
            'message' => 'Message test',
        ]);

        $this->actingAs($user)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('Test');
    }

    public function test_in_app_service_creates_notification(): void
    {
        $user = User::factory()->create();
        $service = app(InAppNotificationService::class);

        $notification = $service->notify($user, 'Titre', 'Corps');

        $this->assertDatabaseHas('gpi_user_notifications', [
            'id' => $notification->id,
            'user_id' => $user->id,
            'title' => 'Titre',
        ]);
        $this->assertSame(1, $service->unreadCount($user));
    }

    public function test_mark_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = GpiUserNotification::create([
            'user_id' => $user->id,
            'title' => 'Lu',
            'message' => 'Test',
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
