<?php

namespace App\Repositories;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * City Repository for handling city-related database operations
 */
class CityRepository extends BaseRepository
{
    /**
     * CityRepository constructor.
     *
     * @param City $model
     */
    public function __construct(City $model)
    {
        parent::__construct($model);
    }

    /**
     * Get cities by country
     *
     * @param int $countryId
     * @param array $relations
     * @return Collection
     */
    public function getByCountry(int $countryId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('country_id', $countryId)
            ->get();
    }

    /**
     * Get active cities
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
     * Get cities with order counts
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
     * Search cities by name
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

    /**
     * Get all cities with filters
     *
     * @param int $perPage
     * @param array $filters
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getAllWithFilters(int $perPage = 15, array $filters = [], array $relations = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Apply relations
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply filters
        if (!empty($filters)) {
            // Filter by status
            if (isset($filters['status']) && $filters['status'] !== null && $filters['status'] !== '') {
                $query->where('status', $filters['status']);
            }

            // Filter by country
            if (isset($filters['country_id']) && !empty($filters['country_id'])) {
                $query->where('country_id', $filters['country_id']);
            }

            // Search by name
            if (isset($filters['search']) && !empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'LIKE', "%{$filters['search']}%")
                        ->orWhereHas('country', function ($q2) use ($filters) {
                            $q2->where('name', 'LIKE', "%{$filters['search']}%");
                        });
                });
            }
        }

        // Order by name
        $query->orderBy('name', 'asc');

        return $query->paginate($perPage);
    }

    /**
     * Restore a soft-deleted city
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool
    {
        $city = $this->model->withTrashed()->findOrFail($id);
        return $city->restore();
    }

    /**
     * Force delete a city
     *
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool
    {
        $city = $this->model->withTrashed()->findOrFail($id);
        return $city->forceDelete();
    }
}
