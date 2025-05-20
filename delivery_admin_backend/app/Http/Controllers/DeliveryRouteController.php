<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeliveryRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $auth_user = Auth::user();
        $user_role = $auth_user->roles->first();

        $query = DeliveryRoute::query();

        // Apply filters
        // Route ID filter - exact match
        if ($request->has('route_id') && !empty($request->route_id)) {
            $query->where('id', $request->route_id);
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Driver filter
        if ($request->has('driver_id') && !empty($request->driver_id)) {
            $query->where('deliveryman_id', $request->driver_id);
        }

        // Zone filter (using start_location)
        if ($request->has('zone') && !empty($request->zone)) {
            $zone = $request->zone;
            $query->where('start_location', 'LIKE', "%$zone%");
        }

        // Vehicle filter (need to join with users and their vehicles)
        if ($request->has('vehicle') && !empty($request->vehicle)) {
            $vehicle = $request->vehicle;
            $query->whereHas('deliveryman', function ($q) use ($vehicle) {
                $q->where('vehicle_id', $vehicle);
            });
        }

        // Orders Count filter (need to count related orders)
        if ($request->has('orders_count') && !empty($request->orders_count)) {
            $ordersCount = $request->orders_count;
            $query->withCount('orders')->having('orders_count', '=', $ordersCount);
        }

        // General search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // Use regular table if explicitly requested
        if ($request->has('regular') && $request->regular == 'true') {
            $delivery_routes = $query->orderBy('id', 'desc')->paginate(10);
            return view('delivery-routes.index', compact('delivery_routes'));
        }

        // Get all route IDs for the dropdown
        $routeIds = DeliveryRoute::pluck('id')->toArray();

        // Get data for filters
        $drivers = User::whereHas('roles', function ($q) {
            $q->where('name', 'deliveryman');
        })->get();

        // Get vehicles - convert to array to avoid collection methods issue
        $vehiclesCollection = Vehicle::where('status', 1)->get();
        $vehicles = [];
        foreach ($vehiclesCollection as $vehicle) {
            $vehicles[] = $vehicle;
        }

        $zones = ['Beirut', 'Tripoli', 'Sidon', 'Tyre', 'Jounieh', 'Zahle']; // Example zones

        // Get statuses from the database or use predefined values
        $statuses = ['active', 'inactive'];

        // Get routes with pagination
        $perPage = $request->input('per_page', 10);
        $delivery_routes = $query->with('deliveryman')
            ->withCount('orders')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Default to ShadCN table
        $pageTitle = 'Delivery Routes';
        $auth_user = authSession();
        $assets = ['datatable'];

        return view('shadcn.delivery-routes', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'delivery_routes',
            'drivers',
            'vehicles',
            'zones',
            'statuses',
            'routeIds'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        $countries = Country::all();
        $deliverymen = User::whereHas('roles', function ($q) {
            $q->where('name', 'deliveryman');
        })->get();

        return view('delivery-routes.create', compact('cities', 'countries', 'deliverymen'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_location' => 'required|string',
            'end_location' => 'required|string',
            'waypoints' => 'nullable|string',
            'deliveryman_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $delivery_route = new DeliveryRoute();
        $delivery_route->name = $request->name;
        $delivery_route->description = $request->description;
        $delivery_route->start_location = $request->start_location;
        $delivery_route->end_location = $request->end_location;
        $delivery_route->waypoints = $request->waypoints;
        $delivery_route->deliveryman_id = $request->deliveryman_id;
        $delivery_route->status = $request->status;
        $delivery_route->save();

        return redirect()->route('delivery-routes.index')
            ->with('success', __('message.created_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $delivery_route = DeliveryRoute::findOrFail($id);
        return view('delivery-routes.show', compact('delivery_route'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $delivery_route = DeliveryRoute::findOrFail($id);
        $cities = City::all();
        $countries = Country::all();
        $deliverymen = User::whereHas('roles', function ($q) {
            $q->where('name', 'deliveryman');
        })->get();

        return view('delivery-routes.edit', compact('delivery_route', 'cities', 'countries', 'deliverymen'));
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_location' => 'required|string',
            'end_location' => 'required|string',
            'waypoints' => 'nullable|string',
            'deliveryman_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $delivery_route = DeliveryRoute::findOrFail($id);
        $delivery_route->name = $request->name;
        $delivery_route->description = $request->description;
        $delivery_route->start_location = $request->start_location;
        $delivery_route->end_location = $request->end_location;
        $delivery_route->waypoints = $request->waypoints;
        $delivery_route->deliveryman_id = $request->deliveryman_id;
        $delivery_route->status = $request->status;
        $delivery_route->save();

        return redirect()->route('delivery-routes.index')
            ->with('success', __('message.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delivery_route = DeliveryRoute::findOrFail($id);
        $delivery_route->delete();

        return redirect()->route('delivery-routes.index')
            ->with('success', __('message.deleted_successfully'));
    }

    /**
     * Display the map view for a delivery route.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function map($id)
    {
        $delivery_route = DeliveryRoute::findOrFail($id);

        // Use regular map if explicitly requested
        if (request()->has('regular') && request()->regular == 'true') {
            return view('delivery-routes.map', compact('delivery_route'));
        }

        // Default to ShadCN map
        $pageTitle = 'Delivery Route Map';
        $auth_user = authSession();
        $assets = ['datatable', 'leaflet'];

        return view('shadcn.delivery-routes-map', compact('pageTitle', 'auth_user', 'assets', 'delivery_route'));
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
        $delivery_route = DeliveryRoute::findOrFail($id);
        $delivery_route->status = $request->status;
        $delivery_route->save();

        return response()->json(['success' => true]);
    }
}
