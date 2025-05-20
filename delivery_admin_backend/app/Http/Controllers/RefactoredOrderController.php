<?php

namespace App\Http\Controllers;

use App\DataTables\OrderDataTable;
use App\DataTables\OrderPrintDataTable;
use App\DataTables\ShippedOrderDataTable;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\AppSetting;
use App\Models\Vehicle;
use App\Models\Payment;
use App\Models\StaticData;
use App\Models\Setting;
use App\Models\OrderHistory;
use App\Models\CustomerSupport;
use App\Models\DeliverymanVehicleHistory;
use App\Models\OrderVehicleHistory;
use App\Models\Profofpictures;
use App\Services\OrderService;
use App\Traits\OrderTrait;
use App\Traits\PaymentTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * OrderController handles order-related operations
 */
class RefactoredOrderController extends Controller
{
    use OrderTrait, PaymentTrait;

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * OrderController constructor.
     *
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param OrderDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(OrderDataTable $dataTable)
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // Continue with the original DataTable implementation
        } else {
            return $this->shadcnIndex();
        }

        // This code will only run if classic view is requested
        $pageTitle = __('message.list_form_title', ['form' => __('message.order')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $params = null;
        $params = [
            'status' => request('status') ?? null,
            'from_date' => request('from_date') ?? null,
            'to_date' => request('to_date') ?? null,
            'created_at' => request('created_at') ?? null,
            'city_id' => request('city_id') ?? null,
            'country_id' => request('country_id') ?? null,
        ];

        $filter_file_button = '<a href="' . route('filter.order.data', $params) . '" class=" mr-1 mt-1 btn btn-sm btn-success  text-dark loadRemoteModel"><i class="fas fa-filter"></i> ' . __('message.filter') . '</a>';
        $reset_file_button = '<a href="' . route('order.index') . '" class="float-right mr-1 mt-0 mb-1 btn btn-sm btn-info text-dark mt-1 pt-1 pb-1"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $multi_checkbox_delete = $auth_user->can('order-delete') ? '<button id="deleteSelectedBtn" checked-title = "order-checked " class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';

        return $dataTable->render('global.order-filter', compact('pageTitle', 'auth_user', 'multi_checkbox_delete', 'params', 'reset_file_button', 'filter_file_button'));
    }

    /**
     * Display a listing of orders with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex()
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.order')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Get orders using the service
        $perPage = 15;
        $filters = [
            'status' => request('status') ?? null,
            'from_date' => request('from_date') ?? null,
            'to_date' => request('to_date') ?? null,
            'client_id' => $auth_user->user_type == 'client' ? $auth_user->id : null,
            'delivery_man_id' => $auth_user->user_type == 'delivery_man' ? $auth_user->id : null,
        ];

        $orders = $this->orderService->getAllOrders($perPage, $filters);

        // Create button for admin
        $button = '';
        if ($auth_user->can('order-add')) {
            $button = '<a href="' . route('order.create') . '" class="shadcn-button shadcn-button-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                ' . __('message.add_form_title', ['form' => __('message.order')]) . '
            </a>';
        }

        return view('order.shadcn-orders', compact('pageTitle', 'auth_user', 'assets', 'orders', 'button'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('order-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $currency = appSettingcurrency('currency') ?? '$';
        $is_vehicle_in_order = appSettingcurrency('is_vehicle_in_order');
        $staticData = StaticData::get();
        $is_allow_deliveryman = SettingData('insurance_allow', 'insurance_allow');
        if ($is_allow_deliveryman == 1) {
            $is_insurance_percentage = SettingData('insurance_percentage', 'insurance_percentage');
        } else {
            $is_insurance_percentage = null;
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.order')]);
        $assets = ['phone', 'contact_nbr', 'location'];

        return view('order.form', compact('pageTitle', 'assets', 'staticData', 'is_allow_deliveryman', 'is_insurance_percentage', 'is_vehicle_in_order', 'currency'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreOrderRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            // Process packaging symbols
            $data = $request->validated();
            $symbol = $request->input('packaging_symbols');

            if (is_array($symbol)) {
                $symbols = [];
                foreach ($symbol as $charge) {
                    if (isset($charge['title'], $charge['key'])) {
                        $chargeEntry = [
                            'title' => $charge['title'],
                            'key' => $charge['key'],
                        ];
                        $symbols[] = $chargeEntry;
                    }
                }
                $data['packaging_symbols'] = json_encode($symbols);
            }

            // Set currency and milisecond
            $currency = appSettingcurrency()->currency ?? '$';
            $data['currency'] = $currency;
            $data['milisecond'] = strtoupper(appSettingcurrency('prefix')) . '' . round(microtime(true) * 1000);

            // Get vehicle data if provided
            if ($request->has('vehicle_id') && $request->input('vehicle_id') != null) {
                $data['vehicle_data'] = Vehicle::where('id', $request->input('vehicle_id'))->first() ?? null;
            }

            // Process extra charges
            if (!$request->is('api/*')) {
                $extraCharges = $request->input('extra_charges');
                if ($extraCharges) {
                    $extraCharges = json_decode($extraCharges, true);
                    if (is_array($extraCharges)) {
                        $formattedCharges = [];
                        foreach ($extraCharges as $charge) {
                            if (isset($charge['title'], $charge['charges'], $charge['charges_type'])) {
                                $chargeEntry = [
                                    'key' => $charge['title'],
                                    'value' => $charge['charges'],
                                    'value_type' => $charge['charges_type']
                                ];
                                $formattedCharges[] = $chargeEntry;
                            }
                        }
                        $data['extra_charges'] = $formattedCharges;
                    }
                }
            }

            // Set status for API requests
            if ($request->is('api/*')) {
                $data['status'] = ($data['payment_type'] == 'online') ? 'pending' : ($data['status'] ?? null);
            }

            // Create the order using the service
            $order = $this->orderService->createOrder($data);

            // Handle user address saving
            if ($request->has('save_user_address') && $request->save_user_address == 1) {
                $user_pickup_address_data = $order->pickup_point;
                $user_pickup_address_data['user_id'] = $order->client_id;
                $user_pickup_address_data['country_id'] = $order->country_id;
                $user_pickup_address_data['city_id'] = $order->city_id;

                $order->saveUserAddress()->create($user_pickup_address_data);

                $user_delivery_address_data = $order->delivery_point;
                $user_delivery_address_data['user_id'] = $order->client_id;
                $user_delivery_address_data['country_id'] = $order->country_id;
                $user_delivery_address_data['city_id'] = $order->city_id;

                $order->saveUserAddress()->create($user_delivery_address_data);
            }

            // Handle order cancellation
            if ($request->cancelorderreturn == 1) {
                $updateSuccessful = Order::where('id', $request->order_id)
                    ->update(['status' => 'cancelled', 'reason' => $request->reason]);
                if ($updateSuccessful) {
                    $data['history_type'] = 'cancelled';
                    $data['history_message'] = __('message.cancelled_order');
                    $history_data = [
                        'reason' => $request->reason,
                        'status' => 'cancelled',
                    ];
                    OrderHistory::create($data);
                }
                $message = __('message.return_order');
            } else {
                $message = __('message.save_form', ['form' => __('message.order')]);
            }

            // Handle auto-assignment and notifications
            if ($order->status === 'create') {
                $app_setting = AppSetting::first();

                if ($order->bid_type == 1) {
                    $this->nearByDeliveryman($order, $request->all());
                } else {
                    if ($app_setting && $app_setting->auto_assign == 1) {
                        $this->autoAssignOrder($order, $request->all());
                    }

                    if (!empty($order->pickup_point['contact_number'])) {
                        $this->sendTwilioSMS($order);
                    }
                }
            }

            // Return response
            if ($request->is('api/*')) {
                $response = [
                    'order_id' => $order->id,
                    'message' => $message
                ];
                return json_custom_response($response);
            }
            return redirect()->route('order.index')->withSuccess($message);
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
        if (!auth()->user()->can('order-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        try {
            $user = Auth::user();
            $is_vehicle_in_order = appSettingcurrency('is_vehicle_in_order');
            $pageTitle = __('message.view_form_title', ['form' => __('message.order')]);

            // Get the order using the repository
            if ($user->user_type == 'client') {
                $data = Order::where('id', $id)->where('client_id', $user->id)->first();
                if (!$data) {
                    return redirect()->route('home')->withErrors(__('message.demo_permission_denied'));
                }
            } elseif ($user->user_type == 'admin') {
                $data = Order::withTrashed()->findOrFail($id);
            } else {
                return redirect()->route('home')->withErrors(__('message.unauthorized_access'));
            }

            $complate_data = Order::withTrashed()->where('parent_order_id', $data->id)->first();
            $customerSupport = CustomerSupport::where('order_id', $data->id)->get();

            $courierCompany = $data->couriercompany ?? null;
            $trackingId = ($courierCompany && strpos($courierCompany->link, '=') !== false)
                ? trim(explode('=', $courierCompany->link)[1])
                : null;

            $profpicture = Profofpictures::where('order_id', $data->id)->get();
            $mediaItems = [
                'prof_file' => [],
            ];
            if ($profpicture->isNotEmpty()) {
                foreach ($profpicture as $picture) {
                    $mediaItems['prof_file'] = array_merge($mediaItems['prof_file'], $picture->getMedia('prof_file')->all());
                }
            }

            return view('order.show', compact('id', 'data', 'pageTitle', 'complate_data', 'courierCompany', 'trackingId', 'is_vehicle_in_order', 'mediaItems', 'customerSupport'));
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
        $user = auth()->user();
        $pageTitle = __('message.update_form_title', ['form' => __('message.order')]);

        try {
            $data = Order::findOrFail($id);

            if (auth()->user()) {
                if ($data->client_id === auth()->id() && $data->status === 'draft') {
                    // User is allowed to edit
                } else {
                    $message = __('message.demo_permission_denied');
                    return redirect()->back()->withErrors($message);
                }
            } else {
                return redirect()->back();
            }

            $assets = ['phone', 'contact_nbr', 'location'];
            $staticData = StaticData::get();

            return view('order.form', compact('data', 'pageTitle', 'id', 'staticData', 'assets'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOrderRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        try {
            // Get validated data
            $data = $request->validated();

            // Update the order using the service
            $order = $this->orderService->updateOrder($id, $data);

            // Handle media uploads
            uploadMediaFile($order, $request->pickup_time_signature, 'pickup_time_signature');
            uploadMediaFile($order, $request->delivery_time_signature, 'delivery_time_signature');

            // Create order history for status changes
            if (in_array($order->status, ['delayed', 'cancelled', 'failed', 'courier_picked_up', 'courier_arrived', 'completed', 'courier_departed'])) {
                $history_data = [
                    'history_type' => $order->status,
                    'order_id' => $id,
                    'order' => $order,
                ];

                saveOrderHistory($history_data);
            }

            // Handle active status for vehicle history
            if ($order->status == 'active') {
                $deliveryManId = auth()->id();
                $vehicleHistory = DeliverymanVehicleHistory::where('delivery_man_id', $deliveryManId)
                    ->where('is_active', 1)
                    ->first();

                if ($vehicleHistory) {
                    $vehicleInfo = json_encode($vehicleHistory->vehicle_info, true);

                    $orderVehicleData = [
                        'order_id' => $id,
                        'delivery_man_id' => $deliveryManId,
                        'vehicle_info' => $vehicleInfo,
                    ];

                    OrderVehicleHistory::create($orderVehicleData);

                    return response()->json(['message' => 'Data updated successfully.'], 200);
                } else {
                    return response()->json(['error' => 'No vehicle info found for the delivery man.'], 404);
                }
            }

            $message = __('message.update_form', ['form' => __('message.order')]);

            // Return response
            if ($request->is('api/*')) {
                return json_message_response($message);
            }

            return redirect()->route('draft-order')->with('success', $message);
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
            $order = Order::findOrFail($id);

            if (!auth()->user()->can('order-delete')) {
                $message = __('message.demo_permission_denied');
                return redirect()->back()->withErrors($message);
            }

            DB::beginTransaction();

            // Delete the order using the service
            $result = $this->orderService->deleteById($id);

            DB::commit();

            return redirect()->route('order.index')->withSuccess(__('message.delete_form', ['form' => __('message.order')]));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
