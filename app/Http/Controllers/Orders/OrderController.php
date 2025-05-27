<?php

namespace App\Http\Controllers\Orders;

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
use App\Http\Controllers\Controller; // Added this use statement as the parent class is in a different namespace now.

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

        return view('orders.shadcn-index', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'button',
            'mockOrders' // Use mockOrders instead of orders
        ));
    }


    /**
     * Show the test page.
     *
     * @return \Illuminate\Http\Response
     */
    public function testPage()
    {
        return view('test');
    }


    /**
     * Handle inline editing of order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function inlineEdit(Request $request, $id)
    {
        if (!auth()->user()->can('order-edit')) {
            return response()->json(['message' => __('message.demo_permission_denied')], 403);
        }

        $order = Order::findOrFail($id);
        $field = $request->input('field');
        $value = $request->input('value');

        // Validate field and value
        if (!in_array($field, ['status', 'payment_status'])) {
            return response()->json(['message' => 'Invalid field for inline editing.'], 400);
        }

        // Update the order
        $order->$field = $value;
        $order->save();

        // Additional logic for status change (e.g., notifications)
        if ($field === 'status') {
            // You might want to trigger notifications or other actions here
            // For example:
            // event(new OrderStatusUpdated($order));
        }

        // Clear cache related to this order or orders list
        Cache::forget("order_{$id}");
        // Or a more general cache key if you cache the entire list
        // Cache::forget("orders_list_page_1"); // Example

        return response()->json(['message' => ucfirst($field) . ' updated successfully.']);
    }


    /**
     * Get order statistics for dashboard or reports.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // You can add more statistics as needed, e.g., revenue, average order value, etc.
        // Example: Total revenue from completed orders
        // $totalRevenue = Order::where('status', 'completed')->sum('total_amount');

        return response()->json([
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            // 'total_revenue' => $totalRevenue,
        ]);
    }


    /**
     * Get real-time updates for orders (e.g., for a live dashboard).
     * This is a basic example; for true real-time, consider WebSockets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdates(Request $request)
    {
        $lastCheckedTimestamp = $request->input('last_checked_at', Carbon::now()->subMinutes(1)->toDateTimeString());

        // Fetch orders created or updated since the last check
        $newOrUpdatedOrders = Order::where('updated_at', '>', $lastCheckedTimestamp)
            ->with(['client', 'delivery_man']) // Eager load relations if needed
            ->orderBy('updated_at', 'desc')
            ->get();

        // Fetch recently cancelled orders
        $recentlyCancelledOrders = Order::where('status', 'cancelled')
            ->where('updated_at', '>', $lastCheckedTimestamp)
            ->pluck('id'); // Get only IDs for cancellations

        // You could also fetch other types of updates, like new bids, etc.

        // Get current server time to send back to client for next request
        $currentServerTime = Carbon::now()->toDateTimeString();

        return response()->json([
            'new_or_updated_orders' => $newOrUpdatedOrders,
            'recently_cancelled_orders' => $recentlyCancelledOrders,
            'last_checked_at' => $currentServerTime,
        ]);
    }


    /**
     * Export orders to CSV.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv()
    {
        if (!auth()->user()->can('order-export')) {
            return redirect()->back()->withErrors(__('message.demo_permission_denied'));
        }

        $fileName = 'orders-' . date('Y-m-d_H-i-s') . '.csv';

        // Build the query with filters
        $query = Order::query();
        $auth_user = authSession();

        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }
        $this->applyFilters($query); // Apply common filters

        $orders = $query->with(['client', 'delivery_man', 'payment'])->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = [
            __('message.id'),
            __('message.date'),
            __('message.status'),
            __('message.customer'),
            __('message.phone'),
            __('message.pickup_location'),
            __('message.delivery_location'),
            __('message.delivery_man'),
            __('message.payment_status'),
            __('message.total_amount')
        ];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $pickupPoint = json_decode($order->pickup_point);
                $deliveryPoint = json_decode($order->delivery_point);

                $statusLabel = match ($order->status) {
                    'create' => __('message.create'),
                    'courier_assigned' => __('message.courier_assigned'),
                    'courier_accepted' => __('message.courier_accepted'),
                    'courier_arrived' => __('message.courier_arrived'),
                    'courier_picked_up' => __('message.courier_picked_up'),
                    'courier_departed' => __('message.courier_departed'),
                    'delivered' => __('message.delivered'),
                    'cancelled' => __('message.cancelled'),
                    default => ucfirst(str_replace('_', ' ', $order->status)),
                };


                $row = [
                    $order->id,
                    optional($order->created_at)->format('Y-m-d H:i'),
                    $statusLabel,
                    optional($order->client)->name ?? '-',
                    $order->phone ?? optional($order->client)->contact_number ?? '-',
                    $pickupPoint->address ?? ($order->pickup_point['address'] ?? ($order->pickup_point ?? '-')),
                    $deliveryPoint->address ?? ($order->delivery_point['address'] ?? ($order->delivery_point ?? '-')),
                    optional($order->delivery_man)->name ?? __('message.unassigned'),
                    ucfirst($order->payment_status ?? '-'),
                    $order->total_amount ?? '0.00',
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Export orders to PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        if (!auth()->user()->can('order-export')) {
            return redirect()->back()->withErrors(__('message.demo_permission_denied'));
        }

        // Build the query with filters
        $query = Order::query();
        $auth_user = authSession();

        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }
        $this->applyFilters($query); // Apply common filters

        $orders = $query->with(['client', 'delivery_man', 'payment'])->get();
        $pageTitle = __('message.order_list');

        // You might want to create a specific Blade view for PDF export
        // For simplicity, we'll pass data to a generic view or use a simple structure here.
        $pdf = Pdf::loadView('orders.export_pdf', compact('orders', 'pageTitle')); // Ensure you have this view

        return $pdf->download('orders-' . date('Y-m-d_H-i-s') . '.pdf');
    }


    public function orderprintindex(OrderPrintDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.order')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = $auth_user->can('order-add') ? '<a href="' . route('order.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.order')]) . '</a>' : '';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'button', 'assets'));
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
        $pageTitle = __('message.add_form_title', ['form' => __('message.order')]);
        $settings = AppSetting::first();
        $order = new Order;
        $order->country_id = $settings->country_id;
        $order->city_id = $settings->city_id;
        $order->pickup_point = json_decode('{"latitude": "' . $settings->latitude . '", "longitude": "' . $settings->longitude . '"}', true);
        $order->delivery_point = json_decode('{"latitude": "' . $settings->latitude . '", "longitude": "' . $settings->longitude . '"}', true);
        $order->extra_charges = StaticData::where('type', 'extra_charges')->get();
        $order->courier_companies = CourierCompanies::all();
        $order->vehicles = Vehicle::all();

        $clients = User::where('user_type', 'client')->where('status', 1)->get();
        $delivery_men = User::where('user_type', 'delivery_man')->where('status', 1)->get();

        return view('order.create', compact('pageTitle', 'order', 'clients', 'delivery_men'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('order-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $data = $request->all();

        // Validate the request data
        $validatedData = $request->validate([
            'client_id' => 'required|exists:users,id',
            'order_type' => 'required|in:single,multiple',
            'date' => 'nullable|date',
            'pickup_point.address' => 'required_if:order_type,single|string|max:255',
            'pickup_point.latitude' => 'required_if:order_type,single|numeric',
            'pickup_point.longitude' => 'required_if:order_type,single|numeric',
            'delivery_point.address' => 'required_if:order_type,single|string|max:255',
            'delivery_point.latitude' => 'required_if:order_type,single|numeric',
            'delivery_point.longitude' => 'required_if:order_type,single|numeric',
            'parcel_type' => 'nullable|string|max:255',
            'total_weight' => 'nullable|numeric|min:0',
            'total_distance' => 'nullable|numeric|min:0',
            'pickup_datetime' => 'nullable|date_format:Y-m-d H:i:s',
            'delivery_datetime' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:pickup_datetime',
            'status' => 'nullable|string|in:create,pending,courier_assigned,courier_accepted,courier_arrived,courier_picked_up,courier_departed,delivered,cancelled',
            'payment_type' => 'nullable|string|in:cash,wallet,card',
            'payment_status' => 'nullable|string|in:pending,paid,failed',
            'description' => 'nullable|string',
            'reason' => 'nullable|string',
            'fixed_charges' => 'nullable|numeric|min:0',
            'parent_order_id' => 'nullable|exists:orders,id',
            'delivery_man_id' => 'nullable|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'courier_company_id' => 'nullable|exists:courier_companies,id',
            'tracking_id' => 'nullable|string|max:255',
            'extra_charges' => 'nullable|array',
            'extra_charges.*.title' => 'required_with:extra_charges|string',
            'extra_charges.*.value' => 'required_with:extra_charges|numeric',
            'items' => 'required_if:order_type,multiple|array',
            'items.*.name' => 'required_if:order_type,multiple|string',
            'items.*.quantity' => 'required_if:order_type,multiple|integer|min:1',
            'items.*.price' => 'required_if:order_type,multiple|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);


        $data['pickup_point'] = isset($data['pickup_point']) ? json_encode($data['pickup_point']) : null;
        $data['delivery_point'] = isset($data['delivery_point']) ? json_encode($data['delivery_point']) : null;
        $data['extra_charges'] = isset($data['extra_charges']) ? json_encode($data['extra_charges']) : null;
        $data['date'] = isset($data['date']) ? date('Y-m-d H:i:s', strtotime($data['date'])) : now();
        $data['status'] = $request->status ?? 'create';

        // Calculate total amount
        $data['total_amount'] = $this->calculateOrderTotal($data);

        $order = Order::create($data);

        // Handle multiple items if order_type is 'multiple'
        if ($request->order_type === 'multiple' && isset($request->items)) {
            foreach ($request->items as $itemData) {
                $order->items()->create($itemData); // Assuming OrderItem model and relationship
            }
        }

        // Create order history entry
        OrderHistory::create([
            'order_id' => $order->id,
            'datetime' => now(),
            'history_type' => 'order_placed',
            'history_message' => __('message.order_placed_message', ['order_id' => $order->id]),
            'history_data' => json_encode($order->toArray()),
        ]);

        // Send notifications (email, SMS, push)
        $this->sendOrderCreationNotifications($order);

        // Log activity
        activity()
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $order->toArray()])
            ->log('Order created: ' . $order->id);

        return redirect()->route('order.index')->withSuccess(__('message.save_form', ['form' => __('message.order')]));
    }

    public function autoAssignCancelOrder(Request $request)
    {
        $type = $request->type;
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        if ($order != null && $order->status == 'courier_assigned') {
            $order->status = 'create';
            $order->delivery_man_id = null;
            $order->save();

            $history_data = [
                'order_id' => $order_id,
                'datetime' => date('Y-m-d H:i:s'),
                'history_type' => 'auto_assign_canceled',
                'history_message' => __('message.order_auto_assign_canceled', ['order_id' => $order_id]),
                'history_data' => json_encode($order),
            ];
            OrderHistory::create($history_data);
            $message = __('message.updated_form', ['form' => __('message.order_status')]);
        } else {
            $message = __('message.already_accepted');
        }
        return response()->json(['message' => $message]);
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
        $pageTitle = __('message.view_form_title', ['form' => __('message.order')]);
        $data = Order::with(['payment', 'orderHistory.user', 'delivery_man', 'client', 'orderReason', 'orderExtraCharge.extraCharge', 'orderItems'])->findOrFail($id);

        $auth_user = authSession();
        if ($auth_user->hasRole('admin') || $auth_user->hasRole('demo_admin') || $data->client_id == $auth_user->id || $data->delivery_man_id == $auth_user->id) {
            $profileImage = getSingleMedia($data, 'profile_image');
            $settings = Setting::first();
            $order_settings = $settings->order_setting;
            $order_setting = json_decode($order_settings, true);
            $delivery_man_accept_time = $order_setting['delivery_man_accept_time'] ?? 0;
            $delivery_man_accept_time = $delivery_man_accept_time * 60;
            $delivery_man_assigned_progress_time = 0;
            if ($data->status == 'courier_assigned') {
                $delivery_man_assigned_progress_time = (strtotime(date('Y-m-d H:i:s')) - strtotime($data->courier_assigned_datetime)) * 100 / $delivery_man_accept_time;
            }
            return view('order.show', compact('pageTitle', 'data', 'profileImage', 'delivery_man_assigned_progress_time'));
        }
        return redirect()->back()->withErrors(__('message.you_are_not_authorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('order-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title', ['form' => __('message.order')]);
        $order = Order::with('orderExtraCharge')->findOrFail($id);

        $order->extra_charges_value = $order->orderExtraCharge->mapWithKeys(function ($item) {
            return [$item->extra_charge_id => $item->extra_charge_value];
        })->all();

        $order->extra_charges = StaticData::where('type', 'extra_charges')->get();
        $order->courier_companies = CourierCompanies::all();
        $order->vehicles = Vehicle::all();

        $clients = User::where('user_type', 'client')->where('status', 1)->get();
        $delivery_men = User::where('user_type', 'delivery_man')->where('status', 1)->get();

        return view('order.create', compact('pageTitle', 'order', 'clients', 'delivery_men'));
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
        if (!auth()->user()->can('order-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $order = Order::findOrFail($id);
        $data = $request->all();

        // Validate the request data
        $validatedData = $request->validate([
            'client_id' => 'sometimes|required|exists:users,id',
            'order_type' => 'sometimes|required|in:single,multiple',
            'date' => 'nullable|date',
            'pickup_point.address' => 'sometimes|required_if:order_type,single|string|max:255',
            'pickup_point.latitude' => 'sometimes|required_if:order_type,single|numeric',
            'pickup_point.longitude' => 'sometimes|required_if:order_type,single|numeric',
            'delivery_point.address' => 'sometimes|required_if:order_type,single|string|max:255',
            'delivery_point.latitude' => 'sometimes|required_if:order_type,single|numeric',
            'delivery_point.longitude' => 'sometimes|required_if:order_type,single|numeric',
            'parcel_type' => 'nullable|string|max:255',
            'total_weight' => 'nullable|numeric|min:0',
            'total_distance' => 'nullable|numeric|min:0',
            'pickup_datetime' => 'nullable|date_format:Y-m-d H:i:s',
            'delivery_datetime' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:pickup_datetime',
            'status' => 'nullable|string|in:create,pending,courier_assigned,courier_accepted,courier_arrived,courier_picked_up,courier_departed,delivered,cancelled',
            'payment_type' => 'nullable|string|in:cash,wallet,card',
            'payment_status' => 'nullable|string|in:pending,paid,failed',
            'description' => 'nullable|string',
            'reason' => 'nullable|string',
            'fixed_charges' => 'nullable|numeric|min:0',
            'parent_order_id' => 'nullable|exists:orders,id',
            'delivery_man_id' => 'nullable|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'courier_company_id' => 'nullable|exists:courier_companies,id',
            'tracking_id' => 'nullable|string|max:255',
            'extra_charges' => 'nullable|array',
            'extra_charges.*.title' => 'required_with:extra_charges|string',
            'extra_charges.*.value' => 'required_with:extra_charges|numeric',
            'items' => 'sometimes|required_if:order_type,multiple|array',
            'items.*.name' => 'required_if:order_type,multiple|string',
            'items.*.quantity' => 'required_if:order_type,multiple|integer|min:1',
            'items.*.price' => 'required_if:order_type,multiple|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);


        $data['pickup_point'] = isset($data['pickup_point']) ? json_encode($data['pickup_point']) : $order->pickup_point;
        $data['delivery_point'] = isset($data['delivery_point']) ? json_encode($data['delivery_point']) : $order->delivery_point;
        $data['extra_charges'] = isset($data['extra_charges']) ? json_encode($data['extra_charges']) : $order->extra_charges;
        $data['date'] = isset($data['date']) ? date('Y-m-d H:i:s', strtotime($data['date'])) : $order->date;

        // Calculate total amount if relevant fields are updated
        if ($request->has('fixed_charges') || $request->has('extra_charges') || ($request->order_type === 'multiple' && $request->has('items'))) {
            $data['total_amount'] = $this->calculateOrderTotal(array_merge($order->toArray(), $data));
        }

        $order->fill($data)->save();

        // Handle multiple items if order_type is 'multiple'
        if ($request->order_type === 'multiple' && isset($request->items)) {
            $order->items()->delete(); // Remove old items
            foreach ($request->items as $itemData) {
                $order->items()->create($itemData); // Add new items
            }
        }

        // Create order history entry for update
        OrderHistory::create([
            'order_id' => $order->id,
            'datetime' => now(),
            'history_type' => 'order_updated',
            'history_message' => __('message.order_updated_message', ['order_id' => $order->id]),
            'history_data' => json_encode($order->getChanges()), // Log only changed attributes
        ]);

        // Send notifications if status or important details changed
        if ($order->wasChanged('status') || $order->wasChanged('delivery_man_id')) {
            $this->sendOrderUpdateNotifications($order);
        }

        // Log activity
        activity()
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $order->getChanges()])
            ->log('Order updated: ' . $order->id);

        return redirect()->route('order.index')->withSuccess(__('message.update_form', ['form' => __('message.order')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        if (!auth()->user()->can('order-delete')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['message' => __('message.no_items_selected')], 400);
        }

        // Ensure IDs are an array and sanitize them
        $ids = is_array($ids) ? array_map('intval', $ids) : [intval($ids)];

        // Perform deletion
        $deletedCount = Order::whereIn('id', $ids)->delete();

        // Optionally, delete related records (e.g., order history, items) if not handled by DB cascades
        // OrderHistory::whereIn('order_id', $ids)->delete();
        // OrderItem::whereIn('order_id', $ids)->delete();

        if ($deletedCount > 0) {
            // Log activity for bulk delete
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['deleted_ids' => $ids, 'count' => $deletedCount])
                ->log('Bulk orders deleted');

            return response()->json(['message' => __('message.bulk_delete_success', ['count' => $deletedCount])]);
        }

        return response()->json(['message' => __('message.no_matching_records_found')], 404);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('order-delete')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $order = Order::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.order')]);

        if ($order != '') {
            // Optionally, handle related data deletion if not cascaded
            // $order->orderHistory()->delete();
            // $order->payment()->delete();
            // $order->items()->delete(); // If OrderItem model exists

            $order->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.order')]);

            // Log activity
            activity()
                ->performedOn($order) // The deleted model instance
                ->causedBy(auth()->user())
                ->log('Order deleted: ' . $order->id);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $order = Order::findOrFail($id);

        $action_type = $request->type;
        $pageTitle = __('message.assign_form_title', ['form' => __('message.order')]);
        $delivery_man = User::where('user_type', 'delivery_man')->where('status', 1)->where('is_online', 1)->get();
        $auth_user = authSession();
        $settings = Setting::first();
        $order_settings = $settings->order_setting;
        $order_setting = json_decode($order_settings, true);
        $delivery_man_accept_time = $order_setting['delivery_man_accept_time'] ?? 0;
        $delivery_man_accept_time = $delivery_man_accept_time * 60;
        $delivery_man_assigned_progress_time = 0;
        if ($order->status == 'courier_assigned') {
            $delivery_man_assigned_progress_time = (strtotime(date('Y-m-d H:i:s')) - strtotime($order->courier_assigned_datetime)) * 100 / $delivery_man_accept_time;
        }
        switch ($action_type) {
            case 'assigned_deliveryman':
                $view = view('order.assigned_deliveryman', compact('order', 'pageTitle', 'delivery_man', 'auth_user', 'delivery_man_assigned_progress_time'))->render();
                break;
            case 'view_delivery_man_detail':
                $delivery_man_detail = User::with('userBankAccount')->find($order->delivery_man_id);
                $view = view('order.delivery_man_detail', compact('order', 'pageTitle', 'delivery_man_detail', 'auth_user'))->render();
                break;
            case 'view_tracking_history':
                $order_history = OrderHistory::where('order_id', $id)->orderBy('id', 'desc')->get();
                $view = view('order.tracking_history', compact('order', 'pageTitle', 'order_history', 'auth_user'))->render();
                break;
            case 'reassign_delivery_man':
                $view = view('order.reassign_delivery_man', compact('order', 'pageTitle', 'delivery_man', 'auth_user'))->render();
                break;
            default:
                # code...
                break;
        }
        return response()->json(['data' => $view, 'status' => true]);
    }

    public function InvoicePdf($id)
    {
        $pageTitle = __('message.view_form_title', ['form' => __('message.order')]);
        $order = Order::with('payment', 'orderHistory.user', 'delivery_man', 'client', 'orderReason', 'orderExtraCharge.extraCharge')->findOrFail($id);
        $profileImage = getSingleMedia($order, 'profile_image');
        $pdf = Pdf::loadView('order.invoicepdf', compact('order', 'pageTitle', 'profileImage'));
        return $pdf->download($order->id . '_invoice.pdf');
    }

    public function ApiInvoicePdf($id)
    {
        $pageTitle = __('message.view_form_title', ['form' => __('message.order')]);
        $order = Order::with('payment', 'orderHistory.user', 'delivery_man', 'client', 'orderReason', 'orderExtraCharge.extraCharge')->findOrFail($id);
        $profileImage = getSingleMedia($order, 'profile_image');
        $pdf = Pdf::loadView('order.invoicepdf', compact('order', 'pageTitle', 'profileImage'));
        return $pdf->download($order->id . '_invoice.pdf');
    }

    public function assign($id)
    {
        $pageTitle = __('message.assign_form_title', ['form' => __('message.order')]);
        $order = Order::findOrFail($id);
        $delivery_man = User::where('user_type', 'delivery_man')->where('status', 1)->get();
        return view('order.assign', compact('pageTitle', 'order', 'delivery_man'));
    }
    public function filterOrder()
    {
        $pageTitle = __('message.filter');
        $country = Country::pluck('name', 'id');
        $city = City::pluck('name', 'id');
        return view('order.filterOrder', compact('pageTitle', 'country', 'city'));
    }
    public function draftOrder(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = 'create';
        $order->delivery_man_id = null;
        $order->save();

        $history_data = [
            'order_id' => $order->id,
            'datetime' => date('Y-m-d H:i:s'),
            'history_type' => 'order_draft',
            'history_message' => __('message.order_draft_message', ['order_id' => $order->id]),
            'history_data' => json_encode($order),
        ];
        OrderHistory::create($history_data);

        $message = __('message.updated_form', ['form' => __('message.order_status')]);
        if (request()->ajax()) {
            return response()->json(['status' => true, 'event' => 'callbackOrder', 'message' => $message]);
        }
    }

    public function multipleLabel(Request $request)
    {
        $ids = $request->ids;
        $orders = Order::whereIn('id', explode(",", $ids))->get();
        return view('order.multiplelabel', compact('orders'));
    }

    public function getOrderDetails($id)
    {
        $order = Order::find($id);
        return response()->json($order);
    }

    public function labelprint($id)
    {
        $order = Order::find($id);
        return view('order.labelprint', compact('order'));
    }

    public function printorder($id)
    {
        $order = Order::find($id);
        return view('order.printorder', compact('order'));
    }
    public function printOrderMultiple(Request $request)
    {
        $ids = $request->ids;
        $orders = Order::whereIn('id', explode(",", $ids))->get();
        return view('order.printordermultiple', compact('orders'));
    }
    public function getOrderDetailsQrcode($id)
    {
        $order = Order::find($id);
        return response()->json($order);
    }
    public function printbarcode($id)
    {
        $order = Order::find($id);
        return view('order.printbarcode', compact('order'));
    }
    public function printorderqrSingal($id)
    {
        $order = Order::find($id);
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($order->id, $generator::TYPE_CODE_128));
        return view('order.printorderqrsingal', compact('order', 'barcode'));
    }


    /**
     * Generate and display a delivery label for a single order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deliveryLabel($id)
    {
        $order = Order::with(['client', 'delivery_man'])->findOrFail($id);
        $pageTitle = __('message.delivery_label_for_order', ['id' => $order->id]);
        $appSettings = AppSetting::first(); // Assuming you have app settings for company info

        // Generate barcode for the order ID
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = null;
        try {
            $barcodeImage = base64_encode($generator->getBarcode((string) $order->id, $generator::TYPE_CODE_128, 2, 60));
        } catch (\Exception $e) {
            // Log error or handle gracefully if barcode generation fails
            \Log::error("Barcode generation failed for order ID {$order->id}: " . $e->getMessage());
        }


        return view('orders.delivery_label', compact('order', 'pageTitle', 'appSettings', 'barcodeImage'));
    }


    /**
     * Generate and display delivery labels for multiple orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function multipleDeliveryLabels(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return redirect()->back()->withErrors(__('message.no_orders_selected'));
        }

        // Ensure IDs are an array and sanitize them
        $orderIds = is_array($ids) ? array_map('intval', $ids) : array_map('intval', explode(',', $ids));

        $orders = Order::with(['client', 'delivery_man'])->whereIn('id', $orderIds)->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->withErrors(__('message.no_orders_found_for_labels'));
        }

        $pageTitle = __('message.multiple_delivery_labels');
        $appSettings = AppSetting::first();
        $generator = new BarcodeGeneratorPNG();
        $barcodes = [];

        foreach ($orders as $order) {
            try {
                $barcodes[$order->id] = base64_encode($generator->getBarcode((string) $order->id, $generator::TYPE_CODE_128, 2, 60));
            } catch (\Exception $e) {
                \Log::error("Barcode generation failed for order ID {$order->id} in multiple labels: " . $e->getMessage());
                $barcodes[$order->id] = null; // Set to null if generation fails
            }
        }

        // You might want to use a different view for multiple labels,
        // or adapt the single label view to loop through orders.
        return view('orders.multiple_delivery_labels', compact('orders', 'pageTitle', 'appSettings', 'barcodes'));
    }


    public function updateCourierCompany(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'courier_company_id' => 'nullable|exists:courier_companies,id',
            'tracking_id' => 'nullable|string|max:255',
        ]);

        $order->courier_company_id = $validatedData['courier_company_id'] ?? null;
        $order->tracking_id = $validatedData['tracking_id'] ?? null;

        // If a courier company is assigned, you might want to update the order status
        if ($order->courier_company_id && $order->status === 'create') { // Example: update status if it was 'create'
            $order->status = 'pending_shipment'; // Or any relevant status
        }

        $order->save();

        // Create order history entry
        OrderHistory::create([
            'order_id' => $order->id,
            'datetime' => now(),
            'history_type' => 'courier_updated',
            'history_message' => __('message.courier_info_updated_for_order', [
                'order_id' => $order->id,
                'courier' => optional($order->courierCompany)->name ?? 'N/A',
                'tracking_id' => $order->tracking_id ?? 'N/A'
            ]),
            'history_data' => json_encode($validatedData),
        ]);

        return response()->json(['message' => __('message.courier_info_updated_successfully')]);
    }

    public function bulkorderdata()
    {
        $pageTitle = __('message.bulk_order_upload');
        return view('order.bulkorderdata', compact('pageTitle'));
    }

    public function importorderdata(Request $request)
    {
        Excel::import(new ImportOrderdata, $request->file('order_file'));
        return redirect()->route('order.index')->withSuccess(__('message.save_form', ['form' => __('message.order')]));
    }

    public function orderhelp()
    {
        $pageTitle = __('message.order_help');
        return view('order.orderhelp', compact('pageTitle'));
    }

    public function orderdownloadtemplate()
    {
        $pageTitle = __('message.order_download_template');
        return view('order.orderdownloadtemplate', compact('pageTitle'));
    }
    public function ordertemplateExcel()
    {
        return Excel::download(new Order, 'order.xlsx');
    }

    public function isReschedule(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = 'reschedule';
        $order->save();

        $reschedule = new Reschedule();
        $reschedule->order_id = $request->id;
        $reschedule->delivery_man_id = $request->delivery_man_id;
        $reschedule->reason = $request->reason;
        $reschedule->save();

        $history_data = [
            'order_id' => $order->id,
            'datetime' => date('Y-m-d H:i:s'),
            'history_type' => 'order_reschedule',
            'history_message' => __('message.order_reschedule_message', ['order_id' => $order->id]),
            'history_data' => json_encode($order),
        ];
        OrderHistory::create($history_data);

        $message = __('message.updated_form', ['form' => __('message.order_status')]);
        if (request()->ajax()) {
            return response()->json(['status' => true, 'event' => 'callbackOrder', 'message' => $message]);
        }
    }
    public function shippedOrder(ShippedOrderDataTable $datatable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.shipped_order')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = '';
        return $datatable->render('global.datatable', compact('pageTitle', 'auth_user', 'button', 'assets'));
    }

    public function deliveryManVehiclehistory(Request $request)
    {
        $items = DeliverymanVehicleHistory::where('delivery_man_id', $request->delivery_man_id)->orderBy('id', 'DESC')->get();
        $history_data = DeliverymanVehicleHistoryResource::collection($items);
        return response()->json(['status' => true, 'data' => $history_data, 'message' => __('message.list_form_title', ['form' => __('message.delivery_man_vehicle_history')])]);
    }
    public function clientOrderdatatable(ClientOrderDataTable $datatable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.order')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = '';
        return $datatable->render('global.datatable', compact('pageTitle', 'auth_user', 'button', 'assets'));
    }

    public function applyBidOrder(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = 'biding';
        $order->save();

        $history_data = [
            'order_id' => $order->id,
            'datetime' => date('Y-m-d H:i:s'),
            'history_type' => 'order_biding',
            'history_message' => __('message.order_biding_message', ['order_id' => $order->id]),
            'history_data' => json_encode($order),
        ];
        OrderHistory::create($history_data);

        $message = __('message.updated_form', ['form' => __('message.order_status')]);
        if (request()->ajax()) {
            return response()->json(['status' => true, 'event' => 'callbackOrder', 'message' => $message]);
        }
    }

    public function getBiddingDeliveryMan(Request $request)
    {
        $order_bid = OrderBid::where('order_id', $request->order_id)->with('delivery_man_data')->get();
        $view = view('order.bidding_deliveryman', compact('order_bid'))->render();
        return response()->json(['status' => true, 'data' => $view, 'message' => __('message.list_form_title', ['form' => __('message.delivery_man')])]);
    }

    public function acceptBidRequest(Request $request)
    {
        $order = Order::find($request->order_id);
        $order_bid = OrderBid::where('order_id', $request->order_id)->where('delivery_man_id', $request->delivery_man_id)->first();
        if ($order_bid) {
            $order->delivery_man_id = $request->delivery_man_id;
            $order->status = 'courier_assigned';
            $order->courier_assigned_datetime = date('Y-m-d H:i:s');
            $order->save();

            $history_data = [
                'order_id' => $order->id,
                'datetime' => date('Y-m-d H:i:s'),
                'history_type' => 'courier_assigned',
                'history_message' => __('message.order_assign_message', ['order_id' => $order->id, 'delivery_person_name' => $order->delivery_man->display_name]),
                'history_data' => json_encode($order),
            ];
            OrderHistory::create($history_data);

            $activity_data = [
                'activity_type' => 'order_assigned_deliveryman',
                'order_id' => $order->id,
                'order' => $order,
            ];
            saveWalletHistory($activity_data);
            $message = __('message.updated_form', ['form' => __('message.order_status')]);
            if (request()->ajax()) {
                return response()->json(['status' => true, 'event' => 'callbackOrder', 'message' => $message]);
            }
        } else {
            $message = __('message.not_found_entry', ['name' => __('message.order_bid')]);
            if (request()->ajax()) {
                return response()->json(['status' => false, 'event' => 'callbackOrder', 'message' => $message]);
            }
        }
    }

    public function assignOrder(Request $request)
    {
        $order = Order::find($request->id);
        $order->delivery_man_id = $request->delivery_man_id;
        $order->status = $request->status ?? 'courier_assigned';
        $order->courier_assigned_datetime = date('Y-m-d H:i:s');
        $order->save();

        $history_data = [
            'order_id' => $order->id,
            'datetime' => date('Y-m-d H:i:s'),
            'history_type' => 'courier_assigned',
            'history_message' => __('message.order_assign_message', ['order_id' => $order->id, 'delivery_person_name' => $order->delivery_man->display_name]),
            'history_data' => json_encode($order),
        ];
        OrderHistory::create($history_data);

        $activity_data = [
            'activity_type' => 'order_assigned_deliveryman',
            'order_id' => $order->id,
            'order' => $order,
        ];
        saveWalletHistory($activity_data);
        $message = __('message.updated_form', ['form' => __('message.order_status')]);
        if (request()->ajax()) {
            return response()->json(['status' => true, 'event' => 'callbackOrder', 'message' => $message]);
        }
    }
}