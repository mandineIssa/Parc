<?php
// app/Http/Controllers/ChangeTicketController.php

namespace App\Http\Controllers;

use App\Models\ChangeTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChangeTicketController extends Controller
{
    // Constants
    const STATUS_LABEL = [
        'DRAFT' => 'Brouillon',
        'PENDING_N2' => 'En attente N+2',
        'REJECTED' => 'Rejeté',
        'VALIDATED_N2' => 'Validé N+2 – En attente envoi N+3',
        'PENDING_N3' => 'En attente N+3',
        'CLOSED' => 'Clôturé',
    ];

    const ENV_OPTS = ["Flexcube", "Report", "OBBRN", "Autre"];
    const TYPE_OPTS = ["Standard", "Normal", "Urgent"];
    const IMPACT_LVLS = ["Faible", "Moyen", "Élevé"];

   /*  public function __construct()
    {
        $this->middleware('auth');
    } */

    // ==================== Redirection automatique selon le rôle ====================
    
    /**
     * Redirige automatiquement vers la page appropriée selon le rôle_change de l'utilisateur
     */
    public function redirectToRolePage()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a un rôle Change Management
        if ($user->role_change) {
            // Sauvegarder le rôle en session pour compatibilité avec le code existant
            session(['change_role' => $user->role_change]);
            
            // Rediriger vers la page appropriée
            return match($user->role_change) {
                'N1' => redirect()->route('change.n1.index'),
                'N2' => redirect()->route('change.n2.index'),
                'N3' => redirect()->route('change.n3.index'),
                default => redirect()->route('change.role')->with('error', 'Rôle non reconnu'),
            };
        }
        
        // Si pas de rôle Change Management, rediriger vers la page de sélection
        return redirect()->route('change.role');
    }

    // ==================== Role Selection (gardé pour compatibilité) ====================
    public function selectRole()
    {
        $user = Auth::user();
        
        // Si l'utilisateur a déjà un rôle, on le redirige automatiquement
        if ($user->role_change) {
            return $this->redirectToRolePage();
        }
        
        return view('change.role-selector');
    }

    public function setRole(Request $request)
    {
        $request->validate(['role' => 'required|in:N1,N2,N3']);
        
        // Sauvegarder le rôle dans la session et dans la base de données
        session(['change_role' => $request->role]);
        
        $user = Auth::user();
        $user->role_change = $request->role;
        $user->save();
        
        return redirect()->route('change.dashboard');
    }

    public function clearRole()
    {
        // Supprimer le rôle de la session mais pas de la base de données
        session()->forget('change_role');
        
        return redirect()->route('change.role');
    }

    public function dashboard()
    {
        $role = session('change_role');
        
        if (!$role) {
            return redirect()->route('change.role');
        }

        return match($role) {
            'N1' => redirect()->route('change.n1.index'),
            'N2' => redirect()->route('change.n2.index'),
            'N3' => redirect()->route('change.n3.index'),
        };
    }

    // ==================== N+1 Functions ====================
    
    /**
     * Afficher la liste des formulaires pour N+1 avec pagination
     */
    public function n1Index()
    {
        $this->authorizeRole('N1');
        
        $tickets = ChangeTicket::where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Pagination à 10 éléments par page
        
        $pendingCount = ChangeTicket::where('status', 'PENDING_N2')->count();
        
        // S'assurer que la session est synchronisée avec le rôle de l'utilisateur
        if (Auth::user()->role_change === 'N1') {
            session(['change_role' => 'N1']);
        }
        
        return view('change.n1.index', compact('tickets', 'pendingCount'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function n1Create()
    {
        $this->authorizeRole('N1');
        
        $tickets = ChangeTicket::where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $envOpts = self::ENV_OPTS;
        $typeOpts = self::TYPE_OPTS;
        $impactLvls = self::IMPACT_LVLS;
        $pendingCount = ChangeTicket::where('status', 'PENDING_N2')->count();
        
        return view('change.n1.form', compact('envOpts', 'typeOpts', 'impactLvls', 'tickets', 'pendingCount'));
    }

    /**
     * Enregistrer un nouveau formulaire
     */
    public function n1Store(Request $request)
    {
        $this->authorizeRole('N1');
        
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|in:Standard,Normal,Urgent',
            'ticket_number' => 'nullable|string|max:50',
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'departement' => 'required|string|max:100',
            'environnement' => 'required|in:Flexcube,Report,OBBRN,Autre',
            'problematique' => 'required|string',
            'date_execution' => 'nullable|date',
            'impact_ops' => 'nullable|in:Faible,Moyen,Élevé',
            'impact_users' => 'nullable|in:Faible,Moyen,Élevé',
            'impact_prod' => 'nullable|in:Faible,Moyen,Élevé',
            'risques' => 'nullable|string',
            'rollback' => 'nullable|string',
        ]);

        $data['status'] = 'DRAFT';
        $data['created_by'] = Auth::id();
        $data['history'] = [[
            'role' => 'N1',
            'action' => 'Création du formulaire',
            'at' => now()->format('d/m/Y H:i:s')
        ]];

        if ($request->hasFile('files')) {
            $data['files'] = $this->uploadFiles($request->file('files'));
        }

        $ticket = ChangeTicket::create($data);

        return redirect()->route('change.n1.edit', $ticket)
            ->with('success', 'Formulaire créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function n1Edit(ChangeTicket $ticket)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($ticket);
        
        $tickets = ChangeTicket::where('created_by', Auth::id())
            ->where('id', '!=', $ticket->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $envOpts = self::ENV_OPTS;
        $typeOpts = self::TYPE_OPTS;
        $impactLvls = self::IMPACT_LVLS;
        $pendingCount = ChangeTicket::where('status', 'PENDING_N2')->count();
        
        return view('change.n1.form', compact('ticket', 'envOpts', 'typeOpts', 'impactLvls', 'tickets', 'pendingCount'));
    }

    /**
     * Mettre à jour un formulaire
     */
    public function n1Update(Request $request, ChangeTicket $ticket)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($ticket);
        
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|in:Standard,Normal,Urgent',
            'ticket_number' => 'nullable|string|max:50',
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'departement' => 'required|string|max:100',
            'environnement' => 'required|in:Flexcube,Report,OBBRN,Autre',
            'problematique' => 'required|string',
            'date_execution' => 'nullable|date',
            'impact_ops' => 'nullable|in:Faible,Moyen,Élevé',
            'impact_users' => 'nullable|in:Faible,Moyen,Élevé',
            'impact_prod' => 'nullable|in:Faible,Moyen,Élevé',
            'risques' => 'nullable|string',
            'rollback' => 'nullable|string',
        ]);

        $data['updated_by'] = Auth::id();
        $ticket->update($data);

        if ($request->hasFile('files')) {
            $files = $this->uploadFiles($request->file('files'));
            $ticket->files = array_merge($ticket->files ?? [], $files);
            $ticket->save();
        }

        return back()->with('success', 'Formulaire mis à jour.');
    }

    /**
     * Soumettre le formulaire à N+2
     */
    public function n1SubmitToN2(Request $request, ChangeTicket $ticket)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($ticket);
        
        $ticket->status = 'PENDING_N2';
        $ticket->history = array_merge($ticket->history ?? [], [[
            'role' => 'N1',
            'action' => 'Formulaire soumis à N+2',
            'note' => $request->note,
            'at' => now()->format('d/m/Y H:i:s')
        ]]);
        $ticket->updated_by = Auth::id();
        $ticket->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Formulaire transmis à N+2']);
        }
        
        return redirect()->route('change.n1.index')
            ->with('success', 'Formulaire transmis à N+2 avec succès.');
    }

    /**
     * Transmettre le formulaire à N+3
     */
    public function n1SubmitToN3(Request $request, ChangeTicket $ticket)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($ticket);
        
        $ticket->status = 'PENDING_N3';
        $ticket->history = array_merge($ticket->history ?? [], [[
            'role' => 'N1',
            'action' => 'Formulaire transmis à N+3 pour validation finale',
            'at' => now()->format('d/m/Y H:i:s')
        ]]);
        $ticket->updated_by = Auth::id();
        $ticket->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Formulaire transmis à N+3']);
        }
        
        return redirect()->route('change.n1.index')
            ->with('success', 'Formulaire transmis à N+3 pour clôture.');
    }

    // ==================== N+2 Functions ====================

    /**
     * Afficher la liste des formulaires pour N+2 avec pagination et filtres
     */
    public function n2Index(Request $request)
    {
        $this->authorizeRole('N2');
        
        // S'assurer que la session est synchronisée avec le rôle de l'utilisateur
        if (Auth::user()->role_change === 'N2') {
            session(['change_role' => 'N2']);
        }
        
        // Construction de la requête de base
        $query = ChangeTicket::whereIn('status', ['PENDING_N2', 'VALIDATED_N2', 'PENDING_N3', 'CLOSED', 'REJECTED'])
            ->orderBy('created_at', 'desc');
        
        // Appliquer le filtre si présent
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'pending':
                    $query->where('status', 'PENDING_N2');
                    break;
                case 'validated':
                    $query->where('status', 'VALIDATED_N2');
                    break;
                case 'rejected':
                    $query->where('status', 'REJECTED');
                    break;
                case 'closed':
                    $query->where('status', 'CLOSED');
                    break;
            }
        }
        
        // Pagination à 15 éléments par page
        $tickets = $query->paginate(15)->withQueryString();
        
        $pendingCount = ChangeTicket::where('status', 'PENDING_N2')->count();
        
        return view('change.n2.index', compact('tickets', 'pendingCount'));
    }

    /**
     * Afficher le formulaire d'édition pour N+2
     */
    public function n2Edit(ChangeTicket $ticket)
    {
        $this->authorizeRole('N2');
        
        if (!in_array($ticket->status, ['PENDING_N2', 'VALIDATED_N2', 'PENDING_N3', 'CLOSED', 'REJECTED'])) {
            abort(404);
        }
        
        $tickets = ChangeTicket::whereIn('status', ['PENDING_N2', 'VALIDATED_N2', 'PENDING_N3', 'CLOSED', 'REJECTED'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $impactLvls = self::IMPACT_LVLS;
        $pendingCount = ChangeTicket::where('status', 'PENDING_N2')->count();
        
        return view('change.n2.form', compact('ticket', 'impactLvls', 'tickets', 'pendingCount'));
    }

    /**
     * Mettre à jour le formulaire (N+2)
     */
    public function n2Update(Request $request, ChangeTicket $ticket)
    {
        $this->authorizeRole('N2');
        
        $data = $request->validate([
            'recommandation' => 'nullable|string',
            'requete' => 'nullable|string',
            'date_exec_reelle' => 'nullable|date',
            'operateur' => 'nullable|string|max:100',
            'resultat' => 'nullable|string',
            'ecarts' => 'nullable|string',
        ]);

        $data['updated_by'] = Auth::id();

        if ($request->hasFile('recomm_files')) {
            $data['recomm_files'] = $this->uploadFiles($request->file('recomm_files'));
        }
        if ($request->hasFile('exec_files')) {
            $data['exec_files'] = $this->uploadFiles($request->file('exec_files'));
        }

        $ticket->update($data);

        return back()->with('success', 'Formulaire mis à jour.');
    }

    /**
     * Valider le formulaire et ouvrir un incident
     */
    public function n2Validate(Request $request, ChangeTicket $ticket)
    {
        $this->authorizeRole('N2');
        
        $incidentNum = 'INC-' . random_int(100000, 999999);
        
        $ticket->status = 'VALIDATED_N2';
        $ticket->incident_num = $incidentNum;
        $ticket->incident_opened_at = now();
        $ticket->history = array_merge($ticket->history ?? [], [[
            'role' => 'N2',
            'action' => "Formulaire validé — Rapport incident ouvert : {$incidentNum}",
            'at' => now()->format('d/m/Y H:i:s')
        ]]);
        $ticket->updated_by = Auth::id();
        $ticket->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => "Validation effectuée. Rapport d'incident ouvert : {$incidentNum}"
            ]);
        }
        
        return redirect()->route('change.n2.index')
            ->with('success', "Validation effectuée. Rapport d'incident ouvert : {$incidentNum}");
    }

    /**
     * Rejeter le formulaire
     */
    public function n2Reject(Request $request, ChangeTicket $ticket)
    {
        $this->authorizeRole('N2');
        
        $request->validate(['note' => 'required|string']);

        $ticket->status = 'REJECTED';
        $ticket->rejet_note = $request->note;
        $ticket->history = array_merge($ticket->history ?? [], [[
            'role' => 'N2',
            'action' => 'Formulaire rejeté',
            'note' => $request->note,
            'at' => now()->format('d/m/Y H:i:s')
        ]]);
        $ticket->updated_by = Auth::id();
        $ticket->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Formulaire rejeté']);
        }
        
        return redirect()->route('change.n2.index')
            ->with('success', 'Formulaire rejeté.');
    }

    // ==================== N+3 Functions ====================

    /**
     * Afficher la liste des formulaires pour N+3 avec pagination
     */
    public function n3Index(Request $request)
    {
        $this->authorizeRole('N3');
        
        // S'assurer que la session est synchronisée avec le rôle de l'utilisateur
        if (Auth::user()->role_change === 'N3') {
            session(['change_role' => 'N3']);
        }
        
        $query = ChangeTicket::whereIn('status', ['PENDING_N3', 'CLOSED'])
            ->orderBy('created_at', 'desc');
        
        // Appliquer le filtre si présent
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'pending':
                    $query->where('status', 'PENDING_N3');
                    break;
                case 'closed':
                    $query->where('status', 'CLOSED');
                    break;
            }
        }
        
        $tickets = $query->paginate(15)->withQueryString();
            
        $pendingCount = ChangeTicket::where('status', 'PENDING_N3')->count();
        
        return view('change.n3.index', compact('tickets', 'pendingCount'));
    }

    /**
     * Afficher le formulaire de clôture pour N+3
     */
    public function n3Edit(ChangeTicket $ticket)
    {
        $this->authorizeRole('N3');
        
        if (!in_array($ticket->status, ['PENDING_N3', 'CLOSED'])) {
            abort(404);
        }
        
        $tickets = ChangeTicket::whereIn('status', ['PENDING_N3', 'CLOSED'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $pendingCount = ChangeTicket::where('status', 'PENDING_N3')->count();
        
        return view('change.n3.form', compact('ticket', 'tickets', 'pendingCount'));
    }

    /**
     * Clôturer le ticket
     */
    public function n3Close(Request $request, ChangeTicket $ticket)
    {
        $this->authorizeRole('N3');
        
        $request->validate(['note' => 'required|string']);

        $ticket->status = 'CLOSED';
        $ticket->close_note = $request->note;
        $ticket->closed_at = now();
        $ticket->history = array_merge($ticket->history ?? [], [[
            'role' => 'N3',
            'action' => 'Ticket clôturé',
            'note' => $request->note,
            'at' => now()->format('d/m/Y H:i:s')
        ]]);
        $ticket->updated_by = Auth::id();
        $ticket->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Ticket clôturé avec succès.']);
        }
        
        return redirect()->route('change.n3.index')
            ->with('success', 'Ticket clôturé avec succès.');
    }

    // ==================== File Functions ====================

    /**
     * Télécharger un fichier
     */
    public function downloadFile($ticketId, $fileIndex, $type = 'files')
    {
        $ticket = ChangeTicket::findOrFail($ticketId);
        
        $files = $ticket->$type ?? [];
        
        if (isset($files[$fileIndex])) {
            $file = $files[$fileIndex];
            $filePath = storage_path('app/public/' . $file['path']);
            
            if (file_exists($filePath)) {
                return response()->download($filePath, $file['name']);
            }
        }
        
        abort(404);
    }

    /**
     * Supprimer un fichier
     */
    public function deleteFile(Request $request, ChangeTicket $ticket, $fileIndex, $type = 'files')
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($ticket);
        
        $files = $ticket->$type ?? [];
        
        if (isset($files[$fileIndex])) {
            Storage::disk('public')->delete($files[$fileIndex]['path']);
            unset($files[$fileIndex]);
            $ticket->$type = array_values($files);
            $ticket->save();
        }
        
        return back()->with('success', 'Fichier supprimé.');
    }

    // ==================== Helper Functions ====================

    /**
     * Vérifier que l'utilisateur a le bon rôle
     */
    private function authorizeRole($role)
    {
        $user = Auth::user();
        
        // Vérifier d'abord dans la base de données
        if ($user->role_change !== $role && $user->role_change !== null) {
            abort(403, 'Rôle non autorisé.');
        }
        
        // Puis vérifier la session pour compatibilité
        if (session('change_role') !== $role && session('change_role') !== null) {
            // Synchroniser la session avec la BD
            session(['change_role' => $user->role_change]);
        }
    }

    /**
     * Vérifier que l'utilisateur est propriétaire du ticket
     */
    private function authorizeOwner(ChangeTicket $ticket)
    {
        if ($ticket->created_by !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas propriétaire de ce ticket.');
        }
    }

    /**
     * Uploader des fichiers
     */
    private function uploadFiles($files)
    {
        $uploaded = [];
        foreach ($files as $file) {
            $path = $file->store('ticket-files/' . date('Y/m'), 'public');
            $uploaded[] = [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'path' => $path,
                'url' => Storage::url($path)
            ];
        }
        return $uploaded;
    }
}