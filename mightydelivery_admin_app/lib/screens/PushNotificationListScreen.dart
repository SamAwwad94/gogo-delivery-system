import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../network/RestApis.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/PushNotificationListModel.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';

class PushNotificationListScreen extends StatefulWidget {
  const PushNotificationListScreen({super.key});

  @override
  State<PushNotificationListScreen> createState() => _PushNotificationListScreenState();
}

class _PushNotificationListScreenState extends State<PushNotificationListScreen> {
  int currentPage = 1;
  int totalPage = 1;
  int perPage = 10;
  ScrollController controller = ScrollController();
  List<PushNotification> pushNotificationList = [];

  @override
  void initState() {
    super.initState();
    init();
    controller.addListener(() {
      if (controller.position.pixels == controller.position.maxScrollExtent) {
        if (currentPage < totalPage) {
          currentPage++;
          setState(() {});
          getPushNotificationListApiCall();
        }
      }
    });
  }

  Future<void> init() async {
    getPushNotificationListApiCall();
  }

  // Document List
  getPushNotificationListApiCall() async {
    appStore.setLoading(true);
    await getPushNotificationList(
      page: currentPage,
      perPage: perPage,
    ).then((value) {
      appStore.setLoading(false);
      totalPage = value.pagination!.totalPages!;
      if (currentPage == 1) {
        pushNotificationList.clear();
      }
      pushNotificationList.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error.toString());
    });
  }

  deleteDeliveryBoyApiCall(int id) async {
    appStore.setLoading(true);
    await deletePushNotification(id).then((value) {
      appStore.setLoading(false);
      getPushNotificationListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Widget pushNotificationCard(PushNotification data) {
    return Container(
      margin: EdgeInsets.only(bottom: 8),
      decoration: boxDecorationWithRoundedCorners(borderRadius: BorderRadius.circular(defaultRadius), backgroundColor: primaryColor.withOpacity(0.08)),
      padding: EdgeInsets.all(12),
      child: Row(
        children: [
          commonCachedNetworkImage(data.notificationImage.validate(), fit: BoxFit.fill, height: 48, width: 48),
          12.width,
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('${data.title.validate()}', style: boldTextStyle(),overflow: TextOverflow.ellipsis,),
              6.height,
              Text('${data.message.validate()}', style: primaryTextStyle(size: 17)),
              6.height,
              Text(
                '${language.numberOfSent} : ${data.notificationCount.validate()}',
                style: primaryTextStyle(
                  size: 14,
                ),
              ),
            ],
          ).expand(),
          4.width,
          Icon(
            Icons.delete,
            color: Colors.red,
          ).onTap(() {
            deleteDeliveryBoyApiCall(data.id.validate());
          }),
        ],
      ),
    );
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
        appBar: appBarWidget(language.pushNotificationList),
        body: Observer(
          builder: (_) => Stack(
            children: [
              ListView.builder(
                  padding: EdgeInsets.only(left: 16, right: 16, top: 16),
                  controller: controller,
                  itemCount: pushNotificationList.length,
                  itemBuilder: (context, i) {
                    PushNotification data = pushNotificationList[i];
                    return pushNotificationCard(data);
                  }),
              appStore.isLoading
                  ? loaderWidget()
                  : pushNotificationList.isEmpty
                      ? emptyWidget()
                      : SizedBox()
            ],
          ),
        ),
      /*  floatingActionButton: FloatingActionButton(
          backgroundColor: primaryColor,
          child: Icon(Icons.add, color: Colors.white),
          onPressed: () {
            SendPushNotificationScreen().launch(context);
          },
        ),*/
      ),
    );
  }
}
