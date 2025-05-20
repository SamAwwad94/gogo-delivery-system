<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserBankAccount;
use App\Models\Wallet;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * User Service for handling user-related business logic
 */
class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = User::query();
        
        // Apply user type filter
        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }
        
        // Apply status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Apply date filters
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            if ($filters['from_date'] == $filters['to_date']) {
                $query->whereDate('created_at', '=', $filters['from_date']);
            } else {
                $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
            }
        }
        
        // Apply city filter
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }
        
        // Apply country filter
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }
        
        return $query->with(['country', 'city'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            // Create the user
            $user = $this->userRepository->create($data);
            
            // Create wallet for the user
            if (in_array($data['user_type'], ['client', 'delivery_man'])) {
                Wallet::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                ]);
            }
            
            // Assign role if provided
            if (!empty($data['role'])) {
                $user->assignRole($data['role']);
            }
            
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing user
     *
     * @param int $userId
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function updateUser(int $userId, array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            // Update the user
            $user = $this->userRepository->update($userId, $data);
            
            // Update role if provided
            if (!empty($data['role'])) {
                $user->syncRoles([$data['role']]);
            }
            
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a user
     *
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function deleteUser(int $userId): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->userRepository->deleteById($userId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Force delete a user
     *
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function forceDeleteUser(int $userId): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->userRepository->forceDeleteById($userId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore a deleted user
     *
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function restoreUser(int $userId): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->userRepository->restoreById($userId);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Save user bank account details
     *
     * @param int $userId
     * @param array $bankData
     * @return UserBankAccount
     * @throws Exception
     */
    public function saveBankAccount(int $userId, array $bankData): UserBankAccount
    {
        DB::beginTransaction();
        
        try {
            $bankData['user_id'] = $userId;
            
            $bankAccount = UserBankAccount::updateOrCreate(
                ['user_id' => $userId],
                $bankData
            );
            
            DB::commit();
            return $bankAccount;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Change user password
     *
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     * @throws Exception
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!Hash::check($currentPassword, $user->password)) {
            throw new Exception(__('message.current_password_not_match'));
        }
        
        $user->password = Hash::make($newPassword);
        return $user->save();
    }
}
