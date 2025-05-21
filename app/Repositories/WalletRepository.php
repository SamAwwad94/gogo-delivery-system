<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\WalletHistory;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * Wallet Repository for handling wallet-related database operations
 */
class WalletRepository extends BaseRepository
{
    /**
     * WalletRepository constructor.
     *
     * @param Wallet $model
     */
    public function __construct(Wallet $model)
    {
        parent::__construct($model);
    }

    /**
     * Get wallet by user
     *
     * @param int $userId
     * @param array $relations
     * @return Wallet|null
     */
    public function getByUser(int $userId, array $relations = []): ?Wallet
    {
        return $this->model->with($relations)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get wallet history by user
     *
     * @param int $userId
     * @param array $relations
     * @return Collection
     */
    public function getHistoryByUser(int $userId, array $relations = []): Collection
    {
        return WalletHistory::with($relations)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get wallet history by date range
     *
     * @param int $userId
     * @param string $fromDate
     * @param string $toDate
     * @param array $relations
     * @return Collection
     */
    public function getHistoryByDateRange(int $userId, string $fromDate, string $toDate, array $relations = []): Collection
    {
        return WalletHistory::with($relations)
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get wallet history by transaction type
     *
     * @param int $userId
     * @param string $transactionType
     * @param array $relations
     * @return Collection
     */
    public function getHistoryByTransactionType(int $userId, string $transactionType, array $relations = []): Collection
    {
        return WalletHistory::with($relations)
            ->where('user_id', $userId)
            ->where('transaction_type', $transactionType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get wallet history by type (credit/debit)
     *
     * @param int $userId
     * @param string $type
     * @param array $relations
     * @return Collection
     */
    public function getHistoryByType(int $userId, string $type, array $relations = []): Collection
    {
        return WalletHistory::with($relations)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create wallet history
     *
     * @param array $data
     * @return WalletHistory
     */
    public function createHistory(array $data): WalletHistory
    {
        return WalletHistory::create($data);
    }

    /**
     * Get total credit amount
     *
     * @param int $userId
     * @param array $conditions
     * @return float
     */
    public function getTotalCreditAmount(int $userId, array $conditions = []): float
    {
        $query = WalletHistory::where('user_id', $userId)
            ->where('type', 'credit');
        
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->sum('amount');
    }

    /**
     * Get total debit amount
     *
     * @param int $userId
     * @param array $conditions
     * @return float
     */
    public function getTotalDebitAmount(int $userId, array $conditions = []): float
    {
        $query = WalletHistory::where('user_id', $userId)
            ->where('type', 'debit');
        
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->sum('amount');
    }

    /**
     * Get monthly wallet statistics
     *
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMonthlyStatistics(int $userId, int $year, int $month): array
    {
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $statistics = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            
            $creditAmount = WalletHistory::where('user_id', $userId)
                ->where('type', 'credit')
                ->whereDate('created_at', $date)
                ->sum('amount');
            
            $debitAmount = WalletHistory::where('user_id', $userId)
                ->where('type', 'debit')
                ->whereDate('created_at', $date)
                ->sum('amount');
            
            $statistics[] = [
                'date' => $date,
                'credit_amount' => $creditAmount,
                'debit_amount' => $debitAmount,
                'net_amount' => $creditAmount - $debitAmount
            ];
        }
        
        return $statistics;
    }
}
