import 'package:flutter/material.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';

import '../extensions/decorations.dart';
import '../main.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

enum ThemeModes { SystemDefault, Light, Dark }

class ThemeScreen extends StatefulWidget {
  @override
  _ThemeScreenState createState() => _ThemeScreenState();
}

class _ThemeScreenState extends State<ThemeScreen> {
  int? currentIndex = 0;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    currentIndex = sharedPref.getInt(THEME_MODE_INDEX) ?? AppThemeMode().themeModeLight;
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  String _getName(ThemeModes themeModes) {
    switch (themeModes) {
      case ThemeModes.Light:
        return language.light;
      case ThemeModes.Dark:
        return language.dark;
      case ThemeModes.SystemDefault:
        return language.systemDefault;
    }
  }

  Widget _getIcons(BuildContext context, ThemeModes themeModes) {
    switch (themeModes) {
      case ThemeModes.Light:
        return Icon(Icons.light_mode_outlined);
      case ThemeModes.Dark:
        return Icon(Icons.dark_mode);
      case ThemeModes.SystemDefault:
        return Icon(Icons.light_mode_outlined);
    }
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
        appBar: appBarWidget(language.theme),
        body: ListView(
          children: List.generate(
            ThemeModes.values.length,
            (index) {
              return Container(
                margin: EdgeInsets.all(8),
                padding: EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                decoration: boxDecorationWithRoundedCorners(backgroundColor: Colors.transparent, border: Border.all(color: index == currentIndex ? primaryColor : Colors.grey.withOpacity(0.3))),
                child: Row(
                  children: [
                    _getIcons(context, ThemeModes.values[index]),
                    16.width,
                    Text('${_getName(ThemeModes.values[index])}', style: boldTextStyle()).expand(),
                    if (index == currentIndex) Icon(Icons.check_circle, color: primaryColor),
                  ],
                ),
              ).onTap(() async {
                currentIndex = index;
                if (index == AppThemeMode().themeModeSystem) {
                  appStore.setDarkMode(MediaQuery.of(context).platformBrightness == Brightness.dark);
                } else if (index == AppThemeMode().themeModeLight) {
                  appStore.setDarkMode(false);
                } else if (index == AppThemeMode().themeModeDark) {
                  appStore.setDarkMode(true);
                }
                setValue(THEME_MODE_INDEX, index);
                setState(() {});
                LiveStream().emit('UpdateTheme');
                resetMenuIndex();
                finish(context);
              });
            },
          ),
        ).paddingAll(8),
      ),
    );
  }
}
