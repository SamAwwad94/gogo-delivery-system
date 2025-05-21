<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AppSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = ['site_name', 'site_email', 'site_description', 'site_copyright', 'facebook_url', 'twitter_url', 'linkedin_url', 'instagram_url', 'support_email', 'support_number', 'notification_settings', 'auto_assign', 'distance_unit', 'distance', 'otp_verify_on_pickup_delivery', 'currency', 'currency_code', 'currency_position', 'is_vehicle_in_order', 'language_option', 'color', 'prefix', 'backup_type', 'backup_email', 'is_bidding_in_order', 'is_sms_order'];

    public $timestamps = false;

    protected static function getData()
    {
        $data = self::get()->first();

        if ($data == null) {
            $data = new self;
        }

        $data->site_logo = getSingleMedia($data, 'site_logo');
        $data->site_favicon = getSingleMedia($data, 'site_favicon');

        return $data;
    }

    public function setLanguageOptionAttribute($value)
    {
        $this->attributes['language_option'] = isset($value) ? json_encode($value) : null;
    }

    public function getLanguageOptionAttribute($value)
    {
        // Always return only English and Arabic
        return ['en', 'ar'];
    }

    public function getCurrencyCodeAttribute($value)
    {
        // Always return USD as default if not set
        return $value ?? 'USD';
    }

    public function getCurrencyAttribute($value)
    {
        // Always return $ as default if not set
        return $value ?? '$';
    }

    protected $casts = [
        'auto_assign' => 'integer',
        'otp_verify_on_pickup_delivery' => 'integer',
        'distance' => 'double',
        'is_vehicle_in_order' => 'integer',
    ];
    public function getNotificationSettingsAttribute($value)
    {
        return (!empty($value) && is_string($value)) ? json_decode($value, true) : [];
    }

    public function setNotificationSettingsAttribute($value)
    {
        $this->attributes['notification_settings'] = isset($value) ? json_encode($value) : null;
    }
}
