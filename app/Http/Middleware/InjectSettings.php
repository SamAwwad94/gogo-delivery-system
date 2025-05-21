<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use App\Models\Setting;

class InjectSettings
{
    public function handle($request, Closure $next)
    {
        $appSettings = AppSetting::all();
        $themeColor = $appSettings->isNotEmpty() ? $appSettings->pluck('color')[0] : '#2563eb';
        view()->share('themeColor', $themeColor);

        // Share app_settings with all views
        $app_settings = AppSetting::first();
        if (!$app_settings) {
            $app_settings = new AppSetting();
            $app_settings->site_name = config('app.name', 'Gogo Delivery');
            $app_settings->site_email = config('mail.from.address', 'info@gogodelivery.com');
            $app_settings->site_description = 'Delivery Service';
            $app_settings->site_copyright = '© ' . date('Y') . ' Gogo Delivery';
        }
        view()->share('app_settings', $app_settings);

        // Add dummy data for frontend
        $dummy_data = 'Lorem ipsum dolor sit amet';
        view()->share('dummy_data', $dummy_data);

        // Share pages with all views
        $pages = \App\Models\Pages::where('status', 1)->get();
        view()->share('pages', $pages);

        return $next($request);
    }
}
