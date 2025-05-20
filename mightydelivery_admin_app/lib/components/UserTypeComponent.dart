import 'package:flutter/material.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Colors.dart';

import '../../main.dart';
import '../extensions/decorations.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';

class UserTypeComponent extends StatefulWidget {
  final String userType;

  UserTypeComponent({required this.userType});

  @override
  UserTypeComponentState createState() => UserTypeComponentState();
}

class UserTypeComponentState extends State<UserTypeComponent> {
  UserType type = UserType();
  int? currentIndex = 0;
  List<String?> typeList = [
    language.all,
    language.active,
   language.inActive,
    language.pending,
  ];

  @override
  void initState() {
    super.initState();
    if (widget.userType.isNotEmpty) {
      currentIndex = typeList.indexOf(widget.userType);
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      contentPadding: EdgeInsets.zero,
      backgroundColor: appStore.isDarkMode ? textPrimaryColor : white,
      content: Container(
        width: context.width(),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: EdgeInsets.all(8),
              alignment: Alignment.topLeft,
              decoration: boxDecorationWithShadow(backgroundColor: primaryColor, borderRadius: radiusOnly(topRight: defaultRadius, topLeft: defaultRadius)),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(language.type, style: boldTextStyle(size: 20, color: Colors.white)).paddingLeft(12),
                  CloseButton(color: Colors.white),
                ],
              ),
            ),
            ListView(
              shrinkWrap: true,
              padding: EdgeInsets.symmetric(horizontal: 4, vertical: 8),
              children: List.generate(
                typeList.length,
                (index) {
                  return RadioListTile(
                    value: index,
                    dense: true,
                    contentPadding: EdgeInsets.symmetric(horizontal: 8),
                    groupValue: currentIndex,
                    activeColor: primaryColor,
                    title: Text(typeList[index]!, style: primaryTextStyle(size: 16)),
                    onChanged: (dynamic val) {
                      currentIndex = index;
                      setState(() {});
                      Navigator.pop(context, {
                        'type': typeList[index],
                      });
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class UserType {
  final int all = 0;
  final int active = 1;
  final int inActive = 2;
  final int pending = 3;
}
