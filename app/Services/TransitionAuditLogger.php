<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\TransitionApproval;
use App\Traits\Auditable;

class TransitionAuditLogger
{
    public function logApproval(TransitionApproval $approval, string $context = 'approved'): void
    {
        $approval->loadMissing('equipment');
        $equipment = $approval->equipment;

        if ($equipment instanceof Equipment) {
            Auditable::auditManual('transition_'.$context, $equipment, [
                'notes' => "Transition {$approval->type} — {$context} (ref #{$approval->id})",
                'transition_type' => $approval->type,
                'changes' => [
                    'approval_id' => $approval->id,
                    'status' => $approval->status,
                ],
            ]);
        }

        Auditable::auditManual('transition_'.$context, $approval, [
            'notes' => "Approbation {$approval->type} — {$context}",
            'transition_type' => $approval->type,
            'new_data' => ['status' => $approval->status],
        ]);
    }

    public function logRejection(TransitionApproval $approval, ?string $reason = null): void
    {
        $this->logApproval($approval, 'rejected');
        if ($reason) {
            Auditable::auditManual('transition_rejected', $approval, [
                'notes' => "Motif : {$reason}",
                'transition_type' => $approval->type,
            ]);
        }
    }
}
