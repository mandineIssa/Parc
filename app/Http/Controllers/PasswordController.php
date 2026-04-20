<?php
// app/Http/Controllers/PasswordController.php

namespace App\Http\Controllers;

use App\Models\Password;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        return view('passwords.show', compact('password', 'availableUsers'));
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

        // Code 6 chiffres
        $otp      = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = "pwd_otp_{$password->id}_{$user->id}";
        Cache::put($cacheKey, $otp, now()->addMinutes(5));

        $email    = $user->email;
        $nom      = $user->name;
        $nomFiche = $password->nom;
        $html     = $this->buildOtpHtml($otp, $nomFiche, $nom);

        try {
            Mail::html($html, function ($message) use ($email, $nom) {
                $message->to($email, $nom)
                        ->subject('🔐 Code de vérification — Accès mot de passe IT COFINA');
            });

            $password->logAction('consultation', "OTP envoyé à {$email}");

            return response()->json([
                'success' => true,
                'email'   => $email,
                'message' => "Code envoyé à {$email}",
            ]);

        } catch (\Exception $e) {
            Log::error('OTP mail error: ' . $e->getMessage());

            // En mode debug seulement : afficher le code à l'écran
            return response()->json([
                'success'      => true,
                'email'        => $email,
                'message'      => "Code envoyé à {$email}",
                'debug_code'   => config('app.debug') ? $otp : null,
                'mail_error'   => config('app.debug') ? $e->getMessage() : null,
            ]);
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

    // ── Template email OTP ────────────────────────────────────────────────────
    private function buildOtpHtml(string $otp, string $nomFiche, string $nom): string
    {
        // Séparer les chiffres pour les afficher un par un
        $digits = implode('</td><td style="width:44px;height:52px;background:#eef2fa;border-radius:8px;text-align:center;vertical-align:middle;font-size:28px;font-weight:800;color:#0a2558;font-family:Courier New,monospace;border:2px solid #c6d2e8">', str_split($otp));

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Code OTP — COFINA IT</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f1f5f9;padding:40px 16px">
  <tr><td align="center">
    <table width="520" cellpadding="0" cellspacing="0" role="presentation"
           style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(10,37,88,.10);max-width:520px;width:100%">

      <!-- HEADER -->
      <tr>
        <td style="background:linear-gradient(135deg,#0a2558 0%,#1a3a8a 100%);padding:28px 36px;text-align:center">
          <table cellpadding="0" cellspacing="0" role="presentation" width="100%">
            <tr>
              <td align="center">
                <div style="display:inline-block;background:rgba(255,255,255,.12);border-radius:50%;padding:14px;margin-bottom:12px">
                  <span style="font-size:28px">🔐</span>
                </div>
                <p style="margin:0;color:#ffffff;font-size:20px;font-weight:700;letter-spacing:.5px">COFINA IT</p>
                <p style="margin:4px 0 0;color:#a8c4f0;font-size:12px">Gestion des mots de passe sécurisés</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- BODY -->
      <tr>
        <td style="padding:32px 36px">

          <p style="margin:0 0 6px;color:#1a2a40;font-size:15px;font-weight:600">Bonjour {$nom},</p>
          <p style="margin:0 0 24px;color:#5a6e8a;font-size:13px;line-height:1.7">
            Vous avez demandé l'accès au mot de passe de la fiche :<br>
            <strong style="color:#0a2558;font-size:14px">📋 {$nomFiche}</strong>
          </p>

          <!-- CODE DIGITS -->
          <table cellpadding="0" cellspacing="0" role="presentation" width="100%"
                 style="margin-bottom:24px;background:#f8fafc;border-radius:12px;padding:24px">
            <tr>
              <td align="center" style="padding-bottom:10px">
                <p style="margin:0;color:#5a6e8a;font-size:11px;text-transform:uppercase;letter-spacing:1.5px;font-weight:600">
                  Votre code de vérification
                </p>
              </td>
            </tr>
            <tr>
              <td align="center">
                <table cellpadding="0" cellspacing="6" role="presentation">
                  <tr>
                    <td style="width:44px;height:52px;background:#eef2fa;border-radius:8px;text-align:center;vertical-align:middle;font-size:28px;font-weight:800;color:#0a2558;font-family:Courier New,monospace;border:2px solid #c6d2e8">{$digits}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- AVERTISSEMENT -->
          <table cellpadding="0" cellspacing="0" role="presentation" width="100%" style="margin-bottom:20px">
            <tr>
              <td style="background:#fef6e4;border-left:4px solid #f59e0b;border-radius:0 6px 6px 0;padding:14px 16px">
                <p style="margin:0;color:#92400e;font-size:12px;line-height:1.6">
                  <strong>⚠ Attention :</strong> Ce code est valable <strong>5 minutes</strong>
                  et ne peut être utilisé <strong>qu'une seule fois</strong>.<br>
                  Si vous n'avez pas effectué cette demande, ignorez cet email.
                </p>
              </td>
            </tr>
          </table>

          <p style="margin:0;color:#94a3b8;font-size:11px;text-align:center;line-height:1.6">
            COFINA Sénégal — Service IT &amp; Exploitation
          </p>
        </td>
      </tr>

      <!-- FOOTER -->
      <tr>
        <td style="background:#f8fafc;padding:14px 36px;border-top:1px solid #e2e8f0;text-align:center">
          <p style="margin:0;color:#94a3b8;font-size:10px">
            Document confidentiel — Usage interne uniquement · COFINA Sénégal
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}
