<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\RepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Repositories\CityRepository;
use App\Repositories\CountryRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\WalletRepository;
use App\Models\Order;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\Wallet;
use App\Services\Orders\OrderService;
use App\Services\UserService;
use App\Services\CityService;
use App\Services\CountryService;
use App\Services\PaymentService;
use App\Services\VehicleService;
use App\Services\WalletService;
use App\Services\CacheService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the base repository
        $this->app->bind(RepositoryInterface::class, BaseRepository::class);

        // Bind repositories
        $this->app->bind(OrderRepository::class, function ($app) {
            return new OrderRepository(new Order());
        });

        $this->app->bind(UserRepository::class, function ($app) {
            return new UserRepository(new User());
        });

        $this->app->bind(CityRepository::class, function ($app) {
            return new CityRepository(new City());
        });

        $this->app->bind(CountryRepository::class, function ($app) {
            return new CountryRepository(new Country());
        });

        $this->app->bind(PaymentRepository::class, function ($app) {
            return new PaymentRepository(new Payment());
        });

        $this->app->bind(VehicleRepository::class, function ($app) {
            return new VehicleRepository(new Vehicle());
        });

        $this->app->bind(WalletRepository::class, function ($app) {
            return new WalletRepository(new Wallet());
        });

        // Bind CacheService
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        // Bind services
        $this->app->bind(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(OrderRepository::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepository::class)
            );
        });

        $this->app->bind(CityService::class, function ($app) {
            return new CityService(
                $app->make(CityRepository::class)
            );
        });

        $this->app->bind(CountryService::class, function ($app) {
            return new CountryService(
                $app->make(CountryRepository::class),
                $app->make(\App\Services\CacheService::class)
            );
        });

        $this->app->bind(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(PaymentRepository::class)
            );
        });

        $this->app->bind(VehicleService::class, function ($app) {
            return new VehicleService(
                $app->make(VehicleRepository::class)
            );
        });

        $this->app->bind(WalletService::class, function ($app) {
            return new WalletService(
                $app->make(WalletRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
