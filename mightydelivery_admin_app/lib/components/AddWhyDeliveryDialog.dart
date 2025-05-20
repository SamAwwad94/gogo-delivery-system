import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../models/GetFrontendDataResponseModel.dart';
import '../../network/RestApis.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../../utils/Images.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../utils/Colors.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';
import 'RequiredValidation.dart';

class AddWhyDeliveryDialog extends StatefulWidget {
  static String tag = '/WhyDeliveryDialog';
  final bool? isAdd;
  final Function()? onUpdate;
  final WhyChooseData? data;
  AddWhyDeliveryDialog({super.key, this.isAdd = true, this.onUpdate, this.data});

  @override
  State<AddWhyDeliveryDialog> createState() => _AddWhyDeliveryDialogState();
}

class _AddWhyDeliveryDialogState extends State<AddWhyDeliveryDialog> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String? imageName;
  String? imagePath;
  final ImagePicker _picker = ImagePicker();
  TextEditingController titleController = TextEditingController();
  TextEditingController subTitleController = TextEditingController();
  Uint8List? imageUint8List;
  XFile? image;
  @override
  void initState() {
    super.initState();
    init();
  }

  init() {
    if (!widget.isAdd!) {
      titleController.text = widget.data!.title!;
      subTitleController.text = widget.data!.subtitle!;
      imagePath = widget.data!.image;
    }
  }

  settingsApiCall() async {
    //if (_formKey.currentState!.validate()) {
    appStore.setLoading(true);
    setState(() {});
    await frontendDataSave(
      id: widget.isAdd! ? "null " : widget.data!.id.toString(),
      title: titleController.text,
      subtitle: subTitleController.text,
      //  description: reviewController.text,
      frontEndImage: imageUint8List,
      frontEndImageName: imageName,
      type: WHY_CHOOSE,
    ).then((value) {
      appStore.setLoading(false);

      toast(language.dataSavedMsg);
      widget.onUpdate!.call();
      finish(context);
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
    //   }
  }

  pickImage() async {
    image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      imagePath = image!.path;
      imageUint8List = await image!.readAsBytes();
      imageName = image!.name;
    }
    setState(() {});
  }

  // Widget saveSettingButton() {
  //   return Row(
  //     mainAxisAlignment: MainAxisAlignment.end,
  //     children: [
  //       InkWell(
  //         child: Container(
  //           padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
  //           decoration: BoxDecoration(color: primaryColor, borderRadius: BorderRadius.circular(defaultRadius)),
  //           child: Text(language.save, style: boldTextStyle(color: Colors.white)),
  //         ),
  //         onTap: () {
  //           if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
  //             toast(language.demo_admin_msg);
  //           } else {
  //             settingsApiCall();
  //           }
  //         },
  //       ),
  //     ],
  //   );
  // }

  Widget imageFunction() {
    if (image != null) {
      return Image.file(File(image!.path), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center);
    } else {
      if (!imagePath.isEmptyOrNull) {
        return commonCachedNetworkImage(imagePath, fit: BoxFit.cover, height: 100, width: 100);
      } else {
        return Image.asset(ic_delivery_app_logo_color, color: Colors.transparent, height: 90, width: 90);
      }
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      child: Form(
        key: _formKey,
        child: Container(
          width: context.width(),
          //    color: context.scaffoldBackgroundColor,
          child: Stack(
            children: [
              SingleChildScrollView(
                padding: EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    8.height,
                    Text('${widget.isAdd! ? language.add : language.edit} ${language.whyChooseData}', style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor, size: 18)),
                    16.height,
                    Row(
                      children: [
                        RequiredValidation(required: true, titleText: language.title),
                        8.width,
                        Text(
                          language.max100character,
                          style: secondaryTextStyle(color: redColor, size: 12),
                        ),
                      ],
                    ),

                    //  staticWidget(),
                    8.height,
                    AppTextField(
                      controller: titleController,
                      textFieldType: TextFieldType.OTHER,
                      decoration: commonInputDecoration(),
                      textInputAction: TextInputAction.next,
                      isValidationRequired: true,
                      errorThisFieldRequired: language.field_required_msg,
                      inputFormatters: [LengthLimitingTextInputFormatter(100)],
                      validator: (s) {
                        if (s!.trim().isEmpty) return language.field_required_msg;
                        return null;
                      },
                    ),
                    16.height,
                    Row(
                      children: [
                        RequiredValidation(required: true, titleText: language.subTitle),
                        8.width,
                        Text(
                          language.max100character,
                          style: secondaryTextStyle(color: redColor, size: 12),
                        ),
                      ],
                    ),
                    8.width,

                    8.height,
                    AppTextField(
                      controller: subTitleController,
                      textFieldType: TextFieldType.OTHER,
                      decoration: commonInputDecoration(),
                      textInputAction: TextInputAction.next,
                      isValidationRequired: true,
                      errorThisFieldRequired: language.field_required_msg,
                      inputFormatters: [LengthLimitingTextInputFormatter(100)],
                      validator: (s) {
                        if (s!.trim().isEmpty) return language.field_required_msg;
                        return null;
                      },
                    ),
                    16.height,
                    Text(language.image, style: primaryTextStyle()),
                    8.width,
                    Row(
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
                            pickImage();
                          },
                        ),
                        10.width,
                        ClipRRect(borderRadius: radius(defaultRadius), child: imageFunction()).paddingLeft(20),
                      ],
                    ),
                    16.height,
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        dialogSecondaryButton(language.cancel, () {
                          Navigator.pop(context);
                        }),
                        16.width,
                        dialogPrimaryButton(language.save, () {
                          if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                            toast(language.demo_admin_msg);
                          } else {
                            if (_formKey.currentState!.validate()) {
                              if (widget.isAdd! && imageUint8List == null) {
                                toast(language.imgSelectValidation);
                              } else if (imageUint8List == null && widget.data!.image.isEmptyOrNull) {
                                toast(language.imgSelectValidation);
                              } else {
                                settingsApiCall();
                              }
                            }
                          }
                        }),
                      ],
                    ),
                  ],
                ),
              ),
              Visibility(
                visible: appStore.isLoading,
                child: Positioned.fill(
                  child: Observer(builder: (context) {
                    return loaderWidget();
                  }),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
