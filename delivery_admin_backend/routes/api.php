<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\API;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Order inline editing
Route::middleware('auth:api')->patch('/orders/{id}/inline-edit', [App\Http\Controllers\OrderController::class, 'inlineEdit']);

// Order statistics
Route::middleware('auth:api')->get('/orders/statistics', [App\Http\Controllers\OrderController::class, 'getStatistics']);

// Order updates (for polling fallback)
Route::middleware('auth:api')->get('/orders/updates', [App\Http\Controllers\OrderController::class, 'getUpdates']);
Route::post('save-external-order', [Api\RestApiController::class, 'store']);
Route::get('get-code', [API\RestApiController::class, 'getCount']);
Route::post('new-register', [API\UserController::class, 'newregister']);
Route::post('driver-register', [API\UserController::class, 'driverRegister']);
Route::post('new-login', [API\UserController::class, 'newlogin']);
Route::post('new-forget-password', [API\UserController::class, 'newforgetPassword']);
Route::post('new-socialLogin', [API\UserController::class, 'newsocialLogin']);
Route::get('user-list', [API\UserController::class, 'userList']);
Route::get('user-detail', [API\UserController::class, 'userDetail']);

Route::get('appsetting', [API\DashboardController::class, 'appsetting']);
Route::get('language-table-list', [API\LanguageTableController::class, 'getList']);

// ShadCN Orders API - Public routes
Route::get('orders', [App\Http\Controllers\Api\OrderApiController::class, 'getOrders']);
Route::get('clients', [App\Http\Controllers\Api\OrderApiController::class, 'getClients']);
Route::get('delivery-men', [App\Http\Controllers\Api\DeliveryRouteApiController::class, 'getDeliveryMen']);

Route::group(['middleware' => ['auth:sanctum', 'assign_user_role']], function () {

    Route::get('dashboard-detail', [API\UserController::class, 'dashboard']);
    Route::get('dashboard-chartdata', [API\UserController::class, 'dashboardChartData']);

    Route::post('notification-list', [API\NotificationController::class, 'getList']);

    Route::get('paymentgateway-list', [API\PaymentGatewayController::class, 'getList']);
    Route::post('paymentgateway-save', [App\Http\Controllers\PaymentGatewayController::class, 'store']);


    Route::post('update-profile', [API\UserController::class, 'updateProfile']);
    Route::post('change-password', [API\UserController::class, 'changePassword']);
    Route::post('update-user-status', [API\UserController::class, 'updateUserStatus']);

    Route::post('delete-user-account', [API\UserController::class, 'deleteUserAccount']);

    Route::get('logout', [API\UserController::class, 'logout']);
    Route::get('get-dashboard-list', [API\DashboardController::class, 'getdashboardlist']);

    Route::get('country-list', [API\CountryController::class, 'getList']);
    Route::get('country-detail', [API\CountryController::class, 'getDetail']);
    Route::post('country-save', [App\Http\Controllers\CountryController::class, 'store']);
    Route::post('country-update/{id}', [App\Http\Controllers\CountryController::class, 'update']);
    Route::post('country-delete/{id}', [App\Http\Controllers\CountryController::class, 'destroy']);
    Route::post('multiple-delete-country', [API\CountryController::class, 'multipleDeleteRecords']);
    Route::post('country-action', [App\Http\Controllers\CountryController::class, 'action']);

    Route::get('city-detail', [API\CityController::class, 'getDetail']);
    Route::get('city-list', [API\CityController::class, 'getList']);
    Route::post('city-save', [App\Http\Controllers\CityController::class, 'store']);
    Route::post('city-update/{id}', [App\Http\Controllers\CityController::class, 'update']);
    Route::post('city-delete/{id}', [App\Http\Controllers\CityController::class, 'destroy']);
    Route::post('multiple-delete-city', [API\CityController::class, 'multipleDeleteRecords']);
    Route::post('city-action', [App\Http\Controllers\CityController::class, 'action']);

    Route::get('vehicle-list', [API\VehicleController::class, 'getList']);
    Route::post('vehicle-save', [App\Http\Controllers\VehicleController::class, 'store']);
    Route::post('vehicle-update/{id}', [App\Http\Controllers\VehicleController::class, 'update']);
    Route::post('vehicle-delete/{id}', [App\Http\Controllers\VehicleController::class, 'destroy']);
    Route::post('multiple-delete-vehicle', [API\VehicleController::class, 'multipleDeleteRecords']);
    Route::post('vehicle-action', [App\Http\Controllers\VehicleController::class, 'action']);

    Route::get('extracharge-list', [API\ExtraChargeController::class, 'getList']);
    Route::post('extracharge-save', [App\Http\Controllers\ExtraChargeController::class, 'store']);
    Route::post('extracharge-update/{id}', [App\Http\Controllers\ExtraChargeController::class, 'update']);
    Route::post('extracharge-delete/{id}', [App\Http\Controllers\ExtraChargeController::class, 'destroy']);
    Route::post('extracharge-action', [App\Http\Controllers\ExtraChargeController::class, 'action']);

    Route::get('staticdata-list', [API\StaticDataController::class, 'getList']);
    Route::post('staticdata-save', [App\Http\Controllers\StaticDataController::class, 'store']);
    Route::post('staticdata-delete/{id}', [App\Http\Controllers\StaticDataController::class, 'destroy']);

    Route::get('get-setting', [API\SettingController::class, 'getList']);
    Route::post('save-setting', [App\Http\Controllers\SettingController::class, 'saveInvoiceSetting']);
    Route::post('setting-upload-invoice-image', [App\Http\Controllers\SettingController::class, 'settingUploadInvoiceImage']);

    Route::get('notification-detail', [API\NotificationController::class, 'getNotificationDetail']);

    Route::get('user-list', [API\UserController::class, 'userList']);
    Route::get('reference-list', [API\ReferenceController::class, 'getList']);
    Route::get('user-detail', [API\UserController::class, 'userDetail']);
    Route::get('user-profile-detail', [API\UserController::class, 'commonUserDetail']);
    Route::post('user-save', [App\Http\Controllers\ClientController::class, 'store']);
    Route::post('user-update/{id}', [App\Http\Controllers\ClientController::class, 'update']);
    Route::post('user-delete/{id}', [App\Http\Controllers\ClientController::class, 'destroy']);
    Route::post('delete-user', [API\UserController::class, 'deleteUser']);
    Route::post('update-appsetting', [App\Http\Controllers\SettingController::class, 'updateAppSetting']);
    Route::get('get-appsetting', [API\UserController::class, 'getAppSetting']);

    Route::post('multiple-delete-user', [API\UserController::class, 'multipleDeleteRecords']);
    Route::post('user-action', [App\Http\Controllers\ClientController::class, 'action']);
    Route::post('user-forceDelete', [App\Http\Controllers\ClientController::class, 'userdelete']);
    Route::get('user-filter-list', [API\UserController::class, 'userFilterList']);

    Route::get('wallet-detail', [API\WalletController::class, 'getWallatDetail']);
    Route::post('save-wallet', [API\WalletController::class, 'saveWallet']);
    Route::get('wallet-list', [API\WalletController::class, 'getList']);
    Route::get('reward-list', [API\WalletController::class, 'rewardhistory']);

    Route::get('document-list', [API\DocumentController::class, 'getList']);
    Route::post('document-save', [App\Http\Controllers\DocumentController::class, 'store']);
    Route::post('document-update/{id}', [App\Http\Controllers\DocumentController::class, 'update']);
    Route::post('document-delete/{id}', [App\Http\Controllers\DocumentController::class, 'destroy']);
    Route::post('document-action', [App\Http\Controllers\DocumentController::class, 'action']);

    Route::get('delivery-man-document-list', [API\DeliveryManDocumentController::class, 'getList']);
    Route::post('multiple-delete-deliveryman-document', [API\DeliveryManDocumentController::class, 'multipleDeleteRecords']);
    Route::post('delivery-man-document-save', [App\Http\Controllers\DeliveryManDocumentController::class, 'store']);
    Route::post('delivery-man-document-delete/{id}', [App\Http\Controllers\DeliveryManDocumentController::class, 'destroy']);
    Route::post('delivery-man-document-action', [App\Http\Controllers\DeliveryManDocumentController::class, 'action']);

    Route::get('order-list', [API\OrderController::class, 'getList']);
    Route::get('order-detail', [API\OrderController::class, 'getDetail']);
    Route::post('multiple-delete-order', [API\OrderController::class, 'multipleDeleteRecords']);
    Route::post('order-save', [App\Http\Controllers\OrderController::class, 'store']);
    Route::post('order-update/{id}', [App\Http\Controllers\OrderController::class, 'update']);
    Route::post('assign-order-update', [App\Http\Controllers\OrderController::class, 'assignOrder']);
    Route::post('order-delete/{id}', [App\Http\Controllers\OrderController::class, 'destroy']);
    Route::post('order-action', [App\Http\Controllers\OrderController::class, 'action']);
    Route::post('order-auto-assign', [App\Http\Controllers\OrderController::class, 'autoAssignCancelOrder']);
    Route::get('order-tracking', [API\OrderController::class, 'getOrderTrackingDetail']);
    Route::Post('calculatetotal-get', [API\OrderController::class, 'calculateTotal']);


    Route::get('useraddress-list', [API\UserAddressController::class, 'getList']);
    Route::get('useraddress-detail', [API\UserAddressController::class, 'getDetail']);
    Route::post('useraddress-save', [App\Http\Controllers\UserAddressController::class, 'store']);
    Route::post('useraddress-delete/{id}', [App\Http\Controllers\UserAddressController::class, 'destroy']);

    Route::get('place-autocomplete-api', [API\CommonController::class, 'placeAutoComplete']);
    Route::get('place-detail-api', [API\CommonController::class, 'placeDetail']);
    Route::get('distance-matrix-api', [API\CommonController::class, 'distanceMatrix']);

    Route::post('payment-save', [API\PaymentController::class, 'paymentSave']);
    Route::get('payment-list', [API\PaymentController::class, 'getList']);

    Route::get('deliveryman-earning-list', [API\PaymentController::class, 'getDeliveryManEarningList']);

    Route::get('withdrawrequest-list', [API\WithdrawRequestController::class, 'getList']);
    Route::post('save-withdrawrequest', [API\WithdrawRequestController::class, 'saveWithdrawrequest']);
    Route::post('approved-withdrawrequest', [API\WithdrawRequestController::class, 'approvedWithdrawRequest']);
    Route::post('decline-withdrawrequest', [API\WithdrawRequestController::class, 'declineWithdrawRequest']);

    Route::get('frontenddata-list', [API\FrontendDataController::class, 'getList']);
    Route::post('frontenddata-save', [App\Http\Controllers\FrontendDataController::class, 'store']);
    Route::post('frontenddata-delete/{id}', [App\Http\Controllers\FrontendDataController::class, 'destroy']);
    Route::get('frontend-website-data', [API\DashboardController::class, 'websiteData']);
    Route::get('deliveryman-dashboard-data', [API\DashboardController::class, 'deliverymandashboard']);


    Route::post('verify-otp-for-email', [API\UserController::class, 'verifyOTPForEmail']);
    Route::post('resend-otp-for-email', [API\UserController::class, 'resendOTPForEmail']);


    // pages
    Route::get('pages-list', [API\PagesController::class, 'getList']);
    Route::post('pages-save', [App\Http\Controllers\PagesController::class, 'store']);
    Route::post('pages-delete/{id}', [App\Http\Controllers\PagesController::class, 'destroy']);
    Route::post('pages-update/{id}', [App\Http\Controllers\PagesController::class, 'update']);
    Route::get('page-detail', [API\PagesController::class, 'getDetail']);

    //customer support route
    // Route::get('customersupport-list', [API\SupportchatHistoryController::class, 'getList']);
    Route::get('customersupport-list', [API\CustomerSupportController::class, 'getList']);
    Route::post('customersupport-save', [App\Http\Controllers\CustomerSupportController::class, 'store']);
    Route::post('customersupport-delete/{id}', [App\Http\Controllers\CustomerSupportController::class, 'destroy']);
    Route::post('chatmessage-save', [App\Http\Controllers\CustomerSupportController::class, 'chatMessage']);
    Route::post('status-save/{id}', [App\Http\Controllers\CustomerSupportController::class, 'updateStatus']);

    //reports route
    Route::get('userreport-list', [API\UserReportController::class, 'getList']);
    Route::get('deliverymanearningreport-list', [API\DeliverymanReportController::class, 'getList']);
    Route::get('adminearningreport-list', [API\AdminEarningReportController::class, 'getList']);
    Route::get('orderreport-list', [API\OrderReportController::class, 'getList']);
    Route::get('deliverymanreport-list', [API\DeliverymanByReportController::class, 'getList']);
    Route::get('cityreport-list', [API\CityByReportController::class, 'getList']);
    Route::get('countryreport-list', [API\CountryByReportController::class, 'getList']);

    //order location
    Route::get('order-location-list', [API\OrderController::class, 'orderLocationList']);

    //push notification
    Route::get('pushnotification-list', [API\PushNotificationController::class, 'getList']);
    Route::post('pushnotification-save', [App\Http\Controllers\PushNotificationController::class, 'store']);
    Route::post('pushnotification-delete/{id}', [App\Http\Controllers\PushNotificationController::class, 'destroy']);

    //isReSchedule api
    Route::post('reschedule-save', [App\Http\Controllers\OrderController::class, 'isReschedule']);

    //deliveryman vehicle history
    Route::post('deliverymanvehiclehistory-save', [App\Http\Controllers\OrderController::class, 'deliveryManVehiclehistory']);
    Route::get('deliverymanvehiclehistory-list', [API\DeliverymanVehicleHistoryController::class, 'getList']);

    //CourierCompanies api
    Route::get('couriercompanies-list', [API\CourierCompaniesController::class, 'getList']);
    Route::post('couriercompanies-save', [App\Http\Controllers\CourierCompaniesController::class, 'store']);
    Route::post('couriercompanies-update/{id}', [App\Http\Controllers\CourierCompaniesController::class, 'update']);
    Route::post('couriercompanies-delete/{id}', [App\Http\Controllers\CourierCompaniesController::class, 'destroy']);

    //SippedOrder in couriercomny add
    Route::post('shipped-save/{id}', [App\Http\Controllers\OrderController::class, 'updateCourierCompany']);

    Route::get('permision-list', [API\UserController::class, 'permissionGetList']);

    //subadmin api
    Route::get('subadmin-list', [API\SubAdminController::class, 'getList']);
    Route::post('subadmin-save', [App\Http\Controllers\SubAdminController::class, 'store']);
    Route::post('subadmin-update/{id}', [App\Http\Controllers\SubAdminController::class, 'update']);
    Route::post('subadmin-delete/{id}', [App\Http\Controllers\SubAdminController::class, 'destroy']);
    Route::post('subadmin-action', [App\Http\Controllers\SubAdminController::class, 'action']);

    // merge api
    Route::get('multipledetails-list', [API\DashboardController::class, 'multipleDetails']);
    Route::get('order-print-list', [API\OrderController::class, 'orderPrintList']);

    //claims mangemant
    Route::post('claims-save', [App\Http\Controllers\ClaimsController::class, 'store']);
    Route::get('claims-list', [App\Http\Controllers\ClaimsController::class, 'getList']);
    Route::get('claimhistory-list', [App\Http\Controllers\ClaimsController::class, 'claimhistorygetList']);
    Route::post('claimshistory-save', [App\Http\Controllers\ClaimsController::class, 'claimhistorySave']);
    Route::post('reject-status/{id}', [App\Http\Controllers\ClaimsController::class, 'rejectStatus']);
    Route::post('approved-status/{id}', [App\Http\Controllers\ClaimsController::class, 'approvedStatus']);

    Route::post('status-details', [App\Http\Controllers\ClaimsController::class, 'statusdetail']);
    //Bid
    Route::post('apply-bid', [App\Http\Controllers\OrderController::class, 'applyBidOrder']);
    Route::get('get-bidding-order', [App\Http\Controllers\OrderController::class, 'getBiddingDeliveryMan']);
    Route::post('order-bid-respond', [App\Http\Controllers\OrderController::class, 'acceptBidRequest']);

    Route::get('orderbid-list', [API\OrderController::class, 'OrderBidList']);
    Route::get('profOfpicture-list', [API\ProfofPicturesController::class, 'profOfpictureList']);
    Route::post('profOfpicture-save', [API\ProfofPicturesController::class, 'profOfpictureSave']);

    //Coupon
    Route::get('coupon-list', [API\CouponController::class, 'getlist']);

    //RestApi
    Route::get('restapi-list', [Api\RestApiController::class, 'getlist']);

    Route::post('order-status/{id}', [Api\OrderController::class, 'orderStatusUpdate']);
    Route::post('order-trasfer-deliveryman/{id}', [Api\OrderController::class, 'transferDeliverymanOrder']);

    // Delivery Routes API
    Route::get('delivery-routes', [App\Http\Controllers\Api\DeliveryRouteApiController::class, 'getDeliveryRoutes']);
    Route::get('delivery-man-location/{id}', [App\Http\Controllers\Api\DeliveryRouteApiController::class, 'getDeliveryManLocation']);
});
