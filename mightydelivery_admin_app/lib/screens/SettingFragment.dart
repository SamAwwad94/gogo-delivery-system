import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/list_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../extensions/widgets.dart';
import '../main.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/DataProvider.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/app_common.dart';
import 'EditProfileScreen.dart';
import 'NotificationScreen.dart';

class SettingFragment extends StatefulWidget {
  @override
  SettingFragmentState createState() => SettingFragmentState();
}

class SettingFragmentState extends State<SettingFragment> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  ScrollController notificationController = ScrollController();

  Map<String, dynamic> notificationSettings = {};
  int? settingId;
  bool isAutoAssign = false;

  TextEditingController distanceController = TextEditingController();
  String? distanceUnitType;
  // bool isExpanded = false;
  int selectedMenuIndex = -1;

  @override
  void initState() {
    super.initState();
    afterBuildCreated(init);
  }

  void init() async {
    LiveStream().on('UpdateTheme', (p0) {
      setState(() {});
    });
    LiveStream().on('UpdateLanguage', (p0) {
      setState(() {});
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Widget settingWidget(String? icon, String? title, Function? onTap) {
    return Padding(
      padding: EdgeInsets.all(16.0),
      child: GestureDetector(
        onTap: () {
          onTap!();
        },
        child: Row(
          children: [
            ImageIcon(
              AssetImage(icon!),
              size: 18,
              //    color: appStore.selectedMenuIndex == e.index || isHovering ? primaryColor : textPrimaryColorGlobal,
            ),
            8.width,
            Expanded(child: Text(title!, style: boldTextStyle())),
            Icon(Icons.arrow_forward_ios_rounded, size: 16),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Observer(builder: (context) {
      return Scaffold(
        appBar: appBarWidget(language.setting, showBack: false, actions: [
          Observer(
            builder: (_) => SizedBox(
              width: 55,
              child: Stack(
                children: [
                  Align(
                    alignment: AlignmentDirectional.center,
                    child: Icon(Icons.notifications, color: Colors.white),
                  ),
                  if (appStore.allUnreadCount != 0)
                    Positioned(
                      right: 10,
                      top: 8,
                      child: Container(
                        height: 20,
                        width: 20,
                        alignment: Alignment.center,
                        decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle),
                        child: Observer(builder: (_) {
                          return Text('${appStore.allUnreadCount < 99 ? appStore.allUnreadCount : '99+'}', style: primaryTextStyle(size: appStore.allUnreadCount > 99 ? 9 : 12, color: Colors.white));
                        }),
                      ),
                    ),
                ],
              ).onTap(() {
                Navigator.push(context, MaterialPageRoute(builder: (_) => NotificationScreen()));
              }),
            ),
          ),
        ]),
        body: Stack(
          children: [
            SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Padding(
                    padding: EdgeInsets.all(16),
                    child: Row(
                      children: [
                        Container(
                          height: 60,
                          width: 60,
                          decoration: BoxDecoration(
                            border: Border.all(color: Colors.grey.withOpacity(0.15)),
                            shape: BoxShape.circle,
                            image: DecorationImage(image: NetworkImage('${appStore.userProfile}'), fit: BoxFit.cover),
                          ),
                        ),
                        SizedBox(width: 8),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text('${sharedPref.getString(NAME) ?? ""}', style: boldTextStyle()),
                                  outlineActionIcon(context, Icons.edit, appStore.isDarkMode ? white : primaryColor, () async {
                                    bool? res = await launchScreen(context, EditProfileScreen());
                                    if (res ?? false) {
                                      setState(() {});
                                    }
                                  }),
                                ],
                              ),
                              SizedBox(height: 4),
                              Text(sharedPref.getString(USER_EMAIL) ?? "", style: secondaryTextStyle(size: 14)),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  // settingWidget(ic_country, language.country, () {
                  //   launchScreen(context, CountryScreen());
                  // }),
                  // settingWidget(ic_city, language.city, () {
                  //   launchScreen(context, CityScreen());
                  // }),
                  // settingWidget(ic_vehicle, language.vehicle, () {
                  //   launchScreen(context, VehicleScreen());
                  // }),
                  // settingWidget(ic_extra_charges, language.extraCharges, () {
                  //   launchScreen(context, ExtraChargesScreen());
                  // }),
                  // settingWidget(ic_vehicle, language.parcelType, () {
                  //   launchScreen(context, ParcelTypeScreen());
                  // }),
                  // settingWidget(ic_payment, language.paymentGateway, () {
                  //   launchScreen(context, PaymentGatewayScreen());
                  // }),
                  // settingWidget(ic_document, language.document, () {
                  //   launchScreen(context, DocumentScreen());
                  // }),
                  // settingWidget(ic_delivery_document, language.deliveryPersonDocuments, () {
                  //   launchScreen(context, DeliveryPersonDocumentScreen());
                  // }),
                  // settingWidget(ic_withdrawal, language.withdrawRequest, () {
                  //   launchScreen(context, WithdrawalRequestScreen());
                  // }),
                  // settingWidget(ic_order_location, language.ordersLocation, () {
                  //   launchScreen(context, OrdersLocationScreen());
                  // }),
                  // settingWidget(ic_delivery_person_location, language.deliveryManLocation, () {
                  //   launchScreen(context, DeliveryLiveLocationScreen());
                  // }),
                  // Divider(thickness: 8, color: Colors.grey.withOpacity(0.15)),
                  // settingWidget(ic_settings, language.appSetting, () {
                  //   launchScreen(context, AppSettingsScreen());
                  // }),
                  // settingWidget(ic_invoice, language.invoiceSetting, () {
                  //   launchScreen(context, InvoiceSettingScreen());
                  // }),
                  // settingWidget(ic_password, language.changePassword, () {
                  //   launchScreen(context, ChangePasswordScreen());
                  // }),
                  // settingWidget(ic_language, language.language, () {
                  //   launchScreen(context, LanguageScreen());
                  // }),
                  // settingWidget(ic_theme, language.theme, () {
                  //   launchScreen(context, ThemeScreen());
                  // }),
                  // Divider(thickness: 9, color: Colors.grey.withOpacity(0.15)),
                  // Theme(
                  //   data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
                  //   child: ExpansionTile(
                  //       initiallyExpanded: false,
                  //       //    collapsedIconColor: appStore.isDarkMode ? white : Colors.black,
                  //       tilePadding: EdgeInsets.only(left: 16, right: 20, top: 0, bottom: 0),
                  //       //     iconColor: appStore.isDarkMode ? white : Colors.black,
                  //       trailing: Transform.rotate(angle: isExpanded ? 90 * pi / 180 : 0, child: Icon(Icons.arrow_forward_ios_rounded, size: 16, color: appStore.isDarkMode ? white : Colors.black)),
                  //       onExpansionChanged: (v) {
                  //         isExpanded = v;
                  //         setState(() {});
                  //       },
                  //       title: Row(
                  //         children: [
                  //           ImageIcon(
                  //               AssetImage(
                  //                 ic_language,
                  //               ),
                  //               color: black,
                  //               size: 18),
                  //           8.width,
                  //           Text(
                  //             language.websiteSection,
                  //             style: boldTextStyle(),
                  //           )
                  //         ],
                  //       ),
                  //       children: getWebsiteSectionMenu().map((e) {
                  //         return InkWell(
                  //           child: Container(
                  //               alignment: Alignment.centerLeft,
                  //               padding: EdgeInsets.only(left: 32, right: appStore.selectedLanguage == 'ar' ? 16 : 0, top: 10, bottom: 10),
                  //               child: Row(
                  //                 children: [
                  //                   ImageIcon(
                  //                     AssetImage(e.imagePath!),
                  //                     size: 18,
                  //                     //    color: appStore.selectedMenuIndex == e.index || isHovering ? primaryColor : textPrimaryColorGlobal,
                  //                   ),
                  //                   20.width,
                  //                   Text(
                  //                     e.title!,
                  //                     style: primaryTextStyle(size: 16, weight: FontWeight.bold),
                  //                   ),
                  //                 ],
                  //               )),
                  //           onTap: () {
                  //             launchScreen(context, e!.widget!);
                  //           },
                  //         );
                  //       }).toList()),
                  // ),
                  // Padding(
                  //   padding: EdgeInsets.all(16),
                  //   child: GestureDetector(
                  //     child: Row(
                  //       children: [
                  //         Icon(Icons.logout, size: 18),
                  //         8.width,
                  //         Text(language.logout, style: boldTextStyle(color: Colors.red)),
                  //       ],
                  //     ),
                  //     onTap: () {
                  //       logOutData(context: context);
                  //     },
                  //   ),
                  // ),
                  AnimatedContainer(
                    duration: Duration(milliseconds: 100),
                    width: context.width(),
                    height: context.height() / 1.4,
                    decoration: BoxDecoration(color: context.scaffoldBackgroundColor),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        ListView(
                          padding: EdgeInsets.zero,
                          children: getMenuItems().map((item) {
                            return item.expansionList != null && item.expansionList!.isNotEmpty
                                ? Theme(
                                    data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
                                    child: ExpansionTile(
                                      collapsedIconColor: appStore.isDarkMode?Colors.white:primaryColor,
                                      iconColor: appStore.isDarkMode?Colors.white:primaryColor,
                                      initiallyExpanded: appStore.expandedIndex == item.expandedIndex ? true : false,
                                      tilePadding: EdgeInsets.only(left: 16, right: 20, top: 0, bottom: 0),
                                      onExpansionChanged: (v) {
                                        //    isExpanded = v;
                                        appStore.setExpandedIndex(item.expandedIndex.validate());
                                      },
                                      title: Row(
                                        children: [
                                          ImageIcon(
                                            AssetImage(item.imagePath!),
                                            size: 18,
                                            color: textPrimaryColorGlobal,
                                          ),
                                          if (appStore.isMenuExpanded) SizedBox(width: 12),
                                          if (appStore.isMenuExpanded) Text(item.title.validate(), style: primaryTextStyle(color: textPrimaryColorGlobal)).expand(),
                                        ],
                                      ),
                                      children: item.expansionList.validate().map((e) {
                                        return InkWell(
                                          child: Row(
                                            children: [
                                              Container(
                                                alignment: Alignment.centerLeft,
                                                padding: EdgeInsets.only(left: 32, right: appStore.selectedLanguage == 'ar' ? 32 : 0, top: 8, bottom: 8),
                                                child: Row(
                                                  children: [
                                                    ImageIcon(AssetImage(e.imagePath!), color: textPrimaryColorGlobal, size: 18),
                                                    appStore.isMenuExpanded
                                                        ? Padding(
                                                            padding: EdgeInsets.only(left: 10),
                                                            child: Text(
                                                              e.title!,
                                                              maxLines: 1,
                                                              style: primaryTextStyle(color: textPrimaryColorGlobal),
                                                            ).paddingOnly(left: 12, right: appStore.selectedLanguage == 'ar' ? 16 : 0),
                                                          )
                                                        : SizedBox(),
                                                  ],
                                                ),
                                              ),
                                            ],
                                          ),
                                          onTap: () {
                                            if (appStore.selectedMenuIndex != e.index!) {
                                              appStore.setSelectedMenuIndex(e.index!);
                                              launchScreen(context, e.widget!);
                                              //    Navigator.pushReplacementNamed(context, e.route!);
                                            }
                                          },
                                        );
                                      }).toList(),
                                    ),
                                  )
                                : InkWell(
                                    child: Row(
                                      children: [
                                        Container(
                                          alignment: Alignment.centerLeft,
                                          padding: EdgeInsets.only(left: 12, top: 12, bottom: 12, right: appStore.selectedLanguage == 'ar' ? 16 : 0),
                                          decoration: BoxDecoration(),
                                          child: Row(
                                            children: [
                                              ImageIcon(AssetImage(item.imagePath!), color: textPrimaryColorGlobal, size: 18),
                                              appStore.isMenuExpanded
                                                  ? Padding(
                                                      padding: EdgeInsets.only(left: 10, right: appStore.selectedLanguage == 'ar' ? 16 : 0),
                                                      child: Text(item.title!, maxLines: 1, style: primaryTextStyle(color: textPrimaryColorGlobal)),
                                                    )
                                                  : SizedBox(),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                    onTap: () {
                                      appStore.setExpandedIndex(item.expandedIndex.validate());

                                      if (appStore.selectedMenuIndex != item.index!) {
                                        appStore.setSelectedMenuIndex(item.index!);
                                        launchScreen(context, item.widget!);
                                        //   Navigator.pushReplacementNamed(context, item.widget()!);
                                      }
                                    },
                                  );
                          }).toList(),
                        ).expand(),
                        Padding(
                          padding: EdgeInsets.only(left:16,top:16,right: 16,bottom: 16),
                          child: GestureDetector(
                            child: Container(
                              width: context.width(),
                              height: 50,
                              decoration: boxDecorationWithRoundedCorners(backgroundColor: Colors.red,borderRadius:BorderRadius.circular(4)),
                              child: Row(
                                children: [
                                  Icon(Icons.logout, size: 18,color: white,),
                                  8.width,
                                  Text(language.logout, style: boldTextStyle(color: white)),
                                ],
                              ).paddingOnly(left: 8,right: 8),
                            ),
                            onTap: () {
                              logOutData(context: context);
                            },
                          ),
                        ),
                      ],
                    ).paddingOnly(left: 8, right: 8),
                  ),

                ],
              ),
            ),
            Visibility(visible: appStore.isLoading, child: loaderWidget()),
          ],
        ),
      );
    });
  }
}
