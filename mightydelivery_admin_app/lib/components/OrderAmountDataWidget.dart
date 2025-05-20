import 'package:flutter/material.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/double_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Colors.dart';

import '../extensions/decorations.dart';
import '../main.dart';
import '../models/CreateOrderDetailResponse.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';

class OrderAmountDataWidget extends StatefulWidget {
  static String tag = '/OrderSummeryWidget';

  final List<ExtraCharges>? extraCharges;
  final double fixedAmount;
  final double weightAmount;
  final double distanceAmount;
  final double vehicleAmount;
  final double? insuranceAmount;
  final double? diffWeight;
  final double? diffDistance;
  final double? totalAmount;
  final double? baseTotal;
  double? perWeightCharge;
  double? perkmVehiclePrice;
  double? perKmCityDataCharge;

  OrderAmountDataWidget({
    required this.fixedAmount,
    required this.weightAmount,
    required this.distanceAmount,
    required this.vehicleAmount,
    required this.insuranceAmount,
    required this.diffWeight,
    required this.diffDistance,
    required this.totalAmount,
    required this.extraCharges,
    required this.baseTotal,
    required this.perWeightCharge,
    required this.perkmVehiclePrice,
    required this.perKmCityDataCharge,
  });

  @override
  OrderAmountDataWidgetState createState() => OrderAmountDataWidgetState();
}

class OrderAmountDataWidgetState extends State<OrderAmountDataWidget> {
  double baseTotal = 0;
  double? extraChargesTotal = 0;
  @override
  void initState() {
    super.initState();
    baseTotal = widget.baseTotal!.toDouble();
    print("-------vehicle price${widget.perkmVehiclePrice}");

    cal();
    setState(() {});
  }

  cal() async {
    double chargesTotal = 0;
    if(widget.extraCharges != null){
      widget.extraCharges!.forEach((element) async {
        double i = 0;
        if (element.chargesType == CHARGE_TYPE_PERCENTAGE) {
          i = (widget.baseTotal!.toDouble() * element.charges!.toDouble() * 0.01)
              .toStringAsFixed(digitAfterDecimal)
              .toDouble();
        } else {
          i = element.charges!.toStringAsFixed(digitAfterDecimal).toDouble();
        }
        chargesTotal = chargesTotal += i;
      });
    }

    extraChargesTotal = chargesTotal;
    print("------------------extrachagres total${extraChargesTotal}");
    setState(() {});
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Container(
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
          Row(
            children: [
              Text("${language.vehicle} ${language.price.toLowerCase()}", style: secondaryTextStyle()),
              4.width,
              Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('(${widget.diffDistance!.toStringAsFixed(digitAfterDecimal)} x ', style: secondaryTextStyle()),
                  Text('${widget.perkmVehiclePrice!.toStringAsFixed(digitAfterDecimal)})', style: secondaryTextStyle()),
                ],
              ).visible(widget.diffDistance!.toDouble() > 0).expand(),
              16.width,
              Text('${printAmount(widget.vehicleAmount)}', style: boldTextStyle(size: 14)),
            ],
          ).paddingBottom(8).visible(widget.vehicleAmount != 0),
          Row(
            children: [
              Text(language.weightCharge, style: secondaryTextStyle()),
              4.width,
              Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('(${widget.diffWeight} x ', style: secondaryTextStyle()),
                  Text('${widget.perWeightCharge})', style: secondaryTextStyle()),
                ],
              ).visible(widget.diffWeight!.toDouble() > 0).expand(),
              16.width,
              Text('${printAmount(widget.weightAmount)}', style: boldTextStyle(size: 14)),
            ],
          ).paddingBottom(8).visible(widget.weightAmount != 0),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(language.deliveryCharges, style: secondaryTextStyle()),
              16.width,
              Text('${printAmount(widget.fixedAmount)}', style: boldTextStyle(size: 14)),
            ],
          ).paddingBottom(8).visible(widget.fixedAmount.validate() != 0),
          if (appStore.isInsuranceAllowed == "1" && widget.insuranceAmount != 0)
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text("Insurance charge", style: secondaryTextStyle()),
                16.width,
                Text('${printAmount(
                   num.parse(widget.insuranceAmount.toString()))}', style: boldTextStyle(size: 14)),
              ],
            ).paddingBottom(8),
          Row(
            children: [
              Text(language.distanceCharge, style: secondaryTextStyle()),
              4.width,
              Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('(${widget.diffDistance!.toStringAsFixed(digitAfterDecimal)}', style: secondaryTextStyle()),
                  Icon(Icons.close, color: Colors.grey, size: 12),
                  Text('${widget.perKmCityDataCharge})', style: secondaryTextStyle()),
                ],
              ).visible(widget.diffDistance!.toDouble() > 0).expand(),
              16.width,
              Text('${printAmount(widget.distanceAmount)}', style: boldTextStyle(size: 14)),
            ],
          ).paddingBottom(8).visible(widget.distanceAmount != 0),
          if(widget.extraCharges != null && widget.extraCharges!.length != 0)
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(language.extraCharges, style: boldTextStyle(size: 14)),
              8.height,
              Column(
                  children: List.generate(widget.extraCharges!.length, (index) {
                    ExtraCharges mData = widget.extraCharges!.elementAt(index);
                    print("-----------${mData.charges}");
                    return Padding(
                      padding: EdgeInsets.only(bottom: 8),
                      child: Row(
                        children: [
                          Text(mData.title!.replaceAll("_", " ").capitalizeFirstLetter(), style: secondaryTextStyle()),
                          4.width,
                          Text('(${mData.chargesType == CHARGE_TYPE_PERCENTAGE ? '${mData.charges}%' : '${printAmount(mData.charges!.toDouble())}'})',
                              style: secondaryTextStyle())
                              .expand(),
                          16.width,
                          Text(
                              '${printAmount(countExtraCharge(totalAmount:widget.baseTotal!, chargesType: !mData.chargesType.isEmptyOrNull ? mData.chargesType! : "", charges: mData.charges!))}',
                              style: boldTextStyle(size: 14)),
                        ],
                      ),
                    );
                  }).toList()),
            ],
          ),
          Divider(color: context.dividerColor),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(language.total,
                  style: boldTextStyle(
                    size: 18,
                    color: Colors.green,
                  )),
              // Text('${printAmount((extraChargesTotal! + baseTotal + widget.insuranceAmount.validate()))}',
              Text('${widget.totalAmount.validate()}',
                  style: boldTextStyle(
                    size: 18,
                    color: Colors.green,
                  )),
            ],
          ),
        ],
      ),
    );
  }
}

