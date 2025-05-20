import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:html_editor_enhanced/html_editor.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';
import 'package:permission_handler/permission_handler.dart';

import '../extensions/widgets.dart';
import '../main.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class WebsitePrivacyPolicyScreen extends StatefulWidget {
  static String route = '/websiteprivacypolicy';
  const WebsitePrivacyPolicyScreen({super.key});

  @override
  State<WebsitePrivacyPolicyScreen> createState() => _WebsitePrivacyPolicyScreenState();
}

class _WebsitePrivacyPolicyScreenState extends State<WebsitePrivacyPolicyScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  HtmlEditorController privacyPolicyController = HtmlEditorController();

  GetFrontendDataResponseModel? frontEndData;
  bool isPrmissionGranted = false;


  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    appStore.setLoading(true);
    frontEndDataListApiCall();
    setState(() {});
  }

  ///FrontendApi
  frontEndDataListApiCall() async {
    await getFrontendDataList().then((value) {
      frontEndData = value;

      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  Future<void> savePrivacyPolicySettings(String description) async {
      appStore.setLoading(true);
      List req = [
        {
          "type": "privacy_policy",
          "key": "privacy_policy",
          "value": description,
        },
      ];
      await setInvoiceSetting(jsonEncode(req)).then((value) {
        appStore.setLoading(false);

        toast(value.message);
        resetMenuIndex();
        Navigator.pop(context);
      }).catchError((error) {
        appStore.setLoading(false);
        log(error);
      });
  }
  Future<bool> requestPermissions() async {
    final status = await Permission.storage.request();
    isPrmissionGranted = status.isGranted;
    setState(() {});
    return status.isGranted;
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
            appBar: appBarWidget(language.privacyPolicy, actions: [
              GestureDetector(
                child: Text(language.clear, style: boldTextStyle(color: Colors.white)).paddingOnly(right: 12),
                onTap: () {
                  privacyPolicyController.clear();
                  setState(() {});
                },
              ).paddingOnly(right: 18, left: appStore.isDarkMode ? 16 : 0)
            ]),
            body: Stack(
              fit: StackFit.expand,
              children: [
/*
                Form(
                  key: formKey,
                  child: AppTextField(
                    controller: privacyPolicyController,
                    textFieldType: TextFieldType.OTHER,
                    decoration: commonInputDecoration(),
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.multiline,
                    autoFocus: true,
                    scrollPadding: EdgeInsets.all(20),
                    maxLines: 35,
                    minLines: 35,
                    validator: (s) {
                      if (s!.trim().isEmpty) return language.field_required_msg;
                      return null;
                    },
                  ).paddingOnly(left: 10, right: 10, top: 10),
                ),
*/
                appStore.isLoading ? loaderWidget() :
                HtmlEditor(
                  controller: privacyPolicyController,
                  htmlEditorOptions: HtmlEditorOptions(
                    hint: language.description,
                    // shouldEnsureVisible: true,
                    autoAdjustHeight: true,
                    adjustHeightForKeyboard: true,
                    androidUseHybridComposition: true,
                  ),
                  htmlToolbarOptions: HtmlToolbarOptions(
                    renderBorder: true,
                    gridViewHorizontalSpacing: 0.5,
                    toolbarPosition: ToolbarPosition.aboveEditor,
                    toolbarType: ToolbarType.nativeGrid,
                    buttonBorderColor: primaryColor,
                    dropdownBoxDecoration: appStore.isDarkMode ? BoxDecoration(color: Colors.white) : null,
                    defaultToolbarButtons: [
                      StyleButtons(),
                      FontSettingButtons(),
                      InsertButtons(video: false, audio: false, hr: false),
                      // OtherButtons(),
                    ],
                    onButtonPressed: (ButtonType type, bool? status, Function? updateStatus) async {
                      print("button '${type}' pressed, the current selected status is $status");
                      if (type == ButtonType.picture) {
                        await requestPermissions();
                        if (!isPrmissionGranted) {
                          return false;
                        }
                      }
                      return true;
                    },
                  ),
                  callbacks: Callbacks(
                    onInit: () async {
                      privacyPolicyController.setText(frontEndData!.privacyPolicy.validate());
                    },
                      onImageLinkInsert: (val){
                      print("image ==> ${val}");
                      }
                  ),
                  otherOptions: OtherOptions(
                    height: context.height() * 0.9,
                    decoration: BoxDecoration(
                      color: appStore.isDarkMode ? Colors.white: null,
                      border: Border.all(width: 1, color: Colors.grey.withOpacity(0.35)),
                      borderRadius: BorderRadius.circular(13),
                    ),
                  ),
                ).paddingAll(8),

              ],
            ),
            bottomNavigationBar: Padding(
              padding: EdgeInsets.all(16),
              child: dialogPrimaryButton(language.save, () async {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demoAdminMsg);
                } else {
                  if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                    toast(language.demo_admin_msg);
                  } else {
                      String description = await privacyPolicyController.getText();
                      savePrivacyPolicySettings(description);
                  }
                }
              }),
            ),
          ),
        );
      },
    );
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

}
