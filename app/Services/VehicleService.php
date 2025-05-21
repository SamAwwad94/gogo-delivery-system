<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\Order;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Vehicle Service for handling vehicle-related business logic
 */
class VehicleService
{
    /**
     * @var VehicleRepository
     */
    protected $vehicleRepository;

    /**
     * VehicleService constructor.
     *
     * @param VehicleRepository $vehicleRepository
     */
    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Get all vehicles with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllVehicles(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Vehicle::query();
        
        // Apply status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Apply type filter
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        
        return $query->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    /**
     * Create a new vehicle
     *
     * @param array $data
     * @return Vehicle
     * @throws Exception
     */
    public function createVehicle(array $data): Vehicle
    {
        DB::beginTransaction();
        
        try {
            // Create the vehicle
            $vehicle = $this->vehicleRepository->create($data);
            
            // Upload vehicle image if provided
            if (isset($data['vehicle_image']) && $data['vehicle_image']) {
                uploadMediaFile($vehicle, $data['vehicle_image'], 'vehicle_image');
            }
            
            DB::commit();
            return $vehicle;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing vehicle
     *
     * @param int $vehicleId
     * @param array $data
     * @return Vehicle
     * @throws Exception
     */
    public function updateVehicle(int $vehicleId, array $data): Vehicle
    {
        DB::beginTransaction();
        
        try {
            // Update the vehicle
            $vehicle = $this->vehicleRepository->update($vehicleId, $data);
            
            // Upload vehicle image if provided
            if (isset($data['vehicle_image']) && $data['vehicle_image']) {
                uploadMediaFile($vehicle, $data['vehicle_image'], 'vehicle_image');
            }
            
            DB::commit();
            return $vehicle;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a vehicle
     *
     * @param int $vehicleId
     * @return bool
     * @throws Exception
     */
    public function deleteVehicle(int $vehicleId): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->vehicleRepository->deleteById($vehicleId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Force delete a vehicle
     *
     * @param int $vehicleId
     * @return bool
     * @throws Exception
     */
    public function forceDeleteVehicle(int $vehicleId): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->vehicleRepository->forceDeleteById($vehicleId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore a deleted vehicle
     *
     * @param int $vehicleId
     * @return bool
     * @throws Exception
     */
    public function restoreVehicle(int $vehicleId): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->vehicleRepository->restoreById($vehicleId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get active vehicles
     *
     * @return Collection
     */
    public function getActiveVehicles(): Collection
    {
        return $this->vehicleRepository->getActive();
    }

    /**
     * Get vehicles with delivery man statistics
     *
     * @return array
     */
    public function getVehiclesWithDeliveryManStats(): array
    {
        $vehicles = $this->vehicleRepository->getActive();
        
        $vehicleData = $vehicles->map(function ($vehicle) {
            $deliveryMenCount = User::where('vehicle_id', $vehicle->id)
                ->where('user_type', 'delivery_man')
                ->where('status', 1)
                ->count();
            
            $ordersCount = Order::where('vehicle_id', $vehicle->id)->count();
            
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'type' => $vehicle->type,
                'capacity' => $vehicle->capacity,
                'image' => getSingleMedia($vehicle, 'vehicle_image', null),
                'delivery_men_count' => $deliveryMenCount,
                'orders_count' => $ordersCount
            ];
        });
        
        return $vehicleData->toArray();
    }

    /**
     * Calculate delivery charges based on vehicle
     *
     * @param int $vehicleId
     * @param float $distance
     * @param float $weight
     * @return float
     */
    public function calculateDeliveryCharges(int $vehicleId, float $distance, float $weight): float
    {
        $vehicle = $this->vehicleRepository->findById($vehicleId);
        
        $baseCharge = $vehicle->base_fare ?? 0;
        $perKmCharge = $vehicle->per_km_fare ?? 0;
        $perKgCharge = $vehicle->per_kg_fare ?? 0;
        
        $distanceCharge = $distance * $perKmCharge;
        $weightCharge = $weight * $perKgCharge;
        
        return $baseCharge + $distanceCharge + $weightCharge;
    }
}
