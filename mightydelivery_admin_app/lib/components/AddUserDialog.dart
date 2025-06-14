import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/colors.dart';

import '../main.dart';
import '../models/UserModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';

class AddUserDialog extends StatefulWidget {
  static String tag = '/AddUserDialog';
  final UserModel? userData;
  final String? userType;
  final Function()? onUpdate;

  AddUserDialog({this.userData, this.userType, this.onUpdate});

  @override
  AddUserDialogState createState() => AddUserDialogState();
}

class AddUserDialogState extends State<AddUserDialog> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String countryCode = defaultPhoneCode;

  TextEditingController emailController = TextEditingController();
  TextEditingController usernameController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  TextEditingController contactNumberController = TextEditingController();
  TextEditingController passwordController = TextEditingController();

  FocusNode emailFocus = FocusNode();
  FocusNode usernameFocus = FocusNode();
  FocusNode nameFocus = FocusNode();
  FocusNode contactFocus = FocusNode();
  FocusNode passwordFocus = FocusNode();

  bool isUpdate = false;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    isUpdate = widget.userData != null;
    if (isUpdate) {
      String phoneNum = widget.userData!.contactNumber.validate();
      emailController.text = widget.userData!.email.validate();
      usernameController.text = widget.userData!.username.validate();
      nameController.text = widget.userData!.name.validate();
      if (phoneNum.split(" ").length == 1) {
        contactNumberController.text = phoneNum.split(" ").last;
      } else {
        countryCode = phoneNum.split(" ").first;
        contactNumberController.text = phoneNum.split(" ").last;
      }
    }
  }

  Future<void> save() async {
    Navigator.pop(context);
    appStore.setLoading(true);
    if (isUpdate) {
      await updateProfile(
        id: widget.userData!.id,
        name: nameController.text,
        userName: usernameController.text,
        userEmail: emailController.text,
        contactNumber: '$countryCode ${contactNumberController.text.trim()}',
      ).then((value) {
        appStore.setLoading(false);
        widget.onUpdate?.call();
        toast(language.userUpdated);
      }).catchError((error) {
        log(error);
        appStore.setLoading(false);
      });
    } else {
      var request = {
        "name": nameController.text,
        "username": usernameController.text,
        "user_type": widget.userType,
        "contact_number": '$countryCode ${contactNumberController.text.trim()}',
        "email": emailController.text.trim(),
        "password": passwordController.text.trim(),
        // "player_id": getStringAsync(USER_PLAYER_ID).validate(),
      };
      await signUpApi(request).then((res) async {
        log('Step3-------');

        authService
            .signUpWithEmailPassword(getContext,
                lName: res.data!.name,
                userName: res.data!.username,
                name: res.data!.name,
                email: res.data!.email,
                password: passwordController.text.trim(),
                mobileNumber: res.data!.contactNumber,
                userType: res.data!.userType,
                userData: res,
                isAddUser: true)
            .then((res) async {
          setState(() {});
          appStore.setLoading(false);
          widget.onUpdate?.call();
          toast(language.userAdded);
        }).catchError((e) {
          appStore.setLoading(false);
          log(e.toString());
          toast(e.toString());
        });
      }).catchError((e) {
        appStore.setLoading(false);
        toast(e.toString());
        log(e.toString());
        return;
      });
    }
  }

  String getTitle() {
    if (widget.userType == CLIENT) {
      return isUpdate ? language.editUser : language.addUser;
    } else {
      return isUpdate ? language.editDeliveryPerson : language.addDeliveryPerson;
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      backgroundColor: appStore.isDarkMode ? textPrimaryColor : white,
      contentPadding: EdgeInsets.all(16),
      insetPadding: EdgeInsets.only(top: 50, right: 16, left: 16, bottom: 16),
      titlePadding: EdgeInsets.only(left: 16, right: 8, top: 8),
      title: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(getTitle(), style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor, size: 20)),
          IconButton(
            icon: Icon(Icons.close),
            padding: EdgeInsets.zero,
            onPressed: () {
              Navigator.pop(context);
            },
          ),
        ],
      ),
      content: SizedBox(
        width: MediaQuery.of(context).size.width,
        child: Stack(
          children: [
            SingleChildScrollView(
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(language.email, style: primaryTextStyle()),
                        SizedBox(height: 8),
                        AppTextField(
                          readOnly: isUpdate,
                          controller: emailController,
                          textFieldType: TextFieldType.EMAIL,
                          focus: emailFocus,
                          nextFocus: usernameFocus,
                          decoration: commonInputDecoration(),
                          onTap: () {
                            if (isUpdate) toast(language.youCannotChangeEmailId);
                          },
                        ),
                      ],
                    ),
                    SizedBox(height: 16),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(language.username, style: primaryTextStyle()),
                        SizedBox(height: 8),
                        AppTextField(
                          readOnly: isUpdate,
                          controller: usernameController,
                          textFieldType: TextFieldType.USERNAME,
                          focus: usernameFocus,
                          nextFocus: nameFocus,
                          decoration: commonInputDecoration(),
                          onTap: () {
                            if (isUpdate) toast(language.youCannotChangeUsername);
                          },
                        ),
                      ],
                    ),
                    SizedBox(height: 16),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(language.name, style: primaryTextStyle()),
                        SizedBox(height: 8),
                        AppTextField(
                          controller: nameController,
                          textFieldType: TextFieldType.NAME,
                          focus: nameFocus,
                          nextFocus: isUpdate ? contactFocus : passwordFocus,
                          decoration: commonInputDecoration(),
                          errorThisFieldRequired: language.fieldRequiredMsg,
                        ),
                      ],
                    ),
                    if (!isUpdate) SizedBox(height: 16),
                    if (!isUpdate)
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(language.password, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: passwordController,
                            textFieldType: TextFieldType.PASSWORD,
                            focus: passwordFocus,
                            nextFocus: contactFocus,
                            decoration: commonInputDecoration(),
                          ),
                        ],
                      ),
                    SizedBox(height: 16),
                    Text(language.contactNumber, style: primaryTextStyle()),
                    SizedBox(height: 8),
                    AppTextField(
                      controller: contactNumberController,
                      textFieldType: TextFieldType.PHONE,
                      focus: contactFocus,
                      readOnly: isUpdate,
                      decoration: commonInputDecoration(
                        prefixIcon: IntrinsicHeight(
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              CountryCodePicker(
                                initialSelection: countryCode,
                                showCountryOnly: false,
                                enabled: !isUpdate,
                                showFlag: true,
                                showFlagDialog: true,
                                showOnlyCountryWhenClosed: false,
                                alignLeft: false,
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
                        // if (s.trim().length < minContactLength || s.trim().length > maxContactLength) return language.contactLengthValidation;
                        return null;
                      },
                      inputFormatters: [
                        FilteringTextInputFormatter.digitsOnly,
                      ],
                      onTap: () {
                        if (isUpdate) {
                          toast(language.youCannotChangePhoneNumber);
                        }
                      },
                    ),
                    SizedBox(height: 30),
                    Row(
                      children: [
                        Expanded(
                          child: dialogSecondaryButton(language.cancel, () {
                            Navigator.pop(context);
                          }),
                        ),
                        SizedBox(width: 16),
                        Expanded(
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
                      ],
                    ),
                  ],
                ),
              ),
            ),
            Observer(builder: (_) => Visibility(visible: appStore.isLoading, child: Positioned.fill(child: loaderWidget()))),
          ],
        ),
      ),
    );
  }
}
