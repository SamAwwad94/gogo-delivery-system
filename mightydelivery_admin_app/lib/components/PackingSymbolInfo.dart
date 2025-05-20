import 'package:flutter/material.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/common.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../utils/Colors.dart';
import '../utils/Extensions/app_common.dart';

class PackagingSymbolsInfo extends StatefulWidget {
  PackagingSymbolsInfo({super.key});

  @override
  State<PackagingSymbolsInfo> createState() => _PackagingSymbolsInfoState();
}

class _PackagingSymbolsInfoState extends State<PackagingSymbolsInfo> {
  List<Map<String, String>> list = getPackagingSymbols();


  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(language.labels),
      body: ListView.builder(
          itemCount: list.length,
          itemBuilder: (context, index) {
            return Container(
                margin: EdgeInsets.all(8),
                padding: EdgeInsets.symmetric(vertical: 10, horizontal: 8),
                decoration: boxDecorationWithRoundedCorners(border: Border.all(color: borderColor)),
                child: Row(
                  children: [
                    Image.asset(
                      list[index]['image']!,
                      width: 24,
                      height: 24,
                      color: appStore.isDarkMode ? Colors.white.withOpacity(0.7) : primaryColor,
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          list[index]['title'].toString(),
                          style: boldTextStyle(),
                        ),
                        4.height,
                        Container(
                          width: context.width() * 0.8,
                          child: Text(
                            list[index]['description'].toString(),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: secondaryTextStyle(),
                          ),
                        ),
                      ],
                    ).paddingOnly(left: 8),
                  ],
                ));
          }).paddingAll(8),
    );
  }
}

