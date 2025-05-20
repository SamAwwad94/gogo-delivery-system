import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../network/RestApis.dart';
import '../../utils/Colors.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../../utils/Extensions/shared_pref.dart';
import '../components/AddClientReviewDialog.dart';
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../models/GetFrontendDataResponseModel.dart';
import '../utils/Extensions/app_common.dart';

class WebsiteClientReviewScreen extends StatefulWidget {
  static String route = '/websiteclientreview';

  const WebsiteClientReviewScreen({super.key});

  @override
  State<WebsiteClientReviewScreen> createState() => _WebsiteClientReviewScreenState();
}

class _WebsiteClientReviewScreenState extends State<WebsiteClientReviewScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController clientReviewTitleController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController reviewController = TextEditingController();

  final ImagePicker _picker = ImagePicker();
  Uint8List? whyChooseFirstReviewImage;
  String? firstReviewImageName;
  String? firstReviewImagePath;
  GetFrontendDataResponseModel? frontEndData;
  List<ClientReviewData> clientReviewDataList = [];

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    appStore.setLoading(true);
    frontEndDataListApiCall();
  }

  frontEndDataListApiCall() async {
    await getFrontendDataList().then((value) {
      frontEndData = value;
      clientReviewDataList = frontEndData!.clientReview!.data!;
      clientReviewTitleController.text = frontEndData!.clientReview!.clientReviewTitle.validate();
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      log("$error");
      setState(() {});
      appStore.setLoading(false);
    });
  }

  pickImage1() async {
    XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      firstReviewImagePath = image.path;
      whyChooseFirstReviewImage = await image.readAsBytes();
      firstReviewImageName = image.name;
    }
    setState(() {});
  }

  Future<void> saveWhyDeliverySettings() async {
    appStore.setLoading(true);
    if (formKey.currentState!.validate()) {
      List req = [
        {
          "type": "client_review",
          "key": "client_review_title",
          "value": clientReviewTitleController.text,
        },
      ];
      await setInvoiceSetting(jsonEncode(req)).then((value) {
        appStore.setLoading(false);

        toast(value.message);
      }).catchError((error) {
        appStore.setLoading(false);
        log(error);
      });
    }
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

  Widget saveSettingButton() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        InkWell(
          child: Container(
            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(color: primaryColor, borderRadius: BorderRadius.circular(defaultRadius)),
            child: Text(language.save, style: boldTextStyle(color: Colors.white)),
          ),
          onTap: () {
            if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
              toast(language.demo_admin_msg);
            } else {
              if (formKey.currentState!.validate()) {
                saveWhyDeliverySettings();
              }
            }
          },
        ),
      ],
    );
  }

  Widget addReviewButton() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        InkWell(
          child: Container(
            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(color: primaryColor, borderRadius: BorderRadius.circular(defaultRadius)),
            child: Text(language.addReview, style: boldTextStyle(color: Colors.white)),
          ),
          onTap: () {
            if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
              toast(language.demo_admin_msg);
            } else {
              showInDialog(
                  context: context,
                  child: AddClientReviewDialog(
                    isAdd: true,
                    onUpdate: () {
                      frontEndDataListApiCall();
                      setState(() {});
                    },
                  ));
            }
          },
        ),
      ],
    );
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
            appBar: appBarWidget(language.clientReview),
            body: Stack(
              fit: StackFit.expand,
              children: [
                SingleChildScrollView(
                  //    padding: EdgeInsets.only(top: 16, left: 16, right: 16, bottom: 100),
                  padding: EdgeInsets.all(16),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      10.height,
                      Form(
                        key: formKey,
                        child: Container(
                          decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              10.height,
                              RequiredValidation(required: true, titleText: language.title),
                              10.height,
                              AppTextField(
                                controller: clientReviewTitleController,
                                textFieldType: TextFieldType.OTHER,
                                decoration: commonInputDecoration(),
                                textInputAction: TextInputAction.next,
                                validator: (s) {
                                  if (s!.trim().isEmpty) return language.field_required_msg;
                                  return null;
                                },
                              ),
                              10.height,
                              saveSettingButton(),
                            ],
                          ).paddingAll(10),
                        ),
                      ),
                      16.height,
                      if (clientReviewDataList.isNotEmpty) Text(language.reviews, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)),
                      if (clientReviewDataList.isNotEmpty) staticWidget(),
                      16.height,
                    ],
                  ),
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
                            child: AddClientReviewDialog(
                              isAdd: true,
                              onUpdate: () {
                                frontEndDataListApiCall();
                                setState(() {});
                              },
                            ));
                      }
                    },
                  ),
                ),
              ],
            ),
          ),
        );
      },
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

  Widget staticWidget() {
    return Column(
      children: [
        ListView.builder(
            physics: NeverScrollableScrollPhysics(),
            controller: ScrollController(),
            padding: EdgeInsets.only(top: 16, bottom: 16),
            shrinkWrap: true,
            itemCount: clientReviewDataList.length,
            itemBuilder: (context, index) {
              ClientReviewData mData = clientReviewDataList[index];
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
                                  "${mData.name.toString()}",
                                  style: boldTextStyle(size: 14),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                4.height,
                                Text(
                                  "${mData.email.toString()}",
                                  style: boldTextStyle(
                                    size: 14,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ).paddingAll(8).expand(),
                          ],
                        ),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              mData.review.toString(),
                              style: secondaryTextStyle(),
                              maxLines: 7,
                              overflow: TextOverflow.ellipsis,
                            ).expand(),
                          ],
                        ).paddingAll(4),
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
                            child: AddClientReviewDialog(
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
                      ).paddingOnly(top: 10, right: 8, left: appStore.selectedLanguage=="ar" ? 8 : 0),
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
