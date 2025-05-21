<?php

namespace App\Http\Controllers;

use App\DataTables\ClaimsDataTable;
use App\DataTables\ClientDataTable;
use App\DataTables\WalletHistoryDataTable;
use App\DataTables\OrderDataTable;
use App\DataTables\ReferenceDataTable;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
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
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefactoredClientController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * ClientController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param ClientDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(ClientDataTable $dataTable)
    {
        if (!auth()->user()->can('users-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // Continue with the original DataTable implementation
        } else {
            return $this->shadcnIndex();
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.user')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        
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

        $reset_file_button = '<a href="' . route('refactored-client.index') . '" class=" mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $button = $auth_user->can('users-add') ? '<a href="' . route('refactored-client.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.user')]) . '</a>' : '';
        $multi_checkbox_delete = $auth_user->can('users-delete') ? '<button id="deleteSelectedBtn" checked-title = "users-checked" class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';
        
        return $dataTable->render('global.user-filter', compact('assets', 'pageTitle', 'button', 'auth_user', 'multi_checkbox_delete', 'params', 'reset_file_button', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
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

        // Get users using the service
        $perPage = 15;
        $filters = [
            'user_type' => 'client',
            'status' => request('status') ?? null,
            'city_id' => request('city_id') ?? null,
            'country_id' => request('country_id') ?? null,
        ];

        $users = $this->userService->getAllUsers($perPage, $filters);

        // Get filter data
        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();

        // Create buttons
        $reset_file_button = '<a href="' . route('refactored-client.index') . '" class="shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> ' . __('message.reset_filter') . '</a>';
        $button = $auth_user->can('users-add') ? '<a href="' . route('refactored-client.create') . '" class="shadcn-button shadcn-button-primary text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg> ' . __('message.add_form_title', ['form' => __('message.user')]) . '</a>' : '';
        $multi_checkbox_delete = $auth_user->can('users-delete') ? '<button id="deleteSelectedBtn" checked-title="users-checked" class="shadcn-button shadcn-button-destructive text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> ' . __('message.delete_selected') . '</button>' : '';

        return view('users.shadcn-users', compact('pageTitle', 'auth_user', 'assets', 'users', 'button', 'multi_checkbox_delete', 'reset_file_button', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
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
     * @param StoreClientRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        try {
            // Get validated data
            $data = $request->validated();
            
            // Add additional data
            $data['password'] = bcrypt($request->password);
            $data['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);
            $data['display_name'] = $data['name'];
            $data['user_type'] = 'client';
            $data['referral_code'] = generateRandomCode();
            
            // Handle email and mobile verification settings
            $is_email_verification = SettingData('email_verification', 'email_verification');
            $is_mobile_verification = SettingData('mobile_verification', 'mobile_verification');
            
            if ($is_email_verification == 0) {
                $data['email_verified_at'] = now();
            }
            
            if ($is_mobile_verification == 0) {
                $data['otp_verify_at'] = now();
            }
            
            // Create the user using the service
            $user = $this->userService->createUser($data);
            
            $message = __('message.save_form', ['form' => __('message.users')]);
            
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-client.index')->withSuccess($message);
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
     * @param ClientDataTable $dataTable
     * @param WalletHistoryDataTable $wallethistorydatatable
     * @param ClaimsDataTable $claimsdataTable
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(ClientDataTable $dataTable, WalletHistoryDataTable $wallethistorydatatable, ClaimsDataTable $claimsdataTable, $id)
    {
        if (!auth()->user()->can('users-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
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
        if (!auth()->user()->can('users-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
            $pageTitle = __('message.update_form_title', ['form' => __('message.client')]);
            $data = User::findOrFail($id);
            $assets = ['phone'];
            
            return view('users.form', compact('data', 'pageTitle', 'id', 'assets'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, $id)
    {
        if (!auth()->user()->can('users-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
            // Get validated data
            $data = $request->validated();
            
            // Update the user using the service
            $user = $this->userService->updateUser($id, $data);
            
            $message = __('message.update_form', ['form' => __('message.users')]);
            
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-client.index')->withSuccess($message);
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
            return redirect()->route('refactored-client.index')->withErrors($message);
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the user using the service
            $result = $this->userService->deleteUser($id);
            
            DB::commit();
            
            $message = __('message.delete_form', ['form' => __('message.users')]);
            
            if (request()->ajax()) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-client.index')->withSuccess($message);
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return json_custom_response(['error' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
