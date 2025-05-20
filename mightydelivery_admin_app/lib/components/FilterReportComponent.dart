import 'package:date_time_picker/date_time_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../utils/Colors.dart';

import '../../main.dart';
import '../utils/Common.dart';
import '../utils/Extensions/app_common.dart';

class FilterReportComponent extends StatefulWidget {
  final DateTime? initialFromDate;
  final DateTime? initialToDate;

  FilterReportComponent({this.initialFromDate, this.initialToDate});

  @override
  FilterReportComponentState createState() => FilterReportComponentState();
}

class FilterReportComponentState extends State<FilterReportComponent> {
  final _formKey = GlobalKey<FormState>();
  TextEditingController fromDateController = TextEditingController();
  TextEditingController toDateController = TextEditingController();

  DateTime? fromDate, toDate;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    fromDate = widget.initialFromDate;
    toDate = widget.initialToDate;
    if (fromDate != null) {
      fromDateController.text = fromDate.toString();
    }
    if (toDate != null) {
      toDateController.text = toDate.toString();
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      backgroundColor: appStore.isDarkMode ? textPrimaryColor : white,
      title: Text(language.applyFilter, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor, size: 20)),
      content: Padding(
        padding: EdgeInsets.all(6),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(language.from, style: primaryTextStyle()),
              8.height,
              DateTimePicker(
                controller: fromDateController,
                type: DateTimePickerType.date,
                fieldHintText: language.from + language.date,
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
                  } else {
                    return null;
                  }
                },
                decoration: commonInputDecoration(suffixIcon: Ionicons.calendar_outline, hintText: "${language.from} ${language.date.toLowerCase()}"),
              ),
              8.height,
              Text(language.to, style: primaryTextStyle()),
              8.height,
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
                decoration: commonInputDecoration(suffixIcon: Ionicons.calendar_outline, hintText: "${language.to} ${language.date.toLowerCase()}"),
              ),
              15.height,
              Row(
                children: [
                  Container(
                    child: dialogPrimaryButton(language.filter, () {
                      if (_formKey.currentState!.validate()) {
                        Navigator.pop(context, {
                          'from_date': fromDate,
                          'to_date': toDate,
                        });
                      }
                    }),
                  ),
                  20.width,
                  Container(
                    child: dialogPrimaryButton(language.reset, () {
                      fromDate = null;
                      toDate = null;
                      fromDateController.clear();
                      toDateController.clear();
                      FocusScope.of(context).unfocus();
                      setState(() {});
                      Navigator.pop(context, {
                        'from_date': fromDate,
                        'to_date': toDate,
                      });
                    }),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
