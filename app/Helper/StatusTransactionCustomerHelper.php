<?php

declare(strict_types=1);

namespace App\Helper;

class StatusTransactionCustomerHelper
{
    /**
     * Get customer-friendly status text from workflow status code
     */
    public static function getStatusText(string $workflowStatus): string
    {
        return match ($workflowStatus) {
            'pending_confirmation' => 'Pending',
            'confirmed' => 'Dikonfirmasi',
            'picked_up', 'at_loading_post' => 'Diproses',
            'in_washing', 'washing_completed' => 'Dicuci',
            'out_for_delivery' => 'Diantar',
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
            'picked_up', 'at_loading_post' => 'badge-warning',
            'in_washing', 'washing_completed' => 'badge-primary',
            'out_for_delivery' => 'badge-warning',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-secondary',
        };
    }

    /**
     * Get all available customer-facing statuses with their text labels
     */
    public static function getAllStatuses(): array
    {
        return [
            'pending_confirmation' => 'Pending',
            'confirmed' => 'Dikonfirmasi',
            'picked_up' => 'Diproses',
            'in_washing' => 'Dicuci',
            'out_for_delivery' => 'Diantar',
            'delivered' => 'Selesai',
            'cancelled' => 'Batal',
        ];
    }
}
