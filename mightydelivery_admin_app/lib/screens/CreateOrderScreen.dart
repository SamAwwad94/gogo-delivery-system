import 'dart:core';

import 'package:country_code_picker/country_code_picker.dart';
import 'package:date_time_picker/date_time_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:intl/intl.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/num_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../utils/Constants.dart';
import 'package:percent_indicator/circular_percent_indicator.dart';

import '../components/OrderAmountDataWidget.dart';
import '../components/PackingSymbolInfo.dart';
import '../components/PickAddressBottomSheet.dart';
import '../extensions/common.dart';
import '../extensions/confirmation_dialog.dart';
import '../extensions/decorations.dart';
import '../main.dart';
import '../models/AutoCompletePlaceListModel.dart';
import '../models/CityListModel.dart';
import '../models/CountryListModel.dart';
import '../models/ExtraChargeRequestModel.dart';
import '../models/OrderModel.dart';
import '../models/ParcelTypeListModel.dart';
import '../models/PlaceIdDetailModel.dart';
import '../models/TotalAmountResponse.dart';
import '../models/VehicleModel.dart';
import '../network/RestApis.dart';
import '../screens/DashboardScreen.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';
import '../utils/Extensions/shared_pref.dart';
import '../utils/Images.dart';
import 'InsuranceDetailsScreen.dart';

class CreateOrderScreen extends StatefulWidget {
  static String tag = '/AppCreateOrderScreen';

  final OrderModel? orderData;

  CreateOrderScreen({this.orderData});

  @override
  CreateOrderScreenState createState() => CreateOrderScreenState();
}

class CreateOrderScreenState extends State<CreateOrderScreen> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  bool isDeliverNow = true;
  String paymentCollectFrom = PAYMENT_ON_PICKUP;

  List<ParcelTypeData> parcelTypeList = [];

  TextEditingController parcelTypeCont = TextEditingController();
  TextEditingController weightController = TextEditingController(text: '1');
  TextEditingController totalParcelController = TextEditingController(text: '1');

  TextEditingController pickAddressCont = TextEditingController();
  TextEditingController pickPersonNameCont = TextEditingController();
  TextEditingController pickPhoneCont = TextEditingController();
  TextEditingController pickDesCont = TextEditingController();
  TextEditingController pickInstructionCont = TextEditingController();
  TextEditingController pickDateController = TextEditingController();
  TextEditingController pickFromTimeController = TextEditingController();
  TextEditingController pickToTimeController = TextEditingController();

  TextEditingController deliverAddressCont = TextEditingController();
  TextEditingController deliverPhoneCont = TextEditingController();
  TextEditingController deliverPersonNameCont = TextEditingController();
  TextEditingController deliverDesCont = TextEditingController();
  TextEditingController deliverInstructionCont = TextEditingController();
  TextEditingController deliverDateController = TextEditingController();
  TextEditingController deliverFromTimeController = TextEditingController();
  TextEditingController deliverToTimeController = TextEditingController();
  TextEditingController insuranceAmountController = TextEditingController();

  FocusNode pickPhoneFocus = FocusNode();
  FocusNode pickPersonNameFocus = FocusNode();
  FocusNode pickDesFocus = FocusNode();
  FocusNode pickInstructionFocus = FocusNode();
  FocusNode deliveryPesonNameFocus = FocusNode();
  FocusNode deliverPhoneFocus = FocusNode();
  FocusNode deliveryInstructionFocus = FocusNode();
  FocusNode deliverDesFocus = FocusNode();

  String deliverCountryCode = defaultPhoneCode;
  String pickupCountryCode = defaultPhoneCode;

  int selectedTabIndex = 0;

  int? selectedVehicle;

  DateTime? pickFromDateTime, pickToDateTime, deliverFromDateTime, deliverToDateTime;
  DateTime? pickDate, deliverDate;
  TimeOfDay? pickFromTime, pickToTime, deliverFromTime, deliverToTime;

  String? pickLat, pickLong, deliverLat, deliverLong;

  num totalDistance = 0;
  num totalAmount = 0;

  num weightCharge = 0;
  num distanceCharge = 0;
  num totalExtraCharge = 0;
  num insuranceAmount = 0.0;
  num vehicleCharge = 0.0;

  List<ExtraChargeRequestModel> extraChargeList = [];
  TotalAmountResponse? totalAmountResponse;

  int? selectedCountry;
  int? selectedCity;

  CityData? cityData;
  CountryData? countryData;

  List<CountryData> countryList = [];
  List<CityData> cityList = [];

  DateTime? currentBackPressTime;

  VehicleData? vehicleData;

  List<VehicleData> vehicleList = [];
  List<Marker> markers = [];
  GoogleMapController? googleMapController;
  Set<Polyline> _polylines = {};
  List<LatLng> polylineCoordinates = [];
  PolylinePoints polylinePoints = PolylinePoints();
  final List<Map<String, String>> selectedPackingSymbols = [];
  final List<Map<String, String>> packingSymbolsItems = getPackagingSymbols();
  int insuranceSelectedOption = 1;
  List<String> appBarTitleList = [
    language.createOrder,
    language.pickupInfo,
    language.deliveryInformation,
    language.routeReview,
    language.details,
  ];

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    await getParcelTypeListApiCall();
    await getCountryApiCall();
    await getAppSetting().then((value) {
      appStore.setCurrencyCode(value.currencyCode ?? currencyCode);
      appStore.setCurrencySymbol(value.currency ?? currencySymbol);
      appStore.setCurrencyPosition(value.currencyPosition ?? CURRENCY_POSITION_LEFT);
      appStore.isVehicleOrder = value.isVehicleInOrder ?? 0;
      appStore.isInsuranceAllowed = value.insuranceAllow.validate();
      appStore.insuranceDescription = value.insuranceDescription.validate();
      appStore.insurancePercentage = value.insurancePercentage.validate();
      sharedPref.setString(ORDER_PREFIX, value.prefix.validate());
      setState(() {});
    }).catchError((error) {
      log(error.toString());
    });

    /*  await getCreateOrderDetails(getIntAsync(cityData.id.validate())).then((value) async {
      appStore.setLoading(false);
      vehicleList.clear();
      vehicleList = value.vehicleDetail!;
      if (value.vehicleDetail!.isNotEmpty) selectedVehicle = value.vehicleDetail![0].id;
      parcelTypeList.clear();
      parcelTypeList.addAll(value.staticDetails!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });*/
  }

  getVehicleApiCall({String? name}) async {
    appStore.setLoading(true);
    await getVehicleList(cityID: selectedCity).then((value) {
      appStore.setLoading(false);
      vehicleList.clear();
      vehicleList = value.data!;
      selectedVehicle = vehicleList[0].id.validate();
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  getTotalForOrder() {
    Map request = {
      "city_id": cityData!.id.validate().toString(),
      if (appStore.isVehicleOrder != 0) "vehicle_id": vehicleList.firstWhere((element) => element.id == selectedVehicle).id,
      //   "is_insurance": insuranceSelectedOption == 0 && appStore.isInsuranceAllowed == "1",
      "is_insurance": insuranceAmount == 0 ? 0 : 1,
      "total_weight": weightController.text.toDouble(),
      "total_distance": totalDistance,
      "insurance_amount": insuranceAmountController.text,
    };
    getTotalAmountForOrder(request).then((value) {
      print("------------request${request.toString()}");
      totalAmountResponse = value;
      double chargesTotal = 0;
      totalAmountResponse!.extraCharges!.forEach((element) async {
        double i = 0;
        if (element.chargesType == CHARGE_TYPE_PERCENTAGE) {
          i = (totalAmountResponse!.baseTotal!.toDouble() * element.charges!.toDouble() * 0.01).toStringAsFixed(digitAfterDecimal).toDouble();
        } else {
          i = element.charges!.toStringAsFixed(digitAfterDecimal).toDouble();
        }

        chargesTotal = chargesTotal + i;
      });
      if (value.vehicleAmount != null) {
        vehicleCharge = value.vehicleAmount!;
      }
      totalAmount =totalAmountResponse!.totalAmount!.toDouble();
      print("------------------totalAmount${totalAmount}");
      setState(() {});
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  getParcelTypeListApiCall() async {
    appStore.setLoading(true);
    await getParcelTypeList().then((value) {
      appStore.setLoading(false);
      parcelTypeList.clear();
      parcelTypeList.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  getCountryApiCall() async {
    appStore.setLoading(true);
    await getCountryList(status: 1).then((value) {
      appStore.setLoading(false);
      countryList = value.data!;
      selectedCountry = countryList[0].id!;

      getCityApiCall();
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  getCityApiCall({String? name}) async {
    appStore.setLoading(true);
    await getCityList(countryId: selectedCountry!, status: 1).then((value) {
      appStore.setLoading(false);
      cityList.clear();
      cityList.addAll(value.data!);
      selectedCity = cityList[0].id!;
      cityData = cityList[0];

      getVehicleApiCall();
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  getCityDetailApiCall() async {
    await getCityDetail(selectedCity ?? 0).then((value) async {
      cityData = value;
      setState(() {});
    }).catchError((error) async {
      if (error.toString() == CITY_NOT_FOUND_EXCEPTION) {
        // await showDialog(
        //     context: getContext,
        //     builder: (_) {
        //       return UserCitySelectScreen(
        //         onUpdate: () {
        //           Navigator.pushNamedAndRemoveUntil(getContext, DashboardScreen.route, (route) {
        //             return true;
        //           });
        //         },
        //       );
        //     });
      }
    });
  }

  String getCountryCode() {
    String countryCode = countryList[0].code!;
    countryList.forEach((element) {
      if (element.id == selectedCountry) {
        countryCode = element.code!;
      }
    });
    return countryCode;
  }

  getTotalAmount() async {
    // totalDistance = calculateDistance(double.tryParse(pickLat!), double.tryParse(pickLong!), double.tryParse(deliverLat!), double.tryParse(deliverLong!));

    String origins = "${pickLat},${pickLong}";
    String destinations = "${deliverLat},${deliverLong}";
    await getDistanceBetweenLatLng(origins, destinations).then((value) {
      print(value);
      double distanceInKms = value.rows[0].elements[0].distance.text.toString().split(' ')[0].toDouble();
      if (appStore.distanceUnit == DISTANCE_UNIT_MILE) {
        totalDistance = (MILES_PER_KM * distanceInKms);
      } else {
        totalDistance = distanceInKms;
      }
      totalAmount = 0;
      weightCharge = 0;
      distanceCharge = 0;
      totalExtraCharge = 0;
      getTotalForOrder();

      /* /// calculate weight Charge
      if (double.tryParse(weightController.text)! > cityData!.minWeight!) {
        weightCharge = double.parse(((double.tryParse(weightController.text)! - cityData!.minWeight!) * cityData!.perWeightCharges!).toStringAsFixed(digitAfterDecimal));
      }

      /// calculate distance Charge
      if (totalDistance > cityData!.minDistance!) {
        distanceCharge = double.parse(((totalDistance - cityData!.minDistance!) * cityData!.perDistanceCharges!).toStringAsFixed(digitAfterDecimal));
      }

      /// total amount
      totalAmount = cityData!.fixedCharges! + weightCharge + distanceCharge;

      /// vehicle charge
      if (selectedVehicle != null && appStore.isVehicleOrder != 0) {
        VehicleData vehicle = vehicleList.firstWhere((element) => element.id == selectedVehicle);
        totalAmount += vehicle.price.validate();
      }

      /// calculate extra charges
      cityData!.extraCharges!.forEach((element) {
        totalExtraCharge += countExtraCharge(totalAmount: totalAmount, charges: element.charges!, chargesType: element.chargesType!);
      });

      /// All Charges
      totalAmount = double.parse((totalAmount + totalExtraCharge).toStringAsFixed(digitAfterDecimal));*/
    });
  }

  extraChargesList() {
    print("fixed charge ${cityData!.fixedCharges.validate()}");
    extraChargeList.clear();
    extraChargeList.add(ExtraChargeRequestModel(key: FIXED_CHARGES, value: cityData!.fixedCharges, valueType: ""));
    extraChargeList.add(ExtraChargeRequestModel(key: MIN_DISTANCE, value: cityData!.minDistance, valueType: ""));
    extraChargeList.add(ExtraChargeRequestModel(key: MIN_WEIGHT, value: cityData!.minWeight, valueType: ""));
    extraChargeList.add(ExtraChargeRequestModel(key: PER_DISTANCE_CHARGE, value: cityData!.perDistanceCharges, valueType: ""));
    extraChargeList.add(ExtraChargeRequestModel(key: PER_WEIGHT_CHARGE, value: cityData!.perWeightCharges, valueType: ""));
    cityData!.extraCharges!.forEach((element) {
      extraChargeList.add(ExtraChargeRequestModel(key: element.title!.toLowerCase().replaceAll(' ', "_"), value: element.charges, valueType: element.chargesType));
    });
  }

  createOrderApiCall(String orderStatus) async {
    List<Map<String, String>> packaging_symbols = [];
    selectedPackingSymbols.map((item) {
      packaging_symbols.add({'key': item["key"]!, 'title': item['title']!});
    }).toList();
    extraChargeList.clear();
    if (totalAmountResponse!.extraCharges != null) {
      totalAmountResponse!.extraCharges!.forEach((element) {
        extraChargeList.add(ExtraChargeRequestModel(key: element.title!.toLowerCase().replaceAll(' ', "_"), value: element.charges, valueType: element.chargesType));
      });
    }
    appStore.setLoading(true);
    Map req = {
      "id": "",
      "client_id": getIntAsync(USER_ID).toString(),
      "date": DateTime.now().toString(),
      "country_id": selectedCountry.toString(),
      "city_id": selectedCity.toString(),
      //   if (appStore.isShowVehicle == 1) "vehicle_id": selectedVehicle.toString(),
      if (!selectedVehicle.toString().isEmptyOrNull && selectedVehicle != 0 && appStore.isShowVehicle == 1) "vehicle_id": selectedVehicle.toString(),
      if (vehicleCharge != 0.0) "vehicle_charge": vehicleCharge,
      "pickup_point": {
        "start_time": (!isDeliverNow && pickFromDateTime != null) ? pickFromDateTime.toString() : DateTime.now().toString(),
        "end_time": (!isDeliverNow && pickToDateTime != null) ? pickToDateTime.toString() : null,
        "address": pickAddressCont.text,
        "latitude": pickLat,
        "longitude": pickLong,
        "name": pickPersonNameCont.text,
        "instruction": pickInstructionCont.text,
        "description": pickDesCont.text,
        "contact_number": '$pickupCountryCode ${pickPhoneCont.text.trim()}'
      },
      "delivery_point": {
        "start_time": (!isDeliverNow && deliverFromDateTime != null) ? deliverFromDateTime.toString() : null,
        "end_time": (!isDeliverNow && deliverToDateTime != null) ? deliverToDateTime.toString() : null,
        "address": deliverAddressCont.text,
        "latitude": deliverLat,
        "longitude": deliverLong,
        "name": deliverPersonNameCont.text,
        "instruction": deliverInstructionCont.text,
        "description": deliverDesCont.text,
        "contact_number": '$deliverCountryCode ${deliverPhoneCont.text.trim()}',
      },
      "packaging_symbols": packaging_symbols,
      "extra_charges": extraChargeList,
      "parcel_type": parcelTypeCont.text,
      "total_weight": double.tryParse(weightController.text),
      "total_distance": totalDistance.toStringAsFixed(digitAfterDecimal),
      "payment_collect_from": paymentCollectFrom,
      "status": orderStatus,
      "payment_type": PAYMENT_TYPE_CASH,
      "payment_status": "",
      "fixed_charges": totalAmountResponse!.fixedAmount!.toDouble(),
      "parent_order_id": "",
      "total_amount": totalAmount,
      "weight_charge": totalAmountResponse!.weightAmount!.toDouble(),
      "distance_charge": totalAmountResponse!.distanceAmount!.toDouble(),
      "total_parcel": totalParcelController.text.toInt(),
      "insurance_charge": insuranceAmount,
    };

    print("====== insurance amount ${insuranceAmount}");
    appStore.setLoading(true);
    await createOrder(req).then((value) {
      appStore.setLoading(false);
      toast(value.message);
      Navigator.pop(context);
      launchScreen(context, DashboardScreen(), isNewTask: true);
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  Future<List<Predictions>> getPlaceAutoCompleteApiCall(String text) async {
    List<Predictions> list = [];
    await placeAutoCompleteApi(searchText: text, language: appStore.selectedLanguage, countryCode: getCountryCode()).then((value) {
      list = value.predictions ?? [];
    }).catchError((e) {
      throw e.toString();
    });
    return list;
  }

  Future<PlaceIdDetailModel?> getPlaceIdDetailApiCall({required String placeId}) async {
    PlaceIdDetailModel? detailModel;
    await getPlaceDetail(placeId: placeId).then((value) {
      detailModel = value;
    }).catchError((e) {
      throw e.toString();
    });
    return detailModel;
  }

  void setMapFitToCenter(Set<Polyline> p) {
    double minLat = p.first.points.first.latitude;
    double minLong = p.first.points.first.longitude;
    double maxLat = p.first.points.first.latitude;
    double maxLong = p.first.points.first.longitude;

    p.forEach((poly) {
      poly.points.forEach((point) {
        if (point.latitude < minLat) minLat = point.latitude;
        if (point.latitude > maxLat) maxLat = point.latitude;
        if (point.longitude < minLong) minLong = point.longitude;
        if (point.longitude > maxLong) maxLong = point.longitude;
      });
    });
    googleMapController?.animateCamera(CameraUpdate.newLatLngBounds(LatLngBounds(southwest: LatLng(minLat, minLong), northeast: LatLng(maxLat, maxLong)), 20));
  }

  setPolylines() async {
    PolylineResult result =
        await polylinePoints.getRouteBetweenCoordinates(googleApiKey: googleMapAPIKey, request: PolylineRequest(origin: PointLatLng(pickLat.toDouble(), pickLong.toDouble()), destination: PointLatLng(deliverLat.toDouble(), deliverLong.toDouble()), mode: TravelMode.driving));
    if (result.points.isNotEmpty) {
      result.points.forEach((PointLatLng point) {
        polylineCoordinates.add(LatLng(point.latitude, point.longitude));
      });
    } else {
      print("--address not found ---");
    }
    setState(() {
      Polyline polyline = Polyline(polylineId: PolylineId("poly"), color: Color.fromARGB(255, 40, 122, 198), width: 5, points: polylineCoordinates);
      _polylines.add(polyline);
    });
  }

  Widget rowWidget({required String title, required String value}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(title, style: secondaryTextStyle()),
        16.width,
        Text(value, style: boldTextStyle(size: 14), maxLines: 3, textAlign: TextAlign.end, overflow: TextOverflow.ellipsis).expand(),
      ],
    );
  }

  Widget addressComponent({required String title, required String address, required String phoneNumber}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: boldTextStyle()),
        8.height,
        Container(
          width: context.width(),
          padding: EdgeInsets.all(16),
          decoration: boxDecorationWithRoundedCorners(
            borderRadius: BorderRadius.circular(defaultRadius),
            border: Border.all(color: primaryColor.withOpacity(0.2)),
            backgroundColor: Colors.transparent,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(address, style: primaryTextStyle()),
              8.height.visible(address.isNotEmpty),
              Row(
                children: [
                  Icon(Icons.call, size: 14),
                  8.width,
                  Text(phoneNumber, style: secondaryTextStyle()).visible(phoneNumber.isNotEmpty),
                ],
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget progressIndicator() {
    return CircularPercentIndicator(
      radius: 20.0,
      lineWidth: 2.0,
      percent: ((selectedTabIndex + 1) / 5) > 1 ? 1 : (selectedTabIndex + 1) / 5,
      animation: true,
      center: Text((selectedTabIndex + 1).toInt().toString() + " /5", style: boldTextStyle(size: 11, color: Colors.white)),
      backgroundColor: Colors.white.withOpacity(0.25),
      progressColor: Colors.white,
    );
  }

  Widget createOrderWidget1() {
    return Observer(builder: (context) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              scheduleOptionWidget(context, isDeliverNow, ic_clock, language.deliverNow).onTap(() {
                isDeliverNow = true;
                setState(() {});
              }).expand(),
              16.width,
              scheduleOptionWidget(context, !isDeliverNow, ic_schedule, language.schedule).onTap(() {
                isDeliverNow = false;
                setState(() {});
              }).expand(),
            ],
          ),
          16.height,
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(language.pickTime, style: boldTextStyle()),
              16.height,
              Container(
                padding: EdgeInsets.all(16),
                decoration: BoxDecoration(border: Border.all(color: borderColor, width: appStore.isDarkMode ? 0.2 : 1), borderRadius: BorderRadius.circular(defaultRadius)),
                child: Column(
                  children: [
                    Theme(
                      data: ThemeData(
                        primarySwatch: Colors.grey,
                        splashColor: Colors.black,
                        textTheme: TextTheme(
                          titleMedium: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                          labelLarge: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                        ),
                        colorScheme: ColorScheme.light(
                            primary: primaryColor,
                            onSecondary: appStore.isDarkMode ? white : Colors.black,
                            onPrimary: appStore.isDarkMode ? white : Colors.white,
                            surface: appStore.isDarkMode ? Colors.grey : borderColor,
                            onSurface: appStore.isDarkMode ? white : Colors.black,
                            secondary: appStore.isDarkMode ? white : Colors.black),
                      ),
                      child: DateTimePicker(
                        controller: pickDateController,
                        type: DateTimePickerType.date,
                        initialDate: DateTime.now(),
                        firstDate: DateTime.now(),
                        lastDate: DateTime(2050),
                        onChanged: (value) {
                          pickDate = DateTime.parse(value);
                          deliverDate = null;
                          deliverDateController.clear();
                          setState(() {});
                        },
                        validator: (value) {
                          if (value!.isEmpty) return language.field_required_msg;
                          return null;
                        },
                        decoration: commonInputDecoration(suffixIcon: Icons.calendar_today, hintText: language.date),
                      ),
                    ),
                    16.height,
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Theme(
                          data: ThemeData(
                            primarySwatch: Colors.grey,
                            splashColor: Colors.black,
                            textTheme: TextTheme(
                              titleMedium: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                              labelLarge: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                            ),
                            colorScheme: ColorScheme.light(
                                primary: primaryColor,
                                onSecondary: appStore.isDarkMode ? white : Colors.black,
                                onPrimary: appStore.isDarkMode ? white : Colors.white,
                                surface: appStore.isDarkMode ? Colors.grey : borderColor,
                                onSurface: appStore.isDarkMode ? white : Colors.black,
                                secondary: appStore.isDarkMode ? white : Colors.black),
                          ),
                          child: DateTimePicker(
                            controller: pickFromTimeController,
                            type: DateTimePickerType.time,
                            onChanged: (value) {
                              pickFromTime = TimeOfDay.fromDateTime(DateFormat('hh:mm').parse(value));
                              setState(() {});
                            },
                            validator: (value) {
                              if (value.validate().isEmpty) return language.field_required_msg;
                              return null;
                            },
                            decoration: commonInputDecoration(suffixIcon: Icons.access_time, hintText: language.from),
                          ).expand(),
                        ),
                        16.width,
                        Theme(
                          data: ThemeData(
                            primarySwatch: Colors.grey,
                            splashColor: Colors.black,
                            textTheme: TextTheme(
                              titleMedium: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                              labelLarge: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                            ),
                            colorScheme: ColorScheme.light(
                                primary: primaryColor,
                                onSecondary: appStore.isDarkMode ? white : Colors.black,
                                onPrimary: appStore.isDarkMode ? white : Colors.white,
                                surface: appStore.isDarkMode ? Colors.grey : borderColor,
                                onSurface: appStore.isDarkMode ? white : Colors.black,
                                secondary: appStore.isDarkMode ? white : Colors.black),
                          ),
                          child: DateTimePicker(
                            controller: pickToTimeController,
                            type: DateTimePickerType.time,
                            onChanged: (value) {
                              pickToTime = TimeOfDay.fromDateTime(DateFormat('hh:mm').parse(value));
                              setState(() {});
                            },
                            validator: (value) {
                              if (value.validate().isEmpty) return language.field_required_msg;
                              double fromTimeInHour = pickFromTime!.hour + pickFromTime!.minute / 60;
                              double toTimeInHour = pickToTime!.hour + pickToTime!.minute / 60;
                              double difference = toTimeInHour - fromTimeInHour;
                              if (difference <= 0) {
                                return language.endStartTimeValidationMsg;
                              }
                              return null;
                            },
                            decoration: commonInputDecoration(suffixIcon: Icons.access_time, hintText: language.to),
                          ).expand(),
                        )
                      ],
                    ),
                  ],
                ),
              ),
              16.height,
              Text(language.deliverTime, style: boldTextStyle()),
              16.height,
              Container(
                padding: EdgeInsets.all(16),
                decoration: BoxDecoration(
                  border: Border.all(color: borderColor, width: appStore.isDarkMode ? 0.2 : 1),
                  borderRadius: BorderRadius.circular(defaultRadius),
                ),
                child: Column(
                  children: [
                    Theme(
                      data: ThemeData(
                        primarySwatch: Colors.grey,
                        splashColor: Colors.black,
                        textTheme: TextTheme(
                          titleMedium: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                          labelLarge: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                        ),
                        colorScheme: ColorScheme.light(
                            primary: primaryColor,
                            onSecondary: appStore.isDarkMode ? white : Colors.black,
                            onPrimary: appStore.isDarkMode ? white : Colors.white,
                            surface: appStore.isDarkMode ? Colors.grey : borderColor,
                            onSurface: appStore.isDarkMode ? white : Colors.black,
                            secondary: appStore.isDarkMode ? white : Colors.black),
                      ),
                      child: DateTimePicker(
                        controller: deliverDateController,
                        type: DateTimePickerType.date,
                        initialDate: pickDate ?? DateTime.now(),
                        firstDate: pickDate ?? DateTime.now(),
                        lastDate: DateTime(2050),
                        onChanged: (value) {
                          deliverDate = DateTime.parse(value);
                          setState(() {});
                        },
                        validator: (value) {
                          if (value!.isEmpty) return language.field_required_msg;
                          return null;
                        },
                        decoration: commonInputDecoration(suffixIcon: Icons.calendar_today, hintText: language.date),
                      ),
                    ),
                    16.height,
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Theme(
                          data: ThemeData(
                            primarySwatch: Colors.grey,
                            splashColor: Colors.black,
                            textTheme: TextTheme(
                              titleMedium: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                              labelLarge: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                            ),
                            colorScheme: ColorScheme.light(
                                primary: primaryColor,
                                onSecondary: appStore.isDarkMode ? white : Colors.black,
                                onPrimary: appStore.isDarkMode ? white : Colors.white,
                                surface: appStore.isDarkMode ? Colors.grey : borderColor,
                                onSurface: appStore.isDarkMode ? white : Colors.black,
                                secondary: appStore.isDarkMode ? white : Colors.black),
                          ),
                          child: DateTimePicker(
                            controller: deliverFromTimeController,
                            type: DateTimePickerType.time,
                            onChanged: (value) {
                              deliverFromTime = TimeOfDay.fromDateTime(DateFormat('hh:mm').parse(value));
                              setState(() {});
                            },
                            validator: (value) {
                              if (value.validate().isEmpty) return language.field_required_msg;
                              return null;
                            },
                            decoration: commonInputDecoration(suffixIcon: Icons.access_time, hintText: language.from),
                          ).expand(),
                        ),
                        16.width,
                        Theme(
                          data: ThemeData(
                            primarySwatch: Colors.grey,
                            splashColor: Colors.black,
                            textTheme: TextTheme(
                              titleMedium: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                              labelLarge: TextStyle(color: appStore.isDarkMode ? white : Colors.black),
                            ),
                            colorScheme: ColorScheme.light(
                                primary: primaryColor,
                                onSecondary: appStore.isDarkMode ? white : Colors.black,
                                onPrimary: appStore.isDarkMode ? white : Colors.white,
                                surface: appStore.isDarkMode ? Colors.grey : borderColor,
                                onSurface: appStore.isDarkMode ? white : Colors.black,
                                secondary: appStore.isDarkMode ? white : Colors.black),
                          ),
                          child: DateTimePicker(
                            controller: deliverToTimeController,
                            type: DateTimePickerType.time,
                            onChanged: (value) {
                              deliverToTime = TimeOfDay.fromDateTime(DateFormat('hh:mm').parse(value));
                              setState(() {});
                            },
                            validator: (value) {
                              if (value!.isEmpty) return language.field_required_msg;
                              double fromTimeInHour = deliverFromTime!.hour + deliverFromTime!.minute / 60;
                              double toTimeInHour = deliverToTime!.hour + deliverToTime!.minute / 60;
                              double difference = toTimeInHour - fromTimeInHour;
                              if (difference < 0) {
                                return language.endStartTimeValidationMsg;
                              }
                              return null;
                            },
                            decoration: commonInputDecoration(suffixIcon: Icons.access_time, hintText: language.to),
                          ).expand(),
                        )
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ).visible(!isDeliverNow),
          16.height,
          // Text(language.weight, style: boldTextStyle()),
          // 8.height,

          Row(
            children: [
              Text(language.weight, style: primaryTextStyle()).expand(),
              Container(
                decoration: BoxDecoration(border: Border.all(color: borderColor, width: appStore.isDarkMode ? 0.2 : 1), borderRadius: BorderRadius.circular(defaultRadius)),
                child: IntrinsicHeight(
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.remove, color: appStore.isDarkMode ? Colors.white : Colors.grey).paddingAll(12).onTap(() {
                        if (weightController.text.toDouble() > 1) {
                          weightController.text = (weightController.text.toDouble() - 1).toString();
                        }
                      }),
                      VerticalDivider(thickness: 1, color: context.dividerColor),
                      Container(
                        width: 50,
                        child: AppTextField(
                          controller: weightController,
                          textAlign: TextAlign.center,
                          maxLength: 5,
                          textFieldType: TextFieldType.PHONE,
                          decoration: InputDecoration(
                            counterText: '',
                            focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: primaryColor)),
                            border: InputBorder.none,
                          ),
                        ),
                      ),
                      VerticalDivider(thickness: 1, color: context.dividerColor),
                      Icon(Icons.add, color: appStore.isDarkMode ? Colors.white : Colors.grey).paddingAll(12).onTap(() {
                        weightController.text = (weightController.text.toDouble() + 1).toString();
                      }),
                    ],
                  ),
                ),
              ),
            ],
          ),
          16.height,
          // Text(language.numberOfParcels, style: boldTextStyle()),
          // 8.height,
          Row(
            children: [
              Text(language.numberOfParcels, style: primaryTextStyle()).expand(),
              Container(
                decoration: BoxDecoration(border: Border.all(color: borderColor, width: appStore.isDarkMode ? 0.2 : 1), borderRadius: BorderRadius.circular(defaultRadius)),
                child: IntrinsicHeight(
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.remove, color: appStore.isDarkMode ? Colors.white : Colors.grey).paddingAll(12).onTap(() {
                        if (totalParcelController.text.toInt() > 1) {
                          totalParcelController.text = (totalParcelController.text.toInt() - 1).toString();
                        }
                      }),
                      VerticalDivider(thickness: 1, color: context.dividerColor),
                      Container(
                        width: 50,
                        child: AppTextField(
                          controller: totalParcelController,
                          textAlign: TextAlign.center,
                          maxLength: 2,
                          textFieldType: TextFieldType.PHONE,
                          decoration: InputDecoration(
                            counterText: '',
                            focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: primaryColor)),
                            border: InputBorder.none,
                          ),
                        ),
                      ),
                      VerticalDivider(thickness: 1, color: context.dividerColor),
                      Icon(Icons.add, color: appStore.isDarkMode ? Colors.white : Colors.grey).paddingAll(12).onTap(() {
                        totalParcelController.text = (totalParcelController.text.toInt() + 1).toString();
                      }),
                    ],
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 16),
          Text(language.country, style: primaryTextStyle()),
          SizedBox(height: 16),
          DropdownButtonFormField<int>(
            value: selectedCountry,
            decoration: commonInputDecoration(),
            dropdownColor: Theme.of(context).cardColor,
            style: primaryTextStyle(),
            items: countryList.map<DropdownMenuItem<int>>((item) {
              return DropdownMenuItem(
                value: item.id,
                child: Text(item.name ?? ''),
              );
            }).toList(),
            onChanged: (value) {
              selectedCountry = value!;
              countryData = countryList.firstWhere((element) => element.id == selectedCountry);
              selectedCity = null;
              cityData = null;
              pickAddressCont.clear();
              pickLat = null;
              pickLong = null;
              deliverAddressCont.clear();
              deliverLat = null;
              deliverLong = null;
              vehicleList.clear();
              getCityApiCall();
              getVehicleApiCall();
              setState(() {});
            },
            validator: (value) {
              if (selectedCountry == null) return language.field_required_msg;
              return null;
            },
          ),
          SizedBox(height: 16),
          Text(language.city, style: primaryTextStyle()),
          SizedBox(height: 16),
          DropdownButtonFormField<int>(
            value: selectedCity,
            decoration: commonInputDecoration(),
            dropdownColor: Theme.of(context).cardColor,
            style: primaryTextStyle(),
            items: cityList.map<DropdownMenuItem<int>>((item) {
              return DropdownMenuItem(
                value: item.id,
                child: Text(item.name ?? ''),
              );
            }).toList(),
            onChanged: (value) {
              selectedCity = value!;
              cityData = cityList.firstWhere((element) => element.id == selectedCity);
              getCityDetailApiCall();
              getVehicleApiCall();
              extraChargesList();
              setState(() {});
            },
            validator: (value) {
              if (selectedCity == null) return language.field_required_msg;
              return null;
            },
          ),
          16.height,
          Text(language.parcelType, style: primaryTextStyle()),
          8.height,
          AppTextField(
            controller: parcelTypeCont,
            textFieldType: TextFieldType.OTHER,
            decoration: commonInputDecoration(),
            validator: (value) {
              if (value!.isEmpty) return language.fieldRequiredMsg;
              return null;
            },
          ),
          8.height,
          Wrap(
            spacing: 8,
            runSpacing: 0,
            children: parcelTypeList.map((item) {
              return Chip(
                backgroundColor: context.scaffoldBackgroundColor,
                label: Text(item.label!, style: secondaryTextStyle()),
                elevation: 0,
                labelStyle: primaryTextStyle(color: Colors.grey),
                padding: EdgeInsets.zero,
                labelPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 0),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(defaultRadius),
                  side: BorderSide(color: borderColor, width: appStore.isDarkMode ? 0.2 : 1),
                ),
              ).onTap(() {
                parcelTypeCont.text = item.label!;
                setState(() {});
              });
            }).toList(),
          ),
          8.height,
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text("Labels", style: primaryTextStyle()),
              Icon(Icons.info, color: appStore.isDarkMode ? Colors.white.withOpacity(0.7) : primaryColor).onTap(() {
                PackagingSymbolsInfo().launch(context);
              })
            ],
          ),
          8.height,
          Wrap(
            spacing: 10,
            runSpacing: 10,
            children: packingSymbolsItems.map((item) {
              bool isSelected = selectedPackingSymbols.contains(item);
              return Container(
                width: 70,
                decoration: boxDecorationWithRoundedCorners(border: Border.all(color: borderColor)),
                child: Stack(
                  children: [
                    Image.asset(item['image']!, width: 24, height: 24, color: appStore.isDarkMode ? Colors.white.withOpacity(0.7) : primaryColor).center().paddingAll(10),
                    if (isSelected)
                      Positioned(
                        top: 0,
                        right: 0,
                        child: Icon(
                          Icons.check_circle,
                          color: Colors.green,
                          size: 16,
                        ),
                      ),
                  ],
                ),
              ).onTap(() {
                setState(() {
                  if (isSelected) {
                    selectedPackingSymbols.remove(item);
                  } else {
                    selectedPackingSymbols.add(item);
                  }
                });

                setState(() {});
              });
            }).toList(),
          )
        ],
      );
    });
  }

  Widget createOrderWidget2() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(language.pickupInfo, style: boldTextStyle()),
        16.height,
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(language.pickupLocation, style: primaryTextStyle()),
            8.height,
            AppTextField(
              controller: pickAddressCont,
              readOnly: true,
              textInputAction: TextInputAction.next,
              nextFocus: pickPhoneFocus,
              textFieldType: TextFieldType.OTHER,
              decoration: commonInputDecoration(suffixIcon: Icons.location_on_outlined),
              validator: (value) {
                if (value!.isEmpty) return language.fieldRequiredMsg;
                if (pickLat == null || pickLong == null) return language.pleaseSelectValidAddress;
                return null;
              },
              onTap: () {
                showModalBottomSheet(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(defaultRadius))),
                  context: context,
                  builder: (context) {
                    return PickAddressBottomSheet(
                      onPick: (address) {
                        pickAddressCont.text = address.placeAddress ?? "";
                        pickLat = address.latitude.toString();
                        pickLong = address.longitude.toString();
                        setState(() {});
                      },
                    );
                  },
                );
              },
            ),
            16.height,
            Text(language.contactNumber, style: primaryTextStyle()),
            8.height,
            AppTextField(
              controller: pickPhoneCont,
              focus: pickPhoneFocus,
              nextFocus: pickDesFocus,
              textFieldType: TextFieldType.PHONE,
              decoration: commonInputDecoration(
                suffixIcon: Icons.phone,
                prefixIcon: IntrinsicHeight(
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      CountryCodePicker(
                        initialSelection: pickupCountryCode,
                        showCountryOnly: false,
                        dialogSize: Size(context.width() - 60, context.height() * 0.6),
                        showFlag: true,
                        showFlagDialog: true,
                        showOnlyCountryWhenClosed: false,
                        alignLeft: false,
                        textStyle: primaryTextStyle(),
                        dialogBackgroundColor: Theme.of(context).cardColor,
                        barrierColor: Colors.black12,
                        dialogTextStyle: primaryTextStyle(),
                        searchDecoration: InputDecoration(
                          iconColor: Theme.of(context).dividerColor,
                          enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Theme.of(context).dividerColor)),
                          focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: primaryColor)),
                        ),
                        searchStyle: primaryTextStyle(),
                        onInit: (c) {
                          pickupCountryCode = c!.dialCode!;
                        },
                        onChanged: (c) {
                          pickupCountryCode = c.dialCode!;
                        },
                      ),
                      VerticalDivider(color: Colors.grey.withOpacity(0.5)),
                    ],
                  ),
                ),
              ),
              textInputAction: TextInputAction.next,
              validator: (value) {
                if (value!.trim().isEmpty) return language.fieldRequiredMsg;
                //  if (value.trim().length < minContactLength || value.trim().length > maxContactLength) return language.contactLength;
                return null;
              },
              inputFormatters: [
                FilteringTextInputFormatter.digitsOnly,
              ],
            ),
          ],
        ),
        16.height,
        Text(language.pickupPersonName, style: primaryTextStyle()),
        8.height,
        AppTextField(
          controller: pickPersonNameCont,
          textInputAction: TextInputAction.done,
          focus: pickPersonNameFocus,
          textFieldType: TextFieldType.NAME,
          decoration: commonInputDecoration(),
          validator: (value) {
            if (value!.trim().isEmpty) return language.fieldRequiredMsg;
            return null;
          },
        ),
        16.height,
        Text(language.description, style: primaryTextStyle()),
        8.height,
        TextField(
          style: TextStyle(color: appStore.isDarkMode ? white : black),
          controller: pickDesCont,
          focusNode: pickDesFocus,
          decoration: commonInputDecoration(suffixIcon: Icons.notes),
          textInputAction: TextInputAction.done,
          maxLines: 3,
          minLines: 3,
        ),
        16.height,
        Text(language.pickupInstruction, style: primaryTextStyle()),
        8.height,
        TextField(
          controller: pickInstructionCont,
          focusNode: pickInstructionFocus,
          decoration: commonInputDecoration(suffixIcon: Icons.notes),
          textInputAction: TextInputAction.done,
          maxLines: 2,
          minLines: 2,
        ),
      ],
    );
  }

  Widget createOrderWidget3() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(language.deliveryInformation, style: boldTextStyle()),
        16.height,
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(language.deliveryLocation, style: primaryTextStyle()),
            8.height,
            AppTextField(
              controller: deliverAddressCont,
              readOnly: true,
              textInputAction: TextInputAction.next,
              nextFocus: deliverPhoneFocus,
              textFieldType: TextFieldType.OTHER,
              decoration: commonInputDecoration(suffixIcon: Icons.location_on_outlined),
              validator: (value) {
                if (value!.isEmpty) return language.fieldRequiredMsg;
                if (deliverLat == null || deliverLong == null) return language.pleaseSelectValidAddress;
                return null;
              },
              onTap: () {
                showModalBottomSheet(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(defaultRadius))),
                  context: context,
                  builder: (context) {
                    return PickAddressBottomSheet(
                      onPick: (address) {
                        deliverAddressCont.text = address.placeAddress ?? "";
                        deliverLat = address.latitude.toString();
                        deliverLong = address.longitude.toString();
                        setState(() {});
                      },
                      isPickup: false,
                    );
                  },
                );
              },
            ),
            16.height,
            Text(language.deliveryContactNumber, style: primaryTextStyle()),
            8.height,
            AppTextField(
              controller: deliverPhoneCont,
              textInputAction: TextInputAction.next,
              focus: deliverPhoneFocus,
              nextFocus: deliverDesFocus,
              textFieldType: TextFieldType.PHONE,
              decoration: commonInputDecoration(
                suffixIcon: Icons.phone,
                prefixIcon: IntrinsicHeight(
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      CountryCodePicker(
                        initialSelection: deliverCountryCode,
                        showCountryOnly: false,
                        dialogSize: Size(context.width() - 60, context.height() * 0.6),
                        showFlag: true,
                        showFlagDialog: true,
                        showOnlyCountryWhenClosed: false,
                        alignLeft: false,
                        textStyle: primaryTextStyle(),
                        dialogBackgroundColor: Theme.of(context).cardColor,
                        barrierColor: Colors.black12,
                        dialogTextStyle: primaryTextStyle(),
                        searchDecoration: InputDecoration(
                          iconColor: Theme.of(context).dividerColor,
                          enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Theme.of(context).dividerColor)),
                          focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: primaryColor)),
                        ),
                        searchStyle: primaryTextStyle(),
                        onInit: (c) {
                          deliverCountryCode = c!.dialCode!;
                        },
                        onChanged: (c) {
                          deliverCountryCode = c.dialCode!;
                        },
                      ),
                      VerticalDivider(color: Colors.grey.withOpacity(0.5)),
                    ],
                  ),
                ),
              ),
              validator: (value) {
                if (value!.trim().isEmpty) return language.fieldRequiredMsg;
                // if (value.trim().length < minContactLength || value.trim().length > maxContactLength) return language.contactLength;
                return null;
              },
              inputFormatters: [
                FilteringTextInputFormatter.digitsOnly,
              ],
            ),
          ],
        ),
        16.height,
        Text(language.deliveryPersonName, style: primaryTextStyle()),
        8.height,
        AppTextField(
          controller: deliverPersonNameCont,
          textInputAction: TextInputAction.done,
          focus: deliveryPesonNameFocus,
          textFieldType: TextFieldType.NAME,
          decoration: commonInputDecoration(),
          validator: (value) {
            if (value!.trim().isEmpty) return language.fieldRequiredMsg;
            return null;
          },
        ),
        16.height,
        Text(language.deliveryDescription, style: primaryTextStyle()),
        8.height,
        TextField(
          style: TextStyle(color: appStore.isDarkMode ? white : black),
          controller: deliverDesCont,
          focusNode: deliverDesFocus,
          decoration: commonInputDecoration(suffixIcon: Icons.notes),
          textInputAction: TextInputAction.done,
          maxLines: 3,
          minLines: 3,
        ),
        16.height,
        Text(language.deliveryInstruction, style: primaryTextStyle()),
        8.height,
        TextField(
          controller: deliverInstructionCont,
          focusNode: deliveryInstructionFocus,
          decoration: commonInputDecoration(suffixIcon: Icons.notes),
          textInputAction: TextInputAction.done,
          maxLines: 2,
          minLines: 2,
        ),
      ],
    );
  }

  void onMapCreated(GoogleMapController controller) async {
    setState(() {
      googleMapController = controller;
      setPolylines().then((_) => setMapFitToCenter(_polylines));
    });
  }

  Widget createOrderWidget4() {
    return Column(
      children: [
        markers.isNotEmpty
            ? Container(
                width: context.width(),
                height: context.height() * 0.67,
                child: Stack(
                  children: [
                    GoogleMap(
                      markers: markers.map((e) => e).toSet(),
                      polylines: _polylines,
                      mapType: MapType.normal,
                      cameraTargetBounds: CameraTargetBounds.unbounded,
                      initialCameraPosition: CameraPosition(
                          // bearing: 192.8334901395799,
                          target: LatLng(pickLat.toDouble(), pickLong.toDouble()),
                          zoom: 12),
                      onMapCreated: onMapCreated,
                      tiltGesturesEnabled: true,
                      scrollGesturesEnabled: true,
                      zoomGesturesEnabled: true,
                      // trafficEnabled: true,
                    )
                  ],
                ),
              )
            : loaderWidget(),
      ],
    );
  }

  Widget createOrderWidget5() {
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(language.packageInformation, style: boldTextStyle()),
          8.height,
          Container(
            padding: EdgeInsets.all(16),
            decoration: boxDecorationWithRoundedCorners(
              borderRadius: BorderRadius.circular(defaultRadius),
              border: Border.all(color: primaryColor.withOpacity(0.2)),
              backgroundColor: Colors.transparent,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                rowWidget(title: language.parcelType, value: parcelTypeCont.text),
                8.height,
                rowWidget(title: language.weight, value: '${weightController.text} ${countryData!.weightType}'),
                8.height,
                rowWidget(title: language.numberOfParcels, value: '${totalParcelController.text}'),
              ],
            ),
          ),
          16.height,
          addressComponent(title: language.pickupLocation, address: pickAddressCont.text, phoneNumber: '$pickupCountryCode ${pickPhoneCont.text.trim()}'),
          16.height,
          addressComponent(title: language.deliveryLocation, address: deliverAddressCont.text, phoneNumber: '$deliverCountryCode ${deliverPhoneCont.text.trim()}'),
          16.height,
          Visibility(
            visible: appStore.isVehicleOrder != 0,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                16.height,
                Text(language.select_vehicle, style: primaryTextStyle()),
                8.height,
                DropdownButtonFormField<int>(
                  isExpanded: true,
                  value: selectedVehicle,
                  decoration: commonInputDecoration(),
                  dropdownColor: Theme.of(context).cardColor,
                  style: primaryTextStyle(),
                  items: vehicleList.map<DropdownMenuItem<int>>((item) {
                    return DropdownMenuItem(
                      value: item.id,
                      child: Row(
                        children: [
                          commonCachedNetworkImage(item.vehicleImage.validate(), height: 40, width: 40),
                          SizedBox(width: 16),
                          Text("${item.title.validate()} (${item.price.validate()})", style: primaryTextStyle()),
                        ],
                      ),
                    );
                  }).toList(),
                  onChanged: (value) {
                    selectedVehicle = value;
                    setState(() {});
                    getTotalForOrder();
                  },
                  validator: (value) {
                    if (selectedVehicle == null) return language.field_required_msg;
                    return null;
                  },
                ),
              ],
            ),
          ),
          16.height,
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text("Insurance", style: boldTextStyle()),
              Icon(
                Icons.info,
              ).onTap(() {
                InsuranceDetailsScreen(appStore.insuranceDescription).launch(context);
              }).visible(appStore.insuranceDescription != null && !appStore.insuranceDescription.isEmptyOrNull)
            ],
          ).visible(appStore.isInsuranceAllowed == "1"),
          16.height.visible(appStore.isInsuranceAllowed == "1"),
          InsuranceOptionsWidget(0, language.addCourierInsurance ).visible(appStore.isInsuranceAllowed == "1"),
          16.height.visible(appStore.isInsuranceAllowed == "1"),
          InsuranceOptionsWidget(1,language.iWillRiskIt ).visible(appStore.isInsuranceAllowed == "1"),
          16.height,
          if (appStore.isInsuranceAllowed == "1" && insuranceSelectedOption == 0) ...[
            12.height,
            Text(language.enterApproxParcelValue, style: primaryTextStyle()),
            9.height,
            AppTextField(
              controller: insuranceAmountController,
              textFieldType: TextFieldType.PHONE,
              decoration: commonInputDecoration(),
              onChanged: (val) {
                if (!val.isEmptyOrNull) {
                  insuranceAmount = (double.parse(val) * appStore.insurancePercentage.toDouble()) / 100;
                  print("--------------insurance amount${insuranceAmount}-----------val${val}-----------insurance "
                      "percentage-${appStore.insurancePercentage}");
                  // getTotalAmount();
                  getTotalForOrder();
                  setState(() {});
                } else {
                  insuranceAmount = 0;
                  setState(() {});
                }
              },
              validator: (value) {
                if (value!.isEmpty) return language.fieldRequiredMsg;
                return null;
              },
            ),
            16.height,
          ],
          if (totalAmountResponse != null)
            OrderAmountDataWidget(
                fixedAmount: totalAmountResponse!.fixedAmount!.toDouble(),
                distanceAmount: totalAmountResponse!.distanceAmount!.toDouble(),
                extraCharges: totalAmountResponse!.extraCharges!,
                vehicleAmount: totalAmountResponse!.vehicleAmount!.toDouble(),
                insuranceAmount: insuranceAmount.toDouble(),
                diffWeight: totalAmountResponse!.diffWeight!.toDouble(),
                diffDistance: totalAmountResponse!.diffDistance!.toDouble(),
                totalAmount: totalAmountResponse!.totalAmount!.toDouble(),
                weightAmount: totalAmountResponse!.weightAmount!.toDouble(),
                perWeightCharge: cityData!.perWeightCharges!.toDouble(),
                perKmCityDataCharge: cityData!.perDistanceCharges!.toDouble(),
                perkmVehiclePrice: vehicleList.firstWhere((element) => element.id == selectedVehicle).perKmCharge!.toDouble(),
                baseTotal: totalAmountResponse!.baseTotal!.toDouble()),
          16.height,
          Text(language.payment, style: boldTextStyle()),
          16.height,
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(language.paymentCollectFrom, style: boldTextStyle()),
              16.width,
              DropdownButtonFormField<String>(
                isExpanded: true,
                isDense: true,
                value: paymentCollectFrom,
                decoration: commonInputDecoration(),
                items: [
                  DropdownMenuItem(value: PAYMENT_ON_PICKUP, child: Text(language.pickupLocation, style: primaryTextStyle(), maxLines: 1)),
                  DropdownMenuItem(value: PAYMENT_ON_DELIVERY, child: Text(language.deliveryLocation, style: primaryTextStyle(), maxLines: 1)),
                ],
                onChanged: (value) {
                  paymentCollectFrom = value!;
                  setState(() {});
                },
              ).expand(),
            ],
          ),
          30.height,
        ],
      ),
    );
  }

  Widget InsuranceOptionsWidget(int value, String text) {
    return Container(
      decoration: boxDecorationWithRoundedCorners(backgroundColor: insuranceSelectedOption == value ? primaryColor : Colors.grey.withOpacity(0.1)),
      child: Row(
        children: [
          Radio<int>(
            value: value,
            groupValue: insuranceSelectedOption,
            onChanged: (int? newValue) {
              print("---------${value}");
              // setState(() {
              //   insuranceSelectedOption = newValue!;
              // });
              if (newValue != insuranceSelectedOption) {
                insuranceSelectedOption = value;
                if (insuranceSelectedOption == 0) {
                  insuranceAmountController.clear();
                  //     getTotalAmount();
                } else {
                  insuranceAmount = 0.0;
                  //     getTotalAmount();
                }
                setState(() {});
              }
              getTotalForOrder();
            },
            fillColor: MaterialStateProperty.resolveWith<Color?>(
              (Set<MaterialState> states) {
                if (states.contains(MaterialState.selected)) {
                  return Colors.white;
                }
                return primaryColor;
              },
            ),
            activeColor: Colors.white,
          ),
          SizedBox(width: 8),
          Text(text, style: TextStyle(color: insuranceSelectedOption == value ? Colors.white : primaryColor)).expand(),
          Text(
            insuranceSelectedOption == 0 ? "${printAmount(insuranceAmount)}" : "",
            style: TextStyle(color: Colors.white),
          ).visible(value == 0).paddingOnly(right: 10),
        ],
      ),
    ).onTap(() {
      setState(() {
        insuranceSelectedOption = value;
        if (insuranceSelectedOption == 0) {
          //   getTotalAmount();
        } else {
          insuranceAmount = 0.0;
          insuranceAmountController.clear();
          //    getTotalAmount();
        }
      });
      setState(() {});
    });
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async {
        if (selectedTabIndex == 0) {
          // if (currentBackPressTime == null || now.difference(currentBackPressTime!) > Duration(seconds: 2)) {
          //   currentBackPressTime = now;
          //   toast(language.pressBackAgainToExit);
          //   return false;
          // }
          return true;
        } else {
          selectedTabIndex--;
          setState(() {});
          return false;
        }
      },
      child: Scaffold(
        appBar: appBarWidget(appBarTitleList[selectedTabIndex], actions: [
          Row(
            children: [
              progressIndicator(),
              10.width,
            ],
          ),
        ]),
        body: Stack(
          children: [
            SingleChildScrollView(
              padding: EdgeInsets.only(left: 16, top: 30, right: 16, bottom: 16),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    /*Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(5, (index) {
                        return Container(
                          alignment: Alignment.center,
                          height: selectedTabIndex == index ? 35 : 25,
                          width: selectedTabIndex == index ? 35 : 25,
                          margin: EdgeInsets.symmetric(horizontal: 8),
                          decoration: BoxDecoration(
                              color: selectedTabIndex >= index ? primaryColor : (appStore.isDarkMode ? scaffoldSecondaryDark : borderColor),
                              shape: BoxShape.circle,
                              border: Border.all(color: selectedTabIndex >= index ? primaryColor : (appStore.isDarkMode ? colorPrimaryLight : primaryColor))),
                          child: Text('${index + 1}', style: primaryTextStyle(color: selectedTabIndex >= index ? Colors.white : null)),
                        );
                      }).toList(),
                    ),
                    30.height,*/
                    if (selectedTabIndex == 0) createOrderWidget1(),
                    if (selectedTabIndex == 1) createOrderWidget2(),
                    if (selectedTabIndex == 2) createOrderWidget3(),
                    if (selectedTabIndex == 3) createOrderWidget4(),
                    if (selectedTabIndex == 4) createOrderWidget5(),
                  ],
                ),
              ),
            ),
            Observer(
              builder: (context) => Visibility(visible: appStore.isLoading, child: loaderWidget()),
            ),
          ],
        ),
        bottomNavigationBar: Container(
          padding: EdgeInsets.all(16),
          color: Theme.of(context).scaffoldBackgroundColor,
          child: Row(
            children: [
              if (selectedTabIndex != 0)
                Expanded(
                  child: Padding(
                    padding: EdgeInsets.only(right: 16, left: appStore.selectedLanguage == "ar" ? 2 : 0),
                    child: dialogSecondaryButton(language.previous, () {
                      FocusScope.of(context).requestFocus(new FocusNode());
                      selectedTabIndex--;
                      setState(() {});
                    }),
                  ),
                ),
              Expanded(
                child: Padding(
                  padding: EdgeInsets.only(right: 16, left: appStore.selectedLanguage == "ar" ? 2 : 0),
                  child: dialogPrimaryButton(selectedTabIndex != 4 ? language.next : language.createOrder, () async {
                    FocusScope.of(context).requestFocus(new FocusNode());
                    if (selectedTabIndex == 2) {
                      markers.clear();
                      markers.add(
                        Marker(
                          markerId: MarkerId("1"),
                          position: LatLng(pickLat.toDouble(), pickLong.toDouble()),
                          infoWindow: InfoWindow(title: language.sourceLocation),
                          icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed),
                        ),
                      );


                      markers.add(
                        Marker(
                          markerId: MarkerId("2"),
                          position: LatLng(deliverLat.toDouble(), deliverLong.toDouble()),
                          infoWindow: InfoWindow(title: language.destinationLocation),
                          icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed),
                        ),
                      );
                      setState(() {});
                    }
                    if (selectedTabIndex != 4) {
                      if (_formKey.currentState!.validate()) {
                        Duration difference = Duration();
                        Duration differenceCurrentTime = Duration();
                        if (!isDeliverNow) {
                          pickFromDateTime = pickDate!.add(Duration(hours: pickFromTime!.hour, minutes: pickFromTime!.minute));
                          pickToDateTime = pickDate!.add(Duration(hours: pickToTime!.hour, minutes: pickToTime!.minute));
                          deliverFromDateTime = deliverDate!.add(Duration(hours: deliverFromTime!.hour, minutes: deliverFromTime!.minute));
                          deliverToDateTime = deliverDate!.add(Duration(hours: deliverToTime!.hour, minutes: deliverToTime!.minute));
                          difference = pickFromDateTime!.difference(deliverFromDateTime!);
                          differenceCurrentTime = DateTime.now().difference(pickFromDateTime!);
                        }
                        if( pickFromDateTime != null && pickFromDateTime!.isBefore(DateTime.now().add(Duration(hours: 1)))) return toast(language.timeOneHourAfter);
                        if (differenceCurrentTime.inMinutes > 0) return toast(language.pickupCurrentValidationMsg);
                        if (difference.inMinutes > 0) return toast(language.pickupDeliverValidationMsg);
                        selectedTabIndex++;
                        if (selectedTabIndex == 4) {
                          await getTotalAmount();
                        }
                        setState(() {});
                      }
                    } else {
                      showConfirmDialogCustom(
                        context,
                        title: language.createOrderConfirmation,
                        positiveText: language.yes,
                        primaryColor: primaryColor,
                        negativeText: language.no,
                        onAccept: (v) {
                          createOrderApiCall(ORDER_CREATED);
                        },
                      );
                    }
                  }),
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}
