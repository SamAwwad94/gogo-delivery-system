<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * DeliveryMan Repository for handling delivery man-related database operations
 */
class DeliveryManRepository extends BaseRepository
{
    /**
     * DeliveryManRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get delivery men by status
     *
     * @param int $status
     * @param array $relations
     * @return Collection
     */
    public function getByStatus(int $status, array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where('status', $status)
            ->with($relations)
            ->get();
    }

    /**
     * Get active delivery men
     *
     * @param array $relations
     * @return Collection
     */
    public function getActive(array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where('status', 1)
            ->with($relations)
            ->get();
    }

    /**
     * Get inactive delivery men
     *
     * @param array $relations
     * @return Collection
     */
    public function getInactive(array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where('status', 0)
            ->with($relations)
            ->get();
    }

    /**
     * Get pending delivery men
     *
     * @param array $relations
     * @return Collection
     */
    public function getPending(array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->whereNull('document_verified_at')
            ->with($relations)
            ->get();
    }

    /**
     * Get online delivery men
     *
     * @param array $relations
     * @return Collection
     */
    public function getOnline(array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where('is_online', 1)
            ->with($relations)
            ->get();
    }

    /**
     * Get available delivery men
     *
     * @param array $relations
     * @return Collection
     */
    public function getAvailable(array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where('is_online', 1)
            ->where('is_available', 1)
            ->with($relations)
            ->get();
    }

    /**
     * Get delivery men by city
     *
     * @param int $cityId
     * @param array $relations
     * @return Collection
     */
    public function getByCity(int $cityId, array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where('city_id', $cityId)
            ->with($relations)
            ->get();
    }

    /**
     * Get delivery men by country
     *
     * @param int $countryId
     * @param array $relations
     * @return Collection
     */
    public function getByCountry(int $countryId, array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where('country_id', $countryId)
            ->with($relations)
            ->get();
    }

    /**
     * Search delivery men by name or email
     *
     * @param string $search
     * @param array $relations
     * @return Collection
     */
    public function search(string $search, array $relations = []): Collection
    {
        return $this->model->where('user_type', 'delivery_man')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('contact_number', 'LIKE', "%{$search}%");
            })
            ->with($relations)
            ->get();
    }

    /**
     * Get all delivery men with filters
     *
     * @param int $perPage
     * @param array $filters
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getAllWithFilters(int $perPage = 15, array $filters = [], array $relations = []): LengthAwarePaginator
    {
        $query = $this->model->where('user_type', 'delivery_man');

        // Apply relations
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply filters
        if (!empty($filters)) {
            // Filter by status
            if (isset($filters['status']) && $filters['status'] !== null && $filters['status'] !== '') {
                if ($filters['status'] === 'pending') {
                    $query->whereNull('document_verified_at');
                } elseif ($filters['status'] === 'active') {
                    $query->where('status', 1);
                } elseif ($filters['status'] === 'inactive') {
                    $query->where('status', 0);
                }
            }

            // Filter by city
            if (isset($filters['city_id']) && !empty($filters['city_id'])) {
                $query->where('city_id', $filters['city_id']);
            }

            // Filter by country
            if (isset($filters['country_id']) && !empty($filters['country_id'])) {
                $query->where('country_id', $filters['country_id']);
            }

            // Filter by last active time
            if (isset($filters['last_actived_at']) && !empty($filters['last_actived_at'])) {
                $query->whereDate('last_active_time', $filters['last_actived_at']);
            }

            // Filter by online status
            if (isset($filters['is_online']) && $filters['is_online'] !== null) {
                $query->where('is_online', $filters['is_online']);
            }

            // Filter by availability
            if (isset($filters['is_available']) && $filters['is_available'] !== null) {
                $query->where('is_available', $filters['is_available']);
            }

            // Search by name, email, or contact number
            if (isset($filters['search']) && !empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'LIKE', "%{$filters['search']}%")
                        ->orWhere('email', 'LIKE', "%{$filters['search']}%")
                        ->orWhere('contact_number', 'LIKE', "%{$filters['search']}%");
                });
            }
        }

        // Order by ID descending
        $query->orderBy('id', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Restore a soft-deleted delivery man
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool
    {
        $deliveryMan = $this->model->withTrashed()->findOrFail($id);
        return $deliveryMan->restore();
    }

    /**
     * Force delete a delivery man
     *
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool
    {
        $deliveryMan = $this->model->withTrashed()->findOrFail($id);
        return $deliveryMan->forceDelete();
    }
}
