<?php

namespace App\Http\Controllers;

use App\DataTables\ClientOrderDataTable;
use App\DataTables\OrderDataTable;
use App\DataTables\OrderPrintDataTable;
use App\DataTables\ShippedOrderDataTable;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\AppSetting;
use App\Models\Vehicle;
use App\Http\Resources\DeliverymanVehicleHistoryResource;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\StaticData;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\OrderHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\OrderTrait;
use App\Traits\PaymentTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Imports\ImportOrderdata;
use App\Mail\sendmail;
use App\Models\CourierCompanies;
use App\Models\CustomerSupport;
use App\Models\DeliverymanVehicleHistory;
use App\Models\OrderBid;
use App\Models\OrderMail;
use App\Models\OrderVehicleHistory;
use App\Models\Profofpictures;
use App\Models\Reschedule;
use App\Notifications\CustomerSupportNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class OrderController extends Controller
{
    use OrderTrait, PaymentTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // For classic view, instantiate DataTable manually
            $dataTable = new OrderDataTable();
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
        } else {
            // Use React/Inertia by default
            return $this->indexInertia();
        }
    }

    /**
     * Display orders using Inertia/React
     *
     * @return \Inertia\Response
     */
    public function indexInertia()
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.order')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Build query with filters
        $query = Order::query();

        // Apply filters based on user type
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }

        // Apply all filters
        $this->applyFilters($query);

        // Get orders with pagination
        $orders = $query->with(['client'])
            ->paginate(15)
            ->appends(request()->query());

        // Prepare filters for React component
        $filters = [
            'order_type' => request('order_type'),
            'status' => request('status'),
            'date_start' => request('date_start'),
            'date_end' => request('date_end'),
            'client_id' => request('client_id'),
            'phone' => request('phone'),
            'pickup_location' => request('pickup_location'),
            'delivery_location' => request('delivery_location'),
            'payment_status' => request('payment_status'),
        ];

        return Inertia::render('Orders/Index', [
            'pageTitle' => $pageTitle,
            'auth_user' => $auth_user,
            'assets' => $assets,
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    /**
     * Display a listing of orders with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex(Request $request)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.order')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Create button for admin
        $button = '';
        if ($auth_user->can('order-add')) {
            $button = '<a href="' . route('order.create') . '" class="btn btn-added">
                <img src="' . asset('assets/img/icons/plus.svg') . '" alt="img" class="me-1">
                ' . __('message.add_new_order') . '
            </a>';
        }

        // Generate cache key based on request parameters
        $cacheKey = $this->getOrdersCacheKey($request);

        // Check if we have cached data
        if (!$request->ajax() && Cache::has($cacheKey)) {
            $orders = Cache::get($cacheKey);
        } else {
            // Build query with filters
            $query = Order::query();

            // Apply filters based on user type
            if ($auth_user->user_type == 'client') {
                $query->where('client_id', $auth_user->id);
            } elseif ($auth_user->user_type == 'delivery_man') {
                $query->where('deliveryman_id', $auth_user->id);
            }

            // Apply all filters
            $this->applyFilters($query);

            // Get orders with pagination
            $orders = $query->with(['client'])
                ->paginate(10)
                ->appends(request()->query());

            // Cache the results for 5 minutes
            if (!$request->ajax()) {
                Cache::put($cacheKey, $orders, now()->addMinutes(5));
            }
        }

        // Handle AJAX requests
        if ($request->ajax()) {
            return view('orders.partials._table', compact('orders'))->render();
        }

        // Use the new ShadCN order view
        return view('orders.shadcn-index', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'button',
            'orders'
        ));
    }

    /**
     * Apply filters to the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function applyFilters($query)
    {
        // Order ID filter
        if (request()->has('order_id') && request('order_id') != '') {
            $query->where('id', request('order_id'));
        }

        // Status filter
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }

        // Order type filter (maps to status)
        if (request()->has('order_type') && request('order_type') != '') {
            $query->where('status', request('order_type'));
        }

        // Date range filter
        if (request()->has('date_start') && request('date_start') != '') {
            $query->whereDate('created_at', '>=', request('date_start'));
        }

        if (request()->has('date_end') && request('date_end') != '') {
            $query->whereDate('created_at', '<=', request('date_end'));
        }

        // Customer filter
        if (request()->has('client_id') && request('client_id') != '') {
            $query->where('client_id', request('client_id'));
        }

        // Phone filter
        if (request()->has('phone') && request('phone') != '') {
            $query->where(function ($q) {
                $phone = request('phone');
                $q->where('phone', 'like', "%{$phone}%")
                    ->orWhereHas('client', function ($q) use ($phone) {
                        $q->where('contact_number', 'like', "%{$phone}%");
                    });
            });
        }

        // Pickup Location filter
        if (request()->has('pickup_location') && request('pickup_location') != '') {
            $location = request('pickup_location');
            $query->where(function ($q) use ($location) {
                $q->whereRaw("JSON_EXTRACT(pickup_point, '$.address') LIKE ?", ["%{$location}%"])
                    ->orWhereRaw("pickup_point LIKE ?", ["%{$location}%"]);
            });
        }

        // Delivery Location filter
        if (request()->has('delivery_location') && request('delivery_location') != '') {
            $location = request('delivery_location');
            $query->where(function ($q) use ($location) {
                $q->whereRaw("JSON_EXTRACT(delivery_point, '$.address') LIKE ?", ["%{$location}%"])
                    ->orWhereRaw("delivery_point LIKE ?", ["%{$location}%"]);
            });
        }

        // Payment Status filter
        if (request()->has('payment_status') && request('payment_status') != '') {
            $query->where('payment_status', request('payment_status'));
        }

        // Apply sorting
        if (request()->has('sort') && request()->has('direction')) {
            $sortColumn = request('sort');
            $sortDirection = request('direction');

            // Validate sort column to prevent SQL injection
            $allowedColumns = ['id', 'created_at', 'status', 'client_id', 'payment_status'];
            if (in_array($sortColumn, $allowedColumns)) {
                $query->orderBy($sortColumn, $sortDirection);
            }
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Cache key for orders query
     *
     * @param Request $request
     * @return string
     */
    private function getOrdersCacheKey(Request $request)
    {
        $auth_user = authSession();
        $user_type = $auth_user->user_type;
        $user_id = $auth_user->id;

        // Build cache key based on filters
        $cacheKey = "orders_{$user_type}_{$user_id}";

        // Add filters to cache key
        $filters = [
            'order_id',
            'status',
            'client_id',
            'phone',
            'pickup_location',
            'delivery_location',
            'payment_status',
            'date_range',
            'sort',
            'direction',
            'page'
        ];

        foreach ($filters as $filter) {
            if ($request->has($filter) && $request->input($filter) != '') {
                $cacheKey .= "_{$filter}_{$request->input($filter)}";
            }
        }

        return $cacheKey;
    }

    /**
     * Display the map view for orders
     *
     * @return \Illuminate\Http\Response
     */
    public function map()
    {
        $pageTitle = __('message.order_map');
        $auth_user = authSession();
        $assets = ['leaflet'];

        // Get active orders with location data
        $orders = Order::whereIn('status', ['create', 'courier_assigned', 'courier_accepted', 'courier_arrived', 'courier_picked_up', 'courier_departed'])
            ->with(['client', 'delivery_man'])
            ->get();

        return view('orders.shadcn-map', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'orders'
        ));
    }

    /**
     * Display a mock orders page with static data
     *
     * @return \Illuminate\Http\Response
     */
    public function mockOrders()
    {
        $pageTitle = __('message.order_list');
        $auth_user = authSession();
        $assets = ['datatable'];

        // Create mock data for orders
        $mockOrders = [
            [
                'id' => 'ORD-001',
                'date' => '12 May 2023',
                'status' => 'delivered',
                'status_label' => 'Delivered',
                'customer' => 'John Doe',
                'phone' => '+961 76 123 456',
                'pickup_location' => 'Hamra, Beirut',
                'delivery_location' => 'Jounieh',
                'payment_status' => 'paid',
                'payment_status_label' => 'Paid'
            ],
            [
                'id' => 'ORD-002',
                'date' => '13 May 2023',
                'status' => 'pending',
                'status_label' => 'Pending',
                'customer' => 'Jane Smith',
                'phone' => '+961 76 789 012',
                'pickup_location' => 'Achrafieh, Beirut',
                'delivery_location' => 'Tripoli',
                'payment_status' => 'unpaid',
                'payment_status_label' => 'Unpaid'
            ],
            [
                'id' => 'ORD-003',
                'date' => '14 May 2023',
                'status' => 'in_progress',
                'status_label' => 'In Progress',
                'customer' => 'Robert Johnson',
                'phone' => '+961 76 345 678',
                'pickup_location' => 'Verdun, Beirut',
                'delivery_location' => 'Sidon',
                'payment_status' => 'partial',
                'payment_status_label' => 'Partial'
            ],
            [
                'id' => 'ORD-004',
                'date' => '15 May 2023',
                'status' => 'cancelled',
                'status_label' => 'Cancelled',
                'customer' => 'Sarah Williams',
                'phone' => '+961 76 901 234',
                'pickup_location' => 'Gemmayze, Beirut',
                'delivery_location' => 'Tyre',
                'payment_status' => 'refunded',
                'payment_status_label' => 'Refunded'
            ],
            [
                'id' => 'ORD-005',
                'date' => '16 May 2023',
                'status' => 'completed',
                'status_label' => 'Completed',
                'customer' => 'Michael Brown',
                'phone' => '+961 76 567 890',
                'pickup_location' => 'Mar Mikhael, Beirut',
                'delivery_location' => 'Batroun',
                'payment_status' => 'paid',
                'payment_status_label' => 'Paid'
            ],
            [
                'id' => 'ORD-006',
                'date' => '17 May 2023',
                'status' => 'courier_assigned',
                'status_label' => 'Assigned',
                'customer' => 'Emily Davis',
                'phone' => '+961 76 234 567',
                'pickup_location' => 'Badaro, Beirut',
                'delivery_location' => 'Byblos',
                'payment_status' => 'unpaid',
                'payment_status_label' => 'Unpaid'
            ],
            [
                'id' => 'ORD-007',
                'date' => '18 May 2023',
                'status' => 'courier_accepted',
                'status_label' => 'Accepted',
                'customer' => 'David Wilson',
                'phone' => '+961 76 890 123',
                'pickup_location' => 'Ras Beirut',
                'delivery_location' => 'Zahle',
                'payment_status' => 'paid',
                'payment_status_label' => 'Paid'
            ],
            [
                'id' => 'ORD-008',
                'date' => '19 May 2023',
                'status' => 'courier_picked_up',
                'status_label' => 'Picked Up',
                'customer' => 'Lisa Martinez',
                'phone' => '+961 76 456 789',
                'pickup_location' => 'Ain El Mraiseh, Beirut',
                'delivery_location' => 'Baalbek',
                'payment_status' => 'paid',
                'payment_status_label' => 'Paid'
            ],
            [
                'id' => 'ORD-009',
                'date' => '20 May 2023',
                'status' => 'courier_departed',
                'status_label' => 'Departed',
                'customer' => 'James Taylor',
                'phone' => '+961 76 012 345',
                'pickup_location' => 'Raouche, Beirut',
                'delivery_location' => 'Aley',
                'payment_status' => 'partial',
                'payment_status_label' => 'Partial'
            ],
            [
                'id' => 'ORD-010',
                'date' => '21 May 2023',
                'status' => 'delivered',
                'status_label' => 'Delivered',
                'customer' => 'Patricia Anderson',
                'phone' => '+961 76 678 901',
                'pickup_location' => 'Ramlet El Baida, Beirut',
                'delivery_location' => 'Beit Mery',
                'payment_status' => 'paid',
                'payment_status_label' => 'Paid'
            ]
        ];

        // Create button for admin
        $button = '';
        if ($auth_user->can('order-add')) {
            $button = '<a href="' . route('order.create') . '" class="btn btn-added">
                <img src="' . asset('assets/img/icons/plus.svg') . '" alt="img" class="me-1">
                ' . __('message.add_new_order') . '
            </a>';
        }

        return view('orders.mock-simple', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'button',
            'mockOrders'
        ));
    }

    /**
     * Display a test page with static content
     *
     * @return \Illuminate\Http\Response
     */
    public function testPage()
    {
        return view('orders.test');
    }



    /**
     * Handle inline editing of order fields
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function inlineEdit(Request $request, $id)
    {
        if (!auth()->user()->can('order-edit')) {
            return response()->json([
                'success' => false,
                'message' => __('message.demo_permission_denied')
            ], 403);
        }

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => __('message.order_not_found')
            ], 404);
        }

        $field = $request->input('field');
        $value = $request->input('value');

        // Validate field
        $allowedFields = [
            'status',
            'payment_status',
            'phone',
            'note'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json([
                'success' => false,
                'message' => __('message.invalid_field')
            ], 400);
        }

        // Validate value based on field
        switch ($field) {
            case 'status':
                $allowedStatuses = [
                    'draft',
                    'create',
                    'courier_assigned',
                    'courier_accepted',
                    'courier_arrived',
                    'courier_picked_up',
                    'courier_departed',
                    'completed',
                    'cancelled'
                ];

                if (!in_array($value, $allowedStatuses)) {
                    return response()->json([
                        'success' => false,
                        'message' => __('message.invalid_status')
                    ], 400);
                }
                break;

            case 'payment_status':
                $allowedPaymentStatuses = [
                    'paid',
                    'unpaid',
                    'partial',
                    'refunded'
                ];

                if (!in_array($value, $allowedPaymentStatuses)) {
                    return response()->json([
                        'success' => false,
                        'message' => __('message.invalid_payment_status')
                    ], 400);
                }
                break;

            case 'phone':
                if (!preg_match('/^[0-9+\-\s()]*$/', $value)) {
                    return response()->json([
                        'success' => false,
                        'message' => __('message.invalid_phone_format')
                    ], 400);
                }
                break;
        }

        // Update the field
        $order->$field = $value;
        $order->save();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => __('message.order_updated_successfully'),
            'data' => [
                'id' => $order->id,
                'field' => $field,
                'value' => $value
            ]
        ]);
    }

    /**
     * Get order statistics for dashboard visualizations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        if (!auth()->user()->can('order-list')) {
            return response()->json([
                'success' => false,
                'message' => __('message.demo_permission_denied')
            ], 403);
        }

        // Generate cache key
        $auth_user = authSession();
        $cacheKey = "order_statistics_{$auth_user->id}";

        // Return cached data if available (cache for 30 minutes)
        $data = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            // Get status counts
            $statusCounts = Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Get payment status counts
            $paymentCounts = Order::select('payment_status', DB::raw('count(*) as count'))
                ->groupBy('payment_status')
                ->pluck('count', 'payment_status')
                ->toArray();

            // Get timeline data (orders per month for the last 6 months)
            $timeline = [];
            $timeline['labels'] = [];
            $timeline['data'] = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $timeline['labels'][] = $month->format('M');

                $count = Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $timeline['data'][] = $count;
            }

            return [
                'statusCounts' => $statusCounts,
                'paymentCounts' => $paymentCounts,
                'timeline' => $timeline
            ];
        });

        return response()->json([
            'success' => true,
            'statusCounts' => $data['statusCounts'],
            'paymentCounts' => $data['paymentCounts'],
            'timeline' => $data['timeline']
        ]);
    }

    /**
     * Get order updates since a specific timestamp
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdates(Request $request)
    {
        if (!auth()->user()->can('order-list')) {
            return response()->json([
                'success' => false,
                'message' => __('message.demo_permission_denied')
            ], 403);
        }

        // Get the last update timestamp
        $lastUpdate = $request->input('last_update');
        if (!$lastUpdate) {
            $lastUpdate = Carbon::now()->subMinutes(5)->toDateTimeString();
        }

        // Generate cache key
        $auth_user = authSession();
        $cacheKey = "order_updates_{$auth_user->id}_{$lastUpdate}";

        // Add filters to cache key
        $filters = [
            'order_id',
            'status',
            'client_id',
            'phone',
            'pickup_location',
            'delivery_location',
            'payment_status'
        ];

        foreach ($filters as $filter) {
            if ($request->has($filter) && $request->input($filter) != '') {
                $cacheKey .= "_{$filter}_{$request->input($filter)}";
            }
        }

        // Return cached data if available (cache for 1 minute)
        return Cache::remember($cacheKey, now()->addMinute(), function () use ($request, $lastUpdate, $auth_user) {
            // Build query with filters
            $query = Order::query();

            // Apply filters based on user type
            if ($auth_user->user_type == 'client') {
                $query->where('client_id', $auth_user->id);
            } elseif ($auth_user->user_type == 'delivery_man') {
                $query->where('deliveryman_id', $auth_user->id);
            }

            // Apply other filters
            $this->applyFilters($query);

            // Get updated orders with eager loading for performance
            $orders = $query->where(function ($q) use ($lastUpdate) {
                $q->where('created_at', '>=', $lastUpdate)
                    ->orWhere('updated_at', '>=', $lastUpdate);
            })
                ->with([
                    'client' => function ($query) {
                        $query->select('id', 'name', 'email', 'phone');
                    }
                ])
                ->get();

            // Get deleted orders
            $deletedOrders = [];

            // Combine results
            $allOrders = $orders->toArray();
            foreach ($deletedOrders as $deletedOrder) {
                $allOrders[] = [
                    'id' => $deletedOrder,
                    '_deleted' => true
                ];
            }

            return response()->json([
                'success' => true,
                'orders' => $allOrders,
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);
        });
    }

    /**
     * Export orders to CSV
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCsv()
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Build query with filters
        $query = Order::query();

        // Apply filters based on user type
        $auth_user = authSession();
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }

        // Apply all filters from request
        $this->applyFilters($query);

        // Get orders
        $orders = $query->with(['client'])->get();

        // Create CSV file
        $filename = 'orders_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'Order ID',
                'Date',
                'Status',
                'Customer',
                'Phone',
                'Pickup Location',
                'Delivery Location',
                'Payment Status',
                'Total Amount'
            ]);

            // Add data rows
            foreach ($orders as $order) {
                // Process pickup location
                $pickupLocation = 'N/A';
                if ($order->pickup_point) {
                    $pickupPoint = is_string($order->pickup_point) ? json_decode($order->pickup_point, true) : $order->pickup_point;
                    if (is_array($pickupPoint) && isset($pickupPoint['address'])) {
                        $pickupLocation = $pickupPoint['address'];
                    }
                }

                // Process delivery location
                $deliveryLocation = 'N/A';
                if ($order->delivery_point) {
                    $deliveryPoint = is_string($order->delivery_point) ? json_decode($order->delivery_point, true) : $order->delivery_point;
                    if (is_array($deliveryPoint) && isset($deliveryPoint['address'])) {
                        $deliveryLocation = $deliveryPoint['address'];
                    }
                }

                // Map status to label
                $statusLabel = match ($order->status) {
                    'draft' => 'Draft',
                    'create' => 'Created',
                    'courier_assigned' => 'Assigned',
                    'courier_accepted' => 'Accepted',
                    'courier_arrived' => 'Arrived',
                    'courier_picked_up' => 'Picked Up',
                    'courier_departed' => 'Departed',
                    'completed' => 'Delivered',
                    'cancelled' => 'Cancelled',
                    default => ucfirst($order->status)
                };

                fputcsv($file, [
                    $order->id,
                    $order->created_at->format('M d, Y'),
                    $statusLabel,
                    $order->client->name ?? 'N/A',
                    $order->phone ?? 'N/A',
                    $pickupLocation,
                    $deliveryLocation,
                    ucfirst($order->payment_status),
                    $order->total_amount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export orders to PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Build query with filters
        $query = Order::query();

        // Apply filters based on user type
        $auth_user = authSession();
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }

        // Apply all filters from request
        $this->applyFilters($query);

        // Get orders
        $orders = $query->with(['client'])->get();

        // Generate PDF
        $pdf = PDF::loadView('orders.export.pdf', compact('orders'));

        return $pdf->download('orders_export_' . date('Y-m-d_H-i-s') . '.pdf');
    }



    public function orderprintindex(Request $request)
    {
        // Check if Inertia request or classic view is requested
        if (!$request->has('classic') || $request->classic != 1) {
            return $this->orderprintindexInertia();
        }

        // For classic view, instantiate DataTable manually
        $dataTable = new OrderPrintDataTable();
        $pageTitle = __('message.list_form_title', ['form' => __('message.print_order')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        $multi_checkbox_print = $auth_user->can('order-list')
            ? '<div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ' . __('message.print') . ' <i class="fa-solid fa-angle-down"></i>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" id="printsectionBtn">' . __('message.print_multiple') . '</a>
                <a class="dropdown-item" id="printLabelBtn">' . __('message.print_label') . '</a>
                <a class="dropdown-item" id="printDeliveryLabelsBtn">' . __('message.delivery_labels') . '</a>
            </div>
        </div>'
            : '';

        return $dataTable->render('global.order-filter', compact('pageTitle', 'auth_user', 'multi_checkbox_print'));
    }

    /**
     * Display print orders using Inertia/React
     *
     * @return \Inertia\Response
     */
    public function orderprintindexInertia()
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.print_order')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Build query with filters
        $query = Order::query();

        // Apply filters based on user type
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }

        // Apply all filters
        $this->applyFilters($query);

        // Get orders with pagination
        $orders = $query->with(['client'])
            ->paginate(15)
            ->appends(request()->query());

        // Prepare filters for React component
        $filters = [
            'order_type' => request('order_type'),
            'status' => request('status'),
            'date_start' => request('date_start'),
            'date_end' => request('date_end'),
            'client_id' => request('client_id'),
            'phone' => request('phone'),
            'pickup_location' => request('pickup_location'),
            'delivery_location' => request('delivery_location'),
            'payment_status' => request('payment_status'),
        ];

        return Inertia::render('Orders/PrintIndex', [
            'pageTitle' => $pageTitle,
            'auth_user' => $auth_user,
            'assets' => $assets,
            'orders' => $orders,
            'filters' => $filters,
            'canPrint' => $auth_user->can('order-list'),
        ]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $data = $request->all();
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

        $currency = appSettingcurrency()->currency ?? '$';
        $data['currency'] = $currency;
        $data['milisecond'] = strtoupper(appSettingcurrency('prefix')) . '' . round(microtime(true) * 1000);

        if ($request->has('vehicle_id') && $request->input('vehicle_id') != null) {
            $data['vehicle_data'] = Vehicle::where('id', $request->input('vehicle_id'))->first() ?? null;
        }

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
        if ($request->is('api/*')) {
            $data['status'] = ($data['payment_type'] == 'online') ? 'pending' : ($data['status'] ?? null);
        }
        $result = Order::updateOrCreate(['id' => $request->id], $data);
        $message = __('message.update_form', ['form' => __('message.order')]);
        if ($result->wasRecentlyCreated) {
            if ($request->cancelorderreturn == 1) {
                $message = __('message.return_order');
            } else {
                $message = __('message.save_form', ['form' => __('message.order')]);
            }
        }

        if ($request->has('save_user_address') && $request->save_user_address == 1) {
            $user_pickup_address_data = $result->pickup_point;
            $user_pickup_address_data['user_id'] = $result->client_id;
            $user_pickup_address_data['country_id'] = $result->country_id;
            $user_pickup_address_data['city_id'] = $result->city_id;

            $result->saveUserAddress()->create($user_pickup_address_data);

            $user_delivery_address_data = $result->delivery_point;
            $user_delivery_address_data['user_id'] = $result->client_id;
            $user_delivery_address_data['country_id'] = $result->country_id;
            $user_delivery_address_data['city_id'] = $result->city_id;

            $result->saveUserAddress()->create($user_delivery_address_data);
        }

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
        }

        if ($result->parent_order_id != null) {
            $history_data = [
                'history_type' => 'return',
                'parent_order_id' => $result->parent_order_id,
                'order_id' => $result->parent_order_id,
                'order' => $result,
            ];
            saveOrderHistory($history_data);
        }

        if ($result->status != 'pending') {
            $history_data = [
                'history_type' => $result->status,
                'order_id' => $result->id,
                'order' => $result,
            ];
            saveOrderHistory($history_data);
        }
        if ($result->status === 'create') {
            $app_setting = AppSetting::first();

            if ($result->bid_type == 1) {
                $this->nearByDeliveryman($result, $request->all());
            } else {
                if ($app_setting && $app_setting->auto_assign == 1) {
                    $this->autoAssignOrder($result, $request->all());
                }

                if (!empty($result->pickup_point['contact_number'])) {
                    $this->sendTwilioSMS($result);
                }
            }
        }
        if ($request->is('api/*')) {
            $response = [
                'order_id' => $result->id,
                'message' => $message
            ];
            return json_custom_response($response);
        }
        return redirect()->route('order.index')->withSuccess($message);
    }


    public function autoAssignCancelOrder(Request $request)
    {
        $order_data = Order::find($request->id);

        $result = $this->autoAssignOrder($order_data, $request->all());

        $message = __('message.updated');
        if ($result->delivery_man_id == null) {
            $message = __('message.save_form', ['form' => __('message.order')]);
        }
        if ($request->is('api/*')) {
            $response = [
                'order_id' => $result->id,
                'message' => $message
            ];
            return json_custom_response($response);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('order-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $user = Auth::user();
        $is_vehicle_in_order = appSettingcurrency('is_vehicle_in_order');
        $pageTitle = __('message.add_form_title', ['form' => __('message.order')]);

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
        // $orderVehicleHistories = OrderVehicleHistory::where('order_id', $id)->get();

        // if ($orderVehicleHistories->isEmpty()) {
        //     return redirect()->back()->with('error', 'No vehicle history found for this order.');
        // }

        // $deliveryManIds = $orderVehicleHistories->pluck('delivery_man_id')->unique();

        // $deliveryMen = User::whereIn('id', $deliveryManIds)->get();


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
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $pageTitle = __('message.update_form_title', ['form' => __('message.order')]);
        $data = order::findOrFail($id);
        if (auth()->user()) {
            if ($data->client_id === auth()->id() && $data->status === 'draft') {
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $old_status = $order->status;

        try {
            DB::beginTransaction();

            $order->fill($request->all())->update();

            $payment = Payment::where('order_id', $id)->first();

            if ($payment != null && $payment->payment_status == 'paid' && $order->status == 'completed') {
                $this->walletTransactionCompleted($order->id);
            }
            if ($order->status == 'cancelled') {
                $this->walletTransactionCancelled($order->id);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::info('error-'.$e);
            return json_custom_response($e);
        }

        uploadMediaFile($order, $request->pickup_time_signature, 'pickup_time_signature');
        uploadMediaFile($order, $request->delivery_time_signature, 'delivery_time_signature');
        $message = __('message.update_form', ['form' => __('message.order')]);


        if (in_array(request('status'), ['delayed', 'cancelled', 'failed'])) {
            $history_data = [
                'history_type' => request('status'),
                'order_id' => $id,
                'order' => $order,
            ];

            saveOrderHistory($history_data);
        }

        if (in_array(request('status'), ['courier_picked_up', 'courier_arrived', 'completed', 'courier_departed'])) {
            $history_data = [
                'history_type' => request('status'),
                'order_id' => $id,
                'order' => $order,
            ];

            saveOrderHistory($history_data);
        }
        if (request('status') == 'active') {
            $deliveryManId = auth()->id();

            $vehicleHistory = DeliverymanVehicleHistory::where('delivery_man_id', $deliveryManId)->where('is_active', 1)->first();

            if ($vehicleHistory) {
                $vehicleInfo = json_encode($vehicleHistory->vehicle_info, true);

                $orderVehicleData = [
                    'order_id' => $id,
                    'delivery_man_id' => $deliveryManId,
                    'vehicle_info' => $vehicleInfo,
                ];

                $model = OrderVehicleHistory::create($orderVehicleData);

                return response()->json(['message' => 'Data updated successfully.'], 200);
            } else {
                return response()->json(['error' => 'No vehicle info found for the delivery man.'], 404);
            }
        }
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('draft-order')->with($message);
    }

    /**
     * Bulk delete orders
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        if (!auth()->user()->can('order-delete')) {
            $message = __('message.demo_permission_denied');

            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }

            return redirect()->route('order.index')->withErrors($message);
        }

        $ids = $request->ids;

        if (empty($ids)) {
            return redirect()->route('order.index')->withErrors(__('message.no_orders_selected'));
        }

        $deletedCount = 0;

        foreach ($ids as $id) {
            $order = Order::find($id);

            if ($order) {
                $order->delete();
                $deletedCount++;
            }
        }

        if (request()->ajax()) {
            return response()->json([
                'status' => true,
                'message' => __('message.bulk_delete_success', ['count' => $deletedCount])
            ]);
        }

        return redirect()->route('order.index')
            ->with('success', __('message.bulk_delete_success', ['count' => $deletedCount]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth()->user()->user_type == 'admin') {
            if (!auth()->user()->can('order-delete')) {
                $message = __('message.demo_permission_denied');
                return response()->json(['status' => true, 'message' => $message]);
            }
        }

        if (env('APP_DEMO') && auth()->user()->hasRole('admin')) {
            $message = __('message.demo_permission_denied');
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('order.index')->withErrors($message);
        }
        $order = order::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.order')]);

        if ($order != '') {
            $order->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.order')]);
        }

        if (request()->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $order = Order::withTrashed()->where('id', $id)->first();

        $message = __('message.not_found_entry', ['name' => __('message.order')]);
        if ($request->type === 'restore') {
            $order->restore();
            $message = __('message.msg_restored', ['name' => __('message.order')]);
        }

        if ($request->type === 'forcedelete') {
            if (env('APP_DEMO')) {
                $message = __('message.demo_permission_denied');
                if (request()->is('api/*')) {
                    return response()->json(['status' => true, 'message' => $message]);
                }
                if (request()->ajax()) {
                    return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                }
                return redirect()->route('order.index')->withErrors($message);
            }
            $order->forceDelete();
            $search = "id" . '":' . $id;
            Notification::where('data', 'like', "%{$search}%")->delete();
            $message = __('message.msg_forcedelete', ['name' => __('message.order')]);
        }

        if ($request->type == 'courier_assigned') {
            if ($order->delivery_man_id != null) {
                $message = __('message.couriertransfer');
                $history_type = 'courier_transfer';
                $data['assign_datetime'] = now();
            } else {
                $message = __('message.courierassigned');
                $history_type = 'courier_assigned';
                $data['assign_datetime'] = now();
            }

            $assignorder = Order::updateOrCreate(['id' => $request->id], $data);
            $order->update(['delivery_man_id' => $request->delivery_man_id, 'status' => $request->status]);
            $history_data = [
                'history_type' => $history_type,
                'order_id' => $id,
                'order' => $order,
            ];

            saveOrderHistory($history_data);
        }

        if ($request->type == 'courier_departed') {
            $order->update(['status' => $request->status]);
            $history_data = [
                'history_type' => 'courier_departed',
                'order_id' => $id,
                'order' => $order,
            ];

            saveOrderHistory($history_data);
        }

        if ($request->type == 'completed') {
            $order->update(['status' => $request->type]);
            $history_data = [
                'history_type' => 'completed',
                'order_id' => $id,
                'order' => $order,
            ];

            saveOrderHistory($history_data);
        }

        if (request()->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        return redirect()->route('order.index')->withSuccess($message);
    }

    public function InvoicePdf($id)
    {
        $order = Order::find($id);
        $today = Carbon::now()->format('d/m/Y');

        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companynumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();

        // If company logo exists and it's a direct file path, make sure it's properly accessible
        if ($invoice && $invoice->value && file_exists(public_path($invoice->value))) {
            $invoice->image_url = asset($invoice->value) . '?v=' . time();
        } else {
            // Fallback to media library if direct file doesn't exist
            $mediaUrl = getSingleMediaSettingImage($invoice, 'company_logo');
            if ($mediaUrl) {
                $invoice->image_url = $mediaUrl . '?v=' . time();
            }
        }

        $pdf = Pdf::loadView('order.invoice', compact('invoice', 'companyName', 'companyAddress', 'companynumber', 'order', 'today'));
        return $pdf->download('invoice_' . $order->id . '.pdf');
    }

    public function ApiInvoicePdf($id)
    {
        $order = Order::find($id);
        $today = Carbon::now()->format('d/m/Y');

        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companynumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();

        // If company logo exists and it's a direct file path, make sure it's properly accessible
        if ($invoice && $invoice->value && file_exists(public_path($invoice->value))) {
            $invoice->image_url = asset($invoice->value) . '?v=' . time();
        } else {
            // Fallback to media library if direct file doesn't exist
            $mediaUrl = getSingleMediaSettingImage($invoice, 'company_logo');
            if ($mediaUrl) {
                $invoice->image_url = $mediaUrl . '?v=' . time();
            }
        }

        $pdf = Pdf::loadView('order.invoice', compact('invoice', 'companyName', 'companyAddress', 'companynumber', 'order', 'today'));
        return $pdf->stream('invoice_' . $order->id . '.pdf');
    }

    public function assign($id)
    {
        $order = Order::find($id);
        $deliveryMenQuery = User::where('city_id', $order->city_id)
            ->where('status', 1)
            ->where('user_type', 'delivery_man')
            ->where(function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->whereNotNull('otp_verify_at')
                    ->whereNotNull('document_verified_at');
            });
        $deliveryMen = $deliveryMenQuery->get();

        $pageTitle = __('message.assign_order');
        return view('order.assgin', compact('pageTitle', 'deliveryMen', 'id', 'order'));
    }
    public function filterOrder()
    {
        $pageTitle = __('message.order_filter');
        $params = null;

        $params = [
            'status' => request('status') ?? null,
            'from_date' => request('from_date') ?? null,
            'to_date' => request('to_date') ?? null,
            'created_at' => request('created_at') ?? null,
            'city_id' => request('city_id') ?? null,
            'country_id' => request('country_id') ?? null,
        ];
        if (!isset($params['city_id'])) {
            $params['city_id'] = null;
        }
        if (!isset($params['country_id'])) {
            $params['country_id'] = null;
        }
        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();

        return view('global.order-datatable', compact('pageTitle', 'params', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
    }
    public function draftOrder(Request $request)
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.order')]);

        $client = Auth::user();

        $orderquery = Order::where('client_id', $client->id)
            ->where(function ($query) use ($client, $request) {
                $query->where('city_id', $client->city_id)
                    ->orWhere('city_id', $request->city_id);
            })
            ->where(function ($query) use ($client, $request) {
                $query->where('country_id', $client->country_id)
                    ->orWhere('country_id', $request->country_id);
            })
            ->where('status', 'draft')->orderBy('created_at', 'asc');

        if ($request->filled('city_id')) {
            $orderquery->whereHas('city', function ($query) use ($request) {
                $query->where('id', $request->input('city_id'));
            });
        }

        if ($request->filled('country_id')) {
            $orderquery->whereHas('country', function ($query) use ($request) {
                $query->where('id', $request->input('country_id'));
            });
        }

        // $orderquery->orderBy('created_at', 'desc');
        $orders = $orderquery->get();

        $cityId = $client->city_id;
        $countryId = $client->country_id;

        if ($request->filled('city_id')) {
            $cityId = $request->input('city_id');
        }

        if ($request->filled('country_id')) {
            $countryId = $request->input('country_id');
        }
        if ($cityId != null && $countryId != null) {
            $selectedCity = City::where('id', $cityId)->pluck('name', 'id');
            $selectedCountry = Country::where('id', $countryId)->pluck('name', 'id');
        } else {
            $selectedCity = City::pluck('name', 'id')->prepend('Select City', '');
            $selectedCountry = Country::pluck('name', 'id')->prepend('Select Country', '');
        }

        return view('clientside.draftorder', compact('pageTitle', 'orders', 'selectedCity', 'selectedCountry'));
    }

    public function multipleLabel(Request $request)
    {
        $ids = $request->input('print_checked_ids');


        $orders = Order::whereIn('id', $ids)->get();

        // Fetch company info
        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companynumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();
        $labelnumber = SettingData('mobaile_number_allow', 'mobaile_number_allow');

        $barcodeBase64 = [];
        $generator = new BarcodeGeneratorPNG();
        foreach ($orders as $order) {
            $barcode = $generator->getBarcode($order->milisecond, $generator::TYPE_CODE_128);
            $barcodeBase64[$order->id] = base64_encode($barcode);
        }


        return view('order.multiplelabel', compact('orders', 'barcodeBase64', 'companyName', 'companynumber', 'companyAddress', 'invoice', 'labelnumber'));
    }

    public function getOrderDetails($id)
    {
        $order = Order::find($id);
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($order->milisecond, $generator::TYPE_CODE_128);
        $barcodeBase64 = base64_encode($barcode);
        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companynumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();
        $labelnumber = SettingData('mobaile_number_allow', 'mobaile_number_allow');

        return compact('order', 'barcodeBase64', 'companyName', 'companynumber', 'companyAddress', 'invoice', 'id', 'labelnumber');
    }

    public function labelprint($id)
    {
        $data = $this->getOrderDetails($id);
        return view('order.label', $data);
    }

    public function printorder($id)
    {
        $data = $this->getOrderDetails($id);
        return view('order.print', $data);
    }
    public function printOrderMultiple(Request $request)
    {
        $ids = $request->input('print_checked_ids');

        $orders = Order::whereIn('id', $ids)->get();

        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companyNumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $labelnumber = SettingData('mobaile_number_allow', 'mobaile_number_allow');
        $barcodeBase64 = [];

        foreach ($orders as $order) {
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($order->milisecond, $generator::TYPE_CODE_128);
            $barcodeBase64[$order->id] = base64_encode($barcode);
        }
        return view('order.multipleprint', compact('orders', 'companyName', 'companyNumber', 'barcodeBase64', 'labelnumber'));
    }
    public function getOrderDetailsQrcode($id)
    {
        $order = Order::find($id);
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($order->milisecond, $generator::TYPE_CODE_128);

        $barcodeBase64 = base64_encode($barcode);
        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companynumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();
        $labelnumber = SettingData('mobaile_number_allow', 'mobaile_number_allow');

        return compact('order', 'barcodeBase64', 'companyName', 'companynumber', 'id', 'invoice', 'labelnumber');
    }
    public function printbarcode($id)
    {
        $data = $this->getOrderDetailsQrcode($id);
        return view('order.printbarcode', $data);
    }
    public function printorderqrSingal($id)
    {
        $order = Order::find($id);

        $data = $this->getOrderDetailsQrcode($id);
        return view('order.qrcode', $data);
    }

    /**
     * Generate a professional delivery label for an order
     *
     * @param int $id Order ID
     * @return \Illuminate\View\View
     */
    public function deliveryLabel($id)
    {
        $order = Order::find($id);

        if (!$order) {
            abort(404, __('message.not_found_entry', ['name' => __('message.order')]));
        }

        // Generate barcode
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($order->milisecond, $generator::TYPE_CODE_128);
        $barcodeBase64 = base64_encode($barcode);

        // Get company data
        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companyNumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();

        // Handle company logo
        if ($invoice && $invoice->value && file_exists(public_path($invoice->value))) {
            $invoice->image_url = asset($invoice->value) . '?v=' . time();
        } else {
            $mediaUrl = getSingleMedia($invoice, 'company_logo');
            if ($mediaUrl) {
                $invoice->image_url = $mediaUrl . '?v=' . time();
            }
        }

        // Get settings
        $labelnumber = SettingData('mobaile_number_allow', 'mobaile_number_allow');

        // Format dates
        $orderDate = $order->created_at ? Carbon::parse($order->created_at)->format('M d, Y') : 'N/A';
        $shippedDate = $order->shipped_verify_at ? Carbon::parse($order->shipped_verify_at)->format('M d, Y') : 'N/A';

        return view('order.delivery_label', compact(
            'order',
            'barcodeBase64',
            'companyName',
            'companyNumber',
            'companyAddress',
            'invoice',
            'labelnumber',
            'orderDate',
            'shippedDate'
        ));
    }

    /**
     * Generate multiple delivery labels for selected orders
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function multipleDeliveryLabels(Request $request)
    {
        $ids = $request->input('print_checked_ids');
        if (empty($ids)) {
            return redirect()->back()->with('error', __('message.no_orders_selected'));
        }

        // Handle comma-separated IDs from query string
        if (is_string($ids) && strpos($ids, ',') !== false) {
            $ids = explode(',', $ids);
        }

        $orders = Order::whereIn('id', $ids)->get();

        // Company data
        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companyNumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();

        // Handle company logo
        if ($invoice && $invoice->value && file_exists(public_path($invoice->value))) {
            $invoice->image_url = asset($invoice->value) . '?v=' . time();
        } else {
            $mediaUrl = getSingleMedia($invoice, 'company_logo');
            if ($mediaUrl) {
                $invoice->image_url = $mediaUrl . '?v=' . time();
            }
        }

        // Settings
        $labelnumber = SettingData('mobaile_number_allow', 'mobaile_number_allow');

        // Generate barcodes for each order
        $barcodeBase64 = [];
        $generator = new BarcodeGeneratorPNG();
        foreach ($orders as $order) {
            $barcode = $generator->getBarcode($order->milisecond, $generator::TYPE_CODE_128);
            $barcodeBase64[$order->id] = base64_encode($barcode);

            // Add formatted dates to each order
            $order->formattedCreatedAt = $order->created_at ? Carbon::parse($order->created_at)->format('M d, Y') : 'N/A';
            $order->formattedShippedAt = $order->shipped_verify_at ? Carbon::parse($order->shipped_verify_at)->format('M d, Y') : 'N/A';
        }

        return view('order.multiple_delivery_labels', compact(
            'orders',
            'barcodeBase64',
            'companyName',
            'companyNumber',
            'companyAddress',
            'invoice',
            'labelnumber'
        ));
    }

    public function updateCourierCompany(Request $request, $id)
    {
        $setting = SettingData('order_mail', 'order_shipped_mail');
        $order = Order::findOrFail($id);

        $order->couriercompany_id = $request->input('couriercompany_id');
        $order->is_shipped = $request->has('is_shipped') ? 1 : 0;
        $order->shipped_verify_at = $request->input('shipped_verify_at', $request->date_shipped);
        $status = $request->input('status');
        if ($status === 'courier_departed' || $status === 'courier_picked_up') {
            $order->status = 'shipped';
        }
        $order->save();
        $emailData = OrderMail::where('type', $order->status)->first();
        if ($setting == 1) {
            $dynamicData = [
                '[order ID]' => $order->id,
                '[status]' => $order->status,
                '[Company name]' => config('app.name'),
            ];

            $email = $order->client_id ? $order->client->email : null;
            if ($email) {
                $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description);
                Mail::to($email)->send(new sendmail($emailData->subject, $mailDescription, [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'company_name' => config('app.name'),
                ]));
            }
        }

        $trackingId = $request->input('tracking_id');
        $courierCompanyId = $request->input('couriercompany_id');
        $trackingDetails = $request->input('tracking_details');
        $trackingNumber = $request->input('tracking_number');
        $shippingProvider = $request->input('shipping_provider');
        $dateShipped = $request->input('date_shipped');

        $courierCompany = CourierCompanies::find($courierCompanyId);
        if ($courierCompany) {
            $linkParts = explode('=', $courierCompany->link);
            $courierCompany->link = $linkParts[0] . '=' . $trackingId;

            $courierCompany->tracking_details = $trackingDetails;
            $courierCompany->tracking_number = $trackingNumber;
            $courierCompany->shipping_provider = $shippingProvider;
            $courierCompany->date_shipped = $dateShipped;
            $courierCompany->save();

            if ($order) {
                $data['order_id'] = $order->id;
                $data['history_type'] = 'shipped_order';
                $data['history_message'] = __('message.order_has_been_shipped');
                $data['history_message'] = __('message.order_has_been_shipped_via_tracking', [
                    'courier_company' => $courierCompany->name,
                    'tracking_id' => $trackingId,
                ]);
                $data['datetime'] = now();
                OrderHistory::create($data);
            }
            $notification_data = [
                'id' => $order->id,
                'type' => __('message.shipped_order'),
                'subject' => __('message.shipped_order', ['id' => $order->id]),
                'message' => $data['history_message'] ?? null,
            ];
            $admins = User::admin()->get();
            foreach ($admins as $admin) {
                $admin->notify(new CustomerSupportNotification($notification_data));
            }
            $message = __('message.shipped_order_add');
            if ($request->is('api/*')) {
                $order->is_shipped = 1;
                $order->save();
                return json_message_response($message);
            }
        } else {
            return redirect()->back()->with('error', __('message.courier_company_not_found'));
        }

        return redirect()->back()->with('success', __('message.courier_companies_updated'));
    }

    public function bulkorderdata()
    {
        $auth_user = authSession();
        if (!auth()->user()->can('bulkimport-list')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.bulk_import_order_data');

        return view('order.bulkimport', compact(['pageTitle']));
    }

    public function importorderdata(Request $request)
    {
        Excel::import(new ImportOrderdata, $request->file('order_data')->store('files'));
        $message = __('message.save_form', ['form' => __('message.order_data')]);
        return redirect()->route('bulk.order.data')->withSuccess($message);
    }

    public function orderhelp()
    {

        $pageTitle = __('message.order_data_bulk_upload_fields');

        return view('order.help', compact(['pageTitle']));
    }

    public function orderdownloadtemplate()
    {
        $pageTitle = __('message.download_template');

        return view('order.downloadtemplate', compact(['pageTitle']));
    }

    public function ordertemplateExcel()
    {
        $file = public_path("exportorder.xlsx");
        return response()->download($file);
    }

    public function isReschedule(Request $request)
    {
        $data = $request->all();

        $order = Order::find($data['order_id']);
        if ($order == null) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.order')]), 400);
        }
        if ($order) {
            $order->status = 'reschedule';
            $order->save();
        }
        $reschedule = Reschedule::create([
            'date' => now(),
            'order_id' => $order->id,
            'reason' => $data['reason'],
        ]);

        if ($order) {
            // $order->is_reschedule = $reschedule->id;
            $order->rescheduledatetime = now();
            $order->save();
            $emailData = OrderMail::where('type', 'reschedule')->first();
            if ($order->rescheduledatetime) {
                $setting = SettingData('order_mail', 'order_reschedule_mail');
                if ($setting == 1) {
                    $dynamicData = [
                        '[order ID]' => $order->id,
                        '[status]' => 'reschedule',
                        '[Company name]' => config('app.name'),
                    ];

                    $email = $order->client_id ? $order->client->email : null;
                    if ($email) {
                        $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description);
                        Mail::to($email)->send(new sendmail($emailData->subject, $mailDescription, [
                            'order_id' => $order->id,
                            'status' => 'reschedule',
                            'company_name' => config('app.name'),
                        ]));
                    }
                }
            }
        }
        if ($order) {
            $data['history_type'] = 'isrechedule';
            $data['history_message'] = __('message.order_has_been_rechedule');
            $data['datetime'] = now();
            OrderHistory::create($data);
        }
        $notification_data = [
            'id' => $order->id,
            'type' => __('message.isrechedule_order'),
            'subject' => __('message.isrechedule_order', ['id' => $order->id]),
            'message' => $data['history_message'] ?? null,
        ];
        $admins = User::admin()->get();
        foreach ($admins as $admin) {
            $admin->notify(new CustomerSupportNotification($notification_data));
        }
        $message = __('message.order_reschedule_succesfully');

        if ($request->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        return redirect()->back()->with('success', $message);
    }
    public function shippedOrder(Request $request)
    {
        // Check if Inertia request or classic view is requested
        if (!$request->has('classic') || $request->classic != 1) {
            return $this->shippedOrderInertia();
        }

        // For classic view, instantiate DataTable manually
        $datatable = new ShippedOrderDataTable();
        $pageTitle = __('message.list_form_title', ['form' => __('message.shipped_order')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $params = null;
        $multi_checkbox_delete = $auth_user->can('order-delete') ? '<button id="deleteSelectedBtn" checked-title = "order-checked " class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';
        return $datatable->render('global.order-filter', compact('pageTitle', 'auth_user', 'params', 'multi_checkbox_delete'));
    }

    /**
     * Display shipped orders using Inertia/React
     *
     * @return \Inertia\Response
     */
    public function shippedOrderInertia()
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.shipped_order')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Build query with filters
        $query = Order::query()->where('status', 'shipped');

        // Apply filters based on user type
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }

        // Apply all filters
        $this->applyFilters($query);

        // Get orders with pagination
        $orders = $query->with(['client'])
            ->paginate(15)
            ->appends(request()->query());

        // Prepare filters for React component
        $filters = [
            'order_type' => request('order_type'),
            'status' => request('status'),
            'date_start' => request('date_start'),
            'date_end' => request('date_end'),
            'client_id' => request('client_id'),
            'phone' => request('phone'),
            'pickup_location' => request('pickup_location'),
            'delivery_location' => request('delivery_location'),
            'payment_status' => request('payment_status'),
        ];

        return Inertia::render('Orders/ShippedIndex', [
            'pageTitle' => $pageTitle,
            'auth_user' => $auth_user,
            'assets' => $assets,
            'orders' => $orders,
            'filters' => $filters,
            'canDelete' => $auth_user->can('order-delete'),
        ]);
    }

    public function deliveryManVehiclehistory(Request $request)
    {
        $json_data = $request->all();
        unset($json_data['vehicle_history_image']);
        $data = json_encode($json_data, true);
        $user = auth()->user();
        $userdata = User::find($user->id);
        $userdata->vehicle_id = $request->vehicle_id;
        $userdata->save();

        $currentDatetime = now();

        $activeRecord = DeliverymanVehicleHistory::where('delivery_man_id', $user->id)
            ->where('is_active', 1)
            ->first();

        if ($activeRecord) {
            $activeRecord->update([
                'is_active' => 0,
                'end_datetime' => $currentDatetime,
            ]);
        }

        $vehicle_data = [
            'delivery_man_id' => $user->id,
            'start_datetime' => $currentDatetime,
            'end_datetime' => null,
            'is_active' => 1,
            'vehicle_info' => $data,
        ];
        $user = auth()->user();
        if ($user->hasRole('delivery_man')) {
            $model = DeliverymanVehicleHistory::create($vehicle_data);
        }

        if ($request->hasFile('vehicle_history_image')) {
            $model->clearMediaCollection('vehicle_history_image');

            foreach ($request->file('vehicle_history_image') as $image) {
                $model->addMedia($image)->toMediaCollection('vehicle_history_image');
            }
        }
        $message = __('message.deliveryman_vehicle_history');

        $item = new DeliverymanVehicleHistoryResource($model);
        $response = [
            'message' => $message,
            'data' => $item,
        ];

        return json_custom_response($response);
    }
    public function clientOrderdatatable(Request $request)
    {
        // Check if Inertia request or classic view is requested
        if (!$request->has('classic') || $request->classic != 1) {
            return $this->clientOrderdatatableInertia();
        }

        // For classic view, instantiate DataTable manually
        $datatable = new ClientOrderDataTable();
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
        return $datatable->render('global.order-filter', compact('pageTitle', 'auth_user', 'params', 'multi_checkbox_delete', 'filter_file_button', 'reset_file_button'));
    }

    /**
     * Display client orders using Inertia/React
     *
     * @return \Inertia\Response
     */
    public function clientOrderdatatableInertia()
    {
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.order')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Build query with filters
        $query = Order::query();

        // For client orders, ensure we're only showing orders for the current client
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } else {
            // If not a client, ensure we're only showing orders that have a client
            $query->whereNotNull('client_id');
        }

        // Apply all filters
        $this->applyFilters($query);

        // Get orders with pagination
        $orders = $query->with(['client'])
            ->paginate(15)
            ->appends(request()->query());

        // Prepare filters for React component
        $filters = [
            'order_type' => request('order_type'),
            'status' => request('status'),
            'date_start' => request('date_start') ?? request('from_date'),
            'date_end' => request('date_end') ?? request('to_date'),
            'client_id' => request('client_id'),
            'phone' => request('phone'),
            'pickup_location' => request('pickup_location'),
            'delivery_location' => request('delivery_location'),
            'payment_status' => request('payment_status'),
            'city_id' => request('city_id'),
            'country_id' => request('country_id'),
        ];

        return Inertia::render('Orders/ClientIndex', [
            'pageTitle' => $pageTitle,
            'auth_user' => $auth_user,
            'assets' => $assets,
            'orders' => $orders,
            'filters' => $filters,
            'canDelete' => $auth_user->can('order-delete'),
        ]);
    }

    public function applyBidOrder(Request $request)
    {
        $auth_user = auth()->user();
        $deliveryManId = $auth_user->id;

        $orderData = Order::find($request->order_id);

        if (!$orderData) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.order')]), 404);
        }
        $existingBid = OrderBid::where('order_id', $request->order_id)
            ->where('delivery_man_id', $deliveryManId)
            ->where('is_bid_accept', 0)
            ->first();

        if ($existingBid) {
            return json_message_response(__('message.already_bid_applied', ['id' => $request->order_id, 'delivery_man' => $auth_user->name]), 400);
        }
        $orderBidData = OrderBid::updateOrCreate(
            ['id' => $request->id],
            [
                'order_id' => $request->order_id,
                'is_bid_accept' => 0,
                'delivery_man_id' => $deliveryManId,
                'bid_amount' => $request->bid_amount,
                'notes' => $request->notes,
            ]
        );

        if ($orderBidData) {
            $orderData->accept_delivery_man_ids = json_encode([$deliveryManId]);
            $orderData->save();
        }

        $history_data = [
            'history_type' => 'bid_placed',
            'order_id' => $orderData->id,
            'order' => $orderData,
            'deliveryManId' => $deliveryManId,
        ];
        saveOrderHistory($history_data);

        return json_message_response(__('message.bid_applied', ['id' => $request->order_id, 'delivery_man' => $auth_user->name]), 201);
    }

    public function getBiddingDeliveryMan(Request $request)
    {
        $order_id = $request->id;
        $orderData = Order::find($order_id);
        if (!$orderData) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        $bidding_drivers = DB::table('order_bids')
            ->join('users', 'order_bids.delivery_man_id', '=', 'users.id')
            ->where('order_bids.order_id', $order_id)
            ->where('order_bids.is_bid_accept', 0)
            ->select(
                'users.id as delivery_man_id',
                'users.name as delivery_man_name',
                'order_bids.bid_amount',
                'order_bids.notes',
                'order_bids.is_bid_accept',
                'order_bids.created_at',
                'order_bids.updated_at',
            )
            ->get();
        foreach ($bidding_drivers as $driver) {
            $user = User::find($driver->delivery_man_id);
            $driver->profile_image = $user->getFirstMedia('profile_image') ?
                $user->getFirstMedia('profile_image')->getUrl() : null;
        }


        return response()->json([
            'success' => true,
            'data' => $bidding_drivers,
            'start_address' => $orderData->pickup_point['address'],
            'end_address' => $orderData->delivery_point['address'],
        ]);
    }

    public function acceptBidRequest(Request $request)
    {
        $orderData = Order::find($request->id);
        if ($orderData == null) {
            $message = __('message.not_found_entry', ['name' => __('message.order')]);
            return json_message_response($message);
        }

        if ($orderData->status == 'courier_assigned') {
            $message = __('message.not_found_entry', ['name' => __('message.order')]);
            return json_message_response($message, 400);
        }

        $deliveryMnaIds = (array) request('delivery_man_id');
        $deliveryManId = $deliveryMnaIds[0];

        if (request()->has('is_bid_accept') && request('is_bid_accept') == 1) {

            $orderBid = OrderBid::where('delivery_man_id', $deliveryManId)->first();
            $history_data = [
                'history_type' => 'bid_accept',
                'order_id' => $orderData->id,
                'order' => $orderData,
                'deliveryManId' => $deliveryManId,
            ];
            saveOrderHistory($history_data);
            $orderData->delivery_man_id = $deliveryManId;
            $orderData->status = 'courier_assigned';
            $orderData->total_amount = $orderBid->bid_amount;
            $orderData->fixed_charges = $orderBid->bid_amount;
            $orderData->save();

            $bid = OrderBid::where('order_id', $orderData->id)
                ->where('delivery_man_id', $deliveryManId)
                ->first();

            if ($bid) {
                $bid->is_bid_accept = 1;
                $bid->save();

                OrderBid::where('order_id', $orderData->id)
                    ->where('delivery_man_id', '!=', $deliveryManId)
                    ->update(['is_bid_accept' => 2]);
            }
            $history_data = [
                'history_type' => 'courier_assigned',
                'order_id' => $orderData->id,
                'order' => $orderData,
            ];
            saveOrderHistory($history_data);
            $document_name = 'order_' . $orderData->id;
            $firebaseData = app('firebase.firestore')->database()->collection('delivery_man')->document($document_name);
            if ($firebaseData) {
                $orderData = [
                    'delivery_man_ids' => (array) $orderData->delivery_man_id ?? [],
                    'order_id' => $orderData->id ?? '',
                    'client_id' => $orderData->client_id ?? '',
                    'status' => $orderData->status ?? '',
                    'client_name' => $orderData->client->name,
                    'client_email' => $orderData->client->email,
                    'client_image' => getSingleMedia($orderData->client, 'profile_image', null),
                    'delivery_man_listening' => 0,
                    'payment_status' => '',
                    'payment_type' => '',
                    'order_has_bids' => $orderData->bid_type == 1 ? 1 : 0,
                    'created_at' => $orderData->created_at,
                ];
            }
            $firebaseData->set($orderData);

            $message = __('message.updated');
        } elseif (request()->has('is_bid_accept') && request('is_bid_accept') == 2) {

            $bid = OrderBid::where('order_id', $orderData->id)
                ->where('delivery_man_id', $deliveryManId)
                ->first();

            if ($bid) {
                $bid->is_bid_accept = 2;
                $bid->save();
            } else {
                OrderBid::create([
                    'order_id' => $orderData->id,
                    'delivery_man_id' => $deliveryManId,
                    'bid_amount' => 0,
                    'is_bid_accept' => 2,
                ]);
            }
            $rejectedDeliveryMen = json_decode($orderData->reject_delivery_man_ids, true) ?? [];
            $acceptedDeliveryMen = json_decode($orderData->accept_delivery_man_ids, true) ?? [];

            if (!in_array($deliveryManId, $rejectedDeliveryMen)) {
                $rejectedDeliveryMen[] = $deliveryManId;
            }

            if (($key = array_search($deliveryManId, $acceptedDeliveryMen)) !== false) {
                unset($acceptedDeliveryMen[$key]);
            }


            $orderData->reject_delivery_man_ids = json_encode($rejectedDeliveryMen);
            $orderData->accept_delivery_man_ids = json_encode(array_values($acceptedDeliveryMen));

            $orderData->save();

            $history_data = [
                'history_type' => 'reject_bid',
                'order_id' => $orderData->id,
                'order' => $orderData,
                'deliveryManId' => $deliveryManId
            ];
            saveOrderHistory($history_data);

        } elseif (request()->has('is_bid_accept') && request('is_bid_accept') == 3) {
            $bid = OrderBid::where('order_id', $orderData->id)
                ->where('delivery_man_id', $deliveryManId)
                ->first();

            if ($bid) {
                $bid->is_bid_accept = 2;
                $bid->save();
            } else {
                OrderBid::create([
                    'order_id' => $orderData->id,
                    'delivery_man_id' => $deliveryManId,
                    'bid_amount' => 0,
                    'is_bid_accept' => 2,
                ]);
            }
            $rejectedDeliveryMen = json_decode($orderData->reject_delivery_man_ids, true) ?? [];
            $acceptedDeliveryMen = json_decode($orderData->accept_delivery_man_ids, true) ?? [];

            if (!in_array($deliveryManId, $rejectedDeliveryMen)) {
                $rejectedDeliveryMen[] = $deliveryManId;
            }

            if (($key = array_search($deliveryManId, $acceptedDeliveryMen)) !== false) {
                unset($acceptedDeliveryMen[$key]);
            }


            $orderData->reject_delivery_man_ids = json_encode($rejectedDeliveryMen);
            $orderData->accept_delivery_man_ids = json_encode(array_values($acceptedDeliveryMen));

            $orderData->save();

            $history_data = [
                'history_type' => 'deliveryman_reject_bid',
                'order_id' => $orderData->id,
                'order' => $orderData,
                'deliveryManId' => $deliveryManId
            ];
            saveOrderHistory($history_data);

        } else {
            $bid = OrderBid::where('order_id', $orderData->id)
                ->where('delivery_man_id', $deliveryManId)
                ->first();

            if ($bid) {
                $bid->is_bid_accept = 0;
                $bid->save();
            }
        }
        $response = [
            'message' => $message ?? __('message.reject_bid'),
        ];

        if ($request->is('api/*')) {
            return json_custom_response($response);
        }

        return response()->json($response);
    }

    public function assignOrder(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $deliveryManId = auth()->id();
        if ($order->status === 'create') {
            foreach (['courier_assigned', 'active'] as $status) {
                $order->status = $status;
                $order->delivery_man_id = $deliveryManId;
                $order->save();

                saveOrderHistory([
                    'history_type' => $status,
                    'order_id' => $order->id,
                    'order' => $order,
                ]);
            }
        }
        if ($order->status === 'courier_assigned') {
            saveOrderHistory([
                'history_type' => 'courier_assigned',
                'order_id' => $order->id,
                'order' => $order,
            ]);
        }

        $order->fill($request->all())->update();
        return response()->json([
            'success' => true,
            'message' => __('message.update_form', ['form' => __('message.order')])
        ]);
    }
}
