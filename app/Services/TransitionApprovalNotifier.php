<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\TransitionApproval;
use App\Models\User;

class TransitionApprovalNotifier
{
    public function __construct(
        private readonly UserMailNotifier $mail
    ) {}

    public function notifySuperAdminsPending(TransitionApproval $approval, ?string $origine = null): void
    {
        $suffix = $origine ? " ({$origine})" : '';
        $label = $this->typeLabel($approval);

        $this->mail->notifyMany(
            $this->mail->superAdmins(),
            "[GPI] Demande en attente — {$label}",
            'Nouvelle demande à valider',
            "Une demande{$suffix} nécessite votre validation.\nRéférence : #{$approval->id}\n{$this->equipmentLine($approval)}",
            $this->approvalUrl($approval)
        );
    }

    public function notifySubmitterApproved(TransitionApproval $approval, string $contextLabel): void
    {
        $this->mail->notifyUser(
            $approval->submitted_by,
            "[GPI] Demande approuvée — {$contextLabel}",
            'Votre demande a été validée',
            "Votre demande « {$contextLabel} » a été validée.\nRéférence : #{$approval->id}\n{$this->equipmentLine($approval)}",
            $this->approvalUrl($approval)
        );
    }

    public function notifySubmitterApprovedByType(TransitionApproval $approval): void
    {
        $this->notifySubmitterApproved($approval, $this->typeLabel($approval));
    }

    public function notifySubmitterRejected(TransitionApproval $approval, string $contextLabel): void
    {
        $reason = $approval->rejection_reason
            ? "\nMotif : {$approval->rejection_reason}"
            : '';

        $this->mail->notifyUser(
            $approval->submitted_by,
            "[GPI] Demande rejetée — {$contextLabel}",
            'Votre demande a été rejetée',
            "Votre demande « {$contextLabel} » a été rejetée.{$reason}\nRéférence : #{$approval->id}\n{$this->equipmentLine($approval)}",
            $this->approvalUrl($approval)
        );
    }

    public function notifyUserApproval(TransitionApproval $approval, ?User $user): void
    {
        if (! $user) {
            return;
        }

        $label = $this->typeLabel($approval);

        $this->mail->notifyUser(
            $user,
            "[GPI] Équipement affecté — {$label}",
            'Transition approuvée',
            "Une transition a été approuvée pour un équipement qui vous concerne.\n{$this->equipmentLine($approval)}",
            $this->approvalUrl($approval)
        );
    }

    public function notifyEquipmentUserByEmail(Equipment $equipment, string $contextLabel, ?string $email): void
    {
        if (! $email) {
            return;
        }

        $this->mail->notifyUserByEmail(
            $email,
            "[GPI] {$contextLabel}",
            $contextLabel,
            "Concernant l'équipement : {$equipment->nom} (N° série : {$equipment->numero_serie}).",
            route('dashboard')
        );
    }

    public function notifyMaintenanceStakeholders(TransitionApproval $approval): void
    {
        $data = is_array($approval->data) ? $approval->data : json_decode($approval->data ?? '[]', true);
        $label = 'Envoi en maintenance';

        if (! empty($data['parc_info']['utilisateur_id'])) {
            $this->mail->notifyUser(
                (int) $data['parc_info']['utilisateur_id'],
                "[GPI] {$label}",
                'Équipement envoyé en maintenance',
                "Un équipement qui vous est affecté est en cours de traitement pour maintenance.\n{$this->equipmentLine($approval)}",
                $this->approvalUrl($approval)
            );
        } elseif (! empty($data['parc_info']['utilisateur_email'])) {
            $this->mail->notifyUserByEmail(
                $data['parc_info']['utilisateur_email'],
                "[GPI] {$label}",
                'Équipement envoyé en maintenance',
                "Un équipement qui vous est affecté est en cours de traitement pour maintenance.\n{$this->equipmentLine($approval)}",
                $this->approvalUrl($approval)
            );
        }

        $this->notifySubmitterApproved($approval, $label);
    }

    private function approvalUrl(TransitionApproval $approval): string
    {
        try {
            return route('transitions.approval.show', $approval);
        } catch (\Throwable) {
            return url('/dashboard');
        }
    }

    private function equipmentLine(TransitionApproval $approval): string
    {
        $approval->loadMissing('equipment');
        $equipment = $approval->equipment;

        if (! $equipment) {
            return 'Équipement : non renseigné';
        }

        return "Équipement : {$equipment->nom} (N° série : {$equipment->numero_serie})";
    }

    private function typeLabel(TransitionApproval $approval): string
    {
        return match ($approval->type) {
            'stock_to_parc' => 'Stock → Parc',
            'parc_to_maintenance' => 'Parc → Maintenance',
            'maintenance_to_stock' => 'Maintenance → Stock',
            'stock_to_hors_service', 'parc_to_hors_service', 'maintenance_to_hors_service' => 'Mise hors service',
            'parc_to_perdu', 'stock_to_perdu' => 'Déclaration de perte',
            default => str_replace('_', ' ', (string) $approval->type),
        };
    }
}
