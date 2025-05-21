<?php

namespace App\Helpers;

use App\Models\Setting;

class LogoHelper
{
    public static function getLogo($type = 'site_logo')
    {
        $path = Setting::where('key', $type)->value('value');
        $timestamp = time() . rand(1000, 9999); // More unique cache busting

        if ($path && file_exists(public_path($path))) {
            return asset($path) . '?v=' . $timestamp;
        }

        // Default fallbacks
        switch ($type) {
            case 'site_logo':
                return asset('images/logos/default-logo.png') . '?v=' . $timestamp;
            case 'site_dark_logo':
                return asset('images/logos/dark_logo.png') . '?v=' . $timestamp;
            case 'site_favicon':
                return asset('images/logos/site_favicon.png') . '?v=' . $timestamp;
            default:
                return asset('images/logos/default-logo.png') . '?v=' . $timestamp;
        }
    }
}