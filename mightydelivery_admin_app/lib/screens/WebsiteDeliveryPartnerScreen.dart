import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../network/RestApis.dart';
import '../../utils/Colors.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../../utils/Extensions/shared_pref.dart';
import '../components/AddDeliveryPartnerDialog.dart';
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../network/NetworkUtils.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Images.dart';

class WebsiteDeliveryPartnerScreen extends StatefulWidget {
  static String route = '/WebsiteDeliveryPartner';

  const WebsiteDeliveryPartnerScreen({super.key});

  @override
  State<WebsiteDeliveryPartnerScreen> createState() => _WebsiteDeliveryPartnerScreenState();
}

class _WebsiteDeliveryPartnerScreenState extends State<WebsiteDeliveryPartnerScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();
  final ImagePicker _picker = ImagePicker();

  TextEditingController deliveryPartnerTitleController = TextEditingController();
  TextEditingController deliveryPartnerDescriptionController = TextEditingController();
  List<Benefits> benefitsDataList = [];
  GetFrontendDataResponseModel? frontEndData;
  Uint8List? imageUnit8List;
  String? imageName;
  String? imagePath;
  XFile? image;

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
      deliveryPartnerTitleController.text = frontEndData!.deliveryPartner!.title.validate();
      deliveryPartnerDescriptionController.text = frontEndData!.deliveryPartner!.subtitle.validate();
      benefitsDataList = frontEndData!.deliveryPartner!.benefits!;
      appStore.setDeliveryPartnerImage(frontEndData!.deliveryPartner!.image.validate());
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  Future<void> saveDeliveryPartnerImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "delivery_partner";
    multiPartRequest.fields["key"] = "delivery_partner_image";
    if (imageUnit8List != null) {
      multiPartRequest.files.add(MultipartFile.fromBytes('delivery_partner_image', imageUnit8List!, filename: imageName));
      multiPartRequest.headers.addAll(buildHeaderTokens());
      appStore.setLoading(true);
      sendMultiPartRequest(
        multiPartRequest,
        onSuccess: (data) async {
          appStore.setLoading(false);
          // getInvoiceSetting();
          setState(() {});
        },
        onError: (error) {
          toast(error.toString());
          appStore.setLoading(false);
        },
      ).catchError((e) {
        appStore.setLoading(false);
        toast(e.toString());
      });
    }
  }

  Future<void> saveDeliveryPartnerSettingsApiCall() async {
    appStore.setLoading(true);

    if (formKey.currentState!.validate()) {
      List req = [
        {
          "type": "delivery_partner",
          "key": "title",
          "value": deliveryPartnerTitleController.text,
        },
        {
          "type": "delivery_partner",
          "key": "subtitle",
          "value": deliveryPartnerDescriptionController.text,
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
              if (formKey.currentState!.validate()) {
                if (imageUnit8List != null) {
                  saveDeliveryPartnerImage();
                  saveDeliveryPartnerSettingsApiCall();
                } else if (appStore.deliveryPartnerImage.isEmptyOrNull) {
                  toast(language.imgSelectValidation);
                  appStore.setLoading(false);
                } else {
                  saveDeliveryPartnerSettingsApiCall();
                }
              }
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

  pickDeliveryPartnerImage() async {
    image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      imagePath = image!.path;
      imageUnit8List = await image!.readAsBytes();
      imageName = image!.name;
    }
    setState(() {});
  }

  Widget deliveryPartnerImageFunction() {
    if (imagePath != null) {
      return Image.file(File(imagePath!), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.deliveryPartnerImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.deliveryPartnerImage.toString(), fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(defaultAssetImage: DELIVERY_PARTNER_IMAGE, appStore.deliveryPartnerImage, height: 90, width: 90);
      }
    }
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
            appBar: appBarWidget(language.deliveryPartner),
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
                            Row(
                              children: [
                                RequiredValidation(required: true, titleText: language.title),
                                8.width,
                                Text(
                                  language.max100character,
                                  style: secondaryTextStyle(color: redColor, size: 12),
                                ),
                              ],
                            ),
                            8.height,
                            AppTextField(
                              controller: deliveryPartnerTitleController,
                              textFieldType: TextFieldType.OTHER,
                              decoration: commonInputDecoration(),
                              textInputAction: TextInputAction.next,
                              inputFormatters: [LengthLimitingTextInputFormatter(100)],
                              validator: (s) {
                                if (s!.trim().isEmpty) return language.field_required_msg;
                                return null;
                              },
                            ),
                            16.height,
                            Row(
                              children: [
                                RequiredValidation(required: true, titleText: language.description),
                                8.width,
                                Text(
                                  language.max250character,
                                  style: secondaryTextStyle(color: redColor, size: 12),
                                ),
                              ],
                            ),
                            8.height,
                            AppTextField(
                              controller: deliveryPartnerDescriptionController,
                              textFieldType: TextFieldType.OTHER,
                              decoration: commonInputDecoration(),
                              textInputAction: TextInputAction.next,
                              inputFormatters: [LengthLimitingTextInputFormatter(250)],
                              maxLines: 4,
                              minLines: 4,
                              validator: (s) {
                                if (s!.trim().isEmpty) return language.field_required_msg;
                                return null;
                              },
                            ),
                            16.height,
                            Text(language.image, style: primaryTextStyle()),
                            16.height,
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                TextButton(
                                  style: TextButton.styleFrom(
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(defaultRadius)),
                                    elevation: 0,
                                    padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                    backgroundColor: Colors.grey.withOpacity(0.2),
                                  ),
                                  child: Text(language.select_file, style: boldTextStyle(color: Colors.grey)),
                                  onPressed: () {
                                    pickDeliveryPartnerImage();
                                  },
                                ),
                                8.width,
                                ClipRRect(borderRadius: radius(defaultRadius), child: deliveryPartnerImageFunction())
                              ],
                            ),
                            8.height,
                            saveSettingButton(),
                          ]).paddingAll(10),
                        ),
                      ),
                      16.height,
                      if (benefitsDataList.isNotEmpty) Text(language.sectionList, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)),
                      if (benefitsDataList.isNotEmpty) staticWidget(),
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
                            child: AddDeliveryPartnerDialog(
                              isAdd: true,
                              onUpdate: () {
                                frontEndDataListApiCall();
                                setState(() {});
                              },
                            ));
                      }
                    },
                  ),
                ),
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
            itemCount: benefitsDataList.length,
            itemBuilder: (context, index) {
              Benefits mData = benefitsDataList[index];
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
                            child: AddDeliveryPartnerDialog(
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
                      ).paddingOnly(top: 10, right: 8, left: appStore.isDarkMode ? 8 : 0),
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
