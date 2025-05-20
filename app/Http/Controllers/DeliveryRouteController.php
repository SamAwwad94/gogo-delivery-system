<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\User;
use App\DataTables\DeliveryRouteDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeliveryRouteController extends Controller
{
    /**
     * Display a listing of the delivery routes.
     *
     * @param DeliveryRouteDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(DeliveryRouteDataTable $dataTable)
    {
        $pageTitle = __('message.delivery_routes');
        $auth_user = authSession();
        $assets = ['datatable', 'leaflet'];
        $button = '<a href="' . route('delivery-routes.create') . '" class="shadcn-button shadcn-button-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                ' . __('message.add_form_title', ['form' => __('message.delivery_route')]) . '
            </a>';

        return $dataTable->render('delivery-routes.index', compact('pageTitle', 'button', 'auth_user', 'assets'));
    }

    /**
     * Show the form for creating a new delivery route.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.delivery_route')]);
        $auth_user = authSession();
        $assets = ['leaflet'];

        // Get delivery men for dropdown
        $deliveryMen = User::where('user_type', 'delivery_man')->where('status', 1)->get();

        // Get pending orders for dropdown
        $pendingOrders = Order::whereIn('status', ['pending', 'accepted'])->get();

        return view('delivery-routes.create', compact('pageTitle', 'auth_user', 'assets', 'deliveryMen', 'pendingOrders'));
    }

    /**
     * Store a newly created delivery route in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'delivery_man_id' => 'required|exists:users,id',
            'start_location' => 'required|string',
            'start_latitude' => 'required|numeric',
            'start_longitude' => 'required|numeric',
            'orders' => 'required|array',
            'orders.*' => 'exists:orders,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create the delivery route
        $route = new DeliveryRoute();
        $route->name = $request->name;
        $route->delivery_man_id = $request->delivery_man_id;
        $route->start_location = $request->start_location;
        $route->start_latitude = $request->start_latitude;
        $route->start_longitude = $request->start_longitude;
        $route->status = 'pending';
        $route->created_by = Auth::id();
        $route->save();

        // Attach orders to the route
        $route->orders()->attach($request->orders);

        // Update order status to 'assigned'
        Order::whereIn('id', $request->orders)->update([
            'status' => 'assigned',
            'delivery_man_id' => $request->delivery_man_id
        ]);

        return redirect()->route('delivery-routes.index')->withSuccess(__('message.save_form', ['form' => __('message.delivery_route')]));
    }

    /**
     * Display the specified delivery route.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = __('message.view_form_title', ['form' => __('message.delivery_route')]);
        $auth_user = authSession();
        $assets = ['leaflet'];

        $route = DeliveryRoute::with(['deliveryMan', 'orders', 'orders.client'])->findOrFail($id);

        return view('delivery-routes.show', compact('pageTitle', 'auth_user', 'assets', 'route'));
    }

    /**
     * Show the form for editing the specified delivery route.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.update_form_title', ['form' => __('message.delivery_route')]);
        $auth_user = authSession();
        $assets = ['leaflet'];

        $route = DeliveryRoute::with('orders')->findOrFail($id);

        // Get delivery men for dropdown
        $deliveryMen = User::where('user_type', 'delivery_man')->where('status', 1)->get();

        // Get pending orders for dropdown
        $pendingOrders = Order::whereIn('status', ['pending', 'accepted'])
            ->orWhereIn('id', $route->orders->pluck('id'))
            ->get();

        return view('delivery-routes.edit', compact('pageTitle', 'auth_user', 'assets', 'route', 'deliveryMen', 'pendingOrders'));
    }

    /**
     * Update the specified delivery route in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'delivery_man_id' => 'required|exists:users,id',
            'start_location' => 'required|string',
            'start_latitude' => 'required|numeric',
            'start_longitude' => 'required|numeric',
            'orders' => 'required|array',
            'orders.*' => 'exists:orders,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $route = DeliveryRoute::findOrFail($id);

        // Get current orders
        $currentOrderIds = $route->orders->pluck('id')->toArray();

        // Update the delivery route
        $route->name = $request->name;
        $route->delivery_man_id = $request->delivery_man_id;
        $route->start_location = $request->start_location;
        $route->start_latitude = $request->start_latitude;
        $route->start_longitude = $request->start_longitude;
        $route->save();

        // Sync orders
        $route->orders()->sync($request->orders);

        // Reset orders that were removed from the route
        $removedOrderIds = array_diff($currentOrderIds, $request->orders);
        if (!empty($removedOrderIds)) {
            Order::whereIn('id', $removedOrderIds)->update([
                'status' => 'pending',
                'delivery_man_id' => null
            ]);
        }

        // Update new orders
        $newOrderIds = array_diff($request->orders, $currentOrderIds);
        if (!empty($newOrderIds)) {
            Order::whereIn('id', $newOrderIds)->update([
                'status' => 'assigned',
                'delivery_man_id' => $request->delivery_man_id
            ]);
        }

        return redirect()->route('delivery-routes.index')->withSuccess(__('message.update_form', ['form' => __('message.delivery_route')]));
    }

    /**
     * Remove the specified delivery route from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $route = DeliveryRoute::findOrFail($id);

        // Reset orders associated with this route
        $orderIds = $route->orders->pluck('id')->toArray();
        Order::whereIn('id', $orderIds)->update([
            'status' => 'pending',
            'delivery_man_id' => null
        ]);

        // Detach orders and delete the route
        $route->orders()->detach();
        $route->delete();

        return redirect()->route('delivery-routes.index')->withSuccess(__('message.delete_form', ['form' => __('message.delivery_route')]));
    }

    /**
     * Display the delivery route map.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function map($id)
    {
        $pageTitle = __('message.route_map');
        $auth_user = authSession();
        $assets = ['leaflet'];

        $route = DeliveryRoute::with(['deliveryMan', 'orders', 'orders.client'])->findOrFail($id);

        // Check if we should use ShadCN version
        if (request()->has('regular') && request()->regular == 'true') {
            return view('delivery-routes.map', compact('pageTitle', 'auth_user', 'assets', 'route'));
        }

        // Default to ShadCN map
        return view('delivery-routes.shadcn-map', compact('pageTitle', 'auth_user', 'assets', 'route'));
    }



    /**
     * Change the status of a delivery route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, $id)
    {
        $route = DeliveryRoute::findOrFail($id);
        $route->status = $request->status;
        $route->save();

        return redirect()->back()->withSuccess(__('message.status_updated'));
    }
}
