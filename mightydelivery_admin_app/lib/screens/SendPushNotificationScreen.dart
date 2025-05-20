import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Colors.dart';
import '../utils/Extensions/app_common.dart';
import 'package:multi_dropdown/multi_dropdown.dart';
import 'package:flutter/services.dart';
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/UserModel.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';




class SendPushNotificationScreen extends StatefulWidget {
  const SendPushNotificationScreen({super.key});

  @override
  State<SendPushNotificationScreen> createState() => _SendPushNotificationScreenState();
}

class _SendPushNotificationScreenState extends State<SendPushNotificationScreen> {
  final userController = MultiSelectController<UserModel>();
  final deliverymanController = MultiSelectController<UserModel>();
  bool isAllUser = false;
  bool isAllDeliveryMan = false;
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController titleController = TextEditingController();
  TextEditingController messageController = TextEditingController();

  String? imageName;
  String? imagePath;
  Uint8List? imageUnit8List;
  XFile? image;
  final ImagePicker _picker = ImagePicker();



  List<DropdownItem<UserModel>> userList = [];
  List<DropdownItem<UserModel>> deliveryManList = [];

  getUserApiCall({String? type}) async {
    appStore.setLoading(true);
    await getAllUserList(type: type, status: 1).then((value) {
      appStore.setLoading(false);
      List<DropdownItem<UserModel>> list = value.data!.map((user) {
        return DropdownItem<UserModel>(
          label: user.name.validate(),
          value: UserModel(id: user.id.validate(), name: user.name.validate()),
        );
      }).toList();
      if (type == CLIENT) {
        userList = list;
      } else {
        deliveryManList = list;
      }
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    await getUserApiCall(type: CLIENT);
    await getUserApiCall(type: DELIVERYMAN);
    print("User list: ${userList.map((e) => e.label).toList()}");
    print("Delivery man list: ${deliveryManList.map((e) => e.label).toList()}");
  }

  sendPushNotification() async {
    List clientList = [];
    List deliverymanList = [];
    userController.selectedItems.forEach((e) {
      clientList.add(e.label.validate());
    });
    deliverymanController.selectedItems.forEach((e) {
      deliverymanList.add(e.label.validate());
    });
    MultipartRequest multiPartRequest = await getMultiPartRequest('pushnotification-save');
    multiPartRequest.fields["title"] = titleController.text.validate();
    multiPartRequest.fields["client"] = jsonEncode(clientList);
    multiPartRequest.fields["delivery_man"] = jsonEncode(deliverymanList);
    multiPartRequest.fields["message"] = messageController.text.validate();
    if(imageUnit8List != null) multiPartRequest.files.add(MultipartFile.fromBytes('notification_image', imageUnit8List!, filename: imageName));
    multiPartRequest.headers.addAll(buildHeaderTokens());
    appStore.setLoading(true);
    sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
      },
      onError: (error) {
        print("error ${error.toString()}");
        toast(error.toString());
        appStore.setLoading(false);
      },
    ).catchError((e) {
      appStore.setLoading(false);
      toast(e.toString());
    });
  }

  Widget pushNotificationWidget() {
    return Column(
      children: [
        Container(
          decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius)),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              RequiredValidation(required: true, titleText: language.title),
              8.height,
              AppTextField(
                controller: titleController,
                textFieldType: TextFieldType.OTHER,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                maxLines: 1,
                minLines: 1,
                validator: (s) {
                  if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
              RequiredValidation(required: true, titleText: language.message),
              16.height,
              AppTextField(
                controller: messageController,
                textFieldType: TextFieldType.OTHER,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                maxLines: 4,
                minLines: 4,
                validator: (s) {
                  if (s!.trim().isEmpty) return language.field_required_msg;
                  return null;
                },
              ),
              16.height,
              Text(language.image, style: primaryTextStyle()),
              16.height,
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  TextButton(
                    style: TextButton.styleFrom(
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(defaultRadius)),
                      elevation: 0,
                      padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                      backgroundColor: Colors.grey.withOpacity(0.2),
                    ),
                    child: Text(language.select_file, style: boldTextStyle(color: Colors.grey)),
                    onPressed: () {
                      picknotificationImage();
                    },
                  ),
                  8.width,
                  ClipRRect(borderRadius: radius(defaultRadius), child: notificationImageFunction()),
                ],
              ),
              16.height,
            ],
          ),
        ),
        16.height,
      ],
    );
  }

  Widget notificationImageFunction() {
    if (imagePath != null) {
      return Image.file(File(imagePath!), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (appStore.aboutUsAppScreenShotImage != null && appStore.aboutUsAppScreenShotImage.isNotEmpty) {
        return commonCachedNetworkImage(appStore.aboutUsAppScreenShotImage.toString(), fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return commonCachedNetworkImage(appStore.aboutUsAppScreenShotImage.toString(), height: 90, width: 90);
      }
    }
  }

  picknotificationImage() async {
    image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      imagePath = image!.path;
      imageUnit8List = (await image!.readAsBytes()) as Uint8List?;
      imageName = image!.name;
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
        backgroundColor: Colors.white,
        appBar: appBarWidget(language.sendPushNotification),
        body: Stack(
          children: [
            Padding(
              padding: EdgeInsets.all(16),
              child: SingleChildScrollView(
                physics: AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.max,
                  children: [
                    4.height,
                    Row(
                      children: [
                        Text(
                          language.user,
                          style: primaryTextStyle(),
                        ).expand(),
                        Row(
                          children: [
                            Checkbox(
                                value: isAllUser,
                                onChanged: (val) {
                                  isAllUser = val!;
                                  if (isAllUser) {
                                    userController.selectAll();
                                  } else {
                                    userController.clearAll();
                                  }
                                  setState(() {});
                                }),
                            Text(
                              language.selectAll,
                              style: primaryTextStyle(),
                            ),
                          ],
                        ),
                      ],
                    ),
                    5.height,
                    if (userList.isNotEmpty)
                      MultiDropdown<UserModel>(
                        items: userList,
                        controller: userController,
                        enabled: true,
                        searchEnabled: false,
                        chipDecoration: ChipDecoration(
                            backgroundColor: primaryColor,
                            wrap: false,
                            runSpacing: 2,
                            spacing: 10,
                            labelStyle: TextStyle(color: Colors.white),
                            deleteIcon: Icon(
                              Icons.close,
                              size: 16,
                              color: Colors.white,
                            )),
                        fieldDecoration: FieldDecoration(
                          showClearIcon: false,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: primaryColor),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(
                              color: primaryColor,
                            ),
                          ),
                        ),
                        dropdownItemDecoration: DropdownItemDecoration(
                          selectedIcon: Icon(Icons.check_box, color: primaryColor),
                          disabledIcon: Icon(Icons.lock, color: Colors.grey.shade300),
                        ),
                        onSelectionChange: (selectedItems) {
                          debugPrint("OnSelectionChange: $selectedItems");
                        },
                      ),
                    20.height,
                    Row(
                      children: [
                        Text(
                          language.deliveryBoy,
                          style: primaryTextStyle(),
                        ).expand(),
                        Row(
                          children: [
                            Checkbox(
                                value: isAllDeliveryMan,
                                onChanged: (val) {
                                  isAllDeliveryMan = val!;
                                  if (isAllDeliveryMan) {
                                    deliverymanController.selectAll();
                                  } else {
                                    deliverymanController.clearAll();
                                  }
                                  setState(() {});
                                }),
                            Text(
                             language.selectAll,
                              style: primaryTextStyle(),
                            ),
                          ],
                        ),
                      ],
                    ),
                    5.height,
                    if (deliveryManList.isNotEmpty)
                      MultiDropdown<UserModel>(
                        items: deliveryManList,
                        controller: deliverymanController,
                        enabled: true,
                        searchEnabled: false,
                        chipDecoration: ChipDecoration(
                            backgroundColor: primaryColor,
                            wrap: false,
                            runSpacing: 2,
                            spacing: 10,
                            labelStyle: TextStyle(color: Colors.white),
                            deleteIcon: Icon(
                              Icons.close,
                              size: 16,
                              color: Colors.white,
                            )),
                        fieldDecoration: FieldDecoration(
                          showClearIcon: false,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: primaryColor),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(
                              color: primaryColor,
                            ),
                          ),
                        ),
                        dropdownItemDecoration: DropdownItemDecoration(
                          selectedIcon: Icon(Icons.check_box, color: primaryColor),
                          disabledIcon: Icon(Icons.lock, color: Colors.grey.shade300),
                        ),
                        onSelectionChange: (selectedItems) {
                          debugPrint("OnSelectionChange: $selectedItems");
                        },
                      ),
                    Form(
                      key: formKey,
                      child: Column(
                        children: [
                          Container(
                            //    decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius)),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                16.height,
                                pushNotificationWidget(),
                                16.height,
                              ],
                            ),
                          ),
                          16.height,
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
            Observer(builder: (context) {
              return Visibility(visible: appStore.isLoading, child: Positioned.fill(child: loaderWidget()));
            })
          ],
        ),
        bottomNavigationBar: Padding(
          padding: EdgeInsets.all(16),
          child: dialogPrimaryButton(language.save, () async {
            if (formKey.currentState!.validate()) {
              await sendPushNotification();
              resetMenuIndex();
              Navigator.pop(context, true);
            }
          }),
        ),
      ),
    );
  }
}
