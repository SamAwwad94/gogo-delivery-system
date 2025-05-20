import 'dart:convert';
import 'dart:io';
import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../main.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';
import '../utils/Extensions/shared_pref.dart';

class InvoiceSettingScreen extends StatefulWidget {
  @override
  InvoiceSettingScreenState createState() => InvoiceSettingScreenState();
}

class InvoiceSettingScreenState extends State<InvoiceSettingScreen> {
  GlobalKey<FormState> invoiceFormKey = GlobalKey<FormState>();

  TextEditingController companyNameController = TextEditingController();
  TextEditingController companyContactNoController = TextEditingController();
  TextEditingController companyAddressController = TextEditingController();
  final ImagePicker _picker = ImagePicker();
  Uint8List? logoImage;
  String? logoImageName;
  String? pickedImagePath;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    companyNameController.text = appStore.invoiceCompanyName;
    companyContactNoController.text = appStore.invoiceContactNumber;
    companyAddressController.text = appStore.invoiceAddress;
    setState(() {});
  }

  Future<void> saveInvoiceSetting() async {
    if (invoiceFormKey.currentState!.validate()) {
      invoiceFormKey.currentState!.save();
      List req = [
        {
          "type": "order_invoice",
          "key": "company_name",
          "value": companyNameController.text,
        },
        {
          "type": "order_invoice",
          "key": "company_contact_number",
          "value": companyContactNoController.text,
        },
        {
          "type": "order_invoice",
          "key": "company_address",
          "value": companyAddressController.text,
        }
      ];
      await setInvoiceSetting(jsonEncode(req)).then((value) {
        appStore.setLoading(false);
        appStore.setInvoiceCompanyName(companyNameController.text);
        appStore.setInvoiceContactNumber(companyContactNoController.text);
        appStore.setCompanyAddress(companyAddressController.text);
        Navigator.pop(context);
        toast(value.message);
      }).catchError((error) {
        appStore.setLoading(false);
        log(error);
      });
    }
  }

  Future<void> saveInvoiceImage() async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('setting-upload-invoice-image');
    multiPartRequest.fields["type"] = "order_invoice";
    multiPartRequest.fields["key"] = "company_logo";

    if (logoImage != null) {
      multiPartRequest.files.add(MultipartFile.fromBytes('company_logo', logoImage!, filename: logoImageName));
    } else if (appStore.invoiceImage.isEmptyOrNull) {
      toast("please select Invoice image");
    }

    multiPartRequest.headers.addAll(buildHeaderTokens());
    appStore.setLoading(true);
    sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);

        await getInvoiceSetting().then((value) {
          if (value.invoiceData != null) {
            appStore.setInvoiceCompanyName(value.invoiceData!.firstWhere((element) => element.key == 'company_name').value.validate());
            appStore.setInvoiceContactNumber(value.invoiceData!.firstWhere((element) => element.key == 'company_contact_number').value.validate());
            appStore.setCompanyAddress(value.invoiceData!.firstWhere((element) => element.key == 'company_address').value.validate());
            appStore.setEmailVerification(value.invoiceData!.firstWhere((element) => element.key == 'email_verification').value.validate().toInt());
            appStore.setInvoiceImage(value.invoiceData!.firstWhere((element) => element.key == 'company_logo').value.validate());
          }
        }).catchError((error) {
          log(error.toString());
        });
        setState(() {});
        // getDocumentListApiCall();
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

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Widget invoiceImage() {
    if (pickedImagePath != null) {
      return Image.file(File(pickedImagePath!), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
      //    return commonCachedNetworkImage(pickedImagePath!, height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      // if (appStore.invoiceImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.invoiceImage, fit: BoxFit.cover, height: 100, width: 100);
      // } else {
      //   return Image.asset(AppImg, height: 90, width: 90);
      // }
    }
  }

  pickImage() async {
    XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      pickedImagePath = image.path;
      logoImage = await image.readAsBytes();
      logoImageName = image.name;
    }
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () {
        resetMenuIndex();
        Navigator.pop(context, true);
        return Future.value(true);
      },
      child: Scaffold(
        appBar: appBarWidget(language.invoiceSetting),
        body: SingleChildScrollView(
          padding: EdgeInsets.all(16),
          child: Form(
            key: invoiceFormKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(language.companyName, style: primaryTextStyle()),
                SizedBox(height: 8),
                AppTextField(
                  controller: companyNameController,
                  textFieldType: TextFieldType.NAME,
                  decoration: commonInputDecoration(),
                  textInputAction: TextInputAction.next,
                  validator: (s) {
                    if (s!.trim().isEmpty) return language.field_required_msg;
                    return null;
                  },
                ),
                SizedBox(height: 16),
                Text(language.companyContactNumber, style: primaryTextStyle()),
                SizedBox(height: 8),
                AppTextField(
                  controller: companyContactNoController,
                  textFieldType: TextFieldType.PHONE,
                  decoration: commonInputDecoration(),
                  textInputAction: TextInputAction.next,
                  validator: (s) {
                    if (s!.trim().isEmpty) return language.field_required_msg;
                    return null;
                  },
                ),
                SizedBox(height: 16),
                Text(language.companyAddress, style: primaryTextStyle()),
                SizedBox(height: 8),
                AppTextField(
                  controller: companyAddressController,
                  textFieldType: TextFieldType.ADDRESS,
                  decoration: commonInputDecoration(),
                  textInputAction: TextInputAction.next,
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
                        pickImage();
                      },
                    ).paddingLeft(20),
                    SizedBox(width: 8),
                    invoiceImage(),
                  ],
                ),
              ],
            ),
          ),
        ),
        bottomNavigationBar: Padding(
          padding: EdgeInsets.all(16),
          child: dialogPrimaryButton(language.save, () {
            if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
              toast(language.demoAdminMsg);
            } else {
              saveInvoiceSetting();
              if (logoImage != null) {
                saveInvoiceImage();
              } else if (appStore.invoiceImage.isEmptyOrNull) {
                toast(language.pleaseSelectImage);
              }
            }
          }),
        ),
      ),
    );
  }
}
