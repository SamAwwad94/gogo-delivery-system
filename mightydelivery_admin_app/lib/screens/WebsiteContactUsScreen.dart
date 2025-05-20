import 'dart:convert';
import 'dart:io';

import 'package:country_code_picker/country_code_picker.dart';
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
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/widgets.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../network/NetworkUtils.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';
import '../utils/Images.dart';

class WebsiteContactUsScreen extends StatefulWidget {
  static String route = '/websitecontactus';

  const WebsiteContactUsScreen({super.key});

  @override
  State<WebsiteContactUsScreen> createState() => _WebsiteContactUsScreenState();
}

class _WebsiteContactUsScreenState extends State<WebsiteContactUsScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController supportEmailController = TextEditingController();
  TextEditingController siteEmailController = TextEditingController();

  TextEditingController supportContactNoController = TextEditingController();
  TextEditingController supportAddressController = TextEditingController();
  TextEditingController facebookController = TextEditingController();
  TextEditingController twitterController = TextEditingController();
  TextEditingController linkedinController = TextEditingController();
  TextEditingController instagramController = TextEditingController();

  TextEditingController contactUsTitleController = TextEditingController();
  TextEditingController contactUsSubTitleController = TextEditingController();
  TextEditingController playStoreController = TextEditingController();
  TextEditingController appStoreController = TextEditingController();

  bool settingToast = false;
  String countryCode = defaultPhoneCode;

  String? imageName;
  String? imagePath;
  Uint8List? imageUnit8List;
  int? settingId;
  XFile? image;
  GetFrontendDataResponseModel? frontEndData;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    appStore.setLoading(true);
    frontEndDataListApiCall();

    await getAppSetting().then((value) {
      settingId = value.id!;
      sharedPref.setString(ORDER_PREFIX, value.prefix.validate());
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  ///FrontendApi
  frontEndDataListApiCall() async {
    await getFrontendDataList().then((value) {
      frontEndData = value;
      String phoneNum = frontEndData!.appsetting!.supportNumber.validate();
      countryCode = phoneNum.split(" ").first;
      contactUsTitleController.text = frontEndData!.contactUs!.contactTitle.validate();
      contactUsSubTitleController.text = frontEndData!.contactUs!.contactSubtitle.validate();
      playStoreController.text = frontEndData!.playStoreLink.validate();
      appStoreController.text = frontEndData!.appStoreLink.validate();
      supportEmailController.text = frontEndData!.appsetting!.supportEmail.validate();
      siteEmailController.text = frontEndData!.appsetting!.siteEmail.validate();
      supportContactNoController.text = phoneNum.split(" ").last;
      supportAddressController.text = frontEndData!.appsetting!.siteDescription.validate();
      facebookController.text = frontEndData!.appsetting!.facebookUrl.validate();
      twitterController.text = frontEndData!.appsetting!.twitterUrl.validate();
      instagramController.text = frontEndData!.appsetting!.instagramUrl.validate();
      linkedinController.text = frontEndData!.appsetting!.linkedinUrl.validate();
      appStore.setContactUsAppScreenShotImage(value.contactUs!.contactUsAppSs.toString() != "null" ? value.contactUs!.contactUsAppSs.toString() : "");
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  Future<void> saveContactInfoSettings() async {
    if (formKey.currentState!.validate()) {
      appStore.setLoading(true);
      if (imageUnit8List != null) {
        saveContactUsImage();
        saveContactUsSettings();
        setNotificationApiCall();
      } else if (appStore.contactUsAppScreenShotImage.isEmptyOrNull) {
        toast(language.imgSelectValidation);
        appStore.setLoading(false);
      } else {
        saveContactUsSettings();
        setNotificationApiCall();
      }
    }
  }

  Future<void> saveContactUsSettings() async {
    appStore.setLoading(true);
    List req = [
      {
        "type": "contact_us",
        "key": "contact_title",
        "value": contactUsTitleController.text,
      },
      {
        "type": "contact_us",
        "key": "contact_subtitle",
        "value": contactUsSubTitleController.text,
      },
      {
        "type": "app_content",
        "key": "play_store_link",
        "value": playStoreController.text,
      },
      {
        "type": "app_content",
        "key": "app_store_link",
        "value": appStoreController.text,
      }
    ];
    await setInvoiceSetting(jsonEncode(req)).then((value) {
      appStore.setLoading(false);
      print(value.message.toString());
      if (value.message!.contains('Setting has been save successfully')) {
        settingToast = true;
        setState(() {});
      }
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
    // }
  }

  setNotificationApiCall() async {
    Map req = {
      "id": settingId != null ? settingId : "",
      "site_email": siteEmailController.text.toString(),
      "support_email": supportEmailController.text.toString(),
      "support_number": '$countryCode ${supportContactNoController.text.trim()}',
      "site_description": supportAddressController.text.toString(),
      "facebook_url": facebookController.text.toString(),
      "twitter_url": twitterController.text.toString(),
      "linkedin_url": linkedinController.text.toString(),
      "instagram_url": instagramController.text.toString(),
    };
    await setNotification(req).then((value) {
      appStore.setLoading(false);
      if (settingToast) {
        toast(value.message.toString());
      } else {
        toast(value.message.toString());
      }
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  pickAppContactUsImage() async {
    image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      imagePath = image!.path;
      imageUnit8List = await image!.readAsBytes();
      imageName = image!.name;
    }
    setState(() {});
  }

  Future<void> saveContactUsImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "contact_us";
    multiPartRequest.fields["key"] = "contact_us_app_ss";
    if (imageUnit8List != null) {
      multiPartRequest.files.add(MultipartFile.fromBytes('contact_us_app_ss', imageUnit8List!, filename: imageName));
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

  Widget contactUsSsImageFunction() {
    if (imagePath != null) {
      return Image.file(File(imagePath!), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.contactUsAppScreenShotImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.contactUsAppScreenShotImage.toString(), fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(defaultAssetImage: CONTACT_US_IMAGE, appStore.contactUsAppScreenShotImage.toString(), height: 90, width: 90);
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
            appBar: appBarWidget(language.contactInfo),
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
                          decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius)),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              RequiredValidation(required: true, titleText: language.title),
                              8.height,
                              AppTextField(
                                controller: contactUsTitleController,
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
                                controller: contactUsSubTitleController,
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
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(language.image, style: primaryTextStyle()),
                                  TextButton(
                                    style: TextButton.styleFrom(
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(defaultRadius)),
                                      elevation: 0,
                                      padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                      backgroundColor: Colors.grey.withOpacity(0.2),
                                    ),
                                    child: Text(language.select_file, style: boldTextStyle(color: Colors.grey)),
                                    onPressed: () {
                                      pickAppContactUsImage();
                                    },
                                  ).paddingLeft(20),
                                  8.width,
                                  ClipRRect(borderRadius: radius(defaultRadius), child: contactUsSsImageFunction()),
                                ],
                              ),
                              8.height,
                              Text(
                                '${"#"}' '${language.note} ${language.contactUsImageValidationMsg}',
                                style: secondaryTextStyle(color: redColor, size: 12),
                              ),
                              8.height,
                              RequiredValidation(required: true, titleText: language.supportEmail),
                              8.height,
                              AppTextField(
                                controller: supportEmailController,
                                textFieldType: TextFieldType.EMAIL,
                                decoration: commonInputDecoration(),
                                textInputAction: TextInputAction.next,
                                errorInvalidEmail: language.emailValidation,
                                errorThisFieldRequired: language.field_required_msg,
                              ),
                              16.height,
                              RequiredValidation(required: true, titleText: language.siteEmail),
                              8.height,
                              AppTextField(
                                controller: siteEmailController,
                                textFieldType: TextFieldType.EMAIL,
                                decoration: commonInputDecoration(),
                                textInputAction: TextInputAction.next,
                                errorInvalidEmail: language.emailValidation,
                                errorThisFieldRequired: language.field_required_msg,
                              ),
                              16.height,
                              RequiredValidation(required: true, titleText: language.contactNumber),
                              8.height,
                              AppTextField(
                                controller: supportContactNoController,
                                textFieldType: TextFieldType.PHONE,
                                decoration: commonInputDecoration(
                                  prefixIcon: IntrinsicHeight(
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        CountryCodePicker(
                                          initialSelection: countryCode,
                                          showCountryOnly: false,
                                          showFlag: true,
                                          showFlagDialog: true,
                                          showOnlyCountryWhenClosed: false,
                                          alignLeft: false,
                                          textStyle: primaryTextStyle(),
                                          dialogBackgroundColor: Theme.of(context).cardColor,
                                          barrierColor: Colors.black12,
                                          dialogTextStyle: primaryTextStyle(),
                                          searchDecoration: InputDecoration(
                                            iconColor: Theme.of(context).dividerColor,
                                            enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Theme.of(context).dividerColor)),
                                            focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: primaryColor)),
                                          ),
                                          searchStyle: primaryTextStyle(),
                                          onInit: (c) {
                                            countryCode = c!.dialCode!;
                                          },
                                          onChanged: (c) {
                                            countryCode = c.dialCode!;
                                          },
                                        ),
                                        VerticalDivider(color: Colors.grey.withOpacity(0.5)),
                                      ],
                                    ),
                                  ),
                                ),
                                validator: (s) {
                                  if (s!.trim().isEmpty) return language.field_required_msg;
                                  return null;
                                },
                                inputFormatters: [
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                              ),

                              // AppTextField(
                              //   controller: supportContactNoController,
                              //   textFieldType: TextFieldType.PHONE,
                              //   decoration: commonInputDecoration(),
                              //   textInputAction: TextInputAction.next,
                              //   validator: (value) {
                              //     if (value!.trim().isEmpty) return language.field_required_msg;
                              //     return null;
                              //   },
                              // ),
                              16.height,
                              RequiredValidation(required: true, titleText: language.address),
                              8.height,
                              AppTextField(
                                controller: supportAddressController,
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
                              socialUrlsWidget(),
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
              child: dialogPrimaryButton(language.save, () {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  saveContactInfoSettings();
                }
              }),
            ),
          ),
        );
      },
    );
  }

  Widget socialUrlsWidget() {
    return Column(
      children: [
        Container(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              16.width,
              Text(language.facebookUrl, style: primaryTextStyle()),
              8.height,
              AppTextField(
                controller: facebookController,
                textFieldType: TextFieldType.URL,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                isValidationRequired: false,
              ),
              16.height,
              Text(language.twitterUrl, style: primaryTextStyle()),
              8.height,
              AppTextField(
                controller: twitterController,
                textFieldType: TextFieldType.URL,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                isValidationRequired: false,
              ),
              16.height,
              Text(language.linkedInUrl, style: primaryTextStyle()),
              8.height,
              AppTextField(
                controller: linkedinController,
                textFieldType: TextFieldType.URL,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                isValidationRequired: false,
              ),
              16.height,
              Text(language.instagramUrl, style: primaryTextStyle()),
              8.height,
              AppTextField(
                controller: instagramController,
                textFieldType: TextFieldType.URL,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                isValidationRequired: false,
              ),
              16.height,
              8.height,
              Text(language.playStoreUrl, style: primaryTextStyle()),
              AppTextField(
                controller: playStoreController,
                textFieldType: TextFieldType.URL,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                isValidationRequired: false,
              ),
              16.height,
              Text(language.appStoreUrl, style: primaryTextStyle()),
              8.height,
              AppTextField(
                controller: appStoreController,
                textFieldType: TextFieldType.URL,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                isValidationRequired: false,
              ),
              16.height,
            ],
          ),
        ),
        16.height,
      ],
    );
  }

  Widget webSiteClientReviewWidget() {
    return Column(
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [],
        ),
        16.height,
      ],
    );
  }
}
