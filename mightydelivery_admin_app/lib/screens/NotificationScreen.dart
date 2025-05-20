import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';

import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/NotificationModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import 'OrderDetailScreen.dart';

class NotificationScreen extends StatefulWidget {
  @override
  NotificationScreenState createState() => NotificationScreenState();
}

class NotificationScreenState extends State<NotificationScreen> {
  ScrollController scrollController = ScrollController();
  int currentPage = 1;
  ScrollController controller = ScrollController();

  bool mIsLastPage = false;
  List<NotificationData> notificationData = [];

  @override
  void initState() {
    super.initState();
    afterBuildCreated(init);
    scrollController.addListener(() {
      if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
        if (!mIsLastPage) {
          appStore.setLoading(true);

          currentPage++;
          setState(() {});

          init();
        }
      }
    });
  }

  void init() async {
    appStore.setLoading(true);
    getNotification(page: currentPage).then((value) {
      appStore.setLoading(false);
      appStore.setAllUnreadCount(value.allUnreadCount ?? 0);
      mIsLastPage = value.notificationData!.length < currentPage;
      if (currentPage == 1) {
        notificationData.clear();
      }
      notificationData.addAll(value.notificationData!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Widget notificationCard(NotificationData data) {
    return Container(
      margin: EdgeInsets.only(bottom: 8),
      decoration: boxDecorationWithRoundedCorners(borderRadius: BorderRadius.circular(defaultRadius), backgroundColor: primaryColor.withOpacity(0.08)),
      padding: EdgeInsets.all(12),
      child: Row(
        children: [
          Container(
            height: 32,
            width: 32,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: primaryColor.withOpacity(0.15),
            ),
            child: Image.asset(statusTypeIcon(type: data.data!.type), fit: BoxFit.fill, color: primaryColor, width: 18, height: 18),
          ),
          8.width,
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('${data.data!.subject}', style: secondaryTextStyle()).expand(),
                  8.width,
                  Text(timeAgo(data.createdAt.validate()), style: secondaryTextStyle()),
                ],
              ),
              6.height,
              Row(
                children: [
                  Text('${data.data!.message}', style: primaryTextStyle(size: 14)).expand(),
                  if (data.readAt.isEmptyOrNull) Icon(Entypo.dot_single, color: primaryColor),
                ],
              ),
            ],
          ).expand(),
        ],
      ).onTap(() async {
        bool? res = await OrderDetailScreen(orderId: data.data!.id.validate()).launch(context);
        if (res!) {
          currentPage = 1;
          init();
        }
      }),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(language.notification, actions: [
        if (appStore.allUnreadCount > 0)
          GestureDetector(
            child: Text(language.markAllAsRead, style: primaryTextStyle(size: 14, color: white)).paddingRight(8),
            onTap: () {
              Map req = {
                "type": "markas_read",
              };
              appStore.setLoading(true);
              getNotification(page: 1, req: req).then((value) {
                appStore.setLoading(false);
                appStore.setAllUnreadCount(value.allUnreadCount ?? 0);
                notificationData.addAll(value.notificationData!);
                init();
                setState(() {});
              }).catchError((error) {
                appStore.setLoading(false);
                log(error);
              });
            },
          ).paddingOnly(right: 10, left: appStore.selectedLanguage == "ar" ? 12 : 0),
      ]),
      body: Observer(builder: (context) {
        return Stack(
          children: [
            notificationData.isNotEmpty
                ? ListView.builder(
                    controller: scrollController,
                    padding: EdgeInsets.zero,
                    itemCount: notificationData.length,
                    itemBuilder: (_, index) {
                      NotificationData data = notificationData[index];
                      return notificationCard(data);
                    },
                  ).paddingAll(10)
                : !appStore.isLoading
                    ? emptyWidget()
                    : SizedBox(),
            Visibility(visible: appStore.isLoading, child: loaderWidget()),
          ],
        );
      }),
    );
  }
}
