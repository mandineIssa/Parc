<?php

namespace App\Services;

use App\Models\EodBatchAssignment;
use App\Models\EodBatchWeek;
use App\Models\EodPlanningSetting;

class EodBatchPlanningNotifier
{
    public function __construct(
        private readonly UserMailNotifier $mail
    ) {}

    /**
     * Notifie immédiatement (e-mail + in-app) les personnes désignées à la publication.
     */
    public function notifyWeekPublished(EodBatchWeek $week, EodPlanningSetting $settings): int
    {
        $week->loadMissing(['assignments.assignee', 'assignments.supervisor']);
        $sent = 0;

        foreach ($week->assignments as $assignment) {
            // Toujours notifier l'agent désigné dès la publication
            if ($this->notifyAssignee($assignment, 'assignment', sync: true)) {
                $assignment->update(['assignment_notified_at' => now()]);
                $sent++;
            }

            if ($settings->notify_supervisor_on_publish && $assignment->supervisor_user_id) {
                if ($this->notifySupervisor($assignment, 'assignment', sync: true)) {
                    $sent++;
                }
            }
        }

        return $sent;
    }

    public function notifyAssignee(EodBatchAssignment $assignment, string $type = 'reminder', bool $sync = false): bool
    {
        $assignment->loadMissing(['assignee', 'week']);
        $assignee = $assignment->assignee;

        if (! $assignee) {
            return false;
        }

        $isReminder = $type === 'reminder';
        $subject = $isReminder
            ? '[GPI] Rappel — traitement batch EOD du '.$assignment->scheduled_date->format('d/m/Y')
            : '[GPI] Planification batch — '.$assignment->dayLabel();

        $title = $isReminder ? 'Rappel traitement batch' : 'Vous êtes désigné pour le batch';

        $message = $isReminder
            ? "Rappel : vous êtes désigné pour le traitement batch EOD.\n\n"
                ."Date : {$assignment->dayLabel()}\n"
                ."Superviseur batch : {$assignment->supervisorDisplayName()}\n\n"
                .'Merci de préparer et exécuter le batch selon la procédure COFINA.'
            : "Vous avez été désigné pour le traitement batch EOD de la semaine.\n\n"
                ."Date : {$assignment->dayLabel()}\n"
                ."Superviseur batch : {$assignment->supervisorDisplayName()}\n\n"
                .'Consultez le planning pour les détails.';

        $weekParam = $assignment->week?->week_start?->format('Y-m-d')
            ?? $assignment->scheduled_date->format('Y-m-d');

        return $this->mail->notifyUser(
            $assignee,
            $subject,
            $title,
            $message,
            route('eod.planning.index', ['week' => $weekParam]),
            'Voir le planning',
            $sync
        );
    }

    public function notifySupervisor(EodBatchAssignment $assignment, string $type = 'assignment', bool $sync = false): bool
    {
        $assignment->loadMissing(['assignee', 'supervisor', 'week']);
        $supervisor = $assignment->supervisor;

        if (! $supervisor) {
            return false;
        }

        $subject = '[GPI] Supervision batch — '.$assignment->dayLabel();
        $message = "Vous supervisez le traitement batch EOD du {$assignment->dayLabel()}.\n"
            ."Responsable batch : {$assignment->assigneeDisplayName()}";

        $weekParam = $assignment->week?->week_start?->format('Y-m-d')
            ?? $assignment->scheduled_date->format('Y-m-d');

        return $this->mail->notifyUser(
            $supervisor,
            $subject,
            'Supervision batch EOD',
            $message,
            route('eod.planning.index', ['week' => $weekParam]),
            'Voir le planning',
            $sync
        );
    }

    public function sendReminder(EodBatchAssignment $assignment): bool
    {
        if ($this->notifyAssignee($assignment, 'reminder', sync: true)) {
            $assignment->update([
                'last_reminder_at' => now(),
                'reminder_count' => $assignment->reminder_count + 1,
            ]);

            return true;
        }

        return false;
    }
}
