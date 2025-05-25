<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderModernController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:order-list')->only('index');
    }

    /**
     * Display a listing of the orders with modern UI.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check permissions
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.modern_orders');
        $currentUser = auth()->user();
        $auth_user_data = $currentUser ? [
            'id' => $currentUser->id,
            'name' => $currentUser->name,
            'email' => $currentUser->email,
            // Add any other specific user properties needed by the Index.jsx page
        ] : null;
        $assets = ['datatable'];

        // Get customers for filter dropdown
        $customers = User::where('user_type', 'client')->where('status', 1)->get();

        // Build query with filters
        $query = Order::with(['client', 'payment', 'delivery_man']);

        // Apply filters from request
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('id', $request->order_id);
        }

        if ($request->has('date_start') && !empty($request->date_start)) {
            $query->whereDate('date', '>=', $request->date_start);
        }

        if ($request->has('date_end') && !empty($request->date_end)) {
            $query->whereDate('date', '<=', $request->date_end);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer') && !empty($request->customer)) {
            $query->where('client_id', $request->customer);
        }

        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }

        // Get orders with pagination
        $perPage = $request->input('per_page', 10);
        $orders = $query->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return Inertia::render('Admin/OrdersModern/Index', [
            'pageTitle' => $pageTitle,
            'auth_user' => $auth_user_data,
            'assets' => $assets,
            'orders' => $orders,
            'customers' => $customers,
        ]);
    }

    /**
     * Export orders to CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Check permissions
        if (!auth()->user()->can('order-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Build query with filters
        $query = Order::with(['client', 'payment', 'delivery_man']);

        // Apply filters from request
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('id', $request->order_id);
        }

        if ($request->has('date_start') && !empty($request->date_start)) {
            $query->whereDate('date', '>=', $request->date_start);
        }

        if ($request->has('date_end') && !empty($request->date_end)) {
            $query->whereDate('date', '<=', $request->date_end);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer') && !empty($request->customer)) {
            $query->where('client_id', $request->customer);
        }

        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }

        // Get orders
        $orders = $query->orderBy('id', 'desc')->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-export-' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
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
                'Amount'
            ]);

            // Add data
            foreach ($orders as $order) {
                $pickupPoint = $order->pickup_point ? json_decode($order->pickup_point) : null;
                $deliveryPoint = $order->delivery_point ? json_decode($order->delivery_point) : null;

                fputcsv($file, [
                    $order->id,
                    $order->date,
                    $order->status,
                    $order->client->name ?? 'N/A',
                    $order->client->contact_number ?? 'N/A',
                    $pickupPoint ? $pickupPoint->address : 'N/A',
                    $deliveryPoint ? $deliveryPoint->address : 'N/A',
                    $order->payment ? $order->payment->payment_status : 'N/A',
                    $order->total_amount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
