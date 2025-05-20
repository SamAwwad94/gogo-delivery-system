import 'package:flutter/material.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';

import '../extensions/animatedList/animated_scroll_view.dart';
import '../extensions/decorations.dart';
import '../main.dart';
import '../models/LanguageDataModel.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/LiveStream.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class LanguageScreen extends StatefulWidget {
  static String tag = '/AppLanguageScreen';

  @override
  LanguageScreenState createState() => LanguageScreenState();
}

class LanguageScreenState extends State<LanguageScreen> {
  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
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
        appBar: appBarWidget(language.language),
        body: AnimatedScrollView(
          padding: EdgeInsets.all(8),
          children: List.generate(localeLanguageList.length, (index) {
            LanguageDataModel data = localeLanguageList[index];
            return Container(
              margin: EdgeInsets.all(8),
              decoration: boxDecorationWithRoundedCorners(
                  backgroundColor: Colors.transparent,
                  border: Border.all(color: getStringAsync(SELECTED_LANGUAGE_CODE, defaultValue: defaultLanguage) == data.languageCode ? primaryColor : Colors.grey.withOpacity(0.3))),
              padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: Row(
                children: [
                  Image.asset(data.flag.validate(), width: 34, height: 34).cornerRadiusWithClipRRect(4),
                  8.width,
                  Text('${data.name.validate()}', style: primaryTextStyle()).expand(),
                  getStringAsync(SELECTED_LANGUAGE_CODE, defaultValue: defaultLanguage) == data.languageCode
                      ? Icon(Ionicons.radio_button_on, size: 20, color: appStore.isDarkMode ? white : primaryColor)
                      : Icon(Ionicons.radio_button_off_sharp, size: 20, color: Colors.grey.withOpacity(0.3)),
                ],
              ),
            ).onTap(() async {
              await setValue(SELECTED_LANGUAGE_CODE, data.languageCode);
              selectedLanguageDataModel = data;
              appStore.setLanguage(data.languageCode!, context: context);
              setState(() {});
              LiveStream().emit('UpdateLanguage');
              resetMenuIndex();
              finish(context);
            }, splashColor: Colors.transparent, highlightColor: Colors.transparent);
          }),
        ),
      ),
    );
  }
}
