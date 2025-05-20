<?php

namespace App\Helpers;

/**
 * Helper functions for orders
 */
class OrderHelpers
{
    /**
     * Get the CSS class for an order status
     *
     * @param string $status
     * @return string
     */
    public static function getOrderStatusClass(string $status): string
    {
        switch ($status) {
            case 'draft':
                return 'light';
            case 'create':
                return 'primary';
            case 'active':
                return 'info';
            case 'courier_assigned':
                return 'warning';
            case 'courier_arrived':
                return 'info';
            case 'courier_picked_up':
                return 'info';
            case 'courier_departed':
                return 'info';
            case 'completed':
                return 'success';
            case 'cancelled':
                return 'danger';
            case 'failed':
                return 'danger';
            case 'delayed':
                return 'warning';
            default:
                return 'primary';
        }
    }
}
