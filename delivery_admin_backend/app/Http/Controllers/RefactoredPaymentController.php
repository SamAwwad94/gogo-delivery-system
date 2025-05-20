<?php

namespace App\Http\Controllers;

use App\DataTables\PaymentDataTable;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefactoredPaymentController extends Controller
{
    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * PaymentController constructor.
     *
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param PaymentDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(PaymentDataTable $dataTable)
    {
        $payment_type = request('payment_type') ?? null;

        switch ($payment_type) {
            case 'cash':
                $pageTitle = __('message.list_form_title', ['form' => __('message.cash_payment')]);
                break;
            case 'online':
                $pageTitle = __('message.list_form_title', ['form' => __('message.online_payment')]);
                break;
            case 'wallet':
                $pageTitle = __('message.list_form_title', ['form' => __('message.wallet_payment')]);
                break;
            default:
                $pageTitle = __('message.list_form_title', ['form' => __('message.payment')]);
                break;
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // Continue with the original DataTable implementation
        } else {
            return $this->shadcnIndex();
        }

        $auth_user = authSession();
        $assets = ['datatable'];

        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user'));
    }

    /**
     * Display a listing of payments with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex()
    {
        $payment_type = request('payment_type') ?? null;

        switch ($payment_type) {
            case 'cash':
                $pageTitle = __('message.list_form_title', ['form' => __('message.cash_payment')]);
                break;
            case 'online':
                $pageTitle = __('message.list_form_title', ['form' => __('message.online_payment')]);
                break;
            case 'wallet':
                $pageTitle = __('message.list_form_title', ['form' => __('message.wallet_payment')]);
                break;
            default:
                $pageTitle = __('message.list_form_title', ['form' => __('message.payment')]);
                break;
        }

        $auth_user = authSession();
        $assets = ['datatable'];

        // Get payments using the service
        $perPage = 15;
        $filters = [
            'payment_type' => $payment_type,
            'payment_status' => request('payment_status') ?? null,
            'from_date' => request('from_date') ?? null,
            'to_date' => request('to_date') ?? null,
            'client_id' => request('client_id') ?? null,
            'order_id' => request('order_id') ?? null,
        ];

        $payments = $this->paymentService->getAllPayments($perPage, $filters);

        return view('payment.shadcn-payments', compact('pageTitle', 'auth_user', 'assets', 'payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.payment')]);
        $assets = ['datepicker'];

        return view('payment.form', compact('pageTitle', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePaymentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request)
    {
        try {
            // Get validated data
            $data = $request->validated();
            
            // Set datetime if not provided
            $data['datetime'] = isset($data['datetime']) ? date('Y-m-d H:i:s', strtotime($data['datetime'])) : date('Y-m-d H:i:s');
            
            // Handle wallet payment
            if ($request->payment_type == 'wallet') {
                $wallet = Wallet::where('user_id', $request->client_id)->first();
                if ($wallet != null) {
                    if ($wallet->total_amount < $request->total_amount) {
                        $message = __('message.balance_insufficient');
                        if ($request->is('api/*')) {
                            return json_message_response($message, 400);
                        }
                        return redirect()->back()->withErrors($message);
                    }
                    $data['payment_status'] = 'paid';
                } else {
                    $message = __('message.not_found_entry', ['name' => __('message.wallet')]);
                    if ($request->is('api/*')) {
                        return json_message_response($message, 400);
                    }
                    return redirect()->back()->withErrors($message);
                }
            }
            
            // Set received_by for non-cash/wallet payments
            if (!in_array($request->payment_type, ['cash', 'wallet'])) {
                $data['received_by'] = 'admin';
            }
            
            // Create the payment using the service
            $payment = $this->paymentService->createPayment($data);
            
            // Process wallet payment
            if ($payment->payment_status == 'paid' && $payment->payment_type == 'wallet') {
                $this->processWalletPayment($payment, $wallet);
            }
            
            // Update order status if online payment is paid
            if (isset($data['payment_status'], $data['is_online']) && $data['payment_status'] == 'paid' && $data['is_online'] == true) {
                $order = Order::find($data['order_id']);
                if ($order) {
                    $order->status = 'create';
                    $order->save();
                    
                    saveOrderHistory(['history_type' => $order->status, 'order_id' => $order->id, 'order' => $order]);
                    
                    $app_setting = AppSetting::first();
                    if ($app_setting && $app_setting->auto_assign == 1) {
                        $this->autoAssignOrder($order);
                    }
                }
            }
            
            // Create order history
            $order = Order::find($request->order_id);
            $history_data = [
                'history_type' => 'payment_status_message',
                'payment_status' => $payment->payment_status,
                'order_id' => $order->id,
                'order' => $order,
            ];
            
            saveOrderHistory($history_data);
            
            // Set response message
            if ($payment->payment_status == 'paid') {
                $message = __('message.payment_completed');
            } else {
                $message = __('message.payment_status_message', ['status' => __('message.' . $payment->payment_status), 'id' => $order->id]);
            }
            
            // Return response
            if ($request->is('api/*')) {
                $status_code = $payment->payment_status == 'failed' ? 400 : 200;
                return json_message_response($message, $status_code);
            }
            
            return redirect()->route('refactored-payment.index')->withSuccess($message);
        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return json_custom_response(['error' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $pageTitle = __('message.view_form_title', ['form' => __('message.payment')]);
            $data = Payment::findOrFail($id);
            
            return view('payment.show', compact('data', 'pageTitle'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $pageTitle = __('message.update_form_title', ['form' => __('message.payment')]);
            $data = Payment::findOrFail($id);
            $assets = ['datepicker'];
            
            return view('payment.form', compact('data', 'pageTitle', 'id', 'assets'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePaymentRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentRequest $request, $id)
    {
        try {
            // Get validated data
            $data = $request->validated();
            
            // Update the payment using the service
            $payment = $this->paymentService->updatePayment($id, $data);
            
            $message = __('message.update_form', ['form' => __('message.payment')]);
            
            // Return response
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-payment.index')->withSuccess($message);
        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return json_custom_response(['error' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Delete the payment using the service
            $result = $this->paymentService->deleteById($id);
            
            DB::commit();
            
            $message = __('message.delete_form', ['form' => __('message.payment')]);
            
            if (request()->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-payment.index')->withSuccess($message);
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->is('api/*')) {
                return json_custom_response(['error' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Process wallet payment
     *
     * @param Payment $payment
     * @param Wallet $wallet
     * @return void
     */
    private function processWalletPayment(Payment $payment, Wallet $wallet)
    {
        DB::beginTransaction();
        
        try {
            $wallet->decrement('total_amount', $payment->total_amount);
            $order = $payment->order;
            
            $admin_id = User::admin()->id;
            $currency = appSettingcurrency('currency_code');
            
            // Create client wallet history
            $client_wallet_history = [
                'user_id' => $order->client_id,
                'type' => 'debit',
                'currency' => $currency,
                'transaction_type' => 'order_fee',
                'amount' => $payment->total_amount,
                'balance' => $wallet->total_amount,
                'order_id' => $payment->order_id,
                'datetime' => date('Y-m-d H:i:s'),
                'data' => [
                    'payment_id' => $payment->id,
                ]
            ];
            
            WalletHistory::create($client_wallet_history);
            
            // Create admin wallet history
            $admin_wallet = Wallet::firstOrCreate(['user_id' => $admin_id]);
            $admin_wallet->increment('total_amount', $payment->total_amount);
            
            $admin_wallet_history = [
                'user_id' => $admin_id,
                'type' => 'credit',
                'currency' => $currency,
                'transaction_type' => 'order_fee',
                'amount' => $payment->total_amount,
                'balance' => $admin_wallet->total_amount,
                'order_id' => $payment->order_id,
                'datetime' => date('Y-m-d H:i:s'),
                'data' => [
                    'payment_id' => $payment->id,
                ]
            ];
            
            WalletHistory::create($admin_wallet_history);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
