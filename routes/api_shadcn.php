<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for ShadCN Tables
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for ShadCN tables.
|
*/

// Delivery Routes API
Route::get('delivery-routes', [App\Http\Controllers\Api\DeliveryRouteApiController::class, 'getDeliveryRoutes']);
Route::get('delivery-men', [App\Http\Controllers\Api\DeliveryRouteApiController::class, 'getDeliveryMen']);
Route::get('delivery-man-location/{id}', [App\Http\Controllers\Api\DeliveryRouteApiController::class, 'getDeliveryManLocation']);

// Orders API
Route::get('orders', [App\Http\Controllers\Api\OrderApiController::class, 'getOrders']);
Route::get('clients', [App\Http\Controllers\Api\OrderApiController::class, 'getClients']);

// Users API
Route::get('users', [App\Http\Controllers\Api\UserApiController::class, 'getUsers']);
