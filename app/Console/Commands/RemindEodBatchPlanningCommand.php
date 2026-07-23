<?php

namespace App\Console\Commands;

use App\Models\EodBatchAssignment;
use App\Models\EodPlanningSetting;
use App\Services\EodBatchPlanningNotifier;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindEodBatchPlanningCommand extends Command
{
    protected $signature = 'gpi:remind-eod-batch-planning {--force : Ignorer les horaires paramétrés}';

    protected $description = 'Envoie les rappels batch EOD selon la planification paramétrée';

    public function handle(EodBatchPlanningNotifier $notifier): int
    {
        $settings = EodPlanningSetting::current();

        if (! $settings->reminder_enabled) {
            $this->info('Rappels batch EOD désactivés.');

            return self::SUCCESS;
        }

        $now = Carbon::now();
        $force = (bool) $this->option('force');
        $sent = 0;

        if ($settings->reminder_same_day) {
            $sameDayTime = $this->parseTime($settings->reminder_same_day_time);
            if ($force || $now->format('H:i') >= $sameDayTime) {
                $sent += $this->remindForDate($now->toDateString(), $notifier, $settings, $now);
            }
        }

        if ($settings->reminder_day_before) {
            $beforeTime = $this->parseTime($settings->reminder_day_before_time);
            if ($force || $now->format('H:i') >= $beforeTime) {
                $sent += $this->remindForDate($now->copy()->addDay()->toDateString(), $notifier, $settings, $now, true);
            }
        }

        $this->info("Rappels batch EOD envoyés : {$sent}.");

        return self::SUCCESS;
    }

    private function remindForDate(
        string $date,
        EodBatchPlanningNotifier $notifier,
        EodPlanningSetting $settings,
        Carbon $now,
        bool $isDayBefore = false
    ): int {
        $assignments = EodBatchAssignment::query()
            ->with(['assignee', 'supervisor', 'week'])
            ->whereDate('scheduled_date', $date)
            ->whereHas('week', fn ($q) => $q->where('status', 'published'))
            ->get();

        $sent = 0;

        foreach ($assignments as $assignment) {
            if (! $assignment->canSendReminderToday($now, $settings)) {
                continue;
            }

            if ($notifier->sendReminder($assignment)) {
                $sent++;
                $label = $isDayBefore ? 'veille' : 'jour J';
                $this->line("  → {$assignment->assigneeDisplayName()} ({$assignment->dayLabel()}, {$label})");
            }
        }

        return $sent;
    }

    private function parseTime(?string $time): string
    {
        if (! $time) {
            return '08:00';
        }

        return substr((string) $time, 0, 5);
    }
}
