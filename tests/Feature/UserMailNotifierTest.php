<?php

namespace Tests\Feature;

use App\Mail\GpiNotificationMail;
use App\Models\User;
use App\Services\UserMailNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserMailNotifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifier_queues_mail_and_creates_in_app_notification(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'notify@test.local']);
        $notifier = app(UserMailNotifier::class);

        $sent = $notifier->notifyUser($user, 'Sujet', 'Titre', 'Corps du message');

        $this->assertTrue($sent);
        Mail::assertQueued(GpiNotificationMail::class, function ($mail) {
            return $mail->mailSubject === 'Sujet' && $mail->title === 'Titre';
        });
        $this->assertDatabaseHas('gpi_user_notifications', [
            'user_id' => $user->id,
            'title' => 'Titre',
        ]);
    }

    public function test_notifier_ignores_unknown_email(): void
    {
        Mail::fake();
        $notifier = app(UserMailNotifier::class);

        $sent = $notifier->notifyUserByEmail('inconnu@test.local', 'S', 'T', 'M');

        $this->assertFalse($sent);
        Mail::assertNothingQueued();
    }
}
