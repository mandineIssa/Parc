<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Control extends Model
{
    protected $fillable = [
        'name', 'type', 'frequency', 'status', 'description',
        'template_id', 'associated_application', 'responsible_role',
        'parameters', 'last_run_at', 'next_run_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime'
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(ControlTask::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ControlTemplate::class);
    }

    public function getPendingTasksCountAttribute(): int
    {
        return $this->tasks()->where('status', 'pending')->count();
    }

    public function getCompletedTasksCountAttribute(): int
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    public function getConformityRateAttribute(): float
    {
        $total = $this->tasks()->whereNotNull('conformity')->count();
        if ($total === 0) return 0;
        
        $conforme = $this->tasks()->where('conformity', 'conforme')->count();
        return round(($conforme / $total) * 100, 2);
    }
}