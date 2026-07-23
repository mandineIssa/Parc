<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('audit-collecte', function (Request $request) {
            $perMinute = max(1, (int) config('audit_collecte.rate_limit_per_minute', 60));

            return Limit::perMinute($perMinute)->by($request->ip() ?? 'audit');
        });
    }
}