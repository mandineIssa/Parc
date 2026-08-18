<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Exports\ProfilExport;
use App\Exports\ProfilTemplateExport;
use App\Imports\ProfilImport;
use App\Models\Agency;
use App\Models\Departement;
use App\Models\Filiale;
use App\Models\PosteOrganisation;
use App\Models\User;
use App\Support\UserSignatureStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        try {
            $user->load([
                'agence',
                'filiale',
                'nPlus1',
                'nPlus2',
                'equipment' => function ($query) {
                    $query->latest()->take(5);
                },
            ]);
            $user->loadCount('equipment');
        } catch (\Exception $e) {
            $user->equipment_count = 0;
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index()
    {
        $users = User::query()
            ->with(['agence', 'filiale'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.users.create', $this->profilFormData());
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['user', 'agent_it', 'super_admin', 'eod_n3', 'eod_controller'])],
            'role_change' => ['nullable', Rule::in(['N1', 'N2', 'N3', 'CONTROLLER'])],
            'eod_signature_only_ui' => ['sometimes', 'boolean'],
            'departement' => ['nullable', 'string', 'max:255'],
            'fonction' => ['nullable', 'string', 'max:255'],
            'matricule' => ['nullable', 'string', 'max:50', 'unique:users,matricule'],
            'telephone' => ['nullable', 'string', 'max:40'],
            'type_contrat' => ['nullable', Rule::in(['CDI', 'CDD', 'Stagiaire', 'Autre'])],
            'statut' => ['nullable', Rule::in(['actif', 'inactif'])],
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'filiale_id' => ['nullable', 'exists:filiales,id'],
            'n_plus_1_id' => ['nullable', 'exists:users,id'],
            'n_plus_2_id' => ['nullable', 'exists:users,id'],
            'signature_file' => ['nullable', 'image', 'max:4096'],
            'signature_canvas' => ['nullable', 'string'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['eod_signature_only_ui'] = $request->boolean('eod_signature_only_ui');
        $validated['email'] = strtolower(trim($validated['email']));
        $validated['type_contrat'] = $validated['type_contrat'] ?? 'CDI';
        $validated['statut'] = $validated['statut'] ?? 'actif';
        if (blank($validated['matricule'] ?? null)) {
            unset($validated['matricule']);
        }

        unset($validated['signature_file'], $validated['signature_canvas']);

        $user = User::create($validated);

        $signaturePath = UserSignatureStorage::storeFromRequest(
            $request,
            'signature_file',
            'signature_canvas',
            $user
        );
        if ($signaturePath) {
            $user->signature_path = $signaturePath;
            $user->signature_updated_at = now();
            $user->save();
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', array_merge(
            compact('user'),
            $this->profilFormData($user->id)
        ));
    }

    /**
     * Mettre à jour un utilisateur
     * CORRECTION IMPORTANTE ICI
     */


public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'prenom' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'role' => ['required', Rule::in(['user', 'agent_it', 'super_admin', 'eod_n3', 'eod_controller'])],
        'role_change' => ['nullable', Rule::in(['N1', 'N2', 'N3', 'CONTROLLER'])],
        'eod_signature_only_ui' => ['sometimes', 'boolean'],
        'departement' => ['nullable', 'string', 'max:255'],
        'fonction' => ['nullable', 'string', 'max:255'],
        'matricule' => ['nullable', 'string', 'max:50', Rule::unique('users', 'matricule')->ignore($user->id)],
        'telephone' => ['nullable', 'string', 'max:40'],
        'type_contrat' => ['nullable', Rule::in(['CDI', 'CDD', 'Stagiaire', 'Autre'])],
        'statut' => ['nullable', Rule::in(['actif', 'inactif'])],
        'agency_id' => ['nullable', 'exists:agencies,id'],
        'filiale_id' => ['nullable', 'exists:filiales,id'],
        'n_plus_1_id' => ['nullable', 'exists:users,id'],
        'n_plus_2_id' => ['nullable', 'exists:users,id'],
    ]);

    // Mise à jour du mot de passe si fourni
    if ($request->filled('password')) {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $validated['password'] = Hash::make($request->password);
    }

        $payload = [
            'name' => $validated['name'],
            'prenom' => $validated['prenom'],
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'role_change' => $validated['role_change'] ?? null,
            'eod_signature_only_ui' => $request->boolean('eod_signature_only_ui'),
            'departement' => $validated['departement'] ?? null,
            'fonction' => $validated['fonction'] ?? null,
            'matricule' => filled($validated['matricule'] ?? null) ? $validated['matricule'] : $user->matricule,
            'telephone' => $validated['telephone'] ?? null,
            'type_contrat' => $validated['type_contrat'] ?? 'CDI',
            'statut' => $validated['statut'] ?? 'actif',
            'agency_id' => $validated['agency_id'] ?? null,
            'filiale_id' => $validated['filiale_id'] ?? null,
            'n_plus_1_id' => $validated['n_plus_1_id'] ?? null,
            'n_plus_2_id' => $validated['n_plus_2_id'] ?? null,
            'updated_at' => now(),
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update($payload);

    // Vérification immédiate
    $updated = DB::table('users')->where('id', $user->id)->first();
    Log::info('Vérification après mise à jour:', [
        'id' => $updated->id,
        'role_change' => $updated->role_change
    ]);

    return redirect()->route('users.index')
        ->with('success', 'Utilisateur mis à jour avec succès.');
}

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression du dernier super admin
        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->count() === 1) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer le dernier super administrateur.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function export()
    {
        $filename = 'profils_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new ProfilExport(), $filename);
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProfilTemplateExport(), 'modele_import_profils.xlsx');
    }

    public function showImportForm()
    {
        return view('admin.users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        set_time_limit(300);

        $import = new ProfilImport();
        Excel::import($import, $request->file('excel_file'));

        $message = "Import terminé : {$import->imported} profil(s) importé(s)";
        if ($import->skipped > 0) {
            $message .= ", {$import->skipped} ligne(s) ignorée(s)";
        }

        if ($import->errors !== []) {
            return redirect()->route('users.import.form')
                ->with('warning', $message)
                ->with('import_errors', $import->errors);
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    /**
     * @return array{agencies: \Illuminate\Support\Collection, filiales: \Illuminate\Support\Collection, managers: \Illuminate\Support\Collection}
     */
    private function profilFormData(?int $exceptUserId = null): array
    {
        $managers = User::query()
            ->when($exceptUserId, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->orderBy('name')
            ->orderBy('prenom')
            ->get(['id', 'name', 'prenom', 'matricule']);

        return [
            'agencies' => Agency::query()->orderBy('nom')->get(['id', 'nom']),
            'filiales' => Filiale::query()->orderBy('nom')->get(['id', 'nom']),
            'departementOptions' => Departement::options(),
            'posteOptions' => PosteOrganisation::options(),
            'managers' => $managers,
        ];
    }
}