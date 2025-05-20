import 'package:flutter/material.dart';
import 'package:mobx/mobx.dart';

import '../language/AppLocalizations.dart';
import '../language/BaseLanguage.dart';
import '../main.dart';
import '../models/CountryListModel.dart';
import '../models/LanguageDataModel.dart';
import '../utils/Colors.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/shared_pref.dart';

part 'AppStore.g.dart';

class AppStore = _AppStore with _$AppStore;

abstract class _AppStore with Store {
  @observable
  bool isLoggedIn = false;

  @observable
  bool isLoading = false;

  @observable
  List<CountryData> countryList = ObservableList<CountryData>();

  @observable
  int allUnreadCount = 0;

  @observable
  bool isDarkMode = false;

  @observable
  String  isInsuranceAllowed = "";
  @observable
  String  insuranceDescription = "";
  @observable
  String  insurancePercentage = "";

  @observable
  AppBarTheme appBarTheme = AppBarTheme();

  @observable
  String selectedLanguage = "en";

  @observable
  int selectedMenuIndex = 0;

  @observable
  int? expandedIndex;

  @observable
  String userProfile = '';

  @observable
  String currencyCode = "INR";

  @observable
  String currencySymbol = "₹";

  @observable
  String currencyPosition = CURRENCY_POSITION_LEFT;

  @observable
  int isShowVehicle = 0;

  @observable
  String invoiceCompanyName = '';

  @observable
  String invoiceContactNumber = '';

  @observable
  String invoiceAddress = '';

  @observable
  int emailVerification = 0;
  @observable
  String invoiceImage = '';
  @observable
  String deliveryManImage = '';
  @observable
  String roadImage = '';
  @observable
  String appLogoImage = '';
  @observable
  String downloadAppLogo = "";
  @observable
  String deliveryPartnerImage = '';
  @observable
  String contactUsAppScreenShotImage = '';
  @observable
  String aboutUsAppScreenShotImage = "";
  @observable
  String distanceUnit = '';
  @observable
  int isVehicleOrder = 0;
  @observable
  bool isFiltering = false;

  @observable
  bool isMenuExpanded = true;
  @observable
  @action
  Future<void> setLoggedIn(bool val, {bool isInitializing = false}) async {
    isLoggedIn = val;
    if (!isInitializing) await sharedPref.setBool(IS_LOGGED_IN, val);
  }

  @action
  Future<void> setUserProfile(String val, {bool isInitializing = false}) async {
    userProfile = val;
    if (!isInitializing) await sharedPref.setString(USER_PROFILE_PHOTO, val);
  }

  @action
  Future<void> setLoading(bool value) async {
    isLoading = value;
  }

  @action
  void setAllUnreadCount(int val) {
    allUnreadCount = val;
  }

  @action
  void setSelectedMenuIndex(int val) {
    selectedMenuIndex = val;
  }

  @action
  Future<void> setCurrencyCode(String val) async {
    currencyCode = val;
  }

  @action
  Future<void> setCurrencySymbol(String val) async {
    currencySymbol = val;
  }

  @action
  Future<void> setCurrencyPosition(String val) async {
    currencyPosition = val;
  }

  @action
  Future<void> setDarkMode(bool aIsDarkMode) async {
    isDarkMode = aIsDarkMode;

    if (isDarkMode) {
      textPrimaryColorGlobal = Colors.white;
      textSecondaryColorGlobal = textSecondaryColor;

      defaultLoaderBgColorGlobal = scaffoldSecondaryDark;
      //shadowColorGlobal = Colors.white12;
    } else {
      textPrimaryColorGlobal = textPrimaryColor;
      textSecondaryColorGlobal = textSecondaryColor;

      defaultLoaderBgColorGlobal = Colors.white;
    }
  }

  @action
  Future<void> setLanguage(String aCode, {BuildContext? context}) async {
    selectedLanguageDataModel = getSelectedLanguageModel(defaultLanguage: defaultLanguage);
    selectedLanguage = getSelectedLanguageModel(defaultLanguage: defaultLanguage)!.languageCode!;

    //shared_pref.setString(SELECTED_LANGUAGE_CODE, aCode);

    if (context != null) language = BaseLanguage.of(context)!;
    language = await AppLocalizations().load(Locale(selectedLanguage));
  }

  @action
  void showVehicleDialog(int val) {
    isShowVehicle = val;
  }

  @action
  void setInvoiceCompanyName(String val) {
    invoiceCompanyName = val;
  }

  @action
  void setInvoiceContactNumber(String val) {
    invoiceContactNumber = val;
  }

  @action
  void setCompanyAddress(String val) {
    invoiceAddress = val;
  }

  @action
  void setEmailVerification(int val) {
    emailVerification = val;
  }

  @action
  Future<void> setInvoiceImage(String val) async {
    invoiceImage = val;
  }

  @action
  void setDeliveryManImage(String val) {
    deliveryManImage = val;
  }

  @action
  void setRoadImage(String val) {
    roadImage = val;
  }

  @action
  void setAppLogoImage(String val) {
    appLogoImage = val;
  }

  @action
  void setDownloadAppLogo(String val) {
    downloadAppLogo = val;
  }

  @action
  void setDeliveryPartnerImage(String val) {
    deliveryPartnerImage = val;
  }

  @action
  void setContactUsAppScreenShotImage(String val) {
    contactUsAppScreenShotImage = val;
  }

  @action
  void setAboutUsScreenShotImage(String val) {
    aboutUsAppScreenShotImage = val;
  }

  @action
  void setDistanceUnit(String val) {
    distanceUnit = val;
  }

  @action
  Future<void> setFiltering(bool val) async {
    isFiltering = val;
  }

  @action
  Future<void> setExpandedIndex(int val, {bool isInitializing = false}) async {
    expandedIndex = val;
    if (!isInitializing) await setValue(EXPANDED_INDEX, val);
  }

  @action
  void setExpandedMenu(bool val) {
    isMenuExpanded = val;
  }
}
