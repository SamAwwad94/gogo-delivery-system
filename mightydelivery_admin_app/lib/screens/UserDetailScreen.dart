import 'package:flutter/material.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:maps_launcher/maps_launcher.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';
import 'package:url_launcher/url_launcher.dart';

import '../components/AddMoneyDialog.dart';
import '../components/AddUserDialog.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/UserModel.dart';
import '../models/UserProfileDetailModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Images.dart';
import 'DeliveryPersonDocumentScreen.dart';

class UserDetailScreen extends StatefulWidget {
  final int? userId;
  final String? userType;

  UserDetailScreen({this.userId, this.userType});

  @override
  UserDetailScreenState createState() => UserDetailScreenState();
}

class UserDetailScreenState extends State<UserDetailScreen> with SingleTickerProviderStateMixin {
  UserProfileDetailModel? userProfileData;
  UserModel? userData;
  WalletHistory? walletHistory;
  EarningDetail? earningDetail;
  EarningList? earningList;

  bool isUpdated = false;
  TabController? tabController;
  static List<String> tabs = [];
  static int selectedTabIndex = 0;

  @override
  void initState() {
    super.initState();
    init();
  }

  getTabsContent() {
    return Column(
      children: [tab1(), tab2(), if (tabs.length == 3) tab3()],
    );
  }

  void init() async {
    if (widget.userType == CLIENT) {
      tabs.clear();
      tabs.add(language.profile);
      tabs.add(language.walletHistory);
    } else {
      tabs.clear();
      tabs.add(language.profile);
      tabs.add(language.walletHistory);
      tabs.add(language.earningHistory);
    }
    tabController = TabController(
      length: tabs.length,
      vsync: this,
      initialIndex: selectedTabIndex,
    );
    setState(() {});
    getUserDetailApiCall();
  }

  getUserDetailApiCall() async {
    appStore.setLoading(true);
    await getUserProfile(widget.userId!).then((value) {
      appStore.setLoading(false);
      userProfileData = value;
      userData = value.data;
      walletHistory = value.walletHistory;
      earningDetail = value.earningDetail;
      earningList = value.earningList;
      setState(() {});
    }).catchError((e) {
      log(e.toString());
      appStore.setLoading(false);
    });
  }

  updateStatusApiCall() async {
    Map req = {
      "id": userData!.id,
      "status": userData!.status == 1 ? 0 : 1,
    };
    appStore.setLoading(true);
    await updateUserStatus(req).then((value) {
      appStore.setLoading(false);
      getUserDetailApiCall();
      isUpdated = true;
      setState(() {});
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteUserApiCall() async {
    Map req = {"id": userData!.id};
    appStore.setLoading(true);
    await deleteUser(req).then((value) {
      appStore.setLoading(false);
      getUserDetailApiCall();
      isUpdated = true;
      setState(() {});
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  restoreUserBoyApiCall({@required String? type}) async {
    Map req = {"id": userData!.id, "type": type};
    appStore.setLoading(true);
    await userAction(req).then((value) {
      appStore.setLoading(false);
      isUpdated = true;
      if (type == RESTORE) {
        getUserDetailApiCall();
      } else {
        finish(context, true);
      }

      setState(() {});
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  getWalletListApi(int currentPage) async {
    appStore.setLoading(true);
    await getWalletList(page: currentPage, userId: userData!.id).then((value) {
      appStore.setLoading(false);
      walletHistory = value;
      setState(() {});
    }).catchError((e) {
      appStore.setLoading(false);
      log(e);
    });
  }

  getPaymentListApi(int currentPage) async {
    appStore.setLoading(true);
    await getPaymentList(page: currentPage, userId: userData!.id).then((value) {
      appStore.setLoading(false);
      earningList = value;
      setState(() {});
    }).catchError((e) {
      appStore.setLoading(false);
      log(e);
    });
  }

  mobileNumberVerification({int? userId}) {
    Map req = {
      "id": userId,
      "otp_verify_at": DateTime.now().toString(),
    };
    updateUserStatus(req).then((value) {
      finish(context);
      toast(value.message);
      getUserDetailApiCall();
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  commonWidget({String? title, var value}) {
    return Container(
      decoration: boxDecorationWithRoundedCorners(backgroundColor: context.cardColor, border: Border.all(width: 0.9, color: context.dividerColor), borderRadius: radius(defaultRadius)),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Text(title!, style: primaryTextStyle()),
          8.height,
          Text(value!, style: boldTextStyle()),
        ],
      ),
    );
  }

  tab1() {
    return SingleChildScrollView(
      child: Column(
        children: [
          if (userData != null)
            Container(
              margin: EdgeInsets.only(bottom: 16),
              decoration: containerDecoration(
                color: userData != null
                    ? Colors.red.shade100.withOpacity(0.2)
                    : appStore.isDarkMode
                        ? textPrimaryColor
                        : white,
              ),
              child: Column(
                children: [
                  Container(
                    decoration: BoxDecoration(
                      color: primaryColor.withOpacity(0.2),
                      borderRadius: BorderRadius.only(topLeft: Radius.circular(16), topRight: Radius.circular(16)),
                    ),
                    padding: EdgeInsets.all(12),
                    child: Row(
                      children: [
                        Text('#${userData!.id ?? "-"}', style: boldTextStyle()),
                        Spacer(),
                        userData!.deletedAt == null
                            ? userData!.otpVerifyAt.isEmptyOrNull
                                ? outlineActionIcon(context, Icons.mobile_friendly, appStore.isDarkMode ? white : primaryColor, () {
                                    commonConfirmationDialog(context, DIALOG_TYPE_VERIFY, () {
                                      if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                        toast(language.demo_admin_msg);
                                      } else {
                                        mobileNumberVerification(userId: userData!.id);
                                      }
                                    }, title: '${language.mobileNumberVerify} ?', subtitle: '${userData!.contactNumber}\n${language.mobileNumberVerifyMsg}');
                                  })
                                : SizedBox()
                            : Text('-', textAlign: TextAlign.center),
                        8.width,
                        GestureDetector(
                          child: Container(
                            alignment: Alignment.center,
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            margin: EdgeInsets.only(right: 8, left: appStore.selectedLanguage == "ar" ? 8 : 0),
                            child: Text(
                              '${userData!.status == 1 ? language.enable : language.disable}',
                              style: primaryTextStyle(
                                  color: userData!.status == 1
                                      ? appStore.isDarkMode
                                          ? white
                                          : primaryColor
                                      : Colors.red,
                                  size: 14),
                            ),
                            decoration: BoxDecoration(
                                border: Border.all(color: userData!.status == 1 ? primaryColor.withOpacity(0.6) : Colors.red.withOpacity(0.6)),
                                color: userData!.status == 1 ? primaryColor.withOpacity(0.15) : Colors.red.withOpacity(0.15),
                                borderRadius: BorderRadius.circular(defaultRadius)),
                          ),
                          onTap: () {
                            userData!.deletedAt == null
                                ? commonConfirmationDialog(context, userData!.status == 1 ? DIALOG_TYPE_DISABLE : DIALOG_TYPE_ENABLE, () {
                                    if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                      toast(language.demoAdminMsg);
                                    } else {
                                      Navigator.pop(context);
                                      updateStatusApiCall();
                                    }
                                  },
                                    title: userData!.status != 1
                                        ? userData!.userType == DELIVERYMAN
                                            ? language.enableDeliveryPerson
                                            : language.enableUser
                                        : userData!.userType == DELIVERYMAN
                                            ? language.disableDeliveryPerson
                                            : language.disableUser,
                                    subtitle: userData!.status != 1
                                        ? userData!.userType == DELIVERYMAN
                                            ? language.enableDeliveryPersonMsg
                                            : language.enableUserMsg
                                        : userData!.userType == DELIVERYMAN
                                            ? language.disableDeliveryPersonMsg
                                            : language.disableUserMsg)
                                : toast(language.youCannotUpdateStatusRecordDeleted);
                          },
                        ),
                        Row(
                          children: [
                            outlineActionIcon(context, userData!.deletedAt == null ? Icons.edit : Icons.restore, Colors.green, () {
                              userData!.deletedAt == null
                                  ? showDialog(
                                      context: context,
                                      barrierDismissible: false,
                                      builder: (BuildContext dialogContext) {
                                        return AddUserDialog(
                                          userData: userData!,
                                          userType: userData?.userType,
                                          onUpdate: () {
                                            isUpdated = true;
                                            getUserDetailApiCall();
                                            setState(() {});
                                          },
                                        );
                                      },
                                    )
                                  : commonConfirmationDialog(context, DIALOG_TYPE_RESTORE, () {
                                      if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                        toast(language.demoAdminMsg);
                                      } else {
                                        Navigator.pop(context);
                                        restoreUserBoyApiCall(type: RESTORE);
                                      }
                                    }, title: language.restoreDeliveryPerson, subtitle: language.restoreDeliveryPersonMsg);
                            }),
                            SizedBox(width: 8),
                          ],
                        ),
                        outlineActionIcon(context, userData!.deletedAt == null ? Icons.delete : Icons.delete_forever, Colors.red, () {
                          commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () {
                            if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                              toast(language.demoAdminMsg);
                            } else {
                              Navigator.pop(context);
                              userData!.deletedAt == null ? deleteUserApiCall() : restoreUserBoyApiCall(type: FORCE_DELETE);
                            }
                          },
                              isForceDelete: userData!.deletedAt != null,
                              title: userData!.userType == DELIVERYMAN ? language.deleteDeliveryPerson : language.deleteUser,
                              subtitle: userData!.userType == DELIVERYMAN ? language.deleteDeliveryPersonMsg : language.deleteUserMsg);
                        }),
                      ],
                    ),
                  ),
                  Padding(
                    padding: EdgeInsets.all(12),
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Container(
                              height: 60,
                              width: 60,
                              decoration: BoxDecoration(
                                border: Border.all(color: Colors.grey.withOpacity(0.15)),
                                shape: BoxShape.circle,
                                image: DecorationImage(image: NetworkImage('${userData!.profileImage!}'), fit: BoxFit.cover),
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
                                      Text('${userData!.name ?? ""}', style: boldTextStyle()),
                                      if (widget.userType == DELIVERYMAN && userData!.deletedAt == null)
                                        Row(
                                          children: [
                                            userData!.isVerifiedDeliveryMan! == 1
                                                ? Text(language.verified, style: primaryTextStyle(color: Colors.green))
                                                : SizedBox(
                                                    height: 30,
                                                    child: ElevatedButton(
                                                        style:
                                                            ElevatedButton.styleFrom(elevation: 0, shape: RoundedRectangleBorder(borderRadius: radius(defaultRadius)), backgroundColor: primaryColor),
                                                        onPressed: () async {
                                                          bool res = await launchScreen(context, DeliveryPersonDocumentScreen(deliveryManId: userData!.id!));
                                                          if (res) {
                                                            isUpdated = true;
                                                            getUserDetailApiCall();
                                                            setState(() {});
                                                          }
                                                        },
                                                        child: Text(language.verify, style: primaryTextStyle(color: Colors.white)))),
                                            SizedBox(width: 8),
                                            outlineActionIcon(context, Icons.location_on, appStore.isDarkMode ? white : primaryColor, () {
                                              if (userData!.latitude != null && userData!.longitude != null) {
                                                MapsLauncher.launchCoordinates(double.parse(userData!.latitude!), double.parse(userData!.longitude!));
                                              } else {
                                                toast(language.locationNotExist);
                                              }
                                            }),
                                          ],
                                        ),
                                    ],
                                  ),
                                  Text(userData!.email.validate(), style: secondaryTextStyle()),
                                ],
                              ),
                            ),
                          ],
                        ),
                        SizedBox(height: 16),
                        if (userData!.contactNumber != null)
                          GestureDetector(
                            onTap: () {
                              launchUrl(Uri.parse('tel:${userData!.contactNumber}'));
                            },
                            child: Row(
                              children: [
                                Icon(Icons.call, color: Colors.green, size: 20),
                                SizedBox(width: 8),
                                Text(userData!.contactNumber.validate(), style: primaryTextStyle(size: 14)),
                              ],
                            ),
                          ),
                        SizedBox(height: 8),
                        if (userData!.cityName != null || userData!.countryName != null)
                          Row(
                            children: [
                              Icon(Icons.location_city, color: appStore.isDarkMode ? white : primaryColor, size: 20),
                              SizedBox(width: 8),
                              Text(userData!.cityName.validate() + " ," + userData!.countryName.validate(), style: primaryTextStyle(size: 14)),
                            ],
                          ),
                        if (userData!.cityName != null || userData!.countryName != null) SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(Entypo.calendar, color: appStore.isDarkMode ? white : primaryColor, size: 20),
                            SizedBox(width: 8),
                            Text(printDate(userData!.createdAt.validate()), style: primaryTextStyle(size: 14)),
                          ],
                        ),
                        8.height,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            Image.asset(ic_version, height: 20, width: 20, color: appStore.isDarkMode ? white : primaryColor),
                            8.width,
                            Text(language.appVersion, style: primaryTextStyle(size: 14)),
                            8.width,
                            Text(userData!.app_version.isEmptyOrNull ? "0" : userData!.app_version.toString(), style: primaryTextStyle()),
                          ],
                        ),
                        8.height,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            Image.asset(ic_appsource, height: 20, width: 20, color: appStore.isDarkMode ? white : primaryColor),
                            8.width,
                            Text(language.appSource, style: primaryTextStyle(size: 14)),
                            8.width,
                            Text(userData!.app_source.isEmptyOrNull ? "N/A" : userData!.app_source.toString(), style: primaryTextStyle()),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          if (userData != null && userData!.userBankAccount != null)
            Container(
              margin: EdgeInsets.only(bottom: 16),
              decoration: containerDecoration(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Container(
                    decoration: BoxDecoration(
                      color: primaryColor.withOpacity(0.2),
                      borderRadius: BorderRadius.only(topLeft: Radius.circular(16), topRight: Radius.circular(16)),
                    ),
                    padding: EdgeInsets.all(12),
                    child: Text(language.bankDetails, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)),
                  ),
                  Padding(
                    padding: EdgeInsets.all(12),
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(language.bankName, style: primaryTextStyle(size: 14)),
                            Text('${userData!.userBankAccount!.bankName.validate()}', style: boldTextStyle(size: 15)),
                          ],
                        ),
                        Divider(thickness: 0.9, height: 20),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(language.ifscCode, style: primaryTextStyle(size: 14)),
                            Text('${userData!.userBankAccount!.bankCode.validate()}', style: boldTextStyle(size: 15)),
                          ],
                        ),
                        Divider(thickness: 0.9, height: 20),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(language.accountHolderName, style: primaryTextStyle(size: 14)),
                            Text('${userData!.userBankAccount!.accountHolderName.validate()}', style: boldTextStyle(size: 15)),
                          ],
                        ),
                        Divider(thickness: 0.9, height: 20),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(language.accountNumber, style: primaryTextStyle(size: 14)),
                            Text('${userData!.userBankAccount!.accountNumber.validate()}', style: boldTextStyle(size: 15)),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          if (earningDetail != null)
            Container(
                margin: EdgeInsets.only(bottom: 16),
                decoration: containerDecoration(),
                child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
                  Container(
                    decoration: BoxDecoration(
                      color: primaryColor.withOpacity(0.2),
                      borderRadius: BorderRadius.only(topLeft: Radius.circular(16), topRight: Radius.circular(16)),
                    ),
                    padding: EdgeInsets.all(12),
                    child: Text(language.earningHistory, style: boldTextStyle(size: 16, color: appStore.isDarkMode ? white : primaryColor)),
                  ),
                  8.height,
                  GridView.count(
                    // scrollDirection: Axis.horizontal,
                    physics: ScrollPhysics(),
                    shrinkWrap: true,
                    primary: true,
                    crossAxisCount: 3,
                    childAspectRatio: 1, //1.0
                    mainAxisSpacing: 8, //1.0
                    crossAxisSpacing: 8.0,
                    children: [
                      commonWidget(title: language.walletBalance, value: printAmount(earningDetail!.walletBalance ?? 0)),
                      commonWidget(title: language.totalWithdraw, value: printAmount(earningDetail!.totalWithdrawn ?? 0)),
                      commonWidget(title: language.commission, value: printAmount(earningDetail!.adminCommission ?? 0)),
                      commonWidget(title: language.commission, value: printAmount(earningDetail!.deliveryManCommission ?? 0)),
                      commonWidget(title: language.totalOrder, value: earningDetail!.totalOrder.toString().validate(value: '0')),
                      commonWidget(title: language.paidOrder, value: earningDetail!.paidOrder.toString().validate(value: '0')),
                    ],
                  ).paddingOnly(left: 8, right: 8, bottom: 8),
                ])),
        ],
      ),
      padding: EdgeInsets.all(16),
    );
  }

  tab2() {
    return Stack(
      children: [
        if (walletHistory != null && (walletHistory!.data ?? []).isNotEmpty)
          Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              8.height,
              if (walletHistory!.pagination!.totalItems! > walletHistory!.pagination!.perPage!)
                Padding(
                  padding: EdgeInsets.only(bottom: 12, right: 12, top: 12),
                  child: paginationWidget(
                      currentPage: walletHistory!.pagination!.currentPage!,
                      totalPage: walletHistory!.pagination!.totalPages!,
                      onUpdate: (currentPage) {
                        getWalletListApi(currentPage);
                        getWalletListApi(currentPage);
                      }),
                ),
              ListView.builder(
                  padding: EdgeInsets.fromLTRB(12, 12, 12, 0),
                  primary: true,
                  shrinkWrap: true,
                  itemCount: walletHistory!.data!.length,
                  itemBuilder: (context, i) {
                    WalletData data = walletHistory!.data![i];
                    return Container(
                      margin: EdgeInsets.only(bottom: 12),
                      padding: EdgeInsets.all(8),
                      decoration: containerDecoration(),
                      child: Column(
                        children: [
                          Row(
                            children: [
                              Text(transactionType(data.transactionType!), style: boldTextStyle(size: 16)).expand(),
                              // Spacer(),
                              Text('${printAmount(data.amount ?? 0)}', style: primaryTextStyle(color: data.type == CREDIT ? Colors.green : Colors.red))
                            ],
                          ),
                          SizedBox(height: 8),
                          Row(
                            children: [
                              Text(printDate(data.createdAt.validate()), style: secondaryTextStyle()),
                              Spacer(),
                              if (data.orderId != null) Text('${language.orderId}: #${data.orderId}', style: secondaryTextStyle()),
                            ],
                          ),
                        ],
                      ),
                    );
                  }).expand(),
            ],
          ),
        emptyWidget().visible(walletHistory != null && walletHistory!.pagination!.totalItems == 0)
      ],
    );
  }

  tab3() {
    return Stack(
      children: [
        if (earningList != null && (earningList!.data ?? []).isNotEmpty)
          Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              8.height,
              Padding(
                padding: EdgeInsets.only(bottom: 12, right: 12, top: 12),
                child: paginationWidget(
                    currentPage: earningList!.pagination!.currentPage!,
                    totalPage: earningList!.pagination!.totalPages!,
                    onUpdate: (currentPage) {
                      getPaymentListApi(currentPage);
                    }),
              ).visible(earningList != null && earningList!.pagination!.totalItems! > earningList!.pagination!.perPage!),
              ListView.builder(
                  padding: EdgeInsets.fromLTRB(12, 12, 12, 0),
                  primary: true,
                  shrinkWrap: true,
                  itemCount: earningList!.data!.length,
                  itemBuilder: (context, i) {
                    EarningData data = earningList!.data![i];
                    return Container(
                      margin: EdgeInsets.only(bottom: 12),
                      padding: EdgeInsets.all(8),
                      decoration: containerDecoration(),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Text('${language.orderId}: #${data.orderId}', style: boldTextStyle()),
                              Spacer(),
                              Text(
                                '${data.paymentType}',
                                style: primaryTextStyle(),
                              )
                            ],
                          ),
                          SizedBox(height: 4),
                          Text(printDate(data.createdAt.validate()), style: secondaryTextStyle()),
                          SizedBox(height: 16),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                language.deliveryManEarning,
                                textAlign: TextAlign.center,
                                style: primaryTextStyle(size: 14),
                              ),
                              Text('${printAmount(data.deliveryManCommission ?? 0)}', style: boldTextStyle(size: 15)),
                            ],
                          ),
                          SizedBox(height: 8),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                language.adminCommission,
                                textAlign: TextAlign.center,
                                style: primaryTextStyle(size: 14),
                              ),
                              Text('${printAmount(data.adminCommission ?? 0)}', style: boldTextStyle(size: 15)),
                            ],
                          ),
                        ],
                      ),
                    );
                  }).expand(),
            ],
          ),
        emptyWidget().visible(earningList != null && earningList!.pagination!.totalItems == 0)
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () {
        Navigator.pop(context, isUpdated);
        return Future.value(true);
      },
      child: Scaffold(
          appBar: appBarWidget(widget.userType == CLIENT ? language.viewUser : language.viewDeliveryPerson, actions: [
            Align(
              alignment: Alignment.center,
              child: ElevatedButton(
                  style: ElevatedButton.styleFrom(elevation: 0, shape: RoundedRectangleBorder(borderRadius: radius(defaultRadius)), backgroundColor: primaryColor),
                  onPressed: () {
                    showDialog(
                      context: context,
                      builder: (context) {
                        return AddWalletDialog(
                            userId: userData!.id,
                            onUpdate: () {
                              getUserDetailApiCall();
                            });
                      },
                    );
                  },
                  child: Container(decoration: boxDecorationDefault(), child: Text(language.addMoney, style: primaryTextStyle(color: primaryColor)).paddingAll(5))),
            ),
          ]),
          body: Container(
            width: context.width(),
            height: context.height(),
            child: Stack(
              children: [
                Column(children: [
                  8.height,
                  TabBar(
                    labelColor: primaryColor,
                    indicatorColor: primaryColor,
                    dividerColor: Colors.grey.withOpacity(0.3),
                    controller: tabController,
                    tabs: List.generate(
                      tabs.length,
                          (index) => Text("${tabs[index]}", style: primaryTextStyle(size: 15)).paddingAll(8),
                    ),
                  ),

                  8.height,
                  Expanded(
                    child: TabBarView(
                      controller: tabController,
                      children: <Widget>[tab1(), tab2(), if (tabs.length == 3) tab3()],
                    ),
                  ),
                ]),
                if (appStore.isLoading) loaderWidget(),
              ],
            ),
          )),
    );
  }
}
