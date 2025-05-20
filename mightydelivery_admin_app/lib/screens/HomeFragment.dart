import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../models/UserModel.dart';
import '../screens/UserDetailScreen.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../components/MonthlyOrderCountComponent.dart';
import '../components/MonthlyPaymentCountComponent.dart';
import '../components/OrderWidgetComponent.dart';
import '../components/WeeklyOrderCountComponent.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/DashboardModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/app_common.dart';
import 'NotificationScreen.dart';

class HomeFragment extends StatefulWidget {
  static String tag = '/AppHomeWidget';

  @override
  HomeFragmentState createState() => HomeFragmentState();
}

class HomeFragmentState extends State<HomeFragment> {
  ScrollController scrollController = ScrollController();
  ScrollController recentOrderController = ScrollController();
  ScrollController recentOrderHorizontalController = ScrollController();
  ScrollController upcomingOrderController = ScrollController();
  ScrollController upcomingOrderHorizontalController = ScrollController();
  ScrollController userController = ScrollController();
  ScrollController userHorizontalController = ScrollController();
  ScrollController deliveryBoyController = ScrollController();
  ScrollController deliveryBoyHorizontalController = ScrollController();

  List<WeeklyOrderCount> userWeeklyCount = [];
  List<WeeklyOrderCount> weeklyOrderCount = [];
  List<WeeklyOrderCount> weeklyPaymentReport = [];

  List<MonthlyOrderCount> monthlyOrderCount = [];
  List<MonthlyPaymentCompletedReport> monthlyCompletePaymentReport = [];
  List<MonthlyPaymentCompletedReport> monthlyCancelPaymentReport = [];

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    // var userModel;
    // bool isUser = await userService.isUserExist(sharedPref.getString(USER_EMAIL));
    // if (!isUser) {
    //   userModel = UserModel(
    //       email: sharedPref.getString(USER_EMAIL),
    //       playerId: sharedPref.getString(PLAYER_ID),
    //       apiToken: sharedPref.getString(TOKEN),
    //       loginType: sharedPref.getString(NAME),
    //       uid: sharedPref.getInt(USER_ID).toString(),
    //       name: sharedPref.getString(NAME),
    //       username: sharedPref.getString(USER_NAME),
    //       userType: sharedPref.getString(USER_TYPE),
    //       profileImage: sharedPref.getString(USER_PROFILE_PHOTO),
    //       contactNumber: sharedPref.getString(
    //         USER_CONTACT_NUMBER,
    //       ),
    //       address: sharedPref.getString(
    //         USER_ADDRESS,
    //       ));
    //   userService.addDocument(userModel.toJson());
    // } else {
    //   String uid1 = sharedPref.getString(FIREBASE_UID) ?? "";
    //   var map = Map<String, dynamic>();
    //   map = {'uid': uid1};
    //   userService.updateDocument(
    //     map,
    //     uid1,
    //   );
    //}

    LiveStream().on(streamDarkMode, (p0) {
      setState(() {});
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  void callMethod(int count) {
    afterBuildCreated(() => appStore.setAllUnreadCount(count));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(language.dashboard, showBack: false, actions: [
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
      body: FutureBuilder<DashboardModel>(
        future: getDashBoardData(),
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            userWeeklyCount = snapshot.data!.userWeeklyCount!;
            weeklyOrderCount = snapshot.data!.weeklyOrderCount!;
            weeklyPaymentReport = snapshot.data!.weeklyPaymentReport!;
            monthlyOrderCount = snapshot.data!.monthlyOrderCount ?? [];
            monthlyCompletePaymentReport = snapshot.data!.monthlyPaymentCompletedReport ?? [];
            monthlyCancelPaymentReport = snapshot.data!.monthlyPaymentCancelledReport ?? [];
            callMethod(snapshot.data!.allUnreadCount ?? 0);
            return SingleChildScrollView(
              controller: scrollController,
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  GridView.count(
                    // scrollDirection: Axis.horizontal,
                    physics: ScrollPhysics(),
                    shrinkWrap: true,
                    primary: true,
                    crossAxisCount: 4,
                    childAspectRatio: 1, //1.0
                    mainAxisSpacing: 8, //1.0
                    crossAxisSpacing: 8.0, //1.0
                    children: [
                      totalUserWidget(context, title: language.totalOrder, totalCount: snapshot.data!.totalOrder, bgColor: Color(0xFFC8FACD), color: Color(0xFF2A956D)),
                      totalUserWidget(context, title: language.createdOrder, totalCount: snapshot.data!.totalCreateOrder, bgColor: Color(0xFFD0F2FF), color: Color(0xFF0D3380)),
                      totalUserWidget(context, title: language.assignedOrder, totalCount: snapshot.data!.totalCourierAssignedOrder, bgColor: Color(0xFFFFE7D9), color: Color(0xFF6D001E)),
                      totalUserWidget(context, title: language.acceptedOrder, totalCount: snapshot.data!.totalActiveOrder, bgColor: Color(0xFFfadee8), color: Color(0xFFb51f4f)),
                      totalUserWidget(context, title: language.arrivedOrder, totalCount: snapshot.data!.totalCourierArrivedOrder, bgColor: Color(0xFFFFF7CD), color: Color(0xFFB17700)),
                      totalUserWidget(context, title: language.pickedOrder, totalCount: snapshot.data!.totalCourierPickedUpOrder, bgColor: Color(0xFFC8FACD), color: Color(0xFF2A956D)),
                      totalUserWidget(context, title: language.departedOrder, totalCount: snapshot.data!.totalCourierDepartedOrder, bgColor: Color(0xFFD0F2FF), color: Color(0xFF0D3380)),
                      totalUserWidget(context, title: language.deliveredOrder, totalCount: snapshot.data!.totalCompletedOrder, bgColor: Color(0xFFFFE7D9), color: Color(0xFF6D001E)),
                      totalUserWidget(context, title: language.cancelledOrder, totalCount: snapshot.data!.totalCancelledOrder, bgColor: Color(0xFFfadee8), color: Color(0xFFb51f4f)),
                      totalUserWidget(context, title: language.totalUser, totalCount: snapshot.data!.totalOrder, bgColor: Color(0xFFFFF7CD), color: Color(0xFFB17700)),
                      totalUserWidget(context, title: language.totalDeliveryPerson, totalCount: snapshot.data!.totalDeliveryMan, bgColor: Color(0xFFC8FACD), color: Color(0xFF2A956D)),
                    ],
                  ),
                  16.height,
                  Text(language.charts, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)).paddingLeft(6),
                  16.height,
                  Container(
                    height: 400,
                    child: ListView(
                      scrollDirection: Axis.horizontal,
                      //   controller: pageController,
                      children: [
                        WeeklyOrderCountComponent(weeklyOrderCount: weeklyOrderCount).paddingRight(8),
                        MonthlyOrderCountComponent(monthlyCount: monthlyOrderCount).paddingRight(8),
                        MonthlyPaymentCountComponent(monthlyCompletePayment: monthlyCompletePaymentReport, monthlyCancelPayment: monthlyCancelPaymentReport, isPaymentType: true).paddingRight(8),
                      ],
                    ),
                  ).paddingOnly(right: 6),
                  SizedBox(height: 16),
                  Text(language.recentOrder, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)).visible(snapshot.data!.recentOrder!.isNotEmpty).paddingLeft(6),
                  16.height,
                  snapshot.data!.recentOrder!.isNotEmpty
                      ? SizedBox(
                          height: context.height() * 0.31,
                          child: ListView.builder(
                              scrollDirection: Axis.horizontal,
                              //   physics: NeverScrollableScrollPhysics(),
                              itemCount: snapshot.data!.recentOrder!.length,
                              itemBuilder: (context, i) {
                                return SizedBox(width: context.width() / 1.1, child: orderWidget(context, snapshot.data!.recentOrder![i])).paddingRight(8);
                              }),
                        )
                      : SizedBox(),
                  Text(language.upcomingOrder, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)).visible(snapshot.data!.upcomingOrder!.isNotEmpty).paddingLeft(6),
                  16.height.visible(snapshot.data!.upcomingOrder!.isNotEmpty),
                  snapshot.data!.upcomingOrder!.isNotEmpty
                      ? SizedBox(
                          height: context.height() * 0.31,
                          child: ListView.builder(
                              scrollDirection: Axis.horizontal,
                              //   physics: NeverScrollableScrollPhysics(),
                              itemCount: snapshot.data!.upcomingOrder!.length,
                              itemBuilder: (context, i) {
                                return SizedBox(width: context.width() / 1.1, child: orderWidget(context, snapshot.data!.upcomingOrder![i])).paddingRight(8);
                              }),
                        )
                      : SizedBox(),
                  Text(language.recentUser, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)).visible(snapshot.data!.recentClient!.isNotEmpty).paddingLeft(6),
                  16.height.visible(snapshot.data!.recentClient!.isNotEmpty),
                  snapshot.data!.recentClient!.isNotEmpty
                      ? SizedBox(
                          height: context.height() * 0.31,
                          child: ListView.builder(
                              scrollDirection: Axis.horizontal,
                              //   physics: NeverScrollableScrollPhysics(),
                              itemCount: snapshot.data!.recentClient!.length,
                              itemBuilder: (context, i) {
                                return SizedBox(width: context.width() / 1.1, child: userCardWidget(snapshot.data!.recentClient![i])).paddingRight(8);
                              }),
                        )
                      : SizedBox(),
                  Text(language.recentDelivery, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)).visible(snapshot.data!.recentDeliveryMan!.isNotEmpty).paddingLeft(6),
                  16.height.visible(snapshot.data!.recentDeliveryMan!.isNotEmpty),
                  snapshot.data!.recentDeliveryMan!.isNotEmpty
                      ? SizedBox(
                          height: context.height() * 0.31,
                          child: ListView.builder(
                              scrollDirection: Axis.horizontal,
                              //   physics: NeverScrollableScrollPhysics(),
                              itemCount: snapshot.data!.recentDeliveryMan!.length,
                              itemBuilder: (context, i) {
                                return SizedBox(width: context.width() / 1.1, child: userCardWidget(snapshot.data!.recentDeliveryMan![i])).paddingRight(8);
                              }),
                        )
                      : SizedBox(),
                ],
              ),
            );
          } else if (snapshot.hasError) {
            return emptyWidget();
          }
          return loaderWidget();
        },
      ),
    );
  }

  userCardWidget(UserModel data) {
    return Container(
      margin: EdgeInsets.only(bottom: 16),
      width: MediaQuery.of(context).size.width,
      decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.start,
        children: [
          Container(
            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            decoration: BoxDecoration(
              color: primaryColor.withOpacity(0.2),
              borderRadius: BorderRadius.only(topLeft: Radius.circular(defaultRadius), topRight: Radius.circular(defaultRadius)),
            ),
            width: MediaQuery.of(context).size.width,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('#' + data.id.toString(), style: boldTextStyle()),
              ],
            ),
          ),
          SizedBox(height: 4),
          Padding(
            padding: EdgeInsets.all(12),
            child: Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(language.name, style: primaryTextStyle(size: 14)),
                    Text(data.username.validate(), style: boldTextStyle(size: 15)),
                  ],
                ),
                Divider(thickness: 0.9, height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [Text(language.email, style: primaryTextStyle(size: 14)), Text(data.email.toString(), style: primaryTextStyle(size: 15))],
                ),
                Divider(thickness: 0.9, height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(language.city, style: primaryTextStyle(size: 14)),
                    Text(data.cityName.isEmptyOrNull ? "-" : data.cityName.validate(), style: primaryTextStyle(size: 15)),
                  ],
                ),
                Divider(thickness: 0.9, height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(language.contactNumber, style: primaryTextStyle(size: 14)),
                    Text(data.contactNumber.isEmptyOrNull ? "-" : data.contactNumber.validate(), style: primaryTextStyle(size: 15)),
                  ],
                ),
                Divider(thickness: 0.9, height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(language.status, style: primaryTextStyle(size: 14)),
                    Text(data.status == "1" ? language.enable : language.disable, style: boldTextStyle(size: 15, color: appStore.isDarkMode ? white : primaryColor)),
                  ],
                ),
              ],
            ),
          ),
        ],
      ).onTap(() {
        launchScreen(context, UserDetailScreen(userId: data.id, userType: data.userType));
      }),
    );
  }
}
