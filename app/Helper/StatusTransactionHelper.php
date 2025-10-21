<?php

declare(strict_types=1);

namespace App\Helper;

class StatusTransactionHelper
{
    /**
     * Get human-readable status text from workflow status code
     */
    public static function getStatusText(string $workflowStatus): string
    {
        return match ($workflowStatus) {
            'pending_confirmation' => 'Konfirmasi?',
            'confirmed' => 'Terkonfirmasi',
            'picked_up' => 'Dijemput',
            'at_loading_post' => 'Di Pos',
            'in_washing' => 'Dicuci',
            'washing_completed' => 'Siap Antar',
            'out_for_delivery' => 'Mengantar',
            'delivered' => 'Selesai',
            'cancelled' => 'Batal',
            default => $workflowStatus,
        };
    }

    /**
     * Get badge color class for workflow status
     */
    public static function getStatusBadgeColor(string $workflowStatus): string
    {
        return match ($workflowStatus) {
            'pending_confirmation' => 'badge-secondary',
            'confirmed' => 'badge-info',
            'picked_up' => 'badge-warning',
            'at_loading_post' => 'badge-warning',
            'in_washing' => 'badge-primary',
            'washing_completed' => 'badge-success',
            'out_for_delivery' => 'badge-warning',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-secondary',
        };
    }

    /**
     * Get all available workflow statuses with their text labels
     * Useful for filters, select options, etc.
     */
    public static function getAllStatuses(): array
    {
        return [
            'pending_confirmation' => 'Konfirmasi?',
            'confirmed' => 'Terkonfirmasi',
            'picked_up' => 'Dijemput',
            'at_loading_post' => 'Di Pos',
            'in_washing' => 'Dicuci',
            'washing_completed' => 'Siap Antar',
            'out_for_delivery' => 'Mengantar',
            'delivered' => 'Selesai',
            'cancelled' => 'Batal',
        ];
    }

    /**
     * Get workflow status options for select/dropdown
     * Returns array in format ['value' => 'label']
     */
    public static function getStatusOptions(): array
    {
        return self::getAllStatuses();
    }
}
