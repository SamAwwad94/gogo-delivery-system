<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\User;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Wallet Service for handling wallet-related business logic
 */
class WalletService
{
    /**
     * @var WalletRepository
     */
    protected $walletRepository;

    /**
     * WalletService constructor.
     *
     * @param WalletRepository $walletRepository
     */
    public function __construct(WalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    /**
     * Get wallet by user
     *
     * @param int $userId
     * @return Wallet|null
     */
    public function getWalletByUser(int $userId): ?Wallet
    {
        return $this->walletRepository->getByUser($userId, ['user']);
    }

    /**
     * Get wallet history with pagination
     *
     * @param int $userId
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getWalletHistory(int $userId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = WalletHistory::where('user_id', $userId);
        
        // Apply transaction type filter
        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }
        
        // Apply type filter (credit/debit)
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Apply date filters
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            if ($filters['from_date'] == $filters['to_date']) {
                $query->whereDate('created_at', '=', $filters['from_date']);
            } else {
                $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
            }
        }
        
        return $query->with(['order', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Add money to wallet
     *
     * @param int $userId
     * @param float $amount
     * @param string $transactionType
     * @param string $description
     * @param int|null $orderId
     * @return Wallet
     * @throws Exception
     */
    public function addMoney(int $userId, float $amount, string $transactionType, string $description, ?int $orderId = null): Wallet
    {
        DB::beginTransaction();
        
        try {
            // Get or create wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $userId],
                ['total_amount' => 0]
            );
            
            // Update wallet balance
            $wallet->total_amount += $amount;
            $wallet->save();
            
            // Create wallet history
            $this->walletRepository->createHistory([
                'user_id' => $userId,
                'order_id' => $orderId,
                'type' => 'credit',
                'transaction_type' => $transactionType,
                'amount' => $amount,
                'balance' => $wallet->total_amount,
                'datetime' => Carbon::now(),
                'description' => $description
            ]);
            
            DB::commit();
            return $wallet;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Deduct money from wallet
     *
     * @param int $userId
     * @param float $amount
     * @param string $transactionType
     * @param string $description
     * @param int|null $orderId
     * @return Wallet
     * @throws Exception
     */
    public function deductMoney(int $userId, float $amount, string $transactionType, string $description, ?int $orderId = null): Wallet
    {
        DB::beginTransaction();
        
        try {
            // Get wallet
            $wallet = Wallet::where('user_id', $userId)->first();
            
            if (!$wallet) {
                throw new Exception(__('message.wallet_not_found'));
            }
            
            if ($wallet->total_amount < $amount) {
                throw new Exception(__('message.insufficient_balance'));
            }
            
            // Update wallet balance
            $wallet->total_amount -= $amount;
            $wallet->save();
            
            // Create wallet history
            $this->walletRepository->createHistory([
                'user_id' => $userId,
                'order_id' => $orderId,
                'type' => 'debit',
                'transaction_type' => $transactionType,
                'amount' => $amount,
                'balance' => $wallet->total_amount,
                'datetime' => Carbon::now(),
                'description' => $description
            ]);
            
            DB::commit();
            return $wallet;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Transfer money between wallets
     *
     * @param int $fromUserId
     * @param int $toUserId
     * @param float $amount
     * @param string $description
     * @param int|null $orderId
     * @return array
     * @throws Exception
     */
    public function transferMoney(int $fromUserId, int $toUserId, float $amount, string $description, ?int $orderId = null): array
    {
        DB::beginTransaction();
        
        try {
            // Deduct from sender
            $fromWallet = $this->deductMoney(
                $fromUserId,
                $amount,
                'transfer_sent',
                $description,
                $orderId
            );
            
            // Add to receiver
            $toWallet = $this->addMoney(
                $toUserId,
                $amount,
                'transfer_received',
                $description,
                $orderId
            );
            
            DB::commit();
            return [
                'from_wallet' => $fromWallet,
                'to_wallet' => $toWallet
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process wallet transaction for completed order
     *
     * @param int $orderId
     * @return void
     * @throws Exception
     */
    public function processCompletedOrderTransaction(int $orderId): void
    {
        DB::beginTransaction();
        
        try {
            $order = Order::with('payment')->findOrFail($orderId);
            $payment = $order->payment;
            
            if (!$payment || $payment->payment_status !== 'paid') {
                throw new Exception(__('message.payment_not_completed'));
            }
            
            // Add admin commission to admin wallet
            $adminUser = User::where('user_type', 'admin')->first();
            if ($adminUser) {
                $this->addMoney(
                    $adminUser->id,
                    $payment->admin_commission,
                    'commission',
                    "Commission from order #{$order->id}",
                    $order->id
                );
            }
            
            // Add delivery man commission to delivery man wallet
            if ($order->delivery_man_id) {
                $this->addMoney(
                    $order->delivery_man_id,
                    $payment->delivery_man_commission,
                    'commission',
                    "Commission from order #{$order->id}",
                    $order->id
                );
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process wallet transaction for cancelled order
     *
     * @param int $orderId
     * @return void
     * @throws Exception
     */
    public function processCancelledOrderTransaction(int $orderId): void
    {
        DB::beginTransaction();
        
        try {
            $order = Order::with('payment')->findOrFail($orderId);
            $payment = $order->payment;
            
            if (!$payment) {
                throw new Exception(__('message.payment_not_found'));
            }
            
            if ($payment->payment_status === 'paid' && $payment->cancel_charges > 0) {
                // Add cancel charges to admin wallet
                $adminUser = User::where('user_type', 'admin')->first();
                if ($adminUser) {
                    $this->addMoney(
                        $adminUser->id,
                        $payment->cancel_charges,
                        'cancel_charge',
                        "Cancellation charges from order #{$order->id}",
                        $order->id
                    );
                }
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get wallet statistics
     *
     * @param int $userId
     * @param array $filters
     * @return array
     */
    public function getWalletStatistics(int $userId, array $filters = []): array
    {
        $conditions = [];
        
        if (!empty($filters['transaction_type'])) {
            $conditions['transaction_type'] = $filters['transaction_type'];
        }
        
        $totalCredit = $this->walletRepository->getTotalCreditAmount($userId, $conditions);
        $totalDebit = $this->walletRepository->getTotalDebitAmount($userId, $conditions);
        
        $wallet = $this->walletRepository->getByUser($userId);
        
        return [
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
            'current_balance' => $wallet ? $wallet->total_amount : 0,
            'net_earnings' => $totalCredit - $totalDebit
        ];
    }
}
