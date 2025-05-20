<?php

namespace App\Repositories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Country Repository for handling country-related database operations
 */
class CountryRepository extends BaseRepository
{
    /**
     * CountryRepository constructor.
     *
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active countries
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
     * Get countries with cities
     *
     * @return Collection
     */
    public function getWithCities(): Collection
    {
        return $this->model->with([
            'cities' => function ($query) {
                $query->where('status', 1);
            }
        ])
            ->where('status', 1)
            ->get();
    }

    /**
     * Get countries with order counts
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
     * Search countries by name
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
     * Get all countries with filters
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

            // Filter by distance type
            if (isset($filters['distance_type']) && !empty($filters['distance_type'])) {
                $query->where('distance_type', $filters['distance_type']);
            }

            // Search by name
            if (isset($filters['search']) && !empty($filters['search'])) {
                $query->where('name', 'LIKE', "%{$filters['search']}%");
            }
        }

        // Add with counts for better performance
        if (isset($filters['with_counts']) && $filters['with_counts']) {
            $query->withCount(['cities', 'orders']);
        }

        // Order by name
        $query->orderBy('name', 'asc');

        return $query->paginate($perPage);
    }

    /**
     * Restore a soft-deleted country
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool
    {
        $country = $this->model->withTrashed()->findOrFail($id);
        return $country->restore();
    }

    /**
     * Force delete a country
     *
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool
    {
        $country = $this->model->withTrashed()->findOrFail($id);
        return $country->forceDelete();
    }
}
