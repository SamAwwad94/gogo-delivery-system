import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../main.dart';
import '../models/models.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Constants.dart';
import '../utils/DataProvider.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/app_common.dart';

class DashboardScreen extends StatefulWidget {
  static String tag = '/AppDashboardScreen';

  @override
  DashboardScreenState createState() => DashboardScreenState();
}

class DashboardScreenState extends State<DashboardScreen> {
  List<MenuItemModel> menuList = getAppDashboardItems();

  int currentIndex = 0;

  @override
  void initState() {
    init();

    super.initState();
  }

  Future<void> init() async {
    var uid = sharedPref.getString(UID);
    print("-->${uid}");
    //sharedPref.setString(UID, userModel.uid.validate());
    await updateProfile(uid: uid, userName: sharedPref.getString(USER_NAME), userEmail: sharedPref.getString(USER_EMAIL));
    await getAppSetting().then((value) {
      appStore.setCurrencyCode(value.currencyCode ?? currencyCodeDefault);
      appStore.setCurrencySymbol(value.currency ?? currencySymbolDefault);
      appStore.setCurrencyPosition(value.currencyPosition ?? CURRENCY_POSITION_LEFT);
      appStore.isShowVehicle = value.isVehicleInOrder ?? 0;
      sharedPref.setString(ORDER_PREFIX, value.prefix.validate());
    }).catchError((error) {
      log(error.toString());
    });
    await getInvoiceSetting().then((value) {
      if (value.invoiceData != null) {
        appStore.setInvoiceCompanyName(value.invoiceData!.firstWhere((element) => element.key == 'company_name').value.validate());
        appStore.setInvoiceContactNumber(value.invoiceData!.firstWhere((element) => element.key == 'company_contact_number').value.validate());
        appStore.setCompanyAddress(value.invoiceData!.firstWhere((element) => element.key == 'company_address').value.validate());
        appStore.setEmailVerification(value.invoiceData!.firstWhere((element) => element.key == 'email_verification').value.validate().toInt());
        appStore.setInvoiceImage(value.invoiceData!.firstWhere((element) => element.key == 'company_logo').value.validate());
      }
    }).catchError((error) {
      log(error.toString());
    });
    LiveStream().on('UpdateLanguage', (p0) {
      menuList.clear();
      menuList = getAppDashboardItems();
      setState(() {});
    });
  }

  String getTitle() {
    String title = language.dashboard;
    if (currentIndex == 0) {
      title = language.dashboard;
    } else if (currentIndex == 1) {
      title = language.allOrder;
    } else if (currentIndex == 2) {
      title = language.users;
    } else if (currentIndex == 3) {
      title = language.deliveryPerson;
    } else if (currentIndex == 4) {
      title = language.setting;
    }
    return title;
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Observer(builder: (context) {
      return Scaffold(
        body: menuList[currentIndex].widget,
        bottomNavigationBar: BottomNavigationBar(
          backgroundColor: appStore.isDarkMode ? scaffoldSecondaryDark : Colors.white,
          currentIndex: currentIndex,
          type: BottomNavigationBarType.fixed,
          showUnselectedLabels: false,
          elevation: 5,
          selectedIconTheme: IconThemeData(size: 18),
          selectedItemColor: Colors.white,
          iconSize: 18,
          unselectedItemColor: Colors.grey.withOpacity(0.6),
          showSelectedLabels: false,
          items: menuList.map((item) {
            return BottomNavigationBarItem(
                icon: Icon(item.icon!),
                activeIcon: Container(padding: EdgeInsets.symmetric(horizontal: 10, vertical: 8), decoration: BoxDecoration(color: primaryColor, borderRadius: radius(12)), child: Icon(item.icon!)),
                label: item.title);
          }).toList(),
          onTap: (index) {
            currentIndex = index;
            setState(() {});
          },
        ),
      );
    });
  }
}
