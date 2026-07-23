<?php

namespace App\Console\Commands;

use App\Models\TransitionApproval;
use App\Services\UserMailNotifier;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindPendingApprovalsCommand extends Command
{
    protected $signature = 'gpi:remind-pending-approvals {--days=2 : Âge minimum en jours}';

    protected $description = 'Rappel e-mail aux super admins pour les approbations en attente';

    public function handle(UserMailNotifier $mail): int
    {
        $days = (int) $this->option('days');
        $pending = TransitionApproval::query()
            ->with(['equipment', 'submitter'])
            ->where('status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subDays($days))
            ->get();

        if ($pending->isEmpty()) {
            $this->info('Aucune approbation en attente.');

            return self::SUCCESS;
        }

        $lines = $pending->map(fn ($a) => "#{$a->id} — {$a->type} — ".($a->equipment?->numero_serie ?? '—'))->implode("\n");

        $sent = $mail->notifyMany(
            $mail->superAdmins(),
            '[GPI] Rappel — approbations en attente',
            'Approbations en attente',
            "Les demandes suivantes attendent une validation depuis plus de {$days} jour(s) :\n\n{$lines}",
            route('admin.approvals')
        );

        $this->info("Rappels envoyés à {$sent} super admin(s).");

        return self::SUCCESS;
    }
}
