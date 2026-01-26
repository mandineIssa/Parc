<?php
// app/Models/TransitionApproval.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class TransitionApproval extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'transition_approvals';
    protected $primaryKey = 'id'; // à ajouter si jamais la PK est différente


    protected $fillable = [
        // CHAMPS DE VOTRE MIGRATION
        'equipment_id',
        'from_status',
        'to_status',
        'type', // ← CHAMP PRÉSENT DANS LA MIGRATION
        'submitted_by',
        'approved_by',
        'rejected_by',
        'status',
        'data',
        'form_data', 
        'generated_files',
        'checklist_data',
        'super_admin_signature',
        'validation_notes',
        'rejection_reason',
        'approved_at',
        'rejected_at',
        
        // CHAMPS MANQUANTS DANS VOTRE MIGRATION MAIS UTILISÉS PAR LE CONTROLEUR
        'installation_data', // ← MANQUE DANS LA MIGRATION
        'final_mouvement_file', // ← MANQUE DANS LA MIGRATION
        'final_installation_file', // ← MANQUE DANS LA MIGRATION
        'validation_date', // ← MANQUE DANS LA MIGRATION
    ];

    protected $casts = [
        'data' => 'array',
        'form_data' => 'array',
        'generated_files' => 'array',
        'checklist_data' => 'array',
        'installation_data' => 'array', // ← AJOUTER CE CAST
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'validation_date' => 'datetime', // ← AJOUTER CE CAST
    ];

    

    /**
     * Relations
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Pour compatibilité avec votre ancien code
     * (si vous avez du code qui utilise requested_by ou request_data)
     */
    
    // Accesseur pour requested_by (alias de submitted_by)
    public function getRequestedByAttribute()
    {
        return $this->submitted_by;
    }
    
    // Accesseur pour request_data (alias de data)
    public function getRequestDataAttribute()
    {
        return $this->data;
    }
    
    // Mutateur pour request_data (alias de data)
    public function setRequestDataAttribute($value)
    {
        $this->attributes['data'] = is_array($value) ? json_encode($value) : $value;
    }
    
    // Accesseur pour approver_id (alias de approved_by)
    public function getApproverIdAttribute()
    {
        return $this->approved_by;
    }
    
    // Mutateur pour approver_id (alias de approved_by)
    public function setApproverIdAttribute($value)
    {
        $this->attributes['approved_by'] = $value;
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

    /**
     * Accesseurs pratiques
     */
    public function getFormattedIdAttribute()
    {
        return str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Méthodes
     */
    public function approve(User $approver, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'validation_notes' => $notes,
        ]);
    }

    public function reject($reason, User $rejecter = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_by' => $rejecter?->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}