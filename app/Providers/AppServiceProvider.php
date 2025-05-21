<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register LoggingService
        $this->app->singleton('App\Services\LoggingService', function ($app) {
            return new \App\Services\LoggingService();
        });

        /*Event::listen(MigrationsStarted::class, function (){
            DB::statement('SET SESSION sql_require_primary_key=0');
        });

        Event::listen(MigrationsEnded::class, function (){
            DB::statement('SET SESSION sql_require_primary_key=1');
        });*/
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Share app_settings with all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $app_settings = \App\Models\AppSetting::first();

            // If no settings exist, create a default object
            if (!$app_settings) {
                $app_settings = new \App\Models\AppSetting();
                $app_settings->site_name = config('app.name', 'Gogo Delivery');
                $app_settings->site_email = config('mail.from.address', 'info@gogodelivery.com');
                $app_settings->site_description = 'Delivery Service';
                $app_settings->site_copyright = '© ' . date('Y') . ' Gogo Delivery';
            }

            // Add dummy data for frontend
            $dummy_data = 'Lorem ipsum dolor sit amet';
            $pages = \App\Models\Pages::where('status', 1)->get();

            $view->with([
                'app_settings' => $app_settings,
                'dummy_data' => $dummy_data,
                'pages' => $pages
            ]);
        });
    }
}
