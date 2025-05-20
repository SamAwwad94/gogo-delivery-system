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
import '../../utils/Colors.dart';
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

class WebsiteAboutUsScreen extends StatefulWidget {
  static String route = '/websiteaboutus';

  const WebsiteAboutUsScreen({super.key});

  @override
  State<WebsiteAboutUsScreen> createState() => _WebsiteAboutUsScreenState();
}

class _WebsiteAboutUsScreenState extends State<WebsiteAboutUsScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController aboutUsTitleController = TextEditingController();
  TextEditingController aboutUsSubTitleController = TextEditingController();
  TextEditingController aboutUsSortDescriptionController = TextEditingController();
  TextEditingController aboutUsLongDescriptionController = TextEditingController();

  Uint8List? imageUnit8List;
  String? imageName;
  String? imagePath;
  final ImagePicker _picker = ImagePicker();
  XFile? image;
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
    await getFrontendDataList().then((value) {
      frontEndData = value;
      print(frontEndData!.toJson().toString());
      if (frontEndData!.aboutUs!.downloadTitle != null && frontEndData!.aboutUs!.downloadTitle!.isNotEmpty) {
        aboutUsTitleController.text = frontEndData!.aboutUs!.downloadTitle.validate();
      }

      aboutUsSubTitleController.text = frontEndData!.aboutUs!.downloadSubtitle.validate();
      aboutUsSortDescriptionController.text = frontEndData!.aboutUs!.sortDes.validate();
      aboutUsLongDescriptionController.text = frontEndData!.aboutUs!.longDes.validate();
      appStore.setAboutUsScreenShotImage(frontEndData!.aboutUs!.aboutUsAppSs.validate());
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  Future<void> saveAboutUsSettings() async {
    if (formKey.currentState!.validate()) {
      appStore.setLoading(true);
      List req = [
        {
          "type": "about_us",
          "key": "download_title",
          "value": aboutUsTitleController.text,
        },
        {
          "type": "about_us",
          "key": "download_subtitle",
          "value": aboutUsSubTitleController.text,
        },
        {
          "type": "about_us",
          "key": "sort_des",
          "value": aboutUsSortDescriptionController.text,
        },
        {
          "type": "about_us",
          "key": "long_des",
          "value": aboutUsLongDescriptionController.text,
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

  pickAboutUsScreenShotImage() async {
    image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      imagePath = image!.path;
      imageUnit8List = await image!.readAsBytes();
      imageName = image!.name;
    }
    setState(() {});
  }

  Future<void> saveAboutUsImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "about_us";
    multiPartRequest.fields["key"] = "about_us_app_ss";
    if (imageUnit8List != null) {
      multiPartRequest.files.add(MultipartFile.fromBytes('about_us_app_ss', imageUnit8List!, filename: imageName));
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

  Widget aboutUsScreenShotImageFunction() {
    if (imagePath != null) {
      return Image.file(File(imagePath!), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.aboutUsAppScreenShotImage != null && appStore.aboutUsAppScreenShotImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.aboutUsAppScreenShotImage.toString(), fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(defaultAssetImage: ABOUT_US_IMAGE, appStore.aboutUsAppScreenShotImage.toString(), height: 90, width: 90);
      }
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
            appBar: appBarWidget(language.aboutUs),
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
                    if (imageUnit8List != null) {
                      await saveAboutUsImage();
                      saveAboutUsSettings();
                    } else if (appStore.aboutUsAppScreenShotImage.isEmptyOrNull) {
                      toast(language.imgSelectValidation);
                      appStore.setLoading(false);
                    } else {
                      await saveAboutUsImage();
                      saveAboutUsSettings();
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

  Widget webSiteAboutUsWidget() {
    return Column(
      children: [
        Container(
          //   padding: EdgeInsets.all(16),
          decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius)),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              RequiredValidation(required: true, titleText: language.title),
              8.height,
              AppTextField(
                controller: aboutUsTitleController,
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
                  RequiredValidation(required: true, titleText: language.subTitle),
                  8.width,
                  Text(
                    language.max100character,
                    style: secondaryTextStyle(color: redColor, size: 12),
                  ),
                ],
              ),
              8.height,
              AppTextField(
                controller: aboutUsSubTitleController,
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
              RequiredValidation(required: true, titleText: language.sortDescription),
              8.height,
              AppTextField(
                controller: aboutUsSortDescriptionController,
                textFieldType: TextFieldType.OTHER,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                maxLines: 3,
                minLines: 3,
                validator: (s) {
                  if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
              RequiredValidation(required: true, titleText: language.longDescription),
              16.height,
              AppTextField(
                controller: aboutUsLongDescriptionController,
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
                      pickAboutUsScreenShotImage();
                    },
                  ),
                  8.width,
                  ClipRRect(borderRadius: radius(defaultRadius), child: aboutUsScreenShotImageFunction()),
                ],
              ),
              8.height,
              Text(
                '${"#"}' '${language.note} ${language.contactUsImageValidationMsg}',
                style: secondaryTextStyle(color: redColor, size: 12),
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
