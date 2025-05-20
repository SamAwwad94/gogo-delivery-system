import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:intl/intl.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/widgets.dart';
import '../models/ReferenceListModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../main.dart';
import '../utils/Extensions/app_common.dart';

class ReferenceProgramScreen extends StatefulWidget {
  final String? title;

  ReferenceProgramScreen({this.title});

  @override
  State<ReferenceProgramScreen> createState() => _ReferenceProgramScreenState();
}

class _ReferenceProgramScreenState extends State<ReferenceProgramScreen> {
  int currentPage = 1;
  int totalPage = 1;
  int totalItems = 0;
  int perPage = 0;
  int id = 0;
  String apiInputText = "";

  List<ReferenceData> referenceList = [];


  @override
  void initState() {
    super.initState();
    init();
  }

  void init() {
    getReferenceListApi(currentPage);
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Future<void> getReferenceListApi(int crntPage) async {
    appStore.setLoading(true);
    await getReferenceList().then((value) {
      appStore.setLoading(false);
      currentPage = value.pagination!.currentPage.validate();
      totalPage = value.pagination!.totalPages.validate();
      totalItems = value.pagination!.totalItems.validate();
      perPage = value.pagination!.perPage;
      referenceList.clear();
      referenceList.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  Widget headerText(String name, double width) {
    return Container(
      width: width,
      alignment: Alignment.center,
      child: Text(
        name,
        style: boldTextStyle(size: 16, color: primaryColor),
        textAlign: TextAlign.center,
        overflow: TextOverflow.ellipsis,
        maxLines: 2,
      ),
    );
  }

  Widget dataText(String name, double width, {bool isBoldText = false}) {
    return Container(
      width: width,
      alignment: Alignment.center,
      child: Text(
        name,
        overflow: TextOverflow.ellipsis,
        maxLines: 4,
        softWrap: true,
        style: isBoldText ? boldTextStyle() : primaryTextStyle(),
        textAlign: TextAlign.center,
      ),
    );
  }

  String dateFormat(String dateTime) {
    String formattedDate = DateFormat('dd MMM yyyy').format(DateTime.parse(dateTime).toLocal());
    formattedDate += ",\n";
    formattedDate += DateFormat('hh:mm a').format(DateTime.parse(dateTime).toLocal());
    return formattedDate;
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
        appBar: appBarWidget("Reference program",),
        body: Observer(builder: (context) {
          return Stack(
            children: [
              Column(
                children: [
                  if (totalItems > perPage)
                    Padding(
                      padding: EdgeInsets.only(bottom: 12, right: 12, top: 12),
                      child: paginationWidget(
                          currentPage: currentPage,
                          totalPage: totalPage,
                          onUpdate: (currentPage) {
                            getReferenceListApi(currentPage);
                          }),
                    ),
                  if (referenceList.isNotEmpty)
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: DataTable(columnSpacing: 15.0, columns: [
                        DataColumn(label: headerText(language.name, 100)),
                        DataColumn(label: headerText(language.email, 180)),
                        DataColumn(label: headerText(language.city, 100)),
                        DataColumn(label: headerText(language.country, 100)),
                        DataColumn(label: headerText(language.contactNumber, 150)),
                        DataColumn(label: headerText(language.createDate, 150)),
                        DataColumn(label: headerText(language.lastActivedDate, 100)),
                        DataColumn(label: headerText(language.appVersion, 100)),
                        DataColumn(label: headerText(language.userRefferalCode, 100)),
                      ], rows: [
                        ...referenceList.map((reference) {
                          return DataRow(
                            cells: [
                              DataCell(dataText(reference.name.validate().toString(), 100)),
                              DataCell(dataText("${reference.email.validate().toString()}", 180)),
                              DataCell(dataText(reference.cityName.validate(), 100)),
                              DataCell(dataText(reference.countryName.validate(), 100)),
                              DataCell(dataText(reference.contactNumber.validate(), 150)),
                              DataCell(dataText(dateFormat(reference.createdAt.toString()), 150)),
                              DataCell(dataText(dateFormat(reference.lastActivedAt.toString() + "Z"), 100)),
                              DataCell(dataText(reference.appVersion.validate(), 100)),
                              DataCell(dataText(reference.partnerReferralCode.toString() , 100)),
                            ],
                          );
                        }).toList(),
                      ]),
                    ),
                ],
              ),
              appStore.isLoading
                  ? loaderWidget()
                  : referenceList.isEmpty
                      ? emptyWidget()
                      : SizedBox(),
            ],
          );
        }),
      ),
    );
  }
}
