<?php

namespace App\Services;

use App\Models\EodSuivi;
use App\Models\User;
use Illuminate\Support\Collection;

class EodSuiviNotifier
{
    public function __construct(
        private readonly UserMailNotifier $mail
    ) {}

    /**
     * Fiche soumise par N+1 / N+2 → notifier N+3, Controller et l'auteur.
     */
    public function notifySubmittedForSignatures(EodSuivi $fiche): void
    {
        $fiche->loadMissing('creator');
        $reference = $fiche->reference ?? "#{$fiche->id}";
        $date = $fiche->date_traitement?->format('d/m/Y') ?? '—';
        $author = $this->authorName($fiche);

        $signatories = $this->signatoryRecipients()
            ->reject(fn (User $u) => (int) $u->id === (int) $fiche->created_by);

        $this->mail->notifyMany(
            $signatories,
            "[GPI] Fiche EOD à signer — {$reference}",
            'Signature EOD requise',
            "Une fiche de suivi EOD du {$date} a été soumise par {$author}.\n"
            . "Référence : {$reference}\n"
            . "Statut : en attente des signatures N+3 et Controller.",
            route('eod.n3.pending'),
            'Voir les fiches en attente'
        );

        $this->mail->notifyUser(
            $fiche->created_by,
            "[GPI] Fiche EOD transmise — {$reference}",
            'Fiche transmise avec succès',
            "Votre fiche EOD du {$date} a bien été envoyée à N+3 et au Controller pour signature.\n"
            . "Référence : {$reference}",
            $this->authorEditUrl($fiche),
            'Voir ma fiche'
        );
    }

    /**
     * Signature N+3 enregistrée, Controller encore attendu.
     */
    public function notifyWaitingControllerSignature(EodSuivi $fiche, ?int $excludeUserId = null): void
    {
        $reference = $fiche->reference ?? "#{$fiche->id}";
        $recipients = $this->controllerRecipients()
            ->when($excludeUserId, fn (Collection $c) => $c->where('id', '!=', $excludeUserId));

        $this->mail->notifyMany(
            $recipients,
            "[GPI] Signature Controller requise — {$reference}",
            'Fiche EOD — signature Controller',
            "La signature N+3 est enregistrée. La fiche {$reference} attend la signature Controller.",
            route('eod.controller.edit', $fiche),
            'Signer la fiche'
        );
    }

    /**
     * Signature Controller enregistrée, N+3 encore attendu.
     */
    public function notifyWaitingN3Signature(EodSuivi $fiche, ?int $excludeUserId = null): void
    {
        $reference = $fiche->reference ?? "#{$fiche->id}";
        $recipients = $this->n3Recipients()
            ->when($excludeUserId, fn (Collection $c) => $c->where('id', '!=', $excludeUserId));

        $this->mail->notifyMany(
            $recipients,
            "[GPI] Signature N+3 requise — {$reference}",
            'Fiche EOD — signature N+3',
            "La signature Controller est enregistrée. La fiche {$reference} attend la signature N+3.",
            route('eod.n3.show', $fiche),
            'Signer la fiche'
        );
    }

    /**
     * Fiche clôturée (deux signatures complètes) → notifier l'auteur.
     */
    public function notifyClosed(EodSuivi $fiche): void
    {
        $reference = $fiche->reference ?? "#{$fiche->id}";
        $date = $fiche->date_traitement?->format('d/m/Y') ?? '—';

        $this->mail->notifyUser(
            $fiche->created_by,
            "[GPI] Fiche EOD clôturée — {$reference}",
            'Fiche EOD clôturée',
            "Votre fiche EOD du {$date} est clôturée (signatures N+3 et Controller complètes).\n"
            . "Référence : {$reference}",
            $this->authorEditUrl($fiche),
            'Voir la fiche'
        );
    }

    /**
     * @return Collection<int, User>
     */
    private function signatoryRecipients(): Collection
    {
        return $this->n3Recipients()
            ->merge($this->controllerRecipients())
            ->unique('id')
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function n3Recipients(): Collection
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get()
            ->filter(fn (User $user) => $user->canAccessEodAsN3())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function controllerRecipients(): Collection
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get()
            ->filter(fn (User $user) => $user->canSignEodControllerSlot())
            ->values();
    }

    private function authorName(EodSuivi $fiche): string
    {
        $creator = $fiche->creator;
        if (! $creator) {
            return 'un utilisateur';
        }

        $name = trim((string) $creator->prenom . ' ' . (string) $creator->name);

        return $name !== '' ? $name : (string) $creator->email;
    }

    private function authorEditUrl(EodSuivi $fiche): string
    {
        $creator = $fiche->creator ?? User::find($fiche->created_by);

        if ($creator && $creator->role_change === 'N2') {
            return route('eod.n2.edit', $fiche);
        }

        return route('eod.n1.edit', $fiche);
    }
}
