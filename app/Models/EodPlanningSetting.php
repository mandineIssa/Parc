<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EodPlanningSetting extends Model
{
    protected $fillable = [
        'notify_on_publish',
        'notify_supervisor_on_publish',
        'reminder_enabled',
        'reminder_same_day_time',
        'reminder_same_day',
        'reminder_day_before',
        'reminder_day_before_time',
        'default_supervisor_user_id',
        'default_supervisor_name',
        'max_reminders_per_day',
    ];

    protected $casts = [
        'notify_on_publish' => 'boolean',
        'notify_supervisor_on_publish' => 'boolean',
        'reminder_enabled' => 'boolean',
        'reminder_same_day' => 'boolean',
        'reminder_day_before' => 'boolean',
    ];

    public static function current(): self
    {
        $defaults = config('eod.planning.defaults', []);

        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'notify_on_publish' => $defaults['notify_on_publish'] ?? true,
                'notify_supervisor_on_publish' => $defaults['notify_supervisor_on_publish'] ?? false,
                'reminder_enabled' => $defaults['reminder_enabled'] ?? true,
                'reminder_same_day_time' => $defaults['reminder_same_day_time'] ?? '08:00',
                'reminder_same_day' => $defaults['reminder_same_day'] ?? true,
                'reminder_day_before' => $defaults['reminder_day_before'] ?? true,
                'reminder_day_before_time' => $defaults['reminder_day_before_time'] ?? '17:00',
                'default_supervisor_name' => $defaults['default_supervisor_name'] ?? 'NDICK/MAR',
                'max_reminders_per_day' => $defaults['max_reminders_per_day'] ?? 2,
            ]
        );
    }

    public function defaultSupervisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'default_supervisor_user_id');
    }
}
