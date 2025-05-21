<?php

namespace App\Http\Controllers;

use App\DataTables\VehicleDataTable;
use App\Http\Requests\VehicleRequest;
use App\Models\City;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefactoredVehicleController extends Controller
{
    /**
     * @var VehicleService
     */
    protected $vehicleService;

    /**
     * RefactoredVehicleController constructor.
     *
     * @param VehicleService $vehicleService
     */
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param VehicleDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleDataTable $dataTable)
    {
        if (!auth()->user()->can('vehicle-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            $pageTitle = __('message.list_form_title', ['form' => __('message.vehicle')]);
            $auth_user = authSession();
            $assets = ['datatable'];

            $multi_checkbox_delete = $auth_user->can('vehicle-delete') ? '<button id="deleteSelectedBtn" checked-title="vehicle-checked" class="float-left btn btn-sm">' . __('message.delete_selected') . '</button>' : '';

            $button = $auth_user->can('vehicle-add') ? '<a href="' . route('refactored-vehicle.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.vehicle')]) . '</a>' : '';

            return $dataTable->render('global.datatable', compact('pageTitle', 'button', 'auth_user', 'multi_checkbox_delete'));
        } else {
            return $this->shadcnIndex();
        }
    }

    /**
     * Display a listing of vehicles with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex()
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.vehicle')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Get vehicles using the service
        $perPage = 15;
        $filters = [
            'status' => request('status') ?? null,
            'type' => request('type') ?? null,
            'search' => request('search') ?? null,
        ];

        $vehicles = $this->vehicleService->getAllVehicles($perPage, $filters);

        // Create button for admin
        $button = '';
        if ($auth_user->can('vehicle-add')) {
            $button = '<a href="' . route('refactored-vehicle.create') . '" class="shadcn-button shadcn-button-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                ' . __('message.add_form_title', ['form' => __('message.vehicle')]) . '
            </a>';
        }

        // Multi checkbox delete button
        $multi_checkbox_delete = $auth_user->can('vehicle-delete') ? true : false;

        // Reset filter button
        $reset_file_button = '<a href="' . route('refactored-vehicle.index') . '" class="shadcn-button shadcn-button-outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
            ' . __('message.reset_filter') . '
        </a>';

        return view('vehicle.shadcn-vehicles', compact('pageTitle', 'auth_user', 'assets', 'vehicles', 'button', 'multi_checkbox_delete', 'reset_file_button'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('vehicle-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.vehicle')]);

        return view('vehicle.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\VehicleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleRequest $request)
    {
        if (!auth()->user()->can('vehicle-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        try {
            $data = $request->all();
            
            if (!request()->is('api/*')) {
                $data['city_ids'] = json_encode($request->city_ids, true);
            }
            
            $vehicle = $this->vehicleService->createVehicle($data);
            
            $message = __('message.save_form', ['form' => __('message.vehicle')]);
            
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-vehicle.index')->withSuccess($message);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
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
        if (!auth()->user()->can('vehicle-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.view_form_title', ['form' => __('message.vehicle')]);
        $data = Vehicle::findOrFail($id);

        return view('vehicle.show', compact('data', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('vehicle-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title', ['form' => __('message.vehicle')]);
        $data = Vehicle::findOrFail($id);
        $selected_cities = [];
        if (isset($data->city_ids)) {
            $selected_cities = City::whereIn('id', $data->city_ids)->get()->pluck('name', 'id')->toArray();
        }
        return view('vehicle.form', compact('data', 'pageTitle', 'id', 'selected_cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\VehicleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequest $request, $id)
    {
        if (!auth()->user()->can('vehicle-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        try {
            $data = $request->all();
            
            if (!request()->is('api/*')) {
                $data['city_ids'] = json_encode($request->city_ids, true);
            }
            
            $vehicle = $this->vehicleService->updateVehicle($id, $data);
            
            $message = __('message.update_form', ['form' => __('message.vehicle')]);
            
            if ($request->is('api/*')) {
                return json_message_response(['message' => $message, 'status' => true]);
            }
            
            return redirect()->route('refactored-vehicle.index')->withSuccess($message);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('vehicle-delete')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        if (env('APP_DEMO')) {
            $message = __('message.demo_permission_denied');
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('refactored-vehicle.index')->withErrors($message);
        }
        
        try {
            DB::beginTransaction();
            
            $result = $this->vehicleService->deleteVehicle($id);
            
            DB::commit();
            
            $message = __('message.delete_form', ['form' => __('message.vehicle')]);
            
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            
            if (request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            
            return redirect()->route('refactored-vehicle.index')->withSuccess($message);
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->is('api/*')) {
                return json_custom_response(['error' => $e->getMessage()], 500);
            }
            
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Perform actions on the resource.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function action($id, Request $request)
    {
        if (!auth()->user()->can('vehicle-delete')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        $type = $request->type;
        $message = '';
        
        try {
            switch ($type) {
                case 'restore':
                    $this->vehicleService->restoreVehicle($id);
                    $message = __('message.restore_form', ['form' => __('message.vehicle')]);
                    break;
                case 'forcedelete':
                    if (env('APP_DEMO')) {
                        $message = __('message.demo_permission_denied');
                        return redirect()->route('refactored-vehicle.index')->withErrors($message);
                    }
                    $this->vehicleService->forceDeleteVehicle($id);
                    $message = __('message.forcedelete_form', ['form' => __('message.vehicle')]);
                    break;
            }
            
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            
            return redirect()->route('refactored-vehicle.index')->withSuccess($message);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
