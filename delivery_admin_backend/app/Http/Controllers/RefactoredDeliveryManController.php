<?php

namespace App\Http\Controllers;

use App\DataTables\DeliverymanDataTable;
use App\DataTables\WalletHistoryDataTable;
use App\Http\Requests\StoreDeliveryManRequest;
use App\Http\Requests\UpdateDeliveryManRequest;
use App\Http\Resources\DeliveryManEarningResource;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\WalletHistoryResource;
use App\Models\City;
use App\Models\Country;
use App\Models\DeliveryManDocument;
use App\Models\DeliverymanVehicleHistory;
use App\Models\Document;
use App\Models\Order;
use App\Models\OrderVehicleHistory;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawRequest;
use App\Services\DeliveryManService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefactoredDeliveryManController extends Controller
{
    /**
     * @var DeliveryManService
     */
    protected $deliveryManService;

    /**
     * DeliveryManController constructor.
     *
     * @param DeliveryManService $deliveryManService
     */
    public function __construct(DeliveryManService $deliveryManService)
    {
        $this->deliveryManService = $deliveryManService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param DeliverymanDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(DeliverymanDataTable $dataTable)
    {
        if (!auth()->user()->can('deliveryman-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }

        // Use ShadCN table by default, unless classic view is requested
        if (request()->has('classic') && request()->classic == 1) {
            // Continue with the original DataTable implementation
        } else {
            return $this->shadcnIndex();
        }

        $pageTitle = __('message.list_form_title', ['form' => __('message.delivery_man')]);
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
            $pageTitle = __('message.active_list_form_title', ['form' => __('message.delivery_man')]);
        } elseif (request('status') == 'inactive') {
            $pageTitle = __('message.inactive_list_form_title', ['form' => __('message.delivery_man')]);
        } elseif (request('status') == 'pending') {
            $pageTitle = __('message.pending_list_form_title', ['form' => __('message.delivery_man')]);
        }

        $reset_file_button = '<a href="' . route('refactored-deliveryman.index') . '" class=" mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $is_allow_deliveryman = SettingData('allow_deliveryman', 'allow_deliveryman');

        $button = ($is_allow_deliveryman == 0 && $auth_user->can('deliveryman-add'))
            ? '<a href="' . route('refactored-deliveryman.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.delivery_man')]) . '</a>'
            : null;

        $multi_checkbox_delete = $auth_user->can('deliveryman-delete') ? '<button id="deleteSelectedBtn" checked-title="deliveryman-checked" class="float-left btn btn-sm">' . __('message.delete_selected') . '</button>' : '';
        
        return $dataTable->with('status', request('status'))->render('global.deliveryman-filter', compact('assets', 'pageTitle', 'button', 'auth_user', 'multi_checkbox_delete', 'params', 'reset_file_button', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
    }

    /**
     * Display a listing of delivery men with ShadCN styling.
     *
     * @return \Illuminate\Http\Response
     */
    public function shadcnIndex()
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.delivery_man')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        // Get delivery men using the service
        $perPage = 15;
        $filters = [
            'status' => request('status') ?? null,
            'city_id' => request('city_id') ?? null,
            'country_id' => request('country_id') ?? null,
            'last_actived_at' => request('last_actived_at') ?? null,
            'search' => request('search') ?? null,
        ];

        $deliveryMen = $this->deliveryManService->getAllDeliveryMen($perPage, $filters);

        // Get filter data
        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();

        // Create buttons
        $reset_file_button = '<a href="' . route('refactored-deliveryman.index') . '" class="shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> ' . __('message.reset_filter') . '</a>';
        
        $is_allow_deliveryman = SettingData('allow_deliveryman', 'allow_deliveryman');
        
        $button = ($is_allow_deliveryman == 0 && $auth_user->can('deliveryman-add'))
            ? '<a href="' . route('refactored-deliveryman.create') . '" class="shadcn-button shadcn-button-primary text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg> ' . __('message.add_form_title', ['form' => __('message.delivery_man')]) . '</a>'
            : null;
            
        $multi_checkbox_delete = $auth_user->can('deliveryman-delete') ? '<button id="deleteSelectedBtn" checked-title="deliveryman-checked" class="shadcn-button shadcn-button-destructive text-sm h-9 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> ' . __('message.delete_selected') . '</button>' : '';

        return view('deliveryman.shadcn-deliverymen', compact('pageTitle', 'auth_user', 'assets', 'deliveryMen', 'button', 'multi_checkbox_delete', 'reset_file_button', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('deliveryman-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        $is_allow_deliveryman = SettingData('allow_deliveryman', 'allow_deliveryman');
        if ($is_allow_deliveryman == 0) {
            $pageTitle = __('message.add_form_title', ['form' => __('message.delivery_man')]);
            $assets = ['phone'];
            return view('deliveryman.form', compact('pageTitle', 'assets'));
        } else {
           $message = __('message.demo_permission_denied');
            return redirect()->route('refactored-deliveryman.index')->withErrors($message);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDeliveryManRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeliveryManRequest $request)
    {
        try {
            // Get validated data
            $data = $request->validated();
            
            // Create the delivery man using the service
            $deliveryMan = $this->deliveryManService->createDeliveryMan($data);
            
            $message = __('message.save_form', ['form' => __('message.delivery_man')]);
            
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-deliveryman.index')->withSuccess($message);
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
     * @param DeliverymanDataTable $dataTable
     * @param WalletHistoryDataTable $wallethistorydatatable
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(DeliverymanDataTable $dataTable, WalletHistoryDataTable $wallethistorydatatable, $id)
    {
        if (!auth()->user()->can('deliveryman-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
            $user = User::where('id', $id)->first();
            $auth_user = authSession();
            $pageTitle = __('message.view_form_title', ['form' => __('message.delivery_man')]);
            $data = User::findOrFail($id);
            $type = request('type') ?? 'detail';
            
            $requiredDocumentIds = Document::where('is_required', 1)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();
            
            switch ($type) {
                case 'detail':
                    $bank_detail = $user->userBankAccount()->orderBy('id', 'desc')->paginate(10);
                    $bank_detail_items = UserDetailResource::collection($bank_detail);
                    return $dataTable->with($id)->render('deliveryman.show', compact('pageTitle', 'type', 'data', 'bank_detail', 'bank_detail_items', 'user', 'requiredDocumentIds'));
                    break;
                
                case 'wallethistory':
                    $wallet_history = $user->userWalletHistory()->orderBy('id', 'desc')->get();
                    $wallet_history_items = WalletHistoryResource::collection($wallet_history);
                    
                    $earning_list = Payment::with('order')->withTrashed()->where('payment_status', 'paid')
                        ->whereHas('order', function ($query) use ($user) {
                            $query->whereIn('status', ['completed', 'cancelled'])->where('delivery_man_id', $user->id);
                        })->orderBy('id', 'desc')->paginate(10);
                    
                    $earning_detail_items = DeliveryManEarningResource::collection($earning_list);
                    
                    $earning_detail = User::select('id', 'name')->withTrashed()->where('id', $user->id)
                        ->with([
                            'userWallet:total_amount,total_withdrawn',
                            'getPayment:order_id,delivery_man_commission,admin_commission'
                        ])
                        ->withCount([
                            'deliveryManOrder as total_order',
                            'getPayment as paid_order' => function ($query) {
                                $query->where('payment_status', 'paid');
                            }
                        ])
                        ->withSum('userWallet', 'total_amount')
                        ->withSum('userWallet', 'total_withdrawn')
                        ->withSum('getPayment', 'admin_commission')
                        ->withSum('getPayment as delivery_man_commission', 'delivery_man_commission')
                        ->first();
                    
                    return $wallethistorydatatable->with($id)->render('deliveryman.show', compact('pageTitle', 'type', 'data', 'id', 'earning_detail', 'wallet_history', 'wallet_history_items', 'earning_list', 'earning_detail_items'));
                    break;
                
                case 'orderhistory':
                    $order = Order::where('delivery_man_id', $id)->get();
                    return view('deliveryman.show', compact('pageTitle', 'data', 'type', 'order'));
                    break;
                
                case 'withdrawrequest':
                    $wallte = Wallet::where('user_id', $id)->first();
                    $withdraw = WithdrawRequest::where('user_id', $id)->get();
                    return view('deliveryman.show', compact('pageTitle', 'data', 'type', 'withdraw', 'wallte'));
                    break;
                
                case 'document':
                    $documents = DeliveryManDocument::where('delivery_man_id', $user->id)->get();
                    return view('deliveryman.show', compact('pageTitle', 'data', 'type', 'documents'));
                    break;
                
                case 'vehicle_information':
                    $deliverymanvehicle = DeliverymanVehicleHistory::where('delivery_man_id', $user->id)->get();
                    return view('deliveryman.show', compact('pageTitle', 'data', 'type', 'deliverymanvehicle'));
                    break;
                
                default:
                    break;
            }
            
            return $dataTable->with($id)->render('deliveryman.show', compact('pageTitle', 'data', 'id', 'auth_user'));
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
        if (!auth()->user()->can('deliveryman-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
            $pageTitle = __('message.update_form_title', ['form' => __('message.delivery_man')]);
            $data = User::find($id);
            $assets = ['phone'];
            
            return view('deliveryman.form', compact('data', 'pageTitle', 'id', 'assets'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDeliveryManRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDeliveryManRequest $request, $id)
    {
        if (!auth()->user()->can('deliveryman-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        
        try {
            // Get validated data
            $data = $request->validated();
            
            // Update the delivery man using the service
            $deliveryMan = $this->deliveryManService->updateDeliveryMan($id, $data);
            
            $message = __('message.update_form', ['form' => __('message.delivery_man')]);
            
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            
            return redirect()->route('refactored-deliveryman.index')->withSuccess($message);
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
        if (!auth()->user()->can('deliveryman-delete')) {
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
            return redirect()->route('refactored-deliveryman.index')->withErrors($message);
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the delivery man using the service
            $result = $this->deliveryManService->deleteDeliveryMan($id);
            
            DB::commit();
            
            $message = __('message.delete_form', ['form' => __('message.delivery_man')]);
            
            if (request()->is('api/*')) {
                return json_message_response($message);
            }
            
            if (request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            
            return redirect()->route('refactored-deliveryman.index')->withSuccess($message);
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
        $users = User::withTrashed()->where('id', $id)->first();
        $message = __('message.not_found_entry', ['name' => __('message.delivery_man')]);
        
        if ($request->type === 'restore') {
            try {
                $result = $this->deliveryManService->restoreDeliveryMan($id);
                $message = __('message.msg_restored', ['name' => __('message.delivery_man')]);
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
                return redirect()->route('refactored-deliveryman.index')->withErrors($message);
            }
            
            try {
                $result = $this->deliveryManService->forceDeleteDeliveryMan($id);
                $message = __('message.msg_forcedelete', ['name' => __('message.delivery_man')]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
        
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }
        
        return redirect()->route('refactored-deliveryman.index')->withSuccess($message);
    }

    /**
     * Update verification status.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateVerification(Request $request)
    {
        if ($request->confirm === 'yes') {
            try {
                $this->deliveryManService->updateVerification($request->id, $request->type);
                
                switch ($request->type) {
                    case 'email':
                        $message = __('message.re_email_verification');
                        break;
                    case 'mobile':
                        $message = __('message.re_mobile_verification');
                        break;
                    case 'document':
                        $message = __('message.re_document_verification');
                        break;
                    default:
                        $message = __('message.verification_updated');
                        break;
                }
                
                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        } else {
            return redirect()->back()->with('info', __('message.cancel_verification'));
        }
    }

    /**
     * Display vehicle information for an order.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function vehicleInformationOrder(Request $request, $id)
    {
        $pageTitle = __('message.vehicle_information');
        $ordervehiclehistorydata = OrderVehicleHistory::where('order_id', $id)->get();
        
        foreach ($ordervehiclehistorydata as $history) {
            $history->vehicle_info = json_decode($history->vehicle_info, true);
        }
        
        return view('deliveryman.ordervehicleinfromation', compact('pageTitle', 'id', 'ordervehiclehistorydata'));
    }

    /**
     * Display vehicle information for a delivery man.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function vehicleInformation(Request $request, $id)
    {
        $pageTitle = __('message.vehicle_information');
        $ordervehiclehistory = DeliverymanVehicleHistory::where('id', $id)->get();
        
        foreach ($ordervehiclehistory as $history) {
            $history->vehicle_info = json_decode($history->vehicle_info, true);
        }
        
        return view('deliveryman.vehicleinfromation', compact('pageTitle', 'id', 'ordervehiclehistory'));
    }
}
