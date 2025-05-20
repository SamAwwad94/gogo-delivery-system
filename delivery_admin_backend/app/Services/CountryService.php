<?php

namespace App\Services;

use App\Models\Country;
use App\Repositories\CountryRepository;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Country Service for handling country-related business logic
 */
class CountryService
{
    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * CountryService constructor.
     *
     * @param CountryRepository $countryRepository
     * @param CacheService $cacheService
     */
    public function __construct(CountryRepository $countryRepository, CacheService $cacheService)
    {
        $this->countryRepository = $countryRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Get all countries with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllCountries(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Generate cache key based on parameters
        $cacheKey = $this->cacheService->getCollectionKey('countries', [
            'perPage' => $perPage,
            'filters' => $filters,
            'page' => request()->get('page', 1)
        ]);

        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 15, function () use ($perPage, $filters) {
            return $this->countryRepository->getAllWithFilters($perPage, $filters);
        });
    }

    /**
     * Create a new country
     *
     * @param array $data
     * @return Country
     * @throws Exception
     */
    public function createCountry(array $data): Country
    {
        DB::beginTransaction();

        try {
            // Create the country
            $country = $this->countryRepository->create($data);

            // Clear cache
            $this->clearCountryCache();

            DB::commit();
            return $country;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing country
     *
     * @param int $countryId
     * @param array $data
     * @return Country
     * @throws Exception
     */
    public function updateCountry(int $countryId, array $data): Country
    {
        DB::beginTransaction();

        try {
            // Update the country
            $country = $this->countryRepository->update($countryId, $data);

            // Clear cache
            $this->clearCountryCache($countryId);

            DB::commit();
            return $country;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a country
     *
     * @param int $countryId
     * @return bool
     * @throws Exception
     */
    public function deleteCountry(int $countryId): bool
    {
        DB::beginTransaction();

        try {
            $result = $this->countryRepository->deleteById($countryId);

            // Clear cache
            $this->clearCountryCache($countryId);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Clear country cache
     *
     * @param int|null $countryId
     * @return void
     */
    protected function clearCountryCache(?int $countryId = null): void
    {
        // Clear collection cache
        $this->cacheService->clearCollectionCache('countries');

        // Clear specific country cache if ID is provided
        if ($countryId) {
            $this->cacheService->clearModelCache('country', $countryId);
        }

        // Clear active countries cache
        $this->cacheService->forget($this->cacheService->getCollectionKey('countries_active'));

        // Clear countries with cities cache
        $this->cacheService->forget($this->cacheService->getCollectionKey('countries_with_cities'));
    }

    /**
     * Force delete a country
     *
     * @param int $countryId
     * @return bool
     * @throws Exception
     */
    public function forceDeleteCountry(int $countryId): bool
    {
        DB::beginTransaction();

        try {
            $result = $this->countryRepository->forceDelete($countryId);

            // Clear cache
            $this->clearCountryCache($countryId);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore a deleted country
     *
     * @param int $countryId
     * @return bool
     * @throws Exception
     */
    public function restoreCountry(int $countryId): bool
    {
        DB::beginTransaction();

        try {
            $result = $this->countryRepository->restore($countryId);

            // Clear cache
            $this->clearCountryCache($countryId);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get active countries
     *
     * @return Collection
     */
    public function getActiveCountries(): Collection
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getCollectionKey('countries_active');

        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 30, function () {
            return $this->countryRepository->getActive();
        });
    }

    /**
     * Get countries with cities
     *
     * @return Collection
     */
    public function getCountriesWithCities(): Collection
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getCollectionKey('countries_with_cities');

        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 30, function () {
            return $this->countryRepository->getWithCities();
        });
    }

    /**
     * Get countries with order statistics
     *
     * @return array
     */
    public function getCountriesWithOrderStats(): array
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getCollectionKey('countries_order_stats');

        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 15, function () {
            $countries = $this->countryRepository->getActive();

            $countryData = $countries->map(function ($country) {
                $orderStats = DB::table('orders')
                    ->select(
                        DB::raw('COUNT(*) as total'),
                        DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as delivered'),
                        DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled'),
                        DB::raw('SUM(CASE WHEN status IN ("courier_assigned", "courier_arrived", "create", "courier_picked_up", "active", "courier_departed") THEN 1 ELSE 0 END) as in_progress')
                    )
                    ->where('country_id', $country->id)
                    ->first();

                return [
                    'country' => $country->name,
                    'count' => $orderStats->total ?? 0,
                    'color' => '#' . substr(md5($country->name), 0, 6),
                    'in_progress' => $orderStats->in_progress ?? 0,
                    'delivered' => $orderStats->delivered ?? 0,
                    'cancelled' => $orderStats->cancelled ?? 0
                ];
            });

            return collect($countryData)->sortByDesc('count')->values()->all();
        });
    }
}
