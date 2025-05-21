<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRoute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryRouteApiController extends Controller
{
    /**
     * Get delivery routes with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeliveryRoutes(Request $request)
    {
        $query = DeliveryRoute::with(['deliveryMan', 'orders']);

        // Apply filters
        // Basic filters
        if ($request->has('id') && $request->id) {
            $query->where('id', $request->id);
        }

        if ($request->has('code') && $request->code) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        if ($request->has('stage') && $request->stage) {
            $query->where('stage', $request->stage);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('route') && $request->route) {
            $query->where('name', 'like', '%' . $request->route . '%');
        }

        if ($request->has('services') && $request->services) {
            $query->where('service_type', $request->services);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Date filters
        if ($request->has('created_at_from') && $request->created_at_from) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }

        if ($request->has('created_at_to') && $request->created_at_to) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        if ($request->has('updated_at_from') && $request->updated_at_from) {
            $query->whereDate('updated_at', '>=', $request->updated_at_from);
        }

        if ($request->has('updated_at_to') && $request->updated_at_to) {
            $query->whereDate('updated_at', '<=', $request->updated_at_to);
        }

        // Location filters
        if ($request->has('pickup_zone') && $request->pickup_zone) {
            $query->where('start_location', 'like', '%' . $request->pickup_zone . '%');
        }

        if ($request->has('delivery_zone') && $request->delivery_zone) {
            $query->where('end_location', 'like', '%' . $request->delivery_zone . '%');
        }

        if ($request->has('delivery_method') && $request->delivery_method) {
            $query->where('delivery_method', $request->delivery_method);
        }

        // Legacy filters for backward compatibility
        if ($request->has('deliveryman_id') && $request->deliveryman_id) {
            $query->where('deliveryman_id', $request->deliveryman_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get results
        $routes = $query->orderBy('id', 'desc')->get();

        // Format results
        $formattedRoutes = $routes->map(function ($route) {
            return [
                'id' => $route->id,
                'code' => $route->code ?? 'R-' . $route->id,
                'name' => $route->name,
                'stage' => $route->stage ?? 'pending',
                'start_location' => $route->start_location,
                'end_location' => $route->end_location,
                'status' => $route->status,
                'status_badge' => $route->status_badge,
                'delivery_man' => optional($route->deliveryMan)->name,
                'delivery_man_id' => $route->deliveryman_id,
                'service_type' => $route->service_type ?? 'standard',
                'payment_status' => $route->payment_status ?? 'pending',
                'delivery_method' => $route->delivery_method ?? 'standard',
                'orders_count' => $route->orders->count(),
                'created_at' => $route->created_at->format('M d, Y H:i'),
                'updated_at' => $route->updated_at->format('M d, Y H:i'),
                'actions' => $this->getActionButtons($route)
            ];
        });

        return response()->json([
            'data' => $formattedRoutes
        ]);
    }

    /**
     * Get delivery men
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeliveryMen()
    {
        $deliveryMen = User::where('user_type', 'delivery_man')
            ->where('status', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($deliveryMen);
    }

    /**
     * Get delivery man location
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeliveryManLocation($id)
    {
        $deliveryMan = User::where('user_type', 'delivery_man')
            ->where('id', $id)
            ->first();

        if (!$deliveryMan) {
            return response()->json(['error' => 'Delivery man not found'], 404);
        }

        // In a real application, you would get the actual location from a tracking service
        // For demo purposes, we'll return a random location near the route
        $route = DeliveryRoute::where('deliveryman_id', $id)
            ->where('status', 'in_progress')
            ->first();

        if (!$route) {
            return response()->json(['error' => 'No active route found for this delivery man'], 404);
        }

        // Parse start location coordinates (assuming format like "33.8547, 35.8623")
        $startCoords = explode(',', $route->start_location);
        $lat = trim($startCoords[0]);
        $lng = isset($startCoords[1]) ? trim($startCoords[1]) : 0;

        // Add some random variation for simulation
        $lat = floatval($lat) + (mt_rand(-10, 10) / 1000);
        $lng = floatval($lng) + (mt_rand(-10, 10) / 1000);

        return response()->json([
            'id' => $deliveryMan->id,
            'name' => $deliveryMan->name,
            'latitude' => $lat,
            'longitude' => $lng,
            'last_updated' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get action buttons HTML
     *
     * @param DeliveryRoute $route
     * @return string
     */
    private function getActionButtons($route)
    {
        $action = '<div class="flex items-center space-x-2">';

        // View button
        $action .= '<a href="' . route('delivery-routes.show', $route->id) . '" class="action-button" data-tooltip="View">
            <i class="fas fa-eye text-primary"></i>
        </a>';

        // Map button
        $action .= '<a href="' . route('delivery-routes.map', $route->id) . '" class="action-button" data-tooltip="View Map">
            <i class="fas fa-map-marked-alt text-info"></i>
        </a>';

        // Edit button
        $action .= '<a href="' . route('delivery-routes.edit', $route->id) . '" class="action-button" data-tooltip="Edit">
            <i class="fas fa-edit text-secondary"></i>
        </a>';

        // Delete button
        $action .= '<a href="javascript:void(0)" class="action-button delete-route" data-id="' . $route->id . '" data-tooltip="Delete">
            <i class="fas fa-trash text-destructive"></i>
        </a>';

        $action .= '</div>';

        return $action;
    }
}
