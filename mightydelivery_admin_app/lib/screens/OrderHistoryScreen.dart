import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/widgets.dart';
import '../utils/Extensions/StringExtensions.dart';
import 'package:timeline_tile/timeline_tile.dart';

import '../main.dart';
import '../models/OrderHistoryModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';

class OrderHistoryScreen extends StatefulWidget {
  final int? orderId;
  final List<OrderHistoryModel>? orderHistoryData;

  OrderHistoryScreen({required this.orderId, this.orderHistoryData});

  @override
  OrderHistoryScreenState createState() => OrderHistoryScreenState();
}

class OrderHistoryScreenState extends State<OrderHistoryScreen> {
  List<OrderHistoryModel> orderHistory = [];

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    if (widget.orderHistoryData != null && widget.orderHistoryData!.isNotEmpty) {
      orderHistory = widget.orderHistoryData!;
      setState(() {});
    } else {
      await orderDetailApiCall();
    }
  }

  messageData(OrderHistoryModel orderData) {
    if (orderData.historyType == ORDER_ASSIGNED) {
      return '${language.yourOrder} #${orderData.orderId} ${language.assignTo} ${orderData.historyData!.deliveryManName}.';
    } else if (orderData.historyType == ORDER_TRANSFER) {
      return '${language.yourOrder} #${orderData.orderId} ${language.transferTo} ${orderData.historyData!.deliveryManName}.';
    } else if (orderData.historyType == ORDER_CREATED) {
      return language.newOrderHasBeenCreated;
    } else if (orderData.historyType == ORDER_PICKED_UP) {
      return language.deliveryPersonArrivedMsg;
    } else if (orderData.historyType == ORDER_CREATED) {
      return language.deliveryPersonPickedUpCourierMsg;
    } else if (orderData.historyType == ORDER_DEPARTED) {
      return '${language.yourOrder} #${orderData.orderId}  ${language.hasBeenOutForDelivery}';
    } else if (orderData.historyType == ORDER_PAYMENT) {
      return '${language.yourOrder} #${orderData.orderId} ${language.paymentStatusPaisMsg}';
    } else if (orderData.historyType == ORDER_DELIVERED) {
      return '${language.yourOrder} #${orderData.orderId}  ${language.deliveredMsg}';
    } else {
      return '${orderData.historyMessage}';
    }
  }

  orderDetailApiCall() async {
    appStore.setLoading(true);
    await orderDetail(orderId: widget.orderId!).then((value) async {
      appStore.setLoading(false);
      orderHistory = value.orderHistory!;
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(language.orderHistory),
      body: Stack(
        children: [
          ListView.builder(
            padding: EdgeInsets.all(16),
            itemCount: orderHistory.length,
            shrinkWrap: true,
            itemBuilder: (context, index) {
              OrderHistoryModel mData = orderHistory[index];
              return TimelineTile(
                alignment: TimelineAlign.start,
                isFirst: index == 0 ? true : false,
                isLast: index == (orderHistory.length - 1) ? true : false,
                indicatorStyle: IndicatorStyle(width: 15, color: primaryColor),
                afterLineStyle: LineStyle(color: primaryColor, thickness: 3),
                beforeLineStyle: LineStyle(color: primaryColor, thickness: 3),
                endChild: Padding(
                  padding: EdgeInsets.all(12),
                  child: Row(
                    children: [
                      ImageIcon(AssetImage(statusTypeIcon(type: mData.historyType)), color: primaryColor, size: 30),
                      SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('${historyStatus(mData.historyType.validate())}', style: boldTextStyle()),
                            SizedBox(height: 8),
                            Text(messageData(mData), style: primaryTextStyle()),
                            SizedBox(height: 8),
                            Text('${printDate('${mData.createdAt}')}', style: secondaryTextStyle()),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
          Observer(builder: (context) => Visibility(visible: appStore.isLoading, child: loaderWidget())),
        ],
      ),
    );
  }
}
