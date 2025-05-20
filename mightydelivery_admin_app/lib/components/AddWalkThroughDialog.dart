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

class AddWalkThroughDialog extends StatefulWidget {
  static String tag = '/WalkThroughDialog';

  final bool? isAdd;
  final Function()? onUpdate;
  final Walkthrough? data;
  AddWalkThroughDialog({super.key, this.isAdd = true, this.onUpdate, this.data});

  @override
  State<AddWalkThroughDialog> createState() => _AddWalkThroughDialogState();
}

class _AddWalkThroughDialogState extends State<AddWalkThroughDialog> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String? imageName;
  String? imagePath;
  final ImagePicker _picker = ImagePicker();
  TextEditingController titleController = TextEditingController();
  TextEditingController descriptionController = TextEditingController();
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
      descriptionController.text = widget.data!.description!;
      imagePath = widget.data!.image;
    }
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

  settingsApiCall() async {
    //if (_formKey.currentState!.validate()) {
    appStore.setLoading(true);
    setState(() {});
    await frontendDataSave(
      id: widget.isAdd! ? "null " : widget.data!.id.toString(),
      title: titleController.text,
      description: descriptionController.text,
      frontEndImage: imageUint8List,
      frontEndImageName: imageName,
      type: WALKTHROUGH,
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
                    Text('${widget.isAdd! ? language.add : language.edit} ${language.walkThroughSection}', style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor, size: 20)),
                    16.height,
                    RequiredValidation(required: true, titleText: language.title),
                    8.width,
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
                    RequiredValidation(required: true, titleText: language.description),
                    8.height,
                    AppTextField(
                      controller: descriptionController,
                      textFieldType: TextFieldType.OTHER,
                      decoration: commonInputDecoration(),
                      textInputAction: TextInputAction.next,
                      isValidationRequired: true,
                      maxLines: 4,
                      minLines: 4,
                      errorThisFieldRequired: language.field_required_msg,
                      validator: (s) {
                        if (s!.trim().isEmpty) return language.field_required_msg;
                        return null;
                      },
                    ),
                    16.height,
                    Text(language.image, style: primaryTextStyle()),
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
                        ).paddingLeft(20),
                        8.width,
                        ClipRRect(borderRadius: radius(defaultRadius), child: imageFunction())
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
