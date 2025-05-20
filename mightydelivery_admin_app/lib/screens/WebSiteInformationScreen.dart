import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../network/NetworkUtils.dart';
import '../../network/RestApis.dart';
import '../../utils/Colors.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../../utils/Extensions/shared_pref.dart';
import '../../utils/Images.dart';
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../utils/Extensions/app_common.dart';

class WebSiteInformationScreen extends StatefulWidget {
  static String route = '/websiteinformation';

  const WebSiteInformationScreen({super.key});

  @override
  State<WebSiteInformationScreen> createState() => _WebSiteInformationScreenState();
}

class _WebSiteInformationScreenState extends State<WebSiteInformationScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();
  TextEditingController appNameController = TextEditingController();
  TextEditingController createOrderDescriptionController = TextEditingController();
  final ImagePicker _picker = ImagePicker();

  String? deliveryManImageName;
  String? pickedDeliveryImagePath;
  Uint8List? newDeliveryManImage;

  String? roadImageName;
  String? pickedRoadImagePath;
  Uint8List? newRoadImage;

  String? appLogoImageName;
  String? pickedAppLogoImagePath;

  Uint8List? newAppLogoImage;
  GetFrontendDataResponseModel? frontEndData;
  XFile? deliveryManImage;
  XFile? roadImageImage;
  XFile? appLogoImage;
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
      appNameController.text = frontEndData!.appName.validate();
      createOrderDescriptionController.text = frontEndData!.createOrderDescription.validate();
      appStore.setDeliveryManImage(frontEndData!.deliveryManImage.validate());
      appStore.setRoadImage(frontEndData!.deliveryRoadImage.validate());
      appStore.setAppLogoImage(frontEndData!.appLogoImage.validate());

      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  pickDeliveryManImage() async {
    deliveryManImage = await _picker.pickImage(source: ImageSource.gallery);
    if (deliveryManImage != null) {
      pickedDeliveryImagePath = deliveryManImage!.path;
      newDeliveryManImage = await deliveryManImage!.readAsBytes();
      deliveryManImageName = deliveryManImage!.name;
    }
    setState(() {});
  }

  pickRoadImage() async {
    roadImageImage = await _picker.pickImage(source: ImageSource.gallery);
    if (roadImageImage != null) {
      pickedRoadImagePath = roadImageImage!.path;
      newRoadImage = await roadImageImage!.readAsBytes();
      roadImageName = roadImageImage!.name;
    }
    setState(() {});
  }

  pickAppLogoImage() async {
    appLogoImage = await _picker.pickImage(source: ImageSource.gallery);
    if (appLogoImage != null) {
      pickedAppLogoImagePath = appLogoImage!.path;
      newAppLogoImage = await appLogoImage!.readAsBytes();
      appLogoImageName = appLogoImage!.name;
    }
    setState(() {});
  }

  Future<void> saveDeliveryManImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "app_content";
    multiPartRequest.fields["key"] = "delivery_man_image";
    multiPartRequest.files.add(MultipartFile.fromBytes('delivery_man_image', newDeliveryManImage!, filename: deliveryManImageName));
    multiPartRequest.headers.addAll(buildHeaderTokens());
    appStore.setLoading(true);
    sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
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

  Future<void> saveAppLogoImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "app_content";
    multiPartRequest.fields["key"] = "app_logo_image";
    multiPartRequest.files.add(MultipartFile.fromBytes('app_logo_image', newAppLogoImage!, filename: appLogoImageName));
    multiPartRequest.headers.addAll(buildHeaderTokens());
    appStore.setLoading(true);
    sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
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

  Future<void> saveRoadImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "app_content";
    multiPartRequest.fields["key"] = "delivery_road_image";
    multiPartRequest.files.add(MultipartFile.fromBytes('delivery_road_image', newRoadImage!, filename: roadImageName));
    multiPartRequest.headers.addAll(buildHeaderTokens());
    appStore.setLoading(true);
    sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
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

  Future<void> saveWhyDeliverySettings() async {
    if (formKey.currentState!.validate()) {
      appStore.setLoading(true);
      List req = [
        {
          "type": "app_content",
          "key": "app_name",
          "value": appNameController.text,
        },
        {
          "type": "app_content",
          "key": "create_order_description",
          "value": createOrderDescriptionController.text,
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
          onTap: () async {
            if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
              toast(language.demo_admin_msg);
            } else {
              saveWhyDeliverySettings();
              // if user has selected new image then upload
              if (newDeliveryManImage != null && newDeliveryManImage!.isNotEmpty) {
                await saveDeliveryManImage();
              } else {
                if (appStore.deliveryManImage.isEmptyOrNull && appStore.deliveryManImage.isEmpty) {
                  toast(language.selectDeliveryManImgValidation);
                } else if (newDeliveryManImage != null && newDeliveryManImage!.isNotEmpty) {
                  toast(language.selectDeliveryManImgValidation);
                }
              }
              //road image
              if (newRoadImage != null && newRoadImage!.isNotEmpty) {
                await saveRoadImage();
              } else {
                if (appStore.roadImage.isEmptyOrNull && appStore.roadImage.isEmpty) {
                  toast(language.selectRoadImgValidation);
                } else if (newRoadImage != null && newRoadImage!.isNotEmpty) {
                  toast(language.selectRoadImgValidation);
                }
              }
              //  app logo
              if (newAppLogoImage != null && newAppLogoImage!.isNotEmpty) {
                await saveAppLogoImage();
              } else {
                if (appStore.appLogoImage.isEmptyOrNull && appStore.appLogoImage.isEmpty) {
                  toast(language.selectAppLogoImgValidation);
                } else if (newAppLogoImage != null && newAppLogoImage!.isNotEmpty) {
                  toast(language.selectAppLogoImgValidation);
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
            appBar: appBarWidget(language.information),
            body: Stack(
              fit: StackFit.expand,
              children: [
                SingleChildScrollView(
                  padding: EdgeInsets.all(16),
                  child: Form(
                    key: formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [webSiteInformationWidget(), 18.height],
                    ),
                  ),
                ),
                Observer(builder: (context) => Visibility(visible: appStore.isLoading, child: loaderWidget())),
              ],
            ),
            bottomNavigationBar: Padding(
              padding: EdgeInsets.all(16),
              child: dialogPrimaryButton(language.save, () async {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  saveWhyDeliverySettings();
                  // if user has selected new image then upload
                  if (newDeliveryManImage != null && newDeliveryManImage!.isNotEmpty) {
                    await saveDeliveryManImage();
                  } else {
                    if (appStore.deliveryManImage.isEmptyOrNull && appStore.deliveryManImage.isEmpty) {
                      toast(language.selectDeliveryManImgValidation);
                    } else if (newDeliveryManImage != null && newDeliveryManImage!.isNotEmpty) {
                      toast(language.selectDeliveryManImgValidation);
                    }
                  }
                  //road image
                  if (newRoadImage != null && newRoadImage!.isNotEmpty) {
                    await saveRoadImage();
                  } else {
                    if (appStore.roadImage.isEmptyOrNull && appStore.roadImage.isEmpty) {
                      toast(language.selectRoadImgValidation);
                    } else if (newRoadImage != null && newRoadImage!.isNotEmpty) {
                      toast(language.selectRoadImgValidation);
                    }
                  }
                  //  app logo
                  if (newAppLogoImage != null && newAppLogoImage!.isNotEmpty) {
                    await saveAppLogoImage();
                  } else {
                    if (appStore.appLogoImage.isEmptyOrNull && appStore.appLogoImage.isEmpty) {
                      toast(language.selectAppLogoImgValidation);
                    } else if (newAppLogoImage != null && newAppLogoImage!.isNotEmpty) {
                      toast(language.selectAppLogoImgValidation);
                    }
                  }
                }
              }),
            ),
          ),
        );
      },
    );
  }

  Widget deliveryManImageFunction() {
    if (pickedDeliveryImagePath != null) {
      return Image.file(File(deliveryManImage!.path), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.deliveryManImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.deliveryManImage.toString(), fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(defaultAssetImage: DELIVERY_MAN_IMAGE, appStore.deliveryManImage, height: 90, width: 90);
      }
    }
  }

  Widget roadImageFunction() {
    if (pickedRoadImagePath != null) {
      return Image.file(File(roadImageImage!.path), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.roadImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.roadImage.toString(), fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(defaultAssetImage: ROAD_IMAGE, appStore.roadImage, fit: BoxFit.fitWidth, height: 90, width: 90);
      }
    }
  }

  Widget appLogoImageFunction() {
    if (pickedAppLogoImagePath != null) {
      return Image.file(File(appLogoImage!.path), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.appLogoImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.appLogoImage.toString(), fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(defaultAssetImage: APP_LOGO_IMAGE, appStore.appLogoImage, height: 90, width: 90);
      }
    }
  }

  Widget webSiteInformationWidget() {
    return Column(
      children: [
        Container(
          // padding: EdgeInsets.all(16),
          //   decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius), boxShadow: commonBoxShadow()),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              RequiredValidation(required: true, titleText: language.appNameTitle),
              8.height,
              AppTextField(
                controller: appNameController,
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
                controller: createOrderDescriptionController,
                textFieldType: TextFieldType.OTHER,
                keyboardType: TextInputType.multiline,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.go,
                maxLines: 4,
                minLines: 4,
                validator: (s) {
                  if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
              Text(language.deliveryManImage, style: primaryTextStyle()),
              8.height,
              Container(
                decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3))),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
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
                        pickDeliveryManImage();
                      },
                    ),
                    20.width,
                    deliveryManImageFunction(),
                  ],
                ).paddingAll(10),
              ),
              16.height,
              Text(language.roadImage, style: primaryTextStyle()),
              8.height,
              Container(
                decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3))),
                child: Row(
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
                        pickRoadImage();
                      },
                    ),
                    20.width,
                    roadImageFunction().cornerRadiusWithClipRRect(defaultRadius),
                  ],
                ).paddingAll(10),
              ),
              16.height,
              Text(language.appLogoImage, style: primaryTextStyle()),
              8.height,
              Container(
                decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3))),
                child: Row(
                  children: [
                    TextButton(
                      style: TextButton.styleFrom(
                        shape: RoundedRectangleBorder(borderRadius: radius()),
                        elevation: 0,
                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        backgroundColor: Colors.grey.withOpacity(0.2),
                      ),
                      child: Text(language.select_file, style: boldTextStyle(color: Colors.grey)),
                      onPressed: () {
                        pickAppLogoImage();
                      },
                    ),
                    20.width,
                    //  deliveryManImageFunction(),
                    appLogoImageFunction().cornerRadiusWithClipRRect(defaultRadius),
                  ],
                ).paddingAll(10),
              ),
              16.height,
              Text(
                '${"#"}' '${language.note} ${language.deliveryManImageValidationMsg}',
                style: secondaryTextStyle(color: redColor, size: 12),
              ),
              Text(
                '${"#"}' '${language.note} ${language.roadImageValidationMsg}',
                style: secondaryTextStyle(color: redColor, size: 12),
              ),
              Text(
                '${"#"}' '${language.note}  ${language.logoImageValidationMsg}',
                style: secondaryTextStyle(color: redColor, size: 12),
              ),
            ],
          ),
        ),
        16.height,
      ],
    );
  }
}
