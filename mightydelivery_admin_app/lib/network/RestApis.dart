import 'dart:convert';
import 'dart:io';
import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/http.dart';
import 'package:intl/intl.dart';
import '../extensions/extension_util/num_extensions.dart';
import '../models/ClaimsListModel.dart';
import '../models/ReportListModel.dart';

import '../main.dart';
import '../models/AppSettingModel.dart';
import '../models/AutoCompletePlaceListModel.dart';
import '../models/CityListModel.dart';
import '../models/CountryListModel.dart';
import '../models/CourierCompaniesListModel.dart';
import '../models/CreateOrderDetailResponse.dart';
import '../models/CustomerSupportListModel.dart';
import '../models/DashboardModel.dart';
import '../models/DeliveryDocumentListModel.dart';
import '../models/DirectionsResponse.dart';
import '../models/DocumentListModel.dart';
import '../models/ExtraChragesListModel.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../models/InvoiceSettingModel.dart';
import '../models/LDBaseResponse.dart';
import '../models/LoginResponse.dart';
import '../models/MonthlyChartModel.dart';
import '../models/NotificationModel.dart';
import '../models/OrderDetailModel.dart';
import '../models/OrderListModel.dart';
import '../models/PageListModel.dart';
import '../models/PageResponseModel.dart';
import '../models/ParcelTypeListModel.dart';
import '../models/PaymentGatewayListModel.dart';
import '../models/PlaceIdDetailModel.dart';
import '../models/PushNotificationListModel.dart';
import '../models/ReferenceListModel.dart';
import '../models/TotalAmountResponse.dart';
import '../models/UpdateUserStatus.dart';
import '../models/UserListModel.dart';
import '../models/UserModel.dart';
import '../models/UserProfileDetailModel.dart';
import '../models/VehicleModel.dart';
import '../models/WithdrawModel.dart';
import '../network/NetworkUtils.dart';
import '../screens/SignInScreen.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';

Future<LoginResponse> signUpApi(Map request) async {
  Response response = await buildHttpResponse('register', request: request, method: HttpMethod.POST);

  if (!(response.statusCode >= 200 && response.statusCode <= 206)) {
    if (response.body.isJson()) {
      var json = jsonDecode(response.body);

      if (json.containsKey('code') && json['code'].toString().contains('invalid_username')) {
        throw 'invalid_username';
      }
    }
  }

  return await handleResponse(response).then((json) async {
    var loginResponse = LoginResponse.fromJson(json);

    return loginResponse;
  }).catchError((e) {
    log(e.toString());
    throw e.toString();
  });
}

Future<LoginResponse> logInApi(Map request) async {
  Response response = await buildHttpResponse('login', request: request, method: HttpMethod.POST);

  if (!(response.statusCode >= 200 && response.statusCode <= 206)) {
    if (response.body.isJson()) {
      var json = jsonDecode(response.body);

      if (json.containsKey('code') && json['code'].toString().contains('invalid_username')) {
        throw 'invalid_username';
      }
    }
  }

  return await handleResponse(response).then((json) async {
    var loginResponse = LoginResponse.fromJson(json);
    await sharedPref.setString(TOKEN, loginResponse.data!.apiToken.validate());
    await sharedPref.setInt(USER_ID, loginResponse.data!.id!);
    await sharedPref.setString(NAME, loginResponse.data!.name.validate());
    await sharedPref.setString(USER_TYPE, loginResponse.data!.userType.validate());
    await sharedPref.setString(USER_EMAIL, loginResponse.data!.email.validate());
    await sharedPref.setString(USER_CONTACT_NUMBER, loginResponse.data!.contactNumber.validate());
    await sharedPref.setString(USER_NAME, loginResponse.data!.username.validate());
    await sharedPref.setString(USER_ADDRESS, loginResponse.data!.address.validate());
    await sharedPref.setString(USER_PASSWORD, request['password']);

    appStore.setUserProfile(loginResponse.data!.profileImage!.validate());
    await appStore.setLoggedIn(true);
    return loginResponse;
  }).catchError((e) {
    throw e.toString();
  });
}

Future<void> logout(BuildContext context, {bool isFromLogin = false}) async {
  if (!isFromLogin) {
    Navigator.pop(context);
    appStore.setLoading(true);
  }
  await logoutApi().then((value) async {
    await sharedPref.remove(TOKEN);
    await sharedPref.remove(IS_LOGGED_IN);
    await sharedPref.remove(USER_ID);
    await sharedPref.remove(USER_TYPE);
    await sharedPref.remove(FCM_TOKEN);
    await sharedPref.remove(PLAYER_ID);
    await sharedPref.remove(NAME);
    await sharedPref.remove(USER_PROFILE_PHOTO);
    await sharedPref.remove(USER_CONTACT_NUMBER);
    await sharedPref.remove(USER_NAME);
    await sharedPref.remove(USER_ADDRESS);
    await sharedPref.remove(FILTER_DATA);

    if (!(sharedPref.getBool(REMEMBER_ME) ?? false)) {
      await sharedPref.remove(USER_EMAIL);
      await sharedPref.remove(USER_PASSWORD);
    }

    await appStore.setLoggedIn(false);
    appStore.setLoading(false);
    if (isFromLogin) {
      toast(language.credentialNotMatch);
    } else {
      launchScreen(context, SignInScreen(), isNewTask: true);
    }
  }).catchError((e) {
    appStore.setLoading(false);
    throw e.toString();
  });
}

/// Profile Update
Future updateProfile({int? id, String? userName, String? name, String? userEmail, String? address, String? contactNumber, File? file, String? uid}) async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('update-profile');
  multiPartRequest.fields['id'] = '${id ?? sharedPref.getInt(USER_ID)}';

  if (userName != null) multiPartRequest.fields['username'] = userName.validate();

  if (userEmail != null) multiPartRequest.fields['email'] = userEmail.validate();
  if (name != null) multiPartRequest.fields['name'] = name.validate();
  if (contactNumber != null) multiPartRequest.fields['contact_number'] = contactNumber.validate();
  if (address != null) multiPartRequest.fields['address'] = address.validate();
  if (uid != null) multiPartRequest.fields['uid'] = uid.validate();

  if (file != null) multiPartRequest.files.add(await MultipartFile.fromPath('profile_image', file.path));

  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    print("=======>${data}");
    if (data != null) {
      LoginResponse res = LoginResponse.fromJson(data);
      if (id == null) {
        await sharedPref.setString(NAME, res.data!.name.validate());
        await sharedPref.setString(USER_PROFILE_PHOTO, res.data!.profileImage.validate());
        await sharedPref.setString(USER_NAME, res.data!.username.validate());
        await sharedPref.setString(USER_ADDRESS, res.data!.address.validate());
        await sharedPref.setString(USER_CONTACT_NUMBER, res.data!.contactNumber.validate());
        await sharedPref.setString(USER_EMAIL, res.data!.email.validate());

        appStore.setUserProfile(res.data!.profileImage.validate());
      }
    }
  }, onError: (error) {
    toast(error.toString());
  });
}

Future<LDBaseResponse> forgotPassword(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('forget-password', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> changePassword(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('change-password', request: req, method: HttpMethod.POST)));
}

// User Api
// User Api
Future<UserListModel> getAllUserList({String? type, int? perPage, int? page, String? searchText, int? status, String? user_status}) async {
  String? endPoint = '';
  if (!searchText.isEmptyOrNull) {
    endPoint = 'user-filter-list?user_type=$type&search=$searchText&is_deleted=1&page=$page&per_page=$perPage';
  } else if (status != null) {
    endPoint = 'user-list?user_type=$type&page=$page&status=$status&is_deleted=1&per_page=$perPage';
  } else if (user_status != null) {
    endPoint = 'user-list?user_type=$type&page=$page&user_status=$user_status&is_deleted=1&per_page=$perPage';
  } else {
    endPoint = 'user-list?user_type=$type&page=$page&is_deleted=1&per_page=$perPage';
  }

  return UserListModel.fromJson(await handleResponse(await buildHttpResponse(endPoint, method: HttpMethod.GET)));
}

Future<UpdateUserStatus> updateUserStatus(Map req) async {
  return UpdateUserStatus.fromJson(await handleResponse(await buildHttpResponse('update-user-status', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteUser(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('delete-user', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> userAction(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('user-action', request: request, method: HttpMethod.POST)));
}

Future<UserModel> getUserDetail(int id) async {
  return UserModel.fromJson(await handleResponse(await buildHttpResponse('user-detail?id=$id', method: HttpMethod.GET)).then((value) => value['data']));
}

// Country Api
// Country Api
Future<CountryListModel> getCountryList({int? page, bool isDeleted = false, int perPage = 10, int? status = 0}) async {
  if (status == 0)
    return CountryListModel.fromJson(await handleResponse(await buildHttpResponse(page != null ? 'country-list?page=$page&is_deleted=${isDeleted ? 1 : 0}&per_page=$perPage' : 'country-list?per_page=-1&is_deleted=${isDeleted ? 1 : 0}', method: HttpMethod.GET)));
  else
    return CountryListModel.fromJson(await handleResponse(await buildHttpResponse(page != null ? 'country-list?page=$page&is_deleted=${isDeleted ? 1 : 0}&per_page=$perPage' : 'country-list?per_page=-1&is_deleted=${isDeleted ? 1 : 0}&status=$status', method: HttpMethod.GET)));
}

Future<LDBaseResponse> addCountry(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('country-save', request: request, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteCountry(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('country-delete/$id', method: HttpMethod.POST)));
}

Future<CountryData> getCountryDetail(int id) async {
  return CountryData.fromJson(await handleResponse(await buildHttpResponse('country-detail?id=$id', method: HttpMethod.GET)).then((value) => value['data']));
}

Future<LDBaseResponse> countryAction(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('country-action', request: request, method: HttpMethod.POST)));
}

// City Api
// City Api
Future<CityListModel> getCityList({int? page, bool isDeleted = false, int? countryId, int? perPage = 10, int? status = 0, String? searchText}) async {
  String? endPoint = '';
  if (countryId == null && searchText.isEmptyOrNull) {
    endPoint = page != null ? 'city-list?page=$page&is_deleted=${isDeleted ? 1 : 0}&per_page=$perPage' : 'city-list?per_page=-1&is_deleted=${isDeleted ? 1 : 0}';
  } else if (!searchText.isEmptyOrNull) {
    endPoint = 'city-list?search=$searchText&status=1&per_page=$page&is_deleted=1';
  } else {
    if (status == 0)
      endPoint = 'city-list?per_page=-1&country_id=$countryId';
    else
      endPoint = 'city-list?per_page=-1&country_id=$countryId&status=$status';
  }
  return CityListModel.fromJson(await handleResponse(await buildHttpResponse(endPoint, method: HttpMethod.GET)));
}

Future<LDBaseResponse> addCity(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('city-save', request: request, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteCity(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('city-delete/$id', method: HttpMethod.POST)));
}

Future<LDBaseResponse> cityAction(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('city-action', request: request, method: HttpMethod.POST)));
}

Future<CityData> getCityDetail(int id) async {
  return CityData.fromJson(await handleResponse(await buildHttpResponse('city-detail?id=$id', method: HttpMethod.GET)).then((value) => value['data']));
}

addVehicle({
  int? id,
  String? title,
  String? type,
  String? size,
  String? capacity,
  String? cityId,
  String? description,
  String? vehicleImage,
  Uint8List? image,
  num? price,
  num? minKm,
  num? perKmCharge,
  int? status,
}) async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('vehicle-save');
  if (id != null) multiPartRequest.fields['id'] = id.toString();
  if (title != null) multiPartRequest.fields['title'] = title.validate();
  if (type != null) multiPartRequest.fields['type'] = type.validate();
  if (size != null) multiPartRequest.fields['size'] = size.validate();
  if (capacity != null) multiPartRequest.fields['capacity'] = capacity.validate();
  if (cityId != null && cityId.isNotEmpty) multiPartRequest.fields['city_ids'] = cityId.toString();
  if (description != null) multiPartRequest.fields['description'] = description.validate();
  if (price != null) multiPartRequest.fields['price'] = price.validate().toString();
  if (minKm != null) multiPartRequest.fields['min_km'] = minKm.validate().toString();
  if (perKmCharge != null) multiPartRequest.fields['per_km_charge'] = perKmCharge.validate().toString();
  multiPartRequest.fields['status'] = status.toString();
  if (image != null) {
    multiPartRequest.files.add(MultipartFile.fromBytes('vehicle_image', image, filename: vehicleImage));
  }
  print('req: ${multiPartRequest.fields} ${multiPartRequest.files}');
  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    if (data != null) {
      //
    }
  }, onError: (error) {
    log('$error');
  });
}

Future<VehicleListModel> getVehicleList({int? page, bool isDeleted = false, int perPage = 10, int? cityID, String? searchText}) async {
  String endPoint = 'vehicle-list?is_deleted=${isDeleted ? 1 : 0}';
  if (cityID != null) {
    endPoint = endPoint + '&city_id=$cityID&status=1&per_page=-1';
  } else if (!searchText.isEmptyOrNull) {
    endPoint = 'vehicle-list?search=$searchText&status=1&per_page=$page&is_deleted=1';
  } else {
    endPoint = endPoint + '&page=$page&per_page=$perPage';
  }
  return VehicleListModel.fromJson(await handleResponse(await buildHttpResponse(endPoint, method: HttpMethod.GET)));
}

Future<LDBaseResponse> deleteVehicle(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('vehicle-delete/$id', method: HttpMethod.POST)));
}

Future<LDBaseResponse> vehicleAction(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('vehicle-action', request: request, method: HttpMethod.POST)));
}

// ExtraCharge Api
Future<ExtraChargesListModel> getExtraChargeList({int? page, bool isDeleted = false}) async {
  return ExtraChargesListModel.fromJson(await handleResponse(await buildHttpResponse('extracharge-list?page=$page&is_deleted=${isDeleted ? 1 : 0}', method: HttpMethod.GET)));
}

Future<LDBaseResponse> addExtraCharge(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('extracharge-save', request: request, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteExtraCharge(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('extracharge-delete/$id', method: HttpMethod.POST)));
}

Future<LDBaseResponse> extraChargeAction(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('extracharge-action', request: request, method: HttpMethod.POST)));
}

// Document Api
Future<DocumentListModel> getDocumentList({int? page, bool isDeleted = false}) async {
  return DocumentListModel.fromJson(await handleResponse(await buildHttpResponse('document-list?page=$page&is_deleted=${isDeleted ? 1 : 0}', method: HttpMethod.GET)));
}

Future<LDBaseResponse> addDocument(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('document-save', request: request, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteDocument(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('document-delete/$id', method: HttpMethod.POST)));
}

Future<LDBaseResponse> documentAction(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('document-action', request: request, method: HttpMethod.POST)));
}

// Delivery Man Documents
Future<DeliveryDocumentListModel> getDeliveryDocumentList({int? page, bool isDeleted = false, int? deliveryManId, int? perPage = 10}) async {
  return DeliveryDocumentListModel.fromJson(await handleResponse(await buildHttpResponse(
      deliveryManId != null ? 'delivery-man-document-list?page=$page&is_deleted=${isDeleted ? 1 : 0}&delivery_man_id=$deliveryManId&per_page=$perPage' : 'delivery-man-document-list?page=$page&is_deleted=${isDeleted ? 1 : 0}&per_page=$perPage',
      method: HttpMethod.GET)));
}

Future<LDBaseResponse> multipleDeleteDocumentList(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('multiple-delete-deliveryman-document', request: request, method: HttpMethod.POST)));
}

/// Create Order Api
Future<LDBaseResponse> createOrder(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('order-save', request: request, method: HttpMethod.POST)));
}

/// get OrderList
Future<OrderListModel> getAllOrder({int? page, int perPage = 10, String? orderStatus, String? orderType, String? fromDate, String? toDate, bool isTrashed = true}) async {
  String endPoint = '';
  if (isTrashed) {
    endPoint = 'order-list?page=$page&status=trashed&per_page=$perPage';
  } else {
    endPoint = 'order-list?page=$page&per_page=$perPage';
  }

  if (orderStatus.validate().isNotEmpty) {
    endPoint += '&statuses=$orderStatus';
  }
  if (orderType.validate().isNotEmpty) {
    endPoint += '&order_type=$orderType';
  }

  if (fromDate.validate().isNotEmpty && toDate.validate().isNotEmpty) {
    endPoint += '&from_date=${DateFormat('yyyy-MM-dd').format(DateTime.parse(fromDate.validate()))}&to_date=${DateFormat('yyyy-MM-dd').format(DateTime.parse(toDate.validate()))}';
  }

  return OrderListModel.fromJson(await handleResponse(await buildHttpResponse(endPoint, method: HttpMethod.GET)));
}

// ParcelType Api
Future<ParcelTypeListModel> getParcelTypeList({int? page}) async {
  return ParcelTypeListModel.fromJson(await handleResponse(await buildHttpResponse(page != null ? 'staticdata-list?type=parcel_type&page=$page' : 'staticdata-list?type=parcel_type&per_page=-1', method: HttpMethod.GET)));
}

Future<LDBaseResponse> addParcelType(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('staticdata-save', request: request, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteParcelType(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('staticdata-delete/$id', method: HttpMethod.POST)));
}

// Payment Gateway Api
Future<PaymentGatewayListModel> getPaymentGatewayList() async {
  return PaymentGatewayListModel.fromJson(await handleResponse(await buildHttpResponse('paymentgateway-list?perPage=-1', method: HttpMethod.GET)));
}

Future<MultipartRequest> getMultiPartRequest(String endPoint, {String? baseUrl}) async {
  String url = '${baseUrl ?? buildBaseUrl(endPoint).toString()}';
  return MultipartRequest('POST', Uri.parse(url));
}

Future<void> sendMultiPartRequest(MultipartRequest multiPartRequest, {Function(dynamic)? onSuccess, Function(dynamic)? onError}) async {
  multiPartRequest.headers.addAll(buildHeaderTokens());
  http.Response response = await http.Response.fromStream(await multiPartRequest.send());
  print(response);
  if (response.statusCode >= 200 && response.statusCode <= 206) {
    onSuccess?.call(jsonDecode(response.body));
  } else {
    onError?.call(language.somethingWentWrong);
  }
}

// Dashboard Api
Future<DashboardModel> getDashBoardData() async {
  return DashboardModel.fromJson(await handleResponse(await buildHttpResponse('dashboard-detail', method: HttpMethod.GET)));
}

Future<MonthlyChartModel> getDashBoardChartData(String? type, String? startDate, String? endDate) async {
  return MonthlyChartModel.fromJson(await handleResponse(await buildHttpResponse('dashboard-chartdata?type=$type&start_at=$startDate&end_at=$endDate', method: HttpMethod.GET)));
}

Future<MonthlyCancelPaymentChartModel> getCancelPaymentChartData(String? type, String? startDate, String? endDate) async {
  return MonthlyCancelPaymentChartModel.fromJson(await handleResponse(await buildHttpResponse('dashboard-chartdata?type=$type&start_at=$startDate&end_at=$endDate', method: HttpMethod.GET)));
}

Future<MonthlyCompletePaymentChartModel> getCompletePaymentChartData(String? type, String? startDate, String? endDate) async {
  return MonthlyCompletePaymentChartModel.fromJson(await handleResponse(await buildHttpResponse('dashboard-chartdata?type=$type&start_at=$startDate&end_at=$endDate', method: HttpMethod.GET)));
}

Future<LDBaseResponse> getRestoreOrderApi(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('order-action', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteOrderApi(int orderId) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('order-delete/$orderId', method: HttpMethod.POST)));
}

Future<UserListModel> getAllDeliveryBoyList({String? type, int? page, int? cityID, int? countryId}) async {
  return UserListModel.fromJson(await handleResponse(await buildHttpResponse('user-list?user_type=$type&page=$page&country_id=$countryId&city_id=$cityID&status=1', method: HttpMethod.GET)));
}

Future<LDBaseResponse> orderAssign(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('order-action', request: req, method: HttpMethod.POST)));
}

Future<OrderDetailModel> orderDetail({required int orderId}) async {
  return OrderDetailModel.fromJson(await handleResponse(await buildHttpResponse('order-detail?id=$orderId', method: HttpMethod.GET)));
}

Future<NotificationModel> getNotification({required int page, Map? req}) async {
  if (req != null) {
    return NotificationModel.fromJson(await handleResponse(await buildHttpResponse('notification-list', request: req, method: HttpMethod.POST)));
  } else {
    return NotificationModel.fromJson(await handleResponse(await buildHttpResponse('notification-list?limit=20&page=$page', method: HttpMethod.POST)));
  }
}

Future<AppSettingModel> getAppSetting() async {
  return AppSettingModel.fromJson(await handleResponse(await buildHttpResponse('get-appsetting', method: HttpMethod.GET)));
}

Future<LDBaseResponse> setNotification(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('update-appsetting', request: req, method: HttpMethod.POST)));
}

Future<AutoCompletePlacesListModel> placeAutoCompleteApi({String searchText = '', String countryCode = "in", String language = 'en'}) async {
  return AutoCompletePlacesListModel.fromJson(await handleResponse(await buildHttpResponse('place-autocomplete-api?country_code=$countryCode&language=$language&search_text=$searchText', method: HttpMethod.GET)));
}

Future<PlaceIdDetailModel> getPlaceDetail({String placeId = ''}) async {
  return PlaceIdDetailModel.fromJson(await handleResponse(await buildHttpResponse('place-detail-api?placeid=$placeId', method: HttpMethod.GET)));
}

Future<LDBaseResponse> logoutApi() async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('logout?clear=player_id', method: HttpMethod.GET)));
}

// Wallet Api
Future<WithDrawModel> getWithdrawList({int? page, int perPage = 10, String? status}) async {
  String endpoint = "withdrawrequest-list?page=$page&per_page=$perPage";
  if (!status.isEmptyOrNull) {
    endpoint += "&status=${status!}";
  }
  return WithDrawModel.fromJson(await handleResponse(await buildHttpResponse(endpoint, method: HttpMethod.GET)));
}

Future<LDBaseResponse> deleteWithdraw(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('decline-withdrawrequest', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> approveWithdraw(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('approved-withdrawrequest', request: req, method: HttpMethod.POST)));
}

Future<UserProfileDetailModel> getUserProfile(int userId) async {
  return UserProfileDetailModel.fromJson(await handleResponse(await buildHttpResponse('user-profile-detail?id=$userId', method: HttpMethod.GET)));
}

Future<WalletHistory> getWalletList({required int page, required userId}) async {
  return WalletHistory.fromJson(await handleResponse(await buildHttpResponse('wallet-list?page=$page&user_id=$userId', method: HttpMethod.GET)));
}

Future<EarningList> getPaymentList({required int page, required userId}) async {
  return EarningList.fromJson(await handleResponse(await buildHttpResponse('payment-list?page=$page&delivery_man_id=$userId&type=earning', method: HttpMethod.GET)));
}

Future<LDBaseResponse> saveWallet(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('save-wallet', method: HttpMethod.POST, request: request)));
}

Future<LDBaseResponse> setInvoiceSetting(String req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('save-setting', req: req, method: HttpMethod.POST)));
}

Future<InvoiceSettingModel> getInvoiceSetting() async {
  return InvoiceSettingModel.fromJson(await handleResponse(await buildHttpResponse('get-setting', method: HttpMethod.GET)));
}

//Delivery boy live Location

Future<UserListModel> getAllDeliveryBoyLiveLocationList({String? type, int? perPage = 500}) async {
  return UserListModel.fromJson(await handleResponse(await buildHttpResponse('user-list?user_type=$type&status=1&per_page=$perPage', method: HttpMethod.GET)));
}

Future<GetFrontendDataResponseModel> getFrontendDataList() async {
  return GetFrontendDataResponseModel.fromJson(await handleResponse(await buildHttpResponse('frontend-website-data', method: HttpMethod.GET)));
}

Future frontendDataSave({
  String? id,
  String? title,
  String? subtitle,
  String? type,
  String? description,
  Uint8List? frontEndImage,
  String? frontEndImageName,
}) async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('frontenddata-save');
  if (id != null) multiPartRequest.fields['id'] = id;
  if (title != null) multiPartRequest.fields['title'] = title;
  if (subtitle != null) multiPartRequest.fields['subtitle'] = subtitle;
  if (type != null) multiPartRequest.fields['type'] = type;
  if (description != null) multiPartRequest.fields['description'] = description;
  if (frontEndImage != null) multiPartRequest.files.add(MultipartFile.fromBytes('frontend_data_image', frontEndImage, filename: frontEndImageName));

  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    if (data != null) {
      //
    }
  }, onError: (error) {
    toast(error.toString());
  });
}

Future<LDBaseResponse> deleteFrontendData(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('frontenddata-delete/$id', method: HttpMethod.POST)));
}

Future<LDBaseResponse> multipleDeleteCity(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('multiple-delete-city', request: request, method: HttpMethod.POST)));
}

Future<LDBaseResponse> multipleDeleteOrder(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('multiple-delete-order', request: request, method: HttpMethod.POST)));
}

Future<LDBaseResponse> multipleDeleteVehicle(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('multiple-delete-vehicle', request: request, method: HttpMethod.POST)));
}

Future<DirectionsResponse> getDistanceBetweenLatLng(String origins, String destinations) async {
  return DirectionsResponse.fromJson(await handleResponse(await buildHttpResponse('distance-matrix-api?origins=$origins&destinations=$destinations', method: HttpMethod.GET)));
}

Future<LDBaseResponse> multipleDeleteUser(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('multiple-delete-user', request: request, method: HttpMethod.POST)));
}

Future<ReportListModel> getReportList({required String apiEndPoint, page, String? id, String? fromDate, String? toDate}) async {
  String endPoint = '$apiEndPoint?page=$page';
  if (id.validate().isNotEmpty) {
    endPoint += "&$id";
  }

  if (fromDate.validate().isNotEmpty && toDate.validate().isNotEmpty) {
    endPoint += '&from_date=${DateFormat('yyyy-MM-dd').format(DateTime.parse(fromDate.validate()))}&to_date=${DateFormat('yyyy-MM-dd').format(DateTime.parse(toDate.validate()))}';
  }

  return ReportListModel.fromJson(await handleResponse(await buildHttpResponse(endPoint, method: HttpMethod.GET)));
}

Future<PushNotificationListModel> getPushNotificationList({
  int? page,
  int perPage = 10,
}) async {
  return PushNotificationListModel.fromJson(await handleResponse(await buildHttpResponse("pushnotification-list?page=$page&per_page=$perPage", method: HttpMethod.GET)));
}

Future<LDBaseResponse> deletePushNotification(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('pushnotification-delete/$id', method: HttpMethod.POST)));
}

Future<PageListModel> getPagesList(int? page) async {
  return PageListModel.fromJson(await handleResponse(await buildHttpResponse(
    'pages-list?page=$page',
    method: HttpMethod.GET,
  )));
}

Future<LDBaseResponse> deletePages(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('pages-delete/$id', method: HttpMethod.POST)));
}

Future<LDBaseResponse> updatePages(int id, Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('pages-update/$id', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> savePages(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('pages-save', request: req, method: HttpMethod.POST)));
}

Future<CustomerSupportListModel> getCustomerSupportList({int? page, int? support_id, String? status}) async {
  String endpoint = 'customersupport-list?page=$page';
  if (support_id != null && support_id > 0) {
    endpoint += '&support_id=$support_id';
  }
  if (!status.isEmptyOrNull) {
    endpoint += "&status=$status";
  }
  return CustomerSupportListModel.fromJson(await handleResponse(await buildHttpResponse(
    endpoint,
    method: HttpMethod.GET,
  )));
}

Future<LDBaseResponse> saveChat(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('chatmessage-save', method: HttpMethod.POST, request: req)));
}

Future<LDBaseResponse> CustomerSupportStatusSave(int id, Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('status-save/$id', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> deleteCustomerSupport(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('customersupport-delete/$id', method: HttpMethod.POST)));
}

Future<CourierCompaniesList> getCourierCompaniesList(int? page) async {
  return CourierCompaniesList.fromJson(await handleResponse(await buildHttpResponse(
    'couriercompanies-list?page=$page',
    method: HttpMethod.GET,
  )));
}

Future<LDBaseResponse> deleteCourierCompanies(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('couriercompanies-delete/$id', method: HttpMethod.POST)));
}

Future<LDBaseResponse> saveShippedOrder(int id, Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('shipped-save/$id', request: req, method: HttpMethod.POST)));
}

Future<CreateOrderDetailsResponse> getCreateOrderDetails(int id) async {
  return CreateOrderDetailsResponse.fromJson(await handleResponse(await buildHttpResponse(
    'multipledetails-list?city_id=$id',
    method: HttpMethod.GET,
  )));
}

Future<TotalAmountResponse> getTotalAmountForOrder(Map req) async {
  return TotalAmountResponse.fromJson(
      await handleResponse(await buildHttpResponse('calculatetotal-get', method: HttpMethod.POST, request: req)));
}

Future<PageResponse> getPageDetailsById({String? id}) async {
  return PageResponse.fromJson(await handleResponse(await buildHttpResponse(
    'page-detail?id=$id',
    method: HttpMethod.GET,
  )));
}

Future<ReferenceListModel> getReferenceList() async {
  return ReferenceListModel.fromJson(await handleResponse(await buildHttpResponse(
    'reference-list',
    method: HttpMethod.GET,
  )));
}

Future<ClaimListResponseModel> getClaimsList({int? page, String? status}) async {
  String endpoint = 'claims-list?page=$page';
  if (!status.isEmptyOrNull) {
    endpoint += "&status=$status";
  }
  return ClaimListResponseModel.fromJson(await handleResponse(await buildHttpResponse(
    endpoint,
    method: HttpMethod.GET,
  )));
}

Future<LDBaseResponse> changeClaimStatus(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse(
    'status-details',
    method: HttpMethod.POST,
    request: request
  )));
}

