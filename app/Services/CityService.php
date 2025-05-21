<?php

namespace App\Services;

use App\Models\City;
use App\Repositories\CityRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * City Service for handling city-related business logic
 */
class CityService
{
    /**
     * @var CityRepository
     */
    protected $cityRepository;

    /**
     * CityService constructor.
     *
     * @param CityRepository $cityRepository
     */
    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * Get all cities with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllCities(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->cityRepository->getAllWithFilters($perPage, $filters, ['country']);
    }

    /**
     * Create a new city
     *
     * @param array $data
     * @return City
     * @throws Exception
     */
    public function createCity(array $data): City
    {
        DB::beginTransaction();

        try {
            // Create the city
            $city = $this->cityRepository->create($data);

            DB::commit();
            return $city;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing city
     *
     * @param int $cityId
     * @param array $data
     * @return City
     * @throws Exception
     */
    public function updateCity(int $cityId, array $data): City
    {
        DB::beginTransaction();

        try {
            // Update the city
            $city = $this->cityRepository->update($cityId, $data);

            DB::commit();
            return $city;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a city
     *
     * @param int $cityId
     * @return bool
     * @throws Exception
     */
    public function deleteCity(int $cityId): bool
    {
        DB::beginTransaction();

        try {
            $result = $this->cityRepository->deleteById($cityId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Force delete a city
     *
     * @param int $cityId
     * @return bool
     * @throws Exception
     */
    public function forceDeleteCity(int $cityId): bool
    {
        DB::beginTransaction();

        try {
            $result = $this->cityRepository->forceDelete($cityId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore a deleted city
     *
     * @param int $cityId
     * @return bool
     * @throws Exception
     */
    public function restoreCity(int $cityId): bool
    {
        DB::beginTransaction();

        try {
            $result = $this->cityRepository->restore($cityId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get cities by country
     *
     * @param int $countryId
     * @return Collection
     */
    public function getCitiesByCountry(int $countryId): Collection
    {
        return $this->cityRepository->getByCountry($countryId);
    }

    /**
     * Get active cities
     *
     * @return Collection
     */
    public function getActiveCities(): Collection
    {
        return $this->cityRepository->getActive();
    }

    /**
     * Get cities with order statistics
     *
     * @return array
     */
    public function getCitiesWithOrderStats(): array
    {
        $cities = $this->cityRepository->getActive(['country']);

        $cityData = $cities->map(function ($city) {
            $orderStats = DB::table('orders')
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as delivered'),
                    DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled'),
                    DB::raw('SUM(CASE WHEN status IN ("courier_assigned", "courier_arrived", "create", "courier_picked_up", "active", "courier_departed") THEN 1 ELSE 0 END) as in_progress')
                )
                ->where('city_id', $city->id)
                ->first();

            return [
                'city' => $city->name,
                'country' => $city->country->name ?? '',
                'count' => $orderStats->total ?? 0,
                'color' => '#' . substr(md5($city->name), 0, 6),
                'in_progress' => $orderStats->in_progress ?? 0,
                'delivered' => $orderStats->delivered ?? 0,
                'cancelled' => $orderStats->cancelled ?? 0
            ];
        });

        return collect($cityData)->sortByDesc('count')->values()->all();
    }
}
