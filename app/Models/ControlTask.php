<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControlTask extends Model
{
    protected $fillable = [
        'control_id', 'title', 'description', 'status', 'conformity',
        'criticality', 'comment', 'assigned_to', 'validated_by',
        'due_date', 'completed_at', 'validated_at'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'validated_at' => 'datetime'
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ControlAttachment::class);
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeValidatedBy(User $user): bool
    {
        $roleHierarchy = ['N1' => 1, 'N2' => 2, 'N3' => 3];
        $taskRoleLevel = $roleHierarchy[$this->control->responsible_role] ?? 0;
        $userRoleLevel = $roleHierarchy[$user->role_change] ?? 0;
        
        return $userRoleLevel > $taskRoleLevel;
    }
}