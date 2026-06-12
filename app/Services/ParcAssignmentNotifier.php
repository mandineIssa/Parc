<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Parc;

class ParcAssignmentNotifier
{
    public function __construct(
        private readonly UserMailNotifier $mail
    ) {}

    public function notifyCreated(Parc $parc, Equipment $equipment): void
    {
        $parc->loadMissing('affectateur');
        $reference = $parc->numero_bon_affectation ?? "#{$parc->id}";
        $utilisateur = trim($parc->utilisateur_prenom . ' ' . $parc->utilisateur_nom);
        $details = $this->equipmentDetailsBlock($equipment);

        if ($parc->affecte_par) {
            $this->mail->notifyUser(
                (int) $parc->affecte_par,
                "[GPI] Demande validée — {$reference}",
                'Votre demande a été validée',
                "Votre demande d'affectation au parc a été validée avec succès.\n"
                . "Bénéficiaire : {$utilisateur}\n"
                . "Référence : {$reference}\n\n{$details}",
                route('parc.index'),
                'Voir le parc'
            );
        }

        if ($parc->email) {
            $this->mail->notifyUserByEmail(
                $parc->email,
                '[GPI] Équipement affecté — parc informatique',
                'Un équipement vous a été affecté',
                "Bonjour {$utilisateur},\n\nUn équipement du parc informatique vous a été affecté.\n"
                . "Référence : {$reference}\n{$details}",
                route('dashboard'),
                'Ouvrir GPI'
            );
        }
    }

    public function equipmentDetailsBlock(Equipment $equipment): string
    {
        return 'N°' . ($equipment->numero_serie ?: '—');
    }
}
