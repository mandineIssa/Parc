<?php
namespace App\Http\Controllers;

use App\Mail\PasswordOtpMail;
use App\Models\Password;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Password::with('creator')->active()
            ->when($request->search,    fn($q,$s) => $q->where('nom','like',"%$s%")->orWhere('adresse_ip','like',"%$s%")->orWhere('compte','like',"%$s%"))
            ->when($request->categorie, fn($q,$c) => $q->where('categorie',$c))
            ->when($request->site,      fn($q,$s) => $q->where('site',$s))
            ->latest();

        return view('passwords.index', [
            'passwords'    => $query->paginate(15)->withQueryString(),
            'categories'   => Password::categories(),
            'sites'        => Password::sites(),
            'expiresSoon'  => Password::expiresSoon(30)->count(),
            'statsByCateg' => Password::selectRaw('categorie, count(*) as total')->active()->groupBy('categorie')->pluck('total','categorie'),
        ]);
    }

    // ── Create / Store ────────────────────────────────────────────────────────
    public function create()
    {
        return view('passwords.create', [
            'sites' => Password::sites(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categorie'               => 'required|string',
            'nom'                     => 'required|string|max:255',
            'compte'                  => 'required|string|max:255',
            'mot_de_passe'            => 'required|string',
            'adresse_ip'              => 'nullable|string',
            'protocole'               => 'nullable|string',
            'site'                    => 'nullable|string',
            'date_expiration'         => 'nullable|date',
            'duree_renouvellement'    => 'nullable|integer|min:1',
            'description'             => 'nullable|string',
            'nom_exi'                 => 'nullable|string',
            'nom_vm'                  => 'nullable|string',
            'adresse_ip_vm'           => 'nullable|string',
            'instance'                => 'nullable|string',
            'type_equipement'         => 'nullable|string',
            'champs_libres'           => 'nullable|array',
            'champs_libres.*.libelle' => 'nullable|string|max:100',
            'champs_libres.*.contenu' => 'nullable|string',
            'fichiers.*'              => 'nullable|file|max:5120',
            'partages'                => 'nullable|array',
        ]);

        $validated['created_by']    = auth()->id();
        $validated['champs_libres'] = collect($request->champs_libres ?? [])
            ->filter(fn($c) => !empty($c['libelle']))->values()->toArray();

        $password = Password::create($validated);

        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $file) {
                $path = $file->store("passwords/{$password->id}", 'private');
                $password->fichiers()->create([
                    'nom_original' => $file->getClientOriginalName(),
                    'chemin'       => $path,
                    'taille'       => $file->getSize(),
                    'mime'         => $file->getMimeType(),
                    'uploaded_by'  => auth()->id(),
                ]);
            }
        }

        foreach ($request->partages ?? [] as $p) {
            if (!empty($p['user_id'])) {
                $password->shares()->create([
                    'user_id'   => $p['user_id'],
                    'droit'     => $p['droit'] ?? 'lecture',
                    'permanent' => true,
                ]);
            }
        }

        $password->logAction('creation', 'Création de la fiche');
        return redirect()->route('passwords.show', $password)->with('success', 'Fiche créée avec succès.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(Password $password)
    {
        $this->checkAccess($password);
        $password->logAction('consultation');
        $password->load('creator', 'updater', 'logs.user', 'shares.user', 'fichiers');

        $usersAlreadyShared = $password->shares->pluck('user_id')->toArray();
        $availableUsers     = User::whereNotIn('id', array_merge([$password->created_by], $usersAlreadyShared))
                                  ->orderBy('name')->get();

        $user            = auth()->user();
        $canManageShares = in_array($user->role ?? '', ['admin', 'responsable_it'])
                        || $password->created_by === $user->id;

        return view('passwords.show', compact('password', 'availableUsers', 'canManageShares'));
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────
    public function edit(Password $password)
    {
        $this->checkAccess($password, 'modification');
        return view('passwords.edit', [
            'password' => $password,
            'sites'    => Password::sites(),
            'users'    => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Password $password)
    {
        $this->checkAccess($password, 'modification');

        $validated = $request->validate([
            'categorie'               => 'required|string',
            'nom'                     => 'required|string|max:255',
            'compte'                  => 'required|string|max:255',
            'mot_de_passe'            => 'nullable|string',
            'adresse_ip'              => 'nullable|string',
            'protocole'               => 'nullable|string',
            'site'                    => 'nullable|string',
            'date_expiration'         => 'nullable|date',
            'duree_renouvellement'    => 'nullable|integer|min:1',
            'description'             => 'nullable|string',
            'nom_exi'                 => 'nullable|string',
            'nom_vm'                  => 'nullable|string',
            'adresse_ip_vm'           => 'nullable|string',
            'instance'                => 'nullable|string',
            'type_equipement'         => 'nullable|string',
            'champs_libres'           => 'nullable|array',
            'champs_libres.*.libelle' => 'nullable|string|max:100',
            'champs_libres.*.contenu' => 'nullable|string',
            'fichiers.*'              => 'nullable|file|max:5120',
        ]);

        if (empty($validated['mot_de_passe'])) unset($validated['mot_de_passe']);

        $validated['champs_libres'] = collect($request->champs_libres ?? [])
            ->filter(fn($c) => !empty($c['libelle']))->values()->toArray();
        $validated['updated_by'] = auth()->id();
        $password->update($validated);

        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $file) {
                $path = $file->store("passwords/{$password->id}", 'private');
                $password->fichiers()->create([
                    'nom_original' => $file->getClientOriginalName(),
                    'chemin'       => $path,
                    'taille'       => $file->getSize(),
                    'mime'         => $file->getMimeType(),
                    'uploaded_by'  => auth()->id(),
                ]);
            }
        }

        $password->logAction('modification', 'Mise à jour');
        return redirect()->route('passwords.show', $password)->with('success', 'Fiche mise à jour.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy(Password $password)
    {
        $password->logAction('suppression');
        $password->delete();
        return redirect()->route('passwords.index')->with('success', 'Fiche supprimée.');
    }

    // ── Fichiers ──────────────────────────────────────────────────────────────
    public function downloadFichier(Password $password, $id)
    {
        $this->checkAccess($password);
        $f = $password->fichiers()->findOrFail($id);
        $password->logAction('consultation', 'Téléchargement: ' . $f->nom_original);
        return \Storage::disk('private')->download($f->chemin, $f->nom_original);
    }

    public function deleteFichier(Password $password, $id)
    {
        $this->checkManageShares($password);
        $f = $password->fichiers()->findOrFail($id);
        \Storage::disk('private')->delete($f->chemin);
        $f->delete();
        return back()->with('success', 'Fichier supprimé.');
    }

    // ── OTP : Envoyer le code par email ───────────────────────────────────────
    public function sendOtp(Password $password)
    {
        $this->checkAccess($password);
        $user = auth()->user();

        $otp      = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = "pwd_otp_{$password->id}_{$user->id}";
        Cache::put($cacheKey, $otp, now()->addMinutes(5));

        try {
            Mail::to($user->email, $user->name)
                ->send(new PasswordOtpMail($otp, $password->nom, $user->name));

            $password->logAction('consultation', "OTP envoyé à {$user->email}");

            return response()->json([
                'success' => true,
                'email'   => $user->email,
                'message' => "Code envoyé à {$user->email}",
            ]);

        } catch (\Exception $e) {
            Log::error('OTP mail error: ' . $e->getMessage());

            // En mode debug uniquement : renvoyer le code pour tests
            return response()->json([
                'success'    => false,
                'email'      => $user->email,
                'message'    => "Échec d'envoi de l'email.",
                'debug_code' => config('app.debug') ? $otp : null,
                'mail_error' => config('app.debug') ? $e->getMessage() : null,
            ], config('app.debug') ? 200 : 500);
        }
    }

    // ── OTP : Vérifier le code ────────────────────────────────────────────────
    public function verifyOtp(Request $request, Password $password)
    {
        $this->checkAccess($password);
        $request->validate(['otp' => 'required|string']);

        $user     = auth()->user();
        $cacheKey = "pwd_otp_{$password->id}_{$user->id}";
        $stored   = Cache::get($cacheKey);

        if (!$stored || trim($request->otp) !== $stored) {
            return response()->json(['error' => 'Code invalide ou expiré. Demandez un nouveau code.'], 422);
        }

        Cache::forget($cacheKey);
        $password->logAction('consultation', 'Mot de passe révélé via OTP');

        return response()->json(['mot_de_passe' => $password->mot_de_passe]);
    }

    // ── Partages ──────────────────────────────────────────────────────────────
    public function share(Request $request, Password $password)
    {
        $this->checkManageShares($password);

        $request->validate([
            'partages'               => 'required|array|min:1',
            'partages.*.user_id'     => 'required|exists:users,id',
            'partages.*.droit'       => 'required|in:lecture,modification,administration',
            'partages.*.expiration'  => 'nullable|date|after:today',
        ]);

        $added = 0;
        foreach ($request->partages as $p) {
            if (empty($p['user_id'])) continue;
            $password->shares()->updateOrCreate(
                ['user_id' => $p['user_id']],
                [
                    'droit'      => $p['droit'],
                    'expiration' => $p['expiration'] ?? null,
                    'permanent'  => empty($p['expiration']),
                ]
            );
            $added++;
        }

        $password->logAction('partage', "Partagé avec {$added} utilisateur(s)");
        return back()->with('success', "{$added} partage(s) enregistré(s).");
    }

    public function updateShare(Request $request, Password $password, $shareId)
    {
        $this->checkManageShares($password);

        $request->validate([
            'droit'      => 'required|in:lecture,modification,administration',
            'expiration' => 'nullable|date',
        ]);

        $share = $password->shares()->findOrFail($shareId);
        $share->update([
            'droit'      => $request->droit,
            'expiration' => $request->expiration,
            'permanent'  => empty($request->expiration),
        ]);

        $password->logAction('partage', "Rôle modifié → {$request->droit} pour user#{$share->user_id}");
        return back()->with('success', 'Rôle mis à jour.');
    }

    public function revokeShare(Password $password, $shareId)
    {
        $this->checkManageShares($password);

        $share    = $password->shares()->findOrFail($shareId);
        $userName = $share->user?->name ?? 'inconnu';
        $share->delete();
        $password->logAction('partage', "Accès révoqué pour {$userName}");
        return back()->with('success', "Accès de {$userName} révoqué.");
    }

    // ── Contrôle d'accès ──────────────────────────────────────────────────────
    private function checkAccess(Password $password, string $niveau = 'lecture'): void
    {
        $user = auth()->user();
        if (in_array($user->role ?? '', ['admin', 'responsable_it'])) return;
        if ($password->created_by === $user->id) return;

        $share = $password->shares()
            ->where('user_id', $user->id)
            ->where(fn($q) => $q->where('permanent', true)->orWhere('expiration', '>=', now()))
            ->first();

        if (!$share) abort(403, 'Accès non autorisé.');

        $niveaux = ['lecture' => 0, 'modification' => 1, 'administration' => 2];
        if (($niveaux[$share->droit] ?? -1) < ($niveaux[$niveau] ?? 0)) {
            abort(403, 'Droits insuffisants.');
        }
    }

    private function checkManageShares(Password $password): void
    {
        $user = auth()->user();
        if (in_array($user->role ?? '', ['admin', 'responsable_it'])) return;
        if ($password->created_by === $user->id) return;
        abort(403, 'Seul le créateur ou un administrateur peut gérer les partages.');
    }
    
}