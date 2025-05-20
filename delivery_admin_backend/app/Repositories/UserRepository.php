<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * User Repository for handling user-related database operations
 */
class UserRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get users by type
     *
     * @param string $userType
     * @param array $relations
     * @return Collection
     */
    public function getByType(string $userType, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('user_type', $userType)
            ->get();
    }

    /**
     * Get users by status
     *
     * @param int $status
     * @param array $relations
     * @return Collection
     */
    public function getByStatus(int $status, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('status', $status)
            ->get();
    }

    /**
     * Get users by date range
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
     * Get users by city
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
     * Get users by country
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
     * Get nearby delivery personnel
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $cityId
     * @param int $vehicleId
     * @param float $radius
     * @param string $unit
     * @return Collection
     */
    public function getNearbyDeliveryPersonnel(
        float $latitude,
        float $longitude,
        int $cityId,
        int $vehicleId,
        float $radius = 10,
        string $unit = 'km'
    ): Collection {
        $unitValue = $unit == 'km' ? 6371 : 3959;
        
        return $this->model->selectRaw("id, user_type, vehicle_id, latitude, longitude, 
                ( $unitValue * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) 
                * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) 
                * sin( radians( latitude ) ) ) ) AS distance")
            ->where('city_id', $cityId)
            ->where('status', 1)
            ->where('user_type', 'delivery_man')
            ->where('vehicle_id', $vehicleId)
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->where(function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->whereNotNull('otp_verify_at')
                    ->whereNotNull('document_verified_at');
            })
            ->get();
    }

    /**
     * Get user count by type
     *
     * @param string|null $userType
     * @return int
     */
    public function getUserCount(?string $userType = null): int
    {
        $query = $this->model->query();
        
        if ($userType) {
            $query->where('user_type', $userType);
        }
        
        return $query->count();
    }

    /**
     * Get admin user
     *
     * @return User|null
     */
    public function getAdmin(): ?User
    {
        return $this->model->where('user_type', 'admin')->first();
    }
}
