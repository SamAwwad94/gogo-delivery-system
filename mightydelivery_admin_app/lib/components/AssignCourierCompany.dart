import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/app_text_field.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/CourierCompaniesListModel.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Extensions/app_common.dart';

class AssignCourierCompany extends StatefulWidget {
  final Function onUpdate;
  final int? orderId;

  AssignCourierCompany({
    required this.onUpdate,
    this.orderId,
  });

  @override
  State<AssignCourierCompany> createState() => _AssignCourierCompanyState();
}

class _AssignCourierCompanyState extends State<AssignCourierCompany> {
  final _formKey = GlobalKey<FormState>();
  int currentPage = 1;
  int totalPage = 1;
  TextEditingController trackingIdCont = TextEditingController();
  List<CourierCompany> courierCompaniesList = [];
  CourierCompany? selectedType;

  @override
  void initState() {
    super.initState();
    init();
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  init() async {
    // await getCourierCompaniesListApiCall();
    for (currentPage; currentPage <= totalPage; currentPage++) {
      await getCourierCompaniesListApiCall();
    }
  }


  getCourierCompaniesListApiCall() async {
    appStore.setLoading(true);
    await getCourierCompaniesList(currentPage).then((value) {
      appStore.setLoading(false);
      currentPage = value.pagination!.currentPage.validate();
      totalPage = value.pagination!.totalPages.validate();
      if (currentPage == 1) {
        courierCompaniesList.clear();
      }
      value.data!.forEach((element) {
        courierCompaniesList.add(element);
      });
      selectedType = courierCompaniesList.first;
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  saveShippedOrderApiCall() async {
    Map req = {
      "couriercompany_id": selectedType!.id.validate(),
      "tracking_id": trackingIdCont.text.validate(),
    };
    appStore.setLoading(true);
    await saveShippedOrder(widget.orderId.validate(), req).then((value) {
      appStore.setLoading(false);
      toast(value.message.toString());
      Navigator.pop(context);
      widget.onUpdate.call();
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(
        language.courierCompany,
      ),
      body: Observer(builder: (context) {
        return Stack(
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                8.height,
                Form(
                  key: _formKey,
                  child: Padding(
                    padding: const EdgeInsets.all(18.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        Text(language.courierCompany, style: primaryTextStyle()),
                        8.height,
                        courierCompaniesList.isNotEmpty
                            ? DropdownButtonFormField<String>(
                                dropdownColor: Theme.of(context).cardColor,
                                value: selectedType?.name.validate(),
                                decoration: commonInputDecoration(),
                                items: courierCompaniesList.map<DropdownMenuItem<String>>((item) {
                                  return DropdownMenuItem(value: item.name, child: Text(item.name.validate(), style: primaryTextStyle()));
                                }).toList(),
                                onChanged: (value) {
                                  selectedType = courierCompaniesList.firstWhere(
                                    (item) => item.name == value,
                                    orElse: () => courierCompaniesList.first, // fallback to first item
                                  );
                                  setState(() {});
                                },
                              )
                            : SizedBox(),
                        SizedBox(height: 16),
                        Text(language.trakingId, style: primaryTextStyle()),
                        SizedBox(height: 8),
                        AppTextField(
                          controller: trackingIdCont,
                          textFieldType: TextFieldType.OTHER,
                          decoration: commonInputDecoration(),
                          validator: (s) {
                            if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                            return null;
                          },
                          errorThisFieldRequired: language.field_required_msg,
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
            loaderWidget().visible(appStore.isLoading)
          ],
        );
      }),
      bottomNavigationBar: Padding(
        padding: EdgeInsets.all(13),
        child: dialogPrimaryButton(language.save, () async {
          if (_formKey.currentState!.validate()) {
            saveShippedOrderApiCall();
          }
        }),
      ),
    );
  }
}
