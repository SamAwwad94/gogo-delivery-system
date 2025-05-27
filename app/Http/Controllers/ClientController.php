<?php

namespace App\Http\Controllers;

use App\DataTables\ClaimsDataTable;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\DataTables\ClientDataTable;
use App\DataTables\WalletHistoryDataTable;
use App\DataTables\OrderDataTable;
use App\DataTables\ReferenceDataTable;
use App\Http\Resources\WalletHistoryResource;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\City;
use App\Models\Claims;
use App\Models\Country;
use App\Models\UserAddress;
use App\Models\WithdrawRequest;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataTableIndex(ClientDataTable $dataTable)
    {
        if (!auth()->user()->can('users-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use React/Inertia by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // Continue with the original DataTable implementation
        } else {
            return $this->indexInertia();
        }
        $pageTitle = __('message.list_form_title', ['form' => __('message.user')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $params = null;
        $params = [
            'city_id' => request('city_id') ?? null,
            'country_id' => request('country_id') ?? null,
            'last_actived_at' => request('last_actived_at') ?? null,
        ];
        if (!is_array($params['city_id']) && !is_object($params['city_id'])) {
            $params['city_id'] = null;
        }
        if (!is_array($params['country_id']) && !is_object($params['country_id'])) {
            $params['country_id'] = null;
        }
        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();

        if (request('status') == 'active') {
            $pageTitle = __('message.active_list_form_title', ['form' => __('message.user')]);
        } elseif (request('status') == 'inactive') {
            $pageTitle = __('message.inactive_list_form_title', ['form' => __('message.user')]);
        } elseif (request('status') == 'pending') {
            $pageTitle = __('message.pending_list_form_title', ['form' => __('message.user')]);
        }

        $reset_file_button = '<a href="' . route('users.index') . '" class=" mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $button = $auth_user->can('users-add') ? '<a href="' . route('users.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.user')]) . '</a>' : '';
        $multi_checkbox_delete = $auth_user->can('users-delete') ? '<button id="deleteSelectedBtn" checked-title = "users-checked" class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';
        return $dataTable->render('global.user-filter', compact('assets', 'pageTitle', 'button', 'auth_user', 'multi_checkbox_delete', 'params', 'reset_file_button', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
    }

    /**
     * Display users using Inertia/React
     *
     * @return \Inertia\Response
     */
    public function indexInertia()
    {
        if (!auth()->user()->can('users-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.user')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Build query with filters
        $query = User::where('user_type', 'client');

        // Apply filters
        $this->applyUsersFilters($query);

        // Get users with pagination
        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(request()->query());

        // Prepare filters for React component
        $filters = [
            'user_type' => request('user_type'),
            'status' => request('status'),
            'is_verified' => request('is_verified'),
            'search' => request('search'),
            'city_id' => request('city_id'),
            'country_id' => request('country_id'),
        ];

        return Inertia::render('Users/Index', [
            'pageTitle' => $pageTitle,
            'auth_user' => $auth_user,
            'assets' => $assets,
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    /**
     * Apply filters to the users query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function applyUsersFilters($query)
    {
        // User type filter
        if (request()->has('user_type') && request('user_type') != '') {
            $query->where('user_type', request('user_type'));
        }

        // Status filter
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }

        // Verification filter
        if (request()->has('is_verified') && request('is_verified') != '') {
            $isVerified = request('is_verified') == '1';
            if ($isVerified) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Search filter
        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        // City filter
        if (request()->has('city_id') && request('city_id') != '') {
            $query->where('city_id', request('city_id'));
        }

        // Country filter
        if (request()->has('country_id') && request('country_id') != '') {
            $query->where('country_id', request('country_id'));
        }
    }

    /**
     * Display a listing of users with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex()
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.user')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Get users
        $users = User::where('user_type', 'client')->orderBy('id', 'desc')->get();

        // Get filter data
        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();

        // Apply filters if any
        if (request('city_id')) {
            $users = $users->where('city_id', request('city_id'));
        }

        if (request('country_id')) {
            $users = $users->where('country_id', request('country_id'));
        }

        // Create buttons
        $reset_file_button = '<a href="' . route('users.index') . '" class="shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> ' . __('message.reset_filter') . '</a>';
        $button = $auth_user->can('users-add') ? '<a href="' . route('users.create') . '" class="shadcn-button shadcn-button-primary text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg> ' . __('message.add_form_title', ['form' => __('message.user')]) . '</a>' : '';
        $multi_checkbox_delete = $auth_user->can('users-delete') ? '<button id="deleteSelectedBtn" checked-title="users-checked" class="shadcn-button shadcn-button-destructive text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> ' . __('message.delete_selected') . '</button>' : '';

        return view('users.shadcn-users', compact('pageTitle', 'auth_user', 'assets', 'users', 'button', 'multi_checkbox_delete', 'reset_file_button', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
    }

    public function referenceindex(ReferenceDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.reference_program')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $params = null;
        $params = [
            'city_id' => request('city_id') ?? null,
            'country_id' => request('country_id') ?? null,
            'last_actived_at' => request('last_actived_at') ?? null,
        ];
        if (!is_array($params['city_id']) && !is_object($params['city_id'])) {
            $params['city_id'] = null;
        }
        if (!is_array($params['country_id']) && !is_object($params['country_id'])) {
            $params['country_id'] = null;
        }
        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();
        $reset_file_button = '<a href="' . route('reference-list') . '" class=" mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $multi_checkbox_delete = null;
        return $dataTable->render('global.reference-filter', compact('assets', 'pageTitle', 'auth_user', 'multi_checkbox_delete', 'params', 'reset_file_button', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('users-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.user')]);
        $assets = ['phone'];
        return view('users.form', compact('pageTitle', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $is_email_verification = SettingData('email_verification', 'email_verification');
        $is_mobile_verification = SettingData('mobile_verification', 'mobile_verification');

        $request['password'] = bcrypt($request->password);
        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);
        $request['display_name'] = $request['name'];
        $request['user_type'] = 'client';

        $request['referral_code'] = generateRandomCode();

        if ($is_email_verification == 0) {
            $request['email_verified_at'] = now();
        }

        if ($is_mobile_verification == 0) {
            $request['otp_verify_at'] = now();
        }
        $result = User::create($request->all());
        $result->assignRole($request->user_type);
        $message = __('message.save_form', ['form' => __('message.users')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('users.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ClientDataTable $dataTable, WalletHistoryDataTable $wallethistorydatatable, ClaimsDataTable $claimsdataTable, $id)
    {
        if (!auth()->user()->can('users-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $user = User::where('id', $id)->first();
        $pageTitle = __('message.view_form_title', ['form' => __('message.users')]);
        $data = User::findOrFail($id);
        $profileImage = getSingleMedia($data, 'profile_image');
        $type = request('type') ?? 'detail';

        switch ($type) {
            case 'detail':
                $bank_detail = $user->userBankAccount()->orderBy('id', 'desc')->paginate(10);
                $bank_detail_items = UserDetailResource::collection($bank_detail);

                return $dataTable->with($id)->render('users.show', compact('pageTitle', 'type', 'data', 'bank_detail', 'bank_detail_items', 'user'));
                break;

            case 'wallethistory':
                $wallet_history = $user->userWalletHistory()->get();
                $wallet_history_items = WalletHistoryResource::collection($wallet_history);
                $earning_detail = User::select('id', 'name')->withTrashed()->where('id', $user->id)
                    ->with([
                        'userWallet:total_amount,total_withdrawn',
                        'getPayment:order_id,admin_commission'
                    ])
                    ->withCount([
                        'deliveryManOrder as total_order',
                        'getPayment as paid_order' => function ($query) {
                            $query->where('payment_status', 'paid');
                        }
                    ])
                    ->withSum('userWallet', 'total_amount')
                    ->withSum('userWallet', 'total_withdrawn')
                    ->first();
                return $wallethistorydatatable->with('id', $id)->render('users.show', compact('pageTitle', 'type', 'data', 'wallet_history', 'wallet_history_items', 'earning_detail'));
                break;

            case 'orderhistory':
                $order = Order::where('client_id', $id)->get();
                return view('users.show', compact('pageTitle', 'data', 'type', 'order'));
                break;
            case 'withdrawrequest':
                $wallte = Wallet::where('user_id', $id)->first();
                $withdraw = WithdrawRequest::where('user_id', $id)->get();
                return view('users.show', compact('pageTitle', 'data', 'type', 'withdraw', 'wallte'));
                break;
            case 'useraddress':
                $userAddresses = UserAddress::where('user_id', $id)->get();
                return view('users.show', compact('pageTitle', 'data', 'type', 'userAddresses'));
                break;
            case 'claimsinfo':
                $claims = Claims::where('client_id', $id)->get();
                return view('users.show', compact('pageTitle', 'data', 'type', 'claims'));
                break;
            default:
                break;
        }
        return $dataTable->with($id)->render('users.show', compact('pageTitle', 'data', 'id', 'type', 'profileImage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('users-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title', ['form' => __('message.client')]);
        $data = User::findOrFail($id);
        $assets = ['phone'];

        return view('users.form', compact('data', 'pageTitle', 'id', 'assets'));
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
        if (!auth()->user()->can('users-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $user = User::findOrFail($id);

        $user->removeRole($user->user_type);
        $message = __('message.not_found_entry', ['name' => __('message.users')]);
        if ($user == null) {
            return json_custom_response(['status' => false, 'message' => $message]);
        }

        $user->fill($request->all())->update();

        $user->assignRole($request['user_type']);

        $message = __('message.update_form', ['form' => __('message.users')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('users.index')->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('users-delete')) {
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
            return redirect()->route('users.index')->withErrors($message);
        }
        $user = User::find($id);
        if ($user == null) {
            $message = __('message.not_found_entry', ['name' => __('message.users')]);
            return json_custom_response(['status' => false, 'message' => $message]);
        }
        if ($user != '') {
            $user->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.users')]);
        }

        if (request()->ajax()) {
            return json_message_response($message);
        }
        return redirect()->route('users.index')->withSuccess($message);
    }
    public function action(Request $request)
    {
        $id = $request->id;
        $users = User::withTrashed()->where('id', $id)->first();

        $message = __('message.not_found_entry', ['name' => __('message.users')]);
        if ($request->type === 'restore') {
            $users->restore();
            $message = __('message.msg_restored', ['name' => __('message.users')]);
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
                return redirect()->route('users.index')->withErrors($message);
            }
            $users->forceDelete();
            $message = __('message.force_delete_msg', ['name' => __('message.users')]);
        }
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }

        return redirect()->route('users.index')->withSuccess($message);
    }

    public function userdelete(Request $request)
    {
        $id = $request->id;
        $users = User::withTrashed()->where('id', $id)->first();
        $users->forceDelete();
        $message = __('message.delete_form', ['form' => __('message.users')]);

        return json_message_response($message);
    }
    public function frontendclientstore(UserRequest $request)
    {
        if (User::where('email', $request->email)->exists()) {
            $notification = [
                'message' => 'Email already exists',
                'alert-type' => 'error'
            ];
            return redirect()->back()->with($notification);
        }

        $request['password'] = bcrypt($request->password);
        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);
        $request['display_name'] = $request['name'];
        $request['user_type'] = 'client';

        $result = User::create($request->all());
        $result->assignRole($request->user_type);
        $message = __('message.save_form', ['form' => __('message.users')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        $notification = array(
            'message' => 'Successfully Register',
            'alert-type' => 'success'
        );

        return redirect()->route('frontend-section')->with($notification);
    }
}
