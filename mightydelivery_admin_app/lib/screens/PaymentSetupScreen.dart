import 'dart:convert';
import 'dart:io';
import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/bool_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';

import '../main.dart';
import '../models/LDBaseResponse.dart';
import '../models/PaymentGatewayListModel.dart';
import '../models/models.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/DataProvider.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';
import '../utils/Extensions/shared_pref.dart';

class PaymentSetupScreen extends StatefulWidget {
  static String tag = '/AppPaymentSetupScreen';
  final Function()? onUpdate;
  final String? paymentType;
  final List<PaymentGatewayData> paymentGatewayList;

  PaymentSetupScreen({required this.paymentGatewayList, this.paymentType, this.onUpdate});

  @override
  PaymentSetupScreenState createState() => PaymentSetupScreenState();
}

class PaymentSetupScreenState extends State<PaymentSetupScreen> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  TextEditingController paymentMethodController = TextEditingController();
  TextEditingController secretKeyController = TextEditingController();
  TextEditingController publishableKeyController = TextEditingController();
  TextEditingController keyIdController = TextEditingController();
  TextEditingController secretIdController = TextEditingController();
  TextEditingController publicKeyController = TextEditingController();
  TextEditingController encryptionKeyController = TextEditingController();

  TextEditingController tokenizationKeyController = TextEditingController();
  TextEditingController accessTokenController = TextEditingController();
  TextEditingController profileIdController = TextEditingController();
  TextEditingController serverKeyController = TextEditingController();
  TextEditingController clientKeyController = TextEditingController();
  TextEditingController mIDController = TextEditingController();
  TextEditingController merchantKeyController = TextEditingController();

  List<StaticPaymentModel> staticPaymentList = getStaticPaymentItems();

  String selectedPaymentType = PAYMENT_GATEWAY_STRIPE;
  int? isTest;
  bool isUpdate = false;
  PaymentGatewayData? paymentGatewayData;
  String? logoImagePath;

  final ImagePicker _picker = ImagePicker();
  Uint8List? logoImage;
  String? logoImageName;
  bool isPaymentEnable = false;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    LiveStream().on(streamLanguage, (p0) {
      LiveStream().on(streamLanguage, (p0) {
        staticPaymentList.clear();
        staticPaymentList = getStaticPaymentItems();
        setState(() {});
      });
    });
    if (widget.paymentType != null) {
      selectedPaymentType = widget.paymentType!;
    }
    checkIsUpdate();
  }

  checkIsUpdate() async {
    var data = (getStringListAsync(PAYMENT_GATEWAY_LIST) ?? []).where((element) {
      PaymentGatewayData mData = PaymentGatewayData.fromJson(jsonDecode(element));

      if (mData.type!.contains(selectedPaymentType)) {
        paymentGatewayData = mData;
        setState(() {});
      }
      return mData.type!.contains(selectedPaymentType);
    });
    if (data.length >= 1) {
      isUpdate = true;
    } else {
      isUpdate = false;
    }
    await setData();
  }

  setData() {
    if (isUpdate && paymentGatewayData != null) {
      paymentMethodController.text = paymentGatewayData!.title ?? "";
      secretKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.secretKey ?? "" : paymentGatewayData!.liveValue!.secretKey ?? "";
      publishableKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.publishableKey ?? "" : paymentGatewayData!.liveValue!.publishableKey ?? "";
      keyIdController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.keyId ?? "" : paymentGatewayData!.liveValue!.keyId ?? "";
      secretIdController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.secretId ?? "" : paymentGatewayData!.liveValue!.secretId ?? "";
      publicKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.publicKey ?? "" : paymentGatewayData!.liveValue!.publicKey ?? "";
      encryptionKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.encryptionKey ?? "" : paymentGatewayData!.liveValue!.encryptionKey ?? "";

      tokenizationKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.tokenizationKey ?? "" : paymentGatewayData!.liveValue!.tokenizationKey ?? "";
      accessTokenController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.accessToken ?? "" : paymentGatewayData!.liveValue!.accessToken ?? "";
      profileIdController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.profileId ?? "" : paymentGatewayData!.liveValue!.profileId ?? "";
      serverKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.serverKey ?? "" : paymentGatewayData!.liveValue!.serverKey ?? "";
      clientKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.clientKey ?? "" : paymentGatewayData!.liveValue!.clientKey ?? "";
      mIDController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.merchantId ?? "" : paymentGatewayData!.liveValue!.merchantId ?? "";
      merchantKeyController.text = paymentGatewayData!.isTest == 1 ? paymentGatewayData!.testValue!.merchantKey ?? "" : paymentGatewayData!.liveValue!.merchantKey ?? "";

      isTest = paymentGatewayData!.isTest!;
      logoImagePath = paymentGatewayData!.gatewayLogo.validate();
      isPaymentEnable = paymentGatewayData!.status == 1 ? true : false;
    } else {
      paymentMethodController.clear();
      secretKeyController.clear();
      publishableKeyController.clear();
      keyIdController.clear();
      secretIdController.clear();
      publicKeyController.clear();
      encryptionKeyController.clear();
      tokenizationKeyController.clear();
      accessTokenController.clear();
      profileIdController.clear();
      serverKeyController.clear();
      clientKeyController.clear();
      mIDController.clear();
      merchantKeyController.clear();
      isTest = null;
      logoImagePath = null;
      isPaymentEnable = false;
    }
  }

  pickImage() async {
    XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      logoImagePath = image.path;
      logoImage = await image.readAsBytes();
      logoImageName = image.name;
    }
    setState(() {});
  }

  String getPaymentTitle() {
    String title = '';
    staticPaymentList.forEach((element) {
      if (element.type == selectedPaymentType) {
        title = element.title!;
      }
    });
    return title;
  }

  /// Save Payment
  Future<void> savePaymentApiCall() async {
    appStore.setLoading(true);
    Map? valRequest;
    if (selectedPaymentType == PAYMENT_GATEWAY_STRIPE) {
      valRequest = {"secret_key": secretKeyController.text, "publishable_key": publishableKeyController.text};
    } else if (selectedPaymentType == PAYMENT_GATEWAY_RAZORPAY) {
      valRequest = {"key_id": keyIdController.text, "secret_id": secretIdController.text};
    } else if (selectedPaymentType == PAYMENT_GATEWAY_PAYSTACK) {
      valRequest = {"public_key": publicKeyController.text};
    } else if (selectedPaymentType == PAYMENT_GATEWAY_FLUTTERWAVE) {
      valRequest = {"public_key": publicKeyController.text, "secret_key": secretKeyController.text, "encryption_key": encryptionKeyController.text};
    } else if (selectedPaymentType == PAYMENT_GATEWAY_PAYPAL) {
      valRequest = {"tokenization_key": tokenizationKeyController.text};
    } else if (selectedPaymentType == PAYMENT_GATEWAY_PAYTABS) {
      valRequest = {"profile_id": profileIdController.text, "server_key": serverKeyController.text, "client_key": clientKeyController.text};
    }
    /*else if (selectedPaymentType == PAYMENT_TYPE_MERCADOPAGO) {
      valRequest = {"public_key": publicKeyController.text, "access_token": accessTokenController.text};
    }*/
    else if (selectedPaymentType == PAYMENT_GATEWAY_PAYTM) {
      valRequest = {"merchant_id": mIDController.text, "merchant_key": merchantKeyController.text};
    } else if (selectedPaymentType == PAYMENT_GATEWAY_MYFATOORAH) {
      valRequest = {"access_token": accessTokenController.text};
    }

    MultipartRequest multiPartRequest = await getMultiPartRequest('paymentgateway-save');

    multiPartRequest.fields['id'] = isUpdate ? paymentGatewayData!.id.toString() : "";
    multiPartRequest.fields['title'] = paymentMethodController.text;
    multiPartRequest.fields['type'] = selectedPaymentType;
    multiPartRequest.fields['is_test'] = isTest.toString();
    if (isTest == 1) {
      multiPartRequest.fields['test_value'] = jsonEncode(valRequest);
    } else if (isTest == 0) {
      multiPartRequest.fields['live_value'] = jsonEncode(valRequest);
    }
    if (logoImage != null) {
      multiPartRequest.files.add(MultipartFile.fromBytes('gateway_logo', logoImage!, filename: logoImageName));
    }

    log("Payment Save ${multiPartRequest.fields}");

    multiPartRequest.headers.addAll(buildHeaderTokens());

    await sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        if (data != null) {
          LDBaseResponse res = LDBaseResponse.fromJson(data);

          await getPaymentGatewayList().then((value) {
            setValue(PAYMENT_GATEWAY_LIST, value.data!.map((e) => jsonEncode(e.toJson())).toList());
            setState(() {});
          }).catchError((error) {
            appStore.setLoading(false);
            toast(error.toString());
          });
          checkIsUpdate();
          appStore.setLoading(false);
          toast(res.message.toString());
        }
      },
      onError: (error) {
        appStore.setLoading(false);
        toast(error.toString());
      },
    ).catchError((e) {
      appStore.setLoading(false);
      toast(e.toString());
    });
  }

  /// Update Payment Status
  Future<void> updateStatusApiCall(PaymentGatewayData paymentGatewayData, int pstatus) async {
    appStore.setLoading(true);
    MultipartRequest multiPartRequest = await getMultiPartRequest('paymentgateway-save');

    multiPartRequest.fields['id'] = paymentGatewayData.id!.toString();
    multiPartRequest.fields['status'] = pstatus.toString();
    multiPartRequest.headers.addAll(buildHeaderTokens());

    await sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
        if (data != null) {
          LDBaseResponse res = LDBaseResponse.fromJson(data);
          toast(res.message.toString());

          await getPaymentGatewayList().then((value) {
            appStore.setLoading(false);
            setValue(PAYMENT_GATEWAY_LIST, value.data!.map((e) => jsonEncode(e.toJson())).toList());
            setState(() {});
          }).catchError((error) {
            appStore.setLoading(false);
            toast(error.toString());
          });
          checkIsUpdate();
          setState(() {});
        }
      },
      onError: (error) {
        appStore.setLoading(false);
        toast(error.toString());
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

  Widget stripeForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.secretKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: sharedPref.getString(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : secretKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
        Row(
          children: [
            Text(language.publicKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: sharedPref.getString(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : publishableKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget razorPayForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.keyId, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : keyIdController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget payStackForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.publicKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : publicKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget flutterWaveForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.publicKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : publicKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
        Row(
          children: [
            Text(language.secretKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : secretKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
        Row(
          children: [
            Text(language.encryptionKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : encryptionKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget payPalForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.tokenizationKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : tokenizationKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget mercadoPagoForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.publicKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : publicKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
        Row(
          children: [
            Text(language.accessToken, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : accessTokenController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget payTabsForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.profileId, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : profileIdController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
        Row(
          children: [
            Text(language.serverKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : serverKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
        Row(
          children: [
            Text(language.clientKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : clientKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget paytmForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.mId, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : mIDController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
        Row(
          children: [
            Text(language.merchantKey, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : merchantKeyController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
      ],
    );
  }

  Widget myFatoorahForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(language.token, style: primaryTextStyle()),
            Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
          ],
        ),
        SizedBox(height: 8),
        AppTextField(
          controller: getStringAsync(USER_TYPE) == DEMO_ADMIN ? TextEditingController() : accessTokenController,
          textFieldType: TextFieldType.OTHER,
          textInputAction: TextInputAction.next,
          decoration: commonInputDecoration(),
        ),
        SizedBox(height: 16),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: appBarWidget(language.paymentGatewaySetup),
        body: Stack(
          fit: StackFit.expand,
          children: [
            SingleChildScrollView(
              padding: EdgeInsets.all(16),
              controller: ScrollController(),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Text(language.paymentType, style: boldTextStyle()),
                        SizedBox(width: 16),
                        Expanded(
                          child: DropdownButtonFormField(
                              decoration: commonInputDecoration(),
                              dropdownColor: Theme.of(context).cardColor,
                              value: selectedPaymentType,
                              items: staticPaymentList.map<DropdownMenuItem<String>>((mData) {
                                return DropdownMenuItem(value: mData.type, child: Text(mData.title.validate(), style: primaryTextStyle()));
                              }).toList(),
                              onChanged: (String? value) {
                                selectedPaymentType = value!;
                                checkIsUpdate();
                                setState(() {});
                              }),
                        )
                      ],
                    ),
                    SizedBox(height: 20),
                    Container(
                      child: Container(
                        padding: EdgeInsets.all(20),
                        decoration: containerDecoration(),
                        width: 500,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Row(
                                  children: [
                                    Text(language.enable + " ", style: boldTextStyle(size: 18)),
                                    Text('${getPaymentTitle()} ${language.payment}', style: boldTextStyle(size: 18),overflow: TextOverflow.ellipsis,maxLines: 1,).expand(),
                                  ],
                                ).expand(),
                                Transform.scale(
                                  scale: 0.8,
                                  child: Switch(
                                    value: isPaymentEnable,
                                    onChanged: (value) {
                                      isPaymentEnable = value;
                                      setState(() {});
                                    },
                                  ),
                                )
                              ],
                            ),
                            if (isPaymentEnable.validate())
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Text(language.paymentMethod, style: primaryTextStyle()),
                                      Text(" *", style: primaryTextStyle(color: Color(0xffF42B3D), size: 22)),
                                    ],
                                  ),
                                  SizedBox(height: 8),
                                  AppTextField(controller: paymentMethodController, textFieldType: TextFieldType.NAME, textInputAction: TextInputAction.next, decoration: commonInputDecoration()),
                                  SizedBox(height: 16),
                                  Text(getPaymentTitle() + " " + language.mode, style: primaryTextStyle()),
                                  SizedBox(height: 8),
                                  Row(
                                    children: [
                                      Expanded(
                                        child: RadioListTile(
                                          activeColor: appStore.isDarkMode ? white : primaryColor,
                                          value: 1,
                                          toggleable: true,
                                          title: Text(language.test, style: primaryTextStyle()),
                                          groupValue: isTest,
                                          onChanged: (int? val) {
                                            isTest = val!;
                                            setState(() {});
                                          },
                                        ),
                                      ),
                                      Expanded(
                                        child: RadioListTile(
                                          activeColor: appStore.isDarkMode ? white : primaryColor,
                                          value: 0,
                                          title: Text(language.live, style: primaryTextStyle()),
                                          groupValue: isTest,
                                          onChanged: (int? val) {
                                            isTest = val!;
                                            setState(() {});
                                          },
                                        ),
                                      ),
                                    ],
                                  ),

                                  SizedBox(height: 16),
                                  if (selectedPaymentType == PAYMENT_GATEWAY_STRIPE) stripeForm(),
                                  if (selectedPaymentType == PAYMENT_GATEWAY_RAZORPAY) razorPayForm(),
                                  if (selectedPaymentType == PAYMENT_GATEWAY_PAYSTACK) payStackForm(),
                                  if (selectedPaymentType == PAYMENT_GATEWAY_FLUTTERWAVE) flutterWaveForm(),
                                  if (selectedPaymentType == PAYMENT_GATEWAY_PAYPAL) payPalForm(),
                                  if (selectedPaymentType == PAYMENT_GATEWAY_PAYTABS) payTabsForm(),
                                  // if (selectedPaymentType == PAYMENT_TYPE_MERCADOPAGO)  mercadoPagoForm() ,
                                  if (selectedPaymentType == PAYMENT_GATEWAY_PAYTM) paytmForm(),
                                  if (selectedPaymentType == PAYMENT_GATEWAY_MYFATOORAH) myFatoorahForm(),
                                  SizedBox(height: 16),
                                  Row(
                                    children: [
                                      Text(language.image, style: primaryTextStyle()),
                                      SizedBox(width: 16),
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
                                    ],
                                  ),
                                  SizedBox(height: 16),
                                  logoImageData(),
                                  SizedBox(height: 20),
                                ],
                              ),
                            SizedBox(height: 20),
                            Center(
                              child: appCommonButton(isUpdate ? language.update : language.save, () {
                                if (_formKey.currentState!.validate()) {
                                  if (isTest == null) return toast(language.pleaseSelectPaymentGatewayMode);
                                  if (isPaymentEnable) savePaymentApiCall();
                                  updateStatusApiCall(paymentGatewayData!, isPaymentEnable == true ? 1 : 0);
                                }
                              }, width: 150),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            Observer(builder: (context) => appStore.isLoading ? Center(child: loaderWidget()) : SizedBox()),
          ],
        ));
  }

  Widget logoImageData() {
    if (logoImage != null) {
      return Image.file(File(logoImagePath!), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center).center();
      //    return commonCachedNetworkImage(pickedImagePath!, height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      return commonCachedNetworkImage(logoImagePath.validate(), fit: BoxFit.cover, height: 100, width: 100).center();
    }
  }
}
