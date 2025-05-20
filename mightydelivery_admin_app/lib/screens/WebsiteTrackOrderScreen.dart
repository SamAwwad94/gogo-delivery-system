import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';

import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class WebsiteTrackOrderScreen extends StatefulWidget {
  const WebsiteTrackOrderScreen({super.key});

  @override
  State<WebsiteTrackOrderScreen> createState() => _WebsiteTrackOrderScreenState();
}

class _WebsiteTrackOrderScreenState extends State<WebsiteTrackOrderScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController titleController = TextEditingController();
  TextEditingController subTitleController = TextEditingController();
  TextEditingController pageTitleController = TextEditingController();
  TextEditingController pageDescriptionController = TextEditingController();

  GetFrontendDataResponseModel? frontEndData;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    await frontEndDataListApiCall();
  }

  frontEndDataListApiCall() async {
    appStore.setLoading(true);
    await getFrontendDataList().then((value) {
      frontEndData = value;
      print(frontEndData!.toJson().toString());
      titleController.text = frontEndData!.trackOrder!.trackOrderTitle.validate();
      subTitleController.text = frontEndData!.trackOrder!.trackOrderSubtitle.validate();
      pageTitleController.text = frontEndData!.trackOrder!.trackPageTitle.validate();
      pageDescriptionController.text = frontEndData!.trackOrder!.trackPageDescription.validate();
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  Future<void> saveTrackOrderSettings() async {
    if (formKey.currentState!.validate()) {
      appStore.setLoading(true);
      List req = [
        {
          "type": "track_order",
          "key": "track_order_title",
          "value": titleController.text,
        },
        {
          "type": "track_order",
          "key": "track_order_subtitle",
          "value": subTitleController.text,
        },
        {
          "type": "track_order",
          "key": "track_page_title",
          "value": pageTitleController.text,
        },
        {
          "type": "track_order",
          "key": "track_page_description",
          "value": pageDescriptionController.text,
        }
      ];
      await setInvoiceSetting(jsonEncode(req)).then((value) {
        appStore.setLoading(false);

        toast(value.message);
      }).catchError((error) {
        appStore.setLoading(false);
        log(error);
      });
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Observer(
      builder: (context) {
        return WillPopScope(
          onWillPop: () {
            resetMenuIndex();
            Navigator.pop(context, true);
            return Future.value(true);
          },
          child: Scaffold(
            appBar: appBarWidget(language.trackOrder),
            body: Stack(
              fit: StackFit.expand,
              children: [
                SingleChildScrollView(
                  //    padding: EdgeInsets.only(top: 16, left: 16, right: 16, bottom: 100),
                  padding: EdgeInsets.all(16),
                  child: Form(
                    key: formKey,
                    child: Column(
                      children: [
                        Container(
                          //    decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius)),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              16.height,
                              webSiteAboutUsWidget(),
                              16.height,
                            ],
                          ),
                        ),
                        16.height,
                      ],
                    ),
                  ),
                ),
                appStore.isLoading ? loaderWidget() : SizedBox(),
              ],
            ),
            bottomNavigationBar: Padding(
              padding: EdgeInsets.all(16),
              child: dialogPrimaryButton(language.save, () async {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  if (formKey.currentState!.validate()) {
                    saveTrackOrderSettings();
                  }
                }
              }),
            ),
          ),
        );
      },
    );
  }

  Widget webSiteAboutUsWidget() {
    return Column(
      children: [
        Container(
          //   padding: EdgeInsets.all(16),
          decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius)),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              RequiredValidation(required: false, titleText: language.title),
              8.height,
              AppTextField(
                controller: titleController,
                textFieldType: TextFieldType.OTHER,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                validator: (s) {
                  // if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
              RequiredValidation(required: false, titleText: language.subTitle),
              8.height,
              AppTextField(
                controller: subTitleController,
                textFieldType: TextFieldType.OTHER,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                inputFormatters: [LengthLimitingTextInputFormatter(100)],
                validator: (s) {
                  // if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
              RequiredValidation(required: false, titleText: language.pageTitle),
              8.height,
              AppTextField(
                controller: pageTitleController,
                textFieldType: TextFieldType.OTHER,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                maxLines: 3,
                minLines: 3,
                validator: (s) {
                  // if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
              RequiredValidation(required: false, titleText: language.pageDescription),
              16.height,
              AppTextField(
                controller: pageDescriptionController,
                textFieldType: TextFieldType.OTHER,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                maxLines: 4,
                minLines: 4,
                validator: (s) {
                  // if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
            ],
          ),
        ),
        16.height,
      ],
    );
  }
}
