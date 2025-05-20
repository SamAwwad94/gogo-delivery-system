import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../network/RestApis.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/widgets.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../network/NetworkUtils.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';
import '../utils/Images.dart';

class WebsiteDownloadAppScreen extends StatefulWidget {
  static String route = '/websitedownloadapp';

  const WebsiteDownloadAppScreen({super.key});

  @override
  State<WebsiteDownloadAppScreen> createState() => _WebsiteDownloadAppScreenState();
}

class _WebsiteDownloadAppScreenState extends State<WebsiteDownloadAppScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController downloadTitleController = TextEditingController();
  TextEditingController downloadDescriptionController = TextEditingController();
  TextEditingController downloadFooterContentController = TextEditingController();

  final ImagePicker _picker = ImagePicker();
  GetFrontendDataResponseModel? frontEndData;
  Uint8List? imageUint8List;
  XFile? image;
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

  ///FRONTEND API
  frontEndDataListApiCall() async {
    await getFrontendDataList().then((value) {
      frontEndData = value;

      downloadTitleController.text = frontEndData!.downloadApp!.downloadTitle.validate();
      downloadDescriptionController.text = frontEndData!.downloadApp!.downloadDescription.validate();
      downloadFooterContentController.text = frontEndData!.downloadFooterContent.validate();
      appStore.setDownloadAppLogo(frontEndData!.downloadApp!.downloadAppLogo.validate());
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
  }

  pickImage() async {
    image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      imagePath = image!.path;
      imageUint8List = await image!.readAsBytes();
      imageName = image!.name;
    }
    setState(() {});
  }

  Widget downloadAppLogoFunction() {
    if (imagePath != null) {
      return Image.file(File(image!.path), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.downloadAppLogo.isNotEmpty) {
        return commonCachedNetworkImage(appStore.downloadAppLogo, fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(defaultAssetImage: DOWNLOAD_APP_IMAGE, appStore.downloadAppLogo, height: 90, width: 90);
      }
    }
  }

  Future<void> saveScreenShotImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "download_app";
    multiPartRequest.fields["key"] = "download_app_logo";
    if (imagePath != null) {
      multiPartRequest.files.add(MultipartFile.fromBytes('download_app_logo', imageUint8List!, filename: imageName));
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
  }

  Future<void> saveDownloadAppSettings() async {
    appStore.setLoading(true);
    List req = [
      {
        "type": "download_app",
        "key": "download_title",
        "value": downloadTitleController.text,
      },
      {
        "type": "download_app",
        "key": "download_description",
        "value": downloadDescriptionController.text,
      },
      {
        "type": "app_content",
        "key": "download_footer_content",
        "value": downloadFooterContentController.text,
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
            appBar: appBarWidget(language.downloadApp),
            body: Stack(
              fit: StackFit.expand,
              children: [
                SingleChildScrollView(
                  //    padding: EdgeInsets.only(top: 16, left: 16, right: 16, bottom: 100),
                  padding: EdgeInsets.all(16),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      10.height,
                      Form(
                        key: formKey,
                        child: Container(
                          //       decoration: boxDecorationWithRoundedCorners(border: Border.all(color: borderColor, width: 1)),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              16.height,
                              RequiredValidation(required: true, titleText: language.title),
                              16.height,
                              AppTextField(
                                isValidationRequired: true,
                                controller: downloadTitleController,
                                textFieldType: TextFieldType.OTHER,
                                decoration: commonInputDecoration(),
                                textInputAction: TextInputAction.next,
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
                              16.height,
                              AppTextField(
                                isValidationRequired: true,
                                controller: downloadDescriptionController,
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
                              Row(
                                children: [
                                  RequiredValidation(required: true, titleText: language.downloadFooterContent),
                                  8.width,
                                  Text(
                                    language.max100character,
                                    style: secondaryTextStyle(color: redColor, size: 12),
                                  ),
                                ],
                              ),
                              16.height,
                              AppTextField(
                                isValidationRequired: true,
                                controller: downloadFooterContentController,
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
                                      pickImage();
                                    },
                                  ),
                                  8.width,
                                  ClipRRect(borderRadius: radius(defaultRadius), child: downloadAppLogoFunction())
                                ],
                              ),
                              8.height,
                              Text(
                                '${"#"}' '${language.note} ${language.downloadAppImageValidationMsg}',
                                style: secondaryTextStyle(color: redColor, size: 12),
                              ),
                              10.height,
                            ],
                          ),
                        ),
                      ),
                      16.height,
                    ],
                  ),
                ),
                appStore.isLoading ? loaderWidget() : SizedBox(),
              ],
            ),
            bottomNavigationBar: Padding(
              padding: EdgeInsets.all(16),
              child: dialogPrimaryButton(language.save, () {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  if (formKey.currentState!.validate()) {
                    if (image != null) {
                      saveDownloadAppSettings();
                      saveScreenShotImage();
                    } else if (appStore.downloadAppLogo.isEmptyOrNull) {
                      toast(language.imgSelectValidation);
                      appStore.setLoading(false);
                    } else {
                      saveDownloadAppSettings();
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
}
