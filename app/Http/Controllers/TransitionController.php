<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Parc;
use App\Models\User;
use App\Models\TransitionApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Agency;
use Illuminate\Support\Facades\Schema; 
use App\Models\HorsService; 
use App\Models\Stock;
use App\Models\Deceler;
use App\Models\Ceceler;
class TransitionController extends Controller
{
    
    /**
     * Afficher le formulaire de transition
     */
    public function create(Equipment $equipment)
    {
        $equipment->load(['stock', 'parc', 'maintenance']);
        $users = User::all();

        return view('transitions.create', compact('equipment', 'users'));
    }

    /**
     * Soumettre une transition de Stock → Parc
     */
    public function stockToParc(Request $request, Equipment $equipment)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'utilisateur_id' => 'required|exists:users,id',
                'departement' => 'required|string|max:255',
                'poste_affecte' => 'required|string|max:255',
                'date_affectation' => 'required|date',
                'choix_fiches' => 'required|array',
                'choix_fiches.*' => 'in:mouvement,installation',
                'notes' => 'nullable|string',
            ]);

            $user = User::find($validated['utilisateur_id']);
            $agent = auth()->user();

            $data = [
                'utilisateur_id' => $validated['utilisateur_id'],
                'user_name' => $user->name,
                'user_email' => $user->email,
                'departement' => $validated['departement'],
                'poste_affecte' => $validated['poste_affecte'],
                'date_affectation' => $validated['date_affectation'],
                'choix_fiches' => $validated['choix_fiches'],
                'notes' => $validated['notes'],
                'agent_nom' => $agent->name,
                'agent_prenom' => $agent->prenom ?? $agent->name,
                'agent_fonction' => 'AGENT IT',
            ];

            $approval = TransitionApproval::create([
                'equipment_id' => $equipment->id,
                'from_status' => 'stock',
                'to_status' => 'parc',
                'submitted_by' => $agent->id,
                'data' => json_encode($data),
                'status' => 'pending',
                'type' => 'stock_to_parc',
            ]);

            DB::commit();

            return redirect()->route('transitions.approval.show', $approval)
                ->with('success', 'Demande soumise ! Attente de validation.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Soumission Stock→Parc erreur: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de la soumission: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les approbations en attente
     */
    public function pendingApprovals(Request $request)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $isAuthorized = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
            || $user->email === 'superadmin@cofina.sn';

        if (!$isAuthorized) {
            abort(403, "Accès réservé aux administrateurs.");
        }

        $query = TransitionApproval::with(['equipment', 'submitter', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('equipment', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('numero_serie', 'like', "%{$search}%");
                })->orWhereHas('submitter', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $approvals = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.approvals', compact('approvals'));
    }

    /**
     * Afficher les détails d'une approbation
     */
    /* public function showApprovalDetails(TransitionApproval $approval)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
            || $user->email === 'superadmin@cofina.sn'
            || $approval->submitted_by === $user->id;

        if (!$canView) {
            abort(403, "Vous n'avez pas accès à cette approbation.");
        }

        $approval->load(['equipment', 'submitter', 'approver']);
        $data = json_decode($approval->data, true);

        return view('admin.approval-details', compact('approval', 'data'));

        // Dans la méthode showApprovalDetails, après la vérification des permissions
if ($approval->type === 'stock_to_hors_service') {
    $data = json_decode($approval->data, true);
    
    return view('admin.hors-service-approval', compact('approval', 'data'));
}
    } */
/**
 * Afficher les détails d'une approbation
 */
public function showApprovalDetails(TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
        || $user->email === 'superadmin@cofina.sn'
        || $approval->submitted_by === $user->id;

    if (!$canView) {
        abort(403, "Vous n'avez pas accès à cette approbation.");
    }

    $approval->load(['equipment', 'submitter', 'approver']);
    $data = json_decode($approval->data, true);

    // ============================================
    // TRAITEMENT SPÉCIAL POUR LES TRANSITIONS STOCK → PARC
    // ============================================
    if ($approval->type === 'stock_to_parc') {
        // Récupérer les données de checklist
        $verificationChecklist = [];
        $installationChecklist = [];
        
        if (!empty($approval->checklist_data)) {
            $allChecklistData = json_decode($approval->checklist_data, true);
            if (is_array($allChecklistData)) {
                foreach ($allChecklistData as $key => $value) {
                    if (strpos($key, 'verif_') === 0) {
                        $verificationChecklist[$key] = $value;
                    } else {
                        $installationChecklist[$key] = $value;
                    }
                }
            }
        }
        
        if (empty($installationChecklist)) {
            $sources = [
                $data['installation_data']['checklist'] ?? [],
                $data['installation']['checklist'] ?? [],
                $data['checklist'] ?? []
            ];
            
            foreach ($sources as $source) {
                if (!empty($source) && is_array($source)) {
                    foreach ($source as $key => $value) {
                        if (strpos($key, 'verif_') === 0) {
                            $verificationChecklist[$key] = $value;
                        } else {
                            $installationChecklist[$key] = $value;
                        }
                    }
                    break;
                }
            }
        }
        
        // Récupérer les données d'installation
        $verificateurData = [];
        $dateVerification = null;
        $agenceNom = null;
        $dateApplication = null;
        $dateInstallation = null;
        $signatureVerificateur = null;
        
        if (!empty($approval->installation_data)) {
            $storedInstallation = json_decode($approval->installation_data, true);
            
            if (is_array($storedInstallation)) {
                $verificateurData = $storedInstallation['verificateur'] ?? [];
                $dateVerification = $storedInstallation['dates']['verification'] ?? null;
                $dateApplication = $storedInstallation['dates']['application'] ?? null;
                $dateInstallation = $storedInstallation['dates']['installation'] ?? null;
                $agenceNom = $storedInstallation['agence_nom'] ?? null;
                
                $signatureVerificateur = $storedInstallation['signature_verificateur']
                                       ?? $storedInstallation['signatures']['verificateur']
                                       ?? null;
            }
        }
        
        // Préparer les données pour la vue
        $installationData = $data['installation_data'] ?? $data['installation'] ?? [];
        $affectationData = $data['affectation_data'] ?? $data['affectation_simple'] ?? [];
        $mouvementData = $data['mouvement_data'] ?? $data['mouvement'] ?? [];

        $signatures = [
            'expediteur' => $data['signature_expediteur'] ?? $mouvementData['signature_expediteur'] ?? null,
            'receptionnaire' => $data['signature_receptionnaire'] ?? $mouvementData['signature_receptionnaire'] ?? null,
            'installateur' => $data['signature_installateur'] ?? $installationData['signature_installateur'] ?? null,
            'utilisateur' => $data['signature_utilisateur'] ?? $installationData['signature_utilisateur'] ?? null,
            'verificateur' => $signatureVerificateur ?? $data['signature_verificateur'] ?? null,
        ];

        // User info
        $userInfo = [
            'user_name' => $data['user_name'] ?? $affectationData['utilisateur_nom'] ?? '',
            'user_prenom' => $data['user_prenom'] ?? $affectationData['utilisateur_prenom'] ?? '',
            'user_email' => $data['user_email'] ?? '',
            'departement' => $data['departement'] ?? $affectationData['department'] ?? '',
            'poste_affecte' => $data['poste_affecte'] ?? $affectationData['position'] ?? '',
            'date_affectation' => $affectationData['affectation_date'] ?? $data['date_affectation'] ?? null,
        ];

        $formData = array_merge($data, $userInfo, [
            'installation_data' => $installationData,
            'affectation_data' => $affectationData,
            'mouvement_data' => $mouvementData,
            'signatures' => $signatures,
            'installation_checklist' => $installationChecklist,
            'verification_checklist' => $verificationChecklist,
            'checklist' => array_merge($installationChecklist, $verificationChecklist),
            
            // Données pour le template
            'expediteur_nom' => $data['agent_nom'] ?? auth()->user()->name,
            'expediteur_prenom' => $data['agent_prenom'] ?? auth()->user()->prenom ?? '',
            'expediteur_fonction' => $data['agent_fonction'] ?? 'AGENT IT',
            
            'receptionnaire_nom' => $mouvementData['receptionnaire_nom'] ?? $affectationData['utilisateur_nom'] ?? $data['user_name'] ?? '',
            'receptionnaire_prenom' => $mouvementData['receptionnaire_prenom'] ?? $affectationData['utilisateur_prenom'] ?? $data['user_prenom'] ?? '',
            'receptionnaire_fonction' => $mouvementData['receptionnaire_fonction'] ?? $affectationData['position'] ?? $data['poste_affecte'] ?? '',
            
            'installateur_nom' => $installationData['installateur_nom'] ?? $data['agent_nom'] ?? auth()->user()->name,
            'installateur_prenom' => $installationData['installateur_prenom'] ?? $data['agent_prenom'] ?? auth()->user()->prenom ?? '',
            'installateur_fonction' => $installationData['installateur_fonction'] ?? 'IT',
            
            'verificateur_nom' => $verificateurData['nom'] ?? '',
            'verificateur_prenom' => $verificateurData['prenom'] ?? '',
            'verificateur_fonction' => $verificateurData['fonction'] ?? 'Super Admin',
            'date_verification' => $dateVerification,
            'signature_verificateur' => $signatures['verificateur'],
            
            'agence_nom' => $agenceNom ?? $data['departement'] ?? '',
            'date_application' => $dateApplication ?? $data['date_application'] ?? null,
            'date_installation' => $dateInstallation ?? $data['date_installation'] ?? null,
            'sn' => $approval->equipment->numero_serie ?? '',
        ]);

        $hasInstallationForm = isset($data['choix_fiches']) && in_array('installation', $data['choix_fiches']);

        return view('admin.approval-details', compact(
            'approval', 
            'formData', 
            'hasInstallationForm', 
            'installationChecklist',
            'verificationChecklist'
        ));
    }
    
    // ============================================
    // REDIRECTION VERS LES VUES SPÉCIFIQUES
    // ============================================
    
    switch ($approval->type) {
        case 'parc_to_maintenance':
            return view('admin.maintenance-approval', compact('approval', 'data'));
            
        case 'parc_to_hors_service':
            return view('admin.hors-service-approval', compact('approval', 'data'));
            
        case 'stock_to_hors_service':
            return view('admin.hors-service-approval', compact('approval', 'data'));
            
        case 'maintenance_to_hors_service':
            return view('admin.maintenance-hors-service-approval', compact('approval', 'data'));
            
        case 'parc_to_perdu':
            return view('admin.perdu-approval', compact('approval', 'data'));
            
        case 'maintenance_to_stock':
            return view('admin.maintenance-to-stock-approval', compact('approval', 'data'));
              
        case 'parc_to_hors_service':
            return view('admin.parc-hors-service-approval', compact('approval', 'data'));

       
        case 'simple_affectation':
            // Vue pour les affectations simples
            return view('admin.simple-affectation-approval', compact('approval', 'data'));
            
        default:
            // Fallback à la vue générique
            Log::warning("Type d'approbation non reconnu: {$approval->type}, utilisation de la vue générique");
            return view('admin.approval-details', compact('approval', 'data'));
    }
}    /**
     * Afficher une approbation pour compléter les fiches
     */

    // ============================================================
    // FICHIER 2: TransitionController.php
    // REMPLACER la méthode show() complète
    // ============================================================

/**
 * Afficher une approbation pour compléter les fiches
 * FONCTION SHOW() COMPLÈTE CORRIGÉE
 */
/**
 * Afficher une approbation pour compléter les fiches
 * FONCTION SHOW() COMPLÈTE CORRIGÉE
 */
// ============================================================
// CODE COMPLET DE LA FONCTION show() AVEC TOUTES LES CORRECTIONS
// ============================================================

public function show(TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
        || $user->email === 'superadmin@cofina.sn'
        || $approval->submitted_by === $user->id;

    if (!$canView) {
        abort(403, "Vous n'avez pas accès à cette approbation.");
    }

    $approval->load(['equipment', 'submitter', 'approver']);
    $data = json_decode($approval->data, true);

    // ==================================================
    // ✅ RÉCUPÉRATION AMÉLIORÉE DES CHECKBOXES
    // ==================================================
    
    $verificationChecklist = [];
    $installationChecklist = [];
    
    // SOURCE 1 : checklist_data (données validées après approbation)
    if (!empty($approval->checklist_data)) {
        $allChecklistData = json_decode($approval->checklist_data, true);
        if (is_array($allChecklistData)) {
            foreach ($allChecklistData as $key => $value) {
                if (strpos($key, 'verif_') === 0) {
                    $verificationChecklist[$key] = $value;
                } else {
                    $installationChecklist[$key] = $value;
                }
            }
        }
    }
    
    // SOURCE 2 : Fallback sur data (AVANT approbation)
    if (empty($installationChecklist)) {
        $sources = [
            $data['installation_data']['checklist'] ?? [],
            $data['installation']['checklist'] ?? [],
            $data['checklist'] ?? []
        ];
        
        foreach ($sources as $source) {
            if (!empty($source) && is_array($source)) {
                foreach ($source as $key => $value) {
                    if (strpos($key, 'verif_') === 0) {
                        $verificationChecklist[$key] = $value;
                    } else {
                        $installationChecklist[$key] = $value;
                    }
                }
                break;
            }
        }
    }
    
    // ==================================================
    // RÉCUPÉRATION DES DONNÉES DU VÉRIFICATEUR
    // ==================================================
    
    $verificateurData = [];
    $dateVerification = null;
    $agenceNom = null;
    $dateApplication = null;
    $dateInstallation = null;
    $signatureVerificateur = null; // ✅ NOUVEAU
    
    if (!empty($approval->installation_data)) {
        $storedInstallation = json_decode($approval->installation_data, true);
        
        if (is_array($storedInstallation)) {
            $verificateurData = $storedInstallation['verificateur'] ?? [];
            $dateVerification = $storedInstallation['dates']['verification'] ?? null;
            $dateApplication = $storedInstallation['dates']['application'] ?? null;
            $dateInstallation = $storedInstallation['dates']['installation'] ?? null;
            $agenceNom = $storedInstallation['agence_nom'] ?? null;
            
            // ✅ RÉCUPÉRATION DE LA SIGNATURE DU VÉRIFICATEUR (CRUCIAL)
            $signatureVerificateur = $storedInstallation['signature_verificateur']
                                   ?? $storedInstallation['signatures']['verificateur']
                                   ?? null;
        }
    }
    
    // ==================================================
    // DONNÉES ORIGINALES DU FORMULAIRE
    // ==================================================
    
    $installationData = $data['installation_data'] ?? $data['installation'] ?? [];
    $affectationData = $data['affectation_data'] ?? $data['affectation_simple'] ?? [];
    $mouvementData = $data['mouvement_data'] ?? $data['mouvement'] ?? [];

    // ==================================================
    // ✅ EXTRACTION CORRECTE DES SIGNATURES
    // ==================================================
    $signatures = [
        'expediteur' => $data['signature_expediteur'] ?? $mouvementData['signature_expediteur'] ?? null,
        'receptionnaire' => $data['signature_receptionnaire'] ?? $mouvementData['signature_receptionnaire'] ?? null,
        'installateur' => $data['signature_installateur'] ?? $installationData['signature_installateur'] ?? null,
        'utilisateur' => $data['signature_utilisateur'] ?? $installationData['signature_utilisateur'] ?? null,
        'verificateur' => $signatureVerificateur ?? $data['signature_verificateur'] ?? null, // ✅ CORRIGÉ
    ];

    // ==================================================
    // ✅ EXTRACTION CORRECTE DES DONNÉES D'IDENTITÉ
    // ==================================================
    
    // 1. EXPÉDITEUR (Agent IT qui effectue le mouvement)
    $expediteurData = [
        'nom' => $data['agent_nom'] ?? $mouvementData['expediteur_nom'] ?? auth()->user()->name,
        'prenom' => $data['agent_prenom'] ?? $mouvementData['expediteur_prenom'] ?? auth()->user()->prenom ?? '',
        'fonction' => $data['agent_fonction'] ?? $mouvementData['expediteur_fonction'] ?? 'AGENT IT',
    ];
    
    // 2. RÉCEPTIONNAIRE (Utilisateur final qui reçoit)
    $receptionnaireData = [
        'nom' => $mouvementData['receptionnaire_nom'] ?? $affectationData['utilisateur_nom'] ?? $data['user_name'] ?? '',
        'prenom' => $mouvementData['receptionnaire_prenom'] ??$affectationData['utilisateur_prenom'] ?? $data['user_prenom'] ??'',
        'fonction' => $mouvementData['receptionnaire_fonction'] ?? $affectationData['position'] ?? $data['poste_affecte'] ?? '',
    ];
    
    // 3. INSTALLATEUR (Agent IT qui installe)
    $installateurData = [
        'nom' => $installationData['installateur_nom'] ?? $data['agent_nom'] ?? auth()->user()->name,
        'prenom' => $installationData['installateur_prenom'] ?? $data['agent_prenom'] ?? auth()->user()->prenom ?? '',
        'fonction' => $installationData['installateur_fonction'] ?? 'IT',
    ];
    
    // 4. UTILISATEUR FINAL (celui qui reçoit l'équipement)
    $utilisateurData = [
        'nom' => $affectationData['utilisateur_nom'] ?? $data['user_name'] ?? '',
        'prenom' =>$affectationData['utilisateur_prenom'] ?? $data['user_prenom'] ??  '',
        'fonction' => $affectationData['position'] ?? $data['poste_affecte'] ?? '',
        'departement' => $affectationData['department'] ?? $data['departement'] ?? '',
    ];

    // ==================================================
    // ✅ USER INFO POUR LA VUE (UTILISATEUR FINAL)
    // ==================================================
    $userInfo = [
        'user_name' => $utilisateurData['nom'],
        'user_prenom' => $utilisateurData['prenom'],
        'user_email' => $data['user_email'] ?? '',
        'departement' => $utilisateurData['departement'],
        'poste_affecte' => $utilisateurData['fonction'],
        'date_affectation' => $affectationData['affectation_date'] ?? $data['date_affectation'] ?? null,
    ];

    // ==================================================
    // FUSION COMPLÈTE DES DONNÉES POUR LE TEMPLATE
    // ==================================================
    
    $formData = array_merge($data, $userInfo, [
        'installation_data' => $installationData,
        'affectation_data' => $affectationData,
        'mouvement_data' => $mouvementData,
        'signatures' => $signatures,
        'installation_checklist' => $installationChecklist,
        'verification_checklist' => $verificationChecklist,
        
        // ✅ CLÉS POUR LE TEMPLATE PDF (CRUCIAL)
        'checklist' => array_merge($installationChecklist, $verificationChecklist),
        
        // ✅ EXPÉDITEUR (Agent IT)
        'expediteur_nom' => $expediteurData['nom'],
        'expediteur_prenom' => $expediteurData['prenom'],
        'expediteur_fonction' => $expediteurData['fonction'],
        
        // ✅ RÉCEPTIONNAIRE (Utilisateur final)
        'receptionnaire_nom' => $receptionnaireData['nom'],
        'receptionnaire_prenom' => $receptionnaireData['prenom'],
        'receptionnaire_fonction' => $receptionnaireData['fonction'],
        
        // ✅ INSTALLATEUR (Agent IT)
        'installateur_nom' => $installateurData['nom'],
        'installateur_prenom' => $installateurData['prenom'],
        'installateur_fonction' => $installateurData['fonction'],
        
        // ✅ UTILISATEUR FINAL
        'utilisateur_nom' => $utilisateurData['nom'],
        'utilisateur_prenom' => $utilisateurData['prenom'],
        'utilisateur_fonction' => $utilisateurData['fonction'],
        
        // ✅ VÉRIFICATEUR (Super Admin)
        'verificateur_nom' => $verificateurData['nom'] ?? '',
        'verificateur_prenom' => $verificateurData['prenom'] ?? '',
        'verificateur_fonction' => $verificateurData['fonction'] ?? 'Super Admin',
        'date_verification' => $dateVerification,
        
        // ✅ SIGNATURE DU VÉRIFICATEUR (EXPLICITE POUR LE PDF)
        'signature_verificateur' => $signatures['verificateur'],
        'signature_verificateur_data' => $signatures['verificateur'],
        
        // Données générales
        'agence_nom' => $agenceNom ?? $data['departement'] ?? '',
        'date_application' => $dateApplication ?? $data['date_application'] ?? null,
        'date_installation' => $dateInstallation ?? $data['date_installation'] ?? null,
        'sn' => $approval->equipment->numero_serie ?? '',
        
        'agent_nom' => $data['agent_nom'] ?? auth()->user()->name,
        'agent_fonction' => $data['agent_fonction'] ?? 'AGENT IT',
        
        // ✅ NOUVEAU : Attachments (fichiers joints)
        'attachments' => $approval->form_data['attachments'] ?? [],
    ]);

    $hasInstallationForm = isset($data['choix_fiches']) && in_array('installation', $data['choix_fiches']);

    // ✅ IMPORTANT : Passer toutes les variables à la vue
    return view('transitions.approval.show', compact(
        'approval', 
        'formData', 
        'hasInstallationForm', 
        'installationChecklist',
        'verificationChecklist'
    ));
}
    /**
     * Valider la transition (Super Admin)
     */
    public function approveTransition(Request $request, TransitionApproval $approval)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
            || $user->email === 'superadmin@cofina.sn';

        if (!$canApprove) {
            abort(403, "Seuls les super admins peuvent approuver des transitions.");
        }

        DB::beginTransaction();

        try {
            $data = json_decode($approval->data, true);
            $hasInstallationForm = isset($data['choix_fiches']) && in_array('installation', $data['choix_fiches']);

            if ($hasInstallationForm) {
                $validated = $request->validate([
                    'checklist' => 'required|array',
                    'checklist.*' => 'boolean',
                    'installateur_nom' => 'required|string|max:255',
                    'installateur_prenom' => 'required|string|max:255',
                    'installateur_fonction' => 'required|string|max:255',
                    'verificateur_nom' => 'required|string|max:255',
                    'verificateur_prenom' => 'required|string|max:255',
                    'verificateur_fonction' => 'required|string|max:255',
                    'date_application' => 'required|date',
                    'date_installation' => 'required|date',
                    'date_verification' => 'required|date',
                    'agence_nom' => 'required|string|max:255',
                    'observations' => 'nullable|string',

                    'checklist.verif_logiciels_installes' => 'required|accepted',
                    'checklist.verif_messagerie' => 'required|accepted',
                    'checklist.verif_sauvegarde' => 'required|accepted',
                    'checklist.verif_integration_ad' => 'required|accepted',
                    'checklist.verif_systeme_licence' => 'required|accepted',
                    'checklist.verif_restauration' => 'required|accepted',
                    'checklist.verif_fiche_mouvement' => 'required|accepted',
                    'checklist.verif_validation_installation' => 'required|accepted',
                    'signature_verificateur' => 'nullable|string',
                ]);
            } else {
                $validated = $request->validate([
                    'checklist' => 'required|array',
                    'checklist.*' => 'boolean',
                    'date_validation' => 'required|date',
                    'observations' => 'nullable|string',

                    'checklist.mouvement_rempli' => 'required|accepted',
                    'checklist.materiel_verifie' => 'required|accepted',
                    'checklist.signatures_ok' => 'required|accepted',
                ]);
            }

            $equipment = $approval->equipment;
            $targetUser = User::find($data['utilisateur_id'] ?? null);

            $equipment->update([
                'statut' => 'parc',
                'departement' => $data['departement'] ?? null,
                'poste_staff' => $data['poste_affecte'] ?? null,
                'date_mise_service' => $data['date_affectation'] ?? now(),
                'notes' => $data['notes'] ?? $equipment->notes,
            ]);

            $parc = Parc::create([
                'numero_serie' => $equipment->numero_serie,
                'utilisateur_id' => $data['utilisateur_id'] ?? null,
                'departement' => $data['departement'] ?? null,
                'poste_affecte' => $data['poste_affecte'] ?? null,
                'date_affectation' => $data['date_affectation'] ?? now(),
                'statut_usage' => 'actif',
                'notes_affectation' => $data['notes'] ?? null,
                'transition_approval_id' => $approval->id,
            ]);

            if ($equipment->stock) {
                $equipment->stock->update([
                    'date_sortie' => now(),
                    'etat' => 'sorti',
                ]);
            }

            $updateData = [
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'checklist_data' => json_encode($validated['checklist']),
                'validation_notes' => $validated['observations'] ?? null,
            ];

            if ($hasInstallationForm) {
                $updateData['installation_data'] = json_encode([
                    'installateur' => [
                        'nom' => $validated['installateur_nom'],
                        'prenom' => $validated['installateur_prenom'],
                        'fonction' => $validated['installateur_fonction'],
                    ],
                    'verificateur' => [
                        'nom' => $validated['verificateur_nom'],
                        'prenom' => $validated['verificateur_prenom'],
                        'fonction' => $validated['verificateur_fonction'],
                    ],
                    'dates' => [
                        'application' => $validated['date_application'],
                        'installation' => $validated['date_installation'],
                        'verification' => $validated['date_verification'],
                    ],
                    'agence_nom' => $validated['agence_nom'],
                    // ✅ AJOUTER LA SIGNATURE DU VÉRIFICATEUR (CRUCIAL)
                    'signature_verificateur' => $validated['signature_verificateur'] ?? null,
                    // ✅ OU MIEUX : Grouper toutes les signatures
                    'signatures' => [
                         'verificateur' => $validated['signature_verificateur'] ?? null,
                         'utilisateur' => $validated['signature_utilisateur'] ?? null,
            ],
                ]);
            } else {
                $updateData['validation_date'] = $validated['date_validation'] ?? now();
            }

            $approval->update($updateData);

            DB::commit();

            $this->generateFinalDocuments($approval, $validated, $hasInstallationForm);
            $this->notifyUserApproval($approval, $targetUser);

            return redirect()->route('admin.approvals')
                ->with('success', 'Transition validée et enregistrée !');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approbation Stock→Parc erreur: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Rejeter la transition
     */
    public function rejectTransition(Request $request, TransitionApproval $approval)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $canReject = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
            || $user->email === 'superadmin@cofina.sn';

        if (!$canReject) {
            abort(403, "Seuls les super admins peuvent rejeter des transitions.");
        }

        $validated = $request->validate([
            'raison_rejet' => 'required|string|max:500',
        ]);

        $approval->update([
            'status' => 'rejected',
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'rejection_reason' => $validated['raison_rejet'],
        ]);

        $this->notifyUserRejection($approval);

        return redirect()->route('admin.approvals')
            ->with('info', 'Demande rejetée.');
    }

    /**
     * Soumettre une demande d'approbation pour Stock → Parc
     */
    public function submitApproval(Request $request, Equipment $equipment)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
                || $user->email === 'superadmin@cofina.sn';

            $formType = $request->form_type;

            $rules = [
                'form_type' => 'required|string|in:mouvement,installation',
                'transition_type' => 'required|string',
            ];

            if ($formType === 'mouvement') {
                $rules = array_merge($rules, [
                    'date_application_mouvement' => 'required|date',
                    'expediteur_nom' => 'required|string|max:255',
                    'expediteur_fonction' => 'required|string|max:255',
                    'receptionnaire_nom' => 'required|string|max:255',
                    'receptionnaire_fonction' => 'required|string|max:255',
                    'lieu_depart' => 'required|string|max:255',
                    'destination' => 'required|string|max:255',
                    'motif' => 'required|string|max:255',
                    'date_expediteur' => 'required|date',
                    'signature_expediteur' => 'required|string',
                ]);

                if ($isSuperAdmin) {
                    $rules['signature_receptionnaire'] = 'required|string';
                    $rules['date_receptionnaire'] = 'required|date';
                }
            } elseif ($formType === 'installation') {
                $rules = array_merge($rules, [
                    'date_application' => 'required|date',
                    'agence_nom' => 'required|string|max:255',
                    'date_installation' => 'required|date',
                    'installateur_nom' => 'required|string|max:255',
                    'installateur_fonction' => 'required|string|max:255',
                    'signature_installateur' => 'required|string',
                    'checklist' => 'required|array',
                ]);

                if ($isSuperAdmin) {
                    $rules['date_verification'] = 'required|date';
                    $rules['verificateur_nom'] = 'required|string|max:255';
                    $rules['verificateur_prenom'] = 'required|string|max:255';
                    $rules['verificateur_fonction'] = 'required|string|max:255';
                    $rules['signature_utilisateur'] = 'required|string';
                    $rules['signature_verificateur'] = 'required|string';
                }
            }

            $validated = $request->validate($rules);

            $data = [
                'form_type' => $formType,
                'transition_type' => $validated['transition_type'],
                'agent_nom' => $user->name,
                'agent_prenom' => $user->prenom ?? $user->name,
                'agent_fonction' => 'AGENT IT',
                'submitted_at' => now()->format('Y-m-d H:i:s'),
                'is_super_admin_submission' => $isSuperAdmin,
            ];

            if ($formType === 'mouvement') {
                $data = array_merge($data, [
                    'date_application' => $validated['date_application_mouvement'],
                    'expediteur_nom' => $validated['expediteur_nom'],
                    'expediteur_fonction' => $validated['expediteur_fonction'],
                    'receptionnaire_nom' => $validated['receptionnaire_nom'],
                    'receptionnaire_fonction' => $validated['receptionnaire_fonction'],
                    'lieu_depart' => $validated['lieu_depart'],
                    'destination' => $validated['destination'],
                    'motif' => $validated['motif'],
                    'date_expediteur' => $validated['date_expediteur'],
                    'signature_expediteur' => $validated['signature_expediteur'],
                    'choix_fiches' => ['mouvement'],
                ]);

                if ($isSuperAdmin && isset($validated['signature_receptionnaire'])) {
                    $data['signature_receptionnaire'] = $validated['signature_receptionnaire'];
                    $data['date_receptionnaire'] = $validated['date_receptionnaire'] ?? null;
                }
            } else {
                $data = array_merge($data, [
                    'date_application' => $validated['date_application'],
                    'agence_nom' => $validated['agence_nom'],
                    'date_installation' => $validated['date_installation'],
                    'installateur_nom' => $validated['installateur_nom'],
                    'installateur_fonction' => $validated['installateur_fonction'],
                    'signature_installateur' => $validated['signature_installateur'],
                    'choix_fiches' => ['installation'],
                    'checklist' => $validated['checklist'],
                ]);

                if (isset($validated['observations'])) {
                    $data['observations'] = $validated['observations'];
                }

                if ($isSuperAdmin) {
                    $data['date_verification'] = $validated['date_verification'];
                    $data['verificateur_nom'] = $validated['verificateur_nom'];
                    $data['verificateur_prenom'] = $validated['verificateur_prenom'];
                    $data['verificateur_fonction'] = $validated['verificateur_fonction'];
                    $data['signature_utilisateur'] = $validated['signature_utilisateur'];
                    $data['signature_verificateur'] = $validated['signature_verificateur'];
                }
            }

            $approval = TransitionApproval::create([
                'equipment_id' => $equipment->id,
                'from_status' => 'stock',
                'to_status' => 'parc',
                'submitted_by' => $user->id,
                'data' => json_encode($data),
                'status' => 'pending',
                'type' => 'stock_to_parc',
                'requires_super_admin_validation' => !$isSuperAdmin,
            ]);

            if ($isSuperAdmin) {
                $this->processStockToParc($equipment, $data, $approval);
                $approval->update([
                    'status' => 'approved',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transition approuvée et exécutée avec succès!',
                    'redirect_url' => route('equipment.show', $equipment),
                ]);
            } else {
                $this->notifySuperAdmins($approval);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Demande soumise avec succès! Attente de validation Super Admin.',
                    'redirect_url' => route('transitions.approval.show', $approval),
                    'approval_id' => $approval->id,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur submitApproval: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soumettre tous les formulaires en une seule fois (3 étapes)
     */
    public function submitAllForms(Request $request, Equipment $equipment)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
                || $user->email === 'superadmin@cofina.sn';

            $validated = $request->validate([
                'installation' => 'required|array',
                'affectation_simple' => 'required|array',
                'mouvement' => 'required|array',
                'transition_type' => 'required|string|in:stock_to_parc',
            ]);
        
            if ($equipment->statut !== 'stock') {
                throw new \Exception("L'équipement n'est pas en stock. Statut actuel: " . $equipment->statut);
            }

            $data = [
                'transition_type' => $validated['transition_type'],
                'form_type' => 'multi_step',
                'agent_nom' => $user->name,
                'agent_prenom' => $user->prenom ?? $user->name,
                'agent_fonction' => 'AGENT IT',
                'submitted_at' => now()->format('Y-m-d H:i:s'),
                'is_super_admin_submission' => $isSuperAdmin,

                'installation_data' => $validated['installation'],
                'affectation_data' => $validated['affectation_simple'],
                'mouvement_data' => $validated['mouvement'],

                'utilisateur_id' => $validated['affectation_simple']['user_id'] ?? null,
                'user_name' => $this->extractUserName($validated['affectation_simple']),
                'user_prenom' => $this->extractUserPrenom($validated['affectation_simple']),
                'departement' => $validated['affectation_simple']['department'] ?? null,
                'poste_affecte' => $validated['affectation_simple']['position'] ?? null,
                'date_affectation' => $validated['affectation_simple']['affectation_date'] ?? now()->format('Y-m-d'),
                'affectation_reason' => $validated['affectation_simple']['affectation_reason'] ?? null,

                'choix_fiches' => ['installation', 'mouvement'],
            ];
           
            $data = $this->extractSignatures($data, $validated);

            $approval = TransitionApproval::create([
                'equipment_id' => $equipment->id,
                'from_status' => 'stock',
                'to_status' => 'parc',
                'submitted_by' => $user->id,
                'data' => json_encode($data),
                'status' => 'pending',
                'type' => 'stock_to_parc',
                'requires_super_admin_validation' => !$isSuperAdmin,
            ]);

            if ($isSuperAdmin) {
                $this->processMultiStepStockToParc($equipment, $data, $approval);
                $approval->update([
                    'status' => 'approved',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transition complète approuvée et exécutée avec succès!',
                    'redirect_url' => route('equipment.show', $equipment),
                ]);
            } else {
                $this->notifySuperAdmins($approval);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transition complète soumise avec succès! Attente de validation Super Admin.',
                    'redirect_url' => route('transitions.approval.show', $approval),
                    'approval_id' => $approval->id,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur submitAllForms: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ], 500);
        }
    }
/**
 * Extraire le prénom de l'utilisateur
 */
/**
 * Extraire le prénom de l'utilisateur de façon robuste
 */
private function extractUserPrenom($affectationData)
{
    // Priorité 1 : Chercher par user_id dans la base de données
    if (isset($affectationData['user_id'])) {
        $user = User::find($affectationData['user_id']);
        if ($user) {
            return $user->prenom ?? '';
        }
    }
    
    // Priorité 2 : Utiliser utilisateur_prenom du formulaire
    if (isset($affectationData['utilisateur_prenom']) && !empty($affectationData['utilisateur_prenom'])) {
        return $affectationData['utilisateur_prenom'];
    }
    
    // Priorité 3 : Utiliser responsable_prenom
    if (isset($affectationData['responsable_prenom']) && !empty($affectationData['responsable_prenom'])) {
        return $affectationData['responsable_prenom'];
    }
    
    // Fallback vide
    return '';
}

    /**
     * Traiter la transition Stock → Parc
     */
    private function processStockToParc(Equipment $equipment, array $data, TransitionApproval $approval)
    {
        $equipment->update([
            'statut' => 'parc',
            'departement' => $data['destination'] ?? null,
            'poste_staff' => $data['receptionnaire_fonction'] ?? null,
            'date_mise_service' => now(),
        ]);

        Parc::create([
            'numero_serie' => $equipment->numero_serie,
            'utilisateur_id' => null,
            'departement' => $data['destination'] ?? null,
            'poste_affecte' => $data['receptionnaire_fonction'] ?? null,
            'date_affectation' => now(),
            'statut_usage' => 'actif',
            'notes_affectation' => 'Transition directe via formulaire',
            'transition_approval_id' => $approval->id,
        ]);

        if ($equipment->stock) {
            $equipment->stock->update([
                'date_sortie' => now(),
                'etat' => 'sorti',
            ]);
        }
    }

    /**
     * Traiter la transition Stock → Parc pour le flux multi-étapes
     */
    private function processMultiStepStockToParc(Equipment $equipment, array $data, TransitionApproval $approval)
    {
        $equipment->update([
            'statut' => 'parc',
            'departement' => $data['departement'] ?? null,
            'poste_staff' => $data['poste_affecte'] ?? null,
            'date_mise_service' => $data['date_affectation'] ?? now(),
            'notes' => $data['affectation_reason'] ?? $equipment->notes,
        ]);

        Parc::create([
            'numero_serie' => $equipment->numero_serie,
            'utilisateur_id' => $data['utilisateur_id'] ?? null,
            'departement' => $data['departement'] ?? null,
            'poste_affecte' => $data['poste_affecte'] ?? null,
            'date_affectation' => $data['date_affectation'] ?? now(),
            'statut_usage' => 'actif',
            'notes_affectation' => $data['affectation_reason'] ?? 'Transition multi-étapes',
            'transition_approval_id' => $approval->id,

            'responsable_name' => $data['affectation_data']['responsable_name'] ?? null,
            'responsable_function' => $data['affectation_data']['responsable_function'] ?? null,
            'installation_completed' => true,
            'installation_data' => json_encode($data['installation_data'] ?? []),
            'mouvement_data' => json_encode($data['mouvement_data'] ?? []),
        ]);

        if ($equipment->stock) {
            $equipment->stock->update([
                'date_sortie' => now(),
                'etat' => 'sorti',
            ]);
        }
    }

    /**
     * Extraire le nom d'utilisateur
     */
  /**
 * Extraire le nom de l'utilisateur de façon robuste
 */
private function extractUserName($affectationData)
{
    // Priorité 1 : Chercher par user_id dans la base de données
    if (isset($affectationData['user_id'])) {
        $user = User::find($affectationData['user_id']);
        if ($user) {
            return $user->name;
        }
    }
    
    // Priorité 2 : Utiliser utilisateur_nom du formulaire
    if (isset($affectationData['utilisateur_nom']) && !empty($affectationData['utilisateur_nom'])) {
        return $affectationData['utilisateur_nom'];
    }
    
    // Priorité 3 : Utiliser responsable_name
    if (isset($affectationData['responsable_name']) && !empty($affectationData['responsable_name'])) {
        return $affectationData['responsable_name'];
    }
    
    // Fallback : utilisateur actuel
    return auth()->user()->name ?? 'N/A';
}
    /**
     * Extraire les signatures des données
     */
    private function extractSignatures($data, $validated)
    {
        if (isset($validated['installation']['signature_installateur'])) {
            $data['signature_installateur'] = $validated['installation']['signature_installateur'];
        }

        if (isset($validated['installation']['signature_utilisateur'])) {
            $data['signature_utilisateur'] = $validated['installation']['signature_utilisateur'];
        }

        if (isset($validated['installation']['signature_verificateur'])) {
            $data['signature_verificateur'] = $validated['installation']['signature_verificateur'];
        }

        if (isset($validated['mouvement']['signature_expediteur'])) {
            $data['signature_expediteur'] = $validated['mouvement']['signature_expediteur'];
        }

        if (isset($validated['mouvement']['signature_receptionnaire'])) {
            $data['signature_receptionnaire'] = $validated['mouvement']['signature_receptionnaire'];
        }

        if (isset($validated['installation']['installateur_nom'])) {
            $data['installateur_details'] = [
                'nom' => $validated['installation']['installateur_nom'],
                'fonction' => $validated['installation']['installateur_fonction'] ?? 'IT',
            ];
        }

        if (isset($validated['mouvement']['expediteur_nom'])) {
            $data['expediteur_details'] = [
                'nom' => $validated['mouvement']['expediteur_nom'],
                'fonction' => $validated['mouvement']['expediteur_fonction'] ?? 'AGENT IT',
            ];
        }

        return $data;
    }

    /**
     * Générer les documents finaux
     */
    private function generateFinalDocuments($approval, $validationData, $hasInstallationForm = false)
    {
        try {
            $data = json_decode($approval->data, true);
            $equipment = $approval->equipment;

            $basePath = 'documents/transitions/' . $approval->id . '/';

            if ($hasInstallationForm) {
                $installationData = $this->generateFicheInstallation($equipment, $data, null, null);
                $installationData['approved_by'] = auth()->user()->name;
                $installationData['approval_date'] = now()->format('d/m/Y');

                if (isset($validationData['verificateur_nom'])) {
                    $installationData['verificateur_nom'] = $validationData['verificateur_nom'];
                    $installationData['verificateur_prenom'] = $validationData['verificateur_prenom'] ?? '';
                    $installationData['verificateur_fonction'] = $validationData['verificateur_fonction'] ?? 'Super Admin';
                }

                $installationData['date_application'] = $validationData['date_application'] ?? now()->format('d/m/Y');
                $installationData['date_installation'] = $validationData['date_installation'] ?? ($data['date_affectation'] ?? now()->format('d/m/Y'));
                $installationData['date_verification'] = $validationData['date_verification'] ?? now()->format('d/m/Y');
                $installationData['agence_nom'] = $validationData['agence_nom'] ?? ($data['departement'] ?? 'N/A');

                foreach ($validationData['checklist'] as $key => $value) {
                    if (isset($installationData['checklist'][$key])) {
                        $installationData['checklist'][$key] = (bool)$value;
                    }
                }

                $pdf = Pdf::loadView('pdf.fiche-installation', $installationData);
                $filename = 'fiche_installation_complete_' . $equipment->numero_serie . '_' . now()->format('YmdHis') . '.pdf';

                $approval->update(['final_installation_file' => $basePath . $filename]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur génération documents: ' . $e->getMessage());
            return false;
        }
    }

    private function generateFicheInstallation($equipment, $data, $user, $agent)
    {
        $checklistKeys = [
            'sauvegarde_donnees',
            'sauvegarde_outlook',
            'sauvegarde_tous_utilisateurs',
            'reinstallation_os',
            'logiciels_adobe',
            'logiciels_ms_office',
            'logiciels_kaspersky',
            'logiciels_anydesk',
            'logiciels_jre',
            'logiciels_pilotes',
            'logiciels_chrome',
            'logiciels_firefox',
            'logiciels_imprimante',
            'logiciels_zoom',
            'logiciels_vpn',
            'logiciels_winrar',
            'logiciels_scanner',
            'raccourcis_nafa',
            'raccourcis_flexcube',
            'copie_logiciels_local',
            'applications_transfert',
            'applications_cc',
            'creation_compte_admin',
            'integration_domaine',
            'parametrage_messagerie',
            'partition_disque',
            'desactivation_ports_usb',
            'connexion_dossier_partage',
        ];

        $normalizedChecklist = [];

        foreach ($checklistKeys as $key) {
            $normalizedChecklist[$key] = false;
        }

        return [
            'template' => 'installation',
            'data' => [
                'entreprise' => 'COFINA SENEGAL - IT',
                'date_application' => now()->format('d/m/Y'),
                'agence_nom' => $data['departement'] ?? 'N/A',
                'numero_serie' => $equipment->numero_serie,
                'date_installation' => $data['date_affectation'] ?? now()->format('d/m/Y'),
                'installateur' => [
                    'nom' => $agent->name ?? 'N/A',
                    'prenom' => $agent->prenom ?? '',
                    'fonction' => 'IT',
                ],
                'verificateur' => [
                    'nom' => '',
                    'prenom' => '',
                    'fonction' => 'Super Admin',
                ],
                'checklist' => $normalizedChecklist,
            ],
            'filename' => 'fiche_installation_' . $equipment->numero_serie . '.pdf',
        ];
    }

    private function notifySuperAdmins($approval)
    {
        $superAdmins = User::where('role', 'super_admin')->get();

        foreach ($superAdmins as $admin) {
            Log::info("Notification à Super Admin: {$admin->email} - Approbation #{$approval->id}");
        }
    }

    private function notifyUserApproval($approval, $user)
    {
        if ($user) {
            Log::info("Notification d'approbation à: {$user->email} - Approbation #{$approval->id}");
        }
    }

    private function notifyUserRejection($approval)
    {
        $user = User::find($approval->submitted_by);

        if ($user) {
            Log::info("Notification de rejet à: {$user->email} - Approbation #{$approval->id}");
        }
    }

    // ======================================================================
    // AUTRES MÉTHODES
    // ======================================================================

    public function parcToMaintenance(Request $request, Equipment $equipment)
    {
        return back()->with('info', 'Fonctionnalité à venir.');
    }

    public function maintenanceToStock(Request $request, Equipment $equipment)
    {
        return back()->with('info', 'Fonctionnalité à venir.');
    }

    public function parcToHorsService(Request $request, Equipment $equipment)
    {
        return back()->with('info', 'Fonctionnalité à venir.');
    }

    public function parcToPerdu(Request $request, Equipment $equipment)
    {
        return back()->with('info', 'Fonctionnalité à venir.');
    }

    public function stockToHorsService(Request $request, Equipment $equipment)
    {
        return back()->with('info', 'Fonctionnalité à venir.');
    }

    public function maintenanceToHorsService(Request $request, Equipment $equipment)
    {
        return back()->with('info', 'Fonctionnalité à venir.');
    }

public function showTransitionForm(Equipment $equipment)
{
    $equipment->load(['stock', 'parc', 'maintenance']);
    $users = User::all();
    
    // Solution A: Si vous avez un modèle Agency
    $agencies = Agency::all();
    
    // Solution B: Si vous n'avez pas de modèle Agency, créez un tableau
    // $agencies = [
    //     ['id' => 1, 'nom' => 'Agence Dakar'],
    //     ['id' => 2, 'nom' => 'Agence Thiès'],
    //     ['id' => 3, 'nom' => 'Agence Saint-Louis'],
    // ];
    
    return view('transitions.create', compact('equipment', 'users', 'agencies'));
}

    public function viewApproval(TransitionApproval $approval)
    {
        return $this->show($approval);
    }

    public function showApproval(TransitionApproval $approval)
    {
        return $this->showApprovalDetails($approval);
    }

    public function executeTransition(Request $request, Equipment $equipment)
    {
        return back()->with('info', 'Utilisez la nouvelle méthode submitAllForms.');
    }

    public function downloadApproval(TransitionApproval $approval)
    {
        if ($approval->status !== 'approved') {
            return redirect()->back()->with('error', 'Cette approbation n\'est pas encore approuvée.');
        }

        try {
            $data = json_decode($approval->data, true);
            $equipment = $approval->equipment;
            $formType = $data['form_type'] ?? 'simple';

            if ($formType === 'multi_step') {
                $pdf = PDF::loadView('pdf.combined-transition', [
                    'approval' => $approval,
                    'equipment' => $equipment,
                    'data' => $data,
                    'installation_data' => $data['installation_data'] ?? [],
                    'affectation_data' => $data['affectation_data'] ?? [],
                    'mouvement_data' => $data['mouvement_data'] ?? [],
                ]);

                $filename = 'transition_complete_' . $equipment->numero_serie . '_' . now()->format('YmdHis') . '.pdf';
            } elseif (isset($data['choix_fiches']) && in_array('installation', $data['choix_fiches'])) {
                $pdf = PDF::loadView('pdf.fiche-installation', [
                    'approval' => $approval,
                    'equipment' => $equipment,
                    'data' => $data,
                ]);

                $filename = 'fiche_installation_' . $equipment->numero_serie . '_' . now()->format('YmdHis') . '.pdf';
            } else {
                $pdf = PDF::loadView('pdf.fiche-mouvement', [
                    'approval' => $approval,
                    'equipment' => $equipment,
                    'data' => $data,
                ]);

                $filename = 'fiche_mouvement_' . $equipment->numero_serie . '_' . now()->format('YmdHis') . '.pdf';
            }

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF.');
        }
    }

    public function downloadApprovalDocuments(TransitionApproval $approval)
    {
        return $this->downloadApproval($approval);
    }

    /**
     * Télécharger la fiche de mouvement uniquement
     */
    public function downloadFicheMouvement($id)
    {
        try {
            // Récupérer l'approbation
            $approval = TransitionApproval::findOrFail($id);

            // Vérifier si approuvée
            if ($approval->status !== 'approved') {
                return redirect()->back()->with('error', 'Cette approbation n\'est pas encore approuvée.');
            }

            // Récupérer les données
            $data = json_decode($approval->data, true);
            $equipment = $approval->equipment;

            // Vérifier si c'est une fiche de mouvement
            $hasMouvement = isset($data['choix_fiches']) && in_array('mouvement', $data['choix_fiches']);
            $hasMouvementData = isset($data['mouvement_data']) && !empty($data['mouvement_data']);

            if (!$hasMouvement && !$hasMouvementData) {
                return redirect()->back()->with('error', 'Cette approbation ne contient pas de fiche de mouvement.');
            }

            // Fusionner les données de mouvement
            $mouvementData = $data['mouvement_data'] ?? $data;

            // Ajouter les données manquantes si nécessaires
            if (!isset($mouvementData['expediteur_nom']) && isset($data['agent_nom'])) {
                $mouvementData['expediteur_nom'] = $data['agent_nom'];
            } 
            if (!isset($mouvementData['expediteur_fonction']) && isset($data['agent_fonction'])) {
                $mouvementData['expediteur_fonction'] = $data['agent_fonction'];
            }

            // Générer le PDF
            $pdf = PDF::loadView('pdf.fiche-mouvement', [
                'approval' => $approval,
                'equipment' => $equipment,
                'data' => $mouvementData,
            ]);

            // Nom du fichier
            $filename = 'fiche_mouvement_' . $equipment->numero_serie . '_' . now()->format('YmdHis') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Erreur génération fiche mouvement: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération de la fiche de mouvement: ' . $e->getMessage());
        }
    }
    /**
     * Télécharger la fiche d'installation uniquement
     */
/**
 * Télécharger la fiche d'installation uniquement
 */
public function downloadFicheInstallation($id)
{
    try {
        // Récupérer l'approbation
        $approval = TransitionApproval::findOrFail($id);

        // Vérifier si approuvée
        if ($approval->status !== 'approved') {
            return redirect()->back()->with('error', 'Cette approbation n\'est pas encore approuvée.');
        }

        // Récupérer les données EXACTEMENT comme dans show()
        $data = json_decode($approval->data, true);
        $equipment = $approval->equipment;

        // ==================================================
        // RÉCUPÉRATION DES CHECKBOXES (COMME DANS show())
        // ==================================================
        
        $verificationChecklist = [];
        $installationChecklist = [];
        
        if (!empty($approval->checklist_data)) {
            $allChecklistData = json_decode($approval->checklist_data, true);
            if (is_array($allChecklistData)) {
                foreach ($allChecklistData as $key => $value) {
                    if (strpos($key, 'verif_') === 0) {
                        $verificationChecklist[$key] = $value;
                    } else {
                        $installationChecklist[$key] = $value;
                    }
                }
            }
        }
        
        // ==================================================
        // RÉCUPÉRATION DES DONNÉES DU VÉRIFICATEUR
        // ==================================================
        
        $verificateurData = [];
        $dateVerification = null;
        $agenceNom = null;
        $dateApplication = null;
        $dateInstallation = null;
        $signatureVerificateur = null;
        
        if (!empty($approval->installation_data)) {
            $storedInstallation = json_decode($approval->installation_data, true);
            
            if (is_array($storedInstallation)) {
                $verificateurData = $storedInstallation['verificateur'] ?? [];
                $dateVerification = $storedInstallation['dates']['verification'] ?? null;
                $dateApplication = $storedInstallation['dates']['application'] ?? null;
                $dateInstallation = $storedInstallation['dates']['installation'] ?? null;
                $agenceNom = $storedInstallation['agence_nom'] ?? null;
                
                // ✅ RÉCUPÉRATION DE LA SIGNATURE VÉRIFICATEUR (MULTIPLE SOURCES)
                $signatureVerificateur = $storedInstallation['signature_verificateur'] ?? 
                                        $storedInstallation['signatures']['verificateur'] ?? 
                                        null;
            }
        }
        
        // ✅ SI PAS TROUVÉE DANS installation_data, CHERCHER DANS data PRINCIPAL
        if (empty($signatureVerificateur)) {
            $signatureVerificateur = $data['signature_verificateur'] ?? 
                                    $data['signature_verificateur_data'] ?? 
                                    null;
        }
        
        // ✅ SI TOUJOURS PAS TROUVÉE, CHERCHER DANS signatures
        if (empty($signatureVerificateur) && !empty($data['signatures'])) {
            $signatures = $data['signatures'];
            $signatureVerificateur = $signatures['verificateur'] ?? null;
        }
        
        // ==================================================
        // DONNÉES ORIGINALES DU FORMULAIRE
        // ==================================================
        
        $installationData = $data['installation_data'] ?? $data['installation'] ?? [];
        $affectationData = $data['affectation_data'] ?? $data['affectation_simple'] ?? [];
        
        // User info
        $userInfo = [];
        if (!empty($affectationData) && isset($affectationData['user_id'])) {
            $userObj = User::find($affectationData['user_id']);
            if ($userObj) {
                $userInfo = [
                    'user_name' => $userObj->name,
                    'user_penom' => $userObj->prenom ?? '',
                    'user_email' => $userObj->email,
                    'departement' => $affectationData['department'] ?? $affectationData['departement'] ?? null,
                    'poste_affecte' => $affectationData['position'] ?? $affectationData['poste_affecte'] ?? null,
                ];
            }
        }

        if (empty($userInfo)) {
            $userInfo = [
                'user_name' => $data['user_name'] ?? null,
                'user_prenom' => $data['user_prenom'] ?? null,
                'departement' => $data['departement'] ?? null,
                'poste_affecte' => $data['poste_affecte'] ?? null,
            ];
        }
        
        // ✅ RÉCUPÉRATION DES SIGNATURES INSTALLATEUR ET UTILISATEUR (MULTIPLES SOURCES)
        $signatureInstallateur = $data['signature_installateur'] ?? 
                                $data['signature_installateur_data'] ?? 
                                ($data['signatures']['installateur'] ?? null);
        
        $signatureUtilisateur = $data['signature_utilisateur'] ?? 
                               $data['signature_utilisateur_data'] ?? 
                               ($data['signatures']['utilisateur'] ?? null);
        
        // ==================================================
        // PRÉPARER LES DONNÉES POUR LE PDF (CRUCIAL)
        // ==================================================
        
        $pdfData = array_merge($data, $userInfo, [
            'installation_checklist' => $installationChecklist,
            'verification_checklist' => $verificationChecklist,
            
            // ✅ LA CLÉ PRINCIPALE POUR LE TEMPLATE PDF
            'checklist' => array_merge($installationChecklist, $verificationChecklist),
            
            // Données vérificateur
            'verificateur_nom' => $verificateurData['nom'] ?? $data['verificateur_nom'] ?? '',
            'verificateur_prenom' => $verificateurData['prenom'] ?? $data['verificateur_prenom'] ?? '',
            'verificateur_fonction' => $verificateurData['fonction'] ?? $data['verificateur_fonction'] ?? 'Super Admin',
            'date_verification' => $dateVerification ?? $data['date_verification'] ?? null,
            
            // Données utilisateur
            'utilisateur_nom' => $userInfo['user_name'] ?? '',
            'utilisateur_prenom' => $userInfo['user_prenom'] ?? $affectationData['utilisateur_prenom'] ?? $data['user_prenom'] ?? '',
            'utilisateur_fonction' => $userInfo['poste_affecte'] ?? '',
            
            // Données générales
            'agence_nom' => $agenceNom ?? $data['agence_nom'] ?? $data['departement'] ?? '',
            'agency_id' => $data['agency_id'] ?? null,
            'date_application' => $dateApplication ?? $data['date_application'] ?? null,
            'date_installation' => $dateInstallation ?? $data['date_installation'] ?? null,
            'sn' => $equipment->numero_serie ?? '',
            
            // Données installateur
            'installateur_nom' => $data['installateur_nom'] ?? $installationData['installateur_nom'] ?? '',
            'installateur_prenom' => $data['installateur_prenom'] ?? $installationData['installateur_prenom'] ?? '',
            'installateur_fonction' => $data['installateur_fonction'] ?? $installationData['installateur_fonction'] ?? 'IT',
            
            // ✅ SIGNATURES (AVEC TOUTES LES RÉCUPÉRATIONS)
            'signature_installateur' => $signatureInstallateur,
            'signature_verificateur' => $signatureVerificateur,
            'signature_verificateur_data' => $signatureVerificateur, // Pour compatibilité
            'signature_utilisateur' => $signatureUtilisateur,
            'signature_utilisateur_data' => $signatureUtilisateur, // Pour compatibilité
        ]);

        // ✅ LOG POUR DEBUG (À RETIRER EN PRODUCTION)
        Log::info('Signature vérificateur récupérée:', [
            'signature_presente' => !empty($signatureVerificateur),
            'taille_signature' => $signatureVerificateur ? strlen($signatureVerificateur) : 0,
        ]);

        // Générer le PDF avec les bonnes données
        $pdf = PDF::loadView('pdf.fiche-installation', [
            'data' => $pdfData,  // ✅ Passer les données complètes ici
        ]);

        // Nom du fichier
        $filename = 'fiche_installation_' . $equipment->numero_serie . '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
        
    } catch (\Exception $e) {
        Log::error('Erreur génération fiche installation: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return redirect()->back()->with('error', 'Erreur lors de la génération de la fiche d\'installation: ' . $e->getMessage());
    }
}
    /**
     * Afficher la page avec les fichiers attachés
     */
/**
 * Afficher la page avec les fichiers attachés
 */
public function showAttachments($id)
{
    try {
        // Récupérer l'approbation
        $approval = TransitionApproval::findOrFail($id);
        
        // Récupérer les données de form_data
        $formData = $approval->form_data ?? [];
        $attachments = $formData['attachments'] ?? [];
        
        // Formater les données pour la vue
        $formattedAttachments = [];
        foreach ($attachments as $attachment) {
            $formattedAttachments[] = [
                'id' => $attachment['id'] ?? uniqid(),
                'name' => $attachment['name'] ?? 'Fichier sans nom',
                'file' => $attachment['file'] ?? '',
                'path' => $attachment['path'] ?? '',
                'date' => $attachment['date'] ?? now()->format('d/m/Y H:i'),
                'original_name' => $attachment['original_name'] ?? 'Fichier',
                'size' => $attachment['size'] ?? 0,
                'size_formatted' => $attachment['size_formatted'] ?? $this->formatBytes($attachment['size'] ?? 0),
                'mime_type' => $attachment['mime_type'] ?? 'application/octet-stream',
                'extension' => $attachment['extension'] ?? pathinfo($attachment['original_name'] ?? '', PATHINFO_EXTENSION),
                'uploaded_by_name' => $attachment['uploaded_by_name'] ?? 'Utilisateur inconnu',
                'is_replacement' => $attachment['is_replacement'] ?? false,
                'replaced_at' => $attachment['replaced_at'] ?? null,
                'replaced_count' => $attachment['replaced_count'] ?? 0,
            ];
        }
        
        // Utiliser le bon chemin de vue
        return view('attachments.index', [
            'approval' => $approval,
            'attachments' => $formattedAttachments
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'affichage des attachments', [
            'approval_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', 'Impossible de charger les fichiers attachés: ' . $e->getMessage());
    }
}

    /**
     * Stocker un fichier attaché (remplace automatiquement TOUS les fichiers existants avec le même nom)
     * Redirige vers la page des attachments après succès
     */
    public function storeAttachment(Request $request, TransitionApproval $approval)
    {
        // Vérifier que l'approbation est validée
        if ($approval->status !== 'approved') {
            return response()->json([
                'success' => false,
                'error' => 'Seules les approbations validées peuvent avoir des fichiers attachés'
            ], 403);
        }

        // Valider la requête
        $request->validate([
            'attachment_name' => 'required|string|max:255',
            'attachment_file' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
        ]);

        try {
            // Récupérer le form_data existant
            $formData = $approval->form_data ?? [];
            
            // Initialiser le tableau d'attachments s'il n'existe pas
            if (!isset($formData['attachments'])) {
                $formData['attachments'] = [];
            }
            
            // Gérer l'upload du fichier
            $file = $request->file('attachment_file');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $displayName = $request->input('attachment_name');
            
            // Vérifier la taille maximale (10MB)
            if ($fileSize > 10240 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier est trop volumineux. Taille maximale: 10MB'
                ], 400);
            }
            
            // Vérifier si des fichiers avec le même nom original existent déjà
            $existingIndices = [];
            $existingAttachments = [];
            $replacedCount = 0;
            
            foreach ($formData['attachments'] as $index => $attachment) {
                if ($attachment['original_name'] === $originalName) {
                    $existingIndices[] = $index;
                    $existingAttachments[] = $attachment;
                }
            }
            
            // Générer un nom de fichier sécurisé
            $extension = $file->getClientOriginalExtension();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
            $fileName = 'attachment_' . time() . '_' . uniqid() . '_' . $safeBaseName . '.' . $extension;
            
            // Stocker le nouveau fichier
            $path = $file->storeAs('attachments', $fileName, 'public');
            $fileUrl = Storage::url($path);
            
            // Si des fichiers existent déjà avec le même nom
            if (!empty($existingIndices)) {
                // Supprimer tous les anciens fichiers physiques
                foreach ($existingAttachments as $oldAttachment) {
                    if (isset($oldAttachment['path'])) {
                        Storage::disk('public')->delete($oldAttachment['path']);
                    } elseif (isset($oldAttachment['file'])) {
                        $oldPath = str_replace('/storage/', '', parse_url($oldAttachment['file'], PHP_URL_PATH));
                        Storage::disk('public')->delete($oldPath);
                    }
                    $replacedCount++;
                }
                
                // Supprimer toutes les anciennes entrées du tableau
                rsort($existingIndices);
                foreach ($existingIndices as $index) {
                    array_splice($formData['attachments'], $index, 1);
                }
                
                // Log de la suppression des anciens fichiers
                Log::info('Anciens fichiers remplacés', [
                    'approval_id' => $approval->id,
                    'old_files_count' => $replacedCount,
                    'old_files' => array_column($existingAttachments, 'original_name'),
                    'new_file' => $originalName,
                    'user_id' => auth()->id()
                ]);
                
                $message = 'Fichier remplacé avec succès (' . $replacedCount . ' ancien(s) fichier(s) supprimé(s))';
                $action = 'remplacé';
                
            } else {
                $message = 'Fichier ajouté avec succès';
                $action = 'ajouté';
            }
            
            // Ajouter le nouvel attachment aux données
            $newAttachment = [
                'id' => uniqid('att_', true),
                'name' => $displayName,
                'file' => $fileUrl,
                'path' => $path,
                'date' => now()->format('d/m/Y H:i'),
                'datetime' => now()->toDateTimeString(),
                'original_name' => $originalName,
                'size' => $fileSize,
                'size_formatted' => $this->formatBytes($fileSize),
                'mime_type' => $file->getMimeType(),
                'extension' => $extension,
                'uploaded_by' => auth()->id() ?? null,
                'uploaded_by_name' => auth()->user()->name ?? 'Système',
                'is_replacement' => ($replacedCount > 0),
            ];
            
            // Ajouter des informations supplémentaires si c'est un remplacement
            if ($replacedCount > 0) {
                $newAttachment['replaced_at'] = now()->toDateTimeString();
                $newAttachment['replaced_count'] = $replacedCount;
                $newAttachment['replaced_old_ids'] = array_column($existingAttachments, 'id');
            }
            
            $formData['attachments'][] = $newAttachment;
            
            // Mettre à jour l'approbation
            $approval->form_data = $formData;
            $approval->save();
            
            // Log de l'action
            Log::info('Fichier attaché ' . $action, [
                'approval_id' => $approval->id,
                'file_name' => $originalName,
                'file_url' => $fileUrl,
                'action' => $action,
                'user_id' => auth()->id(),
                'replaced_count' => $replacedCount
            ]);
            
            // Rediriger vers la page des attachments
            return redirect()->route('approvals.attachments.show', $approval->id)
                ->with('success', $message);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'upload du fichier', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'upload: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprimer un fichier attaché
     */
    public function destroyAttachment(Request $request, TransitionApproval $approval)
    {
        // Vérifier que l'approbation est validée
        if ($approval->status !== 'approved') {
            return response()->json([
                'success' => false,
                'error' => 'Seules les approbations validées peuvent avoir des fichiers attachés'
            ], 403);
        }

        // Valider la requête
        $request->validate([
            'file_url' => 'nullable|string',
            'attachment_id' => 'nullable|string',
        ], [
            'file_url.required_without' => 'Soit file_url, soit attachment_id est requis',
            'attachment_id.required_without' => 'Soit file_url, soit attachment_id est requis',
        ]);

        // Vérifier qu'au moins un paramètre est fourni
        if (!$request->has('file_url') && !$request->has('attachment_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Soit file_url, soit attachment_id est requis'
            ], 400);
        }

        try {
            $fileUrl = $request->input('file_url');
            $attachmentId = $request->input('attachment_id');
            $formData = $approval->form_data ?? [];
            
            // Vérifier si des attachments existent
            if (!isset($formData['attachments']) || empty($formData['attachments'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun fichier trouvé'
                ], 404);
            }
            
            // Chercher et supprimer le fichier
            $found = false;
            $deletedAttachment = null;
            $newAttachments = [];
            
            foreach ($formData['attachments'] as $attachment) {
                $match = false;
                
                // Vérifier par URL ou par ID
                if ($fileUrl && isset($attachment['file']) && $attachment['file'] === $fileUrl) {
                    $match = true;
                } elseif ($attachmentId && isset($attachment['id']) && $attachment['id'] === $attachmentId) {
                    $match = true;
                }
                
                if ($match) {
                    $found = true;
                    $deletedAttachment = $attachment;
                    
                    // Supprimer le fichier physique du storage
                    if (isset($attachment['path'])) {
                        Storage::disk('public')->delete($attachment['path']);
                    } else {
                        // Fallback: extraire le chemin de l'URL
                        $path = str_replace('/storage/', '', parse_url($attachment['file'], PHP_URL_PATH));
                        Storage::disk('public')->delete($path);
                    }
                    
                    // Ne pas ajouter cet attachment au nouveau tableau
                    continue;
                }
                $newAttachments[] = $attachment;
            }
            
            if (!$found) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }
            
            // Mettre à jour les données
            $formData['attachments'] = $newAttachments;
            $approval->form_data = $formData;
            $approval->save();
            
            // Log de l'action
            Log::info('Fichier attaché supprimé', [
                'approval_id' => $approval->id,
                'attachment_id' => $deletedAttachment['id'] ?? null,
                'file_name' => $deletedAttachment['original_name'] ?? null,
                'user_id' => auth()->id()
            ]);
            
            // Si c'est une requête AJAX, retourner JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fichier supprimé avec succès',
                    'deleted_attachment' => $deletedAttachment,
                    'attachments' => $newAttachments
                ], 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            }
            
            // Sinon, rediriger avec message flash
            return redirect()->route('approvals.attachments.show', $approval->id)
                ->with('success', 'Fichier supprimé avec succès');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->errors());
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du fichier', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Formater les octets en format lisible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === 0 || $bytes === null) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Nettoyer les doublons dans les attachments
     */
    public function cleanupDuplicates(TransitionApproval $approval)
    {
        try {
            $formData = $approval->form_data ?? [];
            
            if (!isset($formData['attachments']) || empty($formData['attachments'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun fichier à nettoyer',
                    'cleaned_count' => 0
                ]);
            }
            
            $uniqueAttachments = [];
            $duplicatesRemoved = 0;
            $filesToKeep = [];
            
            // Garder seulement la dernière version de chaque fichier
            foreach ($formData['attachments'] as $attachment) {
                $originalName = $attachment['original_name'];
                
                // Si on a déjà ce fichier, supprimer l'ancien du storage
                if (isset($filesToKeep[$originalName])) {
                    // Supprimer l'ancien fichier physique
                    $oldAttachment = $filesToKeep[$originalName];
                    if (isset($oldAttachment['path'])) {
                        Storage::disk('public')->delete($oldAttachment['path']);
                    }
                    $duplicatesRemoved++;
                }
                
                // Garder la nouvelle version (la dernière dans la boukle)
                $filesToKeep[$originalName] = $attachment;
            }
            
            // Reconstruire le tableau sans doublons
            $formData['attachments'] = array_values($filesToKeep);
            
            // Mettre à jour l'approbation
            $approval->form_data = $formData;
            $approval->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Nettoyage terminé : ' . $duplicatesRemoved . ' doublon(s) supprimé(s)',
                'cleaned_count' => $duplicatesRemoved,
                'remaining_count' => count($formData['attachments']),
                'attachments' => $formData['attachments']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du nettoyage des doublons', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage'
            ], 500);
        }
    }

    /**
     * Lister tous les fichiers attachés (API)
     */
    public function listAttachments(TransitionApproval $approval)
    {
        try {
            // Récupérer le form_data existant
            $formData = $approval->form_data ?? [];
            $attachments = $formData['attachments'] ?? [];
            
            // Formater les tailles des fichiers
            foreach ($attachments as &$attachment) {
                if (isset($attachment['size']) && !isset($attachment['size_formatted'])) {
                    $attachment['size_formatted'] = $this->formatBytes($attachment['size']);
                }
            }
            
            return response()->json([
                'success' => true,
                'approval_id' => $approval->id,
                'approval_status' => $approval->status,
                'count' => count($attachments),
                'total_size' => $this->formatBytes(array_sum(array_column($attachments, 'size'))),
                'attachments' => $attachments
            ], 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la liste des fichiers', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des fichiers'
            ], 500);
        }
    }

    /**
     * Télécharger un fichier attaché
     */
    public function downloadAttachment(Request $request, TransitionApproval $approval)
    {
        try {
            $fileUrl = $request->input('file_url');
            $attachmentId = $request->input('attachment_id');
            $formData = $approval->form_data ?? [];
            
            // Vérifier si des attachments existent
            if (!isset($formData['attachments']) || empty($formData['attachments'])) {
                abort(404, 'Aucun fichier trouvé');
            }
            
            // Chercher le fichier
            foreach ($formData['attachments'] as $attachment) {
                $match = false;
                
                // Vérifier par URL ou par ID
                if ($fileUrl && isset($attachment['file']) && $attachment['file'] === $fileUrl) {
                    $match = true;
                } elseif ($attachmentId && isset($attachment['id']) && $attachment['id'] === $attachmentId) {
                    $match = true;
                }
                
                if ($match) {
                    // Récupérer le chemin du fichier
                    $path = $attachment['path'] ?? str_replace('/storage/', '', parse_url($attachment['file'], PHP_URL_PATH));
                    
                    // Vérifier si le fichier existe
                    if (!Storage::disk('public')->exists($path)) {
                        abort(404, 'Fichier non trouvé sur le serveur');
                    }
                    
                    // Télécharger le fichier
                    return Storage::disk('public')->download($path, $attachment['original_name']);
                }
            }
            
            abort(404, 'Fichier non trouvé');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement du fichier', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage()
            ]);
            
            abort(500, 'Erreur lors du téléchargement');
        }
    }

    /**
     * Obtenir les informations d'un fichier spécifique (API)
     */
    public function getAttachmentInfo(Request $request, TransitionApproval $approval)
    {
        try {
            $fileUrl = $request->input('file_url');
            $attachmentId = $request->input('attachment_id');
            $formData = $approval->form_data ?? [];
            
            // Vérifier si des attachments existent
            if (!isset($formData['attachments']) || empty($formData['attachments'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun fichier trouvé'
                ], 404);
            }
            
            // Chercher le fichier
            foreach ($formData['attachments'] as $attachment) {
                $match = false;
                
                // Vérifier par URL ou par ID
                if ($fileUrl && isset($attachment['file']) && $attachment['file'] === $fileUrl) {
                    $match = true;
                } elseif ($attachmentId && isset($attachment['id']) && $attachment['id'] === $attachmentId) {
                    $match = true;
                }
                
                if ($match) {
                    // Formater la taille si nécessaire
                    if (isset($attachment['size']) && !isset($attachment['size_formatted'])) {
                        $attachment['size_formatted'] = $this->formatBytes($attachment['size']);
                    }
                    
                    // Vérifier si le fichier existe physiquement
                    $path = $attachment['path'] ?? str_replace('/storage/', '', parse_url($attachment['file'], PHP_URL_PATH));
                    $attachment['file_exists'] = Storage::disk('public')->exists($path);
                    
                    return response()->json([
                        'success' => true,
                        'attachment' => $attachment
                    ], 200, [], JSON_UNESCAPED_SLASHES);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Fichier non trouvé'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des infos du fichier', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des informations'
            ], 500);
        }
    }

    /**
     * Synchroniser les fichiers manquants (API)
     */
    public function syncAttachments(TransitionApproval $approval)
    {
        try {
            $formData = $approval->form_data ?? [];
            $attachments = $formData['attachments'] ?? [];
            $updated = false;
            $missingFiles = [];
            
            // Vérifier chaque fichier
            foreach ($attachments as &$attachment) {
                if (isset($attachment['path'])) {
                    $path = $attachment['path'];
                } elseif (isset($attachment['file'])) {
                    $path = str_replace('/storage/', '', parse_url($attachment['file'], PHP_URL_PATH));
                } else {
                    continue;
                }
                
                // Vérifier si le fichier existe physiquement
                if (!Storage::disk('public')->exists($path)) {
                    $missingFiles[] = $attachment['original_name'] ?? $path;
                    $attachment['status'] = 'missing';
                    $attachment['missing_since'] = now()->toDateTimeString();
                    $updated = true;
                } else {
                    // Mettre à jour la taille si nécessaire
                    $fileSize = Storage::disk('public')->size($path);
                    if (!isset($attachment['size']) || $attachment['size'] !== $fileSize) {
                        $attachment['size'] = $fileSize;
                        $attachment['size_formatted'] = $this->formatBytes($fileSize);
                        $attachment['last_verified'] = now()->toDateTimeString();
                        $updated = true;
                    }
                }
            }
            
            if ($updated) {
                $formData['attachments'] = $attachments;
                $approval->form_data = $formData;
                $approval->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Synchronisation terminée',
                'updated' => $updated,
                'missing_files' => $missingFiles,
                'missing_count' => count($missingFiles),
                'total_files' => count($attachments)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la synchronisation des fichiers', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation'
            ], 500);
        }
    }

    /**
 * Traiter une affectation simple (sans flux 3 étapes)
 */
public function simpleAffectation(Request $request, Equipment $equipment)
{
    DB::beginTransaction();

    try {
        // Valider les données
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'affectation_date' => 'required|date',
            'affectation_reason' => 'nullable|string',
            'responsable_name' => 'required|string|max:255',
            'responsable_function' => 'required|string|max:255',
        ]);

        // Vérifier que l'équipement est en stock
        if ($equipment->statut !== 'stock') {
            throw new \Exception("L'équipement n'est pas en stock. Statut actuel: " . $equipment->statut);
        }

        // Récupérer l'utilisateur
        $user = User::find($validated['user_id']);
        $agent = auth()->user();
        $isSuperAdmin = strtolower(trim((string) ($agent->role ?? ''))) === 'super_admin'
            || $agent->email === 'superadmin@cofina.sn';

        // Préparer les données pour l'approbation
        $data = [
            'utilisateur_id' => $validated['user_id'],
            'user_name' => $user->name,
            'user_email' => $user->email,
            'departement' => $validated['department'],
            'poste_affecte' => $validated['position'],
            'date_affectation' => $validated['affectation_date'],
            'affectation_reason' => $validated['affectation_reason'],
            'responsable_name' => $validated['responsable_name'],
            'responsable_function' => $validated['responsable_function'],
            'agent_nom' => $agent->name,
            'agent_prenom' => $agent->prenom ?? $agent->name,
            'agent_fonction' => 'AGENT IT',
            'choix_fiches' => [], // Aucune fiche spécifique pour affectation simple
            'form_type' => 'simple_affectation',
            'transition_type' => 'stock_to_parc',
            'submitted_at' => now()->format('Y-m-d H:i:s'),
            'is_super_admin_submission' => $isSuperAdmin,
        ];

        // Créer l'approbation
        $approval = TransitionApproval::create([
            'equipment_id' => $equipment->id,
            'from_status' => 'stock',
            'to_status' => 'parc',
            'submitted_by' => $agent->id,
            'data' => json_encode($data),
            'status' => 'pending',
            'type' => 'stock_to_parc',
            'requires_super_admin_validation' => !$isSuperAdmin,
        ]);

        // Si Super Admin, approuver directement
        if ($isSuperAdmin) {
            $this->processSimpleAffectation($equipment, $data, $approval);
            $approval->update([
                'status' => 'approved',
                'approved_by' => $agent->id,
                'approved_at' => now(),
            ]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Affectation simple approuvée et exécutée avec succès!',
                'redirect_url' => route('equipment.show', $equipment),
            ]);
        } else {
            // Notifier les Super Admins
            $this->notifySuperAdmins($approval);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Affectation simple soumise avec succès! Attente de validation Super Admin.',
                'redirect_url' => route('transitions.approval.show', $approval),
                'approval_id' => $approval->id,
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur affectation simple: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Traiter une affectation simple
 */
private function processSimpleAffectation(Equipment $equipment, array $data, TransitionApproval $approval)
{
    // Mettre à jour l'équipement
    $equipment->update([
        'statut' => 'parc',
        'departement' => $data['departement'],
        'poste_staff' => $data['poste_affecte'],
        'date_mise_service' => $data['date_affectation'],
        'notes' => $data['affectation_reason'] ?? $equipment->notes,
    ]);

    // Créer l'entrée dans le parc
    Parc::create([
        'numero_serie' => $equipment->numero_serie,
        'utilisateur_id' => $data['utilisateur_id'],
        'departement' => $data['departement'],
        'poste_affecte' => $data['poste_affecte'],
        'date_affectation' => $data['date_affectation'],
        'statut_usage' => 'actif',
        'notes_affectation' => $data['affectation_reason'] ?? 'Affectation simple',
        'transition_approval_id' => $approval->id,
        'responsable_name' => $data['responsable_name'],
        'responsable_function' => $data['responsable_function'],
        'installation_completed' => false, // Pas d'installation pour affectation simple
    ]);

    // Mettre à jour le stock si existant
    if ($equipment->stock) {
        $equipment->stock->update([
            'date_sortie' => now(),
            'etat' => 'sorti',
        ]);
    }
}
/**
 * Soumettre une demande de mise hors service
 */
/**
 * Soumettre une demande de mise hors service
 */
public function submitHorsService(Request $request)
{
    DB::beginTransaction();

    try {
        // Log des données reçues pour débogage
        Log::info('Données reçues pour hors service:', $request->all());

        // Validation complète avec messages personnalisés
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'transition_type' => 'required|string|in:stock_to_hors_service',
            'raison' => 'required|string|max:255',
            'destinataire' => 'required|string|max:255',
            'valeur_residuelle' => 'nullable|numeric|min:0',
            'description_incident' => 'required|string|min:10',
            'justificatif' => 'nullable|string|max:500',
            'observations' => 'nullable|string',
        ], [
            'equipment_id.required' => 'L\'ID de l\'équipement est requis',
            'equipment_id.exists' => 'Cet équipement n\'existe pas',
            'transition_type.required' => 'Le type de transition est requis',
            'transition_type.in' => 'Type de transition invalide',
            'raison.required' => 'La raison est obligatoire',
            'destinataire.required' => 'Le destinataire est obligatoire',
            'valeur_residuelle.numeric' => 'La valeur résiduelle doit être un nombre',
            'valeur_residuelle.min' => 'La valeur résiduelle ne peut pas être négative',
            'description_incident.required' => 'La description de l\'incident est obligatoire',
            'description_incident.min' => 'La description doit contenir au moins 10 caractères',
            'justificatif.max' => 'Le justificatif ne doit pas dépasser 500 caractères',
        ]);

        // Nettoyer les données
        $validated = array_map('trim', $validated);
        
        // Convertir valeur résiduelle en null si vide
        if (isset($validated['valeur_residuelle']) && empty($validated['valeur_residuelle'])) {
            $validated['valeur_residuelle'] = null;
        }

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $user = auth()->user();
        $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
            || $user->email === 'superadmin@cofina.sn';

        // Vérifier que l'équipement est en stock
        if ($equipment->statut !== 'stock') {
            throw new \Exception("L'équipement n'est pas en stock. Statut actuel: " . $equipment->statut);
        }

        // Log des données après validation
        Log::info('Données validées:', $validated);

        // Préparer les données pour l'approbation
        $data = [
            'transition_type' => $validated['transition_type'],
            'raison' => $validated['raison'],
            'destinataire' => $validated['destinataire'],
            'valeur_residuelle' => $validated['valeur_residuelle'],
            'description_incident' => $validated['description_incident'],
            'justificatif' => $validated['justificatif'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'agent_nom' => $user->name,
            'agent_prenom' => $user->prenom ?? $user->name,
            'agent_fonction' => 'AGENT IT',
            'submitted_at' => now()->format('Y-m-d H:i:s'),
            'is_super_admin_submission' => $isSuperAdmin,
            'equipment_info' => [
                'id' => $equipment->id,
                'nom' => $equipment->nom,
                'numero_serie' => $equipment->numero_serie,
                'modele' => $equipment->modele,
                'type' => $equipment->type,
                'date_acquisition' => $equipment->date_acquisition,
            ]
        ];

        // Créer l'approbation
        $approval = TransitionApproval::create([
            'equipment_id' => $equipment->id,
            'from_status' => 'stock',
            'to_status' => 'hors_service',
            'submitted_by' => $user->id,
            'data' => json_encode($data),
            'status' => 'pending',
            'type' => 'stock_to_hors_service',
            'requires_super_admin_validation' => !$isSuperAdmin,
        ]);

        Log::info('Approbation créée:', ['approval_id' => $approval->id]);

        // Si Super Admin, approuver directement
        if ($isSuperAdmin) {
            $this->processHorsService($equipment, $data, $approval);
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            DB::commit();

            Log::info('Hors service approuvé directement par Super Admin');

            return response()->json([
                'success' => true,
                'message' => 'Équipement mis hors service avec succès!',
                'redirect_url' => route('equipment.show', $equipment),
            ]);
        } else {
            // Notifier les Super Admins
            $this->notifySuperAdminsHorsService($approval);
            
            DB::commit();

            Log::info('Demande hors service soumise pour approbation');

            return response()->json([
                'success' => true,
                'message' => 'Demande de mise hors service soumise avec succès! Attente de validation Super Admin.',
                'redirect_url' => route('transitions.hors-service-approval', $approval),
                'approval_id' => $approval->id,
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        
        Log::error('Erreur de validation hors service:', [
            'errors' => $e->errors(),
            'data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $e->errors(),
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur submit hors service: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Traiter la mise hors service
 */
/**
 * Traiter la mise hors service
 */
private function processHorsService(Equipment $equipment, array $data, TransitionApproval $approval)
{
    // Mettre à jour l'équipement
    $equipment->update([
        'statut' => 'hors_service',
        'date_hors_service' => now(),
        'raison_hors_service' => $data['raison'],
        'notes' => $data['observations'] ?? $equipment->notes,
    ]);

    // Vérifier que la table existe
    if (!Schema::hasTable('hors_service')) {
        throw new \Exception("La table 'hors_service' n'existe pas. Veuillez exécuter les migrations.");
    }

    // Préparer les valeurs pour la raison et le destinataire
    $raison = $data['raison'];
    $destinataire = $data['destinataire'];
    
    // S'assurer que les valeurs sont valides pour l'enum
    $raisonsValides = ['panne_irreparable', 'obsolescence', 'accident', 'vol', 'autre'];
    $destinatairesValides = ['reforme', 'destruction', 'don', 'vente'];
    
    if (!in_array($raison, $raisonsValides)) {
        $raison = 'autre'; // Valeur par défaut
    }
    
    if (!in_array($destinataire, $destinatairesValides)) {
        $destinataire = 'destruction'; // Valeur par défaut
    }

    try {
        // Créer l'entrée dans la table hors_service (structure actuelle)
        DB::table('hors_service')->insert([
            'numero_serie' => $equipment->numero_serie,
            'date_hors_service' => now(),
            'raison' => $raison,
            'description_incident' => $data['description_incident'],
            'destinataire' => $destinataire,
            'date_traitement' => null, // À traiter plus tard
            'valeur_residuelle' => $data['valeur_residuelle'] ?? null,
            'justificatif' => $data['justificatif'] ?? null,
            'observations' => $data['observations'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        Log::info('Entrée hors_service créée pour numero_serie: ' . $equipment->numero_serie);
        
    } catch (\Exception $e) {
        Log::error('Erreur création hors_service: ' . $e->getMessage());
        throw new \Exception('Erreur lors de la création de l\'entrée hors_service: ' . $e->getMessage());
    }

    // Mettre à jour le stock si existant
    if ($equipment->stock) {
        $equipment->stock->update([
            'date_sortie' => now(),
            'etat' => 'sorti_hors_service',
        ]);
    }
}

/**
 * Approuver une demande de mise hors service
 */
public function approveHorsService(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canApprove) {
        abort(403, "Seuls les super admins peuvent approuver des transitions.");
    }

    DB::beginTransaction();

    try {
        $validated = $request->validate([
            'checklist' => 'required|array',
            'checklist.*' => 'boolean',
            'checklist.verif_raison' => 'required|accepted',
            'checklist.verif_justificatif' => 'required|accepted',
            'checklist.verif_valeur_residuelle' => 'nullable|accepted',
            'validation_notes' => 'nullable|string',
        ]);

        $data = json_decode($approval->data, true);
        $equipment = $approval->equipment;

        // Traiter la mise hors service
        $this->processHorsService($equipment, $data, $approval);

        // Mettre à jour l'approbation
        $approval->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'checklist_data' => json_encode($validated['checklist']),
            'validation_notes' => $validated['validation_notes'] ?? null,
        ]);

        DB::commit();

        // Notifier l'agent IT
        $this->notifyAgentHorsServiceApproved($approval);

        return redirect()->route('admin.approvals')
            ->with('success', 'Mise hors service validée et enregistrée !');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approbation hors service erreur: ' . $e->getMessage());

        return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Rejeter une demande de mise hors service
 */
public function rejectHorsService(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canReject = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canReject) {
        abort(403, "Seuls les super admins peuvent rejeter des transitions.");
    }

    $validated = $request->validate([
        'raison_rejet' => 'required|string|max:500',
    ]);

    $approval->update([
        'status' => 'rejected',
        'rejected_by' => $user->id,
        'rejected_at' => now(),
        'rejection_reason' => $validated['raison_rejet'],
    ]);

    $this->notifyAgentHorsServiceRejected($approval);

    return redirect()->route('admin.approvals')
        ->with('info', 'Demande de mise hors service rejetée.');
}

/**
 * Notifier les Super Admins pour une demande hors service
 */


/**
 * Notifier l'agent que sa demande hors service est approuvée
 */
private function notifyAgentHorsServiceApproved($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification approbation hors service à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Notifier l'agent que sa demande hors service est rejetée
 */
private function notifyAgentHorsServiceRejected($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification rejet hors service à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}
/**
 * Afficher la page d'approbation hors service
 */
/**
 * Afficher la page d'approbation pour une mise hors service
 */
public function showHorsServiceApproval(TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    // Vérifier les permissions
    $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
        || $user->email === 'superadmin@cofina.sn'
        || $approval->submitted_by === $user->id;

    if (!$canView) {
        abort(403, "Vous n'avez pas accès à cette approbation.");
    }

    // Vérifier que c'est bien une approbation de type hors service
    if ($approval->type !== 'stock_to_hors_service') {
        abort(404, "Cette approbation n'est pas une demande de mise hors service.");
    }

    // Charger les relations nécessaires
    $approval->load(['equipment', 'submitter', 'approver']);
    
    // Décoder les données
    $data = json_decode($approval->data, true);
    
    // Si les données ne sont pas valides, utiliser un tableau vide
    if (!$data || !is_array($data)) {
        $data = [];
    }

    // Vérifier les checklist_data si l'approbation est déjà approuvée
    $checklistData = [];
    if ($approval->checklist_data) {
        $checklistData = json_decode($approval->checklist_data, true);
    }

    // Passer les données à la vue
    return view('admin.hors-service-approval', [
        'approval' => $approval,
        'data' => $data,
        'checklistData' => $checklistData,
    ]);
}
// TransitionController.php
/**
 * Lister les approbations de mise hors service
 */
/**
 * Lister les approbations de mise hors service
 */
public function listHorsServiceApprovals(Request $request)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    // Vérifier les autorisations
    $isAuthorized = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$isAuthorized) {
        abort(403, "Accès réservé aux administrateurs.");
    }

    // Filtrer seulement les approbations hors service
    $query = TransitionApproval::with(['equipment', 'submitter', 'approver'])
        ->whereIn('type', ['stock_to_hors_service', 'parc_to_hors_service', 'maintenance_to_hors_service'])
        ->orderBy('created_at', 'desc');

    // Appliquer les filtres
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('equipment', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            })->orWhereHas('submitter', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    }

    $approvals = $query->paginate(20);

    return view('admin.hors-service-list', compact('approvals'));
}
/**
 * Soumettre une demande d'envoi en maintenance
 */
public function submitMaintenance(Request $request)
{
    DB::beginTransaction();

    try {
        // Validation complète
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'transition_type' => 'required|string|in:parc_to_maintenance',
            'type_maintenance' => 'required|string|in:preventive,corrective,curative',
            'prestataire' => 'required|string|max:255',
            'date_retour_prevue' => 'required|date|after_or_equal:today',
            'priorite' => 'nullable|string|in:normal,urgent,critique',
            'description_panne' => 'required|string|min:10',
            'cout_estime' => 'nullable|numeric|min:0',
            'localisation' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'equipment_id.required' => 'L\'ID de l\'équipement est requis',
            'equipment_id.exists' => 'Cet équipement n\'existe pas',
            'type_maintenance.required' => 'Le type de maintenance est obligatoire',
            'prestataire.required' => 'Le prestataire est obligatoire',
            'date_retour_prevue.required' => 'La date de retour prévue est obligatoire',
            'date_retour_prevue.after_or_equal' => 'La date de retour doit être aujourd\'hui ou ultérieure',
            'description_panne.required' => 'La description de la panne est obligatoire',
            'description_panne.min' => 'La description doit contenir au moins 10 caractères',
            'cout_estime.numeric' => 'Le coût estimé doit être un nombre',
            'cout_estime.min' => 'Le coût estimé ne peut pas être négatif',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $user = auth()->user();
        $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
            || $user->email === 'superadmin@cofina.sn';

        // Vérifier que l'équipement est au parc
        if ($equipment->statut !== 'parc') {
            throw new \Exception("L'équipement n'est pas au parc. Statut actuel: " . $equipment->statut);
        }

        // Nettoyer les données
        $validated = array_map('trim', $validated);
        
        // Convertir les valeurs numériques
        if (isset($validated['cout_estime']) && empty($validated['cout_estime'])) {
            $validated['cout_estime'] = null;
        }

        // Récupérer les informations du parc
        $parcInfo = $equipment->parc()->latest()->first();

        // Préparer les données pour l'approbation
        $data = [
            'transition_type' => $validated['transition_type'],
            'type_maintenance' => $validated['type_maintenance'],
            'prestataire' => $validated['prestataire'],
            'date_retour_prevue' => $validated['date_retour_prevue'],
            'priorite' => $validated['priorite'] ?? 'normal',
            'description_panne' => $validated['description_panne'],
            'cout_estime' => $validated['cout_estime'],
            'localisation' => $validated['localisation'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'agent_nom' => $user->name,
            'agent_prenom' => $user->prenom ?? $user->name,
            'agent_fonction' => 'AGENT IT',
            'submitted_at' => now()->format('Y-m-d H:i:s'),
            'is_super_admin_submission' => $isSuperAdmin,
            'equipment_info' => [
                'id' => $equipment->id,
                'nom' => $equipment->nom,
                'numero_serie' => $equipment->numero_serie,
                'modele' => $equipment->modele,
                'type' => $equipment->type,
            ],
            'parc_info' => $parcInfo ? [
                'utilisateur_id' => $parcInfo->utilisateur_id,
                'utilisateur_nom' => $parcInfo->utilisateur->name ?? null,
                'departement' => $parcInfo->departement,
                'poste_affecte' => $parcInfo->poste_affecte,
                'date_affectation' => $parcInfo->date_affectation,
            ] : null,
        ];

        // Créer l'approbation
        $approval = TransitionApproval::create([
            'equipment_id' => $equipment->id,
            'from_status' => 'parc',
            'to_status' => 'maintenance',
            'submitted_by' => $user->id,
            'data' => json_encode($data),
            'status' => 'pending',
            'type' => 'parc_to_maintenance',
            'requires_super_admin_validation' => !$isSuperAdmin,
        ]);

        // Si Super Admin, approuver directement
        if ($isSuperAdmin) {
            $this->processMaintenance($equipment, $data, $approval);
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Équipement envoyé en maintenance avec succès!',
                'redirect_url' => route('equipment.show', $equipment),
            ]);
        } else {
            // Notifier les Super Admins
            $this->notifySuperAdminsMaintenance($approval);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'envoi en maintenance soumise avec succès! Attente de validation Super Admin.',
                'redirect_url' => route('transitions.approval.show', $approval),
                'approval_id' => $approval->id,
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $e->errors(),
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur submit maintenance: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Traiter l'envoi en maintenance
 */
/**
 * Traiter l'envoi en maintenance (version robuste)
 */
/**
 * Traiter l'envoi en maintenance - Version adaptée à votre schéma
 */
private function processMaintenance(Equipment $equipment, array $data, TransitionApproval $approval)
{
    // Mettre à jour l'équipement
    $equipment->update([
        'statut' => 'maintenance',
        'date_derniere_maintenance' => now(),
    ]);

    // Mapping des champs du formulaire vers la table maintenance
    $maintenanceData = [
        // Champs obligatoires de la table
        'numero_serie' => $equipment->numero_serie,
        'date_depart' => now(), // Date actuelle pour date_depart
        'date_retour_prevue' => $this->formatDate($data['date_retour_prevue'] ?? now()->addDays(7)),
        'date_retour_reelle' => null,
        'type_maintenance' => $this->mapMaintenanceType($data['type_maintenance'] ?? 'preventive'),
        'prestataire' => $data['prestataire'] ?? 'Non spécifié',
        'cout' => floatval($data['cout_estime'] ?? 0),
        'statut' => 'en_cours',
        'description_panne' => $data['description_panne'] ?? 'Non spécifié',
        'travaux_realises' => null,
        'observations' => $this->buildObservations($data, $approval),
        'created_at' => now(),
        'updated_at' => now(),
    ];

    // Validation : s'assurer que les valeurs sont valides
    $this->validateMaintenanceData($maintenanceData);

    // Insérer dans la table maintenance
    DB::table('maintenance')->insert($maintenanceData);

    // Mettre à jour le parc
    if ($equipment->parc) {
        $equipment->parc->update([
            'statut_usage' => 'inactif',
            'date_sortie_maintenance' => now(),
            'notes_affectation' => 'En maintenance: ' . $maintenanceData['description_panne'],
        ]);
    }
    
    Log::info('Maintenance créée', [
        'equipment' => $equipment->numero_serie,
        'maintenance_type' => $maintenanceData['type_maintenance'],
        'prestataire' => $maintenanceData['prestataire'],
        'approval_id' => $approval->id,
    ]);
}

/**
 * Formater la date pour la base de données
 */
private function formatDate($date)
{
    if ($date instanceof \DateTime) {
        return $date->format('Y-m-d');
    }
    
    try {
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
        return now()->addDays(7)->format('Y-m-d');
    }
}

/**
 * Mapper le type de maintenance
 */
private function mapMaintenanceType($type)
{
    $validTypes = ['preventive', 'corrective', 'curative'];
    
    // Nettoyer et valider le type
    $type = strtolower(trim($type));
    
    // Vérifier si c'est un type valide
    if (in_array($type, $validTypes)) {
        return $type;
    }
    
    // Mapping des valeurs courantes
    $mapping = [
        'preventive' => 'preventive',
        'preventif' => 'preventive',
        'préventif' => 'preventive',
        'corrective' => 'corrective',
        'correctif' => 'corrective',
        'curative' => 'curative',
        'curatif' => 'curative',
    ];
    
    return $mapping[$type] ?? 'preventive';
}

/**
 * Construire les observations
 */
private function buildObservations($data, $approval)
{
    $observations = [];
    
    // Ajouter les notes principales
    if (!empty($data['notes'])) {
        $observations[] = "Notes: " . $data['notes'];
    }
    
    // Ajouter la priorité si spécifiée
    if (!empty($data['priorite'])) {
        $observations[] = "Priorité: " . $data['priorite'];
    }
    
    // Ajouter la localisation si spécifiée
    if (!empty($data['localisation'])) {
        $observations[] = "Localisation: " . $data['localisation'];
    }
    
    // Ajouter l'ID d'approbation
    $observations[] = "ID Approbation: " . $approval->id;
    
    // Ajouter la date
    $observations[] = "Date déclaration: " . now()->format('d/m/Y H:i');
    
    return implode(' | ', $observations);
}

/**
 * Valider les données de maintenance
 */
private function validateMaintenanceData(&$data)
{
    // S'assurer que le cout est un nombre
    $data['cout'] = floatval($data['cout']);
    
    // Limiter la longueur des champs texte
    $data['description_panne'] = substr($data['description_panne'], 0, 2000);
    $data['observations'] = substr($data['observations'], 0, 2000);
    $data['prestataire'] = substr($data['prestataire'], 0, 255);
    
    // S'assurer que le type est valide
    if (!in_array($data['type_maintenance'], ['preventive', 'corrective', 'curative'])) {
        $data['type_maintenance'] = 'preventive';
    }
    
    // S'assurer que le statut est valide
    if (!in_array($data['statut'], ['en_cours', 'termine', 'en_attente'])) {
        $data['statut'] = 'en_cours';
    }
}

/**
 * Approuver une demande d'envoi en maintenance
 */
public function approveMaintenance(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canApprove) {
        abort(403, "Seuls les super admins peuvent approuver des transitions.");
    }

    DB::beginTransaction();

    try {
        $validated = $request->validate([
            'checklist' => 'required|array',
            'checklist.*' => 'boolean',
            'checklist.verif_type_maintenance' => 'required|accepted',
            'checklist.verif_prestataire' => 'required|accepted',
            'checklist.verif_description' => 'required|accepted',
            'checklist.verif_cout_estime' => 'nullable|accepted',
            'validation_notes' => 'nullable|string',
        ]);

        $data = json_decode($approval->data, true);
        $equipment = $approval->equipment;

        // Traiter l'envoi en maintenance
        $this->processMaintenance($equipment, $data, $approval);

        // Mettre à jour l'approbation
        $approval->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'checklist_data' => json_encode($validated['checklist']),
            'validation_notes' => $validated['validation_notes'] ?? null,
        ]);

        DB::commit();

        // Notifier l'utilisateur concerné (si applicable)
        $this->notifyUserMaintenance($approval);

        return redirect()->route('admin.maintenance-approvals.list')
            ->with('success', 'Envoi en maintenance validé et enregistré !');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approbation maintenance erreur: ' . $e->getMessage());

        return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Rejeter une demande d'envoi en maintenance
 */
public function rejectMaintenance(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canReject = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canReject) {
        abort(403, "Seuls les super admins peuvent rejeter des transitions.");
    }

    $validated = $request->validate([
        'raison_rejet' => 'required|string|max:500',
    ]);

    $approval->update([
        'status' => 'rejected',
        'rejected_by' => $user->id,
        'rejected_at' => now(),
        'rejection_reason' => $validated['raison_rejet'],
    ]);

    $this->notifyUserMaintenanceRejected($approval);

    return redirect()->route('admin.maintenance-approvals.list')
        ->with('info', 'Demande d\'envoi en maintenance rejetée.');
}

/**
 * Notifier les Super Admins pour une demande maintenance
 */
private function notifySuperAdminsMaintenance($approval)
{
    $superAdmins = User::where('role', 'super_admin')->get();

    foreach ($superAdmins as $admin) {
        Log::info("Notification maintenance à Super Admin: {$admin->email} - Approbation #{$approval->id}");
        // Ici vous pouvez implémenter l'envoi d'email ou notification
    }
}

/**
 * Notifier l'utilisateur concerné par la maintenance
 */
private function notifyUserMaintenance($approval)
{
    $data = json_decode($approval->data, true);
    
    // Si l'équipement était affecté à un utilisateur
    if (isset($data['parc_info']['utilisateur_id'])) {
        $user = User::find($data['parc_info']['utilisateur_id']);
        
        if ($user) {
            Log::info("Notification maintenance à utilisateur: {$user->email} - Approbation #{$approval->id}");
            // Envoyer une notification ou email à l'utilisateur
        }
    }
    
    // Notifier aussi l'agent IT qui a soumis la demande
    $agent = User::find($approval->submitted_by);
    if ($agent) {
        Log::info("Notification approbation maintenance à agent: {$agent->email} - Approbation #{$approval->id}");
    }
}

/**
 * Notifier l'utilisateur que sa demande maintenance est rejetée
 */
private function notifyUserMaintenanceRejected($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification rejet maintenance à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}
/**
 * Lister les approbations de maintenance
 */
public function listMaintenanceApprovals(Request $request)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    // Vérifier les autorisations
    $isAuthorized = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$isAuthorized) {
        abort(403, "Accès réservé aux administrateurs.");
    }

    // Filtrer seulement les approbations maintenance
    $query = TransitionApproval::with(['equipment', 'submitter', 'approver'])
        ->where('type', 'parc_to_maintenance')
        ->orderBy('created_at', 'desc');

    // Appliquer les filtres
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('type_maintenance')) {
        $query->whereRaw("JSON_EXTRACT(data, '$.type_maintenance') = ?", [$request->type_maintenance]);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('equipment', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            })->orWhereHas('submitter', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereRaw("JSON_EXTRACT(data, '$.prestataire') LIKE ?", ["%{$search}%"]);
        });
    }

    $approvals = $query->paginate(20);

    return view('admin.maintenance-list', compact('approvals'));
}

/**
 * Afficher les détails d'une approbation maintenance
 */
public function showMaintenanceApproval(TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
        || $user->email === 'superadmin@cofina.sn'
        || $approval->submitted_by === $user->id;

    if (!$canView) {
        abort(403, "Vous n'avez pas accès à cette approbation.");
    }

    // Vérifier que c'est bien une approbation maintenance
    if ($approval->type !== 'parc_to_maintenance') {
        abort(404, "Cette approbation n'est pas une demande de maintenance.");
    }

    $approval->load(['equipment', 'submitter', 'approver']);
    $data = json_decode($approval->data, true);

    return view('admin.maintenance-approval', compact('approval', 'data'));
}
/**
 * Soumettre une déclaration de perte
 */
public function submitPerdu(Request $request)
{
    DB::beginTransaction();

    try {
        // Validation des données
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'transition_type' => 'required|string|in:parc_to_perdu',
            'type_disparition' => 'required|string|in:vol,perte,non_localise',
            'date_disparition' => 'required|date',
            'lieu_disparition' => 'required|string|max:255',
            'responsable_disparition' => 'nullable|string|max:255',
            'plainte_deposee' => 'required|boolean',
            'numero_plainte' => 'nullable|string|max:100',
            'valeur_assuree' => 'nullable|numeric|min:0',
            'circonstances' => 'required|string|min:10',
            'mesures_prises' => 'nullable|string',
            'observations' => 'nullable|string',
        ], [
            'equipment_id.required' => 'L\'ID de l\'équipement est requis',
            'equipment_id.exists' => 'Cet équipement n\'existe pas',
            'type_disparition.required' => 'Le type de disparition est obligatoire',
            'type_disparition.in' => 'Type de disparition invalide',
            'date_disparition.required' => 'La date de disparition est obligatoire',
            'lieu_disparition.required' => 'Le lieu de disparition est obligatoire',
            'circonstances.required' => 'La description des circonstances est obligatoire',
            'circonstances.min' => 'La description doit contenir au moins 10 caractères',
            'valeur_assuree.numeric' => 'La valeur assurée doit être un nombre',
            'valeur_assuree.min' => 'La valeur assurée ne peut pas être négative',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $user = auth()->user();
        $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
            || $user->email === 'superadmin@cofina.sn';

        // Vérifier que l'équipement est en parc
        if ($equipment->statut !== 'parc') {
            throw new \Exception("L'équipement n'est pas en parc. Statut actuel: " . $equipment->statut);
        }

        // Préparer les données
        $data = [
            'transition_type' => $validated['transition_type'],
            'type_disparition' => $validated['type_disparition'],
            'date_disparition' => $validated['date_disparition'],
            'lieu_disparition' => $validated['lieu_disparition'],
            'responsable_disparition' => $validated['responsable_disparition'] ?? null,
            'plainte_deposee' => (bool)$validated['plainte_deposee'],
            'numero_plainte' => $validated['numero_plainte'] ?? null,
            'valeur_assuree' => $validated['valeur_assuree'] ?? null,
            'circonstances' => $validated['circonstances'],
            'mesures_prises' => $validated['mesures_prises'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'agent_nom' => $user->name,
            'agent_prenom' => $user->prenom ?? $user->name,
            'agent_fonction' => 'AGENT IT',
            'submitted_at' => now()->format('Y-m-d H:i:s'),
            'is_super_admin_submission' => $isSuperAdmin,
            'equipment_info' => [
                'id' => $equipment->id,
                'nom' => $equipment->nom,
                'numero_serie' => $equipment->numero_serie,
                'modele' => $equipment->modele,
                'type' => $equipment->type,
                'date_acquisition' => $equipment->date_acquisition,
                'departement' => $equipment->departement,
                'poste_staff' => $equipment->poste_staff,
            ]
        ];

        // Créer l'approbation
        $approval = TransitionApproval::create([
            'equipment_id' => $equipment->id,
            'from_status' => 'parc',
            'to_status' => 'perdu',
            'submitted_by' => $user->id,
            'data' => json_encode($data),
            'status' => 'pending',
            'type' => 'parc_to_perdu',
            'requires_super_admin_validation' => !$isSuperAdmin,
        ]);

        // Si Super Admin, approuver directement
        if ($isSuperAdmin) {
            $this->processPerdu($equipment, $data, $approval);
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Équipement déclaré perdu avec succès!',
                'redirect_url' => route('equipment.show', $equipment),
            ]);
        } else {
            // Notifier les Super Admins
            $this->notifySuperAdminsPerdu($approval);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Déclaration de perte soumise avec succès! Attente de validation Super Admin.',
                'redirect_url' => route('transitions.approval.show', $approval),
                'approval_id' => $approval->id,
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur submit perdu: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Traiter la déclaration de perte
 */
/**
 * Traiter la déclaration de perte (version pour table existante)
 */
private function processPerdu(Equipment $equipment, array $data, TransitionApproval $approval)
{
    // Mettre à jour l'équipement
    $equipment->update([
        'statut' => 'perdu',
        'date_perdu' => $data['date_disparition'],
        'raison_perdu' => $data['type_disparition'],
        'notes' => $data['observations'] ?? $equipment->notes,
    ]);

    // Vérifier si un enregistrement existe déjà pour cet équipement
    $existing = DB::table('perdu')->where('numero_serie', $equipment->numero_serie)->first();
    
    if ($existing) {
        // Mettre à jour l'enregistrement existant
        DB::table('perdu')
            ->where('numero_serie', $equipment->numero_serie)
            ->update([
                'type_disparition' => $data['type_disparition'],
                'date_disparition' => $data['date_disparition'],
                'lieu_disparition' => $data['lieu_disparition'],
                'circonstances' => $data['circonstances'],
                'plainte_deposee' => $data['plainte_deposee'],
                'numero_plainte' => $data['numero_plainte'] ?? null,
                'valeur_assuree' => $data['valeur_assuree'] ?? null,
                'observations' => ($data['observations'] ?? '') . ' | ' . ($data['mesures_prises'] ?? ''),
                'statut_recherche' => 'en_cours',
                'updated_at' => now(),
            ]);
    } else {
        // Créer un nouvel enregistrement avec la structure existante
        DB::table('perdu')->insert([
            'numero_serie' => $equipment->numero_serie,
            'type_disparition' => $data['type_disparition'],
            'date_disparition' => $data['date_disparition'],
            'lieu_disparition' => $data['lieu_disparition'],
            'circonstances' => $data['circonstances'],
            'plainte_deposee' => $data['plainte_deposee'],
            'numero_plainte' => $data['numero_plainte'] ?? null,
            'valeur_assuree' => $data['valeur_assuree'] ?? null,
            'statut_recherche' => 'en_cours',
            'observations' => ($data['observations'] ?? '') . ' | Mesures: ' . ($data['mesures_prises'] ?? ''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Mettre à jour le parc si existant
    if ($equipment->parc) {
        $equipment->parc->update([
            'date_sortie' => now(),
            'statut_usage' => 'en_pret',
            'notes_affectation' => 'Déclaré perdu: ' . $data['type_disparition'],
        ]);
    }
}

/**
 * Approuver une déclaration de perte
 */
public function approvePerdu(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canApprove) {
        abort(403, "Seuls les super admins peuvent approuver des transitions.");
    }

    DB::beginTransaction();

    try {
        $validated = $request->validate([
            'checklist' => 'required|array',
            'checklist.*' => 'boolean',
            'checklist.verif_circonstances' => 'required|accepted',
            'checklist.verif_mesures_prises' => 'required|accepted',
            'checklist.verif_plainte' => 'nullable|accepted',
            'validation_notes' => 'nullable|string',
        ]);

        $data = json_decode($approval->data, true);
        $equipment = $approval->equipment;

        // Traiter la déclaration de perte
        $this->processPerdu($equipment, $data, $approval);

        // Mettre à jour l'approbation
        $approval->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'checklist_data' => json_encode($validated['checklist']),
            'validation_notes' => $validated['validation_notes'] ?? null,
        ]);

        DB::commit();

        // Notifier l'agent IT
        $this->notifyAgentPerduApproved($approval);

        return redirect()->route('admin.approvals')
            ->with('success', 'Déclaration de perte validée et enregistrée !');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approbation perdu erreur: ' . $e->getMessage());

        return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Rejeter une déclaration de perte
 */
public function rejectPerdu(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canReject = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canReject) {
        abort(403, "Seuls les super admins peuvent rejeter des transitions.");
    }

    $validated = $request->validate([
        'raison_rejet' => 'required|string|max:500',
    ]);

    $approval->update([
        'status' => 'rejected',
        'rejected_by' => $user->id,
        'rejected_at' => now(),
        'rejection_reason' => $validated['raison_rejet'],
    ]);

    $this->notifyAgentPerduRejected($approval);

    return redirect()->route('admin.approvals')
        ->with('info', 'Déclaration de perte rejetée.');
}

/**
 * Notifier les Super Admins pour une déclaration de perte
 */
private function notifySuperAdminsPerdu($approval)
{
    $superAdmins = User::where('role', 'super_admin')->get();

    foreach ($superAdmins as $admin) {
        Log::info("Notification perte à Super Admin: {$admin->email} - Approbation #{$approval->id}");
        // Ici vous pouvez implémenter l'envoi d'email ou notification
    }
}

/**
 * Notifier l'agent que sa déclaration de perte est approuvée
 */
private function notifyAgentPerduApproved($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification approbation perte à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Notifier l'agent que sa déclaration de perte est rejetée
 */
private function notifyAgentPerduRejected($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification rejet perte à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Lister les approbations de déclaration de perte
 */
public function listPerduApprovals(Request $request)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    // Vérifier les autorisations
    $isAuthorized = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$isAuthorized) {
        abort(403, "Accès réservé aux administrateurs.");
    }

    // Filtrer seulement les approbations perdu
    $query = TransitionApproval::with(['equipment', 'submitter', 'approver'])
        ->where('type', 'parc_to_perdu')
        ->orderBy('created_at', 'desc');

    // Appliquer les filtres
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('equipment', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            })->orWhereHas('submitter', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    }

    $approvals = $query->paginate(20);

    return view('admin.perdu-list', compact('approvals'));
}

/**
 * Afficher les détails d'une approbation de perte
 */
public function showPerduApproval(TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    // Vérifier les autorisations
    $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
        || $user->email === 'superadmin@cofina.sn'
        || $approval->submitted_by === $user->id;

    if (!$canView) {
        abort(403, "Vous n'avez pas accès à cette approbation.");
    }

    // Charger les relations
    $approval->load(['equipment', 'submitter', 'approver']);
    
    // Décoder les données
    $data = json_decode($approval->data, true);
    
    // Préparer les labels pour l'affichage
    $typeLabels = [
        'vol' => 'Vol',
        'perte' => 'Perte',
        'non_localise' => 'Non localisé'
    ];
    
    $destinataireLabels = [
        'reforme' => 'Retour fournisseur',
        'destruction' => 'Destruction',
        'don' => 'Don',
        'vente' => 'Vente comme pièces'
    ];
    
    // Récupérer les données du checklist si elles existent
    $checklistData = [];
    if ($approval->checklist_data) {
        $checklistData = json_decode($approval->checklist_data, true);
    }
    
    // Vérifier si l'utilisateur peut approuver
    $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';
    
    // Préparer les données pour la vue
    $viewData = [
        'approval' => $approval,
        'data' => $data,
        'typeLabels' => $typeLabels,
        'destinataireLabels' => $destinataireLabels,
        'checklistData' => $checklistData,
        'canApprove' => $canApprove,
        'isSuperAdmin' => $role === 'super_admin' || $user->email === 'superadmin@cofina.sn',
    ];
    
    return view('admin.perdu-approval', $viewData);
}

/**
 * Soumettre une demande de mise hors service depuis le parc
 */
/**
 * Soumettre une demande de mise hors service depuis le parc
 */
public function submitParcHorsService(Request $request)
{
    DB::beginTransaction();

    try {
        Log::info('=== DÉBUT submitParcHorsService ===');
        Log::info('Données reçues:', $request->all());

        // Validation complète avec checklist optionnelle
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'transition_type' => 'required|string|in:parc_to_hors_service',
            'raison' => 'required|string|max:255',
            'destinataire' => 'required|string|max:255',
            'valeur_residuelle' => 'nullable|numeric|min:0',
            'description_incident' => 'required|string|min:10',
            'justificatif' => 'nullable|string|max:500',
            'observations' => 'nullable|string',
            'checklist.verif' => 'nullable|array',
            'checklist.verif.*' => 'nullable|boolean',
            'checklist.justificatif' => 'nullable|string',
        ]);

        Log::info('Données validées:', $validated);

        $equipment = Equipment::with(['parc'])->findOrFail($validated['equipment_id']);
        Log::info('Équipement trouvé:', [
            'id' => $equipment->id, 
            'statut' => $equipment->statut,
            'parc_relation' => $equipment->parc ? 'exists' : 'null'
        ]);

        // Vérifier que l'équipement est en parc
        if ($equipment->statut !== 'parc') {
            throw new \Exception("L'équipement n'est pas dans le parc. Statut actuel: " . $equipment->statut);
        }

        $user = auth()->user();
        $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
            || $user->email === 'superadmin@cofina.sn';

        // Récupérer les informations du parc
        $parcInfo = null;
        $utilisateurInfo = null;
        
        if ($equipment->parc && $equipment->parc->isNotEmpty()) {
            $parcInfo = $equipment->parc->first();
            
            if ($parcInfo->utilisateur_id) {
                $utilisateurInfo = User::find($parcInfo->utilisateur_id);
            }
            
            Log::info('Infos parc trouvées:', [
                'parc_id' => $parcInfo->id ?? null,
                'utilisateur_id' => $parcInfo->utilisateur_id ?? null
            ]);
        } else {
            Log::warning('Aucune information parc trouvée pour l\'équipement', ['equipment_id' => $equipment->id]);
        }

        // Préparer les données pour l'approbation avec checklist optionnelle
        $checklistData = [];
        
        // Inclure la checklist seulement si elle existe
        if (isset($validated['checklist'])) {
            $checklistData = [
                'checklist' => [
                    'verif' => $validated['checklist']['verif'] ?? [],
                    'justificatif' => $validated['checklist']['justificatif'] ?? '',
                ]
            ];
        }

        $data = array_merge([
            'transition_type' => $validated['transition_type'],
            'raison' => $validated['raison'],
            'destinataire' => $validated['destinataire'],
            'valeur_residuelle' => $validated['valeur_residuelle'] ?? null,
            'description_incident' => $validated['description_incident'],
            'justificatif' => $validated['justificatif'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'agent_nom' => $user->name,
            'agent_prenom' => $user->prenom ?? $user->name,
            'agent_fonction' => 'AGENT IT',
            'submitted_at' => now()->format('Y-m-d H:i:s'),
            'is_super_admin_submission' => $isSuperAdmin,
            'equipment_info' => [
                'id' => $equipment->id,
                'nom' => $equipment->nom,
                'numero_serie' => $equipment->numero_serie,
                'modele' => $equipment->modele,
                'type' => $equipment->type,
                'date_acquisition' => $equipment->date_acquisition,
            ],
            'parc_info' => $parcInfo ? [
                'parc_id' => $parcInfo->id,
                'utilisateur_id' => $parcInfo->utilisateur_id,
                'utilisateur_nom' => $utilisateurInfo->name ?? null,
                'utilisateur_email' => $utilisateurInfo->email ?? null,
                'departement' => $parcInfo->departement,
                'poste_affecte' => $parcInfo->poste_affecte,
                'date_affectation' => $parcInfo->date_affectation,
                'statut_usage' => $parcInfo->statut_usage,
            ] : null
        ], $checklistData);

        // Créer l'approbation
        $approval = TransitionApproval::create([
            'equipment_id' => $equipment->id,
            'from_status' => 'parc',
            'to_status' => 'hors_service',
            'submitted_by' => $user->id,
            'data' => json_encode($data),
            'status' => 'pending',
            'type' => 'parc_to_hors_service',
            'requires_super_admin_validation' => !$isSuperAdmin,
        ]);

        Log::info('Approbation créée:', ['id' => $approval->id]);

        // Si Super Admin, approuver directement
        if ($isSuperAdmin) {
            $this->processParcHorsService($equipment, $data, $approval, $parcInfo);
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            DB::commit();

            Log::info('=== FIN submitParcHorsService (approuvé) ===');

            return response()->json([
                'success' => true,
                'message' => 'Équipement mis hors service avec succès!',
                'redirect_url' => route('equipment.show', $equipment),
            ]);
        } else {
            // Notifier les Super Admins
            $this->notifySuperAdminsHorsService($approval, 'parc');
            
            DB::commit();

            Log::info('=== FIN submitParcHorsService (soumis) ===');

            return response()->json([
                'success' => true,
                'message' => 'Demande de mise hors service soumise avec succès! Attente de validation Super Admin.',
                'redirect_url' => route('transitions.approval.show', $approval),
                'approval_id' => $approval->id,
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        
        Log::error('Erreur de validation:', ['errors' => $e->errors()]);

        return response()->json([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $e->errors(),
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur submitParcHorsService: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Traiter la mise hors service depuis le parc
 */
private function processParcHorsService(Equipment $equipment, array $data, TransitionApproval $approval, $parcInfo = null)
{
    DB::beginTransaction();

    try {
        // Mettre à jour l'équipement
        $equipment->update([
            'statut' => 'hors_service',
            'date_hors_service' => now(),
            'raison_hors_service' => $data['raison'],
            'notes' => $data['observations'] ?? $equipment->notes,
        ]);

        // Créer l'entrée dans la table hors_service
        DB::table('hors_service')->insert([
            'numero_serie' => $equipment->numero_serie,
            'raison' => $data['raison'],
            'destinataire' => $data['destinataire'],
            'valeur_residuelle' => $data['valeur_residuelle'],
            'description_incident' => $data['description_incident'],
            'justificatif' => $data['justificatif'],
            'date_hors_service' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mettre à jour l'entrée du parc si elle existe
        if ($parcInfo) {
            $parcInfo->update([
                'date_sortie' => now(),
                'raison_sortie' => 'mise_hors_service',
                'statut_usage' => 'hors_service',
            ]);
            
            Log::info('Parc mis à jour pour hors service:', ['parc_id' => $parcInfo->id]);
        } else {
            Log::warning('Aucune entrée parc trouvée à mettre à jour pour l\'équipement', ['equipment_id' => $equipment->id]);
        }

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
/**
 * Approuver une demande de mise hors service depuis le parc
 */
public function approveParcHorsService(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canApprove) {
        abort(403, "Seuls les super admins peuvent approuver des transitions.");
    }

    DB::beginTransaction();

    try {
        // Validation ajustée - rendre le checklist optionnel ou avec valeurs par défaut
        $validated = $request->validate([
            'checklist' => 'nullable|array',
            'checklist.verif_diagnostic' => 'sometimes|accepted',
            'checklist.verif_etat'       => 'sometimes|accepted',
            'checklist.verif_cout'       => 'sometimes|accepted',
            'checklist.verif_travaux'    => 'sometimes|accepted',
            'validation_notes' => 'nullable|string',
        ]);

        $data = json_decode($approval->data, true);
        $equipment = $approval->equipment;

        // Traiter la mise hors service
        $this->processParcHorsService($equipment, $data, $approval);

        // Préparer les données de checklist
        $checklistData = $validated['checklist'] ?? [
            'verif_diagnostic' => false,
            'verif_etat' => false,
            'verif_cout' => false,
            'verif_travaux' => false,
        ];

        // Mettre à jour l'approbation
        $approval->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'checklist_data' => json_encode($checklistData),
            'validation_notes' => $validated['validation_notes'] ?? null,
        ]);

        DB::commit();

        // Notifier l'agent IT et l'utilisateur
        $this->notifyAgentHorsServiceApproved($approval, 'parc');

        return redirect()->route('admin.approvals')
            ->with('success', 'Mise hors service depuis le parc validée et enregistrée !');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approbation hors service (parc) erreur: ' . $e->getMessage());

        return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Notifier l'utilisateur affecté
 */
private function notifyUserHorsService(Equipment $equipment, array $data)
{
    if (isset($data['parc_info']['utilisateur_email'])) {
        $userEmail = $data['parc_info']['utilisateur_email'];
        $userName = $data['parc_info']['utilisateur_nom'];
        
        Log::info("Notification hors service à l'utilisateur: {$userEmail} - Équipement: {$equipment->numero_serie}");
        
        // Ici vous pouvez implémenter l'envoi d'email
        // Mail::to($userEmail)->send(new EquipmentHorsServiceMail($equipment, $data));
    }
}

/**
 * Notifier les Super Admins avec origine spécifique
 */
/**
 * Notifier les Super Admins pour une demande hors service
 */
private function notifySuperAdminsHorsService($approval, $origine = null)
{
    $superAdmins = User::where('role', 'super_admin')->get();
    
    // Déterminer le message en fonction de l'origine
    $originesMessages = [
        'stock' => 'depuis le stock',
        'parc' => 'depuis le parc',
        'maintenance' => 'depuis la maintenance'
    ];
    
    $origineMessage = $origine && isset($originesMessages[$origine]) 
        ? " ({$originesMessages[$origine]})" 
        : '';

    foreach ($superAdmins as $admin) {
        Log::info("Notification hors service{$origineMessage} à Super Admin: {$admin->email} - Approbation #{$approval->id}");
        // Ici vous pouvez implémenter l'envoi d'email ou notification
    }
}

/**
 * Afficher une approbation hors service depuis le stock
 */


/**
 * Afficher une approbation hors service depuis le parc
 */
public function showParcHorsServiceApproval(TransitionApproval $approval)
{
    return $this->showHorsServiceDetails($approval, 'parc');
}

/**
 * Afficher une approbation hors service depuis la maintenance
 */
public function showMaintenanceHorsServiceApproval(TransitionApproval $approval)
{
    return $this->showHorsServiceDetails($approval, 'maintenance');
}

/**
 * Méthode commune pour afficher les détails hors service
 */
private function showHorsServiceDetails(TransitionApproval $approval, $type)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
        || $user->email === 'superadmin@cofina.sn'
        || $approval->submitted_by === $user->id;

    if (!$canView) {
        abort(403, "Vous n'avez pas accès à cette approbation.");
    }

    $approval->load(['equipment', 'submitter', 'approver']);
    $data = json_decode($approval->data, true);

    // Déterminer la vue en fonction du type
    $views = [
        'stock' => 'admin.hors-service-approval',
        'parc' => 'admin.parc-hors-service-approval',
        'maintenance' => 'admin.maintenance-hors-service-approval'
    ];

    $view = $views[$type] ?? 'admin.hors-service-approval';

    return view($view, compact('approval', 'data'));
}
/**
 * Rejeter une demande de mise hors service depuis le stock
 */


/**
 * Rejeter une demande de mise hors service depuis le parc
 */
public function rejectParcHorsService(Request $request, TransitionApproval $approval)
{
    return $this->processRejection($request, $approval, 'parc');
}

/**
 * Rejeter une demande de mise hors service depuis la maintenance
 */
public function rejectMaintenanceHorsService(Request $request, TransitionApproval $approval)
{
    return $this->processRejection($request, $approval, 'maintenance');
}

/**
 * Méthode commune pour traiter les rejets
 */
private function processRejection(Request $request, TransitionApproval $approval, $type = 'stock')
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canReject = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canReject) {
        abort(403, "Seuls les super admins peuvent rejeter des transitions.");
    }

    $validated = $request->validate([
        'raison_rejet' => 'required|string|max:500',
    ]);

    $approval->update([
        'status' => 'rejected',
        'rejected_by' => $user->id,
        'rejected_at' => now(),
        'rejection_reason' => $validated['raison_rejet'],
    ]);

    $this->notifyAgentRejection($approval, $type);

    return redirect()->route('admin.hors-service-approvals.list')
        ->with('info', "Demande de mise hors service ({$type}) rejetée.");
}

/**
 * Notifier l'agent du rejet
 */
private function notifyAgentRejection($approval, $type)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification rejet hors service ({$type}) à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Soumettre une demande de retour au stock depuis la maintenance
 */
public function submitMaintenanceToStock(Request $request)
{
    DB::beginTransaction();

    try {
        // Log des données reçues pour débogage
        Log::info('Données reçues pour maintenance→stock:', $request->all());

        // Validation complète
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'transition_type' => 'required|string|in:maintenance_to_stock',
            'etat_retour' => 'required|string|in:bon,reparable,irreparable',
            'cout' => 'nullable|numeric|min:0',
            'origine' => 'required|string|max:255',
            'date_retour' => 'required|date',
            'diagnostic' => 'required|string|min:10',
            'travaux_realises' => 'nullable|string',
            'raison_retour' => 'required|string|min:10',
            'valeur_residuelle' => 'nullable|numeric|min:0',
            'observations_retour' => 'nullable|string',
        ], [
            'equipment_id.required' => 'L\'ID de l\'équipement est requis',
            'equipment_id.exists' => 'Cet équipement n\'existe pas',
            'etat_retour.required' => 'L\'état de retour est obligatoire',
            'etat_retour.in' => 'État de retour invalide',
            'date_retour.required' => 'La date de retour est obligatoire',
            'date_retour.date' => 'Date de retour invalide',
            'diagnostic.required' => 'Le diagnostic est obligatoire',
            'diagnostic.min' => 'Le diagnostic doit contenir au moins 10 caractères',
            'raison_retour.required' => 'La raison du retour est obligatoire',
            'raison_retour.min' => 'La raison doit contenir au moins 10 caractères',
            'cout.numeric' => 'Le coût doit être un nombre',
            'cout.min' => 'Le coût ne peut pas être négatif',
            'valeur_residuelle.numeric' => 'La valeur résiduelle doit être un nombre',
            'valeur_residuelle.min' => 'La valeur résiduelle ne peut pas être négative',
        ]);

        // Nettoyer les données
        $validated = array_map('trim', $validated);
        
        // Convertir valeurs numériques en null si vides
        if (isset($validated['cout']) && empty($validated['cout'])) {
            $validated['cout'] = null;
        }
        if (isset($validated['valeur_residuelle']) && empty($validated['valeur_residuelle'])) {
            $validated['valeur_residuelle'] = null;
        }

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $user = auth()->user();
        $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
            || $user->email === 'superadmin@cofina.sn';

        // Vérifier que l'équipement est en maintenance
        if ($equipment->statut !== 'maintenance') {
            throw new \Exception("L'équipement n'est pas en maintenance. Statut actuel: " . $equipment->statut);
        }

        // Récupérer l'entrée maintenance si elle existe
        $maintenance = $equipment->maintenance()->latest()->first();
        $stock = $equipment->stock;

        // Préparer les données pour l'approbation
        $data = [
            'transition_type' => $validated['transition_type'],
            'etat_retour' => $validated['etat_retour'],
            'cout' => $validated['cout'],
            'origine' => $validated['origine'],
            'date_retour' => $validated['date_retour'],
            'diagnostic' => $validated['diagnostic'],
            'travaux_realises' => $validated['travaux_realises'] ?? null,
            'raison_retour' => $validated['raison_retour'],
            'valeur_residuelle' => $validated['valeur_residuelle'],
            'observations_retour' => $validated['observations_retour'] ?? null,
            'agent_nom' => $user->name,
            'agent_prenom' => $user->prenom ?? $user->name,
            'agent_fonction' => 'AGENT IT',
            'submitted_at' => now()->format('Y-m-d H:i:s'),
            'is_super_admin_submission' => $isSuperAdmin,
            'equipment_info' => [
                'id' => $equipment->id,
                'nom' => $equipment->nom,
                'numero_serie' => $equipment->numero_serie,
                'modele' => $equipment->modele,
                'type' => $equipment->type,
                'date_acquisition' => $equipment->date_acquisition,
            ],
            'maintenance_info' => $maintenance ? [
                'id' => $maintenance->id,
                'date_entree' => $maintenance->date_entree,
                'raison_entree' => $maintenance->raison_entree,
                'description_panne' => $maintenance->description_panne,
            ] : null
        ];

        // Créer l'approbation
        $approval = TransitionApproval::create([
            'equipment_id' => $equipment->id,
            'from_status' => 'maintenance',
            'to_status' => 'stock',
            'submitted_by' => $user->id,
            'data' => json_encode($data),
            'status' => 'pending',
            'type' => 'maintenance_to_stock',
            'requires_super_admin_validation' => !$isSuperAdmin,
        ]);

        Log::info('Approbation maintenance→stock créée:', ['approval_id' => $approval->id]);

        // Si Super Admin, approuver directement
        if ($isSuperAdmin) {
            $this->processMaintenanceToStock($equipment, $data, $approval, $maintenance, $stock);
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            DB::commit();

            Log::info('Maintenance→stock approuvé directement par Super Admin');

            return response()->json([
                'success' => true,
                'message' => 'Équipement retourné au stock avec succès!',
                'redirect_url' => route('equipment.show', $equipment),
            ]);
        } else {
            // Notifier les Super Admins
            $this->notifySuperAdminsMaintenanceToStock($approval);
            
            DB::commit();

            Log::info('Demande maintenance→stock soumise pour approbation');

            return response()->json([
                'success' => true,
                'message' => 'Demande de retour au stock soumise avec succès! Attente de validation Super Admin.',
                'redirect_url' => route('transitions.approval.show', $approval),
                'approval_id' => $approval->id,
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        
        Log::error('Erreur de validation maintenance→stock:', [
            'errors' => $e->errors(),
            'data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $e->errors(),
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur submit maintenance→stock: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Traiter le retour au stock depuis la maintenance
 */
private function processMaintenanceToStock(Equipment $equipment, array $data, TransitionApproval $approval, $maintenance = null, $stock = null)
{
    DB::beginTransaction();

    try {
        // Mettre à jour l'équipement
        $equipment->update([
            'statut' => 'stock',
            'date_retour_maintenance' => $data['date_retour'],
            'notes' => $data['observations_retour'] ?? $equipment->notes,
            'etat_actuel' => $data['etat_retour'],
        ]);

        // Si l'entrée stock existe, la mettre à jour
        if ($stock) {
            $stock->update([
                'date_retour' => $data['date_retour'],
                'etat' => 'disponible',
                'type_stock' => 'deceler',
                'localisation_physique' => 'localIt', // Valeur par défaut
            ]);
        } else {
            // Sinon créer une nouvelle entrée stock
            $stock = stock::create([
                'equipment_id' => $equipment->id,
                'numero_serie' => $equipment->numero_serie,
                'type_stock' => 'deceler',
                'localisation_physique' => 'localIt', // Valeur par défaut
                'date_entree' => $data['date_retour'],
                'date_sortie' => null,
                'etat' => 'disponible',
                'quantite' => 1,
                'observations' => 'Retour de maintenance: ' . ($data['diagnostic'] ?? 'Maintenance terminée'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Créer l'entrée dans la table deceler (si cette table existe)
        if (Schema::hasTable('deceler')) {
            $deceler = deceler::create([
                'stock_id' => $stock->id,
                'origine' => $data['origine'] ?? 'maintenance',
                'numero_serie_origine' => $equipment->numero_serie,
                'date_retour' => $data['date_retour'],
                'raison_retour' => $data['raison_retour'] ?? 'Retour maintenance',
                'diagnostic' => $data['diagnostic'] ?? 'Maintenance terminée',
                'etat_retour' => $data['etat_retour'] ?? 'bon',
                'valeur_residuelle' => $data['valeur_residuelle'] ?? null,
                'observations_retour' => $data['observations_retour'] ?? null,
                'cout_reparation' => $data['cout'] ?? null,
                'travaux_realises' => $data['travaux_realises'] ?? null,
                
                
            ]);
        } else {
            Log::info('Table deceler non trouvée, création stock uniquement', [
                'equipment_id' => $equipment->id,
                'stock_id' => $stock->id,
            ]);
        }

        // Mettre à jour la maintenance si elle existe
        if ($maintenance) {
            $maintenance->update([
                'date_sortie' => $data['date_retour'],
                'etat_sortie' => 'retour_stock',
                'description_travaux' => $data['travaux_realises'] ?? null,
                'cout_reparation' => $data['cout'] ?? null,
                'diagnostic_final' => $data['diagnostic'] ?? null,
                'transition_approval_id' => $approval->id,
            ]);
        }

        DB::commit();

        Log::info('Processus maintenance→stock terminé avec succès', [
            'equipment_id' => $equipment->id,
            'stock_id' => $stock->id,
            'type_stock' => 'deceler',
            'etat' => 'disponible',
            'localisation' => 'localIt',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur dans processMaintenanceToStock: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Approuver une demande de retour au stock
 */
public function approveMaintenanceToStock(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canApprove) {
        abort(403, "Seuls les super admins peuvent approuver des transitions.");
    }

    DB::beginTransaction();

    try {
       $validated = $request->validate([
    'checklist' => 'required|array',

    'checklist.verif_diagnostic' => 'sometimes|accepted',
    'checklist.verif_etat'       => 'sometimes|accepted',
    'checklist.verif_cout'       => 'sometimes|accepted',
    'checklist.verif_travaux'    => 'sometimes|accepted',

    'validation_notes' => 'nullable|string',
]);


        $data = json_decode($approval->data, true);
        $equipment = $approval->equipment;
        
        // Récupérer l'entrée maintenance et stock
        $maintenance = $equipment->maintenance()->latest()->first();
        $stock = $equipment->stock;

        // Traiter le retour au stock
        $this->processMaintenanceToStock($equipment, $data, $approval, $maintenance, $stock);

        // Mettre à jour l'approbation
        $approval->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'checklist_data' => json_encode($validated['checklist']),
            'validation_notes' => $validated['validation_notes'] ?? null,
        ]);

        DB::commit();

        // Notifier l'agent IT
        $this->notifyAgentMaintenanceToStockApproved($approval);

        return redirect()->route('admin.maintenance-to-stock-approvals.list')
            ->with('success', 'Retour au stock validé et enregistré !');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approbation maintenance→stock erreur: ' . $e->getMessage());

        return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Rejeter une demande de retour au stock
 */
public function rejectMaintenanceToStock(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canReject = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canReject) {
        abort(403, "Seuls les super admins peuvent rejeter des transitions.");
    }

    $validated = $request->validate([
        'raison_rejet' => 'required|string|max:500',
    ]);

    $approval->update([
        'status' => 'rejected',
        'rejected_by' => $user->id,
        'rejected_at' => now(),
        'rejection_reason' => $validated['raison_rejet'],
    ]);

    $this->notifyAgentMaintenanceToStockRejected($approval);

    return redirect()->route('admin.maintenance-to-stock-approvals.list')
        ->with('info', 'Demande de retour au stock rejetée.');
}

/**
 * Notifier les Super Admins pour une demande maintenance→stock
 */
private function notifySuperAdminsMaintenanceToStock($approval)
{
    $superAdmins = User::where('role', 'super_admin')->get();

    foreach ($superAdmins as $admin) {
        Log::info("Notification maintenance→stock à Super Admin: {$admin->email} - Approbation #{$approval->id}");
        // Ici vous pouvez implémenter l'envoi d'email ou notification
    }
}

/**
 * Notifier l'agent que sa demande maintenance→stock est approuvée
 */
private function notifyAgentMaintenanceToStockApproved($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification approbation maintenance→stock à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Notifier l'agent que sa demande maintenance→stock est rejetée
 */
private function notifyAgentMaintenanceToStockRejected($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification rejet maintenance→stock à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Lister les approbations de maintenance vers stock
 */
public function listMaintenanceToStockApprovals(Request $request)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    // Vérifier les autorisations
    $isAuthorized = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$isAuthorized) {
        abort(403, "Accès réservé aux administrateurs.");
    }

    // Filtrer seulement les approbations maintenance→stock
    $query = TransitionApproval::with(['equipment', 'submitter', 'approver'])
        ->where('type', 'maintenance_to_stock')
        ->orderBy('created_at', 'desc');

    // Appliquer les filtres
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('equipment', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            })->orWhereHas('submitter', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    }

    $approvals = $query->paginate(20);

    return view('admin.maintenance-to-stock-list', compact('approvals'));
}
/**
 * Afficher les détails d'une approbation maintenance → stock
 */
public function showApprovalMaintenanceToStock(TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canView = in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
        || $user->email === 'superadmin@cofina.sn'
        || $approval->submitted_by === $user->id;

    if (!$canView) {
        abort(403, "Vous n'avez pas accès à cette approbation.");
    }

    $approval->load(['equipment', 'submitter', 'approver', 'equipment.stock', 'equipment.maintenance']);
    $data = json_decode($approval->data, true);

    // Récupérer les informations spécifiques
    $stock = $approval->equipment->stock;
    $maintenance = $approval->equipment->maintenance()->latest()->first();
    
    // Récupérer l'entrée deceler si elle existe
    $deceler = null;
    if ($approval->status === 'approved' && $stock) {
        $deceler = Deceler::where('stock_id', $stock->id)
            ->where('transition_approval_id', $approval->id)
            ->first();
    }

    // Récupérer les données de checklist si elles existent
    $checklistData = [];
    if (!empty($approval->checklist_data)) {
        $checklistData = json_decode($approval->checklist_data, true);
    }

    // Déterminer les couleurs pour l'état
    $etatColors = [
        'bon' => [
            'bg' => 'bg-green-100',
            'text' => 'text-green-800',
            'border' => 'border-green-200',
            'icon' => '✅'
        ],
        'reparable' => [
            'bg' => 'bg-yellow-100',
            'text' => 'text-yellow-800',
            'border' => 'border-yellow-200',
            'icon' => '⚠️'
        ],
        'irreparable' => [
            'bg' => 'bg-red-100',
            'text' => 'text-red-800',
            'border' => 'border-red-200',
            'icon' => '❌'
        ]
    ];

    $etatInfo = $etatColors[$data['etat_retour'] ?? 'bon'] ?? $etatColors['bon'];

    // Préparer les données pour la vue
    $viewData = [
        'approval' => $approval,
        'data' => $data,
        'stock' => $stock,
        'maintenance' => $maintenance,
        'deceler' => $deceler,
        'checklistData' => $checklistData,
        'etatInfo' => $etatInfo,
        'user' => $user,
        
        // Traductions pour l'affichage
        'etatLabels' => [
            'bon' => 'Bon (réparé)',
            'reparable' => 'Réparable (besoin travaux)',
            'irreparable' => 'Irréparable'
        ],
        'origineLabels' => [
            'Maintenance' => 'Retour de maintenance',
            'Parc' => 'Retour du parc',
            'Autre' => 'Autre origine'
        ]
    ];

    return view('admin.maintenance-to-stock-approval', $viewData);
}
/**
 * Obtenir le label d'une checklist
 */
private function getChecklistLabel($key)
{
    $labels = [
        'verif_diagnostic' => 'Diagnostic vérifié',
        'verif_travaux' => 'Travaux vérifiés',
        'verif_cout' => 'Coût vérifié',
        'verif_valeur_residuelle' => 'Valeur résiduelle vérifiée',
        'verif_documentation' => 'Documentation vérifiée',
        'verif_etat' => 'État vérifié',
    ];
    
    return $labels[$key] ?? str_replace(['verif_', '_'], ['', ' '], $key);
}

/**
 * Soumettre une demande de mise hors service depuis la maintenance
 */
public function submitMaintenanceHorsService(Request $request)
{
    DB::beginTransaction();

    try {
        // Log des données reçues pour débogage
        Log::info('Données reçues pour maintenance hors service:', $request->all());

        // Validation complète
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'transition_type' => 'required|string|in:maintenance_to_hors_service',
            'raison' => 'required|string|max:255',
            'destinataire' => 'required|string|max:255',
            'cout_diagnostic' => 'nullable|numeric|min:0',
            'valeur_residuelle' => 'nullable|numeric|min:0',
            'diagnostic_detaille' => 'required|string|min:20',
            'recommandation' => 'nullable|string',
            'justificatif' => 'nullable|string|max:500',
            'technicien_nom' => 'required|string|max:255',
            'date_diagnostic' => 'required|date',
        ], [
            'equipment_id.required' => 'L\'ID de l\'équipement est requis',
            'equipment_id.exists' => 'Cet équipement n\'existe pas',
            'transition_type.required' => 'Le type de transition est requis',
            'raison.required' => 'La raison est obligatoire',
            'destinataire.required' => 'Le destinataire est obligatoire',
            'cout_diagnostic.numeric' => 'Le coût diagnostic doit être un nombre',
            'valeur_residuelle.numeric' => 'La valeur résiduelle doit être un nombre',
            'diagnostic_detaille.required' => 'Le diagnostic détaillé est obligatoire',
            'diagnostic_detaille.min' => 'Le diagnostic doit contenir au moins 20 caractères',
            'technicien_nom.required' => 'Le nom du technicien est obligatoire',
            'date_diagnostic.required' => 'La date du diagnostic est obligatoire',
        ]);

        // Nettoyer les données
        $validated = array_map('trim', $validated);
        
        // Convertir valeurs numériques en null si vide
        $numericFields = ['cout_diagnostic', 'valeur_residuelle'];
        foreach ($numericFields as $field) {
            if (isset($validated[$field]) && empty($validated[$field])) {
                $validated[$field] = null;
            }
        }

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $user = auth()->user();
        $isSuperAdmin = strtolower(trim((string) ($user->role ?? ''))) === 'super_admin'
            || $user->email === 'superadmin@cofina.sn';

        // Vérifier que l'équipement est en maintenance
        if ($equipment->statut !== 'maintenance') {
            throw new \Exception("L'équipement n'est pas en maintenance. Statut actuel: " . $equipment->statut);
        }

        // Récupérer les infos de maintenance
        $maintenance = $equipment->maintenance()->latest()->first();

        // Préparer les données pour l'approbation
        $data = [
            'transition_type' => $validated['transition_type'],
            'raison' => $validated['raison'],
            'destinataire' => $validated['destinataire'],
            'cout_diagnostic' => $validated['cout_diagnostic'],
            'valeur_residuelle' => $validated['valeur_residuelle'],
            'diagnostic_detaille' => $validated['diagnostic_detaille'],
            'recommandation' => $validated['recommandation'] ?? null,
            'justificatif' => $validated['justificatif'] ?? null,
            'technicien_nom' => $validated['technicien_nom'],
            'date_diagnostic' => $validated['date_diagnostic'],
            'agent_nom' => $user->name,
            'agent_prenom' => $user->prenom ?? $user->name,
            'agent_fonction' => 'AGENT IT',
            'submitted_at' => now()->format('Y-m-d H:i:s'),
            'is_super_admin_submission' => $isSuperAdmin,
            'maintenance_info' => $maintenance ? [
                'id' => $maintenance->id,
                'date_entree' => $maintenance->date_entree,
                'probleme_constate' => $maintenance->probleme_constate,
                'type_intervention' => $maintenance->type_intervention,
            ] : null,
            'equipment_info' => [
                'id' => $equipment->id,
                'nom' => $equipment->nom,
                'numero_serie' => $equipment->numero_serie,
                'modele' => $equipment->modele,
                'type' => $equipment->type,
                'date_acquisition' => $equipment->date_acquisition,
                'valeur_initiale' => $equipment->valeur_initiale,
            ]
        ];

        // Créer l'approbation
        $approval = TransitionApproval::create([
            'equipment_id' => $equipment->id,
            'from_status' => 'maintenance',
            'to_status' => 'hors_service',
            'submitted_by' => $user->id,
            'data' => json_encode($data),
            'status' => 'pending',
            'type' => 'maintenance_to_hors_service',
            'requires_super_admin_validation' => !$isSuperAdmin,
        ]);

        Log::info('Approbation maintenance hors service créée:', ['approval_id' => $approval->id]);

        // Si Super Admin, approuver directement
        if ($isSuperAdmin) {
            $this->processMaintenanceHorsService($equipment, $data, $approval, $maintenance);
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            DB::commit();

            Log::info('Maintenance hors service approuvée directement par Super Admin');

            return response()->json([
                'success' => true,
                'message' => 'Équipement déclaré irréparable et mis hors service avec succès!',
                'redirect_url' => route('equipment.show', $equipment),
            ]);
        } else {
            // Notifier les Super Admins
            $this->notifySuperAdminsMaintenanceHorsService($approval);
            
            DB::commit();

            Log::info('Demande maintenance hors service soumise pour approbation');

            return response()->json([
                'success' => true,
                'message' => 'Demande de mise hors service soumise avec succès! Attente de validation Super Admin.',
                'redirect_url' => route('transitions.approval.show', $approval),
                'approval_id' => $approval->id,
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        
        Log::error('Erreur de validation maintenance hors service:', [
            'errors' => $e->errors(),
            'data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $e->errors(),
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur submit maintenance hors service: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Traiter la mise hors service depuis la maintenance
 */
private function processMaintenanceHorsService(Equipment $equipment, array $data, TransitionApproval $approval, $maintenance = null)
{
    DB::beginTransaction();

    try {
        // Mettre à jour l'équipement
        $equipment->update([
            'statut' => 'hors_service',
            'date_hors_service' => now(),
            'raison_hors_service' => $data['raison'] . ' (irréparable en maintenance)',
            'notes' => "Diagnostic: " . $data['diagnostic_detaille'] . 
                      "\nRecommandation: " . ($data['recommandation'] ?? 'Aucune') . 
                      "\nTechnicien: " . $data['technicien_nom'],
        ]);

        // Créer l'entrée dans la table hors_service
        DB::table('hors_service')->insert([
            'equipment_id' => $equipment->id,
            'numero_serie' => $equipment->numero_serie,
            'raison' => $data['raison'],
            'destinataire' => $data['destinataire'],
            'cout_diagnostic' => $data['cout_diagnostic'],
            'valeur_residuelle' => $data['valeur_residuelle'],
            'description_incident' => $data['diagnostic_detaille'],
            'justificatif' => $data['justificatif'],
            'recommandation' => $data['recommandation'],
            'technicien_nom' => $data['technicien_nom'],
            'date_diagnostic' => $data['date_diagnostic'],
            'date_hors_service' => now(),
            'agent_id' => auth()->id(),
            'maintenance_id' => $maintenance->id ?? null,
            'transition_approval_id' => $approval->id,
            'source' => 'maintenance',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mettre à jour la maintenance
        if ($maintenance) {
            $maintenance->update([
                'statut' => 'termine_hors_service',
                'date_sortie' => now(),
                'observations' => "Équipement déclaré irréparable. " . 
                                "Raison: " . $data['raison'] . 
                                "\n" . ($data['recommandation'] ?? ''),
            ]);
        }

        DB::commit();
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

/**
 * Approuver une demande de mise hors service depuis la maintenance
 */
public function approveMaintenanceHorsService(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canApprove = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canApprove) {
        abort(403, "Seuls les super admins peuvent approuver des transitions.");
    }

    DB::beginTransaction();

    try {
        $validated = $request->validate([
            'checklist' => 'required|array',
            'checklist.*' => 'boolean',
            'checklist.verif_diagnostic' => 'required|accepted',
            'checklist.verif_cout_justifie' => 'required|accepted',
            'checklist.verif_recommandation' => 'required|accepted',
            'validation_notes' => 'nullable|string',
        ]);

        $data = json_decode($approval->data, true);
        $equipment = $approval->equipment;
        
        // Récupérer la maintenance associée
        $maintenance = $equipment->maintenance()->latest()->first();

        // Traiter la mise hors service
        $this->processMaintenanceHorsService($equipment, $data, $approval, $maintenance);

        // Mettre à jour l'approbation
        $approval->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'checklist_data' => json_encode($validated['checklist']),
            'validation_notes' => $validated['validation_notes'] ?? null,
        ]);

        DB::commit();

        // Notifier l'agent IT
        $this->notifyAgentMaintenanceHorsServiceApproved($approval);

        return redirect()->route('admin.maintenance-hors-service-approvals.list')
            ->with('success', 'Mise hors service depuis maintenance validée et enregistrée !');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approbation maintenance hors service erreur: ' . $e->getMessage());

        return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Rejeter une demande de mise hors service depuis la maintenance
 */
public function rejectMaintenanceToHorsService(Request $request, TransitionApproval $approval)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    $canReject = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$canReject) {
        abort(403, "Seuls les super admins peuvent rejeter des transitions.");
    }

    $validated = $request->validate([
        'raison_rejet' => 'required|string|max:500',
    ]);

    $approval->update([
        'status' => 'rejected',
        'rejected_by' => $user->id,
        'rejected_at' => now(),
        'rejection_reason' => $validated['raison_rejet'],
    ]);

    $this->notifyAgentMaintenanceHorsServiceRejected($approval);

    return redirect()->route('admin.maintenance-hors-service-approvals.list')
        ->with('info', 'Demande de mise hors service depuis maintenance rejetée.');
}

/**
 * Notifier les Super Admins pour une demande hors service depuis maintenance
 */
private function notifySuperAdminsMaintenanceHorsService($approval)
{
    $superAdmins = User::where('role', 'super_admin')->get();

    foreach ($superAdmins as $admin) {
        Log::info("Notification maintenance hors service à Super Admin: {$admin->email} - Approbation #{$approval->id}");
        // Ici vous pouvez implémenter l'envoi d'email ou notification
    }
}

/**
 * Notifier l'agent que sa demande hors service depuis maintenance est approuvée
 */
private function notifyAgentMaintenanceHorsServiceApproved($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification approbation maintenance hors service à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Notifier l'agent que sa demande hors service depuis maintenance est rejetée
 */
private function notifyAgentMaintenanceHorsServiceRejected($approval)
{
    $agent = User::find($approval->submitted_by);
    
    if ($agent) {
        Log::info("Notification rejet maintenance hors service à: {$agent->email} - Approbation #{$approval->id}");
        // Envoyer une notification ou email
    }
}

/**
 * Lister les approbations de mise hors service depuis maintenance
 */
public function listMaintenanceHorsServiceApprovals(Request $request)
{
    $user = auth()->user();
    $role = strtolower(trim((string) ($user->role ?? '')));

    // Vérifier les autorisations
    $isAuthorized = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
        || $user->email === 'superadmin@cofina.sn';

    if (!$isAuthorized) {
        abort(403, "Accès réservé aux administrateurs.");
    }

    // Filtrer seulement les approbations maintenance hors service
    $query = TransitionApproval::with(['equipment', 'submitter', 'approver'])
        ->where('type', 'maintenance_to_hors_service')
        ->orderBy('created_at', 'desc');

    // Appliquer les filtres
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('equipment', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            })->orWhereHas('submitter', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    }

    $approvals = $query->paginate(20);

    return view('admin.maintenance-hors-service-list', compact('approvals'));
}


}
