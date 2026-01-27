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

        return view('admin.approval-details', compact('approval', 'data'));
    }

    /**
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
        'prenom' => $mouvementData['receptionnaire_prenom'] ?? '',
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
        'prenom' => '',
        'fonction' => $affectationData['position'] ?? $data['poste_affecte'] ?? '',
        'departement' => $affectationData['department'] ?? $data['departement'] ?? '',
    ];

    // ==================================================
    // ✅ USER INFO POUR LA VUE (UTILISATEUR FINAL)
    // ==================================================
    $userInfo = [
        'user_name' => $utilisateurData['nom'],
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
    private function extractUserName($affectationData)
    {
        if (isset($affectationData['user_id'])) {
            $user = User::find($affectationData['user_id']);
            if ($user) {
                return $user->name;
            }
        }

        return $affectationData['responsable_name'] ?? auth()->user()->name;
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
                    'user_email' => $userObj->email,
                    'departement' => $affectationData['department'] ?? $affectationData['departement'] ?? null,
                    'poste_affecte' => $affectationData['position'] ?? $affectationData['poste_affecte'] ?? null,
                ];
            }
        }

        if (empty($userInfo)) {
            $userInfo = [
                'user_name' => $data['user_name'] ?? null,
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
            'utilisateur_prenom' => $userInfo['user_name'] ?? '',
            'utilisateur_fonction' => $userInfo['poste_affecte'] ?? '',
            
            // Données générales
            'agence_nom' => $agenceNom ?? $data['agence_nom'] ?? $data['departement'] ?? '',
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
        // Récupérer l'approbation avec gestion d'erreur
        $approval = TransitionApproval::find($id);
        
        if (!$approval) {
            Log::warning('TransitionApproval introuvable', [
                'approval_id' => $id,
                'requested_by' => auth()->user()->id ?? 'guest'
            ]);
            
            return redirect()->back()
                ->with('error', 'L\'approbation de transition demandée (ID: ' . $id . ') n\'existe pas ou a été supprimée.');
        }
        
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

}
