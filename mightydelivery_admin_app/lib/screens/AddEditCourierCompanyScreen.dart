import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../utils/Common.dart';
import '../../utils/Constants.dart';
import '../../utils/Images.dart';
import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../extensions/widgets.dart';
import '../models/CourierCompaniesListModel.dart';
import '../models/LDBaseResponse.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Extensions/app_common.dart';

class AddEditCourierCompanyScreen extends StatefulWidget {
  final CourierCompany? data;
  final Function() onUpdate;

  AddEditCourierCompanyScreen({this.data, required this.onUpdate});

  @override
  State<AddEditCourierCompanyScreen> createState() => _AddEditCourierCompanyScreenState();
}

class _AddEditCourierCompanyScreenState extends State<AddEditCourierCompanyScreen> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String? imageName;
  String? imagePath;
  final ImagePicker _picker = ImagePicker();
  TextEditingController nameController = TextEditingController();
  TextEditingController linkController = TextEditingController();
  Uint8List? imageUint8List;
  XFile? image;

  @override
  void initState() {
    super.initState();
    init();
  }

  init() {
    if (widget.data != null) {
      nameController.text = widget.data!.name.validate();
      linkController.text = widget.data!.link.validate();
      imagePath = widget.data!.image.validate();
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

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  addUpdateCourierCompanyApi() async {
    MultipartRequest multiPartRequest;
    if (widget.data != null) {
      multiPartRequest = await getMultiPartRequest('couriercompanies-update/${widget.data!.id.validate()}');
    } else {
      multiPartRequest = await getMultiPartRequest('couriercompanies-save');
    }
    multiPartRequest.fields["name"] = nameController.text.validate();
    multiPartRequest.fields["link"] = linkController.text.validate();
    if (imageUint8List != null) multiPartRequest.files.add(MultipartFile.fromBytes('couriercompanies_image', imageUint8List!, filename: imageName));
    multiPartRequest.headers.addAll(buildHeaderTokens());
    appStore.setLoading(true);
    sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
        LDBaseResponse res = LDBaseResponse.fromJson(data);
        toast(res.message);
        Navigator.pop(context);
        widget.onUpdate.call();
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(
        widget.data != null ? language.updateCourierCompany : language.addCourierCompany,
      ),
      body: Form(
        key: _formKey,
        child: Container(
          width: context.width(),
          child: Stack(
            children: [
              SingleChildScrollView(
                padding: EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    8.height,
                    RequiredValidation(required: true, titleText: language.name),
                    8.height,
                    AppTextField(
                      controller: nameController,
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
                    RequiredValidation(required: true, titleText: language.link),
                    8.width,

                    8.height,
                    AppTextField(
                      controller: linkController,
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
                  ],
                ),
              ),
              Positioned.fill(
                child: Observer(builder: (context) {
                  return loaderWidget().visible(appStore.isLoading);
                }),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: Padding(
        padding: EdgeInsets.all(16),
        child: dialogPrimaryButton(widget.data != null ? language.update : language.save, () async {
          if (_formKey.currentState!.validate()) {
            addUpdateCourierCompanyApi();
          }
        }),
      ),
    );
  }
}
