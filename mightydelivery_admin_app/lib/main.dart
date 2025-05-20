import 'dart:io';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../services/ChatMessgaeService.dart';
import '../services/notificationService.dart';
import '../utils/Extensions/shared_pref.dart';
import '../utils/firebase_options.dart';
import 'package:onesignal_flutter/onesignal_flutter.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../AppTheme.dart';
import '../language/BaseLanguage.dart';
import '../models/LanguageDataModel.dart';
import '../screens/SplashScreen.dart';
import '../store/AppStore.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/DataProvider.dart';
import '../utils/Extensions/StringExtensions.dart';
import 'language/AppLocalizations.dart';
import 'models/FileModel.dart';
import 'models/models.dart';
import 'services/AuthSertvices.dart';
import 'services/UserServices.dart';

AppStore appStore = AppStore();
AuthServices authService = AuthServices();
UserService userService = UserService();

Color textPrimaryColorGlobal = textPrimaryColor;
Color textSecondaryColorGlobal = textSecondaryColor;
//Color defaultLoaderBgColorGlobal = Colors.white;
Color? defaultLoaderAccentColorGlobal;

late SharedPreferences sharedPref;
late BaseLanguage language;

final navigatorKey = GlobalKey<NavigatorState>();

get getContext => navigatorKey.currentState?.overlay?.context;

late List<FileModel> fileList = [];

ChatMessageService chatMessageService = ChatMessageService();
NotificationService notificationService = NotificationService();
List<LanguageDataModel> localeLanguageList = [];
LanguageDataModel? selectedLanguageDataModel;
bool mIsEnterKey = false;
String mSelectedImage = "assets/default_wallpaper.png";

Future<void> initialize({
  double? defaultDialogBorderRadius,
  List<LanguageDataModel>? aLocaleLanguageList,
  String? defaultLanguage,
}) async {
  localeLanguageList = aLocaleLanguageList ?? [];
  selectedLanguageDataModel = getSelectedLanguageModel(defaultLanguage: default_Language);
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  sharedPref = await SharedPreferences.getInstance();

  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );

  await initialize(aLocaleLanguageList: languageList());
  appStore.setLanguage(default_Language);

  appStore.setLoggedIn(sharedPref.getBool(IS_LOGGED_IN) ?? false, isInitializing: true);
  appStore.setUserProfile(sharedPref.getString(USER_PROFILE_PHOTO).validate(), isInitializing: true);
  FilterAttributeModel? filterData = FilterAttributeModel.fromJson(getJSONAsync(FILTER_DATA));
  appStore.setFiltering(filterData.orderStatus != null || !filterData.fromDate.isEmptyOrNull || !filterData.toDate.isEmptyOrNull);

  int themeModeIndex = sharedPref.getInt(THEME_MODE_INDEX) ?? AppThemeMode().themeModeLight;
  if (themeModeIndex == AppThemeMode().themeModeDark) {
    appStore.setDarkMode(true);
  } else if (themeModeIndex == AppThemeMode().themeModeLight) {
    appStore.setDarkMode(false);
  }

  await Firebase.initializeApp();
  FlutterError.onError = FirebaseCrashlytics.instance.recordFlutterError;
  oneSignalData();
  // saveOneSignalPlayerId();

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return Observer(builder: (context) {
      return MaterialApp(
          builder: (context, child) {
            return ScrollConfiguration(
              behavior: (Platform.isAndroid || Platform.isIOS)
                  ? MyBehavior()
                  : ScrollConfiguration.of(context).copyWith(
                      dragDevices: {
                        PointerDeviceKind.mouse,
                        PointerDeviceKind.touch,
                      },
                    ),
              child: child!,
            );
          },
          debugShowCheckedModeBanner: false,
          title: language.appName,
          theme: AppTheme.lightTheme,
          darkTheme: AppTheme.darkTheme,
          themeMode: appStore.isDarkMode ? ThemeMode.dark : ThemeMode.light,
          home: SplashScreen(),
          supportedLocales: LanguageDataModel.languageLocales(),
          localizationsDelegates: [
            AppLocalizations(),
            GlobalMaterialLocalizations.delegate,
            GlobalWidgetsLocalizations.delegate,
            GlobalCupertinoLocalizations.delegate,
            // MonthYearPickerLocalizations.delegate,
          ],
          localeResolutionCallback: (locale, supportedLocales) => locale,
          locale: Locale(appStore.selectedLanguage.validate(value: default_Language)));
    });
  }
}

void oneSignalData() async {
  OneSignal.Debug.setLogLevel(OSLogLevel.verbose);
  OneSignal.Debug.setAlertLevel(OSLogLevel.none);
  OneSignal.consentRequired(false);

  OneSignal.initialize(mOneSignalAppIdAdmin);

  OneSignal.Notifications.requestPermission(true);

  OneSignal.Notifications.addForegroundWillDisplayListener((event) {
    event.preventDefault();
    event.notification.display();
  });

  saveOneSignalPlayerId();
  // await setValue(PLAYER_ID, OneSignal.User.pushSubscription.id);
}

class MyBehavior extends ScrollBehavior {
  @override
  Widget buildOverscrollIndicator(BuildContext context, Widget child, ScrollableDetails details) {
    return child;
  }
}
