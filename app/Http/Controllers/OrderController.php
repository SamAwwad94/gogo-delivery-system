<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\OrderDataTable;
use App\Traits\ShadcnDataTableTrait;

class OrderController extends Controller
{
    use ShadcnDataTableTrait;

    /**
     * Display a listing of the resource.
     *
     * @param OrderDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(OrderDataTable $dataTable)
    {
        $pageTitle = __('message.order_list');
        $auth_user = authSession();
        $assets = ['datatable'];

        // Default to classic view
        return $dataTable->render('order.index', compact('pageTitle', 'auth_user', 'assets'));
    }

    /**
     * Display a listing of the resource using ShadCN table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex(Request $request)
    {
        $pageTitle = __('message.order_list');
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

        // Build query with filters
        $query = Order::query();

        // Apply filters based on user type
        if ($auth_user->user_type == 'client') {
            $query->where('client_id', $auth_user->id);
        } elseif ($auth_user->user_type == 'delivery_man') {
            $query->where('deliveryman_id', $auth_user->id);
        }

        // Apply order type filters
        if ($request->has('order_type')) {
            switch ($request->order_type) {
                case 'pending':
                    $query->where('status', 'create');
                    $pageTitle = __('message.pending_orders');
                    break;

                case 'schedule':
                    $tomorrow = \Carbon\Carbon::tomorrow();
                    $query->where(function ($query) use ($tomorrow) {
                        $query->whereDate('pickup_datetime', $tomorrow)
                            ->orWhereDate('delivery_datetime', $tomorrow);
                    });
                    $pageTitle = __('message.scheduled_orders');
                    break;

                case 'draft':
                    $query->where('status', 'draft');
                    $pageTitle = __('message.draft_orders');
                    break;

                case 'today':
                    $query->whereDate('created_at', \Carbon\Carbon::today());
                    $pageTitle = __('message.today_orders');
                    break;

                case 'inprogress':
                    $query->whereIn('status', ['draft', 'courier_departed', 'courier_picked_up', 'courier_assigned', 'courier_arrived', 'active']);
                    $pageTitle = __('message.inprogress_orders');
                    break;

                case 'cancelled':
                    $query->where('status', 'cancelled');
                    $pageTitle = __('message.cancelled_orders');
                    break;

                case 'completed':
                    $query->where('status', 'completed');
                    $pageTitle = __('message.completed_orders');
                    break;

                case 'shipped_order':
                    $query->where(function ($query) {
                        $query->whereNotNull('is_shipped')->where('is_shipped', '!=', 0);
                    });
                    $pageTitle = __('message.shipped_orders');
                    break;
            }
        }

        // Apply additional filters
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('id', $request->order_id);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id') && !empty($request->client_id)) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('phone') && !empty($request->phone)) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->has('pickup_location') && !empty($request->pickup_location)) {
            $query->where('pickup_point', 'like', '%' . $request->pickup_location . '%');
        }

        if ($request->has('delivery_location') && !empty($request->delivery_location)) {
            $query->where('delivery_point', 'like', '%' . $request->delivery_location . '%');
        }

        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Apply sorting
        $query->orderBy('created_at', 'desc');

        // Get orders with pagination
        $orders = $query->with(['client'])
            ->paginate(10)
            ->appends(request()->query());

        // Handle AJAX requests
        if ($request->ajax()) {
            return view('order.partials._table', compact('orders'))->render();
        }

        // Use the new unified ShadCN order view
        return view('order.shadcn-unified', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'button',
            'orders'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_order');
        $auth_user = authSession();
        $clients = User::where('user_type', 'client')->where('status', 1)->get();
        $deliverymen = User::where('user_type', 'delivery_man')->where('status', 1)->get();

        return view('order.create', compact('pageTitle', 'auth_user', 'clients', 'deliverymen'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'pickup_location' => 'required',
            'delivery_location' => 'required',
            'payment_method' => 'required',
            'total_amount' => 'required|numeric',
            'currency' => 'required'
        ]);

        $order = new Order();
        $order->client_id = $request->client_id;
        $order->deliveryman_id = $request->deliveryman_id;
        $order->pickup_location = $request->pickup_location;
        $order->delivery_location = $request->delivery_location;
        $order->payment_method = $request->payment_method;
        $order->payment_status = 'unpaid';
        $order->status = 'pending';
        $order->total_amount = $request->total_amount;
        $order->currency = $request->currency;
        $order->notes = $request->notes;
        $order->order_code = 'ORD-' . time();
        $order->save();

        return redirect()->route('order.index')->with('success', __('message.order_created_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = __('message.order_detail');
        $auth_user = authSession();
        $order = Order::with(['client', 'deliveryMan'])->findOrFail($id);

        return view('order.show', compact('pageTitle', 'auth_user', 'order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.edit_order');
        $auth_user = authSession();
        $order = Order::findOrFail($id);
        $clients = User::where('user_type', 'client')->where('status', 1)->get();
        $deliverymen = User::where('user_type', 'delivery_man')->where('status', 1)->get();

        return view('order.edit', compact('pageTitle', 'auth_user', 'order', 'clients', 'deliverymen'));
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
        $request->validate([
            'client_id' => 'required',
            'pickup_location' => 'required',
            'delivery_location' => 'required',
            'payment_method' => 'required',
            'total_amount' => 'required|numeric',
            'currency' => 'required'
        ]);

        $order = Order::findOrFail($id);
        $order->client_id = $request->client_id;
        $order->deliveryman_id = $request->deliveryman_id;
        $order->pickup_location = $request->pickup_location;
        $order->delivery_location = $request->delivery_location;
        $order->payment_method = $request->payment_method;
        $order->payment_status = $request->payment_status;
        $order->status = $request->status;
        $order->total_amount = $request->total_amount;
        $order->currency = $request->currency;
        $order->notes = $request->notes;
        $order->save();

        return redirect()->route('order.index')->with('success', __('message.order_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['success' => true, 'message' => __('message.order_deleted_successfully')]);
    }

    /**
     * Change order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true, 'message' => __('message.order_status_updated_successfully')]);
    }

    /**
     * Update a specific field of an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateField(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $field = $request->field;
        $value = $request->value;

        // Validate the field
        $allowedFields = ['status', 'payment_status', 'phone', 'id'];
        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => __('message.invalid_field')]);
        }

        // Update the field
        $order->$field = $value;
        $order->save();

        // Log the change
        activity()
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->withProperties(['field' => $field, 'old_value' => $order->getOriginal($field), 'new_value' => $value])
            ->log('updated_field');

        return response()->json(['success' => true, 'message' => __('message.field_updated_successfully')]);
    }
}
