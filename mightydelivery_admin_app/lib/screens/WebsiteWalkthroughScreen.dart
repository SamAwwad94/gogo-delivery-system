import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../components/AddWalkThroughDialog.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';

import '../../main.dart';
import '../../network/RestApis.dart';
import '../../utils/Colors.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../../utils/Extensions/shared_pref.dart';
import '../extensions/widgets.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../utils/Extensions/app_common.dart';

class WebsiteWalkthroughScreen extends StatefulWidget {
  static String route = '/websiteWalkThrough';

  const WebsiteWalkthroughScreen({super.key});

  @override
  State<WebsiteWalkthroughScreen> createState() => _WebsiteWalkthroughScreenState();
}

class _WebsiteWalkthroughScreenState extends State<WebsiteWalkthroughScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();
  GetFrontendDataResponseModel? frontEndData;
  List<Walkthrough> walkThroughDataList = [];

  bool addWalkThroughDialog = false;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    appStore.setLoading(true);
    frontEndDataListApiCall();
  }

  ///FrontendApi
  frontEndDataListApiCall() async {
    await getFrontendDataList().then((value) {
      frontEndData = value;
      walkThroughDataList = frontEndData!.walkthrough!;

      if (walkThroughDataList.length >= 4) {
        addWalkThroughDialog = false;
      } else {
        addWalkThroughDialog = true;
      }
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
    setState(() {});
  }

  deleteFrontEndDataApiCall(int id) async {
    appStore.setLoading(true);
    await deleteFrontendData(id).then((value) {
      toast(value.message.toString());
      init();
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
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
            appBar: appBarWidget(language.walkThrough),
            body: Stack(
              fit: StackFit.expand,
              children: [
                SingleChildScrollView(
                  //    padding: EdgeInsets.only(top: 16, left: 16, right: 16, bottom: 100),
                  padding: EdgeInsets.all(16),
                  child: staticWidget(),
                ),
                appStore.isLoading ? loaderWidget() : SizedBox(),
                Positioned(
                  bottom: 16,
                  right: 16,
                  child: FloatingActionButton(
                    backgroundColor: primaryColor,
                    child: Icon(Icons.add, color: Colors.white),
                    onPressed: () {
                      if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                        toast(language.demo_admin_msg);
                      } else {
                        showInDialog(
                          context: context,
                          child: AddWalkThroughDialog(
                            isAdd: true,
                            onUpdate: () {
                              frontEndDataListApiCall();
                              setState(() {});
                            },
                          ),
                        );
                      }
                    },
                  ),
                ).visible(addWalkThroughDialog),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget staticWidget() {
    return Column(
      children: [
        ListView.builder(
            physics: NeverScrollableScrollPhysics(),
            controller: ScrollController(),
            padding: EdgeInsets.only(top: 16, bottom: 16),
            shrinkWrap: true,
            itemCount: walkThroughDataList.length,
            itemBuilder: (context, index) {
              Walkthrough mData = walkThroughDataList[index];
              return Stack(
                alignment: Alignment.topRight,
                children: [
                  Container(
                    padding: EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      border: Border.all(
                        color: Colors.grey.withOpacity(0.3),
                      ),
                      borderRadius: BorderRadius.circular(
                        defaultRadius,
                      ),
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            ClipRRect(borderRadius: radius(defaultRadius), child: commonCachedNetworkImage(mData.image, height: 80, width: 80, fit: BoxFit.cover, alignment: Alignment.center)),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  "${mData.title.toString()}",
                                  style: boldTextStyle(size: 14),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ).paddingOnly(top: 16),
                                4.height,
                                Text(
                                  "${mData.description.toString()}",
                                  style: secondaryTextStyle(),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ).paddingAll(8).expand(),
                          ],
                        ),
                      ],
                    ),
                  ).paddingBottom(
                    10,
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      outlineActionIcon(
                        context,
                        Icons.edit,
                        Colors.green,
                        () {
                          showInDialog(
                            context: context,
                            child: AddWalkThroughDialog(
                              data: mData,
                              isAdd: false,
                              onUpdate: () {
                                frontEndDataListApiCall();
                              },
                            ),
                          );
                        },
                      ).paddingOnly(top: 10, right: 8),
                      outlineActionIcon(
                        context,
                        Icons.delete,
                        Colors.red,
                        () async {
                          await commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () async {
                            await deleteFrontEndDataApiCall(mData.id!);
                            finish(context);
                          }, title: language.deleteReview, subtitle: language.deleteReviewMsg);
                        },
                      ).paddingOnly(top: 10, right: 8, left: appStore.isDarkMode ? 8 : 0),
                    ],
                  ),
                ],
              );
            }),
        16.height,
      ],
    );
  }
}
