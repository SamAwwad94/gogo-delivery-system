<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Models\Setting;
use App\Models\User;
use App\Models\PaymentGateway;
use App\Models\SMSSetting;
use App\Http\Requests\UserRequest;
use App\Models\FrontendData;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Picqer\Barcode\BarcodeGeneratorPNG;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings(Request $request)
    {
        $auth_user = auth()->user();
        $assets = ['phone'];
        $pageTitle = __('message.setting');
        $page = $request->page;

        if ($page == '') {
            if ($auth_user->hasAnyRole(['admin', 'demo_admin'])) {
                $page = 'general-setting';
            } else {
                $page = 'profile_form';
            }
        }

        return view('setting.index', compact('page', 'pageTitle', 'auth_user', 'assets'));
    }

    public function layoutPage(Request $request)
    {
        // dd($request->all());
        $page = $request->page;
        if ($page == 'payment-setting') {
            $type = isset($request->type) ? $request->type : 'stripe';
        }
        if ($page == 'sms-settings') {
            $type = isset($request->type) ? $request->type : 'twilio';
        }
        $auth_user = auth()->user();
        $user_id = $auth_user->id;
        $settings = AppSetting::first();
        $user_data = User::find($user_id);
        $envSettting = $envSettting_value = [];

        if (count($envSettting) > 0) {
            $envSettting_value = Setting::whereIn('key', array_keys($envSettting))->get();
        }
        if ($settings == null) {
            $settings = new AppSetting;
        } elseif ($user_data == null) {
            $user_data = new User;
        }
        switch ($page) {
            case 'password_form':
                $data = view('setting.' . $page, compact('settings', 'user_data', 'page'))->render();
                break;
            case 'profile_form':
                $assets = ['phone'];
                $data = view('setting.' . $page, compact('settings', 'user_data', 'page', 'assets'))->render();
                break;
            case 'mail-setting':
                $data = view('setting.' . $page, compact('settings', 'page'))->render();
                break;
            case 'logo-settings':
                $pageTitle = __('message.logo_management');
                $data = view('setting.' . $page, compact('page', 'pageTitle'))->render();
                break;
            case 'mobile-config':
                $setting = Config::get('mobile-config');
                $getSetting = [];
                foreach ($setting as $k => $s) {
                    foreach ($s as $sk => $ss) {
                        $getSetting[] = $k . '_' . $sk;
                    }
                }

                $setting_value = Setting::whereIn('key', $getSetting)->get();

                $data = view('setting.' . $page, compact('setting', 'setting_value', 'page'))->render();
                break;
            case 'reference-setting':
                $setting = Config::get('reference');
                $getSetting = [];
                foreach ($setting as $k => $s) {
                    foreach ($s as $sk => $ss) {
                        $getSetting[] = $sk;
                    }
                }

                $setting_value = Setting::whereIn('key', $getSetting)->get();

                $data = view('setting.' . $page, compact('setting', 'setting_value', 'page'))->render();
                break;
            case 'insurance-setting':
                $setting = Config::get('insurance');
                $getSetting = [];
                foreach ($setting as $k => $s) {
                    foreach ($s as $sk => $ss) {
                        $getSetting[] = $sk;
                    }
                }

                $setting_value = Setting::whereIn('key', $getSetting)->get();

                $data = view('setting.' . $page, compact('setting', 'setting_value', 'page'))->render();
                break;

            case 'notification-setting':
                $notification_setting = config('constant.notification');
                $notification_setting_data = AppSetting::first();
                $data = view('setting.' . $page, compact('notification_setting', 'notification_setting_data'))->render();
                break;
            case 'payment-setting':
                $payment_setting_data = PaymentGateway::where('type', $type)->first();
                $data = view('setting.' . $page, compact('settings', 'page', 'type', 'payment_setting_data'))->render();
                break;
            case 'invoice-setting':
                $pageTitle = __('message.invoice');
                $invoice = config('constant.order_invoice');
                foreach ($invoice as $key => $val) {
                    if (in_array($key, ['company_name', 'company_contact_number', 'company_address'])) {
                        $invoice[$key] = Setting::where('type', 'order_invoice')->where('key', $key)->pluck('value')->first();
                    } else {
                        $invoice[$key] = Setting::where('type', 'order_invoice')->where('key', $key)->first();
                    }
                }
                $data = view('setting.' . $page, compact('invoice', 'pageTitle'))->render();
                break;
            case 'order-setting':
                $setting = Config::get('order-config');
                $getSetting = [];
                foreach ($setting as $k => $s) {
                    foreach ($s as $hd => $ss) {
                        $getSetting[] = $hd;
                    }
                }
                $setting_value = AppSetting::get();
                $pageTitle = __('message.add_form_title', ['form' => __('message.order-setting')]);
                $data = view('setting.' . $page, compact('setting', 'page', 'setting_value', 'pageTitle'))->render();
                break;
            case 'register-setting':
                $pageTitle = __('message.register_setting');
                $setting = Config::get('register-config');
                $getSetting = [];
                foreach ($setting as $k => $s) {
                    foreach ($s as $sk => $ss) {
                        $getSetting[] = $sk;
                    }
                }

                $setting_value = Setting::whereIn('key', $getSetting)->get();

                $data = view('setting.' . $page, compact('setting', 'setting_value', 'page', 'pageTitle'))->render();
                break;
            case 'ordermail-setting':
                $setting = Config::get('order-mail');
                $getSetting = [];
                foreach ($setting as $k => $s) {
                    foreach ($s as $sk => $ss) {
                        $getSetting[] = $sk;
                    }
                }
                $setting_value = Setting::whereIn('key', $getSetting)->get();
                $pageTitle = __('message.mail_template_setting');
                $data = view('setting.' . $page, compact('page', 'setting_value', 'setting', 'pageTitle'))->render();
                break;

            case 'database-backup':
                $setting_value = AppSetting::get();
                $pageTitle = __('message.database_backup');
                $data = view('setting.' . $page, compact('page', 'pageTitle', 'setting_value', 'settings'))->render();
                break;

            case 'print-label-mobail-number':
                $setting = Config::get('printlabel');
                $pageTitle = __('message.print_label_setting');
                $getSetting = [];
                foreach ($setting as $k => $s) {
                    foreach ($s as $sk => $ss) {
                        $getSetting[] = $sk;
                    }
                }

                $setting_value = Setting::whereIn('key', $getSetting)->get();
                $pageTitle = __('message.print_label_setting');
                $data = view('setting.' . $page, compact('setting', 'setting_value', 'page', 'pageTitle'))->render();
                break;

            case 'sms-settings':
                $sms_setting = SMSSetting::where('type', $type)->first();
                $data = view('setting.' . $page, compact('settings', 'page', 'type', 'sms_setting'))->render();
                break;

            case 'sms-template-setting':
                $pageTitle = __('message.sms_template_setting');
                $sms_template_setting = config('constant.SMS_TEMPLATE_SETTING');
                foreach ($sms_template_setting as $key => $val) {
                    $sms_template_setting[$key] = Setting::where('type', 'SMS_TEMPLATE')->where('key', $key)->value('value');
                }
                // dd($sms_template_setting);
                // ->pluck('value')->first();
                $data = view('setting.' . $page, compact('sms_template_setting', 'pageTitle', 'page'))->render();
                break;


            default:
                $data = view('setting.' . $page, compact('settings', 'page', 'envSettting'))->render();
                break;
        }
        return response()->json($data);
    }

    public function settingUpdate(Request $request)
    {
        $data = $request->all();
        $page = $request->page;
        $currentValue = SettingData('allow_deliveryman', 'allow_deliveryman');

        foreach ($data['key'] as $key => $val) {
            $value = ($data['value'][$key] != null) ? $data['value'][$key] : null;
            $input = [
                'type' => $data['type'][$key],
                'key' => $data['key'][$key],
                'value' => ($data['value'][$key] != null) ? $data['value'][$key] : null,
            ];
            if ($data['key'][$key] == 'allow_deliveryman') {
                $newValue = $data['value'][$key];

                if ($newValue != $currentValue) {
                    updateLanguageVersion();
                }
            }
            Setting::updateOrCreate(['key' => $input['key']], $input);
            envChanges($data['key'][$key], $value);
        }

        return redirect()->route('setting.index', ['page' => $page])->withSuccess(__('message.updated'));
    }

    /**
     * Process settings updates
     */
    public function settingsUpdates(Request $request)
    {
        try {
            // Log the request data for debugging
            \Log::info('Settings update request received', [
                'request_data' => $request->all(),
                'token_match' => session()->token() === $request->input('_token'),
                'session_token' => session()->token(),
                'request_token' => $request->input('_token')
            ]);

            $currency = currencyArray($request->currency_code);
            $request->merge(['currency' => $currency['symbol'] ?? '$']);

            $page = $request->page;

            $language_option = $request->language_option;

            if (!is_array($language_option)) {
                $language_option = (array) $language_option;
            }

            if (isset($request->env['DEFAULT_LANGUAGE'])) {
                array_push($language_option, $request->env['DEFAULT_LANGUAGE']);
            }

            $request->merge(['language_option' => $language_option]);

            if ($request->site_name) {
                $request->merge(['site_name' => str_replace("'", "", str_replace('"', '', $request->site_name))]);
            }

            // Create a filtered request data array with only the fields we want to update
            $filteredData = $request->only([
                'id',
                'site_name',
                'site_description',
                'support_email',
                'support_number',
                'site_email',
                'currency',
                'currency_code',
                'currency_position',
                'color',
                'language_option',
                'facebook_url',
                'twitter_url',
                'linkedin_url',
                'instagram_url',
                'site_copyright'
            ]);

            // Handle favicon upload if present
            if ($request->hasFile('site_favicon')) {
                try {
                    $file = $request->file('site_favicon');

                    if ($file->isValid()) {
                        // Ensure directory exists
                        if (!file_exists(public_path('images/logos'))) {
                            mkdir(public_path('images/logos'), 0755, true);
                        }

                        // Generate filename with timestamp
                        $filename = 'site_favicon_' . time() . '.' . $file->getClientOriginalExtension();
                        $filepath = 'images/logos/' . $filename;

                        // Move file to public directory
                        $file->move(public_path('images/logos'), $filename);

                        // Update database setting
                        Setting::updateOrCreate(
                            ['key' => 'site_favicon'],
                            ['type' => 'general', 'value' => $filepath]
                        );

                        \Log::info("Favicon updated successfully via general settings", [
                            'filepath' => $filepath
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error("Error processing favicon in general settings: " . $e->getMessage());
                }
            }

            \Log::info('Filtered data for AppSetting update', $filteredData);

            $res = AppSetting::updateOrCreate(['id' => $request->id], $filteredData);

            $type = 'APP_NAME';
            $env = $request->env ?? [];

            if (isset($res->site_name)) {
                $env['APP_NAME'] = $res->site_name;
            }

            foreach ($env as $key => $value) {
                envChanges($key, $value);
            }

            if (isset($env['DEFAULT_LANGUAGE'])) {
                App::setLocale($env['DEFAULT_LANGUAGE']);
                session()->put('locale', $env['DEFAULT_LANGUAGE']);
            }

            if ($request->timezone != '') {
                $user = auth()->user();
                $user->timezone = $request->timezone;
                $user->save();
            }

            // Process image uploads directly first
            $logoUpdated = false;

            foreach (['site_logo', 'site_dark_logo', 'site_favicon'] as $logoType) {
                if ($request->hasFile($logoType)) {
                    $logoUpdated = true;
                    $file = $request->file($logoType);

                    // Generate filename with timestamp
                    $filename = $logoType . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filepath = 'images/logos/' . $filename;

                    // Ensure directory exists
                    if (!file_exists(public_path('images/logos'))) {
                        mkdir(public_path('images/logos'), 0755, true);
                    }

                    // Move file to public directory
                    $file->move(public_path('images/logos'), $filename);

                    // Update database setting
                    $logoSetting = Setting::updateOrCreate(
                        ['key' => $logoType],
                        ['type' => 'general', 'value' => $filepath]
                    );
                }
            }

            // If any direct logo uploads were processed, skip the media library upload
            if (!$logoUpdated) {
                uploadMediaFile($res, $request->site_logo, 'site_logo');
                uploadMediaFile($res, $request->site_dark_logo, 'site_dark_logo');
                uploadMediaFile($res, $request->site_favicon, 'site_favicon');
            }

            appSettingData('set');

            createLangFile($env['DEFAULT_LANGUAGE']);

            // Properly redirect based on context
            if (request()->is('api/*')) {
                return json_message_response(__('message.updated'));
            }

            // Use a direct URL instead of named route for safer redirection
            $successMessage = $logoUpdated ?
                __('message.updated') . ". " . __('message.logos_updated') :
                __('message.updated');

            return redirect('/setting?page=' . $page)->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Settings update error: ' . $e->getMessage());

            if (request()->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function envChanges(Request $request)
    {
        $page = $request->page;

        if (!auth()->user()->hasRole('admin')) {
            abort(403, __('message.action_is_unauthorized'));
        }
        $env = $request->ENV;
        $envtype = $request->type;

        foreach ($env as $key => $value) {
            envChanges($key, str_replace('#', '', $value));
        }
        Artisan::call('cache:clear');
        return redirect()->route('setting.index', ['page' => $page])->withSuccess(ucfirst($envtype) . ' ' . __('message.updated'));
    }

    public function updateProfile(UserRequest $request)
    {
        $user = Auth::user();
        $page = $request->page;

        $user->fill($request->all())->update();
        uploadMediaFile($user, $request->profile_image, 'profile_image');

        return redirect()->route('setting.index', ['page' => 'profile_form'])->withSuccess(__('message.profile') . ' ' . __('message.updated'));
    }

    public function changePassword(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();

        if ($user == "") {
            $message = __('message.not_found_entry', ['name' => __('message.user')]);
            return json_message_response($message, 400);
        }

        $validator = Validator::make($request->all(), [
            'old' => 'required|min:8|max:255',
            'password' => 'required|min:8|confirmed|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('setting.index', ['page' => 'password_form'])->with('errors', $validator->errors());
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old, $hashedPassword);

        $same_exits = Hash::check($request->password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('message.old_new_pass_same');
                return redirect()->route('setting.index', ['page' => 'password_form'])->with('error', $message);
            }

            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
            Auth::logout();
            $message = __('message.password_change');
            return redirect()->route('setting.index', ['page' => 'password_form'])->withSuccess($message);
        } else {
            $message = __('message.valid_password');
            return redirect()->route('setting.index', ['page' => 'password_form'])->with('error', $message);
        }
    }

    public function termAndCondition(Request $request)
    {
        $setting_data = Setting::where('type', 'terms_condition')->where('key', 'terms_condition')->first();
        $pageTitle = __('message.terms_condition');
        $assets = ['textarea'];
        return view('setting.term_condition_form', compact('setting_data', 'pageTitle', 'assets'));
    }

    public function saveTermAndCondition(Request $request)
    {
        if (env('APP_DEMO')) {
            $message = __('message.demo_permission_denied');
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('term-condition')->withErrors($message);
        }

        if (!auth()->user()->hasRole('admin')) {
            abort(403, __('message.action_is_unauthorized'));
        }
        $setting_data = [
            'type' => 'terms_condition',
            'key' => 'terms_condition',
            'value' => $request->value
        ];
        $result = Setting::updateOrCreate(['id' => $request->id], $setting_data);
        if ($result->wasRecentlyCreated) {
            $message = __('message.save_form', ['form' => __('message.terms_condition')]);
        } else {
            $message = __('message.update_form', ['form' => __('message.terms_condition')]);
        }

        return redirect()->route('term-condition')->withsuccess($message);
    }

    public function privacyPolicy(Request $request)
    {
        $setting_data = Setting::where('type', 'privacy_policy')->where('key', 'privacy_policy')->first();
        $pageTitle = __('message.privacy_policy');
        $assets = ['textarea'];

        return view('setting.privacy_policy_form', compact('setting_data', 'pageTitle', 'assets'));
    }

    public function savePrivacyPolicy(Request $request)
    {
        if (env('APP_DEMO')) {
            $message = __('message.demo_permission_denied');
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('privacy-policy')->withErrors($message);
        }
        if (!auth()->user()->hasRole('admin')) {
            abort(403, __('message.action_is_unauthorized'));
        }
        $setting_data = [
            'type' => 'privacy_policy',
            'key' => 'privacy_policy',
            'value' => $request->value
        ];
        $result = Setting::updateOrCreate(['id' => $request->id], $setting_data);
        if ($result->wasRecentlyCreated) {
            $message = __('message.save_form', ['form' => __('message.privacy_policy')]);
        } else {
            $message = __('message.update_form', ['form' => __('message.privacy_policy')]);
        }

        return redirect()->route('privacy-policy')->withsuccess($message);
    }

    public function paymentSettingsUpdate(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, __('message.action_is_unauthorized'));
        }
        $data = $request->all();
        $result = PaymentGateway::updateOrCreate(['type' => request('type')], $data);
        uploadMediaFile($result, $request->gateway_image, 'gateway_image');
        return redirect()->route('setting.index', ['page' => 'payment-setting'])->withSuccess(__('message.updated'));
    }

    public function notificationSettingsUpdate(Request $request)
    {
        $app_setting = AppSetting::getData();

        AppSetting::updateOrCreate(['id' => $app_setting->id], ['notification_settings' => $request->notification_settings]);

        return redirect()->route('setting.index', ['page' => 'notification-setting'])->withSuccess(__('message.updated'));
    }

    public function saveInvoiceSetting(Request $request)
    {
        $data = $request->all();
        if ($request->is('api/*')) {
            foreach ($data as $req) {
                $input = [
                    'type' => $req['type'],
                    'key' => $req['key'],
                    'value' => $req['value'],
                ];
                Setting::updateOrCreate(['key' => $req['key'], 'type' => $req['type']], $input);
            }
        } else {
            if (isset($data['key']) && is_array($data['key'])) {
                foreach ($data['key'] as $key => $val) {
                    $value = isset($data['value'][$key]) ? $data['value'][$key] : null;
                    $input = [
                        'type' => isset($data['type'][$key]) ? $data['type'][$key] : null,
                        'key' => $val,
                        'value' => $value,
                    ];
                    $invoice = Setting::updateOrCreate(['key' => $val, 'type' => $data['type']], $input);
                }

                // Handle company_logo upload using direct file storage
                if ($request->hasFile('company_logo')) {
                    $file = $request->file('company_logo');

                    // Generate filename with timestamp
                    $filename = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();
                    $filepath = 'images/invoice/' . $filename;

                    // Ensure directory exists
                    if (!file_exists(public_path('images/invoice'))) {
                        mkdir(public_path('images/invoice'), 0755, true);
                    }

                    // Move file to public directory
                    $file->move(public_path('images/invoice'), $filename);

                    // Update database setting directly
                    Setting::updateOrCreate(
                        ['key' => 'company_logo', 'type' => 'order_invoice'],
                        ['value' => $filepath]
                    );
                }
            }
        }
        $message = __('message.save_form', ['form' => __('message.setting')]);
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        if (isset($data['invoice_settings'])) {
            return redirect()->route('setting.index', ['page' => 'invoice-setting'])->withSuccess(__('message.updated'));
        } elseif (isset($data['register_settings'])) {
            return redirect()->route('setting.index', ['page' => 'register-setting'])->withSuccess(__('message.updated'));
        } else {
            return redirect()->back();
        }
    }

    public function updateAppSetting(Request $request)
    {
        $data = $request->all();

        $appSetting = AppSetting::updateOrCreate(['id' => $request->id], $data);
        $message = __('message.update_form', ['form' => __('message.setting')]);
        if ($appSetting->wasRecentlyCreated) {
            $message = __('message.save_form', ['form' => __('message.setting')]);
        }
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        if (isset($data['notification_settings'])) {
            return redirect()->route('setting.index', ['page' => 'notification-setting'])->withSuccess(__('message.updated'));
        } else if (isset($data['database_backup'])) {
            return redirect()->route('setting.index', ['page' => 'database-backup'])->withSuccess(__('message.updated'));
        } else {
            return redirect()->route('setting.index', ['page' => 'order-setting'])->withSuccess(__('message.updated'));
        }
    }

    public function settingUploadInvoiceImage(Request $request)
    {
        $data = $request->all();
        $collection_name = request('key');

        try {
            $result = Setting::updateOrCreate(['key' => request('key'), 'type' => request('type')], $data);

            // Handle direct file upload
            if (isset($request->$collection_name) && $request->$collection_name != null) {
                $file = $request->file($collection_name);

                // Generate filename with timestamp
                $filename = $collection_name . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filepath = 'images/invoice/' . $filename;

                // Ensure directory exists
                if (!file_exists(public_path('images/invoice'))) {
                    mkdir(public_path('images/invoice'), 0755, true);
                }

                // Move file to public directory
                $file->move(public_path('images/invoice'), $filename);

                // Update the setting value with the file path
                $result->update([
                    'value' => $filepath
                ]);
            }

            if (request()->is('api/*')) {
                return json_message_response(__('message.save_form', ['form' => __('message.setting')]));
            } else {
                return redirect()->back()->withSuccess(__('message.updated'));
            }
        } catch (\Exception $e) {
            \Log::error('Error uploading invoice image: ' . $e->getMessage());

            if (request()->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 500);
            } else {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
    }

    public function previousInvoice()
    {
        // Static data only - no database queries for preview
        if (request()->has('view')) {
            $staticData = [
                'companyName' => (object) ['value' => 'Gogo Delivery'],
                'companyAddress' => (object) ['value' => '123 Delivery St, City'],
                'companyNumber' => (object) ['value' => '+1 234 567 8900'],
                'invoice' => (object) ['image_url' => asset('images/logo.png')],
                'today' => date('d/m/Y'),
                'barcodeBase64' => "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"
            ];

            return view('order.previousinvoice', $staticData);
        }

        // For PDF download, do the regular processing
        // Get company data from database
        $today = Carbon::now()->format('d/m/Y');
        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companyNumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();

        // Simplified logo handling
        $logoUrl = null;
        if ($invoice && $invoice->value && file_exists(public_path($invoice->value))) {
            $logoUrl = asset($invoice->value) . '?v=' . time();
        } else {
            $mediaUrl = getSingleMedia($invoice, 'company_logo');
            if ($mediaUrl) {
                $logoUrl = $mediaUrl;
            }
        }

        // Create a simple image object with the URL to avoid complex objects
        $invoice = (object) [
            'image_url' => $logoUrl
        ];

        // Use cached barcode
        $barcodeBase64 = "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";

        // Configure PDF with custom size for 80x40mm label
        $customPaper = array(0, 0, 226.77, 113.39); // 80mm x 40mm in points (72 points per inch)
        $pdf = Pdf::loadView('order.previousinvoice', compact('invoice', 'companyName', 'companyAddress', 'companyNumber', 'today', 'barcodeBase64'))
            ->setPaper($customPaper, 'landscape');

        return $pdf->stream('shipping_label.pdf');
    }

    public function smsSettingsUpdate(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, __('message.action_is_unauthorized'));
        }

        $request['title'] = __('message.' . $request->type);
        $smsSetting = SMSSetting::where('type', $request->type)->first();

        if ($smsSetting) {
            $smsSetting->update($request->all());
        } else {
            SMSSetting::create($request->all());
        }
        // SMSSetting::updateOrCreate([ 'type' => request('type') ],$request->all());
        return redirect()->route('setting.index', ['page' => 'sms-settings'])->withSuccess(__('message.updated'));
    }

    public function updateLogos(Request $request)
    {
        try {
            // Log request information for debugging
            \Log::info('Logo update request received', [
                'has_files' => $request->hasFile('site_logo') || $request->hasFile('site_dark_logo') || $request->hasFile('site_favicon'),
                'token_match' => session()->token() === $request->input('_token'),
                'session_token' => session()->token(),
                'request_token' => $request->input('_token')
            ]);

            // Ensure directory exists before validation
            if (!file_exists(public_path('images/logos'))) {
                mkdir(public_path('images/logos'), 0755, true);
            }

            // Validate image files with more relaxed validation
            $validated = $request->validate([
                'site_logo' => 'nullable|file|max:2048',
                'site_dark_logo' => 'nullable|file|max:2048',
                'site_favicon' => 'nullable|file|max:2048',
            ]);

            // Process each logo type
            $logoTypes = ['site_logo', 'site_dark_logo', 'site_favicon'];
            $updatedLogos = [];

            foreach ($logoTypes as $logoType) {
                if ($request->hasFile($logoType)) {
                    $file = $request->file($logoType);

                    if (!$file->isValid()) {
                        \Log::warning("Invalid file for {$logoType}");
                        continue;
                    }

                    // Generate filename with timestamp
                    $filename = $logoType . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filepath = 'images/logos/' . $filename;

                    try {
                        // Move file to public directory
                        $file->move(public_path('images/logos'), $filename);
                        $updatedLogos[] = $logoType;

                        // Update database setting
                        $logoSetting = Setting::updateOrCreate(
                            ['key' => $logoType],
                            ['type' => 'general', 'value' => $filepath]
                        );

                        \Log::info("Logo {$logoType} updated successfully", [
                            'filepath' => $filepath
                        ]);
                    } catch (\Exception $e) {
                        \Log::error("Error processing {$logoType}: " . $e->getMessage());
                    }
                }
            }

            // If no logos were updated but files were submitted, something went wrong
            if (
                empty($updatedLogos) &&
                ($request->hasFile('site_logo') || $request->hasFile('site_dark_logo') || $request->hasFile('site_favicon'))
            ) {
                \Log::warning('No logos were updated despite files being submitted');
                return redirect()->back()->with('error', __('message.error_updating_logos'));
            }

            // Log success
            \Log::info('Logo update completed', [
                'updated_logos' => $updatedLogos
            ]);

            if (!empty($updatedLogos)) {
                return redirect()->back()->with('success', __('message.updated'));
            } else {
                return redirect()->back()->with('info', __('message.no_changes'));
            }
        } catch (\Exception $e) {
            \Log::error('Error in updateLogos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Update favicon specifically
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFavicon(Request $request)
    {
        try {
            // Log request information for debugging
            \Log::info('Favicon update request received', [
                'has_file' => $request->hasFile('site_favicon'),
                'token_match' => session()->token() === $request->input('_token'),
                'session_token' => session()->token(),
                'request_token' => $request->input('_token')
            ]);

            // Ensure directory exists
            if (!file_exists(public_path('images/logos'))) {
                mkdir(public_path('images/logos'), 0755, true);
            }

            // Validate favicon file
            $request->validate([
                'site_favicon' => 'required|file|max:2048',
            ]);

            if ($request->hasFile('site_favicon')) {
                $file = $request->file('site_favicon');

                if (!$file->isValid()) {
                    \Log::warning("Invalid favicon file");
                    return redirect()->back()->with('error', __('message.invalid_file'));
                }

                // Generate filename with timestamp
                $filename = 'site_favicon_' . time() . '.' . $file->getClientOriginalExtension();
                $filepath = 'images/logos/' . $filename;

                try {
                    // Move file to public directory
                    $file->move(public_path('images/logos'), $filename);

                    // Update database setting
                    Setting::updateOrCreate(
                        ['key' => 'site_favicon'],
                        ['type' => 'general', 'value' => $filepath]
                    );

                    \Log::info("Favicon updated successfully", [
                        'filepath' => $filepath
                    ]);

                    return redirect()->back()->with('success', __('message.favicon_updated'));
                } catch (\Exception $e) {
                    \Log::error("Error processing favicon: " . $e->getMessage());
                    return redirect()->back()->with('error', $e->getMessage());
                }
            } else {
                return redirect()->back()->with('error', __('message.no_file_selected'));
            }
        } catch (\Exception $e) {
            \Log::error('Error in updateFavicon: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Get the current logo paths for AJAX requests
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogoPaths()
    {
        try {
            $logoSettings = Setting::whereIn('key', ['site_logo', 'site_dark_logo', 'site_favicon'])->get();

            $result = [
                'site_logo' => null,
                'site_dark_logo' => null,
                'site_favicon' => null
            ];

            // First try to get logos from settings table (direct file uploads)
            foreach ($logoSettings as $setting) {
                $path = $setting->value;

                // Ensure path exists
                if ($path && file_exists(public_path($path))) {
                    // Force a new cache version with timestamps
                    $result[$setting->key] = asset($path) . '?v=' . time();
                }
            }

            // For any missing logos, try media library as fallback
            foreach ($result as $key => $value) {
                if (!$value) {
                    $appSetting = AppSetting::first();
                    if ($appSetting) {
                        $mediaPath = getSingleMedia($appSetting, $key);
                        if ($mediaPath) {
                            $result[$key] = $mediaPath . '?v=' . time();
                        }
                    }
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error getting logo paths: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
