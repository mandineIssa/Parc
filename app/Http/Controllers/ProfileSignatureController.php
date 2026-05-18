<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\UserSignatureStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileSignatureController extends Controller
{
    /**
     * Signature enregistrée du compte connecté (pour le bouton « Charger ma signature »).
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'has_signature' => (bool) $user->signature_path,
            'data_url' => UserSignatureStorage::dataUriForUser($user),
            'preview_url' => $user->signaturePublicUrl(),
            'updated_at' => $user->signature_updated_at?->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Enregistrer / mettre à jour la signature du profil connecté.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'signature_file' => ['nullable', 'image', 'max:4096'],
            'signature_canvas' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        $path = UserSignatureStorage::storeFromRequest($request, 'signature_file', 'signature_canvas', $user);

        if (! $path && ! UserSignatureStorage::isDataImage($request->input('signature_canvas'))) {
            return back()->with('error', 'Veuillez dessiner une signature ou importer une image.');
        }

        if ($path) {
            UserSignatureStorage::deleteForUser($user);
            $user->signature_path = $path;
            $user->signature_updated_at = now();
            $user->save();
        }

        return back()->with('success', 'Signature enregistrée sur votre profil.');
    }

    /**
     * Supprimer la signature du profil connecté.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        UserSignatureStorage::deleteForUser($user);
        $user->signature_path = null;
        $user->signature_updated_at = null;
        $user->save();

        return back()->with('success', 'Signature supprimée de votre profil.');
    }

    /**
     * Admin : enregistrer la signature d'un utilisateur.
     */
    public function storeForUser(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->validate([
            'signature_file' => ['nullable', 'image', 'max:4096'],
            'signature_canvas' => ['nullable', 'string'],
        ]);

        $path = UserSignatureStorage::storeFromRequest($request, 'signature_file', 'signature_canvas', $user);

        if (! $path && ! UserSignatureStorage::isDataImage($request->input('signature_canvas'))) {
            return back()->with('error', 'Veuillez dessiner une signature ou importer une image.');
        }

        if ($path) {
            UserSignatureStorage::deleteForUser($user);
            $user->signature_path = $path;
            $user->signature_updated_at = now();
            $user->save();
        }

        return back()->with('success', 'Signature enregistrée pour cet utilisateur.');
    }

    /**
     * Admin : supprimer la signature d'un utilisateur.
     */
    public function destroyForUser(User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        UserSignatureStorage::deleteForUser($user);
        $user->signature_path = null;
        $user->signature_updated_at = null;
        $user->save();

        return back()->with('success', 'Signature supprimée pour cet utilisateur.');
    }

    private function authorizeAdmin(): void
    {
        if (! Auth::user()?->canManageUsers()) {
            abort(403);
        }
    }
}
