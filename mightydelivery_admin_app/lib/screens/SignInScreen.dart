import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/colors.dart';

import '../components/ForgotPasswordDialog.dart';
import '../main.dart';
import '../network/RestApis.dart';
import '../services/AuthSertvices.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';
import 'DashboardScreen.dart';

class SignInScreen extends StatefulWidget {
  static String tag = '/SignInScreen';

  @override
  SignInScreenState createState() => SignInScreenState();
}

class SignInScreenState extends State<SignInScreen> {
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  AuthServices authService = AuthServices();

  TextEditingController emailController = TextEditingController(text: 'demo@admin.com');
  TextEditingController passController = TextEditingController(text: '12345678');

  FocusNode emailFocus = FocusNode();
  FocusNode passFocus = FocusNode();

  bool mIsCheck = false;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    if (sharedPref.getString(PLAYER_ID).isEmptyOrNull) {
      saveOneSignalPlayerId();
    }
    mIsCheck = sharedPref.getBool(REMEMBER_ME) ?? false;
    if (mIsCheck) {
      emailController.text = sharedPref.getString(USER_EMAIL) ?? "";
      passController.text = sharedPref.getString(USER_PASSWORD) ?? "";
    }
    setState(() {});
  }

  Future<void> loginApiCall() async {
    if (_formKey.currentState!.validate()) {
      _formKey.currentState!.save();
      appStore.setLoading(true);
      Map req = {
        "email": emailController.text,
        "password": passController.text,
        "player_id": sharedPref.getString(PLAYER_ID).validate(),
      };

      if (mIsCheck) {
        await sharedPref.setBool(REMEMBER_ME, mIsCheck);
        await sharedPref.setString(USER_EMAIL, emailController.text);
        await sharedPref.setString(USER_PASSWORD, passController.text);
      }
      await logInApi(req).then((value1) async {
        sharedPref.getString(UID) == null
            ? authService
                .signUpWithEmailPassword(context,
                    name: value1.data!.name,
                    email: emailController.text,
                    password: passController.text,
                    userName: value1.data!.username,
                    lName: value1.data!.name,
                    mobileNumber: value1.data!.contactNumber,
                    userType: value1.data!.userType,
                    userData: value1)
                .then((value) {
                if (value1.data!.userType != ADMIN && value1.data!.userType != DEMO_ADMIN) {
                  logout(context, isFromLogin: true);

                  appStore.setLoading(false);
                } else {
                  launchScreen(context, DashboardScreen(), isNewTask: true);
                }
              })
            : authService.signInWithEmailPassword(context, email: emailController.text, password: passController.text).then((value2) {
                if (value1.data!.userType != ADMIN && value1.data!.userType != DEMO_ADMIN) {
                  logout(context, isFromLogin: true);
                  appStore.setLoading(false);
                } else {
                  launchScreen(context, DashboardScreen(), isNewTask: true);
                }
              }).catchError((e) {
                appStore.setLoading(false);
                toast(e.toString());
              });
      });
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: appStore.isDarkMode ? scaffoldSecondaryDark : primaryColor,
      body: Stack(
        children: [
          Column(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                height: MediaQuery.of(context).size.height * 0.25,
                child: Container(
                  height: 90,
                  width: 90,
                  alignment: Alignment.center,
                  decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle),
                  child: Image.asset('assets/app_logo_primary.png', height: 70, width: 70),
                ),
              ),
              Expanded(
                child: Container(
                  width: MediaQuery.of(context).size.width,
                  padding: EdgeInsets.only(left: 24, right: 24),
                  decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(30))),
                  child: SingleChildScrollView(
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          SizedBox(height: 30),
                          Text(language.adminSignIn, style: boldTextStyle(size: 24)),
                          SizedBox(height: 8),
                          Text(language.signInYourAccount, style: secondaryTextStyle(size: 16)),
                          SizedBox(height: 30),
                          Text(language.email, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: emailController,
                            textFieldType: TextFieldType.EMAIL,
                            focus: emailFocus,
                            nextFocus: passFocus,
                            decoration: commonInputDecoration(),
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            errorInvalidEmail: language.emailValidation,
                          ),
                          SizedBox(height: 16),
                          Text(language.password, style: primaryTextStyle()),
                          SizedBox(height: 8),
                          AppTextField(
                            controller: passController,
                            textFieldType: TextFieldType.PASSWORD,
                            focus: passFocus,
                            decoration: commonInputDecoration(),
                            errorThisFieldRequired: language.fieldRequiredMsg,
                            errorMinimumPasswordLength: language.passwordValidation,
                          ),
                          CheckboxListTile(
                            contentPadding: EdgeInsets.zero,
                            controlAffinity: ListTileControlAffinity.leading,
                            activeColor: primaryColor,
                            title: Text(language.rememberMe, style: primaryTextStyle()),
                            value: mIsCheck,
                            onChanged: (val) async {
                              mIsCheck = val!;
                              if (!mIsCheck) {
                                sharedPref.remove(REMEMBER_ME);
                              }
                              setState(() {});
                            },
                          ),
                          appCommonButton(
                            language.login,
                            () {
                              loginApiCall();
                            },
                            width: MediaQuery.of(context).size.width,
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
          Observer(builder: (context) => Visibility(visible: appStore.isLoading, child: loaderWidget())),
        ],
      ),
      bottomNavigationBar: Container(
        color: appStore.isDarkMode ? scaffoldColorDark : Colors.white,
        height: 50,
        alignment: Alignment.center,
        child: GestureDetector(
          child: Text(language.forgotPassword, style: primaryTextStyle(color: appStore.isDarkMode ? white : primaryColor)),
          onTap: () {
            showDialog(
              context: context,
              barrierDismissible: false,
              builder: (BuildContext dialogContext) {
                return ForgotPasswordDialog();
              },
            );
          },
        ),
      ),
    );
  }

  signUpUser() {}

  signInUser() {}
}
