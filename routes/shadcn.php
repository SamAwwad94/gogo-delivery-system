<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShadcnDemoController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| ShadCN UI Routes
|--------------------------------------------------------------------------
|
| These routes are for the ShadCN UI components and pages
|
*/

Route::group(['middleware' => ['auth', 'verified', 'assign_user_role']], function () {
    // Demo routes
    Route::get('shadcn-orders', [ShadcnDemoController::class, 'orders'])->name('shadcn.orders');
    Route::get('shadcn-test', function () {
        return view('shadcn.test', [
            'pageTitle' => 'ShadCN Test',
            'assets' => ['datatable']
        ]);
    })->name('shadcn.test');

    // New Orders ShadCN
    Route::get('new-orders-shadcn', function () {
        return view('order.new-orders-shadcn', [
            'pageTitle' => 'New Orders ShadCN',
            'assets' => ['datatable']
        ]);
    })->name('shadcn.new-orders');

    // ShadCN Dashboard
    Route::get('shadcn-dashboard', function () {
        // Get the same data as the regular admin dashboard
        $controller = app()->make(HomeController::class);
        $request = request();
        $response = $controller->index($request);

        // Extract the data from the response
        $data = $response->getData();

        // Return the ShadCN-styled dashboard with the same data
        return view('dashboards.admin-dashboard-shadcn', $data);
    })->name('shadcn.dashboard');

    // ShadCN Components
    Route::prefix('shadcn')->name('shadcn.')->group(function () {
        Route::get('components', function () {
            return view('shadcn.components', [
                'pageTitle' => 'ShadCN Components',
                'assets' => []
            ]);
        })->name('components');

        Route::get('tables', function () {
            return view('shadcn.tables', [
                'pageTitle' => 'ShadCN Tables',
                'assets' => ['datatable']
            ]);
        })->name('tables');

        Route::get('forms', function () {
            return view('shadcn.forms', [
                'pageTitle' => 'ShadCN Forms',
                'assets' => ['select2']
            ]);
        })->name('forms');

        Route::get('charts', function () {
            return view('shadcn.charts', [
                'pageTitle' => 'ShadCN Charts',
                'assets' => ['chart', 'apexchart']
            ]);
        })->name('charts');

        Route::get('maps', function () {
            return view('shadcn.maps', [
                'pageTitle' => 'ShadCN Maps',
                'assets' => ['leaflet']
            ]);
        })->name('maps');
    });
});
