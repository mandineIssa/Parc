<?php
// app/Models/ChangeTicket.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ChangeTicket extends Model
{
    protected $table = 'change_tickets';
    
    protected $fillable = [
        'ticket_id', 'status', 'titre', 'type', 'prenom', 'nom', 'ticket_number',
        'departement', 'date_execution', 'environnement', 'problematique',
        'impact_ops', 'impact_users', 'impact_prod', 'risques', 'rollback',
        'recommandation', 'requete', 'date_exec_reelle', 'operateur',
        'resultat', 'ecarts', 'close_note', 'closed_at', 'incident_num',
        'incident_opened_at', 'rejet_note', 'history', 'files',
        'recomm_files', 'exec_files', 'created_by', 'updated_by',
        'incident_description',
    'incident_actions',
    'incident_resolved_at',
    'incident_impact_residuel',
    'incident_files'
    ];

    protected $casts = [
        'history' => 'array',
        'files' => 'array',
        'recomm_files' => 'array',
        'exec_files' => 'array',
        'date_execution' => 'date',
        'date_exec_reelle' => 'datetime',
        'incident_opened_at' => 'datetime',
         'incident_resolved_at' => 'datetime',
    'incident_files' => 'array',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (!$ticket->ticket_id) {
                $ticket->ticket_id = 'CHG-' . strtoupper(Str::random(6));
            }
        });
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'DRAFT' => 'Brouillon',
            'PENDING_N2' => 'En attente N+2',
            'REJECTED' => 'Rejeté',
            'VALIDATED_N2' => 'Validé N+2 – En attente envoi N+3',
            'PENDING_N3' => 'En attente N+3',
            'CLOSED' => 'Clôturé',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusClassAttribute()
    {
        $classes = [
            'DRAFT' => 'badge-draft',
            'PENDING_N2' => 'badge-pending',
            'REJECTED' => 'badge-rejected',
            'VALIDATED_N2' => 'badge-approved',
            'PENDING_N3' => 'badge-pending',
            'CLOSED' => 'badge-closed',
        ];
        
        return $classes[$this->status] ?? 'badge-draft';
    }

    public function getTypeClassAttribute()
    {
        return match($this->type) {
            'Urgent' => 'type-urg',
            'Standard' => 'type-std',
            default => 'type-nrm'
        };
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }



// Dans les méthodes qui utilisent les rôles, adapter pour utiliser le nouveau champ

public function n1Index()
{
    // Vérifier que l'utilisateur a le rôle N1
    if (!auth()->user()->isN1() && !auth()->user()->isSuperAdmin()) {
        abort(403, 'Rôle non autorisé.');
    }
    
    $tickets = ChangeTicket::where('created_by', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();
    $pendingCount = ChangeTicket::where('status', 'PENDING_N2')->count();
    
    return view('change.n1.index', compact('tickets', 'pendingCount'));
}


// Même logique pour N2 et N3...
}