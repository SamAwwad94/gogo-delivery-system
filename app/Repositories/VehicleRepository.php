<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

/**
 * Vehicle Repository for handling vehicle-related database operations
 */
class VehicleRepository extends BaseRepository
{
    /**
     * VehicleRepository constructor.
     *
     * @param Vehicle $model
     */
    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active vehicles
     *
     * @param array $relations
     * @return Collection
     */
    public function getActive(array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('status', 1)
            ->get();
    }

    /**
     * Get vehicles by type
     *
     * @param string $type
     * @param array $relations
     * @return Collection
     */
    public function getByType(string $type, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('type', $type)
            ->get();
    }

    /**
     * Get vehicles with delivery man counts
     *
     * @param array $relations
     * @return Collection
     */
    public function getWithDeliveryManCounts(array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->withCount(['deliveryMen' => function ($query) {
                $query->where('status', 1);
            }])
            ->orderBy('delivery_men_count', 'desc')
            ->get();
    }

    /**
     * Get vehicles with order counts
     *
     * @param array $relations
     * @return Collection
     */
    public function getWithOrderCounts(array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->get();
    }

    /**
     * Search vehicles by name
     *
     * @param string $name
     * @param array $relations
     * @return Collection
     */
    public function searchByName(string $name, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('name', 'like', "%{$name}%")
            ->get();
    }
}
