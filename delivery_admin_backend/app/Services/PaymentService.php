<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Payment Service for handling payment-related business logic
 */
class PaymentService
{
    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * PaymentService constructor.
     *
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Get all payments with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllPayments(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Payment::query();
        
        // Apply payment status filter
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }
        
        // Apply client filter
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        
        // Apply order filter
        if (!empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }
        
        // Apply date filters
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            if ($filters['from_date'] == $filters['to_date']) {
                $query->whereDate('created_at', '=', $filters['from_date']);
            } else {
                $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
            }
        }
        
        return $query->with(['order', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new payment
     *
     * @param array $data
     * @return Payment
     * @throws Exception
     */
    public function createPayment(array $data): Payment
    {
        DB::beginTransaction();
        
        try {
            // Create the payment
            $payment = $this->paymentRepository->create($data);
            
            // Update order payment_id
            if (!empty($data['order_id'])) {
                $order = Order::findOrFail($data['order_id']);
                $order->payment_id = $payment->id;
                $order->save();
            }
            
            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing payment
     *
     * @param int $paymentId
     * @param array $data
     * @return Payment
     * @throws Exception
     */
    public function updatePayment(int $paymentId, array $data): Payment
    {
        DB::beginTransaction();
        
        try {
            // Update the payment
            $payment = $this->paymentRepository->update($paymentId, $data);
            
            // Process payment if status changed to paid
            if (isset($data['payment_status']) && $data['payment_status'] === 'paid') {
                $this->processCompletedPayment($payment);
            }
            
            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process payment for completed order
     *
     * @param Payment $payment
     * @return void
     */
    public function processCompletedPayment(Payment $payment): void
    {
        $order = Order::findOrFail($payment->order_id);
        
        if ($order->status === 'completed') {
            // Add admin commission to admin wallet
            $adminUser = User::where('user_type', 'admin')->first();
            if ($adminUser) {
                $adminWallet = Wallet::firstOrCreate(
                    ['user_id' => $adminUser->id],
                    ['total_amount' => 0]
                );
                
                $adminWallet->total_amount += $payment->admin_commission;
                $adminWallet->save();
                
                // Create wallet history
                WalletHistory::create([
                    'user_id' => $adminUser->id,
                    'order_id' => $order->id,
                    'type' => 'credit',
                    'transaction_type' => 'commission',
                    'amount' => $payment->admin_commission,
                    'balance' => $adminWallet->total_amount,
                    'datetime' => Carbon::now(),
                    'description' => "Commission from order #{$order->id}"
                ]);
            }
            
            // Add delivery man commission to delivery man wallet
            if ($order->delivery_man_id) {
                $deliveryManWallet = Wallet::firstOrCreate(
                    ['user_id' => $order->delivery_man_id],
                    ['total_amount' => 0]
                );
                
                $deliveryManWallet->total_amount += $payment->delivery_man_commission;
                $deliveryManWallet->save();
                
                // Create wallet history
                WalletHistory::create([
                    'user_id' => $order->delivery_man_id,
                    'order_id' => $order->id,
                    'type' => 'credit',
                    'transaction_type' => 'commission',
                    'amount' => $payment->delivery_man_commission,
                    'balance' => $deliveryManWallet->total_amount,
                    'datetime' => Carbon::now(),
                    'description' => "Commission from order #{$order->id}"
                ]);
            }
        }
    }

    /**
     * Process payment for cancelled order
     *
     * @param Payment $payment
     * @return void
     */
    public function processCancelledPayment(Payment $payment): void
    {
        $order = Order::findOrFail($payment->order_id);
        
        if ($order->status === 'cancelled' && $payment->cancel_charges > 0) {
            // Add cancel charges to admin wallet
            $adminUser = User::where('user_type', 'admin')->first();
            if ($adminUser) {
                $adminWallet = Wallet::firstOrCreate(
                    ['user_id' => $adminUser->id],
                    ['total_amount' => 0]
                );
                
                $adminWallet->total_amount += $payment->cancel_charges;
                $adminWallet->save();
                
                // Create wallet history
                WalletHistory::create([
                    'user_id' => $adminUser->id,
                    'order_id' => $order->id,
                    'type' => 'credit',
                    'transaction_type' => 'cancel_charge',
                    'amount' => $payment->cancel_charges,
                    'balance' => $adminWallet->total_amount,
                    'datetime' => Carbon::now(),
                    'description' => "Cancellation charges from order #{$order->id}"
                ]);
            }
        }
    }

    /**
     * Get payment statistics
     *
     * @param array $filters
     * @return array
     */
    public function getPaymentStatistics(array $filters = []): array
    {
        $conditions = [];
        
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            // Date range filtering will be handled separately
        } else {
            $conditions['payment_status'] = 'paid';
        }
        
        $query = Payment::query();
        $query->where('payment_status', 'paid');
        
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            if ($filters['from_date'] == $filters['to_date']) {
                $query->whereDate('created_at', '=', $filters['from_date']);
            } else {
                $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
            }
        }
        
        return [
            'total_amount' => $query->sum('total_amount'),
            'admin_commission' => $query->sum('admin_commission'),
            'delivery_man_commission' => $query->sum('delivery_man_commission'),
            'cancel_charges' => $query->sum('cancel_charges'),
            'monthly_payment_count' => Payment::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->where('payment_status', 'paid')
                ->sum('total_amount')
        ];
    }

    /**
     * Get monthly payment report
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getMonthlyPaymentReport(string $startDate, string $endDate): array
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $diff = $startDate->diffInDays($endDate) + 1;
        
        $completedPayments = Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date, total_amount')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->get()
            ->groupBy('date');
        
        $cancelledPayments = Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date, cancel_charges')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('order', function ($query) {
                $query->where('status', 'cancelled');
            })
            ->get()
            ->groupBy('date');
        
        $report = [];
        
        for ($i = 0; $i < $diff; $i++) {
            $date = date('Y-m-d', strtotime($startDate . ' + ' . $i . 'day'));
            
            $completedAmount = $completedPayments->get($date, collect())->sum('total_amount');
            $cancelledAmount = $cancelledPayments->get($date, collect())->sum('cancel_charges');
            
            $report[] = [
                'date' => $date,
                'completed_amount' => $completedAmount,
                'cancelled_amount' => $cancelledAmount,
                'total_amount' => $completedAmount + $cancelledAmount
            ];
        }
        
        return $report;
    }
}
