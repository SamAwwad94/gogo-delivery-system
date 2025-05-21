<?php

namespace App\Services;

use App\Models\DeliveryManDocument;
use App\Models\Document;
use App\Models\User;
use App\Repositories\DeliveryManRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeliveryManService
{
    /**
     * @var DeliveryManRepository
     */
    protected $deliveryManRepository;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * DeliveryManService constructor.
     *
     * @param DeliveryManRepository $deliveryManRepository
     * @param CacheService $cacheService
     */
    public function __construct(DeliveryManRepository $deliveryManRepository, CacheService $cacheService)
    {
        $this->deliveryManRepository = $deliveryManRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Get all delivery men with filters
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllDeliveryMen(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Generate cache key based on parameters
        $cacheKey = $this->cacheService->getCollectionKey('delivery_men', [
            'perPage' => $perPage,
            'filters' => $filters,
            'page' => request()->get('page', 1)
        ]);
        
        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 5, function () use ($perPage, $filters) {
            return $this->deliveryManRepository->getAllWithFilters($perPage, $filters, ['country', 'city']);
        });
    }

    /**
     * Get delivery man by ID
     *
     * @param int $id
     * @return User
     */
    public function getById(int $id): User
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getModelKey('delivery_man', $id);
        
        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 15, function () use ($id) {
            return $this->deliveryManRepository->findById($id, ['country', 'city', 'userWallet', 'userBankAccount']);
        });
    }

    /**
     * Create a new delivery man
     *
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function createDeliveryMan(array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Set default values
            $data['password'] = bcrypt($data['password']);
            $data['username'] = $data['username'] ?? stristr($data['email'], "@", true) . rand(100, 1000);
            $data['display_name'] = $data['name'];
            $data['user_type'] = 'delivery_man';
            $data['referral_code'] = generateRandomCode();
            
            // Handle verification settings
            $is_email_verification = SettingData('email_verification', 'email_verification');
            $is_mobile_verification = SettingData('mobile_verification', 'mobile_verification');
            $is_document_verification = SettingData('document_verification', 'document_verification');
            
            if ($is_email_verification == 0) {
                $data['email_verified_at'] = now();
            }
            
            if ($is_mobile_verification == 0) {
                $data['otp_verify_at'] = now();
            }
            
            if ($is_document_verification == 0) {
                $data['document_verified_at'] = now();
            }
            
            // Create the delivery man
            $deliveryMan = $this->deliveryManRepository->create($data);
            
            // Assign role
            $deliveryMan->assignRole('delivery_man');
            
            // Clear cache
            $this->clearDeliveryManCache();
            
            DB::commit();
            return $deliveryMan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing delivery man
     *
     * @param int $id
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function updateDeliveryMan(int $id, array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Get the delivery man
            $deliveryMan = $this->deliveryManRepository->findById($id);
            
            // Remove old role
            $deliveryMan->removeRole($deliveryMan->user_type);
            
            // Handle password
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            
            // Update the delivery man
            $deliveryMan = $this->deliveryManRepository->update($id, $data);
            
            // Assign role
            $deliveryMan->assignRole('delivery_man');
            
            // Clear cache
            $this->clearDeliveryManCache($id);
            
            DB::commit();
            return $deliveryMan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a delivery man
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deleteDeliveryMan(int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->deliveryManRepository->deleteById($id);
            
            // Clear cache
            $this->clearDeliveryManCache($id);
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore a soft-deleted delivery man
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function restoreDeliveryMan(int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->deliveryManRepository->restore($id);
            
            // Clear cache
            $this->clearDeliveryManCache($id);
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Force delete a delivery man
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function forceDeleteDeliveryMan(int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->deliveryManRepository->forceDelete($id);
            
            // Clear cache
            $this->clearDeliveryManCache($id);
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update delivery man verification status
     *
     * @param int $id
     * @param string $type
     * @return User
     * @throws Exception
     */
    public function updateVerification(int $id, string $type): User
    {
        DB::beginTransaction();
        
        try {
            $deliveryMan = $this->deliveryManRepository->findById($id);
            
            switch ($type) {
                case 'email':
                    $deliveryMan->is_autoverified_email = 0;
                    $deliveryMan->email_verified_at = null;
                    break;
                case 'mobile':
                    $deliveryMan->is_autoverified_mobile = 0;
                    $deliveryMan->otp_verify_at = null;
                    break;
                case 'document':
                    $deliveryMan->is_autoverified_document = 0;
                    $deliveryMan->document_verified_at = null;
                    
                    // Delete documents
                    $documents = DeliveryManDocument::where('delivery_man_id', $deliveryMan->id)->get();
                    foreach ($documents as $document) {
                        $document->delete();
                    }
                    break;
                default:
                    throw new Exception('Invalid verification type');
            }
            
            $deliveryMan->save();
            
            // Clear cache
            $this->clearDeliveryManCache($id);
            
            DB::commit();
            return $deliveryMan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get active delivery men
     *
     * @return Collection
     */
    public function getActiveDeliveryMen(): Collection
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getCollectionKey('delivery_men_active');
        
        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 15, function () {
            return $this->deliveryManRepository->getActive();
        });
    }

    /**
     * Get online delivery men
     *
     * @return Collection
     */
    public function getOnlineDeliveryMen(): Collection
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getCollectionKey('delivery_men_online');
        
        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 5, function () {
            return $this->deliveryManRepository->getOnline();
        });
    }

    /**
     * Get available delivery men
     *
     * @return Collection
     */
    public function getAvailableDeliveryMen(): Collection
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getCollectionKey('delivery_men_available');
        
        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 5, function () {
            return $this->deliveryManRepository->getAvailable();
        });
    }

    /**
     * Get required documents
     *
     * @return Collection
     */
    public function getRequiredDocuments(): Collection
    {
        // Generate cache key
        $cacheKey = $this->cacheService->getCollectionKey('required_documents');
        
        // Get data from cache or repository
        return $this->cacheService->remember($cacheKey, 60 * 60, function () {
            return Document::where('is_required', 1)
                ->where('status', 1)
                ->get();
        });
    }

    /**
     * Clear delivery man cache
     *
     * @param int|null $deliveryManId
     * @return void
     */
    protected function clearDeliveryManCache(?int $deliveryManId = null): void
    {
        // Clear collection cache
        $this->cacheService->clearCollectionCache('delivery_men');
        
        // Clear specific delivery man cache if ID is provided
        if ($deliveryManId) {
            $this->cacheService->clearModelCache('delivery_man', $deliveryManId);
        }
        
        // Clear active delivery men cache
        $this->cacheService->forget($this->cacheService->getCollectionKey('delivery_men_active'));
        
        // Clear online delivery men cache
        $this->cacheService->forget($this->cacheService->getCollectionKey('delivery_men_online'));
        
        // Clear available delivery men cache
        $this->cacheService->forget($this->cacheService->getCollectionKey('delivery_men_available'));
    }
}
