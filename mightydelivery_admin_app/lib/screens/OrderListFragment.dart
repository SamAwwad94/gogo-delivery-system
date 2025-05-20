import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';

import '../components/DeliveryOrderAssignComponent.dart';
import '../components/FilterOrderComponent.dart';
import '../components/OrderWidgetComponent.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/OrderModel.dart';
import '../models/models.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';
import '../utils/Extensions/shared_pref.dart';
import 'CreateOrderScreen.dart';
import 'NotificationScreen.dart';
import 'OrderDetailScreen.dart';

class OrderListFragment extends StatefulWidget {
  @override
  OrderListFragmentState createState() => OrderListFragmentState();
}

class OrderListFragmentState extends State<OrderListFragment> {
  TextEditingController orderIdController = TextEditingController();
  ScrollController controller = ScrollController();
  String selectedStatus = language.all;
  List<String> statusList = [language.all, ORDER_ACCEPTED, ORDER_ARRIVED, ORDER_ASSIGNED, ORDER_CANCELLED, ORDER_DELIVERED, ORDER_CREATED, ORDER_DEPARTED, ORDER_DRAFT, ORDER_PICKED_UP];

  int currentPage = 1;
  int totalPage = 1;
  FilterAttributeModel? filterData;

  List<OrderModel> orderData = [];

  DateTimeRange? picked;
  String? date;
  String? dateMin;
  String? dateMax;
  bool isSelectAll = false;
  List<int> ordersChecked = [];

  @override
  void initState() {
    super.initState();
    init();
    LiveStream().on('UpdateOrderData', (p0) {
      setState(() {});
      getOrderListApi();
    });
    controller.addListener(() {
      scrollHandler();
    });
  }

  Future<void> scrollHandler() async {
    if (controller.position.pixels == controller.position.maxScrollExtent) {
      if (currentPage < totalPage) {
        currentPage++;
        setState(() {});
        getOrderListApi();
      }
    }
  }

  void init() async {
    appStore.setLoading(true);
    afterBuildCreated(() {
      getOrderListApi();
    });
  }

  getOrderListApi() async {
    filterData = FilterAttributeModel.fromJson(getJSONAsync(FILTER_DATA));
    if(!appStore.isFiltering){
      filterData?.orderType = TODAY_ORDER;
    }
    appStore.setLoading(true);
    await getAllOrder(page: currentPage, orderStatus: filterData!.orderStatus, orderType: filterData!.orderType, fromDate: filterData!.fromDate, toDate: filterData!.toDate).then((value) {
      appStore.setLoading(false);
      totalPage = value.pagination!.totalPages!;
      if (currentPage == 1) {
        orderData.clear();
      }
      orderData.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  restoreOrderApiCall({int? orderId, String? type}) async {
    appStore.setLoading(true);
    Map req = {'id': orderId, 'type': type};
    await getRestoreOrderApi(req).then((value) {
      appStore.setLoading(false);
      currentPage = 1;
      getOrderListApi();
      toast(value.message);
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteOrderApiCall(int orderId) async {
    appStore.setLoading(true);
    await deleteOrderApi(orderId).then((value) {
      appStore.setLoading(false);
      currentPage = 1;
      getOrderListApi();
      toast(value.message);
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteMultipleOderApiCall(List<int> orderData) async {
    Map req = {
      "ids": ordersChecked,
    };
    appStore.setLoading(true);
    await multipleDeleteOrder(req).then((value) {
      appStore.setLoading(false);
      getOrderListApi();
      ordersChecked.clear();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  _onSelected(bool selected, int id) {
    if (selected == true) {
      setState(() {
        ordersChecked.remove(id);
      });
    } else {
      setState(() {
        ordersChecked.add(id);
      });
    }
  }

  //Select all Checkbox
  _onChangedProperty() {
    ordersChecked.clear();
    for (int i = 0; i < orderData.length; i++) {
      if (isSelectAll == true) {
        ordersChecked.add(orderData[i].id!);
      } else {
        if (ordersChecked.isNotEmpty) {
          ordersChecked.remove(orderData[i].id!);
        }
      }
    }
    setState(() {});
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Observer(builder: (context) {
      return Scaffold(
        appBar: appBarWidget(
            filterData != null
                ? (filterData!.orderStatus != null
                    ? orderStatus(filterData!.orderStatus.validate()) + " ${language.order.toLowerCase()}"
                    : filterData!.orderType != null
                        ? orderType(filterData!.orderType.validate())
                        : language.allOrder)
                : language.allOrder,
            showBack: false,
            actions: [
              Icon(MaterialCommunityIcons.select_multiple, color: white, size: 26).paddingOnly(right: 8).onTap(() {
                setState(() {
                  isSelectAll = !isSelectAll;
                  _onChangedProperty();
                });
              }).visible(ordersChecked.length > 0),
              Icon(MaterialCommunityIcons.delete, color: white, size: 24).paddingOnly(right: 8, left: appStore.selectedLanguage == "ar" ? 8 : 0).onTap(() {
                commonConfirmationDialog(
                  context,
                  DIALOG_TYPE_DELETE,
                  title: language.deleteOrders,
                  subtitle: language.doYouWantToDeleteAllSelectedOrders,
                  () {
                    if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                      toast(language.demo_admin_msg);
                    } else {
                      Navigator.pop(context);
                      deleteMultipleOderApiCall(ordersChecked);
                    }
                  },
                );
              }).visible(ordersChecked.length > 0),
              Stack(
                children: [
                  Align(alignment: AlignmentDirectional.center, child: Icon(Ionicons.md_options_outline, color: Colors.white)),
                  Observer(builder: (context) {
                    return Positioned(
                      right: 8,
                      top: 16,
                      child: Container(
                        height: 10,
                        width: 10,
                        decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle),
                      ),
                    ).visible(appStore.isFiltering);
                  }),
                ],
              ).withWidth(20).onTap(() {
                showModalBottomSheet(
                  context: context,
                  isScrollControlled: true,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(defaultRadius), topRight: Radius.circular(defaultRadius))),
                  builder: (context) {
                    return FilterOrderComponent();
                  },
                );
              }, splashColor: Colors.transparent, hoverColor: Colors.transparent, highlightColor: Colors.transparent),
              Observer(
                builder: (_) => SizedBox(
                  width: 55,
                  child: Stack(
                    children: [
                      Align(
                        alignment: AlignmentDirectional.center,
                        child: Icon(Icons.notifications, color: Colors.white),
                      ),
                      if (appStore.allUnreadCount != 0)
                        Positioned(
                          right: 10,
                          top: 8,
                          child: Container(
                            height: 20,
                            width: 20,
                            alignment: Alignment.center,
                            decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle),
                            child: Observer(builder: (_) {
                              return Text('${appStore.allUnreadCount < 99 ? appStore.allUnreadCount : '99+'}', style: primaryTextStyle(size: appStore.allUnreadCount > 99 ? 9 : 12, color: Colors.white));
                            }),
                          ),
                        ),
                    ],
                  ).onTap(() {
                    Navigator.push(context, MaterialPageRoute(builder: (_) => NotificationScreen()));
                  }),
                ),
              ),
            ]),
        body: Stack(
          fit: StackFit.expand,
          children: [
            SingleChildScrollView(
              controller: controller,
              padding: EdgeInsets.all(16),
              scrollDirection: Axis.vertical,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: context.width(),
                    decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        Text(language.orderId, style: boldTextStyle()),
                        SizedBox(width: 16),
                        AppTextField(
                          controller: orderIdController,
                          textFieldType: TextFieldType.OTHER,
                          inputFormatters: [
                            FilteringTextInputFormatter.digitsOnly,
                          ],
                          decoration: commonInputDecoration(),
                          textAlign: TextAlign.start,
                        ).expand(),
                        10.width,
                        GestureDetector(
                          child: Container(
                            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                            decoration: BoxDecoration(color: primaryColor, borderRadius: BorderRadius.circular(defaultRadius)),
                            child: Text(language.go, style: boldTextStyle(color: Colors.white)),
                          ),
                          onTap: () async {
                            int? orderId = int.tryParse(orderIdController.text);
                            FocusScope.of(context).unfocus();
                            if (orderId != null) {
                              await orderDetail(orderId: orderId).then((value) async {
                                orderIdController.clear();
                                launchScreen(
                                  context,
                                  OrderDetailScreen(orderId: orderId, orderModel: value.data),
                                );
                              }).catchError((error) {
                                toast(error.toString());
                              });
                            } else {
                              toast(language.pleaseEnterOrderId);
                            }
                          },
                        ),
                      ],
                    ).paddingAll(8),
                  ),
                  orderData.isNotEmpty
                      ? Column(
                          children: [
                            SizedBox(height: 8),
                            Align(alignment: Alignment.topRight, child: Text('* ${language.indicatesAutoAssignOrder}', style: primaryTextStyle(color: Colors.red))),
                            SizedBox(height: 8),
                            ListView.builder(
                                shrinkWrap: true,
                                primary: true,
                                physics: NeverScrollableScrollPhysics(),
                                padding: EdgeInsets.zero,
                                itemCount: orderData.length,
                                itemBuilder: (context, i) {
                                  OrderModel data = orderData[i];
                                  return InkWell(
                                    onLongPress: () {
                                      setState(() {
                                        _onSelected(ordersChecked.contains(data.id), data.id!);
                                      });
                                    },
                                    child: orderWidget(
                                      context,
                                      data,
                                      isFragment: true,
                                      assign: data.deletedAt == null
                                          ? (data.status == ORDER_DELIVERED || data.status == ORDER_CANCELLED || data.status == ORDER_DRAFT)
                                              ? SizedBox()
                                              : GestureDetector(
                                                  onTap: () async {
                                                    await showDialog(
                                                      context: context,
                                                      builder: (_) {
                                                        return DeliveryOrderAssignComponent(
                                                          orderModel: data,
                                                          orderId: data.id!,
                                                          onUpdate: () {
                                                            currentPage = 1;
                                                            getOrderListApi();
                                                          },
                                                        );
                                                      },
                                                    );
                                                  },
                                                  child: Container(
                                                    padding: EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                                                    decoration: boxDecorationWithRoundedCorners(backgroundColor: primaryColor.withOpacity(0.8), borderRadius: radius(8)),
                                                    child: Text(data.deliveryManId == null ? language.assign : language.transfer, style: primaryTextStyle(color: Colors.white, size: 14)),
                                                  ),
                                                )
                                          : SizedBox(),
                                      restore: outlineActionIcon(context, Icons.restore, Colors.green, () async {
                                        await commonConfirmationDialog(context, DIALOG_TYPE_RESTORE, () {
                                          if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                            toast(language.demoAdminMsg);
                                          } else {
                                            Navigator.pop(context);
                                            restoreOrderApiCall(orderId: data.id, type: RESTORE);
                                          }
                                        }, title: language.restoreOrder, subtitle: language.restoreOrderMsg);
                                      }),
                                      delete: outlineActionIcon(
                                        context,
                                        data.deletedAt == null ? Icons.delete : Icons.delete_forever,
                                        Colors.red,
                                        () {
                                          commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () {
                                            if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                              toast(language.demoAdminMsg);
                                            } else {
                                              Navigator.pop(context);
                                              data.deletedAt != null ? restoreOrderApiCall(orderId: data.id, type: FORCE_DELETE) : deleteOrderApiCall(data.id!);
                                            }
                                          }, isForceDelete: data.deletedAt != null, title: language.deleteOrder, subtitle: language.deleteOrderMsg);
                                        },
                                      ),
                                      isMultiSelectContainsId: ordersChecked.contains(data.id),
                                    ),
                                  );
                                }),
                          ],
                        )
                      : SizedBox(),
                ],
              ),
            ),
            Positioned(
              bottom: 16,
              right: 16,
              child: FloatingActionButton(
                backgroundColor: primaryColor,
                child: Icon(Icons.add, color: Colors.white),
                onPressed: () {
                  launchScreen(context, CreateOrderScreen());
                },
              ),
            ),
            appStore.isLoading
                ? loaderWidget()
                : orderData.isEmpty
                    ? emptyWidget()
                    : SizedBox()
          ],
        ),
      );
    });
  }
}
