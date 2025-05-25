<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    /**
     * Get orders with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrders(Request $request)
    {
        // Start with a base query
        $query = Order::with(['client', 'deliveryMan']);

        // Apply filters based on user type
        $auth_user = auth()->user();
        if ($auth_user) {
            if ($auth_user->user_type == 'client') {
                $query->where('client_id', $auth_user->id);
            } elseif ($auth_user->user_type == 'delivery_man') {
                $query->where('deliveryman_id', $auth_user->id);
            }
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('milisecond', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Apply Order ID filter
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('id', $request->order_id);
        }

        // Apply Date filter
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('created_at', $request->date);
        }

        // Apply Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Apply Customer filter
        if ($request->has('customer') && !empty($request->customer)) {
            $query->where('client_id', $request->customer);
        }

        // Apply Customer ID filter (new)
        if ($request->has('customer_id') && !empty($request->customer_id)) {
            $query->where('client_id', $request->customer_id);
        }

        // Apply Phone filter (new)
        if ($request->has('phone') && !empty($request->phone)) {
            $phone = $request->phone;
            $query->where(function ($q) use ($phone) {
                // Search in pickup_point and delivery_point JSON for contact_number
                $q->whereRaw("JSON_EXTRACT(pickup_point, '$.contact_number') LIKE ?", ["%{$phone}%"])
                    ->orWhereRaw("JSON_EXTRACT(delivery_point, '$.contact_number') LIKE ?", ["%{$phone}%"]);
            });
        }

        // Apply Location filter
        if ($request->has('location') && !empty($request->location)) {
            $location = $request->location;
            $query->where(function ($q) use ($location) {
                $q->where('pickup_point', 'LIKE', "%{$location}%")
                    ->orWhere('delivery_point', 'LIKE', "%{$location}%");
            });
        }

        // Apply Pickup Location filter (new)
        if ($request->has('pickup_location') && !empty($request->pickup_location)) {
            $location = $request->pickup_location;
            $query->whereRaw("JSON_EXTRACT(pickup_point, '$.address') LIKE ?", ["%{$location}%"]);
        }

        // Apply Delivery Location filter (new)
        if ($request->has('delivery_location') && !empty($request->delivery_location)) {
            $location = $request->delivery_location;
            $query->whereRaw("JSON_EXTRACT(delivery_point, '$.address') LIKE ?", ["%{$location}%"]);
        }

        // Apply Payment Status filter
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }

        // Legacy filters support
        if ($request->has('id') && $request->id) {
            $query->where('id', $request->id);
        }

        if ($request->has('order_code') && $request->order_code) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        if ($request->has('stage') && $request->stage) {
            $query->where('stage', $request->stage);
        }

        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('deliveryman_id') && $request->deliveryman_id) {
            $query->where('deliveryman_id', $request->deliveryman_id);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date filters
        if ($request->has('created_at_from') && $request->created_at_from) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }

        if ($request->has('created_at_to') && $request->created_at_to) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // Location filters
        if ($request->has('pickup_zone') && $request->pickup_zone) {
            $query->where('pickup_point', 'like', '%' . $request->pickup_zone . '%');
        }

        if ($request->has('delivery_zone') && $request->delivery_zone) {
            $query->where('delivery_point', 'like', '%' . $request->delivery_zone . '%');
        }

        // Amount filters
        if ($request->has('amount_from') && $request->amount_from) {
            $query->where('total_amount', '>=', $request->amount_from);
        }

        if ($request->has('amount_to') && $request->amount_to) {
            $query->where('total_amount', '<=', $request->amount_to);
        }

        // Get total count before pagination
        $totalCount = $query->count();

        // Apply pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Apply sorting
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Get paginated results
        $orders = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Format results
        $formattedOrders = $orders->map(function ($order) {
            // Extract phone number from pickup_point
            $pickupPoint = is_string($order->pickup_point) ? json_decode($order->pickup_point, true) : $order->pickup_point;
            $phone = isset($pickupPoint['contact_number']) ? $pickupPoint['contact_number'] : 'N/A';

            // Extract addresses
            $pickupAddress = isset($pickupPoint['address']) ? $pickupPoint['address'] : 'N/A';

            $deliveryPoint = is_string($order->delivery_point) ? json_decode($order->delivery_point, true) : $order->delivery_point;
            $deliveryAddress = isset($deliveryPoint['address']) ? $deliveryPoint['address'] : 'N/A';

            return [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'stage' => $order->stage ?? 'pending',
                'status' => $order->status,
                'status_badge' => $this->getStatusBadge($order->status),
                'client_id' => $order->client_id,
                'customer_name' => optional($order->client)->name,
                'client_name' => optional($order->client)->name,
                'deliveryman_id' => $order->deliveryman_id,
                'delivery_man_name' => optional($order->deliveryMan)->name,
                'payment_status' => $order->payment_status,
                'payment_status_badge' => $this->getPaymentStatusBadge($order->payment_status),
                'payment_method' => $order->payment_method,
                'total_amount' => $order->total_amount,
                'formatted_amount' => $this->formatAmount($order->total_amount, $order->currency),
                'currency' => $order->currency,
                'pickup_location' => $pickupAddress,
                'delivery_location' => $deliveryAddress,
                'phone' => $phone,
                'created_at' => \Carbon\Carbon::parse($order->created_at)->format('M d, Y H:i'),

                'actions' => $this->getActionButtons($order)
            ];
        });

        return response()->json([
            'data' => $formattedOrders,
            'meta' => [
                'total' => $totalCount,
                'per_page' => (int) $perPage,
                'current_page' => (int) $page,
                'last_page' => ceil($totalCount / $perPage),
                'from' => (($page - 1) * $perPage) + 1,
                'to' => min($page * $perPage, $totalCount)
            ]
        ]);
    }

    /**
     * Get clients
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClients()
    {
        $clients = User::where('user_type', 'client')
            ->where('status', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($clients);
    }

    /**
     * Get status badge HTML
     *
     * @param string $status
     * @return string
     */
    private function getStatusBadge($status)
    {
        $badgeClass = '';

        switch ($status) {
            case 'pending':
                $badgeClass = 'bg-warning/10 text-warning ring-warning/20';
                break;
            case 'in_progress':
                $badgeClass = 'bg-info/10 text-info ring-info/20';
                break;
            case 'completed':
                $badgeClass = 'bg-success/10 text-success ring-success/20';
                break;
            case 'cancelled':
                $badgeClass = 'bg-destructive/10 text-destructive ring-destructive/20';
                break;
            default:
                $badgeClass = 'bg-muted/10 text-muted-foreground ring-muted/20';
        }

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ' . $badgeClass . '">' . ucfirst($status) . '</span>';
    }

    /**
     * Get payment status badge HTML
     *
     * @param string $status
     * @return string
     */
    private function getPaymentStatusBadge($status)
    {
        $badgeClass = '';

        switch ($status) {
            case 'paid':
                $badgeClass = 'bg-success/10 text-success ring-success/20';
                break;
            case 'unpaid':
                $badgeClass = 'bg-destructive/10 text-destructive ring-destructive/20';
                break;
            case 'partial':
                $badgeClass = 'bg-warning/10 text-warning ring-warning/20';
                break;
            default:
                $badgeClass = 'bg-muted/10 text-muted-foreground ring-muted/20';
        }

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ' . $badgeClass . '">' . ucfirst($status) . '</span>';
    }

    /**
     * Format amount with currency
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    private function formatAmount($amount, $currency)
    {
        $symbol = '';

        switch ($currency) {
            case 'USD':
                $symbol = '$';
                break;
            case 'LBP':
                $symbol = 'L£';
                break;
            default:
                $symbol = '$';
        }

        return $symbol . number_format($amount, 2);
    }

    /**
     * Get action buttons HTML
     *
     * @param Order $order
     * @return string
     */
    private function getActionButtons($order)
    {
        $action = '<div class="flex items-center space-x-2">';

        // View button
        $action .= '<a href="' . route('order.show', $order->id) . '" class="action-button" data-tooltip="View">
            <i class="fas fa-eye text-primary"></i>
        </a>';

        // Edit button
        $action .= '<a href="' . route('order.edit', $order->id) . '" class="action-button" data-tooltip="Edit">
            <i class="fas fa-edit text-secondary"></i>
        </a>';

        // Delete button
        $action .= '<a href="javascript:void(0)" class="action-button delete-order" data-id="' . $order->id . '" data-tooltip="Delete">
            <i class="fas fa-trash text-destructive"></i>
        </a>';

        $action .= '</div>';

        return $action;
    }
}
