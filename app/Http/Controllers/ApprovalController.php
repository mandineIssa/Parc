<?php
namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\FicheMouvement;
use App\Models\FicheInstallation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Afficher la liste des approbations
     */
    public function index(Request $request)
    {
        $query = Approval::with(['equipment', 'requester', 'approver', 'user'])
            ->latest();
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('equipment', function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('numero_serie', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('requester', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        $approvals = $query->paginate(20);
        
        return view('admin.approvals', compact('approvals'));
    }
    
    /**
     * Afficher les détails d'une approbation
     */
    public function show(Approval $approval)
    {
        $data = $approval->formatted_request_data;
        
        // Vérifier si des fiches existent déjà
        $hasFicheMouvement = $approval->ficheMouvement()->exists();
        $hasFicheInstallation = $approval->ficheInstallation()->exists();
        
        return view('admin.approval-show', compact('approval', 'data', 'hasFicheMouvement', 'hasFicheInstallation'));
    }
    
    /**
     * Approuver une demande (version simple - pour le modal rapide)
     */
    public function approve(Request $request, Approval $approval)
    {
        $user = Auth::user();
        
        // Vérifier les autorisations
        if (!$this->canApprove($user, $approval)) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à approuver cette demande.');
        }
        
        // Valider les signatures si présentes
        if ($request->filled('signature_super_admin')) {
            $request->validate([
                'signature_super_admin' => 'required|string|min:3',
            ]);
        }
        
        // Approuver la demande
        $approval->update([
            'status' => 'approved',
            'approver_id' => $user->id,
            'approved_at' => now(),
            'validation_notes' => $request->input('notes'),
        ]);
        
        // Mettre à jour le statut de l'équipement
        $approval->equipment()->update(['status' => $approval->to_status]);
        
        // Créer une fiche de mouvement automatique si demandée
        if ($request->input('checklist.mouvement_rempli')) {
            $this->createAutoFicheMouvement($approval, $user);
        }
        
        return redirect()->route('admin.approvals')
            ->with('success', 'Demande approuvée avec succès.');
    }
    
    /**
     * Rejeter une demande
     */
    public function reject(Request $request, Approval $approval)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        $user = Auth::user();
        
        if (!$this->canApprove($user, $approval)) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à rejeter cette demande.');
        }
        
        $approval->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'approver_id' => $user->id,
        ]);
        
        return redirect()->route('admin.approvals')
            ->with('success', 'Demande rejetée avec succès.');
    }
    
    /**
     * Vérifier si l'utilisateur peut approuver
     */
    private function canApprove($user, $approval)
    {
        // Vérifier les rôles
        if (!in_array(strtolower($user->role ?? ''), ['super_admin', 'admin'])) {
            return false;
        }
        
        // Vérifier le statut
        if ($approval->status !== 'pending') {
            return false;
        }
        
        return true;
    }
    
    /**
     * Créer une fiche de mouvement automatique
     */
    private function createAutoFicheMouvement($approval, $user)
    {
        $data = $approval->formatted_request_data;
        
        FicheMouvement::create([
            'approval_id' => $approval->id,
            'equipment_id' => $approval->equipment_id,
            'user_id' => $user->id,
            'date_application' => now(),
            'numero_fiche' => FicheMouvement::generateNumeroFiche(),
            'expediteur_nom' => $data['agent_nom'] ?? $user->name,
            'expediteur_prenom' => $data['agent_prenom'] ?? '',
            'expediteur_fonction' => $data['agent_fonction'] ?? 'IT',
            'receptionnaire_nom' => $data['user_name'] ?? 'Utilisateur',
            'receptionnaire_prenom' => '',
            'receptionnaire_fonction' => $data['poste_affecte'] ?? 'Utilisateur',
            'type_materiel' => $approval->equipment->type ?? 'N/A',
            'reference' => $approval->equipment->numero_serie,
            'lieu_depart' => 'SIEGE COFINA',
            'destination' => $data['departement'] ?? 'N/A',
            'motif' => 'Affectation approuvée',
            'date_expediteur' => now(),
            'date_receptionnaire' => now(),
            'status' => 'completed',
            'notes' => 'Créé automatiquement lors de l\'approbation',
        ]);
    }
}

