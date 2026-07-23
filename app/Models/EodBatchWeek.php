<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EodBatchWeek extends Model
{
    protected $fillable = [
        'week_start',
        'status',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'week_start' => 'date',
        'published_at' => 'datetime',
    ];

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EodBatchAssignment::class, 'week_id')->orderBy('day_of_week');
    }
}
