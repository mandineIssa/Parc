<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EodBatchAssignment extends Model
{
    protected $fillable = [
        'week_id',
        'scheduled_date',
        'day_of_week',
        'assignee_user_id',
        'supervisor_user_id',
        'supervisor_name',
        'assignment_notified_at',
        'last_reminder_at',
        'reminder_count',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'assignment_notified_at' => 'datetime',
        'last_reminder_at' => 'datetime',
    ];

    public function week(): BelongsTo
    {
        return $this->belongsTo(EodBatchWeek::class, 'week_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function dayLabel(): string
    {
        $name = config('eod.planning.day_labels.'.$this->day_of_week, 'JOUR');

        return $name.' '.$this->scheduled_date->format('d/m/Y');
    }

    public function assigneeDisplayName(): string
    {
        $user = $this->assignee;
        if (! $user) {
            return '—';
        }

        $name = strtoupper(trim((string) $user->prenom.' '.(string) $user->name));

        return $name !== '' ? $name : strtoupper((string) $user->email);
    }

    public function supervisorDisplayName(): string
    {
        if ($this->supervisor_name) {
            return strtoupper($this->supervisor_name);
        }

        $user = $this->supervisor;
        if (! $user) {
            return '—';
        }

        $name = strtoupper(trim((string) $user->prenom.' '.(string) $user->name));

        return $name !== '' ? $name : strtoupper((string) $user->email);
    }

    public function rowColor(): string
    {
        return config('eod.planning.day_colors.'.$this->day_of_week, '#ffffff');
    }

    public function canSendReminderToday(Carbon $now, EodPlanningSetting $settings): bool
    {
        if (! $settings->reminder_enabled) {
            return false;
        }

        if ($this->reminder_count >= $settings->max_reminders_per_day) {
            return false;
        }

        if ($this->last_reminder_at && $this->last_reminder_at->isSameDay($now)) {
            return false;
        }

        return true;
    }
}
