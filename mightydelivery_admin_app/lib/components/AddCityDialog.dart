import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';

import '../main.dart';
import '../models/CityListModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/CommonApiCall.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';

class AddCityDialog extends StatefulWidget {
  static String tag = '/AppAddCityDialog';
  final CityData? cityData;
  final Function()? onUpdate;

  AddCityDialog({this.cityData, this.onUpdate});

  @override
  AddCityDialogState createState() => AddCityDialogState();
}

class AddCityDialogState extends State<AddCityDialog> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  TextEditingController cityNameController = TextEditingController();
  TextEditingController fixedChargeController = TextEditingController();
  TextEditingController cancelChargeController = TextEditingController();
  TextEditingController minDistanceController = TextEditingController();
  TextEditingController minWeightController = TextEditingController();
  TextEditingController perDistanceChargeController = TextEditingController();
  TextEditingController perWeightChargeChargeController = TextEditingController();
  TextEditingController commissionController = TextEditingController();

  int? selectedCountryId;
  String distanceType = '';
  String weightType = '';

  String commissionType = 'fixed';

  List<String> commissionTypeList = ['fixed', 'percentage'];
  bool isUpdate = false;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    afterBuildCreated(() {
      appStore.setLoading(true);
    });

    await getAllCountryApiCall();
    isUpdate = widget.cityData != null;
    if (isUpdate) {
      cityNameController.text = widget.cityData!.name!;
      fixedChargeController.text = widget.cityData!.fixedCharges.toString();
      cancelChargeController.text = widget.cityData!.cancelCharges.toString();
      minDistanceController.text = widget.cityData!.minDistance.toString();
      minWeightController.text = widget.cityData!.minWeight.toString();
      perDistanceChargeController.text = widget.cityData!.perDistanceCharges.toString();
      perWeightChargeChargeController.text = widget.cityData!.perWeightCharges.toString();
      appStore.countryList.forEach((element) {
        if (element.id == widget.cityData!.countryId) {
          selectedCountryId = widget.cityData!.countryId;
        }
      });
      commissionType = widget.cityData!.commissionType.isEmptyOrNull ? 'fixed' : widget.cityData!.commissionType.toString();
      commissionController.text = widget.cityData!.adminCommission.toString();
      getDistanceAndWeightType();
      setState(() {});
    }
    appStore.setLoading(false);
  }

  getDistanceAndWeightType() {
    appStore.countryList.forEach((e) {
      if (e.id == selectedCountryId) {
        distanceType = e.distanceType!;
        weightType = e.weightType!;
      }
    });
  }

  addCityApiCall() async {
    if (_formKey.currentState!.validate()) {
      Navigator.pop(context);
      Map req = {
        "id": isUpdate ? widget.cityData!.id : "",
        "country_id": selectedCountryId,
        "name": cityNameController.text,
        "fixed_charges": fixedChargeController.text,
        "cancel_charges": cancelChargeController.text,
        "min_distance": minDistanceController.text,
        "min_weight": minWeightController.text,
        "per_distance_charges": perDistanceChargeController.text,
        "per_weight_charges": perWeightChargeChargeController.text,
        "commission_type": commissionType,
        "admin_commission": commissionController.text.trim()
      };
      appStore.setLoading(true);
      await addCity(req).then((value) {
        appStore.setLoading(false);
        toast(value.message.toString());
        widget.onUpdate!.call();
      }).catchError((error) {
        appStore.setLoading(false);
        toast(error.toString());
      });
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: context.width(),
      height: context.height() / 2,
      child: AlertDialog(
        //   contentPadding: EdgeInsets.all(16),
        //   titlePadding: EdgeInsets.only(left: 16, right: 8, top: 8),
        title: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(isUpdate ? language.updateCity : language.addCity, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor, size: 20)),
            Icon(Icons.close).onTap(() {
              Navigator.pop(context);
            })
          ],
        ),
        content: Observer(
          builder: (_) {
            return Container(
              width: context.width() * 100,
              height: context.height() / 1.5,
              child: Stack(
                children: [
                  Form(
                    key: _formKey,
                    child: SingleChildScrollView(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          8.height,
                          Text(language.cityName, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: cityNameController,
                            textFieldType: TextFieldType.NAME,
                            decoration: commonInputDecoration(),
                            textInputAction: TextInputAction.next,
                            errorThisFieldRequired: language.fieldRequiredMsg,
                          ),
                          SizedBox(height: 8),
                          Text(language.selectCountry, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          DropdownButtonFormField<int>(
                            isExpanded: true,
                            dropdownColor: Theme.of(context).cardColor,
                            value: selectedCountryId,
                            decoration: commonInputDecoration(),
                            items: appStore.countryList.map<DropdownMenuItem<int>>((item) {
                              return DropdownMenuItem(
                                value: item.id,
                                child: Text(item.name!, style: primaryTextStyle()),
                              );
                            }).toList(),
                            onChanged: (value) {
                              selectedCountryId = value;
                              getDistanceAndWeightType();
                              setState(() {});
                            },
                            validator: (value) {
                              if (selectedCountryId == null) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text(language.fixedCharge, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: fixedChargeController,
                            textFieldType: TextFieldType.OTHER,
                            decoration: commonInputDecoration(),
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            inputFormatters: [
                              FilteringTextInputFormatter.allow(RegExp('[0-9 .]')),
                            ],
                            textInputAction: TextInputAction.next,
                            validator: (s) {
                              if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text(language.cancelCharge, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: cancelChargeController,
                            textFieldType: TextFieldType.OTHER,
                            decoration: commonInputDecoration(),
                            textInputAction: TextInputAction.next,
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            inputFormatters: [
                              FilteringTextInputFormatter.allow(RegExp('[0-9 .]')),
                            ],
                            validator: (s) {
                              if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text('${language.minimumDistance} ${distanceType.isNotEmpty ? '($distanceType)' : ''}', style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: minDistanceController,
                            textFieldType: TextFieldType.OTHER,
                            decoration: commonInputDecoration(),
                            textInputAction: TextInputAction.next,
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            inputFormatters: [
                              FilteringTextInputFormatter.allow(RegExp('[0-9 .]')),
                            ],
                            validator: (s) {
                              if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text('${language.minimumWeight} ${weightType.isNotEmpty ? '($weightType)' : ''}', style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: minWeightController,
                            textFieldType: TextFieldType.OTHER,
                            decoration: commonInputDecoration(),
                            textInputAction: TextInputAction.next,
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            inputFormatters: [
                              FilteringTextInputFormatter.allow(RegExp('[0-9 .]')),
                            ],
                            validator: (s) {
                              if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text(language.perDistanceCharge, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: perDistanceChargeController,
                            textFieldType: TextFieldType.OTHER,
                            decoration: commonInputDecoration(),
                            textInputAction: TextInputAction.next,
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            inputFormatters: [
                              FilteringTextInputFormatter.allow(RegExp('[0-9 .]')),
                            ],
                            validator: (s) {
                              if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text(language.perWeightCharge, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: perWeightChargeChargeController,
                            textFieldType: TextFieldType.PHONE,
                            decoration: commonInputDecoration(),
                            textInputAction: TextInputAction.next,
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            inputFormatters: [
                              FilteringTextInputFormatter.allow(RegExp('[0-9 .]')),
                            ],
                            validator: (s) {
                              if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text(language.commissionType, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          DropdownButtonFormField<String>(
                            isExpanded: true,
                            dropdownColor: Theme.of(context).cardColor,
                            value: commissionType,
                            decoration: commonInputDecoration(),
                            items: commissionTypeList.map<DropdownMenuItem<String>>((item) {
                              return DropdownMenuItem(value: item, child: Text(item, style: primaryTextStyle()));
                            }).toList(),
                            onChanged: (value) {
                              commissionType = value.validate();
                              setState(() {});
                            },
                            validator: (value) {
                              if (commissionType.isEmptyOrNull) return language.fieldRequiredMsg;
                              return null;
                            },
                          ),
                          SizedBox(height: 8),
                          Text(language.adminCommission, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: commissionController,
                            textFieldType: TextFieldType.NAME,
                            decoration: commonInputDecoration(),
                            textInputAction: TextInputAction.next,
                            inputFormatters: [
                              FilteringTextInputFormatter.allow(RegExp('[0-9 .]')),
                            ],
                            errorThisFieldRequired: language.fieldRequiredMsg,
                          ),
                          20.height,
                          Row(
                            children: [
                              dialogSecondaryButton(language.cancel, () {
                                Navigator.pop(context);
                              }).expand(),
                              SizedBox(width: 16),
                              dialogPrimaryButton(isUpdate ? language.update : language.add, () {
                                if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                  toast(language.demoAdminMsg);
                                } else {
                                  addCityApiCall();
                                }
                              }).expand(),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  Visibility(visible: appStore.isLoading, child: Positioned.fill(child: loaderWidget())),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
