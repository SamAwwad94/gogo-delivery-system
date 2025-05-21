<?php

namespace App\Http\Controllers;

use App\DataTables\CountryDataTable;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefactoredCountryController extends Controller
{
    /**
     * @var CountryService
     */
    protected $countryService;

    /**
     * CountryController constructor.
     *
     * @param CountryService $countryService
     */
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param CountryDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(CountryDataTable $dataTable)
    {
        if (!auth()->user()->can('country-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // Continue with the original DataTable implementation
        } else {
            return $this->shadcnIndex();
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.country')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        $multi_checkbox_delete = $auth_user->can('country-delete') ? '<button id="deleteSelectedBtn" checked-title="country-checked" class="float-left btn btn-sm">' . __('message.delete_selected') . '</button>' : '';
        $button = $auth_user->can('country-add') ? '<a href="' . route('refactored-country.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.country')]) . '</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle', 'button', 'auth_user', 'multi_checkbox_delete'));
    }

    /**
     * Display a listing of countries with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex()
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.country')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Get countries using the service
        $perPage = 15;
        $filters = [
            'status' => request('status') ?? null,
            'search' => request('search') ?? null,
            'distance_type' => request('distance_type') ?? null,
            'with_counts' => true, // Add counts for better performance
        ];

        $countries = $this->countryService->getAllCountries($perPage, $filters);

        // Create buttons
        $multi_checkbox_delete = $auth_user->can('country-delete') ? '<button id="deleteSelectedBtn" checked-title="country-checked" class="shadcn-button shadcn-button-destructive text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> ' . __('message.delete_selected') . '</button>' : '';
        $button = $auth_user->can('country-add') ? '<a href="' . route('refactored-country.create') . '" class="shadcn-button shadcn-button-primary text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg> ' . __('message.add_form_title', ['form' => __('message.country')]) . '</a>' : '';

        return view('country.shadcn-countries', compact('pageTitle', 'auth_user', 'assets', 'countries', 'button', 'multi_checkbox_delete'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('country-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.add_form_title', ['form' => __('message.country')]);

        return view('country.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCountryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCountryRequest $request)
    {
        try {
            // Get validated data
            $data = $request->validated();

            // Create the country using the service
            $country = $this->countryService->createCountry($data);

            $message = __('message.save_form', ['form' => __('message.country')]);

            if ($request->is('api/*')) {
                return json_message_response($message);
            }

            return redirect()->route('refactored-country.index')->withSuccess($message);
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
            $pageTitle = __('message.view_form_title', ['form' => __('message.country')]);
            $data = Country::findOrFail($id);

            return view('country.show', compact('data', 'pageTitle'));
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
        if (!auth()->user()->can('country-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        try {
            $pageTitle = __('message.update_form_title', ['form' => __('message.country')]);
            $data = Country::findOrFail($id);

            return view('country.form', compact('data', 'pageTitle', 'id'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCountryRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCountryRequest $request, $id)
    {
        if (!auth()->user()->can('country-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        try {
            // Get validated data
            $data = $request->validated();

            // Update the country using the service
            $country = $this->countryService->updateCountry($id, $data);

            $message = __('message.update_form', ['form' => __('message.country')]);

            if ($request->is('api/*')) {
                return json_message_response($message);
            }

            return redirect()->route('refactored-country.index')->withSuccess($message);
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
        if (!auth()->user()->can('country-delete')) {
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
            return redirect()->route('refactored-country.index')->withErrors($message);
        }

        try {
            DB::beginTransaction();

            // Delete the country using the service
            $result = $this->countryService->deleteCountry($id);

            DB::commit();

            $message = __('message.delete_form', ['form' => __('message.country')]);

            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }

            if (request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message]);
            }

            return redirect()->route('refactored-country.index')->withSuccess($message);
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
        $country = Country::withTrashed()->where('id', $id)->first();
        $message = __('message.not_found_entry', ['name' => __('message.country')]);

        if ($request->type === 'restore') {
            try {
                $result = $this->countryService->restoreCountry($id);
                $message = __('message.msg_restored', ['name' => __('message.country')]);
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
                return redirect()->route('refactored-country.index')->withErrors($message);
            }

            try {
                $result = $this->countryService->forceDeleteCountry($id);
                $message = __('message.msg_forcedelete', ['name' => __('message.country')]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }

        if (request()->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->route('refactored-country.index')->withSuccess($message);
    }
}
