<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Payment Repository for handling payment-related database operations
 */
class PaymentRepository extends BaseRepository
{
    /**
     * PaymentRepository constructor.
     *
     * @param Payment $model
     */
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * Get payments by status
     *
     * @param string $status
     * @param array $relations
     * @return Collection
     */
    public function getByStatus(string $status, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('payment_status', $status)
            ->get();
    }

    /**
     * Get payments by client
     *
     * @param int $clientId
     * @param array $relations
     * @return Collection
     */
    public function getByClient(int $clientId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('client_id', $clientId)
            ->get();
    }

    /**
     * Get payments by order
     *
     * @param int $orderId
     * @param array $relations
     * @return Collection
     */
    public function getByOrder(int $orderId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('order_id', $orderId)
            ->get();
    }

    /**
     * Get payments by date range
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
     * Get payments for current month
     *
     * @param array $relations
     * @return Collection
     */
    public function getCurrentMonthPayments(array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->get();
    }

    /**
     * Get total payment amount
     *
     * @param array $conditions
     * @return float
     */
    public function getTotalAmount(array $conditions = []): float
    {
        $query = $this->model->query();

        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }

        return $query->sum('total_amount');
    }

    /**
     * Get total admin commission
     *
     * @param array $conditions
     * @return float
     */
    public function getTotalAdminCommission(array $conditions = []): float
    {
        $query = $this->model->query();

        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }

        return $query->sum('admin_commission');
    }

    /**
     * Get total delivery man commission
     *
     * @param array $conditions
     * @return float
     */
    public function getTotalDeliveryManCommission(array $conditions = []): float
    {
        $query = $this->model->query();

        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }

        return $query->sum('delivery_man_commission');
    }

    /**
     * Get monthly payment statistics
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMonthlyStatistics(int $year, int $month): array
    {
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $statistics = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');

            $totalAmount = $this->model
                ->whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $statistics[] = [
                'date' => $date,
                'total_amount' => $totalAmount
            ];
        }

        return $statistics;
    }

    /**
     * Get all payments with filters
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
            // Filter by payment type
            if (isset($filters['payment_type']) && !empty($filters['payment_type'])) {
                $query->where('payment_type', $filters['payment_type']);
            }

            // Filter by payment status
            if (isset($filters['payment_status']) && !empty($filters['payment_status'])) {
                $query->where('payment_status', $filters['payment_status']);
            }

            // Filter by client
            if (isset($filters['client_id']) && !empty($filters['client_id'])) {
                $query->where('client_id', $filters['client_id']);
            }

            // Filter by order
            if (isset($filters['order_id']) && !empty($filters['order_id'])) {
                $query->where('order_id', $filters['order_id']);
            }

            // Filter by date range
            if (isset($filters['from_date']) && !empty($filters['from_date'])) {
                $query->whereDate('datetime', '>=', $filters['from_date']);
            }

            if (isset($filters['to_date']) && !empty($filters['to_date'])) {
                $query->whereDate('datetime', '<=', $filters['to_date']);
            }
        }

        // Apply user-specific filters
        $user = auth()->user();
        if ($user) {
            if ($user->user_type == 'client') {
                $query->where('client_id', $user->id);
            } elseif ($user->user_type == 'delivery_man') {
                $query->whereHas('order', function ($q) use ($user) {
                    $q->where('delivery_man_id', $user->id);
                });
            }
        }

        // Order by latest
        $query->orderBy('id', 'desc');

        return $query->paginate($perPage);
    }
}
