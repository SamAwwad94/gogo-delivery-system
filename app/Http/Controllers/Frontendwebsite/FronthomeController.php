<?php

namespace App\Http\Controllers\Frontendwebsite;

use App\Http\Controllers\Controller;
use App\Models\FrontendData;
use App\Models\Order;
use App\Models\Pages;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FronthomeController extends Controller
{
    public function index()
    {
        $data['dummy_title'] = DummyData('dummy_title');
        $data['dummy_description'] = DummyData('dummy_description');

        $data['app_content'] = [
            'app_name' => SettingData('app_content', 'app_name') ?? $data['dummy_title'],
            'create_order_description' => SettingData('app_content', 'create_order_description') ?? $data['dummy_description'],
            'delivery_man_image' => getSingleMediaSettingImage(getSettingFirstData('app_content', 'delivery_man_image'), 'delivery_man_image'),
            'delivery_road_image' => getSingleMediaSettingImage(getSettingFirstData('app_content', 'delivery_road_image'), 'delivery_road_image'),
        ];

        $data['why_choose'] = [
            'title' => SettingData('why_choose', 'title') ?? $data['dummy_title'],
            'description' => SettingData('why_choose', 'description') ?? $data['dummy_description'],
        ];

        $data['client_review'] = [
            'client_review_title' => SettingData('client_review', 'client_review_title') ?? $data['dummy_title'],
        ];

        $data['download_app'] = [
            'download_title' => SettingData('download_app', 'download_title') ?? $data['dummy_title'],
            'download_description' => SettingData('download_app', 'download_description') ?? $data['dummy_description'],
            'download_footer_content' => SettingData('download_app', 'download_footer_content') ?? $data['dummy_title'],
            'download_app_logo' => getSingleMediaSettingImage(getSettingFirstData('download_app', 'download_app_logo'), 'download_app_logo'),
            'play_store_link' => [
                'url' => SettingData('app_content', 'play_store_link') ?? 'javascript:void(0)',
                'target' => SettingData('app_content', 'play_store_link') ? 'target="_blank"' : ''
            ],
            'app_store_link' => [
                'url' => SettingData('app_content', 'app_store_link') ?? 'javascript:void(0)',
                'target' => SettingData('app_content', 'app_store_link') ? 'target="_blank"' : ''
            ],
        ];

        $why_delivery = FrontendData::where('type', 'why_choose')->get();
        if (count($why_delivery) <= 0)
            $why_delivery[] = (object) [
                'id' => null,
                'title' => $data['dummy_title'],
                "subtitle" => $data['dummy_title'],
            ];

        $client_review = FrontendData::where('type', 'client_review')->get();
        if (count($client_review) <= 0)
            $client_review[] = (object) [
                'id' => null,
                'title' => $data['dummy_title'],
                "subtitle" => $data['dummy_title'],
                "description" => $data['dummy_description'],
            ];

        return view('frontend-website.index', compact('why_delivery', 'client_review', 'data'));
    }

    public function deliverypartner()
    {

        $data['delivery_partner'] = [
            'title' => SettingData('delivery_partner', 'title') ?? DummyData('dummy_title'),
            'subtitle' => SettingData('delivery_partner', 'subtitle') ?? DummyData('dummy_title'),
            'delivery_partner_image' => getSingleMediaSettingImage(getSettingFirstData('delivery_partner', 'delivery_partner_image'), 'delivery_partner_image'),
            'play_store_link' => [
                'url' => SettingData('app_content', 'play_store_link') ?? 'javascript:void(0)',
                'target' => SettingData('app_content', 'play_store_link') ? 'target="_blank"' : ''
            ],
        ];

        $delivery_data = FrontendData::where('type', 'partner_benefits')->get();
        return view('frontend-website.delivery_partner', compact('delivery_data', 'data'));
    }

    public function contactus()
    {
        $data['contact_us'] = [
            'contact_title' => SettingData('contact_us', 'contact_title') ?? DummyData('dummy_title'),
            'contact_subtitle' => SettingData('contact_us', 'contact_subtitle') ?? DummyData('dummy_title'),
            'contact_us_app_ss' => getSingleMediaSettingImage(getSettingFirstData('contact_us', 'contact_us_app_ss'), 'contact_us_app_ss'),
            'play_store_link' => [
                'url' => SettingData('app_content', 'play_store_link') ?? 'javascript:void(0)',
                'target' => SettingData('app_content', 'play_store_link') ? 'target="_blank"' : ''
            ],
            'app_store_link' => [
                'url' => SettingData('app_content', 'app_store_link') ?? 'javascript:void(0)',
                'target' => SettingData('app_content', 'app_store_link') ? 'target="_blank"' : ''
            ],
        ];

        $data['app_setting_data'] = appSettingData('set');
        $data['dummy_title'] = DummyData('dummy_title');

        return view('frontend-website.contactus', compact('data'));
    }

    public function about_us()
    {
        $data['about_us'] = [
            'download_title' => SettingData('about_us', 'download_title') ?? DummyData('dummy_title'),
            'download_subtitle' => SettingData('about_us', 'download_subtitle') ?? DummyData('dummy_title'),
            'long_des' => SettingData('about_us', 'long_des') ?? DummyData('dummy_description'),
            'about_us_app_ss' => getSingleMediaSettingImage(getSettingFirstData('about_us', 'about_us_app_ss'), 'about_us_app_ss'),
            'play_store_link' => [
                'url' => SettingData('app_content', 'play_store_link') ?? 'javascript:void(0)',
                'target' => SettingData('app_content', 'play_store_link') ? 'target="_blank"' : ''
            ],
            'app_store_link' => [
                'url' => SettingData('app_content', 'app_store_link') ?? 'javascript:void(0)',
                'target' => SettingData('app_content', 'app_store_link') ? 'target="_blank"' : ''
            ],
        ];

        return view('frontend-website.aboutus', compact('data'));
    }

    public function ordertracking()
    {
        $data['track_order'] = [
            'track_order_title' => SettingData('track_order', 'track_order_title') ?? DummyData('dummy_title'),
            'track_order_subtitle' => SettingData('track_order', 'track_order_subtitle') ?? DummyData('dummy_title'),
            'track_page_title' => SettingData('track_order', 'track_page_title') ?? DummyData('dummy_title'),
            'track_page_description' => SettingData('track_order', 'track_page_description') ?? DummyData('dummy_description'),
        ];

        $data['order_status'] = [
            'order_status_title' => FrontendData::where('type', 'order_status')->first()->title ?? null,
            'order_status_section' => FrontendData::where('type', 'order_status_section')->get(),
        ];

        return view('frontend-website.ordertracking', compact('data'));
    }

    public function orderhistory(Request $request)
    {
        $order = $request->milisecond;

        $orderId = order::where('milisecond', $order)->first();

        if ($orderId === null) {
            $notification = array(
                'message' => __('message.not_found_entry', ['name' => __('message.order')]),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
        $data = order::withTrashed()->find($orderId->id);
        if ($data == null) {
            $notification = array(
                'message' => __('message.not_found_entry', ['name' => __('message.order')]),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
        return view('frontend-website.orderhistory', compact('data'));

    }
    public function smsorderhistory(Request $request, $order_id)
    {
        $order = Order::where('milisecond', $order_id)->first();

        if (!$order) {
            return redirect()->back()->with([
                'message' => __('message.not_found_entry', ['name' => __('message.order')]),
                'alert-type' => 'error'
            ]);
        }

        $data = Order::withTrashed()->find($order->id);

        if (!$data) {
            return redirect()->back()->with([
                'message' => __('message.not_found_entry', ['name' => __('message.order')]),
                'alert-type' => 'error'
            ]);
        }

        return view('frontend-website.sms_orderhistory', compact('data'));
    }

    public function emailOrder($id)
    {
        $orderId = order::where('id', $id)->first();

        if ($orderId === null) {
            $notification = array(
                'message' => __('message.not_found_entry', ['name' => __('message.order')]),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
        $data = order::withTrashed()->find($orderId->id);
        if ($data == null) {
            $notification = array(
                'message' => __('message.not_found_entry', ['name' => __('message.order')]),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
        return view('frontend-website.orderhistory', compact('data'));
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->back();
        }
        $walkthrough_data = FrontendData::where('type', 'walkthrough')->get();
        return view('frontend-website.admin.login', compact('walkthrough_data'));
    }

    public function privacypolicy()
    {
        return view('frontend-website.privacy_policy');
    }

    public function termofservice()
    {
        return view('frontend-website.termofservice');
    }

    public function page($slug)
    {
        $page = Pages::where('slug', $slug)->firstOrFail();
        return view('frontend-website.pages', compact('page'));
    }

    public function websiteSettingForm($type)
    {
        $data = config('constant.' . $type);
        $pageTitle = __('message.' . $type);

        foreach ($data as $key => $val) {
            if (in_array($key, ['delivery_man_image', 'delivery_road_image', 'app_logo_image', 'download_app_logo', 'delivery_partner_image', 'contact_us_app_ss', 'about_us_app_ss', 'company_logo'])) {
                $data[$key] = Setting::where('type', $type)->where('key', $key)->first();
            } else {
                $data[$key] = Setting::where('type', $type)->where('key', $key)->pluck('value')->first();
            }
        }

        return view('websitesection.form', compact('data', 'pageTitle', 'type'));
    }

    public function websiteSettingUpdate(Request $request, $type)
    {
        $data = $request->all();
        foreach (config('constant.' . $type) as $key => $val) {
            $input = [
                'type' => $type,
                'key' => $key,
                'value' => $data[$key] ?? null,
            ];
            $result = Setting::updateOrCreate(['key' => $key, 'type' => $type], $input);
            if (env('APP_DEMO')) {
                $message = __('message.demo_permission_denied');
                if (request()->is('api/*')) {
                    return response()->json(['status' => true, 'message' => $message]);
                }
                if (request()->ajax()) {
                    return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                }
                return redirect()->back()->withErrors($message);

            } else {
                if ($key === 'company_logo' && $request->hasFile($key)) {
                    // Direct file storage for company_logo
                    $file = $request->file($key);

                    // Generate filename with timestamp
                    $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filepath = 'images/invoice/' . $filename;

                    // Ensure directory exists
                    if (!file_exists(public_path('images/invoice'))) {
                        mkdir(public_path('images/invoice'), 0755, true);
                    }

                    // Move file to public directory
                    $file->move(public_path('images/invoice'), $filename);

                    // Update the setting value with the file path
                    $result->update(['value' => $filepath]);
                } else if (in_array($key, ['delivery_man_image', 'delivery_road_image', 'app_logo_image', 'download_app_logo', 'delivery_partner_image', 'contact_us_app_ss', 'about_us_app_ss'])) {
                    uploadMediaFile($result, $request->$key, $key);
                    $uploadedPath = getSingleMedia($result, $key);
                    $result->update(['value' => $uploadedPath]);
                }
            }
        }

        if (isset($data['invoice_setting'])) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => __('message.updated')
                ]);
            }
            return redirect()->route('setting.index', ['page' => 'invoice-setting'])->withSuccess(__('message.' . $type));
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => __('message.save_form', ['form' => __('message.' . $type)])
            ]);
        }
        return redirect()->back()->withSuccess(__('message.save_form', ['form' => __('message.' . $type)]));
    }

    public function helpinfinformation()
    {

        $pageTitle = __('message.web_section_information');
        return view('websitesection.help', compact(['pageTitle']));
    }
    public function helpinfdownlaodapp()
    {

        $pageTitle = __('message.downloandapp');
        return view('websitesection.downloadhelp', compact(['pageTitle']));
    }
    public function helpcontact()
    {

        $pageTitle = __('message.contact_us');
        return view('websitesection.contacthelp', compact(['pageTitle']));
    }
    public function helpabout()
    {

        $pageTitle = __('message.about_us');
        return view('websitesection.abouthelp', compact(['pageTitle']));
    }

    public function orderStatus()
    {
        $pageTitle = __('message.order_status');
        $type = 'order_status_section';
        $id = request('id') ?? null;

        $data = FrontendData::where('type', 'order_status')->first();
        if (request()->ajax()) {
            $sub_type = 'order_status_section';
            $data = FrontendData::find($id);
            $pageTitle = __('message.add_form_title', ['form' => __('message.order_status_section')]);
            return view('websitesection.order_status.section_form', compact('pageTitle', 'data', 'id', 'sub_type'))->render();
        }

        return view('websitesection.order_status.form', compact(['pageTitle', 'type', 'data']));
    }

    public function getFrontendOrderStatusList(Request $request)
    {
        $type = request('type');
        $data = FrontendData::where('type', $type)->orderBy('id', 'desc')->get();
        $title = str_replace('-', '_', $type);
        $count_data = FrontendData::where('type', $type)->orderBy('id', 'desc')->count();
        $view = view('websitesection.order_status.section_list', compact('type', 'data', 'title'))->render();
        return response()->json(['data' => $view, 'status' => true, 'count_data' => $count_data, 'type' => $type]);
    }

    public function storeFrontendOrderStatusData(Request $request)
    {
        $data = $request->all();
        $id = request('id');
        $result = FrontendData::updateOrCreate(['id' => $id], $data);

        uploadMediaFile($result, $request->order_status_section_image, $request->type);
        $title = str_replace('-', '_', $request->type);
        $count_data = FrontendData::where('type', $result->type)->orderBy('id', 'desc')->count();
        $message = __('message.save_form', ['form' => __('message.' . $title)]);
        if (request()->ajax()) {
            return response()->json(['status' => true, 'count_data' => $count_data, 'frontend_id' => $id, 'type' => $request->type, 'event' => 'norefresh', 'message' => $message]);
        }

        return redirect()->back()->withSuccess(__('message.save_form', ['form' => __('message.order_status')]));

    }

    public function frontendOrderStatusDataDestroy(Request $request)
    {
        $frontend_data = FrontendData::find($request->id);

        $title = str_replace('-', '_', $frontend_data->type);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.' . $title)]);
        if ($frontend_data != '') {
            $frontend_data->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.' . $title)]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'type' => 'order_status_section', 'event' => 'norefresh']);
        }

        return redirect()->back()->with($status, $message);
    }
}
