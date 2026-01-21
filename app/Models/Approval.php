<?php
// app/Models/Approval.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'requested_by',
        'from_status',
        'to_status',
        'request_data',
        'status',
        'approver_id',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'validation_notes',
        'metadata',
    ];

    protected $casts = [
        'request_data' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relations
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function ficheMouvement()
    {
        return $this->hasOne(FicheMouvement::class);
    }

    public function ficheInstallation()
    {
        return $this->hasOne(FicheInstallation::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accesseurs
     */
    public function getFormattedIdAttribute()
    {
        return str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute()
    {
        return $this->status === 'rejected';
    }

    public function getTransitionLabelAttribute()
    {
        return strtoupper($this->from_status) . ' → ' . strtoupper($this->to_status);
    }

    public function getRequestDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getFormattedRequestDataAttribute()
    {
        $data = $this->request_data ?? [];
        
        return [
            'user_name' => $data['user_name'] ?? 'N/A',
            'departement' => $data['departement'] ?? 'N/A',
            'poste_affecte' => $data['poste_affecte'] ?? 'N/A',
            'date_affectation' => $data['date_affectation'] ?? null,
            'agent_nom' => $data['agent_nom'] ?? 'N/A',
            'agent_prenom' => $data['agent_prenom'] ?? 'N/A',
            'agent_fonction' => $data['agent_fonction'] ?? 'Agent IT',
        ];
    }

    /**
     * Méthodes
     */
    public function approve(User $approver, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approver_id' => $approver->id,
            'approved_at' => now(),
            'validation_notes' => $notes,
        ]);
    }

    public function reject($reason, User $rejecter = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'approver_id' => $rejecter?->id,
        ]);
    }

    /**
     * Vérifie si l'utilisateur peut approuver cette demande
     */
    public function canBeApprovedBy(User $user)
    {
        return in_array(strtolower($user->role ?? ''), ['super_admin', 'admin']) 
            && $this->status === 'pending';
    }
}