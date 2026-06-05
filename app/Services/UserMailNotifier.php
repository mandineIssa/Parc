<?php

namespace App\Services;

use App\Mail\GpiNotificationMail;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envoie des e-mails uniquement vers des comptes présents dans la table users.
 */
class UserMailNotifier
{
    public function notifyUser(
        User|int|null $user,
        string $subject,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionLabel = null
    ): bool {
        $recipient = $this->resolveUser($user);

        if (! $recipient) {
            return false;
        }

        return $this->send($recipient, $subject, $title, $message, $actionUrl, $actionLabel);
    }

    public function notifyUserByEmail(
        ?string $email,
        string $subject,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionLabel = null
    ): bool {
        if (! $this->isValidEmail($email)) {
            return false;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            Log::info('Notification ignorée : e-mail absent de la table users.', [
                'email' => $email,
            ]);

            return false;
        }

        return $this->notifyUser($user, $subject, $title, $message, $actionUrl, $actionLabel);
    }

    /**
     * @param  iterable<int, User|int|null>  $users
     */
    public function notifyMany(
        iterable $users,
        string $subject,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionLabel = null
    ): int {
        $sent = 0;

        foreach ($users as $user) {
            if ($this->notifyUser($user, $subject, $title, $message, $actionUrl, $actionLabel)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * @return Collection<int, User>
     */
    public function superAdmins(): Collection
    {
        return User::query()
            ->where('role', 'super_admin')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();
    }

    private function resolveUser(User|int|null $user): ?User
    {
        if ($user === null) {
            return null;
        }

        $id = $user instanceof User ? $user->id : $user;

        if (! $id) {
            return null;
        }

        $fresh = User::query()->find($id);

        if (! $fresh || ! $this->isValidEmail($fresh->email)) {
            return null;
        }

        return $fresh;
    }

    private function isValidEmail(?string $email): bool
    {
        return is_string($email) && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function send(
        User $user,
        string $subject,
        string $title,
        string $message,
        ?string $actionUrl,
        ?string $actionLabel
    ): bool {
        try {
            $recipientName = trim((string) $user->prenom . ' ' . (string) $user->name);
            if ($recipientName === '') {
                $recipientName = (string) $user->email;
            }

            Mail::to($user->email, $recipientName)
                ->send(new GpiNotificationMail(
                    $subject,
                    $title,
                    $message,
                    $recipientName,
                    $actionUrl,
                    $actionLabel
                ));

            Log::info('Notification GPI envoyée.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'subject' => $subject,
                'title' => $title,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Échec envoi notification GPI.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
