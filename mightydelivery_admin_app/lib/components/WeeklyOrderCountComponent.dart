import 'package:flutter/material.dart';
import '../extensions/extension_util/context_extensions.dart';
import 'package:syncfusion_flutter_charts/charts.dart';

import '../main.dart';
import '../models/DashboardModel.dart';
import '../utils/Common.dart';
import '../utils/Extensions/app_common.dart';

class WeeklyOrderCountComponent extends StatefulWidget {
  static String tag = '/WeeklyOrderCountComponent';
  final List<WeeklyOrderCount> weeklyOrderCount;

  WeeklyOrderCountComponent({required this.weeklyOrderCount});

  @override
  WeeklyOrderCountComponentState createState() => WeeklyOrderCountComponentState();
}

class WeeklyOrderCountComponentState extends State<WeeklyOrderCountComponent> {
  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    //
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(12),
      width: context.width() / 1.1,
      height: 300,
      child: SfCircularChart(
        margin: EdgeInsets.zero,
        title: ChartTitle(text: language.weeklyOrderCount, textStyle: primaryTextStyle(size: 14)),
        tooltipBehavior: TooltipBehavior(enable: true),
        legend: Legend(isVisible: true, overflowMode: LegendItemOverflowMode.wrap),
        series: <CircularSeries>[
          PieSeries<WeeklyOrderCount, String>(
              dataSource: widget.weeklyOrderCount,
              xValueMapper: (WeeklyOrderCount data, _) => dayTranslate(data.day!),
              yValueMapper: (WeeklyOrderCount data, _) => data.total,
              dataLabelSettings: DataLabelSettings(isVisible: true, textStyle: boldTextStyle()))
        ],
      ),
      decoration: containerDecoration(),
    );
  }
}
