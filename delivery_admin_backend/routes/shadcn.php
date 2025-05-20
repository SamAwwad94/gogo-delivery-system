<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShadcnDemoController;

/*
|--------------------------------------------------------------------------
| ShadCN UI Demo Routes
|--------------------------------------------------------------------------
|
| These routes are for demonstrating the ShadCN UI components
|
*/

Route::group(['middleware' => ['auth', 'verified', 'assign_user_role']], function () {
    Route::get('shadcn-orders', [ShadcnDemoController::class, 'orders'])->name('shadcn.orders');
    Route::get('shadcn-delivery-routes', function () {
        return redirect()->route('delivery-routes.index');
    })->name('shadcn.orders.new');
    Route::get('shadcn-test', function () {
        return view('shadcn.test', [
            'pageTitle' => 'ShadCN Test',
            'assets' => ['datatable']
        ]);
    })->name('shadcn.test');

    // ShadCN Dashboard
    Route::get('shadcn-dashboard', function () {
        // Get the same data as the regular admin dashboard
        $controller = app()->make('App\Http\Controllers\HomeController');
        $request = request();
        $response = $controller->index($request);

        // Extract the data from the response
        $data = $response->getData();

        // Return the ShadCN-styled dashboard with the same data
        return view('dashboards.admin-dashboard-shadcn', $data);
    })->name('shadcn.dashboard');

    // ShadCN Dashboard with Enhanced Filters
    Route::get('shadcn-dashboard-filters', function () {
        // Get the same data as the regular admin dashboard
        $controller = app()->make('App\Http\Controllers\HomeController');
        $request = request();
        $response = $controller->index($request);

        // Extract the data from the response
        $data = $response->getData();

        // Return the ShadCN-styled dashboard with enhanced filters
        return view('dashboards.admin-dashboard-shadcn-filters', $data);
    })->name('shadcn.dashboard.filters');

    // ShadCN Table Example
    Route::get('shadcn-table-example', function () {
        return view('examples.shadcn-table-example');
    })->name('shadcn.table.example');

    // ShadCN Orders
    Route::get('shadcn-orders-table', function () {
        $controller = app()->make('App\Http\Controllers\OrderController');
        return $controller->shadcnIndex();
    })->name('shadcn.orders.table');

    // ShadCN Users
    Route::get('shadcn-users-table', function () {
        return redirect()->route('users.index');
    })->name('shadcn.users.table');
});
