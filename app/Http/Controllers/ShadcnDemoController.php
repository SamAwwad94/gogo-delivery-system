<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\ShadcnOrderDataTable;

class ShadcnDemoController extends Controller
{
    /**
     * Display a listing of the orders with ShadCN styling.
     *
     * @param ShadcnOrderDataTable $dataTable
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View
     */
    public function orders(ShadcnOrderDataTable $dataTable)
    {
        $pageTitle = 'Orders';
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = '<a href="' . route('order.create') . '" class="shadcn-button shadcn-button-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                ' . __('message.add_form_title', ['form' => __('message.order')]) . '
            </a>';

        // Get orders
        $perPage = 15;
        $query = \App\Models\Order::query();

        // Apply filters based on user type
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }

        // Apply additional filters if provided
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }

        if (request()->has('payment_status') && request('payment_status') != '') {
            $query->where('payment_status', request('payment_status'));
        }

        if (request()->has('delivery_status') && request('delivery_status') != '') {
            $query->where('delivery_status', request('delivery_status'));
        }

        if (request()->has('date_range') && request('date_range') != '') {
            $dateRange = request('date_range');
            $today = now()->format('Y-m-d');

            if ($dateRange == 'today') {
                $query->whereDate('created_at', $today);
            } elseif ($dateRange == 'yesterday') {
                $query->whereDate('created_at', now()->subDay()->format('Y-m-d'));
            } elseif ($dateRange == 'this_week') {
                $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d'), $today]);
            } elseif ($dateRange == 'last_week') {
                $query->whereBetween('created_at', [
                    now()->subWeek()->startOfWeek()->format('Y-m-d'),
                    now()->subWeek()->endOfWeek()->format('Y-m-d')
                ]);
            } elseif ($dateRange == 'this_month') {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            } elseif ($dateRange == 'last_month') {
                $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year);
            }
        }

        // Get orders with pagination
        $orders = $query->with(['client', 'delivery_man'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Add status colors
        $orders->each(function ($order) {
            // Order status colors
            switch ($order->status) {
                case 'draft':
                    $order->status_color = 'light';
                    break;
                case 'create':
                    $order->status_color = 'primary';
                    break;
                case 'completed':
                    $order->status_color = 'success';
                    break;
                case 'courier_assigned':
                    $order->status_color = 'warning';
                    break;
                case 'courier_accepted':
                    $order->status_color = 'info';
                    break;
                case 'courier_arrived':
                    $order->status_color = 'info';
                    break;
                case 'courier_picked_up':
                    $order->status_color = 'info';
                    break;
                case 'courier_departed':
                    $order->status_color = 'info';
                    break;
                case 'cancelled':
                    $order->status_color = 'danger';
                    break;
                default:
                    $order->status_color = 'primary';
                    break;
            }

            // Set customer name
            $order->customer_name = $order->client ? $order->client->display_name : 'N/A';

            // Set payment status and delivery status for compatibility
            $order->payment_status = 'N/A';
            $order->payment_status_color = 'light';
            $order->delivery_status = $order->status;
            $order->delivery_status_color = $order->status_color;

            // Set currency
            $order->currency = $order->currency ?? 'LBP';
            $order->total = $order->total_amount ?? 0;
        });

        // Use the DataTable for the main table display
        if (request()->ajax()) {
            return $dataTable->render('layouts.shadcn-datatable', compact('pageTitle', 'button', 'auth_user', 'assets'));
        }

        // Check if delivery routes view is requested
        if (request()->has('new_ui') && request('new_ui') == 'true') {
            $pageTitle = 'Delivery Routes';
            return view('shadcn.delivery-routes', compact('pageTitle', 'auth_user', 'assets', 'orders', 'button'));
        }

        // Use our custom view for the full page with filters
        return view('shadcn.orders', compact('pageTitle', 'auth_user', 'assets', 'orders', 'button'));
    }
}
