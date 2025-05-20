import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../utils/Extensions/shared_pref.dart';

import '../extensions/widgets.dart';
import '../main.dart';
import '../models/LDBaseResponse.dart';
import '../models/PaymentGatewayListModel.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import 'PaymentSetupScreen.dart';

class PaymentGatewayScreen extends StatefulWidget {
  @override
  PaymentGatewayScreenState createState() => PaymentGatewayScreenState();
}

class PaymentGatewayScreenState extends State<PaymentGatewayScreen> {
  int currentIndex = 0;
  int currentPage = 1;

  List<PaymentGatewayData> paymentGatewayList = [];

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    afterBuildCreated(() {
      appStore.setLoading(true);
      getPaymentGatewayListApiCall();
    });
  }

  getPaymentGatewayListApiCall() async {
    appStore.setLoading(true);
    await getPaymentGatewayList().then((value) {
      appStore.setLoading(false);
      paymentGatewayList.clear();
      value.data!.forEach((element) {
        if (element.type != 'mercadopago') {
          paymentGatewayList.add(element);
        }
      });

      setValue(PAYMENT_GATEWAY_LIST, paymentGatewayList.map((e) => jsonEncode(e.toJson())).toList());
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  /// Update Payment Status
  Future<void> updateStatusApiCall(PaymentGatewayData paymentGatewayData) async {
    appStore.setLoading(true);
    MultipartRequest multiPartRequest = await getMultiPartRequest('paymentgateway-save');

    multiPartRequest.fields['id'] = paymentGatewayData.id!.toString();
    multiPartRequest.fields['status'] = paymentGatewayData.status == 1 ? "0" : "1";
    multiPartRequest.headers.addAll(buildHeaderTokens());

    await sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
        if (data != null) {
          LDBaseResponse res = LDBaseResponse.fromJson(data);
          toast(res.message.toString());
          getPaymentGatewayListApiCall();
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

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () {
        resetMenuIndex();
        Navigator.pop(context, true);
        return Future.value(true);
      },
      child: Scaffold(
        appBar: appBarWidget(language.paymentGateway, actions: [
          GestureDetector(
            child: Container(
              alignment: Alignment.center,
              margin: EdgeInsets.all(12),
              padding: EdgeInsets.symmetric(horizontal: 16),
              decoration: BoxDecoration(color: appStore.isDarkMode ? primaryColor : Colors.white, borderRadius: BorderRadius.circular(defaultRadius)),
              child: Text(language.setup, style: boldTextStyle(color: appStore.isDarkMode ? Colors.white : primaryColor)),
            ),
            onTap: () {
              launchScreen(
                  context,
                  PaymentSetupScreen(
                    paymentGatewayList: paymentGatewayList,
                    onUpdate: () {
                      getPaymentGatewayListApiCall();
                    },
                  ));
            },
          ),
        ]),
        body: Observer(builder: (context) {
          return Stack(
            fit: StackFit.expand,
            children: [
              ListView.builder(
                padding: EdgeInsets.only(left: 16, top: 16, right: 16),
                itemCount: paymentGatewayList.length,
                itemBuilder: (context, index) {
                  PaymentGatewayData mData = paymentGatewayList[index];
                  return Container(
                    margin: EdgeInsets.only(bottom: 16),
                    decoration: containerDecoration(),
                    padding: EdgeInsets.all(12),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            border: Border.all(width: 1, color: Colors.grey.withOpacity(0.3)),
                            color: appStore.isDarkMode ? scaffoldColorDark : Colors.white,
                            borderRadius: BorderRadius.circular(16),
                          ),
                          padding: EdgeInsets.all(8),
                          child: commonCachedNetworkImage('${mData.gatewayLogo.validate()}', fit: BoxFit.fitHeight, height: 50, width: 50),
                        ),
                        SizedBox(width: 8),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                crossAxisAlignment: CrossAxisAlignment.end,
                                children: [
                                  Expanded(child: Text('${mData.title ?? ""}', style: boldTextStyle())),
                                  GestureDetector(
                                    child: Container(
                                      alignment: Alignment.center,
                                      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                      margin: EdgeInsets.only(right: 8, left: appStore.selectedLanguage == "ar" ? 8 : 0),
                                      child: Text(
                                        '${mData.status == 1 ? language.enable : language.disable}',
                                        style: primaryTextStyle(
                                            color: mData.status == 1
                                                ? appStore.isDarkMode
                                                    ? white
                                                    : primaryColor
                                                : Colors.red,
                                            size: 14),
                                      ),
                                      decoration: BoxDecoration(
                                          border: Border.all(
                                              color: mData.status == 1
                                                  ? appStore.isDarkMode
                                                      ? white
                                                      : primaryColor.withOpacity(0.6)
                                                  : Colors.red.withOpacity(0.6)),
                                          color: mData.status == 1 ? primaryColor.withOpacity(0.15) : Colors.red.withOpacity(0.15),
                                          borderRadius: BorderRadius.circular(defaultRadius)),
                                    ),
                                    onTap: () {
                                      commonConfirmationDialog(context, mData.status == 1 ? DIALOG_TYPE_DISABLE : DIALOG_TYPE_ENABLE, () {
                                        if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                          toast(language.demoAdminMsg);
                                        } else {
                                          Navigator.pop(context);
                                          updateStatusApiCall(mData);
                                        }
                                      },
                                          title: mData.status != 1 ? language.enablePayment : language.disablePayment,
                                          subtitle: mData.status != 1 ? language.enablePaymentMsg : language.disablePaymentMsg);
                                    },
                                  ),
                                  outlineActionIcon(context, Icons.edit, Colors.green, () async {
                                    await launchScreen(
                                        context,
                                        PaymentSetupScreen(
                                          paymentGatewayList: paymentGatewayList,
                                          paymentType: mData.type,
                                          onUpdate: () {
                                            getPaymentGatewayListApiCall();
                                          },
                                        ));
                                  }),
                                ],
                              ),
                              SizedBox(height: 10),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text('${mData.isTest == 1 ? language.test : language.live} ${language.mode}', style: primaryTextStyle(size: 14)),
                                  Text('${language.id}: #${mData.id ?? ""}', style: secondaryTextStyle()),
                                ],
                              )
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
              appStore.isLoading
                  ? loaderWidget()
                  : paymentGatewayList.isEmpty
                      ? emptyWidget()
                      : SizedBox(),
            ],
          );
        }),
      ),
    );
  }
}
