<?php

use App\Mail\sendmail;
use App\Models\User;
use App\Models\AppSetting;
use App\Models\LanguageVersionDetail;
use App\Models\Setting;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderMail;
use App\Models\SMSSetting;
use App\Models\SMSTemplate;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Notifications\CommonNotification;
use App\Notifications\OrderNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

function authSession($force = false)
{
    $session = new User;
    if ($force) {
        $user = auth()->user()->getRoleNames();
        Session::put('auth_user', $user);
        $session = Session::get('auth_user');
        return $session;
    }
    if (Session::has('auth_user')) {
        $session = Session::get('auth_user');
    } else {
        $user = auth()->user();
        Session::put('auth_user', $user);
        $session = Session::get('auth_user');
    }
    return $session;
}

function DummyData($key)
{
    $dummy_title = 'XXXXXXXXXXXXXX';
    $dummy_description = 'xxxxxxxxxxxxxxxx xxxxxxxxx xxxxxxxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxxxxxxxxxx xxxxxxxxx xxxxxxxxxxx xxxxxxxxxxxxxxxxx';

    switch ($key) {
        case 'dummy_title':
            return $dummy_title;
        case 'dummy_description':
            return $dummy_description;
        default:
            return '';
    }
}

function updateLanguageVersion()
{
    $language_version_data = LanguageVersionDetail::find(1);
    return $language_version_data->increment('version_no', 1);
}

function appSettingData($type = 'get')
{
    if (Session::get('setting_data') == '') {
        $type = 'set';
    }
    switch ($type) {
        case 'set':
            $settings = AppSetting::first();
            if (!$settings) {
                $settings = new AppSetting();
                $settings->site_name = config('app.name', 'Gogo Delivery');
                $settings->site_email = config('mail.from.address', 'info@gogodelivery.com');
                $settings->site_description = 'Delivery Service';
                $settings->site_copyright = '© ' . date('Y') . ' Gogo Delivery';
                $settings->language_option = collect(languagesArray())->pluck('id')->toArray();
            }
            Session::put('setting_data', $settings);
            break;
        default:
            break;
    }
    return Session::get('setting_data');
}

function appSettingcurrency($key = null)
{
    $appsetting = AppSetting::first();

    if ($appsetting != null && $key != null) {
        $appsetting = $appsetting->$key;
        if ($key == 'currency_code') {
            $appsetting = strtolower($appsetting);
        }
    }

    return $appsetting;
}

/**
 * Safely get a property from app_settings
 *
 * @param string $property The property to get
 * @param mixed $default The default value if property doesn't exist
 * @return mixed
 */
function getAppSetting($property, $default = null)
{
    $appSetting = AppSetting::first();

    if ($appSetting && isset($appSetting->$property)) {
        return $appSetting->$property;
    }

    // Special handling for language_option
    if ($property === 'language_option') {
        return collect(languagesArray())->pluck('id')->toArray();
    }

    return $default;
}

function json_message_response($message, $status_code = 200)
{
    return response()->json(['message' => $message], $status_code);
}

function json_custom_response($response, $status_code = 200)
{
    return response()->json($response, $status_code);
}

function json_pagination_response($items)
{
    return [
        'total_items' => $items->total(),
        'per_page' => $items->perPage(),
        'currentPage' => $items->currentPage(),
        'totalPages' => $items->lastPage()
    ];
}

function imageExtention($media)
{
    $extention = null;
    if ($media != null) {
        $path_info = pathinfo($media);
        $extention = $path_info['extension'];
    }
    return $extention;
}

function checkMenuRoleAndPermission($menu)
{
    if (auth()->check()) {
        if ($menu->data('role') == null && auth()->user()->hasRole('admin')) {
            return true;
        }

        if ($menu->data('permission') == null && $menu->data('role') == null) {
            return true;
        }

        if ($menu->data('role') != null) {
            if (auth()->user()->hasAnyRole(explode(',', $menu->data('role')))) {
                return true;
            }
        }

        if ($menu->data('permission') != null) {
            if (auth()->user()->can($menu->data('permission'))) {
                return true;
            }
        }
    }
    return false;
}


function checkRolePermission($role, $permission)
{
    try {
        if ($role->hasPermissionTo($permission)) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function getSettingFirstData($type = null, $key = null)
{
    return Setting::where('type', $type)->where('key', $key)->first();
}

function getSingleMediaSettingImage($model = null, $collection_name = null, $check_collection_type = null)
{

    $image = null;
    if ($model !== null) {
        $image = $model->getFirstMedia($collection_name);
    }

    if (getFileExistsCheck($image)) {
        return $image->getFullUrl();
    } else {
        switch ($collection_name) {
            case 'app_logo_image':
                $image = asset('frontend-website/assets/website/dummy_images/45x45.png');
                break;
            case 'delivery_man_image':
                $image = asset('frontend-website/assets/website/dummy_images/ic_delivery_man.jpg');
                break;
            case 'delivery_road_image':
                $image = asset('frontend-website/assets/website/dummy_images/ic_road_pattern.jpg');
                break;
            case 'download_app_logo':
                $image = asset('frontend-website/assets/website/dummy_images/ic_mobile.jpg');
                break;
            case 'about_us_app_ss':
                $image = asset('frontend-website/assets/website/dummy_images/ic_aboutUs.jpg');
                break;
            case 'contact_us_app_ss':
                $image = asset('frontend-website/assets/website/dummy_images/ic_aboutUs.jpg');
                break;
            case 'delivery_partner_image':
                $image = asset('frontend-website/assets/website/dummy_images/ic_deliveryboy.jpg');
                break;
            case 'frontend_data_image':
                if ($check_collection_type == 'why_delivery_image') {
                    $image = asset('frontend-website/assets/website/dummy_images/245x330.png');
                }
                if ($check_collection_type == 'client_review_image') {
                    $image = asset('frontend-website/assets/website/dummy_images/150x150.png');
                }
                if ($check_collection_type == 'walkthrough_image') {
                    $image = asset('frontend-website/assets/website/dummy_images/slider.png');
                }
                break;
        }
        return $image;
    }
}

function getSingleMedia($model, $collection = 'profile_image', $skip = true)
{
    if (!auth()->check() && $skip) {
        return asset('images/user/1.jpg');
    }
    $media = null;
    if ($model !== null) {
        $media = $model->getFirstMedia($collection);
    }

    if (getFileExistsCheck($media)) {
        return $media->getFullUrl();
    } else {
        switch ($collection) {
            case 'profile_image':
                $media = asset('images/user/1.jpg');
                break;
            case 'site_logo':
                $media = asset('images/logo.png');
                break;
            case 'site_dark_logo':
                $media = asset('images/dark_logo.png');
                break;
            case 'gateway_image':
                $gateway_name = $model->type ?? 'default';
                $media = asset('images/' . $gateway_name . '.png');
                break;
            case 'site_favicon':
                $media = asset('images/favicon.ico');
                break;
            default:
                $media = asset('images/default.png');
                break;
        }
        return $media;
    }
}
function getSingleMediaCustomerSupport($model, $collection = 'support_image', $skip = true, $returnDefault = false)
{
    if (!auth()->check() && $skip) {
        return $returnDefault ? null : null;
    }

    $media = null;
    if ($model !== null) {
        $media = $model->getFirstMedia($collection);
    }

    if (getFileExistsCheck($media)) {
        return $media->getFullUrl();
    } else {
        return $returnDefault ? asset('images/default.png') : null;
    }
}


function getFileExistsCheck($media)
{
    $mediaCondition = false;

    if ($media) {
        if ($media->disk == 'public') {
            $mediaCondition = file_exists($media->getPath());
        } else {
            $mediaCondition = Storage::disk($media->disk)->exists($media->getPath());
        }
    }
    return $mediaCondition;
}

function uploadMediaFile($model, $file, $name)
{
    if ($file) {
        $model->clearMediaCollection($name);
        if (is_array($file)) {
            foreach ($file as $key => $value) {
                $model->addMedia($value)->toMediaCollection($name);
            }
        } else {
            $model->addMedia($file)->toMediaCollection($name);
        }
    }

    return true;
}

function getAttachments($attchments)
{
    $files = [];
    if (count($attchments) > 0) {
        foreach ($attchments as $attchment) {
            if (getFileExistsCheck($attchment)) {
                array_push($files, $attchment->getFullUrl());
            }
        }
    }

    return $files;
}

function getMediaFileExit($model, $collection = 'profile_image')
{
    if ($model == null) {
        return asset('images/user/1.jpg');
    }

    $media = $model->getFirstMedia($collection);

    return getFileExistsCheck($media);
}

function removeValueFromArray($array = [], $find = null)
{
    foreach (array_keys($array, $find) as $key) {
        unset($array[$key]);
    }

    return array_values($array);
}

function timeZoneList()
{
    $list = \DateTimeZone::listAbbreviations();
    $idents = \DateTimeZone::listIdentifiers();

    $data = $offset = $added = array();
    foreach ($list as $abbr => $info) {
        foreach ($info as $zone) {
            if (!empty($zone['timezone_id']) and !in_array($zone['timezone_id'], $added) and in_array($zone['timezone_id'], $idents)) {

                $z = new \DateTimeZone($zone['timezone_id']);
                $c = new \DateTime(null, $z);
                $zone['time'] = $c->format('H:i a');
                $offset[] = $zone['offset'] = $z->getOffset($c);
                $data[] = $zone;
                $added[] = $zone['timezone_id'];
            }
        }
    }

    array_multisort($offset, SORT_ASC, $data);
    $options = array();
    foreach ($data as $key => $row) {
        $options[$row['timezone_id']] = $row['time'] . ' - ' . formatOffset($row['offset']) . ' ' . $row['timezone_id'];
    }
    return $options;
}

function formatOffset($offset)
{
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 and $minutes == 0) {
        $sign = ' ';
    }
    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0');
}
function generateRandomCode()
{
    $letters = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 2);

    $thirdDigit = substr(str_shuffle("123456789"), 0, 1);

    $validNumbers = [];
    for ($i = 101; $i <= 999; $i++) {
        $numberStr = (string) $i;
        if (strpos($numberStr, '0') === false) {
            $validNumbers[] = $numberStr;
        }
    }
    $lastThreeDigits = $validNumbers[array_rand($validNumbers)];

    $randomCode = $letters . $thirdDigit . $lastThreeDigits;

    return $randomCode;
}

/**
 * Get the CSS class for an order status
 *
 * @param string $status
 * @return string
 */
function getOrderStatusClass(string $status): string
{
    switch ($status) {
        case 'draft':
            return 'light';
        case 'create':
            return 'primary';
        case 'active':
            return 'info';
        case 'courier_assigned':
            return 'warning';
        case 'courier_arrived':
            return 'info';
        case 'courier_picked_up':
            return 'info';
        case 'courier_departed':
            return 'info';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        case 'failed':
            return 'danger';
        case 'delayed':
            return 'warning';
        default:
            return 'primary';
    }
}

function languagesArray($ids = [])
{
    // Only include English and Arabic
    $language = [
        ['title' => 'English', 'id' => 'en'],
        ['title' => 'Arabic', 'id' => 'ar']
    ];

    if (!empty($ids)) {
        $language = collect($language)->whereIn('id', $ids)->values();
    }

    return $language;
}

function flattenToMultiDimensional(array $array, $delimiter = '.')
{
    $result = [];
    foreach ($array as $notations => $value) {
        // extract keys
        $keys = explode($delimiter, $notations);
        // reverse keys for assignments
        $keys = array_reverse($keys);

        // set initial value
        $lastVal = $value;
        foreach ($keys as $key) {
            // wrap value with key over each iteration
            $lastVal = [
                $key => $lastVal
            ];
        }
        // merge result
        $result = array_merge_recursive($result, $lastVal);
    }
    return $result;
}

function createLangFile($lang = '')
{
    $langDir = resource_path() . '/lang/';
    $enDir = $langDir . 'en';
    $currentLang = $langDir . $lang;
    if (!File::exists($currentLang)) {
        File::makeDirectory($currentLang);
        File::copyDirectory($enDir, $currentLang);
    }
}

function dateAgoFormate($date, $type2 = '')
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }

    $diff_time1 = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
    $datetime = new \DateTime($date);
    $la_time = new \DateTimeZone(auth()->check() ? auth()->user()->timezone ?? 'UTC' : 'UTC');
    $datetime->setTimezone($la_time);
    $diff_date = $datetime->format('Y-m-d H:i:s');

    $diff_time = \Carbon\Carbon::parse($diff_date)->isoFormat('LLL');

    // if($type2 != ''){
    return $diff_time;
    // }

    // return $diff_time1 .' on '.$diff_time;
}
function dateFormate($date, $type2 = '')
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }

    $diff_time1 = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
    $datetime = new \DateTime($date);
    $la_time = new \DateTimeZone(auth()->check() ? auth()->user()->timezone ?? 'UTC' : 'UTC');
    $datetime->setTimezone($la_time);
    $diff_date = $datetime->format('Y-m-d H:i:s');

    $diff_time = \Carbon\Carbon::parse($diff_date)->isoFormat('LL');

    // if($type2 != ''){
    return $diff_time;
    // }
}

function timeAgoFormate($date)
{
    if ($date == null) {
        return '-';
    }

    date_default_timezone_set('UTC');

    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();

    return $diff_time;
}

function envChanges($type, $value)
{
    $path = base_path('.env');

    $checkType = $type . '="';
    if (strpos($value, ' ') || strpos(file_get_contents($path), $checkType) || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value)) {
        $value = '"' . $value . '"';
    }

    $value = str_replace('\\', '\\\\', $value);

    if (file_exists($path)) {
        $typeValue = env($type);

        if (strpos(env($type), ' ') || strpos(file_get_contents($path), $checkType)) {
            $typeValue = '"' . env($type) . '"';
        }

        file_put_contents($path, str_replace(
            $type . '=' . $typeValue,
            $type . '=' . $value,
            file_get_contents($path)
        ));

        $onesignal = collect(config('constant.ONESIGNAL'))->keys();

        $checkArray = Arr::collapse([['DEFAULT_LANGUAGE']]);


        if (in_array($type, $checkArray)) {
            if (env($type) === null) {
                file_put_contents($path, "\n" . $type . '=' . $value, FILE_APPEND);
            }
        }
    }
}
function convertUnitvalue($unit)
{
    switch ($unit) {
        case 'mile':
            return 3956;
            break;
        default:
            return 6371;
            break;
    }
}

function mile_to_km($mile)
{
    return $mile * 1.60934;
}

function km_to_mile($km)
{
    return $km * 0.621371;
}


function calculateDuration($start_time, $current_time = null)
{
    $current_time = $current_time ?? date('Y-m-d H:i:s');
    $start_time = Carbon\Carbon::parse($start_time);
    $end_time = Carbon\Carbon::parse($current_time);
    $total_duration = $end_time->diffInMinutes($start_time);

    return $total_duration;
}

function calculate_distance($lat1, $lng1, $lat2, $lng2, $unit)
{
    if (($lat1 == $lat2) && ($lng1 == $lng2)) {
        return 0;
    } else {
        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        if ($unit == "km") {
            return ($miles * 1.609344);
        } elseif ($unit == "mile") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}

function SettingData($type, $key = null)
{
    $setting = Setting::where('type', $type);

    $setting->when($key != null, function ($q) use ($key) {
        return $q->where('key', $key);
    });

    $setting_data = $setting->pluck('value')->first();
    return $setting_data;
}

function getPriceFormat($price)
{
    if (gettype($price) == 'string') {
        return $price;
    }
    if ($price === null) {
        $price = 0;
    }

    $currency_code = appSettingcurrency()->currency_code ?? 'USD';
    $currecy = currencyArray($currency_code);

    $code = $currecy['symbol'] ?? '$';
    $position = appSettingcurrency()->currency_position ?? 'left';

    if ($position == 'left') {
        $price = $code . "" . number_format((float) $price, 2, '.', '');
    } else {
        $price = number_format((float) $price, 2, '.', '') . "" . $code;
    }

    return $price;
}
function getAttachmentArray($attchments)
{
    $files = [];
    if (count($attchments) > 0) {
        foreach ($attchments as $attchment) {
            if (getFileExistsCheck($attchment)) {
                $file = [
                    'id' => $attchment->id,
                    'name' => $attchment->file_name,
                    'url' => $attchment->getFullUrl()
                ];
                array_push($files, $file);
            }
        }
    }

    return $files;
}
function saveOrderHistory($data)
{
    $admin = User::where('user_type', 'admin')->first();
    $data['datetime'] = date('Y-m-d H:i:s');
    $orderData = $data['order'];
    $emailData = OrderMail::where('type', $orderData->status)->first();
    $user_type = auth()->user()->user_type;
    $history_data = [];
    $sendTo = [];
    $order_id = $data['order']->id;
    $data['order'] = Order::with('client')->find($order_id);

    switch ($data['history_type']) {
        case 'draft':
            $data['history_message'] = __('message.order_draft');
            $history_data = [
                'client_id' => $data['order']->client_id,
                'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
            ];
            break;
        case 'bid_placed':
            $deliverManName = User::where('id', $data['deliveryManId'])->pluck('name')->first();
            $data['history_message'] = __('message.apply_bid_history', ['delivery_person' => $deliverManName]);
            $history_data = [
                'delivery_man_id' => $data['deliveryManId'],
                'delivery_man_name' => $deliverManName,
            ];
            $document_name = 'order_' . $data['order']->id;
            $firebaseData = app('firebase.firestore')->database()->collection('delivery_man')->document($document_name);

            $nearby_driver_ids = array_map('trim', (array) json_decode($orderData->nearby_driver_ids ?? '[]', true));
            $accept_driver_ids = array_map('trim', (array) json_decode($orderData->accept_delivery_man_ids ?? '[]', true));

            $nearby_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->nearby_driver_ids ?? '[]', true)));
            $accept_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->accept_delivery_man_ids ?? '[]', true)));
            $reject_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->reject_delivery_man_ids ?? '[]', true)));

            $all_delivery_man_ids = array_unique(array_map('intval', array_merge(
                array_diff($nearby_driver_ids, $accept_driver_ids, $reject_driver_ids),
                array_diff($accept_driver_ids, $nearby_driver_ids, $reject_driver_ids)
            )));

            $all_delivery_man_ids = array_map('intval', $all_delivery_man_ids);

            $orderData = [
                'all_delivery_man_ids' => $all_delivery_man_ids ?? [],
                'order_id' => $data['order']->id ?? '',
                'client_id' => $data['order']->client_id ?? '',
                'client_name' => $data['order']->client->name,
                'client_email' => $data['order']->client->email,
                'status' => $data['order']->status ?? '',
                'client_image' => getSingleMedia($orderData->client, 'profile_image', null),
                'payment_status' => '',
                'payment_type' => '',
                'delivery_man_listening' => 0,
                'accepted_delivery_man_ids' => $accept_driver_ids ?? [],
                'order_has_bids' => $data['order']->bid_type == 1 ? 1 : 0,
                'created_at' => $data['order']->created_at,
            ];
            $firebaseData->set($orderData);
            $sendTo = ['admin', 'client'];
            break;
        case 'reject_bid':
            $deliverManName = User::where('id', $data['deliveryManId'])->pluck('name')->first();
            $data['history_message'] = __('message.apply_reject_history.client', ['delivery_person' => $deliverManName]);
            $history_data = [
                'delivery_man_id' => $data['deliveryManId'],
                'delivery_man_name' => $deliverManName,
            ];
            $document_name = 'order_' . $data['order']->id;
            $firebaseData = app('firebase.firestore')->database()->collection('delivery_man')->document($document_name);

            $nearby_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->nearby_driver_ids ?? '[]', true)));
            $accept_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->accept_delivery_man_ids ?? '[]', true)));
            $reject_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->reject_delivery_man_ids ?? '[]', true)));

            $all_delivery_man_ids = array_unique(array_map('intval', array_merge(
                array_diff($nearby_driver_ids, $accept_driver_ids, $reject_driver_ids),
                array_diff($accept_driver_ids, $nearby_driver_ids, $reject_driver_ids)
            )));

            $orderData = [
                'all_delivery_man_ids' => $all_delivery_man_ids ?? [],
                'order_id' => $data['order']->id ?? '',
                'client_id' => $data['order']->client_id ?? '',
                'client_name' => $data['order']->client->name,
                'client_email' => $data['order']->client->email,
                'status' => $data['order']->status ?? '',
                'client_image' => getSingleMedia($orderData->client, 'profile_image', null),
                'payment_status' => '',
                'payment_type' => '',
                'delivery_man_listening' => 0,
                'accepted_delivery_man_ids' => $accept_driver_ids ?? [],
                'reject_driver_ids' => $reject_driver_ids ?? [],
                'order_has_bids' => $data['order']->bid_type == 1 ? 1 : 0,
                'created_at' => $data['order']->created_at,
            ];

            $firebaseData->set($orderData);
            $sendTo = ['admin', 'client', 'deliveryMan'];
            break;

        case 'deliveryman_reject_bid':
            $deliverManName = User::where('id', $data['deliveryManId'])->pluck('name')->first();
            $data['history_message'] = __('message.deliveryman_bid_reject', ['delivery_person' => $deliverManName]);
            $history_data = [
                'delivery_man_id' => $data['deliveryManId'],
                'delivery_man_name' => $deliverManName,
            ];
            $document_name = 'order_' . $data['order']->id;
            $firebaseData = app('firebase.firestore')->database()->collection('delivery_man')->document($document_name);

            $nearby_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->nearby_driver_ids ?? '[]', true)));
            $accept_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->accept_delivery_man_ids ?? '[]', true)));
            $reject_driver_ids = array_map('intval', array_map('trim', (array) json_decode($orderData->reject_delivery_man_ids ?? '[]', true)));

            $all_delivery_man_ids = array_unique(array_map('intval', array_merge(
                array_diff($nearby_driver_ids, $accept_driver_ids, $reject_driver_ids),
                array_diff($accept_driver_ids, $nearby_driver_ids, $reject_driver_ids)
            )));

            $orderData = [
                'all_delivery_man_ids' => $all_delivery_man_ids ?? [],
                'order_id' => $data['order']->id ?? '',
                'client_id' => $data['order']->client_id ?? '',
                'client_name' => $data['order']->client->name,
                'client_email' => $data['order']->client->email,
                'status' => $data['order']->status ?? '',
                'client_image' => getSingleMedia($orderData->client, 'profile_image', null),
                'payment_status' => '',
                'payment_type' => '',
                'delivery_man_listening' => 0,
                'accepted_delivery_man_ids' => $accept_driver_ids ?? [],
                'reject_driver_ids' => $reject_driver_ids ?? [],
                'order_has_bids' => $data['order']->bid_type == 1 ? 1 : 0,
                'created_at' => $data['order']->created_at,
            ];

            $firebaseData->set($orderData);
            $sendTo = ['admin', 'client', 'deliveryMan'];
            break;
        case 'bid_accept':
            $deliverManName = User::where('id', $data['deliveryManId'])->pluck('name')->first();
            $data['history_message'] = __('message.accept_bid_history', ['delivery_person' => $deliverManName]);
            $history_data = [
                'delivery_man_id' => $data['deliveryManId'],
                'delivery_man_name' => $deliverManName,
            ];
            break;

        case 'create':
            $setting = SettingData('order_mail', 'order_create_mail');
            $data['history_message'] = __('message.order_create');
            $history_data = [
                'client_id' => $data['order']->client_id,
                'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
            ];
            processOrderHistory($data, $history_data);
            $sendTo = removeValueFromArray(['admin', 'client'], $user_type);
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => str_replace('_', '', ucfirst($orderData->status)),
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                        'company_name' => config('app.name'),
                    ]));
                }
            }

            $smscheck = (int) (appSettingData('is_sms_order')->is_sms_order ?? 0);
            Log::info("Final SMS Check Value: " . $smscheck);

            // Agar SMS disabled hai, to return kar do
            if ($smscheck === 0) {
                Log::warning("SMS Sending Disabled.");
                return false;
            }

            // Delivery Point data decode karna
            $deliveryPoint = [];
            if (!empty($data['order']->delivery_point)) {
                if (is_string($data['order']->delivery_point)) {
                    $deliveryPoint = json_decode($data['order']->delivery_point, true);
                } elseif (is_array($data['order']->delivery_point)) {
                    $deliveryPoint = $data['order']->delivery_point;
                }
            }
            $client = $data['order']->client;
            $customerContact = $client->contact_number ?? null;
            $orderId = $data['order']->milisecond ?? null;
            // Order Tracking Link Generate
            $fullTrackingUrl = config('app.url') . route('sms-orderhistory', ['order_id' => $orderId], false);
            $shortLink = shortenWithTinyURL($fullTrackingUrl);

            // Delivery Contact get karna
            $deliveryContact = $deliveryPoint['contact_number'] ?? null;
            $deliveryName = $deliveryPoint['name'] ?? null;

            // SMS Service Initialize
            $smsType = isset($orderData->sms_type) ? $orderData->sms_type : '';
            Log::info("Selected SMS Provider: " . $smsType);
            $smsService = app()->make(\App\Services\SmsService::class);

            //Customer name tene order create order
            $smsTemplates = SMSData('twilio', 'get', null, [
                'Order ID' => $data['order']->milisecond,
                'Customer Name' => $data['order']->client->name ?? 'Unknown',
                'Sender Name' => $deliveryName ?? 'Unknown',
                'Tracking Link' => $shortLink,
                'status' => $data['order']->status,
            ]);
            // Delievry person name and send sms
            $smsTemplatesDelivery = SMSData('twilio', 'get', null, [
                'Order ID' => $data['order']->milisecond,
                'Customer Name' => $deliveryName ?? 'Unknown',
                'Sender Name' => $data['order']->client->name ?? 'Unknown',
                'Tracking Link' => $shortLink,
                'status' => $data['order']->status,
            ]);

            // Check if message is an array & pick correct one
            if (is_array($smsTemplates) && is_array($smsTemplatesDelivery)) {
                $orderConfirmationMessage = $smsTemplates['order_confirmation'] ?? null;
                $parcelMessage = $smsTemplatesDelivery['you_have_parcel'] ?? null;
            } else {
                Log::error("SMSData() returned invalid format: " . json_encode($smsTemplates));
                return false;
            }
            // **Customer Contact ke liye SMS bhejna (Pehle Customer ko SMS Send hoga)**
            $customerSmsSent = false;
            if (!empty($customerContact) && !empty($orderConfirmationMessage)) {
                try {
                    $response = $smsService->sendSMS($customerContact, $orderConfirmationMessage);
                    // Log::info("Customer SMS Response: " . print_r($response, true));
                    $customerSmsSent = true;
                } catch (\Exception $e) {
                    Log::error("Customer SMS Error: " . $e->getMessage());
                }
            }

            // **Delivery Contact ke liye SMS bhejna (Customer ke baad Delivery Person ko Send hoga)**
            $deliverySmsSent = false;
            if (!empty($deliveryContact) && !empty($parcelMessage)) {
                try {
                    // Wait for the first SMS to complete before sending the next
                    if ($customerSmsSent) {
                        sleep(1); // Thoda delay deke send karo
                    }

                    $customerResponse = $smsService->sendSMS($deliveryContact, $parcelMessage);
                    // Log::info("Delivery SMS Response: " . print_r($customerResponse, true));
                    $deliverySmsSent = true;
                } catch (\Exception $e) {
                    Log::error("Delivery SMS Error: " . $e->getMessage());
                }
            }

            Log::info("Customer SMS Sent: " . ($customerSmsSent ? 'Yes' : 'No'));
            Log::info("Delivery SMS Sent: " . ($deliverySmsSent ? 'Yes' : 'No'));
            break;

        case 'active':
            $setting = SettingData('order_mail', 'order_active_mail');
            $data['history_message'] = __('message.order_active');
            $history_data = [
                'client_id' => $data['order']->client_id,
                'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['admin', 'client'];
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => str_replace('_', ' ', ucfirst($orderData->status)),
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                    ]));
                }
            }
            break;
        case 'payment_status_message':
            $data['history_message'] = __('message.payment_status_message_list', ['status' => $data['payment_status'], 'id' => $order_id]);

            $history_data = [
                'payment_status' => $data['payment_status'],
                'order_id' => $data['order_id'],
            ];
            break;
            $sendTo = ['admin', 'client'];
        case 'delayed':
            $data['history_message'] = __('message.order_delayed');
            $history_data = [
                'reason' => $data['order']->reason,
                'status' => $data['order']->status,
            ];
            $sendTo = removeValueFromArray(['admin', 'client', 'delivery_man'], $user_type);
            break;

        case 'cancelled':
            $setting = SettingData('order_mail', 'order_cancel_mail');
            $data['history_message'] = __('message.cancelled_order');
            $history_data = [
                'reason' => $data['order']->reason,
                'status' => $data['order']->status,
            ];
            processOrderHistory($data, $history_data);
            $sendTo = removeValueFromArray(['admin', 'client', 'delivery_man'], $user_type);

            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => str_replace('_', ' ', ucfirst($orderData->status)),
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                    ]));
                }
            }
            $firebase = app('firebase.firestore')->database();
            $documentName = 'order_' . $orderData->id;
            $firebaseData = $firebase->collection('delivery_man')->document($documentName);
            if ($firebaseData->snapshot()->exists()) {
                $firebaseData->delete();
            }
            break;

        case 'courier_assigned':
            $setting = SettingData('order_mail', 'order_courier_assigned_mail');
            $data['history_message'] = __('message.courier_assigned_history.client', ['id' => $order_id, 'delivery_person' => optional($data['order']->delivery_man)->name]);
            $history_data = [
                'delivery_man_id' => $data['order']->delivery_man_id,
                'delivery_man_name' => optional($data['order']->delivery_man)->name,
                'auto_assign' => $data['order']->auto_assign,
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['client', 'delivery_man'];
            $datadatatime['assign_datetime'] = now();
            $assignorder = Order::updateOrCreate(['id' => $data['order']->id], $datadatatime);
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => str_replace('_', ' ', ucfirst($orderData->status)),
                    '[Company name]' => config('app.name'),
                    '[client name]' => optional($data['order']->client)->name ?? '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                    ]));
                }
            }
            //sms ka code hai aya che sab
            $smscheck = (int) (appSettingData('is_sms_order')->is_sms_order ?? 0);
            Log::info("Final SMS Check Value: " . $smscheck);

            if ($smscheck === 0) {
                Log::warning("SMS Sending Disabled.");
                return false;
            }

            // Order ke delivery man ka contact number fetch karna
            $deliveryMan = $data['order']->delivery_man; // Ensure 'deliveryMan' relation is loaded
            $contactNumber = $deliveryMan->contact_number ?? null;

            Log::info("Delivery Man Contact Number: " . $contactNumber);

            if (!empty($contactNumber)) {
                try {
                    $smsType = isset($orderData->sms_type) ? $orderData->sms_type : '';
                    Log::info("Selected SMS Provider: " . $smsType);
                    $smsService = app()->make(\App\Services\SmsService::class);

                    // Dynamic SMS Message
                    $message = SMSData('twilio', 'get', null, [
                        'Order ID' => $data['order']->milisecond,
                        'Delievry_Man Name' => $data['order']->delivery_man->name,
                        'status' => $data['order']->status,
                        // 'delivery man' => $deliveryMan->name,
                    ]);

                    $response = $smsService->sendSMS($contactNumber, $message);
                    // Log::info("SMS Response: " . print_r($response, true));

                } catch (\Exception $e) {
                    Log::error("SMS Error: " . $e->getMessage());
                }
            }
            break;
        case 'courier_auto_assign_cancelled':
            $data['history_message'] = __('message.courier_auto_assign_cancelled_history.client', ['id' => $order_id, 'delivery_person' => optional($data['order']->delivery_man)->name]);
            $history_data = [
                'delivery_man_id' => $data['order']->delivery_man_id,
                'delivery_man_name' => optional($data['order']->delivery_man)->name,
                'auto_assign' => $data['order']->auto_assign,
            ];
            break;
        case 'courier_transfer':

            $data['history_message'] = __('message.courier_transfer_history.client', ['id' => $order_id, 'delivery_person' => optional($data['order']->delivery_man)->name]);
            $history_data = [
                'delivery_man_id' => $data['order']->delivery_man_id,
                'delivery_man_name' => optional($data['order']->delivery_man)->name,
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['delivery_man'];
            $datadatatime['assign_datetime'] = now();
            $assignorder = Order::updateOrCreate(['id' => $data['order']->id], $datadatatime);
            break;

        case 'courier_picked_up':
            $setting = SettingData('order_mail', 'order_courier_pickup_up_mail');
            $data['history_message'] = __('message.courier_picked_up_history');

            $history_data = [
                'delivery_man_id' => $data['order']->delivery_man_id,
                'delivery_man_name' => optional($data['order']->delivery_man)->name,
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['admin', 'client'];
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => str_replace('_', ' ', ucfirst($orderData->status)),
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                    ]));
                }
            }
            $smscheck = (int) (appSettingData('is_sms_order')->is_sms_order ?? 0);
            Log::info("Final SMS Check Value: " . $smscheck);

            // SMS disabled hone par return
            if ($smscheck === 0) {
                $errorMessage = __('message.sms_permission_denied');
                Log::warning("SMS Sending Disabled: " . $errorMessage);
                return false;
            }

            //  Pickup Point Contact Number Get Karna
            $pickupPoint = is_string($data['order']->pickup_point) ? json_decode($data['order']->pickup_point, true) : ($data['order']->pickup_point ?? []);
            $deliveryPoint = is_string($data['order']->delivery_point) ? json_decode($data['order']->delivery_point, true) : ($data['order']->delivery_point ?? []);

            $pickupContact = $pickupPoint['contact_number'] ?? null;
            $deliveryContact = $deliveryPoint['contact_number'] ?? null;
            $nameofpickup = $pickupPoint['name'] ?? 'Unknown';
            $namedelivery = $deliveryPoint['name'] ?? 'Unknown';

            Log::info("Pickup Contact: " . $pickupContact);
            Log::info("Delivery Contact: " . $deliveryContact);

            //  SMS Service Initialize
            $smsType = isset($orderData->sms_type) ? $orderData->sms_type : '';
            Log::info("Selected SMS Provider: " . $smsType);
            $smsService = app()->make(\App\Services\SmsService::class);

            //  SMS Template Fetch karna
            $smsTemplatesPickup = SMSData('twilio', 'get', null, [
                'Order ID' => $data['order']->milisecond,
                'Customer Name' => $nameofpickup,
                'status' => $data['order']->status,
                'OTP Code' => otpCode(5),
            ]);

            $smsTemplatesDelivery = SMSData('twilio', 'get', null, [
                'Order ID' => $data['order']->milisecond,
                'Customer Name' => $namedelivery,
                'status' => $data['order']->status,
                'OTP Code' => otpCode(5),
            ]);

            // Log::info("SMSData Pickup Response: " . json_encode($smsTemplatesPickup));
            // Log::info("SMSData Delivery Response: " . json_encode($smsTemplatesDelivery));

            // Check if SMSData() returned valid array
            if (is_array($smsTemplatesPickup) && is_array($smsTemplatesDelivery)) {
                $pickup_verification_code = $smsTemplatesPickup['pickup_verification_code'] ?? null;
                $delivery_verification_code = $smsTemplatesDelivery['delivery_verification_code'] ?? null;
            } else {
                // Log::error("SMSData() returned invalid format. Pickup: " . json_encode($smsTemplatesPickup) . " | Delivery: " . json_encode($smsTemplatesDelivery));
                return false;
            }

            // Delivery Contact ke liye SMS bhejna
            if (!empty($deliveryContact) && !empty($delivery_verification_code)) {
                try {
                    $response = $smsService->sendSMS($deliveryContact, $delivery_verification_code);
                    // Log::info("Delivery SMS Response: " . print_r($response, true));
                } catch (\Exception $e) {
                    Log::error("Delivery SMS Error: " . $e->getMessage());
                }
            }

            //Pickup Contact ke liye SMS bhejna
            if (!empty($pickupContact) && !empty($pickup_verification_code)) {
                try {
                    $customerResponse = $smsService->sendSMS($pickupContact, $pickup_verification_code);
                    // Log::info("Pickup SMS Response: " . print_r($customerResponse, true));
                } catch (\Exception $e) {
                    Log::error("Pickup SMS Error: " . $e->getMessage());
                }
            }

            break;
        case 'courier_departed':
            $setting = SettingData('order_mail', 'order_courier_departed_mail');
            $data['history_message'] = __('message.courier_departed_history', ['id' => $order_id]);
            $history_data = [
                'delivery_man_id' => $data['order']->delivery_man_id,
                'delivery_man_name' => optional($data['order']->delivery_man)->name,
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['admin', 'client'];
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => str_replace('_', ' ', ucfirst($orderData->status)),
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                    ]));
                }
            }
            break;

        case 'courier_arrived':
            $setting = SettingData('order_mail', 'order_courier_arrived_mail');
            $data['history_message'] = __('message.courier_arrived_history');

            $history_data = [
                'order_id' => $data['order_id'],
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['admin', 'client'];
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => $orderData->status,
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                    ]));
                }
            }
            $smscheck = (int) (appSettingData('is_sms_order')->is_sms_order ?? 0);
            Log::info("Final SMS Check Value: " . $smscheck);

            // Agar SMS sending disabled hai, to yahin return kar do
            if ($smscheck === 0) {
                $errorMessage = __('message.sms_permission_denied');
                Log::warning("SMS Sending Disabled: " . $errorMessage);
                return false;
            }
            if (is_string($data['order']->delivery_point)) {
                $deliveryPoint = json_decode($data['order']->delivery_point, true);
            } elseif (is_array($data['order']->delivery_point)) {
                $deliveryPoint = $data['order']->delivery_point; // Already an array
            } else {
                $deliveryPoint = [];
            }

            // Delivery Contact get karna
            $contactNumber = $deliveryPoint['contact_number'] ?? null;
            $deliveryName = $deliveryPoint['name'] ?? null;

            $orderId = $data['order']->milisecond ?? null;
            // Order Tracking Link Generate
            $fullTrackingUrl = config('app.url') . route('sms-orderhistory', ['order_id' => $orderId], false);
            $shortLink = shortenWithTinyURL($fullTrackingUrl);

            Log::info("Contact Number for SMS: " . $contactNumber);

            if (!empty($contactNumber)) {
                try {

                    $smsType = isset($orderData->sms_type) ? $orderData->sms_type : '';
                    Log::info("Selected SMS Provider: " . $smsType);
                    $smsService = app()->make(\App\Services\SmsService::class);

                    $message = SMSData('twilio', 'get', null, [
                        'Order ID' => $data['order']->milisecond ?? '-',
                        'Customer Name' => $deliveryName ?? '-',
                        'Tracking Link' => $shortLink ?? '-',
                        'status' => $data['order']->status,
                    ]);
                    $response = $smsService->sendSMS($contactNumber, $message);
                    // Log::info("SMS Response: " . print_r($response, true));

                } catch (\Exception $e) {
                    Log::error("SMS Error: " . $e->getMessage());
                }
            }
            break;

        case 'completed':
            $setting = SettingData('order_mail', 'order_completed_mail');
            $data['history_message'] = __('message.order_completed_history', ['id' => $order_id]);

            $history_data = [
                'order_id' => $data['order_id'],
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['admin', 'client'];
            $order = Order::find($order_id);
            $client = User::find($order->client_id);
            $deliveryMan = User::find($order->delivery_man_id);
            $processReferral = function ($user, $order_id) {
                if ($user && $user->partner_referral_code) {
                    $partner = User::where('referral_code', $user->partner_referral_code)->first();
                    if ($partner) {
                        $order = Order::find($order_id);

                        if ($order && $order->delivery_man_id && $order->client_id) {
                            $wallet = Wallet::firstOrCreate(['user_id' => $partner->id]);
                            $reference_amount = SettingData('reference_amount', 'reference_amount');
                            $reference_type = SettingData('reference_type', 'reference_type');
                            $maxEarningPerMonth = SettingData('max_earning_per_month', 'max_earning_per_month');

                            $currentMonth = now()->format('Y-m');
                            $totalMonthlyReferrals = WalletHistory::where(['user_id' => $partner->id])
                                ->where('type', 'credit')
                                ->where('transaction_type', 'Reference Reward')
                                ->whereDate('datetime', 'like', "$currentMonth%")
                                ->sum('amount');

                            if ($reference_type == 'fixed') {
                                $amount_to_deduct = $reference_amount;
                            } elseif ($reference_type == 'percentage') {
                                $amount_to_deduct = ($reference_amount / 100) * $wallet->total_amount;
                            } else {
                                $amount_to_deduct = 0;
                            }
                            if (($totalMonthlyReferrals + $amount_to_deduct) <= $maxEarningPerMonth) {
                                $wallet->total_amount = max($wallet->total_amount + $amount_to_deduct, 0);
                                $wallet->save();

                                WalletHistory::create([
                                    'user_id' => $partner->id,
                                    'type' => 'credit',
                                    'transaction_type' => 'Reference Reward',
                                    'currency' => appSettingcurrency('currency'),
                                    'amount' => $amount_to_deduct,
                                    'balance' => $wallet->total_amount,
                                    'order_id' => $order_id,
                                    'datetime' => now(),
                                ]);
                            }
                        }
                    }
                }
            };

            $processReferral($client, $order_id);
            $processReferral($deliveryMan, $order_id);
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => str_replace('_', '', ucfirst($orderData->status)),
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                        'client_name' => isset($data['order']->client) ? $data['order']->client->name : '',
                    ]));
                }
            }
            break;
        case 'failed':
            $data['history_message'] = __('message.order_failed_history', ['id' => $order_id, 'reason' => $data['order']->reason]);
            $history_data = [
                'reason' => $data['order']->reason,
                'status' => $data['order']->status,
            ];
            $sendTo = removeValueFromArray(['admin', 'client', 'delivery_man'], $user_type);
            break;
        case 'return':
            $setting = SettingData('order_mail', 'order_return_mail');
            $data['order_id'] = $data['order']->parent_order_id;
            $data['history_message'] = __('message.retrun_order_history', ['id' => $order_id]);
            $history_data = [
                'reason' => $data['order']->reason,
                'status' => $data['order']->status,
            ];
            processOrderHistory($data, $history_data);
            $sendTo = ['admin', 'client'];
            if ($setting == 1) {
                $dynamicData = [
                    '[order ID]' => $orderData->id,
                    '[status]' => $orderData->status,
                    '[Company name]' => config('app.name'),
                    '[client name]' => isset($data['order']->client) ? $data['order']->client->name : '',
                ];

                $email = $data['order']->client_id ? $data['order']->client->email : null;
                if ($email) {
                    $mailDescription = str_replace(array_keys($dynamicData), array_values($dynamicData), $emailData->mail_description ?? '');
                    Mail::to($email)->send(new SendMail($emailData->subject ?? '', $mailDescription, [
                        'order_id' => $orderData->id,
                        'status' => $orderData->status,
                        'company_name' => config('app.name'),
                    ]));
                }
            }
            break;
        case 'reschedule':
            $history_data = [
                'order_id' => $data['order_id'],
            ];
            $smscheck = (int) (appSettingData('is_sms_order')->is_sms_order ?? 0);
            Log::info("Final SMS Check Value: " . $smscheck);

            // Agar SMS sending disabled hai, to yahin return kar do
            if ($smscheck === 0) {
                $errorMessage = __('message.sms_permission_denied');
                Log::warning("SMS Sending Disabled: " . $errorMessage);
                return false;
            }
            if (is_string($data['order']->delivery_point)) {
                $deliveryPoint = json_decode($data['order']->delivery_point, true);
            } elseif (is_array($data['order']->delivery_point)) {
                $deliveryPoint = $data['order']->delivery_point; // Already an array
            } else {
                $deliveryPoint = [];
            }

            // Log::info("Delivery Point Data: " . print_r($deliveryPoint, true));

            $contactNumber = $deliveryPoint['contact_number'] ?? null;
            $name = $deliveryPoint['name'] ?? null;

            Log::info("Contact Number for SMS: " . $contactNumber);

            if (!empty($contactNumber)) {
                try {

                    $smsType = isset($orderData->sms_type) ? $orderData->sms_type : '';
                    Log::info("Selected SMS Provider: " . $smsType);
                    $smsService = app()->make(\App\Services\SmsService::class);

                    $message = SMSData('twilio', 'get', null, [
                        'Order ID' => $data['order']->milisecond,
                        'Customer Name' => $name,
                        'Date Time' => $data['order']->rescheduledatetime,
                        // 'status' => $data['order']->status,
                    ]);
                    //    dd($message);
                    $response = $smsService->sendSMS($contactNumber, $message);
                    // Log::info("SMS Response: " . print_r($response, true));

                } catch (\Exception $e) {
                    Log::error("SMS Error: " . $e->getMessage());
                }
            }
            break;
        default:
            # code...
            $history_data = [];
            break;
    }

    // $data['history_data'] = json_encode($history_data);
    // sleep(1);
    // OrderHistory::create($data);


    $notification_data = [
        'id' => $data['order']->id,
        'type' => $data['history_type'],
        'subject' => __('message.order_notification_title', ['id' => $order_id]),
        'message' => $data['history_message'] ?? null,
    ];

    foreach ($sendTo as $send) {

        switch ($send) {
            case 'admin':
                $user = User::whereUserType('admin')->first();
                if ($data['history_type'] == 'create') {
                    $template_data = $notification_data;
                    $template_data['message_subject'] = "New Order #" . $data['order']->id . " Created";
                    $template_data['message_body'] = "<p>Hi,</p><p>The order #" . $data['order']->id . " has been created by " . optional($data['order']->client)->name . ".</p>\n\n<p>Please login to your account and check order details.</p>\n\n<p>Regards,<br />" . env('APP_NAME') . "</p>";
                }
                break;
            case 'client':
                $user = User::whereId($data['order']->client_id)->first();
                if ($data['history_type'] == 'courier_assigned') {
                    $notification_data['message'] = __('message.courier_assigned_history.client', ['id' => $order_id, 'delivery_person' => $history_data['delivery_man_name']]);
                }

                if ($data['history_type'] == 'courier_transfer') {
                    $notification_data['message'] = __('message.courier_transfer_history.client', ['id' => $order_id, 'delivery_person' => $history_data['delivery_man_name']]);
                }
                break;


            case 'delivery_man':
                $user = User::whereId($data['order']->delivery_man_id)->first();
                if ($data['history_type'] == 'courier_assigned') {
                    $notification_data['message'] = __('message.courier_assigned_history.delivery_man', ['id' => $order_id]);
                }

                if ($data['history_type'] == 'courier_transfer') {
                    $notification_data['message'] = __('message.courier_transfer_history.delivery_man', ['id' => $order_id]);
                }
                break;
            case 'deliveryMan':
                $user = User::whereId($data['deliveryManId'])->first();
                $notification_data['message'] = __('message.apply_reject_history.delivery_man', ['id' => $order_id]);

                break;
        }
        if ($user != null) {
            $user->notify(new OrderNotification($notification_data));
            $user->notify(new CommonNotification($notification_data['type'], $notification_data));
        }
    }
}
function processOrderHistory($data, $history_data)
{
    $data['history_data'] = json_encode($history_data);
    sleep(1);
    OrderHistory::create($data);
}
function stringLong($str = '', $type = 'title', $length = 0) //Add … if string is too long
{
    if ($length != 0) {
        return strlen($str) > $length ? mb_substr($str, 0, $length) . "..." : $str;
    }
    if ($type == 'desc') {
        return strlen($str) > 150 ? mb_substr($str, 0, 150) . "..." : $str;
    } elseif ($type == 'title') {
        return strlen($str) > 15 ? mb_substr($str, 0, 25) . "..." : $str;
    } else {
        return $str;
    }
}

function currencyArray($code = null)
{
    // Only include USD and LBP
    $currency = [
        ['code' => 'USD', 'name' => 'United States (US) dollar', 'symbol' => '$'],
        ['code' => 'LBP', 'name' => 'Lebanese pound', 'symbol' => 'ل.ل']
    ];

    if ($code != null) {
        $currency = collect($currency)->where('code', $code)->first();
    }
    return $currency;
}

function maskSensitiveInfo($type, $info)
{
    if ($type === 'email' && empty($info) or $type === 'contact_number' && empty($info)) {
        return '-';
    }
    if (env('APP_DEMO')) {
        switch ($type) {
            case 'email':
                $parts = explode('@', $info);
                $username = $parts[0];
                $domain = $parts[1];
                $maskedUsername = substr($username, 0, 1) . str_repeat('*', strlen($username) - 1);
                return $maskedUsername . '@' . $domain;

            case 'contact_number':
                return substr($info, 0, 3) . str_repeat('*', strlen($info) - 4) . substr($info, -2);

            default:
                return $info;
        }
    } else {
        return $info;
    }
}

function mighty_language_direction($language = null)
{
    if (empty($language)) {
        $language = app()->getLocale();
    }
    $language = strtolower(substr($language, 0, 2));
    $rtlLanguages = [
        'ar', //  'العربية', Arabic
        'arc', //  'ܐܪܡܝܐ', Aramaic
        'bcc', //  'بلوچی مکرانی', Southern Balochi`
        'bqi', //  'بختياري', Bakthiari
        'ckb', //  'Soranî / کوردی', Sorani Kurdish
        'dv', //  'ދިވެހިބަސް', Dhivehi
        'fa', //  'فارسی', Persian
        'glk', //  'گیلکی', Gilaki
        'he', //  'עברית', Hebrew
        'lrc', //- 'لوری', Northern Luri
        'mzn', //  'مازِرونی', Mazanderani
        'pnb', //  'پنجابی', Western Punjabi
        'ps', //  'پښتو', Pashto
        'sd', //  'سنڌي', Sindhi
        'ug', //  'Uyghurche / ئۇيغۇرچە', Uyghur
        'ur', //  'اردو', Urdu
        'yi', //  'ייִדיש', Yiddish
    ];
    if (in_array($language, $rtlLanguages)) {
        return 'rtl';
    }

    return 'ltr';
}

function country()
{

    $country = [
        [
            "countryNameEn" => "Andorra",
            "countryCode" => "AD",
            "region" => "Europe",
            "flag" => "🇦🇩"
        ],
        [
            "countryNameEn" => "Afghanistan",
            "countryCode" => "AF",
            "region" => "Asia & Pacific",
            "flag" => "🇦🇫"
        ],
        [
            "countryNameEn" => "Antigua and Barbuda",
            "countryCode" => "AG",
            "region" => "South/Latin America",
            "flag" => "🇦🇬"
        ],
        [
            "countryNameEn" => "Anguilla",
            "countryCode" => "AI",
            "region" => "South/Latin America",
            "flag" => "🇦🇮"
        ],
        [
            "countryNameEn" => "Albania",
            "countryCode" => "AL",
            "region" => "Europe",
            "flag" => "🇦🇱"
        ],
        [
            "countryNameEn" => "Armenia",
            "countryCode" => "AM",
            "region" => "Europe",
            "flag" => "🇦🇲"
        ],
        [
            "countryNameEn" => "Angola",
            "countryCode" => "AO",
            "region" => "Africa",
            "flag" => "🇦🇴"
        ],
        [
            "countryNameEn" => "Antarctica",
            "countryCode" => "AQ",
            "region" => "Asia & Pacific",
            "flag" => "🇦🇶"
        ],
        [
            "countryNameEn" => "Argentina",
            "countryCode" => "AR",
            "region" => "South/Latin America",
            "flag" => "🇦🇷"
        ],
        [
            "countryNameEn" => "American Samoa",
            "countryCode" => "AS",
            "region" => "Asia & Pacific",
            "flag" => "🇦🇸"
        ],
        [
            "countryNameEn" => "Austria",
            "countryCode" => "AT",
            "region" => "Europe",
            "flag" => "🇦🇹"
        ],
        [
            "countryNameEn" => "Australia",
            "countryCode" => "AU",
            "region" => "Asia & Pacific",
            "flag" => "🇦🇺"
        ],
        [
            "countryNameEn" => "Aruba",
            "countryCode" => "AW",
            "region" => "South/Latin America",
            "flag" => "🇦🇼"
        ],
        [
            "countryNameEn" => "Åland Islands",
            "countryCode" => "AX",
            "region" => "Europe",
            "flag" => "🇦🇽"
        ],
        [
            "countryNameEn" => "Azerbaijan",
            "countryCode" => "AZ",
            "region" => "Asia & Pacific",
            "flag" => "🇦🇿"
        ],
        [
            "countryNameEn" => "Bosnia and Herzegovina",
            "countryCode" => "BA",
            "region" => "Europe",
            "flag" => "🇧🇦"
        ],
        [
            "countryNameEn" => "Barbados",
            "countryCode" => "BB",
            "region" => "South/Latin America",
            "flag" => "🇧🇧"
        ],
        [
            "countryNameEn" => "Bangladesh",
            "countryCode" => "BD",
            "region" => "Asia & Pacific",
            "flag" => "🇧🇩"
        ],
        [
            "countryNameEn" => "Belgium",
            "countryCode" => "BE",
            "region" => "Europe",
            "flag" => "🇧🇪"
        ],
        [
            "countryNameEn" => "Burkina Faso",
            "countryCode" => "BF",
            "region" => "Africa",
            "flag" => "🇧🇫"
        ],
        [
            "countryNameEn" => "Bulgaria",
            "countryCode" => "BG",
            "region" => "Europe",
            "flag" => "🇧🇬"
        ],
        [
            "countryNameEn" => "Bahrain",
            "countryCode" => "BH",
            "region" => "Arab States",
            "flag" => "🇧🇭"
        ],
        [
            "countryNameEn" => "Burundi",
            "countryCode" => "BI",
            "region" => "Africa",
            "flag" => "🇧🇮"
        ],
        [
            "countryNameEn" => "Benin",
            "countryCode" => "BJ",
            "region" => "Africa",
            "flag" => "🇧🇯"
        ],
        [
            "countryNameEn" => "Saint Barthélemy",
            "countryCode" => "BL",
            "region" => "South/Latin America",
            "flag" => "🇧🇱"
        ],
        [
            "countryNameEn" => "Bermuda",
            "countryCode" => "BM",
            "region" => "North America",
            "flag" => "🇧🇲"
        ],
        [
            "countryNameEn" => "Brunei Darussalam",
            "countryCode" => "BN",
            "region" => "Asia & Pacific",
            "flag" => "🇧🇳"
        ],
        [
            "countryNameEn" => "Bolivia (Plurinational State of)",
            "countryCode" => "BO",
            "region" => "South/Latin America",
            "flag" => "🇧🇴"
        ],
        [
            "countryNameEn" => "Bonaire, Sint Eustatius and Saba",
            "countryCode" => "BQ",
            "region" => "Unknown",
            "flag" => "🇧🇶"
        ],
        [
            "countryNameEn" => "Brazil",
            "countryCode" => "BR",
            "region" => "South/Latin America",
            "flag" => "🇧🇷"
        ],
        [
            "countryNameEn" => "Bhutan",
            "region" => "Asia & Pacific",
            "flag" => "🇧🇹"
        ],
        [
            "countryNameEn" => "Bouvet Island",
            "countryCode" => "BV",
            "region" => "South/Latin America",
            "flag" => "🇧🇻"
        ],
        [
            "countryNameEn" => "Botswana",
            "countryCode" => "BW",
            "region" => "Africa",
            "flag" => "🇧🇼"
        ],
        [
            "countryNameEn" => "Belarus",
            "countryCode" => "BY",
            "region" => "Europe",
            "flag" => "🇧🇾"
        ],
        [
            "countryNameEn" => "Belize",
            "countryCode" => "BZ",
            "region" => "South/Latin America",
            "flag" => "🇧🇿"
        ],
        [
            "countryNameEn" => "Canada",
            "countryCode" => "CA",
            "region" => "North America",
            "flag" => "🇨🇦"
        ],
        [
            "countryNameEn" => "Switzerland",
            "countryCode" => "CH",
            "region" => "Europe",
            "flag" => "🇨🇭"
        ],
        [
            "countryNameEn" => "Côte d'Ivoire",
            "countryCode" => "CI",
            "region" => "Africa",
            "flag" => "🇨🇮"
        ],
        [
            "countryNameEn" => "Chile",
            "countryCode" => "CL",
            "region" => "South/Latin America",
            "flag" => "🇨🇱"
        ],
        [
            "countryNameEn" => "Cameroon",
            "countryCode" => "CM",
            "region" => "Africa",
            "flag" => "🇨🇲"
        ],
        [
            "countryNameEn" => "China",
            "countryCode" => "CN",
            "region" => "Asia & Pacific",
            "flag" => "🇨🇳"
        ],
        [
            "countryNameEn" => "Colombia",
            "countryCode" => "CO",
            "region" => "South/Latin America",
            "flag" => "🇨🇴"
        ],
        [
            "countryNameEn" => "Costa Rica",
            "countryCode" => "CR",
            "region" => "South/Latin America",
            "flag" => "🇨🇷"
        ],
        [
            "countryNameEn" => "Cuba",
            "countryCode" => "CU",
            "region" => "South/Latin America",
            "flag" => "🇨🇺"
        ],
        [
            "countryNameEn" => "Cabo Verde",
            "countryCode" => "CV",
            "region" => "Africa",
            "flag" => "🇨🇻"
        ],
        [
            "countryNameEn" => "Curaçao",
            "countryCode" => "CW",
            "region" => "Unknown",
            "flag" => "🇨🇼"
        ],
        [
            "countryNameEn" => "Christmas Island",
            "countryCode" => "CX",
            "region" => "Asia & Pacific",
            "flag" => "🇨🇽"
        ],
        [
            "countryNameEn" => "Cyprus",
            "countryCode" => "CY",
            "region" => "Europe",
            "flag" => "🇨🇾"
        ],
        [
            "countryNameEn" => "Germany",
            "countryCode" => "DE",
            "region" => "Europe",
            "flag" => "🇩🇪"
        ],
        [
            "countryNameEn" => "Djibouti",
            "countryCode" => "DJ",
            "flag" => "🇩🇯"
        ],
        [
            "countryNameEn" => "Denmark",
            "countryCode" => "DK",
            "region" => "Europe",
            "flag" => "🇩🇰"
        ],
        [
            "countryNameEn" => "Dominica",
            "countryCode" => "DM",
            "region" => "South/Latin America",
            "flag" => "🇩🇲"
        ],
        [
            "countryNameEn" => "Algeria",
            "countryCode" => "DZ",
            "region" => "Arab States",
            "flag" => "🇩🇿"
        ],
        [
            "countryNameEn" => "Ecuador",
            "countryCode" => "EC",
            "region" => "South/Latin America",
            "flag" => "🇪🇨"
        ],
        [
            "countryNameEn" => "Estonia",
            "countryCode" => "EE",
            "region" => "Europe",
            "flag" => "🇪🇪"
        ],
        [
            "countryNameEn" => "Egypt",
            "countryCode" => "EG",
            "region" => "Arab States",
            "flag" => "🇪🇬"
        ],
        [
            "countryNameEn" => "Western Sahara",
            "countryCode" => "EH",
            "region" => "Africa",
            "flag" => "🇪🇭"
        ],
        [
            "countryNameEn" => "Eritrea",
            "countryCode" => "ER",
            "region" => "Africa",
            "flag" => "🇪🇷"
        ],
        [
            "countryNameEn" => "Spain",
            "countryCode" => "ES",
            "region" => "Europe",
            "flag" => "🇪🇸"
        ],
        [
            "countryNameEn" => "Ethiopia",
            "countryCode" => "ET",
            "region" => "Africa",
            "flag" => "🇪🇹"
        ],
        [
            "countryNameEn" => "Finland",
            "countryCode" => "FI",
            "region" => "Europe",
            "flag" => "🇫🇮"
        ],
        [
            "countryNameEn" => "Fiji",
            "countryCode" => "FJ",
            "region" => "Asia & Pacific",
            "flag" => "🇫🇯"
        ],
        [
            "countryNameEn" => "Micronesia (Federated States of)",
            "countryCode" => "FM",
            "region" => "Asia & Pacific",
            "flag" => "🇫🇲"
        ],
        [
            "countryNameEn" => "France",
            "countryCode" => "FR",
            "region" => "Europe",
            "flag" => "🇫🇷"
        ],
        [
            "countryNameEn" => "Gabon",
            "countryCode" => "GA",
            "region" => "Africa",
            "flag" => "🇬🇦"
        ],
        [
            "countryNameEn" => "Grenada",
            "countryCode" => "GD",
            "region" => "South/Latin America",
            "flag" => "🇬🇩"
        ],
        [
            "countryNameEn" => "Georgia",
            "countryCode" => "GE",
            "region" => "Europe",
            "flag" => "🇬🇪"
        ],
        [
            "countryNameEn" => "French Guiana",
            "countryCode" => "GF",
            "region" => "South/Latin America",
            "flag" => "🇬🇫"
        ],
        [
            "countryNameEn" => "Guernsey",
            "countryCode" => "GG",
            "region" => "Europe",
            "flag" => "🇬🇬"
        ],
        [
            "countryNameEn" => "Ghana",
            "countryCode" => "GH",
            "region" => "Africa",
            "flag" => "🇬🇭"
        ],
        [
            "countryNameEn" => "Gibraltar",
            "countryCode" => "GI",
            "region" => "Europe",
            "flag" => "🇬🇮"
        ],
        [
            "countryNameEn" => "Greenland",
            "countryCode" => "GL",
            "region" => "Europe",
            "flag" => "🇬🇱"
        ],
        [
            "countryNameEn" => "Guinea",
            "countryCode" => "GN",
            "region" => "Africa",
            "flag" => "🇬🇳"
        ],
        [
            "countryNameEn" => "Guadeloupe",
            "countryCode" => "GP",
            "region" => "South/Latin America",
            "flag" => "🇬🇵"
        ],
        [
            "countryNameEn" => "Equatorial Guinea",
            "countryCode" => "GQ",
            "region" => "Africa",
            "flag" => "🇬🇶"
        ],
        [
            "countryNameEn" => "Greece",
            "countryCode" => "GR",
            "region" => "Europe",
            "flag" => "🇬🇷"
        ],
        [
            "countryNameEn" => "South Georgia and the South Sandwich Islands",
            "countryCode" => "GS",
            "region" => "South/Latin America",
            "flag" => "🇬🇸"
        ],
        [
            "countryNameEn" => "Guatemala",
            "countryCode" => "GT",
            "region" => "South/Latin America",
            "flag" => "🇬🇹"
        ],
        [
            "countryNameEn" => "Guam",
            "countryCode" => "GU",
            "region" => "Asia & Pacific",
            "flag" => "🇬🇺"
        ],
        [
            "countryNameEn" => "Guinea-Bissau",
            "countryCode" => "GW",
            "region" => "Africa",
            "flag" => "🇬🇼"
        ],
        [
            "countryNameEn" => "Guyana",
            "countryCode" => "GY",
            "region" => "South/Latin America",
            "flag" => "🇬🇾"
        ],
        [
            "countryNameEn" => "Hong Kong",
            "countryCode" => "HK",
            "region" => "Asia & Pacific",
            "flag" => "🇭🇰"
        ],
        [
            "countryNameEn" => "Honduras",
            "countryCode" => "HN",
            "region" => "South/Latin America",
            "flag" => "🇭🇳"
        ],
        [
            "countryNameEn" => "Croatia",
            "countryCode" => "HR",
            "region" => "Europe",
            "flag" => "🇭🇷"
        ],
        [
            "countryNameEn" => "Haiti",
            "countryCode" => "HT",
            "region" => "South/Latin America",
            "flag" => "🇭🇹"
        ],
        [
            "countryNameEn" => "Hungary",
            "countryCode" => "HU",
            "region" => "Europe",
            "flag" => "🇭🇺"
        ],
        [
            "countryNameEn" => "Indonesia",
            "countryCode" => "ID",
            "region" => "Asia & Pacific",
            "flag" => "🇮🇩"
        ],
        [
            "countryNameEn" => "Ireland",
            "countryCode" => "IE",
            "region" => "Europe",
            "flag" => "🇮🇪"
        ],
        [
            "countryNameEn" => "Israel",
            "countryCode" => "IL",
            "region" => "Europe",
            "flag" => "🇮🇱"
        ],
        [
            "countryNameEn" => "Isle of Man",
            "countryCode" => "IM",
            "region" => "Europe",
            "flag" => "🇮🇲"
        ],
        [
            "countryNameEn" => "India",
            "countryCode" => "IN",
            "region" => "Asia & Pacific",
            "flag" => "🇮🇳"
        ],
        [
            "countryNameEn" => "British Indian Ocean Territories",
            "countryCode" => "IO",
            "region" => "Indian Ocean",
            "flag" => "🇮🇴",
        ],
        [
            "countryNameEn" => "Iraq",
            "countryCode" => "IQ",
            "region" => "Arab States",
            "flag" => "🇮🇶"
        ],
        [
            "countryNameEn" => "Iran (Islamic Republic of)",
            "countryCode" => "IR",
            "region" => "Asia & Pacific",
            "flag" => "🇮🇷"
        ],
        [
            "countryNameEn" => "Iceland",
            "countryCode" => "IS",
            "region" => "Europe",
            "flag" => "🇮🇸"
        ],
        [
            "countryNameEn" => "Italy",
            "countryCode" => "IT",
            "region" => "Europe",
            "flag" => "🇮🇹"
        ],
        [
            "countryNameEn" => "Jersey",
            "countryCode" => "JE",
            "region" => "Europe",
            "flag" => "🇯🇪"
        ],
        [
            "countryNameEn" => "Jamaica",
            "countryCode" => "JM",
            "region" => "South/Latin America",
            "flag" => "🇯🇲"
        ],
        [
            "countryNameEn" => "Jordan",
            "countryCode" => "JO",
            "region" => "Arab States",
            "flag" => "🇯🇴"
        ],
        [
            "countryNameEn" => "Japan",
            "countryCode" => "JP",
            "region" => "Asia & Pacific",
            "flag" => "🇯🇵"
        ],
        [
            "countryNameEn" => "Kenya",
            "countryCode" => "KE",
            "region" => "Africa",
            "flag" => "🇰🇪"
        ],
        [
            "countryNameEn" => "Kyrgyzstan",
            "countryCode" => "KG",
            "region" => "Asia & Pacific",
            "flag" => "🇰🇬"
        ],
        [
            "countryNameEn" => "Cambodia",
            "countryCode" => "KH",
            "region" => "Asia & Pacific",
            "flag" => "🇰🇭"
        ],
        [
            "countryNameEn" => "North Korea",
            "countryCode" => "KP",
            "region" => "Asia",
            "flag" => "🇰🇵"
        ],
        [
            "countryNameEn" => "South Korea",
            "countryCode" => "KR",
            "region" => "Asia",
            "flag" => "🇰🇷"
        ],
        [
            "countryNameEn" => "Kiribati",
            "countryCode" => "KI",
            "region" => "Asia & Pacific",
            "flag" => "🇰🇮"
        ],
        [
            "countryNameEn" => "Saint Kitts and Nevis",
            "countryCode" => "KN",
            "region" => "South/Latin America",
            "flag" => "🇰🇳"
        ],
        [
            "countryNameEn" => "Kuwait",
            "countryCode" => "KW",
            "region" => "Arab States",
            "flag" => "🇰🇼"
        ],
        [
            "countryNameEn" => "Kazakhstan",
            "countryCode" => "KZ",
            "region" => "Asia & Pacific",
            "flag" => "🇰🇿"
        ],
        [
            "countryNameEn" => "Lebanon",
            "countryCode" => "LB",
            "region" => "Arab States",
            "flag" => "🇱🇧"
        ],
        [
            "countryNameEn" => "Saint Lucia",
            "countryCode" => "LC",
            "region" => "South/Latin America",
            "flag" => "🇱🇨"
        ],
        [
            "countryNameEn" => "Liechtenstein",
            "countryCode" => "LI",
            "region" => "Europe",
            "flag" => "🇱🇮"
        ],
        [
            "countryNameEn" => "Sri Lanka",
            "countryCode" => "LK",
            "region" => "Asia & Pacific",
            "flag" => "🇱🇰"
        ],
        [
            "countryNameEn" => "Liberia",
            "countryCode" => "LR",
            "region" => "Africa",
            "flag" => "🇱🇷"
        ],
        [
            "countryNameEn" => "Lesotho",
            "countryCode" => "LS",
            "region" => "Africa",
            "flag" => "🇱🇸"
        ],
        [
            "countryNameEn" => "Lithuania",
            "countryCode" => "LT",
            "region" => "Europe",
            "flag" => "🇱🇹"
        ],
        [
            "countryNameEn" => "Luxembourg",
            "countryCode" => "LU",
            "region" => "Europe",
            "flag" => "🇱🇺"
        ],
        [
            "countryNameEn" => "Latvia",
            "countryCode" => "LV",
            "region" => "Europe",
            "flag" => "🇱🇻"
        ],
        [
            "countryNameEn" => "Libya",
            "countryCode" => "LY",
            "region" => "Arab States",
            "flag" => "🇱🇾"
        ],
        [
            "countryNameEn" => "Morocco",
            "countryCode" => "MA",
            "region" => "Arab States",
            "flag" => "🇲🇦"
        ],
        [
            "countryNameEn" => "Monaco",
            "countryCode" => "MC",
            "region" => "Europe",
            "flag" => "🇲🇨"
        ],
        [
            "countryNameEn" => "Montenegro",
            "countryCode" => "ME",
            "region" => "Europe",
            "flag" => "🇲🇪"
        ],
        [
            "countryNameEn" => "Saint Martin (French part)",
            "countryCode" => "MF",
            "region" => "South/Latin America",
            "flag" => "🇲🇫"
        ],
        [
            "countryNameEn" => "Madagascar",
            "countryCode" => "MG",
            "region" => "Africa",
            "flag" => "🇲🇬"
        ],
        [
            "countryNameEn" => "Mali",
            "countryCode" => "ML",
            "region" => "Africa",
            "flag" => "🇲🇱"
        ],
        [
            "countryNameEn" => "Myanmar",
            "countryCode" => "MM",
            "region" => "Asia & Pacific",
            "flag" => "🇲🇲"
        ],
        [
            "countryNameEn" => "Mongolia",
            "countryCode" => "MN",
            "region" => "Asia & Pacific",
            "flag" => "🇲🇳"
        ],
        [
            "countryNameEn" => "Macao",
            "countryCode" => "MO",
            "region" => "Asia & Pacific",
            "flag" => "🇲🇴"
        ],
        [
            "countryNameEn" => "Martinique",
            "countryCode" => "MQ",
            "region" => "South/Latin America",
            "flag" => "🇲🇶"
        ],
        [
            "countryNameEn" => "Mauritania",
            "countryCode" => "MR",
            "region" => "Arab States",
            "flag" => "🇲🇷"
        ],
        [
            "countryNameEn" => "Montserrat",
            "countryCode" => "MS",
            "region" => "South/Latin America",
            "flag" => "🇲🇸"
        ],
        [
            "countryNameEn" => "Malta",
            "countryCode" => "MT",
            "region" => "Europe",
            "flag" => "🇲🇹"
        ],
        [
            "countryNameEn" => "Mauritius",
            "countryCode" => "MU",
            "region" => "Africa",
            "flag" => "🇲🇺"
        ],
        [
            "countryNameEn" => "Maldives",
            "countryCode" => "MV",
            "region" => "Asia & Pacific",
            "flag" => "🇲🇻"
        ],
        [
            "countryNameEn" => "Malawi",
            "countryCode" => "MW",
            "region" => "Africa",
            "flag" => "🇲🇼"
        ],
        [
            "countryNameEn" => "Mexico",
            "countryCode" => "MX",
            "region" => "South/Latin America",
            "flag" => "🇲🇽"
        ],
        [
            "countryNameEn" => "Malaysia",
            "countryCode" => "MY",
            "region" => "Asia & Pacific",
            "flag" => "🇲🇾"
        ],
        [
            "countryNameEn" => "Mozambique",
            "countryCode" => "MZ",
            "region" => "Africa",
            "flag" => "🇲🇿"
        ],
        [
            "countryNameEn" => "Namibia",
            "countryCode" => "NA",
            "region" => "Africa",
            "flag" => "🇳🇦"
        ],
        [
            "countryNameEn" => "New Caledonia",
            "countryCode" => "NC",
            "region" => "Asia & Pacific",
            "flag" => "🇳🇨"
        ],
        [
            "countryNameEn" => "Norfolk Island",
            "countryCode" => "NF",
            "region" => "Asia & Pacific",
            "flag" => "🇳🇫"
        ],
        [
            "countryNameEn" => "Nigeria",
            "countryCode" => "NG",
            "region" => "Africa",
            "flag" => "🇳🇬"
        ],
        [
            "countryNameEn" => "Nicaragua",
            "countryCode" => "NI",
            "region" => "South/Latin America",
            "flag" => "🇳🇮"
        ],
        [
            "countryNameEn" => "Norway",
            "countryCode" => "NO",
            "region" => "Europe",
            "flag" => "🇳🇴"
        ],
        [
            "countryNameEn" => "Nepal",
            "countryCode" => "NP",
            "region" => "Asia & Pacific",
            "flag" => "🇳🇵"
        ],
        [
            "countryNameEn" => "Nauru",
            "countryCode" => "NR",
            "region" => "Asia & Pacific",
            "flag" => "🇳🇷"
        ],
        [
            "countryNameEn" => "Niue",
            "countryCode" => "NU",
            "region" => "Asia & Pacific",
            "flag" => "🇳🇺"
        ],
        [
            "countryNameEn" => "New Zealand",
            "countryCode" => "NZ",
            "region" => "Asia & Pacific",
            "flag" => "🇳🇿"
        ],
        [
            "countryNameEn" => "Oman",
            "countryCode" => "OM",
            "region" => "Arab States",
            "flag" => "🇴🇲"
        ],
        [
            "countryNameEn" => "Panama",
            "countryCode" => "PA",
            "region" => "South/Latin America",
            "flag" => "🇵🇦"
        ],
        [
            "countryNameEn" => "Peru",
            "countryCode" => "PE",
            "region" => "South/Latin America",
            "flag" => "🇵🇪"
        ],
        [
            "countryNameEn" => "French Polynesia",
            "countryCode" => "PF",
            "region" => "Asia & Pacific",
            "flag" => "🇵🇫"
        ],
        [
            "countryNameEn" => "Papua New Guinea",
            "countryCode" => "PG",
            "region" => "Asia & Pacific",
            "flag" => "🇵🇬"
        ],
        [
            "countryNameEn" => "Pakistan",
            "countryCode" => "PK",
            "region" => "Asia & Pacific",
            "flag" => "🇵🇰"
        ],
        [
            "countryNameEn" => "Poland",
            "countryCode" => "PL",
            "region" => "Europe",
            "flag" => "🇵🇱"
        ],
        [
            "countryNameEn" => "Saint Pierre and Miquelon",
            "countryCode" => "PM",
            "region" => "North America",
            "flag" => "🇵🇲"
        ],
        [
            "countryNameEn" => "Pitcairn",
            "countryCode" => "PN",
            "region" => "Asia & Pacific",
            "flag" => "🇵🇳"
        ],
        [
            "countryNameEn" => "Puerto Rico",
            "countryCode" => "PR",
            "region" => "South/Latin America",
            "flag" => "🇵🇷"
        ],
        [
            "countryNameEn" => "Palestine, State of",
            "countryCode" => "PS",
            "region" => "Arab States",
            "flag" => "🇵🇸"
        ],
        [
            "countryNameEn" => "Portugal",
            "countryCode" => "PT",
            "region" => "Europe",
            "flag" => "🇵🇹"
        ],
        [
            "countryNameEn" => "Palau",
            "countryCode" => "PW",
            "region" => "Asia & Pacific",
            "flag" => "🇵🇼"
        ],
        [
            "countryNameEn" => "Paraguay",
            "countryCode" => "PY",
            "region" => "South/Latin America",
            "flag" => "🇵🇾"
        ],
        [
            "countryNameEn" => "Qatar",
            "countryCode" => "QA",
            "region" => "Arab States",
            "flag" => "🇶🇦"
        ],
        [
            "countryNameEn" => "Réunion",
            "countryCode" => "RE",
            "region" => "Asia & Pacific",
            "flag" => "🇷🇪"
        ],
        [
            "countryNameEn" => "Romania",
            "countryCode" => "RO",
            "region" => "Europe",
            "flag" => "🇷🇴"
        ],
        [
            "countryNameEn" => "Serbia",
            "countryCode" => "RS",
            "region" => "Europe",
            "flag" => "🇷🇸"
        ],
        [
            "countryNameEn" => "Russia",
            "countryCode" => "RU",
            "region" => "Europe",
            "flag" => "🇷🇺"
        ],
        [
            "countryNameEn" => "Rwanda",
            "countryCode" => "RW",
            "region" => "Africa",
            "flag" => "🇷🇼"
        ],
        [
            "countryNameEn" => "Saudi Arabia",
            "countryCode" => "SA",
            "region" => "Arab States",
            "flag" => "🇸🇦"
        ],
        [
            "countryNameEn" => "Solomon Islands",
            "countryCode" => "SB",
            "region" => "Asia & Pacific",
            "flag" => "🇸🇧"
        ],
        [
            "countryNameEn" => "Seychelles",
            "countryCode" => "SC",
            "region" => "Africa",
            "flag" => "🇸🇨"
        ],
        [
            "countryNameEn" => "Sweden",
            "countryCode" => "SE",
            "region" => "Europe",
            "flag" => "🇸🇪"
        ],
        [
            "countryNameEn" => "Singapore",
            "countryCode" => "SG",
            "region" => "Asia & Pacific",
            "flag" => "🇸🇬"
        ],
        [
            "countryNameEn" => "Saint Helena, Ascension and Tristan da Cunha",
            "countryCode" => "SH",
            "region" => "Africa",
            "flag" => "🇸🇭"
        ],
        [
            "countryNameEn" => "Slovenia",
            "countryCode" => "SI",
            "region" => "Europe",
            "flag" => "🇸🇮"
        ],
        [
            "countryNameEn" => "Svalbard and Jan Mayen",
            "countryCode" => "SJ",
            "region" => "Europe",
            "flag" => "🇸🇯"
        ],
        [
            "countryNameEn" => "Slovakia",
            "countryCode" => "SK",
            "region" => "Europe",
            "flag" => "🇸🇰"
        ],
        [
            "countryNameEn" => "Sierra Leone",
            "countryCode" => "SL",
            "region" => "Africa",
            "flag" => "🇸🇱"
        ],
        [
            "countryNameEn" => "Republic of San Marino",
            "countryCode" => "SM",
            "region" => "Europe",
            "flag" => "🇸🇲"
        ],
        [
            "countryNameEn" => "Senegal",
            "countryCode" => "SN",
            "region" => "Africa",
            "flag" => "🇸🇳"
        ],
        [
            "countryNameEn" => "Somalia",
            "countryCode" => "SO",
            "region" => "Arab States",
            "flag" => "🇸🇴"
        ],
        [
            "countryNameEn" => "Suriname",
            "countryCode" => "SR",
            "region" => "South/Latin America",
            "flag" => "🇸🇷"
        ],
        [
            "countryNameEn" => "South Sudan",
            "countryCode" => "SS",
            "region" => "Africa",
            "flag" => "🇸🇸"
        ],
        [
            "countryNameEn" => "Sao Tome and Principe",
            "countryCode" => "ST",
            "region" => "Africa",
            "flag" => "🇸🇹"
        ],
        [
            "countryNameEn" => "El Salvador",
            "countryCode" => "SV",
            "region" => "South/Latin America",
            "flag" => "🇸🇻"
        ],
        [
            "countryNameEn" => "Sint Maarten (Dutch part)",
            "countryCode" => "SX",
            "region" => "Unknown",
            "flag" => "🇸🇽"
        ],
        [
            "countryNameEn" => "Syrian Arab Republic",
            "countryCode" => "SY",
            "region" => "Asia & Pacific",
            "flag" => "🇸🇾"
        ],
        [
            "countryNameEn" => "Chad",
            "countryCode" => "TD",
            "region" => "Africa",
            "flag" => "🇹🇩"
        ],
        [
            "countryNameEn" => "Togo",
            "countryCode" => "TG",
            "region" => "Africa",
            "flag" => "🇹🇬"
        ],
        [
            "countryNameEn" => "Thailand",
            "countryCode" => "TH",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇭"
        ],
        [
            "countryNameEn" => "Tajikistan",
            "countryCode" => "TJ",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇯"
        ],
        [
            "countryNameEn" => "Tokelau",
            "countryCode" => "TK",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇰"
        ],
        [
            "countryNameEn" => "Timor-Leste",
            "countryCode" => "TL",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇱"
        ],
        [
            "countryNameEn" => "Turkmenistan",
            "countryCode" => "TM",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇲"
        ],
        [
            "countryNameEn" => "Tunisia",
            "countryCode" => "TN",
            "region" => "Arab States",
            "flag" => "🇹🇳"
        ],
        [
            "countryNameEn" => "Tonga",
            "countryCode" => "TO",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇴"
        ],
        [
            "countryNameEn" => "Turkey",
            "countryCode" => "TR",
            "region" => "Europe",
            "flag" => "🇹🇷"
        ],
        [
            "countryNameEn" => "Trinidad and Tobago",
            "countryCode" => "TT",
            "region" => "South/Latin America",
            "flag" => "🇹🇹"
        ],
        [
            "countryNameEn" => "Tuvalu",
            "countryCode" => "TV",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇻"
        ],
        [
            "countryNameEn" => "United Republic of Tanzania",
            "countryCode" => "TZ",
            "region" => "Africa",
            "flag" => "🇹🇿"
        ],
        [
            "countryNameEn" => "Ukraine",
            "countryCode" => "UA",
            "region" => "Europe",
            "flag" => "🇺🇦"
        ],
        [
            "countryNameEn" => "Uganda",
            "countryCode" => "UG",
            "region" => "Africa",
            "flag" => "🇺🇬"
        ],
        [
            "countryNameEn" => "United States of America",
            "countryCode" => "US",
            "region" => "North America",
            "flag" => "🇺🇸"
        ],
        [
            "countryNameEn" => "Uruguay",
            "countryCode" => "UY",
            "region" => "South/Latin America",
            "flag" => "🇺🇾"
        ],
        [
            "countryNameEn" => "Uzbekistan",
            "countryCode" => "UZ",
            "region" => "Asia & Pacific",
            "flag" => "🇺🇿"
        ],
        [
            "countryNameEn" => "Saint Vincent and the Grenadines",
            "countryCode" => "VC",
            "region" => "South/Latin America",
            "flag" => "🇻🇨"
        ],
        [
            "countryNameEn" => "Venezuela (Bolivarian Republic of)",
            "countryCode" => "VE",
            "region" => "South/Latin America",
            "flag" => "🇻🇪"
        ],
        [
            "countryNameEn" => "Virgin Islands (British)",
            "countryCode" => "VG",
            "region" => "South/Latin America",
            "flag" => "🇻🇬"
        ],
        [
            "countryNameEn" => "Virgin Islands (U.S.)",
            "countryCode" => "VI",
            "region" => "South/Latin America",
            "flag" => "🇻🇮"
        ],
        [
            "countryNameEn" => "Vietnam",
            "countryCode" => "VN",
            "region" => "Asia & Pacific",
            "flag" => "🇻🇳"
        ],
        [
            "countryNameEn" => "Vanuatu",
            "countryCode" => "VU",
            "region" => "Asia & Pacific",
            "flag" => "🇻🇺"
        ],
        [
            "countryNameEn" => "Wallis and Futuna",
            "countryCode" => "WF",
            "region" => "Asia & Pacific",
            "flag" => "🇼🇫"
        ],
        [
            "countryNameEn" => "Samoa",
            "countryCode" => "WS",
            "region" => "Asia & Pacific",
            "flag" => "🇼🇸"
        ],
        [
            "countryNameEn" => "Yemen",
            "countryCode" => "YE",
            "region" => "Arab States",
            "flag" => "🇾🇪"
        ],
        [
            "countryNameEn" => "Mayotte",
            "countryCode" => "YT",
            "region" => "Africa",
            "flag" => "🇾🇹"
        ],
        [
            "countryNameEn" => "South Africa",
            "countryCode" => "ZA",
            "region" => "Africa",
            "flag" => "🇿🇦"
        ],
        [
            "countryNameEn" => "Zambia",
            "countryCode" => "ZM",
            "region" => "Africa",
            "flag" => "🇿🇲"
        ],
        [
            "countryNameEn" => "Zimbabwe",
            "countryCode" => "ZW",
            "region" => "Africa",
            "flag" => "🇿🇼"
        ],
        [
            "countryNameEn" => "Eswatini",
            "countryCode" => "SZ",
            "region" => "Africa",
            "flag" => "🇸🇿"
        ],
        [
            "countryNameEn" => "North Macedonia",
            "countryCode" => "MK",
            "region" => "Europe",
            "flag" => "🇲🇰"
        ],
        [
            "countryNameEn" => "Philippines",
            "countryCode" => "PH",
            "region" => "Asia & Pacific",
            "flag" => "🇵🇭"
        ],
        [
            "countryNameEn" => "Netherlands",
            "countryCode" => "NL",
            "region" => "Europe",
            "flag" => "🇳🇱"
        ],
        [
            "countryNameEn" => "United Arab Emirates",
            "countryCode" => "AE",
            "region" => "Arab States",
            "flag" => "🇦🇪"
        ],
        [
            "countryNameEn" => "Republic of Moldova",
            "countryCode" => "MD",
            "region" => "Europe",
            "flag" => "🇲🇩"
        ],
        [
            "countryNameEn" => "Gambia",
            "countryCode" => "GM",
            "region" => "Africa",
            "flag" => "🇬🇲"
        ],
        [
            "countryNameEn" => "Dominican Republic",
            "countryCode" => "DO",
            "region" => "South/Latin America",
            "flag" => "🇩🇴"
        ],
        [
            "countryNameEn" => "Sudan",
            "countryCode" => "SD",
            "region" => "Arab States",
            "flag" => "🇸🇩"
        ],
        [
            "countryNameEn" => "Lao People's Democratic Republic",
            "countryCode" => "LA",
            "region" => "Asia & Pacific",
            "flag" => "🇱🇦"
        ],
        [
            "countryNameEn" => "Taiwan, Province of China",
            "countryCode" => "TW",
            "region" => "Asia & Pacific",
            "flag" => "🇹🇼"
        ],
        [
            "countryNameEn" => "Republic of the Congo",
            "countryCode" => "CG",
            "flag" => "🇨🇬"
        ],
        [
            "countryNameEn" => "Czechia",
            "countryCode" => "CZ",
            "region" => "Europe",
            "flag" => "🇨🇿"
        ],
        [
            "countryNameEn" => "United Kingdom",
            "countryCode" => "GB",
            "region" => "Europe",
            "flag" => "🇬🇧"
        ],
        [
            "countryNameEn" => "Niger",
            "countryCode" => "NE",
            "region" => "Africa",
            "flag" => "🇳🇪"
        ],
        [
            "countryNameEn" => "Democratic Republic of the Congo",
            "countryCode" => "CD",
            "region" => "Africa",
            "flag" => "🇨🇩",
        ],
        [
            "countryNameEn" => "Commonwealth of The Bahamas",
            "countryCode" => "BS",
            "region" => "Caribbean",
            "flag" => "🇧🇸",
        ],
        [
            "countryNameEn" => "Cocos (Keeling) Islands",
            "countryCode" => "CC",
            "region" => "Australia",
            "flag" => "🇨🇨",
        ],
        [
            "countryNameEn" => "Central African Republic",
            "countryCode" => "CF",
            "region" => "Africa",
            "flag" => "🇨🇫",
        ],
        [
            "countryNameEn" => "Cook Islands",
            "countryCode" => "CK",
            "region" => "South Pacific Ocean",
            "flag" => "🇨🇰",
        ],
        [
            "countryNameEn" => "Falkland Islands",
            "countryCode" => "FK",
            "region" => "South Atlantic Ocean",
            "flag" => "🇫🇰",
        ],
        [
            "countryNameEn" => "Faroe Islands",
            "countryCode" => "FO",
            "region" => "Europe",
            "flag" => "🇫🇴",
        ],
        [
            "countryNameEn" => "Territory of Heard Island and McDonald Islands",
            "countryCode" => "HM",
            "region" => "Indian Ocean",
            "flag" => "🇭🇲",
        ],
        [
            "countryNameEn" => "British Indian Ocean Territory",
            "countryCode" => "IO",
            "region" => "Indian Ocean",
            "flag" => "🇮🇴",
        ],
        [
            "countryNameEn" => "Comoros",
            "countryCode" => "KM",
            "region" => "Indian Ocean",
            "flag" => "🇰🇲",
        ],
        [
            "countryNameEn" => "Cayman Islands",
            "countryCode" => "KY",
            "region" => "Caribbean Sea",
            "flag" => "🇰🇾",
        ],
        [
            "countryNameEn" => "Republic of the Marshall Islands",
            "countryCode" => "MH",
            "region" => "Pacific Ocean",
            "flag" => "🇲🇭",
        ],
        [
            "countryNameEn" => "Commonwealth of the Northern Mariana Islands",
            "countryCode" => "MP",
            "region" => "Pacific Ocean",
            "flag" => "🇲🇵",
        ],
        [
            "countryNameEn" => "Turks and Caicos Islands",
            "countryCode" => "TC",
            "region" => "Atlantic Ocean",
            "flag" => "🇹🇨",
        ],
        [
            "countryNameEn" => "French Southern and Antarctic Lands",
            "countryCode" => "TF",
            "region" => "Indian Ocean",
            "flag" => "🇹🇫",
        ],
        [
            "countryNameEn" => "United States Minor Outlying Islands",
            "countryCode" => "UM",
            "region" => "Pacific Ocean",
            "flag" => "🇺🇲",
        ],
        [
            "countryNameEn" => "Holy See",
            "countryCode" => "VA",
            "region" => "Europe",
            "flag" => "🇻🇦",
        ],
        [
            "countryNameEn" => "Republic of Kosovo",
            "countryCode" => "XK",
            "region" => "Europe",
            "flag" => "🇽🇰",
        ],
        [
            "countryNameEn" => "Netherlands Antilles",
            "countryCode" => "AN",
            "region" => "Europe",
            "flag" => "🇧🇶",
        ],
    ];

    return $country;
}

function getSmsSettings($type = null, $sms_setting_key = null)
{
    if ($type !== null) {
        $sms_setting = SMSSetting::where('status', 1)->where('type', $type)->first();

        if ($sms_setting) {
            return $sms_setting_key === 'get' ? $sms_setting->values : $sms_setting;
        }

        return false;
    }

    $all_sms_settings = SMSSetting::where('status', 1)->get();

    if ($all_sms_settings->isEmpty()) {
        return false;
    }

    return $all_sms_settings->pluck('values', 'type')->toArray();
}

function SMSData($type, $sms_setting_key = null, $message = null, $data = [])
{
    $sms_setting = SMSSetting::where('type', $type)->first();

    if ($sms_setting) {
        $status = $data['status'] ?? null;

        // Default case: Status ke basis pe SMS template find karo
        $sms_templates = collect();

        if ($status === 'create') {
            $sms_templates = SMSTemplate::whereIn('order_status', ['create', 'create_receiver'])->get();
        } elseif ($status === 'courier_picked_up') {
            $sms_templates = SMSTemplate::whereIn('order_status', ['courier_picked_up', 'courier_picked_up_delivery_code'])->get();
        } else {
            $sms_templates = SMSTemplate::where('order_status', $status)->get();
        }
        if ($sms_templates->isEmpty()) {
            return false;
        }

        $messages = [];

        // Agar status "create" hai to dono templates ko check karo
        if ($status === 'create' && $sms_templates->count() > 0) {
            foreach ($sms_templates as $template) {
                if ($template->order_status === 'create' && $template->type === 'order_confirmation') {
                    $messages['order_confirmation'] = replacePlaceholders(strip_tags($template->sms_description), $data);
                }
                if ($template->order_status === 'create_receiver' && $template->type === 'you_have_parcel') {
                    $messages['you_have_parcel'] = replacePlaceholders(strip_tags($template->sms_description), $data);
                }
            }
        } elseif ($status === 'courier_picked_up' && $sms_templates->count() > 0) {
            foreach ($sms_templates as $template) {
                if ($template->order_status === 'courier_picked_up' && $template->type === 'pickup_verification_code') {
                    $messages['pickup_verification_code'] = replacePlaceholders(strip_tags($template->sms_description), $data);
                }
                if ($template->order_status === 'courier_picked_up_delivery_code' && $template->type === 'delivery_verification_code') {
                    $messages['delivery_verification_code'] = replacePlaceholders(strip_tags($template->sms_description), $data);
                }
            }
        } else {
            // Normal case ke liye ek hi message return karna hai
            $sms_template = $sms_templates->first();
            $messages = strip_tags($sms_template ? $sms_template->sms_description : $sms_setting->sms_description);
        }

        // Agar "get" key pass kiya hai, to placeholders replace karo
        if ($sms_setting_key === 'get') {
            return replacePlaceholders($messages, $data);
        }
    }

    return false;
}


/**
 * Function to replace placeholders with actual values in SMS templates.
 */
function replacePlaceholders($message, $data)
{
    foreach ($data as $key => $value) {
        $message = str_replace("[$key]", $value, $message);
    }
    return $message;
}
function otpCode($length = 7)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($characters), 0, $length);
}
function shortenWithTinyURL($longUrl)
{
    $response = Http::get("https://tinyurl.com/api-create.php", [
        'url' => $longUrl
    ]);
    if ($response->successful()) {
        return $response->body();
    } else {
        Log::error("TinyURL API failed: " . $response->status());
    }
    return $longUrl;
}

