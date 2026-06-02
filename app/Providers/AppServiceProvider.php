<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('mail.manager', function ($app) {
            return new class($app) extends MailManager {
                protected function configureSmtpTransport(EsmtpTransport $transport, array $config): EsmtpTransport
                {
                    $transport = parent::configureSmtpTransport($transport, $config);

                    if (! ($config['verify_peer'] ?? true)) {
                        $stream = $transport->getStream();
                        if ($stream instanceof SocketStream) {
                            $stream->setStreamOptions([
                                'ssl' => [
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                    'allow_self_signed' => true,
                                ],
                            ]);
                        }
                    }

                    return $transport;
                }
            };
        });
    }

    public function boot(): void
    {
        // Définir les gates pour les permissions
        Gate::define('manage-users', function (User $user) {
            return $user->isAgentIT();
        });

        Gate::define('manage-all', function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}