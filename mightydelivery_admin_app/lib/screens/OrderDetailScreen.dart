import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:internet_file/internet_file.dart';
import 'package:intl/intl.dart';
import 'package:maps_launcher/maps_launcher.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/num_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import 'package:open_file/open_file.dart';
import 'package:path_provider/path_provider.dart';
import 'package:pdfx/pdfx.dart';
import 'package:readmore/readmore.dart';
import 'package:url_launcher/url_launcher.dart';

import '../components/AssignCourierCompany.dart';
import '../components/DeliveryOrderAssignComponent.dart';
import '../components/OrderSummeryWidget.dart';
import '../extensions/app_text_field.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/ExtraChargeRequestModel.dart';
import '../models/OrderDetailModel.dart';
import '../models/OrderHistoryModel.dart';
import '../models/OrderModel.dart';
import '../models/UserModel.dart';
import '../models/VehicleModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/CommonApiCall.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import 'OrderHistoryScreen.dart';
import 'package:http/http.dart' as http;
import 'dart:io';

class OrderDetailScreen extends StatefulWidget {
  final int orderId;
  final OrderModel? orderModel;

  OrderDetailScreen({required this.orderId, this.orderModel});

  @override
  OrderDetailScreenState createState() => OrderDetailScreenState();
}

class OrderDetailScreenState extends State<OrderDetailScreen> {
  OrderModel? orderModel;
  List<OrderHistoryModel> orderHistory = [];
  Payment? payment;
  CourierCompanyDetail? courierCompany;
  VehicleData? vehicleData;
  var userID;

  List<ExtraChargeRequestModel> extraChargeForListType = [];
  List<ExtraChargeRequestModel> list = [];
  ValueNotifier<List<ExtraChargeRequestModel>> myList = ValueNotifier([]);

  bool extraChargeTypeIsList = true;

  List<String> tabList = [language.orderDetail, language.orderHistory];

  int selectedTab = 0;

  UserModel? userData;
  UserModel? deliveryManData;
  TextEditingController courierCompanyCont = TextEditingController();
  TextEditingController trackingIdCont = TextEditingController();

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    await getAllCountryApiCall();
    if (widget.orderModel != null) {
      orderModel = widget.orderModel;
      extraChargeTypeIsList = orderModel!.extraCharges is List<dynamic>;
      if (extraChargeTypeIsList) {
        (orderModel!.extraCharges as List<dynamic>).forEach((element) {
          extraChargeForListType.add(ExtraChargeRequestModel.fromJson(element));
        });
      }
      if (orderModel!.deliveryManId != null) await userDetailApiCall(orderModel!.deliveryManId!);
      if (orderModel!.clientId != null) await userDetailApiCall(orderModel!.clientId!);
      appStore.setLoading(true);
      await orderDetail(orderId: widget.orderId).then((value) async {
        appStore.setLoading(false);
        if (value.courierCompanyDetail != null) {
          courierCompany = value.courierCompanyDetail!;
          courierCompanyCont.text = value.courierCompanyDetail!.name.validate();
          trackingIdCont.text = value.courierCompanyDetail!.trackingId.validate();
        }
        payment = value.payment;
        if (value.data!.extraCharges.runtimeType == List<dynamic>) {
          (value.data!.extraCharges as List<dynamic>).forEach((element) {
            list.add(ExtraChargeRequestModel.fromJson(element));
          });
        }
        if (value.data!.fixedCharges.validate() != 0) {
          list.add(ExtraChargeRequestModel(key: FIXED_CHARGES, value: value.data!.fixedCharges!));
        }
        if(value.data!.cityDetails != null){
          list.add(ExtraChargeRequestModel(key: MIN_DISTANCE, value: value.data!.cityDetails!.minDistance));
          list.add(ExtraChargeRequestModel(key: MIN_WEIGHT, value: value.data!.cityDetails!.minWeight));
          list.add(ExtraChargeRequestModel(key: PER_DISTANCE_CHARGE, value: value.data!.cityDetails!.perDistanceCharges));
          list.add(ExtraChargeRequestModel(key: PER_WEIGHT_CHARGE, value: value.data!.cityDetails!.perWeightCharges));
        }
        myList.value = list;
        print("list length1 ${list.length}");
        print("insurance charge${orderModel!.insuranceCharge.validate()}");
        setState(() {});
      }).catchError((error) {});
      setState(() {});
    } else {
      await orderDetailApiCall();
    }
    LiveStream().on(streamLanguage, (p0) {
      setState(() {});
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  orderDetailApiCall() async {
    appStore.setLoading(true);
    await orderDetail(orderId: widget.orderId).then((value) async {
      if (value.data!.deliveryManId != null) await userDetailApiCall(value.data!.deliveryManId!);
      if (value.data!.clientId != null) await userDetailApiCall(value.data!.clientId!);
      if (value.courierCompanyDetail != null) {
        courierCompany = value.courierCompanyDetail!;
        courierCompanyCont.text = value.courierCompanyDetail!.name.validate();
        trackingIdCont.text = value.courierCompanyDetail!.trackingId.validate();
      }
      appStore.setLoading(false);
      orderModel = value.data!;
      orderHistory = value.orderHistory!;
      payment = value.payment;
      if (value.data!.extraCharges.runtimeType == List<dynamic>) {
        (value.data!.extraCharges as List<dynamic>).forEach((element) {
          list.add(ExtraChargeRequestModel.fromJson(element));
        });
      }
      if (value.data!.fixedCharges.validate() != 0) {
        list.add(ExtraChargeRequestModel(key: FIXED_CHARGES, value: value.data!.fixedCharges!));
      }
      if(value.data!.cityDetails != null){
        list.add(ExtraChargeRequestModel(key: MIN_DISTANCE, value: value.data!.cityDetails!.minDistance));
        list.add(ExtraChargeRequestModel(key: MIN_WEIGHT, value: value.data!.cityDetails!.minWeight));
        list.add(ExtraChargeRequestModel(key: PER_DISTANCE_CHARGE, value: value.data!.cityDetails!.perDistanceCharges));
        list.add(ExtraChargeRequestModel(key: PER_WEIGHT_CHARGE, value: value.data!.cityDetails!.perWeightCharges));
      }
      myList.value = list;
      print("list length ${list.length}");
      extraChargeTypeIsList = orderModel!.extraCharges is List<dynamic>;
      if (extraChargeTypeIsList) {
        (orderModel!.extraCharges as List<dynamic>).forEach((element) {
          extraChargeForListType.add(ExtraChargeRequestModel.fromJson(element));
        });
      }
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  userDetailApiCall(int id) async {
    await getUserDetail(id).then((value) {
      if (value.userType == DELIVERYMAN) {
        deliveryManData = value;
      } else {
        userData = value;
      }

      userID = sharedPref.getInt(USER_ID);
      if (userData!.id == orderModel!.clientId!) {
        print("both id same");
      }
      setState(() {});
    }).catchError((error) {
      print(error.toString());
    });
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () {
        Navigator.pop(context, true);
        return Future.value(true);
      },
      child: Scaffold(
        appBar: appBarWidget(
          orderModel != null ? orderStatus(orderModel!.status.validate()) : "",
          actions: [
            if (((orderModel != null && orderModel!.status != ORDER_DELIVERED && orderModel!.status != ORDER_CANCELLED && orderModel!.status != ORDER_DRAFT && orderModel!.deletedAt == null)))
              GestureDetector(
                child: Container(
                  alignment: Alignment.center,
                  margin: EdgeInsets.all(12),
                  padding: EdgeInsets.symmetric(horizontal: 16),
                  decoration: BoxDecoration(color: Colors.transparent, borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: Colors.white)),
                  child: Text(orderModel!.deliveryManId == null ? language.assign : language.transfer, style: boldTextStyle(color: Colors.white)),
                ),
                onTap: () async {
                  await showDialog(
                    context: context,
                    builder: (_) {
                      return DeliveryOrderAssignComponent(
                        orderModel: orderModel!,
                        orderId: orderModel!.id!,
                        onUpdate: () {
                          orderDetailApiCall();
                        },
                      );
                    },
                  );
                },
              ),
          ],
        ),
        body: Stack(
          children: [
            orderModel != null
                ? SingleChildScrollView(
                    padding: EdgeInsets.all(16),
                    controller: ScrollController(),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Text('${language.id} #${orderModel!.id}', style: boldTextStyle(size: 18)),
                            Spacer(),
                            if (orderModel!.status == ORDER_DELIVERED)
                              ElevatedButton(
                                style: ElevatedButton.styleFrom(
                                  elevation: 0,
                                  backgroundColor: primaryColor,
                                  padding: EdgeInsets.all(6),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(defaultRadius),
                                    side: BorderSide(color: primaryColor),
                                  ),
                                ),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Text(language.invoiceCapital, style: primaryTextStyle(color: Colors.white)),
                                    Icon(Icons.download_outlined, color: Colors.white),
                                  ],
                                ),
                                onPressed: () {
                                  // generateInvoiceCall(orderModel!);
                                  PDFViewer(
                                    invoice: orderModel!.invoice.validate(),
                                    filename: "${orderModel!.id.validate()}",
                                  ).launch(context);
                                },
                              ),
                            SizedBox(width: 12),
                            ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                elevation: 0,
                                backgroundColor: Colors.transparent,
                                padding: EdgeInsets.all(6),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(defaultRadius),
                                  side: BorderSide(color: primaryColor),
                                ),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Text(language.orderHistory, style: primaryTextStyle()),
                                  Icon(Icons.arrow_right, color: appStore.isDarkMode ? Colors.white : primaryColor),
                                ],
                              ),
                              onPressed: () {
                                launchScreen(context, OrderHistoryScreen(orderId: orderModel!.id, orderHistoryData: orderHistory));
                              },
                            ),
                          ],
                        ),
                        SizedBox(height: 16),
                        Text(language.parcelDetails, style: boldTextStyle()),
                        Container(
                          margin: EdgeInsets.only(top: 16),
                          padding: EdgeInsets.all(12),
                          decoration: containerDecoration(),
                          child: Column(
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Expanded(child: Text(language.parcelType, style: primaryTextStyle(), maxLines: 2, overflow: TextOverflow.ellipsis)),
                                  Expanded(child: Text(orderModel!.parcelType ?? '-', style: primaryTextStyle(), maxLines: 2, overflow: TextOverflow.ellipsis, textAlign: TextAlign.end)),
                                ],
                              ),
                              SizedBox(height: 16),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(language.weight, style: primaryTextStyle()),
                                  Text('${orderModel!.totalWeight.toString()} ${appStore.countryList.isNotEmpty ? '${appStore.countryList.firstWhere((element) => element.id == orderModel!.countryId).weightType ?? 'kg'}' : 'kg'}', style: primaryTextStyle()),
                                ],
                              ),
                              SizedBox(height: 16),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Expanded(child: Text(language.numberOfParcels, style: primaryTextStyle(), maxLines: 2, overflow: TextOverflow.ellipsis)),
                                  Expanded(child: Text('${orderModel!.totalParcel ?? 1}', style: primaryTextStyle(), maxLines: 2, overflow: TextOverflow.ellipsis, textAlign: TextAlign.end)),
                                ],
                              ),
                            ],
                          ),
                        ),
                        SizedBox(height: 16),
                        Text(language.paymentDetails, style: boldTextStyle()),
                        Container(
                          margin: EdgeInsets.only(top: 16),
                          padding: EdgeInsets.all(12),
                          decoration: containerDecoration(),
                          child: Column(
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(language.paymentType, style: primaryTextStyle()),
                                  Text('${paymentType(orderModel!.paymentType ?? PAYMENT_TYPE_CASH)}', style: primaryTextStyle()),
                                ],
                              ),
                              SizedBox(height: 16),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(language.paymentStatus, style: primaryTextStyle()),
                                  Text('${paymentStatus(orderModel!.paymentStatus ?? PAYMENT_PENDING)}', style: primaryTextStyle()),
                                ],
                              ),
                              if ((orderModel!.paymentType ?? PAYMENT_TYPE_CASH) == PAYMENT_TYPE_CASH) SizedBox(height: 16),
                              if ((orderModel!.paymentType ?? PAYMENT_TYPE_CASH) == PAYMENT_TYPE_CASH)
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(language.paymentCollectFrom, style: primaryTextStyle()),
                                    Text('${paymentCollectForm(orderModel!.paymentCollectFrom!)}', style: primaryTextStyle()),
                                  ],
                                ),
                            ],
                          ),
                        ),
                        if (orderModel!.pickupPoint!.address != null)
                          Padding(
                            padding: EdgeInsets.only(top: 16),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(language.pickupAddress, style: boldTextStyle()),
                                Container(
                                  margin: EdgeInsets.only(top: 16),
                                  padding: EdgeInsets.all(12),
                                  decoration: containerDecoration(),
                                  child: Row(
                                    children: [
                                      GestureDetector(
                                        onTap: () {
                                          MapsLauncher.launchCoordinates(double.parse(orderModel!.pickupPoint!.latitude.validate()), double.parse(orderModel!.pickupPoint!.longitude.validate()));
                                        },
                                        child: ImageIcon(AssetImage('assets/icons/ic_pick_location.png'), size: 24, color: primaryColor),
                                      ),
                                      SizedBox(width: 16),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            if (orderModel!.pickupDatetime != null)
                                              Padding(
                                                padding: EdgeInsets.only(bottom: 8.0),
                                                child: Text('${language.pickedAt} ${printDate("${orderModel!.pickupDatetime!}Z")}', style: secondaryTextStyle()),
                                              ),
                                            Text('${orderModel!.pickupPoint!.address}', style: primaryTextStyle()),
                                            if (orderModel!.pickupPoint!.contactNumber != null)
                                              Padding(
                                                padding: EdgeInsets.only(top: 8.0),
                                                child: GestureDetector(
                                                  onTap: () {
                                                    launchUrl(Uri.parse('tel:${orderModel!.pickupPoint!.contactNumber}'));
                                                  },
                                                  child: Row(
                                                    children: [
                                                      Icon(Icons.call, size: 18),
                                                      SizedBox(width: 8),
                                                      Text('${orderModel!.pickupPoint!.contactNumber}', style: secondaryTextStyle()),
                                                      // IconButton(
                                                      //     onPressed: () {
                                                      //       // ChatScreen(
                                                      //       //   id: orderModel!.clientId.toString(),
                                                      //       // //  userData: deliveryManData,
                                                      //       // ).launch(context);
                                                      //     },
                                                      //     icon: Icon(Icons.chat)),
                                                    ],
                                                  ),
                                                ),
                                              ),
                                            if (orderModel!.pickupDatetime == null && orderModel!.pickupPoint!.endTime != null && orderModel!.pickupPoint!.startTime != null)
                                              Padding(
                                                padding: EdgeInsets.only(top: 8.0),
                                                child: Text(
                                                    '${language.note} ${language.courierWillPickupAt} ${DateFormat('dd MMM yyyy').format(DateTime.parse(orderModel!.pickupPoint!.startTime!).toLocal())} ${language.from} ${DateFormat('hh:mm').format(DateTime.parse(orderModel!.pickupPoint!.startTime!).toLocal())} ${language.to} ${DateFormat('hh:mm').format(DateTime.parse(orderModel!.pickupPoint!.endTime!).toLocal())}',
                                                    style: secondaryTextStyle()),
                                              ),
                                            if (orderModel!.pickupPoint!.description.validate().isNotEmpty)
                                              Padding(
                                                padding: EdgeInsets.only(top: 8.0),
                                                child: ReadMoreText(
                                                  '${language.remark}: ${orderModel!.pickupPoint!.description.validate()}',
                                                  trimLines: 3,
                                                  style: primaryTextStyle(size: 14),
                                                  colorClickableText: primaryColor,
                                                  trimMode: TrimMode.Line,
                                                  trimCollapsedText: language.showMore,
                                                  trimExpandedText: language.showLess,
                                                ),
                                              ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        if (orderModel!.deliveryPoint!.address != null)
                          Padding(
                            padding: EdgeInsets.only(top: 16),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(language.deliveryAddress, style: boldTextStyle()),
                                Container(
                                  margin: EdgeInsets.only(top: 16),
                                  padding: EdgeInsets.all(12),
                                  decoration: containerDecoration(),
                                  child: Row(
                                    children: [
                                      GestureDetector(
                                        onTap: () {
                                          MapsLauncher.launchCoordinates(double.parse(orderModel!.deliveryPoint!.latitude.validate()), double.parse(orderModel!.deliveryPoint!.longitude.validate()));
                                        },
                                        child: ImageIcon(AssetImage('assets/icons/ic_delivery_location.png'), size: 24, color: primaryColor),
                                      ),
                                      SizedBox(width: 16),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            if (orderModel!.deliveryDatetime != null)
                                              Padding(
                                                padding: EdgeInsets.only(bottom: 8.0),
                                                child: Text('${language.deliveredAt} ${printDate("${orderModel!.deliveryDatetime!}Z")}', style: secondaryTextStyle()),
                                              ),
                                            Text('${orderModel!.deliveryPoint!.address}', style: primaryTextStyle()),
                                            if (orderModel!.deliveryPoint!.contactNumber != null)
                                              Padding(
                                                padding: EdgeInsets.only(top: 8.0),
                                                child: GestureDetector(
                                                  onTap: () {
                                                    launchUrl(Uri.parse('tel:${orderModel!.deliveryPoint!.contactNumber}'));
                                                  },
                                                  child: Row(
                                                    children: [
                                                      Icon(Icons.call, color: Colors.green, size: 18),
                                                      SizedBox(width: 8),
                                                      Text('${orderModel!.deliveryPoint!.contactNumber}', style: secondaryTextStyle()),
                                                    ],
                                                  ),
                                                ),
                                              ),
                                            if (orderModel!.deliveryDatetime == null && orderModel!.deliveryPoint!.endTime != null && orderModel!.deliveryPoint!.startTime != null)
                                              Padding(
                                                padding: EdgeInsets.only(top: 8.0),
                                                child: Text(
                                                    '${language.note} ${language.courierWillDeliveredAt} ${DateFormat('dd MMM yyyy').format(DateTime.parse(orderModel!.deliveryPoint!.startTime!).toLocal())} ${language.from} ${DateFormat('hh:mm').format(DateTime.parse(orderModel!.deliveryPoint!.startTime!).toLocal())} ${language.to} ${DateFormat('hh:mm').format(DateTime.parse(orderModel!.deliveryPoint!.endTime!).toLocal())}',
                                                    style: secondaryTextStyle()),
                                              ),
                                            if (orderModel!.deliveryPoint!.description.validate().isNotEmpty)
                                              Padding(
                                                padding: EdgeInsets.only(top: 8.0),
                                                child: ReadMoreText(
                                                  '${language.remark}: ${orderModel!.deliveryPoint!.description.validate()}',
                                                  trimLines: 3,
                                                  style: primaryTextStyle(size: 14),
                                                  colorClickableText: primaryColor,
                                                  trimMode: TrimMode.Line,
                                                  trimCollapsedText: language.showMore,
                                                  trimExpandedText: language.showLess,
                                                ),
                                              ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        if (orderModel!.vehicleData != null)
                          Padding(
                            padding: EdgeInsets.only(top: 16),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(language.vehicle, style: boldTextStyle()),
                                16.height,
                                Container(
                                  decoration: containerDecoration(),
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    crossAxisAlignment: CrossAxisAlignment.center,
                                    children: [
                                      8.height,
                                      Text('${orderModel!.vehicleData!.title.validate()}', style: primaryTextStyle()),
                                      8.height,
                                      Container(
                                        //   margin: EdgeInsets.all(10),
                                        child: ClipRRect(
                                            borderRadius: BorderRadius.circular(10), child: commonCachedNetworkImage(orderModel!.vehicleImage.validate(), fit: BoxFit.fill, height: MediaQuery.of(context).size.height * 0.15, width: MediaQuery.of(context).size.width * 0.7)),
                                      ).center(),
                                      20.height,
                                    ],
                                  ),
                                )
                              ],
                            ),
                          ),
                        /* if (orderStatus(orderModel!.status.validate()) == language.pickedUp || orderStatus(orderModel!.status.validate()) == language.departed)
                          ElevatedButton(
                            style: ElevatedButton.styleFrom(
                              elevation: 0,
                              backgroundColor: Colors.transparent,
                              padding: EdgeInsets.all(6),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(defaultRadius),
                                side: BorderSide(color: Colors.grey.withOpacity(0.3)),
                              ),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text("Courier company", style: boldTextStyle()),
                                Icon(Icons.arrow_right, color: appStore.isDarkMode ? Colors.white : primaryColor),
                              ],
                            ),
                            onPressed: () {},
                          ),*/
                        if (orderStatus(orderModel!.status.validate()) == language.pickedUp || orderStatus(orderModel!.status.validate()) == language.departed || orderModel!.isShipped.validate() == 1)
                          Padding(
                            padding: EdgeInsets.only(top: 16),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(language.courierCompany, style: boldTextStyle()),
                                16.height,
                                Container(
                                  padding: EdgeInsets.symmetric(horizontal: 18, vertical: 18),
                                  decoration: containerDecoration(),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Text(language.courierCompany, style: primaryTextStyle()),
                                      8.height,
                                      AppTextField(
                                        controller: courierCompanyCont,
                                        readOnly: true,
                                        textFieldType: TextFieldType.OTHER,
                                        decoration: InputDecoration(
                                          contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                          filled: false,
                                          border: OutlineInputBorder(borderSide: BorderSide(color: primaryColor, width: 1), borderRadius: BorderRadius.circular(defaultRadius)),
                                        ),
                                        onTap: () {
                                          if (orderModel!.isShipped.validate() == 0) {
                                            AssignCourierCompany(
                                              onUpdate: () {
                                                orderDetailApiCall();
                                              },
                                              orderId: orderModel!.id.validate(),
                                            ).launch(context);
                                          }
                                        },
                                      ),
                                      SizedBox(height: 16),
                                      Text(language.trakingId, style: primaryTextStyle()),
                                      SizedBox(height: 8),
                                      AppTextField(
                                        controller: trackingIdCont,
                                        readOnly: true,
                                        textFieldType: TextFieldType.OTHER,
                                        onTap: () {
                                          if (orderModel!.isShipped.validate() == 0) {
                                            AssignCourierCompany(
                                              onUpdate: () {
                                                orderDetailApiCall();
                                              },
                                              orderId: orderModel!.id.validate(),
                                            ).launch(context);
                                          }
                                        },
                                        decoration: InputDecoration(
                                          contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                          filled: false,
                                          border: OutlineInputBorder(borderSide: BorderSide(color: primaryColor, width: 1), borderRadius: BorderRadius.circular(defaultRadius)),
                                        ),
                                      ),
                                    ],
                                  ),
                                )
                              ],
                            ),
                          ),
                        Row(
                          children: [
                            if (orderModel!.pickupConfirmByClient == 1)
                              Expanded(
                                child: Padding(
                                  padding: EdgeInsets.only(top: 16),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(language.picUpSignature, style: boldTextStyle()),
                                      Container(
                                        width: MediaQuery.of(context).size.width,
                                        margin: EdgeInsets.only(top: 16),
                                        padding: EdgeInsets.all(12),
                                        decoration: containerDecoration(),
                                        child: orderModel!.pickupTimeSignature!.isNotEmpty ? commonCachedNetworkImage(orderModel!.pickupTimeSignature ?? '-', fit: BoxFit.contain, height: 140, width: 140) : Text(language.noData),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            orderModel!.pickupConfirmByDeliveryMan == 1
                                ? Expanded(
                                    child: Padding(
                                      padding: EdgeInsets.only(top: 16, left: 16),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(language.deliverySignature, style: boldTextStyle()),
                                          Container(
                                            width: MediaQuery.of(context).size.width,
                                            margin: EdgeInsets.only(top: 16),
                                            padding: EdgeInsets.all(12),
                                            decoration: containerDecoration(),
                                            child: orderModel!.deliveryTimeSignature!.isNotEmpty ? commonCachedNetworkImage(orderModel!.deliveryTimeSignature!, fit: BoxFit.contain, height: 140, width: 140) : Text(language.noData),
                                          ),
                                        ],
                                      ),
                                    ),
                                  )
                                : Spacer(),
                          ],
                        ),
                        if (userData != null)
                          Padding(
                            padding: EdgeInsets.only(top: 16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(language.aboutUser, style: boldTextStyle()),
                                Container(
                                  margin: EdgeInsets.only(top: 16),
                                  padding: EdgeInsets.all(12),
                                  decoration: containerDecoration(),
                                  child: Row(
                                    children: [
                                      Container(
                                        height: 60,
                                        width: 60,
                                        decoration: BoxDecoration(
                                          border: Border.all(color: Colors.grey.withOpacity(0.15)),
                                          shape: BoxShape.circle,
                                          image: DecorationImage(image: NetworkImage('${userData!.profileImage ?? ""}'), fit: BoxFit.cover),
                                        ),
                                      ),
                                      SizedBox(width: 8),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          mainAxisAlignment: MainAxisAlignment.start,
                                          children: [
                                            Row(
                                              mainAxisAlignment: MainAxisAlignment.start,
                                              children: [
                                                Text('${userData!.name}', style: boldTextStyle()).expand(),
                                                // if (userData!.contactNumber != null)
                                                //   sharedPref.getInt(USER_ID) == orderModel!.clientId!
                                                //       ? SizedBox()
                                                //       : IconButton(
                                                //               onPressed: () {
                                                //                 ChatScreen(userData: userData!).launch(context);
                                                //               },
                                                //               icon: Icon(Icons.chat))
                                                //           .visible(userData!.userType != ADMIN || userData!.userType != DEMO_ADMIN),
                                                GestureDetector(
                                                  onTap: () {
                                                    launchUrl(Uri.parse('tel:${userData!.contactNumber}'));
                                                  },
                                                  child: Icon(Icons.call),
                                                ),
                                              ],
                                            ),
                                            userData!.contactNumber != null ? Text('${userData!.contactNumber}', style: secondaryTextStyle()) : SizedBox()
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        if (deliveryManData != null)
                          Padding(
                            padding: EdgeInsets.only(top: 16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(language.aboutDeliveryMan, style: boldTextStyle()),
                                Container(
                                  margin: EdgeInsets.only(top: 16),
                                  padding: EdgeInsets.all(12),
                                  decoration: containerDecoration(),
                                  child: Row(
                                    children: [
                                      Stack(
                                        children: [
                                          Container(
                                            height: 60,
                                            width: 60,
                                            decoration: BoxDecoration(
                                              border: Border.all(color: Colors.grey.withOpacity(0.15)),
                                              shape: BoxShape.circle,
                                              image: DecorationImage(image: NetworkImage('${deliveryManData!.profileImage ?? ""}'), fit: BoxFit.cover),
                                            ),
                                          ),
                                          if (deliveryManData!.isVerifiedDeliveryMan == 1) Icon(Icons.verified_user, color: Colors.green, size: 22),
                                        ],
                                      ),
                                      SizedBox(width: 8),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          mainAxisAlignment: MainAxisAlignment.start,
                                          children: [
                                            Row(
                                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                              children: [
                                                Text('${deliveryManData!.name}', style: boldTextStyle()).expand(),
                                                // IconButton(
                                                //     onPressed: () {
                                                //       ChatScreen(userData: deliveryManData!).launch(context);
                                                //     },
                                                //     icon: Icon(Icons.chat)),
                                                if (deliveryManData!.contactNumber != null)
                                                  GestureDetector(
                                                    onTap: () {
                                                      launchUrl(Uri.parse('tel:${deliveryManData!.contactNumber}'));
                                                    },
                                                    child: Icon(Icons.call),
                                                  ),
                                              ],
                                            ),
                                            deliveryManData!.contactNumber != null ? Text('${deliveryManData!.contactNumber}', style: secondaryTextStyle()) : SizedBox()
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        if (orderModel!.status == ORDER_CANCELLED)
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              SizedBox(height: 16),
                              Text(language.cancelledReason, style: boldTextStyle()),
                              SizedBox(height: 16),
                              Container(
                                width: MediaQuery.of(context).size.width,
                                decoration: BoxDecoration(color: Colors.red.withOpacity(0.1), borderRadius: BorderRadius.circular(16)),
                                padding: EdgeInsets.all(12),
                                child: Text('${orderModel!.reason.validate(value: "-")}', style: primaryTextStyle(color: Colors.red)),
                              ),
                            ],
                          ),
                        Container(
                          margin: EdgeInsets.only(top: 16),
                          padding: EdgeInsets.all(12),
                          decoration: containerDecoration(),
                          child: (orderModel!.extraCharges is List<dynamic>)
                              ? ValueListenableBuilder<List<ExtraChargeRequestModel>>(
                                  valueListenable: myList,
                                  builder: (context, value, child) {
                                    return  OrderSummeryWidget(
                                      extraChargesList: value,
                                      insuranceCharge:  orderModel!.insuranceCharge.validate(),
                                      vehiclePrice: orderModel!.vehicleCharge.validate(),
                                      totalDistance: orderModel!.totalDistance ?? 0,
                                      totalWeight: orderModel!.totalWeight ?? 0,
                                      distanceCharge: orderModel!.distanceCharge ?? 0,
                                      weightCharge: orderModel!.weightCharge ?? 0,
                                      totalAmount: orderModel!.totalAmount ?? 0,
                                      status: orderModel!.status,
                                      payment: payment,
                                    );
                                  })
    //                       OrderAmountDataWidget(
    // fixedAmount: orderModel!.fixedCharges!.toDouble(),
    // distanceAmount: orderModel!.distanceCharge!.toDouble(),
    // extraCharges: orderModel!.extraChargesList,
    // vehicleAmount:
    // orderModel!.vehicleData != null ? orderModel!.vehicleData!.price!.toDouble() : 0,
    // insuranceAmount:
    // orderModel!.insuranceCharge != null ? orderModel!.insuranceCharge!.toDouble() : 0,
    // diffWeight: (orderModel!.cityDetails!.minWeight! > orderModel!.totalWeight!)
    // ? (orderModel!.totalDistance.validate() - orderModel!.cityDetails!.minDistance!).toDouble()
    //     : 0,
    // diffDistance: (orderModel!.cityDetails!.minDistance! > orderModel!.totalDistance!)
    // ? (orderModel!.totalDistance.validate() - orderModel!.cityDetails!.minDistance!).toDouble()
    //     : 0,
    // totalAmount: orderModel!.totalAmount.validate().toDouble(),
    // weightAmount: orderModel!.weightCharge != null ? orderModel!.weightCharge!.toDouble() : 0,
    // perWeightCharge: 0,
    // perKmCityDataCharge: 0,
    // perkmVehiclePrice: orderModel!.vehicleData != null
    // ? orderModel!.vehicleData!.perKmCharge!.toDouble()
    //     : 0,
    // baseTotal: orderModel!.baseTotal.validate().toDouble()
    // )
                              : Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.deliveryCharges, style: primaryTextStyle()),
                                        SizedBox(width: 16),
                                        Text('${printAmount(orderModel!.fixedCharges ?? 0)}', style: primaryTextStyle()),
                                      ],
                                    ),

                                    if (orderModel!.insuranceCharge.validate() != 0)
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(language.insuranceCharge, style: primaryTextStyle()),
                                          SizedBox(width: 16),
                                          Text('${printAmount(orderModel!.insuranceCharge.validate())}', style: primaryTextStyle()),
                                        ],
                                      ).paddingBottom(8),
                                    if (orderModel!.distanceCharge != 0)
                                      Column(
                                        children: [
                                          SizedBox(height: 8),
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(language.distanceCharge, style: primaryTextStyle()),
                                              SizedBox(width: 16),
                                              Text('${printAmount(orderModel!.distanceCharge ?? 0)}', style: primaryTextStyle()),
                                            ],
                                          )
                                        ],
                                      ),
                                    if (orderModel!.weightCharge != 0)
                                      Column(
                                        children: [
                                          SizedBox(height: 8),
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(language.weightCharge, style: primaryTextStyle()),
                                              SizedBox(width: 16),
                                              Text('${printAmount(orderModel!.weightCharge ?? 0)}', style: primaryTextStyle()),
                                            ],
                                          ),
                                        ],
                                      ),
                                    if ((orderModel!.distanceCharge != 0 || orderModel!.weightCharge != 0) && orderModel!.extraCharges.keys.length != 0)
                                      Align(
                                        alignment: Alignment.bottomRight,
                                        child: Column(
                                          children: [
                                            SizedBox(height: 8),
                                            Text('${printAmount((orderModel!.fixedCharges ?? 0) + (orderModel!.distanceCharge ?? 0) + (orderModel!.weightCharge ?? 0))}', style: primaryTextStyle()),
                                          ],
                                        ),
                                      ),
                                    if (orderModel!.extraCharges.keys.length != 0)
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          SizedBox(height: 16),
                                          Text(language.extraCharges, style: boldTextStyle()),
                                          SizedBox(height: 8),
                                          Column(
                                              children: List.generate(orderModel!.extraCharges.keys.length, (index) {
                                            return Padding(
                                              padding: EdgeInsets.only(bottom: 8),
                                              child: Row(
                                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                children: [
                                                  Text(orderModel!.extraCharges.keys.elementAt(index).replaceAll("_", " "), style: primaryTextStyle()),
                                                  SizedBox(width: 16),
                                                  Text('${printAmount(orderModel!.extraCharges.values.elementAt(index))}', style: primaryTextStyle()),
                                                ],
                                              ),
                                            );
                                          }).toList()),
                                        ],
                                      ),
                                    SizedBox(height: 16),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.total, style: boldTextStyle(size: 20)),
                                        (orderModel!.status == ORDER_CANCELLED && payment != null && payment!.deliveryManFee == 0)
                                            ? Row(
                                                mainAxisSize: MainAxisSize.min,
                                                children: [
                                                  Text('${printAmount(orderModel!.totalAmount ?? 0)}', style: secondaryTextStyle(size: 16, decoration: TextDecoration.lineThrough)),
                                                  SizedBox(width: 8),
                                                  Text('${printAmount(payment!.cancelCharges ?? 0)}', style: boldTextStyle(size: 20)),
                                                ],
                                              )
                                            : Text('${printAmount(orderModel!.totalAmount ?? 0)}', style: boldTextStyle(size: 20)),
                                      ],
                                    ),
                                  ],
                                ),
                        ),
                      ],
                    ),
                  )
                : !appStore.isLoading
                    ? emptyWidget()
                    : SizedBox(),
            Visibility(visible: appStore.isLoading, child: loaderWidget()),
          ],
        ),
      ),
    );
  }
}

class PDFViewer extends StatefulWidget {
  final String invoice;
  final String? filename;

  PDFViewer({required this.invoice, this.filename = ""});

  @override
  State<PDFViewer> createState() => _PDFViewerState();
}

class _PDFViewerState extends State<PDFViewer> {
  PdfController? pdfController;

  @override
  void initState() {
    super.initState();
    viewPDF();
  }

  Future<void> viewPDF() async {
    try {
      setState(() {
        appStore.setLoading(true);
        print("invoice ==> ${widget.invoice}");
        pdfController = PdfController(document: PdfDocument.openData(InternetFile.get("${widget.invoice}")));
        appStore.setLoading(false);
      });
    } catch (e) {
      print('Error viewing PDF: $e');
    }
  }

  Future<void> downloadPDF() async {
    appStore.setLoading(true);
    final response = await http.get(Uri.parse(widget.invoice));
    if (response.statusCode == 200) {
      print("success ${response.bodyBytes}");
      final bytes = response.bodyBytes;
      // final directory = await getApplicationDocumentsDirectory();
      final directory = await getExternalStorageDirectory();
      final path = directory!.path;
      String fileName = widget.filename.validate().isEmpty ? "invoice" : widget.filename.validate();
      File file = File('${path}/${fileName}.pdf');
      print("file ${file.path}");
      await file.writeAsBytes(bytes, flush: true);
      appStore.setLoading(false);
      toast("invoice downloaded at ${file.path}");
      final filef = File(file.path);
      if (await filef.exists()) {
        OpenFile.open(file.path);
      } else {
        throw 'File does not exist';
      }
    } else {
      appStore.setLoading(false);
      toast("Failed to download pdf");
      throw Exception('Failed to download PDF');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: appBarWidget(language.invoiceCapital, actions: [
          Icon(Icons.download, color: Colors.white).withWidth(60).onTap(() {
            downloadPDF();
          }, splashColor: Colors.transparent, hoverColor: Colors.transparent, highlightColor: Colors.transparent),
        ]),
        body: Stack(
          children: [
            PdfView(
              controller: pdfController!,
            ),
            Observer(builder: (context) {
              return loaderWidget().visible(appStore.isLoading);
            }),
          ],
        ));
  }
}
