import 'package:date_time_picker/date_time_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../models/models.dart';
import '../utils/Colors.dart';

import '../../main.dart';
import '../extensions/decorations.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class FilterOrderComponent extends StatefulWidget {
  static String tag = '/FilterOrderComponent';

  @override
  FilterOrderComponentState createState() => FilterOrderComponentState();
}

class FilterOrderComponentState extends State<FilterOrderComponent> {
  final _formKey = GlobalKey<FormState>();
  TextEditingController fromDateController = TextEditingController();
  TextEditingController toDateController = TextEditingController();
  FilterAttributeModel? filterData;

  DateTime? fromDate, toDate;
  List<String> statusList = [
    ORDER_CREATED,
    ORDER_ACCEPTED,
    ORDER_CANCELLED,
    ORDER_ASSIGNED,
    ORDER_ARRIVED,
    ORDER_PICKED_UP,
    ORDER_DELIVERED,
    ORDER_DEPARTED,
  ];
  List<String> typeList = [
    ALL_ORDER,
    SCHEDULE_ORDER,
    SHIPPED_ORDER,
    DRAFT_ORDER,
    TODAY_ORDER,
    PENDING_ORDER,
    INPROGRESS_ORDER,
    COMPLETED_ORDER,
    CANCELLED_ORDER,
  ];
  String? selectedStatus;
  String? selectedType;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    filterData = FilterAttributeModel.fromJson(getJSONAsync(FILTER_DATA));
    if (filterData != null) {
      selectedStatus = filterData!.orderStatus;
      selectedType = filterData!.orderType;
      if (filterData!.fromDate != null) {
        fromDate = DateTime.tryParse(filterData!.fromDate!);
        if (fromDate != null) {
          fromDateController.text = fromDate.toString();
        }
      }
      if (filterData!.toDate != null) {
        toDate = DateTime.tryParse(filterData!.toDate!);
        if (toDate != null) {
          toDateController.text = toDate.toString();
        }
      }
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.all(16),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  alignment: Alignment.center,
                  margin: EdgeInsets.only(right: 16, left: appStore.selectedLanguage == "ar" ? 16 : 0),
                  padding: EdgeInsets.all(4),
                  decoration: boxDecorationWithRoundedCorners(boxShape: BoxShape.circle, border: Border.all(color: appStore.isDarkMode ? white : primaryColor)),
                  child: Icon(
                    AntDesign.close,
                    size: 16,
                  ).onTap(() {
                    finish(context);
                  }),
                ),
                Text(language.filter, style: boldTextStyle()).expand(),
                TextButton(
                  child: Text(language.reset, style: boldTextStyle()),
                  onPressed: () {
                    selectedStatus = null;
                    selectedType = null;
                    fromDate = null;
                    toDate = null;
                    fromDateController.clear();
                    toDateController.clear();
                    FocusScope.of(context).unfocus();
                    setState(() {});
                  },
                ),
              ],
            ),
            Divider(height: 20, color: context.dividerColor),
            Text(language.type, style: boldTextStyle()),
            8.height,
            Wrap(
              spacing: 8,
              runSpacing: 0,
              children: typeList.map((item) {
                return Chip(
                  backgroundColor: appStore.isDarkMode
                      ? selectedType == item
                          ? primaryColor
                          : textPrimaryColor
                      : selectedType == item
                          ? primaryColor
                          : Colors.transparent,
                  label: Text(orderType(item)),
                  elevation: 0,
                  labelStyle: primaryTextStyle(
                      size: 14,
                      color: appStore.isDarkMode
                          ? white
                          : selectedType == item
                              ? Colors.white
                              : textPrimaryColor),
                  padding: EdgeInsets.zero,
                  labelPadding: EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(defaultRadius),
                    side: BorderSide(color: selectedType == item ? primaryColor : borderColor, width: appStore.isDarkMode ? 0.2 : 1),
                  ),
                ).onTap(() {
                  selectedStatus = null;
                  selectedType = item;
                  setState(() {});
                });
              }).toList(),
            ),
            16.height,
            Text(language.status, style: boldTextStyle()),
            8.height,
            Wrap(
              spacing: 8,
              runSpacing: 0,
              children: statusList.map((item) {
                return Chip(
                  backgroundColor: appStore.isDarkMode
                      ? selectedStatus == item
                          ? primaryColor
                          : textPrimaryColor
                      : selectedStatus == item
                          ? primaryColor
                          : Colors.transparent,
                  label: Text(orderStatus(item)),
                  elevation: 0,
                  labelStyle: primaryTextStyle(
                      size: 14,
                      color: appStore.isDarkMode
                          ? white
                          : selectedStatus == item
                              ? Colors.white
                              : textPrimaryColor),
                  padding: EdgeInsets.zero,
                  labelPadding: EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(defaultRadius),
                    side: BorderSide(color: selectedStatus == item ? primaryColor : borderColor, width: appStore.isDarkMode ? 0.2 : 1),
                  ),
                ).onTap(() {
                  selectedType = null;
                  selectedStatus = item;
                  setState(() {});
                });
              }).toList(),
            ),
            16.height,
            Text(language.date, style: boldTextStyle()),
            16.height,
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                DateTimePicker(
                  controller: fromDateController,
                  type: DateTimePickerType.date,
                  fieldHintText: language.from,
                  lastDate: DateTime.now(),
                  firstDate: DateTime(2010),
                  onChanged: (value) {
                    fromDate = DateTime.parse(value);
                    fromDateController.text = value;
                    setState(() {});
                  },
                  validator: (value) {
                    if (fromDate == null && toDate != null) {
                      return language.mustSelectStartDate;
                    }
                    return null;
                  },
                  decoration: commonInputDecoration(suffixIcon: Ionicons.calendar_outline, hintText: language.from),
                ).expand(),
                16.width,
                DateTimePicker(
                  controller: toDateController,
                  type: DateTimePickerType.date,
                  lastDate: DateTime.now(),
                  firstDate: DateTime(2010),
                  onChanged: (value) {
                    toDate = DateTime.parse(value);
                    toDateController.text = value;
                    setState(() {});
                  },
                  validator: (value) {
                    if (fromDate != null && toDate != null) {
                      Duration difference = fromDate!.difference(toDate!);
                      if (difference.inDays >= 0) {
                        return language.toDateValidationMsg;
                      }
                    }
                    return null;
                  },
                  decoration: commonInputDecoration(suffixIcon: Ionicons.calendar_outline, hintText: language.to),
                ).expand(),
              ],
            ),
            30.height,
            Container(
              width: context.width(),
              child: dialogPrimaryButton(language.applyFilter, () {
                if (_formKey.currentState!.validate()) {
                  finish(context);
                  if (fromDate != null && toDate == null) {
                    toDate = DateTime.parse(DateTime.now().toString());
                  }
                  setValue(FILTER_DATA, FilterAttributeModel(orderStatus: selectedStatus,orderType: selectedType, fromDate: fromDate.toString(), toDate: toDate.toString()).toJson());
                  appStore.setFiltering(selectedStatus != null || fromDate != null || toDate != null || selectedType != null);
                  LiveStream().emit("UpdateOrderData");
                }
              }),
            ),
          ],
        ),
      ),
    );
  }
}
