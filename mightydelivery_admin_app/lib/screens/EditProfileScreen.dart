import 'dart:io';

import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../utils/Colors.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';

class EditProfileScreen extends StatefulWidget {
  static String tag = '/EditProfileScreen';

  @override
  EditProfileScreenState createState() => EditProfileScreenState();
}

class EditProfileScreenState extends State<EditProfileScreen> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String countryCode = defaultPhoneCode;

  TextEditingController emailController = TextEditingController();
  TextEditingController usernameController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  TextEditingController contactNumberController = TextEditingController();
  TextEditingController addressController = TextEditingController();

  FocusNode emailFocus = FocusNode();
  FocusNode usernameFocus = FocusNode();
  FocusNode nameFocus = FocusNode();
  FocusNode contactFocus = FocusNode();
  FocusNode addressFocus = FocusNode();

  XFile? imageProfile;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    String phoneNum = sharedPref.getString(USER_CONTACT_NUMBER).validate();
    emailController.text = sharedPref.getString(USER_EMAIL).validate();
    usernameController.text = sharedPref.getString(USER_NAME).validate();
    nameController.text = sharedPref.getString(NAME).validate();
    if (phoneNum.split(" ").length == 1) {
      contactNumberController.text = phoneNum.split(" ").last;
    } else {
      countryCode = phoneNum.split(" ").first;
      contactNumberController.text = phoneNum.split(" ").last;
    }
    addressController.text = sharedPref.getString(USER_ADDRESS).validate();
  }

  Widget profileImage() {
    if (imageProfile != null) {
      return ClipRRect(borderRadius: BorderRadius.circular(50), child: Image.file(File(imageProfile!.path), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center));
    } else {
      if (appStore.userProfile.isNotEmpty) {
        return ClipRRect(borderRadius: BorderRadius.circular(50), child: commonCachedNetworkImage(appStore.userProfile, fit: BoxFit.cover, height: 100, width: 100));
      } else {
        return Padding(
          padding: EdgeInsets.only(right: 4, bottom: 4),
          child: ClipRRect(child: commonCachedNetworkImage('assets/profile.png', height: 90, width: 90)),
        );
      }
    }
  }

  Future<void> getImage() async {
    imageProfile = null;
    imageProfile = await ImagePicker().pickImage(source: ImageSource.gallery, imageQuality: 100);
    setState(() {});
  }

  Future<void> save() async {
    appStore.setLoading(true);
    await updateProfile(
      file: imageProfile != null ? File(imageProfile!.path.validate()) : null,
      name: nameController.text.validate(),
      userName: usernameController.text.validate(),
      userEmail: emailController.text.validate(),
      address: addressController.text.validate(),
      contactNumber: '$countryCode ${contactNumberController.text.trim()}',
    ).then((value) {
      Navigator.pop(context, true);
      appStore.setLoading(false);
      toast(language.profileUpdatedSuccessfully);
    }).catchError((error) {
      log(error);
      appStore.setLoading(false);
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(language.editProfile),
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: EdgeInsets.only(left: 16, top: 30, right: 16, bottom: 16),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Stack(
                    children: [
                      Center(child: profileImage()),
                      Align(
                        alignment: Alignment.bottomCenter,
                        child: Container(
                          margin: EdgeInsets.only(top: 60, left: 80),
                          height: 35,
                          width: 35,
                          decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: primaryColor),
                          child: IconButton(
                            onPressed: () {
                              getImage();
                            },
                            icon: Icon(
                              Icons.edit,
                              color: Colors.white,
                              size: 20,
                            ),
                          ),
                        ),
                      )
                    ],
                  ),
                  SizedBox(height: 16),
                  Text(language.email, style: primaryTextStyle()),
                  SizedBox(height: 8),
                  AppTextField(
                    readOnly: true,
                    controller: emailController,
                    textFieldType: TextFieldType.EMAIL,
                    focus: emailFocus,
                    nextFocus: usernameFocus,
                    decoration: commonInputDecoration(),
                    onTap: () {
                      toast(language.youCannotChangeEmailId);
                    },
                  ),
                  SizedBox(height: 16),
                  Text(language.username, style: primaryTextStyle()),
                  SizedBox(height: 8),
                  AppTextField(
                    readOnly: true,
                    controller: usernameController,
                    textFieldType: TextFieldType.USERNAME,
                    focus: usernameFocus,
                    nextFocus: nameFocus,
                    decoration: commonInputDecoration(),
                    onTap: () {
                      toast(language.youCannotChangeUsername);
                    },
                  ),
                  SizedBox(height: 16),
                  Text(language.name, style: primaryTextStyle()),
                  SizedBox(height: 8),
                  AppTextField(
                    controller: nameController,
                    textFieldType: TextFieldType.NAME,
                    focus: nameFocus,
                    nextFocus: addressFocus,
                    decoration: commonInputDecoration(),
                    errorThisFieldRequired: language.fieldRequiredMsg,
                  ),
                  SizedBox(height: 16),
                  Text(language.contactNumber, style: primaryTextStyle()),
                  SizedBox(height: 8),
                  AppTextField(
                    controller: contactNumberController,
                    textFieldType: TextFieldType.PHONE,
                    focus: contactFocus,
                    nextFocus: addressFocus,
                    decoration: commonInputDecoration(
                      prefixIcon: IntrinsicHeight(
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            CountryCodePicker(
                              initialSelection: countryCode,
                              showCountryOnly: false,
                              showFlag: true,
                              showFlagDialog: true,
                              showOnlyCountryWhenClosed: false,
                              alignLeft: false,
                              dialogSize: Size(MediaQuery.of(context).size.width - 60, MediaQuery.of(context).size.height * 0.6),
                              textStyle: primaryTextStyle(),
                              dialogBackgroundColor: Theme.of(context).cardColor,
                              barrierColor: Colors.black12,
                              dialogTextStyle: primaryTextStyle(),
                              searchDecoration: InputDecoration(
                                iconColor: Theme.of(context).dividerColor,
                                enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Theme.of(context).dividerColor)),
                                focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: primaryColor)),
                              ),
                              searchStyle: primaryTextStyle(),
                              onInit: (c) {
                                countryCode = c!.dialCode!;
                              },
                              onChanged: (c) {
                                countryCode = c.dialCode!;
                              },
                            ),
                            VerticalDivider(color: Colors.grey.withOpacity(0.5)),
                          ],
                        ),
                      ),
                    ),
                    validator: (s) {
                      if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                      //if (s.trim().length < minContactLength || s.trim().length > maxContactLength) return language.contactLengthValidation;
                      return null;
                    },
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly,
                    ],
                  ),
                  SizedBox(height: 16),
                  Text(language.address, style: primaryTextStyle()),
                  SizedBox(height: 8),
                  AppTextField(
                    controller: addressController,
                    textFieldType: TextFieldType.OTHER,
                    focus: addressFocus,
                    decoration: commonInputDecoration(),
                  ),
                  SizedBox(height: 16),
                ],
              ),
            ),
          ),
          Observer(builder: (_) => Visibility(visible: appStore.isLoading, child: loaderWidget())),
        ],
      ),
      bottomNavigationBar: Padding(
        padding: EdgeInsets.all(16),
        child: dialogPrimaryButton(language.save, () {
          if (_formKey.currentState!.validate()) {
            if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
              toast(language.demoAdminMsg);
            } else {
              save();
            }
          }
        }),
      ),
    );
  }
}
