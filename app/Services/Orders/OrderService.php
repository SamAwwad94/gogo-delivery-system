<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderHistory;
use App\Models\User;
use App\Models\City;
use App\Repositories\Orders\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Order Service for handling order-related business logic
 */
class OrderService
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderService constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get all orders with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllOrders(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Order::query();

        // Apply user type filter
        $user = Auth::user();
        if ($user->user_type == 'client') {
            $query->where('client_id', $user->id);
        } elseif ($user->user_type == 'delivery_man') {
            $query->where('delivery_man_id', $user->id);
        }

        // Apply date filters
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            if ($filters['from_date'] == $filters['to_date']) {
                $query->whereDate('created_at', '=', $filters['from_date']);
            } else {
                $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
            }
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        // Apply client filter
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        // Apply delivery man filter
        if (!empty($filters['delivery_man_id'])) {
            $query->where('delivery_man_id', $filters['delivery_man_id']);
        }

        // Apply city filter
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        return $query->with(['client', 'delivery_man', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new order
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $data): Order
    {
        DB::beginTransaction();

        try {
            // Set default values
            $data['date'] = $data['date'] ?? Carbon::now()->format('Y-m-d');
            $data['client_id'] = $data['client_id'] ?? Auth::id();
            $data['status'] = $data['status'] ?? 'create';

            // Create the order
            $order = $this->orderRepository->create($data);

            // Create order history
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'created_by' => Auth::id(),
                'datetime' => Carbon::now(),
            ]);

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing order
     *
     * @param int $orderId
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function updateOrder(int $orderId, array $data): Order
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->findById($orderId);

            // Check if status is changing
            $statusChanged = isset($data['status']) && $data['status'] !== $order->status;

            // Update the order
            $order = $this->orderRepository->update($orderId, $data);

            // Create order history if status changed
            if ($statusChanged) {
                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'created_by' => Auth::id(),
                    'datetime' => Carbon::now(),
                ]);
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign order to delivery man
     *
     * @param int $orderId
     * @param int $deliveryManId
     * @return Order
     * @throws Exception
     */
    public function assignOrder(int $orderId, int $deliveryManId): Order
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->findById($orderId);

            // Update order
            $order = $this->orderRepository->update($orderId, [
                'delivery_man_id' => $deliveryManId,
                'status' => 'courier_assigned',
                'assign_datetime' => Carbon::now(),
            ]);

            // Create order history
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'created_by' => Auth::id(),
                'datetime' => Carbon::now(),
            ]);

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get dashboard statistics
     *
     * @param array $params
     * @return array
     */
    public function getDashboardStatistics(array $params = []): array
    {
        $ordersQuery = Order::query();
        $userQuery = User::query();
        $deliverymanQuery = User::query();

        // Apply date filters
        if (!empty($params['from_date']) && !empty($params['to_date'])) {
            if ($params['from_date'] == $params['to_date']) {
                $ordersQuery->whereDate('created_at', '=', $params['from_date']);
                $userQuery->whereDate('created_at', '=', $params['from_date']);
                $deliverymanQuery->whereDate('created_at', '=', $params['from_date']);
            } else {
                $ordersQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
                $userQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
                $deliverymanQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }

        // Clone queries for different statuses
        $totalOrdersQuery = clone $ordersQuery;
        $totalCreateOrderQuery = clone $ordersQuery;
        $totalAcceptedOrderQuery = clone $ordersQuery;
        $totalAssignedOrderQuery = clone $ordersQuery;
        $totalArrivedOrderQuery = clone $ordersQuery;
        $totalPickupOrderQuery = clone $ordersQuery;
        $totalDepartedOrderQuery = clone $ordersQuery;
        $totalDeliveredOrderQuery = clone $ordersQuery;
        $totalCancelledOrderQuery = clone $ordersQuery;

        $statuses = ['courier_assigned', 'active', 'courier_arrived', 'courier_departed', 'courier_picked_up'];

        return [
            'total_order' => $totalOrdersQuery->count(),
            'total_create_order' => $totalCreateOrderQuery->where('status', 'create')->count(),
            'total_accepetd_order' => $totalAcceptedOrderQuery->where('status', 'active')->count(),
            'total_assigned_order' => $totalAssignedOrderQuery->where('status', 'courier_assigned')->count(),
            'total_arrived_order' => $totalArrivedOrderQuery->where('status', 'courier_arrived')->count(),
            'total_pickup_order' => $totalPickupOrderQuery->where('status', 'courier_picked_up')->count(),
            'total_departed_order' => $totalDepartedOrderQuery->where('status', 'courier_departed')->count(),
            'total_delivered_order' => $totalDeliveredOrderQuery->where('status', 'completed')->count(),
            'total_user' => $userQuery->where('user_type', 'client')->count(),
            'total_delivery_person' => $deliverymanQuery->where('user_type', 'delivery_man')->count(),
            'total_cancelled_order' => $totalCancelledOrderQuery->where('status', 'cancelled')->count(),
            'total_order_today' => Order::whereDate('created_at', Carbon::today())->count(),
            'total_order_today_peding' => Order::whereDate('created_at', Carbon::today())->where('status', 'create')->count(),
            'total_order_today_inprogress' => Order::whereDate('created_at', Carbon::today())->whereIn('status', $statuses)->count(),
            'total_order_today_completed' => Order::whereDate('created_at', Carbon::today())->where('status', 'completed')->count(),
            'total_order_today_cancelled' => Order::whereDate('created_at', Carbon::today())->where('status', 'cancelled')->count(),
            'total_collection_by_order' => Payment::sum('total_amount'),
            'total_admin_comission' => Payment::sum('admin_commission'),
            'total_delivery_comission' => Payment::sum('delivery_man_commission'),
        ];
    }

    /**
     * Get city statistics for dashboard
     *
     * @param array $params
     * @return array
     */
    public function getCityStatistics(array $params = []): array
    {
        $cityQuery = City::where('status', 1);

        // Get city statistics
        $cityOrders = $this->orderRepository->getStatisticsByCity(
            [],
            $params['from_date'] ?? null,
            $params['to_date'] ?? null
        );

        $cityList = $cityQuery->get();

        $cityData = $cityList->map(function ($city) use ($cityOrders) {
            $orders = $cityOrders[$city->id] ?? [
                'total' => 0,
                'delivered' => 0,
                'cancelled' => 0,
                'in_progress' => 0
            ];

            return [
                'city' => $city->name,
                'count' => $orders['total'],
                'color' => '#' . substr(md5($city->name), 0, 6),
                'in_progress' => $orders['in_progress'],
                'delivered' => $orders['delivered'],
                'cancelled' => $orders['cancelled']
            ];
        });

        return collect($cityData)->sortByDesc('count')->values()->all();
    }
}