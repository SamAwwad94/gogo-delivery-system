import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:image_picker/image_picker.dart';
import '../components/AddWhyDeliveryDialog.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../network/RestApis.dart';
import '../../utils/Colors.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../../utils/Extensions/shared_pref.dart';
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../utils/Extensions/app_common.dart';

class WebsiteWhyDeliveryScreen extends StatefulWidget {
  static String route = '/websiteWhyDelivery';

  const WebsiteWhyDeliveryScreen({super.key});

  @override
  State<WebsiteWhyDeliveryScreen> createState() => _WebsiteWhyDeliveryScreenState();
}

class _WebsiteWhyDeliveryScreenState extends State<WebsiteWhyDeliveryScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController whyTitleController = TextEditingController();
  TextEditingController whyDescriptionController = TextEditingController();

  GetFrontendDataResponseModel? frontEndData;
  List<WhyChooseData> whyChooseDataList = [];
  bool addWhyChooseDialogue = false;

  Uint8List? whyChooseFirstReviewImage;
  String? imageName;
  String? imagePath;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    appStore.setLoading(true);
    frontEndDataListApiCall();
  }

  frontEndDataListApiCall() async {
    await getFrontendDataList().then((value) {
      frontEndData = value;
      whyChooseDataList = frontEndData!.whyChoose!.data!;

      if (whyChooseDataList.length >= 3) {
        addWhyChooseDialogue = false;
      } else {
        addWhyChooseDialogue = true;
      }
      whyTitleController.text = frontEndData!.whyChoose!.title.validate();
      whyDescriptionController.text = frontEndData!.whyChoose!.description.validate();
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  deleteFrontEndDataApiCall(int id) async {
    appStore.setLoading(true);
    await deleteFrontendData(id).then((value) {
      toast(value.message.toString());
      init();
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  Future<void> saveWhyDeliverySettingsApiCall() async {
    if (formKey.currentState!.validate()) {
      appStore.setLoading(true);

      List req = [
        {
          "type": "why_choose",
          "key": "title",
          "value": whyTitleController.text,
        },
        {
          "type": "why_choose",
          "key": "description",
          "value": whyDescriptionController.text,
        },
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

  Widget saveSettingButton() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        InkWell(
          child: Container(
            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(color: primaryColor, borderRadius: BorderRadius.circular(defaultRadius)),
            child: Text(language.save, style: boldTextStyle(color: Colors.white)),
          ),
          onTap: () {
            if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
              toast(language.demo_admin_msg);
            } else {
              saveWhyDeliverySettingsApiCall();
            }
          },
        ),
      ],
    );
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
          /*onPopInvoked: (val){
            resetMenuIndex();
            Future.delayed(Duration(milliseconds: 100), ()
            {
              Navigator.pop(context, true);
            });
          },*/
          onWillPop: () {
            resetMenuIndex();
            Navigator.pop(context, true);
            return Future.value(true);
          },
          child: Scaffold(
            appBar: appBarWidget(language.whyDelivery),
            body: Stack(
              fit: StackFit.expand,
              children: [
                SingleChildScrollView(
                  //    padding: EdgeInsets.only(top: 16, left: 16, right: 16, bottom: 100),
                  padding: EdgeInsets.all(16),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      10.height,
                      Form(
                        key: formKey,
                        child: Container(
                          decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
                          child: Column(mainAxisAlignment: MainAxisAlignment.start, crossAxisAlignment: CrossAxisAlignment.start, children: [
                            RequiredValidation(required: true, titleText: language.title),
                            8.height,
                            AppTextField(
                              controller: whyTitleController,
                              textFieldType: TextFieldType.OTHER,
                              decoration: commonInputDecoration(),
                              textInputAction: TextInputAction.next,
                              validator: (s) {
                                if (s!.trim().isEmpty) return language.field_required_msg;
                                return null;
                              },
                            ),
                            16.height,
                            RequiredValidation(required: true, titleText: language.description),
                            8.height,
                            AppTextField(
                              controller: whyDescriptionController,
                              textFieldType: TextFieldType.OTHER,
                              decoration: commonInputDecoration(),
                              textInputAction: TextInputAction.next,
                              maxLines: 4,
                              minLines: 4,
                              validator: (s) {
                                if (s!.trim().isEmpty) return language.field_required_msg;
                                return null;
                              },
                            ),
                            16.height,
                            saveSettingButton(),
                          ]).paddingAll(10),
                        ),
                      ),
                      16.height,
                      if (whyChooseDataList.isNotEmpty) Text(language.sectionList, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)),
                      if (whyChooseDataList.isNotEmpty) staticWidget(),
                      16.height,
                    ],
                  ),
                ),
                appStore.isLoading ? loaderWidget() : SizedBox(),
                Positioned(
                  bottom: 16,
                  right: 16,
                  child: FloatingActionButton(
                    backgroundColor: primaryColor,
                    child: Icon(Icons.add, color: Colors.white),
                    onPressed: () {
                      if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                        toast(language.demo_admin_msg);
                      } else {
                        showInDialog(
                            context: context,
                            child: AddWhyDeliveryDialog(
                              isAdd: true,
                              onUpdate: () {
                                frontEndDataListApiCall();
                                setState(() {});
                              },
                            ));
                      }
                    },
                  ),
                ).visible(addWhyChooseDialogue),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget staticWidget() {
    return Column(
      children: [
        ListView.builder(
            physics: NeverScrollableScrollPhysics(),
            controller: ScrollController(),
            padding: EdgeInsets.only(top: 16, bottom: 16),
            shrinkWrap: true,
            itemCount: whyChooseDataList.length,
            itemBuilder: (context, index) {
              WhyChooseData mData = whyChooseDataList[index];
              return Stack(
                alignment: Alignment.topRight,
                children: [
                  Container(
                    padding: EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      border: Border.all(
                        color: Colors.grey.withOpacity(0.3),
                      ),
                      borderRadius: BorderRadius.circular(
                        defaultRadius,
                      ),
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            ClipRRect(borderRadius: radius(defaultRadius), child: commonCachedNetworkImage(mData.image, height: 80, width: 80, fit: BoxFit.cover, alignment: Alignment.center)),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  "${mData.title.toString()}",
                                  style: boldTextStyle(size: 14),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ).paddingOnly(top: 16),
                                4.height,
                                Text(
                                  "${mData.subtitle.toString()}",
                                  style: secondaryTextStyle(),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ).paddingAll(8).expand(),
                          ],
                        ),
                      ],
                    ),
                  ).paddingBottom(
                    10,
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      outlineActionIcon(
                        context,
                        Icons.edit,
                        Colors.green,
                        () {
                          showInDialog(
                            context: context,
                            child: AddWhyDeliveryDialog(
                              data: mData,
                              isAdd: false,
                              onUpdate: () {
                                frontEndDataListApiCall();
                              },
                            ),
                          );
                        },
                      ).paddingOnly(top: 10, right: 8),
                      outlineActionIcon(
                        context,
                        Icons.delete,
                        Colors.red,
                        () async {
                          await commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () async {
                            await deleteFrontEndDataApiCall(mData.id!);
                            finish(context);
                          }, title: language.deleteReview, subtitle: language.deleteReviewMsg);
                        },
                      ).paddingOnly(top: 10, right: 8, left: appStore.selectedLanguage == "ar" ? 8 : 0),
                    ],
                  ),
                ],
              );
            }),
        16.height,
      ],
    );
  }
}
