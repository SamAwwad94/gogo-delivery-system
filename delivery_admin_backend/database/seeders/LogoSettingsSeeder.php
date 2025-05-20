<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class LogoSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'type' => 'general',
                'key' => 'site_logo',
                'value' => 'images/logos/default-logo.png'
            ],
            [
                'type' => 'general',
                'key' => 'site_dark_logo',
                'value' => 'images/logos/dark_logo.png'
            ],
            [
                'type' => 'general',
                'key' => 'site_favicon',
                'value' => 'images/logos/site_favicon.png'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'type' => $setting['type']],
                $setting
            );
        }
    }
}