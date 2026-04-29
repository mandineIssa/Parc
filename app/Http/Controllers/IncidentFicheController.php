<?php
// app/Http/Controllers/IncidentFicheController.php

namespace App\Http\Controllers;

use App\Models\IncidentFiche;
use App\Models\IncidentHistorique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidentFicheController extends Controller
{
   

    // ==================== INDEX ====================
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = IncidentFiche::with(['createdBy', 'n1User', 'n2User', 'n3User'])
            ->orderByDesc('created_at');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('criticite')) {
            $query->where('niveau_criticite', $request->criticite);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference', 'like', "%$s%")
                  ->orWhere('sujet', 'like', "%$s%")
                  ->orWhere('entite', 'like', "%$s%");
            });
        }

        // Restriction par rôle
        if ($user->isN1() && !$user->isSuperAdmin()) {
            $query->where('created_by', $user->id);
        } elseif ($user->isN2() && !$user->isSuperAdmin()) {
            $query->whereIn('statut', ['soumis', 'en_cours_n2', 'cloture']);
        }

        $fiches = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => IncidentFiche::count(),
            'soumis' => IncidentFiche::where('statut', 'soumis')->count(),
            'en_cours' => IncidentFiche::whereIn('statut', ['en_cours_n2', 'en_cours_n3'])->count(),
            'clotures' => IncidentFiche::where('statut', 'cloture')->count(),
        ];

        return view('incidents.index', compact('fiches', 'stats', 'user'));
    }

    // ==================== CREATE ====================
    public function create()
    {
        $user = Auth::user();
        if (!$user->isN1() && !$user->isSuperAdmin()) {
            abort(403, 'Réservé aux agents N+1 (Helpdesk).');
        }
        return view('incidents.create');
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isN1() && !$user->isSuperAdmin()) {
            abort(403, 'Réservé aux agents N+1 (Helpdesk).');
        }

        $data = $request->validate([
            // Champs de base
            'type' => ['required', Rule::in(['logiciel', 'materiel', 'reseau_telecom', 'application', 'infrastructure'])],
            'utilisateur' => 'required|string|max:255',
            'entite' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'point_entree' => ['required', Rule::in(['telephone', 'mail', 'application', 'itsm', 'hotline'])],
            'date_incident' => 'required|date',
            'heure_incident' => 'nullable|date_format:H:i',
            'sujet' => 'required|string|max:500',
            'bloquant' => 'boolean',
            'reproductible' => 'boolean',
            'description' => 'required|string',
            
            // Champs ITIL
            'application_concernee' => 'nullable|string|max:255',
            'environnement' => 'nullable|string|max:50',
            'niveau_criticite' => 'nullable|string|max:10',
            'heure_debut' => 'nullable|date_format:H:i',
            'service_impacte' => 'nullable|string|max:255',
            'nb_clients_impactes' => 'nullable|integer|min:0',
            'nb_utilisateurs_impactes' => 'nullable|integer|min:0',
            'impact_metier' => 'nullable|string',
        ]);

        $data['reference'] = IncidentFiche::generateReference();
        $data['created_by'] = Auth::id();
        $data['statut'] = 'soumis';
        $data['n1_user_id'] = Auth::id();
        $data['n1_date_traitement'] = now();

        $fiche = IncidentFiche::create($data);

        IncidentHistorique::create([
            'incident_fiche_id' => $fiche->id,
            'user_id' => Auth::id(),
            'action' => 'soumis',
            'commentaire' => 'Incident ITIL créé et soumis au workflow.',
            'niveau' => 'N1',
        ]);

        return redirect()->route('incidents.show', $fiche)
            ->with('success', "Incident {$fiche->reference} créé avec succès.");
    }

    // ==================== SHOW ====================
    public function show(IncidentFiche $incident)
    {
        $incident->load(['createdBy', 'n1User', 'n2User', 'n3User', 'historiques.user']);
        $user = Auth::user();
        return view('incidents.show', compact('incident', 'user'));
    }

    // ==================== EDIT ====================
    public function edit(IncidentFiche $incident)
    {
        $user = Auth::user();
        if ($incident->created_by !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Non autorisé.');
        }
        if (!in_array($incident->statut, ['brouillon', 'soumis'])) {
            abort(403, 'Fiche non modifiable une fois traitée.');
        }
        return view('incidents.edit', compact('incident'));
    }

    // ==================== UPDATE ====================
    public function update(Request $request, IncidentFiche $incident)
    {
        $user = Auth::user();
        if ($incident->created_by !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Non autorisé.');
        }
        if (!in_array($incident->statut, ['brouillon', 'soumis'])) {
            abort(403, 'Fiche non modifiable une fois traitée.');
        }

        $data = $request->validate([
            'type' => ['required', Rule::in(['logiciel', 'materiel', 'reseau_telecom', 'application', 'infrastructure'])],
            'utilisateur' => 'required|string|max:255',
            'entite' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'point_entree' => ['required', Rule::in(['telephone', 'mail', 'application', 'itsm', 'hotline'])],
            'date_incident' => 'required|date',
            'heure_incident' => 'nullable|date_format:H:i',
            'sujet' => 'required|string|max:500',
            'bloquant' => 'boolean',
            'reproductible' => 'boolean',
            'description' => 'required|string',
            'application_concernee' => 'nullable|string|max:255',
            'environnement' => 'nullable|string|max:50',
            'niveau_criticite' => 'nullable|string|max:10',
            'service_impacte' => 'nullable|string|max:255',
            'impact_metier' => 'nullable|string',
        ]);

        $incident->update($data);

        IncidentHistorique::create([
            'incident_fiche_id' => $incident->id,
            'user_id' => Auth::id(),
            'action' => 'modifie',
            'commentaire' => 'Déclaration modifiée.',
            'niveau' => 'N1',
        ]);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Fiche mise à jour.');
    }

    // ==================== TRAITEMENT N+1 ====================
    public function traiterN1(Request $request, IncidentFiche $incident)
    {
        $user = Auth::user();

        if (!$user->isN1() && !$user->isSuperAdmin()) {
            abort(403, 'Réservé aux agents N+1 (Helpdesk).');
        }
        if ($incident->statut !== 'soumis') {
            return back()->with('error', 'Fiche non traitable au niveau N+1.');
        }

        $data = $request->validate([
            'n1_description_traitement' => 'required|string',
            'n1_solutions_envisagees' => 'required|string',
            'n1_autres_intervenants' => 'nullable|string|max:500',
            'n1_statut' => ['required', Rule::in(['cloture', 'transfere'])],
            'heure_resolution' => 'nullable|date_format:H:i',
            'duree_incident' => 'nullable|string',
            'cause_racine' => 'nullable|string',
            'actions_correctives' => 'nullable|array',
            'actions_preventives' => 'nullable|array',
        ]);

        $data['n1_user_id'] = $user->id;
        $data['n1_date_traitement'] = now();

        if ($data['n1_statut'] === 'cloture') {
            $data['statut'] = 'cloture';
            $data['date_cloture'] = now();
            $data['valide_par'] = $user->id;
            $action = 'cloture';
            $commentaire = 'Incident clôturé au niveau N+1.';
        } else {
            $data['statut'] = 'en_cours_n2';
            $action = 'transfere_n2';
            $commentaire = 'Transféré au support N+2 pour analyse approfondie.';
        }

        $incident->update($data);

        IncidentHistorique::create([
            'incident_fiche_id' => $incident->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'commentaire' => $commentaire,
            'niveau' => 'N1',
        ]);

        return back()->with('success', 'Traitement N+1 enregistré.');
    }

    // ==================== TRAITEMENT N+2 ====================
    public function traiterN2(Request $request, IncidentFiche $incident)
    {
        $user = Auth::user();

        if (!$user->isN2() && !$user->isSuperAdmin()) {
            abort(403, 'Réservé aux agents N+2 (Support).');
        }
        if ($incident->statut !== 'en_cours_n2') {
            return back()->with('error', 'Fiche non en attente de traitement N+2.');
        }

        $data = $request->validate([
            'n2_description_traitement' => 'required|string',
            'n2_solutions_envisagees' => 'required|string',
            'n2_autres_intervenants' => 'nullable|string|max:500',
            'n2_statut' => ['required', Rule::in(['cloture', 'ouverture_ticket'])],
            'cause_racine' => 'nullable|string',
            'actions_correctives' => 'nullable|array',
            'actions_preventives' => 'nullable|array',
            'chronologie' => 'nullable|array',
            'heure_resolution' => 'nullable|date_format:H:i',
            'duree_incident' => 'nullable|string',
            'temps_resolution' => 'nullable|string',
            'sla_respecte' => 'boolean',
        ]);

        $data['n2_user_id'] = $user->id;
        $data['n2_date_traitement'] = now();

        if ($data['n2_statut'] === 'cloture') {
            $data['statut'] = 'cloture';
            $data['date_cloture'] = now();
            $data['valide_par'] = $user->id;
            $action = 'cloture';
            $commentaire = 'Incident résolu et clôturé au niveau N+2.';
        } else {
            $data['statut'] = 'en_cours_n3';
            $action = 'transfere_n3';
            $commentaire = 'Ticket problème ouvert - Escalade vers N+3 pour validation.';
        }

        $incident->update($data);

        IncidentHistorique::create([
            'incident_fiche_id' => $incident->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'commentaire' => $commentaire,
            'niveau' => 'N2',
        ]);

        return back()->with('success', 'Traitement N+2 enregistré.');
    }

    // ==================== TRAITEMENT N+3 ====================
    public function traiterN3(Request $request, IncidentFiche $incident)
    {
        $user = Auth::user();

        if (!$user->isN3() && !$user->isSuperAdmin()) {
            abort(403, 'Réservé aux agents N+3 (Validateur).');
        }
        if ($incident->statut !== 'en_cours_n3') {
            return back()->with('error', 'Fiche non en attente de traitement N+3.');
        }

        $data = $request->validate([
            'n3_description_traitement' => 'required|string',
            'n3_solutions_envisagees' => 'required|string',
            'n3_autres_intervenants' => 'nullable|string|max:500',
            'n3_statut' => ['required', Rule::in(['cloture', 'escalade'])],
            'commentaires_cloture' => 'nullable|string',
        ]);

        $data['n3_user_id'] = $user->id;
        $data['n3_date_traitement'] = now();
        $data['statut'] = 'cloture';
        $data['date_cloture'] = now();
        $data['valide_par'] = $user->id;

        $incident->update($data);

        IncidentHistorique::create([
            'incident_fiche_id' => $incident->id,
            'user_id' => Auth::id(),
            'action' => 'cloture',
            'commentaire' => 'Incident validé et clôturé définitivement par N+3.',
            'niveau' => 'N3',
        ]);

        return back()->with('success', 'Incident clôturé définitivement.');
    }

    // ==================== UPLOAD PDF ====================
    public function uploadPdf(Request $request, IncidentFiche $incident)
    {
        $user = Auth::user();

        $request->validate([
            'niveau' => ['required', Rule::in(['n1', 'n2', 'n3'])],
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        $niveau = $request->niveau;

        $allowed = match($niveau) {
            'n1' => $user->isN1() || $user->isSuperAdmin() || $incident->created_by === $user->id,
            'n2' => $user->isN2() || $user->isSuperAdmin(),
            'n3' => $user->isN3() || $user->isSuperAdmin(),
            default => false,
        };

        if (!$allowed) {
            abort(403, "Upload non autorisé.");
        }

        $ancien = $incident->{"{$niveau}_pdf_path"};
        if ($ancien && Storage::disk('public')->exists($ancien)) {
            Storage::disk('public')->delete($ancien);
        }

        $path = $request->file('pdf')->store("incidents/pdfs/{$incident->id}/{$niveau}", 'public');
        $incident->update(["{$niveau}_pdf_path" => $path]);

        IncidentHistorique::create([
            'incident_fiche_id' => $incident->id,
            'user_id' => Auth::id(),
            'action' => 'pdf_uploade',
            'commentaire' => 'PDF uploadé au niveau ' . strtoupper($niveau),
            'niveau' => strtoupper($niveau),
        ]);

        return back()->with('success', 'PDF uploadé avec succès.');
    }

    // ==================== GÉNÉRER PDF FINAL ====================
// Dans IncidentFicheController.php, méthode genererPdf()

public function genererPdf(IncidentFiche $incident)
{
    $user = Auth::user();

    if (!$user->isSuperAdmin() && !$user->isN3() && $incident->created_by !== $user->id) {
        abort(403, 'Non autorisé.');
    }

    $incident->load(['createdBy', 'n1User', 'n2User', 'n3User']);

    $pdf = Pdf::loadView('incidents.pdf', compact('incident'))
        ->setPaper('a4', 'portrait');

    $filename = "RAPPORT_INCIDENT_{$incident->reference}.pdf";
    $path = "incidents/pdfs/{$incident->id}/fiche_finale/{$filename}";

    // Stocker le fichier avec les bonnes permissions
    Storage::disk('public')->put($path, $pdf->output());
    
    // ⚠️ Forcer les permissions après l'upload
    $fullPath = Storage::disk('public')->path($path);
    chmod($fullPath, 0644);
    
    $incident->update(['pdf_fiche_path' => $path]);

    IncidentHistorique::create([
        'incident_fiche_id' => $incident->id,
        'user_id' => Auth::id(),
        'action' => 'pdf_final_genere',
        'commentaire' => 'Rapport ITIL PDF final généré.',
        'niveau' => $user->role_change ?? 'ADMIN',
    ]);

    return $pdf->download($filename);
}

    // ==================== DESTROY ====================
    public function destroy(IncidentFiche $incident)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $incident->created_by !== $user->id) {
            abort(403);
        }
        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Fiche supprimée.');
    }

    // Dans IncidentFicheController.php, ajouter cette méthode

public function viewPdf(IncidentFiche $incident)
{
    $user = Auth::user();
    
    // Vérifier les droits d'accès
    if (!$user->isSuperAdmin() && !$user->isN3() && $incident->created_by !== $user->id) {
        abort(403, 'Non autorisé.');
    }
    
    if (!$incident->pdf_fiche_path || !Storage::disk('public')->exists($incident->pdf_fiche_path)) {
        abort(404, 'PDF non trouvé.');
    }
    
    // Lire le fichier et le retourner
    $file = Storage::disk('public')->get($incident->pdf_fiche_path);
    $headers = [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . basename($incident->pdf_fiche_path) . '"',
    ];
    
    return response($file, 200, $headers);
}
}