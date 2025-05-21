<?php

namespace App\Http\Controllers;

use App\DataTables\CityDataTable;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefactoredCityController extends Controller
{
    /**
     * @var CityService
     */
    protected $cityService;

    /**
     * CityController constructor.
     *
     * @param CityService $cityService
     */
    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param CityDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(CityDataTable $dataTable)
    {
        if (!auth()->user()->can('city-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // Continue with the original DataTable implementation
        } else {
            return $this->shadcnIndex();
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.city')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        $multi_checkbox_delete = $auth_user->can('city-delete') ? '<button id="deleteSelectedBtn" checked-title="city-checked" class="float-left btn btn-sm">' . __('message.delete_selected') . '</button>' : '';
        $button = $auth_user->can('city-add') ? '<a href="' . route('refactored-city.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.city')]) . '</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle', 'button', 'auth_user', 'multi_checkbox_delete'));
    }

    /**
     * Display a listing of cities with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex()
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.city')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Get cities using the service
        $perPage = 15;
        $filters = [
            'status' => request('status') ?? null,
            'country_id' => request('country_id') ?? null,
            'search' => request('search') ?? null,
        ];

        $cities = $this->cityService->getAllCities($perPage, $filters);

        // Create buttons
        $multi_checkbox_delete = $auth_user->can('city-delete') ? '<button id="deleteSelectedBtn" checked-title="city-checked" class="shadcn-button shadcn-button-destructive text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> ' . __('message.delete_selected') . '</button>' : '';
        $button = $auth_user->can('city-add') ? '<a href="' . route('refactored-city.create') . '" class="shadcn-button shadcn-button-primary text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg> ' . __('message.add_form_title', ['form' => __('message.city')]) . '</a>' : '';

        return view('city.shadcn-cities', compact('pageTitle', 'auth_user', 'assets', 'cities', 'button', 'multi_checkbox_delete'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('city-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.add_form_title', ['form' => __('message.city')]);

        return view('city.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCityRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCityRequest $request)
    {
        try {
            // Get validated data
            $data = $request->validated();
            
            // Create the city using the service
            $city = $this->cityService->createCity($data);
            
            $message = __('message.save_form', ['form' => __('message.city')]);
            
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-city.index')->withSuccess($message);
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
            $pageTitle = __('message.view_form_title', ['form' => __('message.city')]);
            $data = City::findOrFail($id);
            
            return view('city.show', compact('data', 'pageTitle'));
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
        if (!auth()->user()->can('city-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
            $pageTitle = __('message.update_form_title', ['form' => __('message.city')]);
            $data = City::findOrFail($id);
            
            return view('city.form', compact('data', 'pageTitle', 'id'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCityRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCityRequest $request, $id)
    {
        if (!auth()->user()->can('city-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
            // Get validated data
            $data = $request->validated();
            
            // Update the city using the service
            $city = $this->cityService->updateCity($id, $data);
            
            $message = __('message.update_form', ['form' => __('message.city')]);
            
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-city.index')->withSuccess($message);
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
        if (!auth()->user()->can('city-delete')) {
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
            return redirect()->route('refactored-city.index')->withErrors($message);
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the city using the service
            $result = $this->cityService->deleteCity($id);
            
            DB::commit();
            
            $message = __('message.delete_form', ['form' => __('message.city')]);
            
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            
            if (request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            
            return redirect()->route('refactored-city.index')->withSuccess($message);
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function action(Request $request)
    {
        $id = $request->id;
        $city = City::withTrashed()->where('id', $id)->first();
        $message = __('message.not_found_entry', ['name' => __('message.city')]);
        
        if ($request->type === 'restore') {
            try {
                $result = $this->cityService->restoreCity($id);
                $message = __('message.msg_restored', ['name' => __('message.city')]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
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
                return redirect()->route('refactored-city.index')->withErrors($message);
            }
            
            try {
                $result = $this->cityService->forceDeleteCity($id);
                $message = __('message.msg_forcedelete', ['name' => __('message.city')]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
        
        if (request()->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        
        return redirect()->route('refactored-city.index')->withSuccess($message);
    }
}
