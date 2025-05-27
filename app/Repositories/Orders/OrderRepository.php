<?php

namespace App\Repositories\Orders;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use App\Repositories\BaseRepository; // Assuming BaseRepository is in App\Repositories

/**
 * Order Repository for handling order-related database operations
 */
class OrderRepository extends BaseRepository
{
    /**
     * OrderRepository constructor.
     *
     * @param Order $model
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get orders by status
     *
     * @param string|array $status
     * @param array $relations
     * @return Collection
     */
    public function getByStatus($status, array $relations = []): Collection
    {
        $query = $this->model->with($relations);

        if (is_array($status)) {
            return $query->whereIn('status', $status)->get();
        }

        return $query->where('status', $status)->get();
    }

    /**
     * Get orders created today
     *
     * @param array $relations
     * @return Collection
     */
    public function getTodayOrders(array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->whereDate('created_at', Carbon::today())
            ->get();
    }

    /**
     * Get orders by date range
     *
     * @param string $fromDate
     * @param string $toDate
     * @param array $relations
     * @return Collection
     */
    public function getByDateRange(string $fromDate, string $toDate, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();
    }

    /**
     * Get orders by client
     *
     * @param int $clientId
     * @param array $relations
     * @return Collection
     */
    public function getByClient(int $clientId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('client_id', $clientId)
            ->get();
    }

    /**
     * Get orders by delivery man
     *
     * @param int $deliveryManId
     * @param array $relations
     * @return Collection
     */
    public function getByDeliveryMan(int $deliveryManId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('delivery_man_id', $deliveryManId)
            ->get();
    }

    /**
     * Get orders by city
     *
     * @param int $cityId
     * @param array $relations
     * @return Collection
     */
    public function getByCity(int $cityId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('city_id', $cityId)
            ->get();
    }

    /**
     * Get orders statistics by city
     *
     * @param array $cityIds
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return array
     */
    public function getStatisticsByCity(array $cityIds = [], ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = $this->model->selectRaw('
            city_id,
            COUNT(*) as total,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as delivered,
            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN status IN ("courier_assigned", "courier_arrived", "create", "courier_picked_up", "active", "courier_departed") THEN 1 ELSE 0 END) as in_progress
        ');

        if (!empty($cityIds)) {
            $query->whereIn('city_id', $cityIds);
        }

        if ($fromDate && $toDate) {
            if ($fromDate == $toDate) {
                $query->whereDate('created_at', '=', $fromDate);
            } else {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
        }

        return $query->groupBy('city_id')
            ->orderBy('total', 'desc')
            ->get()
            ->keyBy('city_id')
            ->toArray();
    }
}