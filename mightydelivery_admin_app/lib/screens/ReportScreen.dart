import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:intl/intl.dart';
import '../components/FilterReportComponent.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/list_extensions.dart';
import '../extensions/extension_util/num_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../models/CityListModel.dart';
import '../models/UserModel.dart';
import '../network/RestApis.dart';
import '../screens/OrderDetailScreen.dart';
import '../screens/UserDetailScreen.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../extensions/decorations.dart';
import '../main.dart';
import '../models/CountryListModel.dart';
import '../models/ReportListModel.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class ReportScreen extends StatefulWidget {
  final String? title;

  ReportScreen({this.title});

  @override
  State<ReportScreen> createState() => _ReportScreenState();
}

class _ReportScreenState extends State<ReportScreen> {
  int currentPage = 1;
  int totalPage = 1;
  int totalItems = 0;
  int perPage = 0;
  String apiInput = "";
  int id = 0;
  String apiInputText = "";
  ReportListModel? data;
  DateTime? fromDate, toDate;

  List<ReportData> reportList = [];
  TextEditingController filterController = TextEditingController();

  int? selectedId;

  List<CountryData> countryList = [];
  List<CityData> cityList = [];
  List<UserModel> userList = [];

  String endPoint = "";

  @override
  void initState() {
    super.initState();
    getEndpoint();
    init();
  }

  void init() {
    if (apiInput.validate().isEmpty) {
      getReportApi(currentPage);
    }
  }

  Future<void> getEndpoint() async {
    if (widget.title == language.orderReport) {
      endPoint = "orderreport-list";
    } else if (widget.title == language.adminEarningReport) {
      endPoint = "adminearningreport-list";
    } else if (widget.title == language.deliveryManEarningReport) {
      endPoint = "deliverymanearningreport-list";
    } else if (widget.title == language.deliveryManWiseReport) {
      endPoint = "deliverymanreport-list";
      apiInput = "delivery_man_id";
      apiInputText = language.deliveryBoy;
      await getUserApiCall(type: DELIVERYMAN);
    } else if (widget.title == language.userWiseReport) {
      endPoint = "userreport-list";
      apiInput = "client_id";
      apiInputText = language.users;
      await getUserApiCall(type: CLIENT);
    } else if (widget.title == language.cityWiseReport) {
      endPoint = "cityreport-list";
      apiInput = "city_id";
      apiInputText = language.city;
      await getCityApiCall();
    } else if (widget.title == language.countryWiseReport) {
      endPoint = "countryreport-list";
      apiInput = "country_id";
      apiInputText = language.country;
      await getCountryApiCall();
    } else {
      endPoint = "";
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Future<void> getReportApi(int crntPage) async {
    appStore.setLoading(true);
    await getReportList(
      page: crntPage,
      apiEndPoint: endPoint,
      id: "$apiInput=$id",
      fromDate: fromDate.toString(),
      toDate: toDate.toString(),
    ).then((value) {
      appStore.setLoading(false);
      currentPage = value.pagination!.currentPage!;
      totalPage = value.pagination!.totalPages!;
      totalItems = value.pagination!.totalItems!;
      perPage = value.pagination!.perPage;
      data = value;
      reportList.clear();
      reportList.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  getCountryApiCall() async {
    appStore.setLoading(true);
    await getCountryList(status: 1).then((value) {
      appStore.setLoading(false);
      countryList = value.data!;
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  getCityApiCall({String? name}) async {
    appStore.setLoading(true);
    await getCityList(status: 1).then((value) {
      appStore.setLoading(false);
      cityList = value.data.validate();
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
    });
  }

  getUserApiCall({String? name, String? type}) async {
    appStore.setLoading(true);
    await getAllUserList(type: type, status: 1).then((value) {
      appStore.setLoading(false);
      userList = value.data.validate();
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error);
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

  List<DropdownMenuItem<int>> getList() {
    if (widget.title == language.countryWiseReport) {
      return countryList.map<DropdownMenuItem<int>>((item) {
        return DropdownMenuItem(
          value: item.id,
          child: Text(item.name ?? ''),
        );
      }).toList();
    } else if (widget.title == language.cityWiseReport) {
      return cityList.map<DropdownMenuItem<int>>((item) {
        return DropdownMenuItem(
          value: item.id,
          child: Text(item.name ?? ''),
        );
      }).toList();
    } else {
      return userList.map<DropdownMenuItem<int>>((item) {
        return DropdownMenuItem(
          value: item.id,
          child: Text(item.name ?? ''),
        );
      }).toList();
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
        appBar: appBarWidget(widget.title.validate(), actions: [
          Stack(
            children: [
              Align(alignment: AlignmentDirectional.center, child: Icon(Ionicons.md_options_outline, color: Colors.white)),
            ],
          ).withWidth(20).paddingSymmetric(horizontal: 20).onTap(() async {
            final result = await showDialog(
              context: context,
              builder: (BuildContext dialogContext) {
                return FilterReportComponent(
                  initialFromDate: fromDate,
                  initialToDate: toDate,
                );
              },
            );
            if (result != null) {
              fromDate = result['from_date'];
              toDate = result['to_date'];
              getReportApi(currentPage);
            }
          }, splashColor: Colors.transparent, hoverColor: Colors.transparent, highlightColor: Colors.transparent),
        ]),
        body: Observer(builder: (context) {
          return Stack(
            children: [
                Column(
                  children: [
                    Container(
                      width: context.width(),
                      decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          Text(apiInputText, style: boldTextStyle()),
                          SizedBox(width: 16),
                          DropdownButtonFormField<int>(
                            value: selectedId,
                            decoration: commonInputDecoration(),
                            dropdownColor: Theme.of(context).cardColor,
                            style: primaryTextStyle(),
                            items: getList(),
                            onChanged: (value) {
                              selectedId = value!;
                              id = selectedId.validate();
                              currentPage = 1;
                              getReportApi(currentPage);
                              setState(() {});
                            },
                          ).expand(),
                        ],
                      ).paddingAll(8),
                    ).paddingAll(8).visible(apiInput.validate().isNotEmpty),
                    if (totalItems > perPage)
                      Padding(
                        padding: EdgeInsets.only(bottom: 12, right: 12, top: 12),
                        child: paginationWidget(
                            currentPage: currentPage,
                            totalPage: totalPage,
                            onUpdate: (currentPage) {
                              getReportApi(currentPage);
                            }),
                      ),
                    if (data != null)
                      SingleChildScrollView(
                        scrollDirection: Axis.vertical,
                        child: SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: DataTable(columnSpacing: 15.0, columns: [
                            DataColumn(label: headerText(language.orderId, 100)),
                            DataColumn(label: headerText('${getStringAsync(ORDER_PREFIX)}#', 150)),
                            DataColumn(label: headerText(language.client, 80)),
                            DataColumn(label: headerText(language.deliveryBoy, 80)),
                            if (endPoint == "cityreport-list") DataColumn(label: headerText(language.city, 100)),
                            if (endPoint == "countryreport-list") DataColumn(label: headerText(language.country, 100)),
                            DataColumn(label: headerText(language.totalAmount, 100)),
                            DataColumn(label: headerText(language.pickUpDateTime, 150)),
                            DataColumn(label: headerText(language.deliveryDateTime, 150)),
                            DataColumn(label: headerText(language.commissionType, 100)),
                            DataColumn(label: headerText(language.adminCommission, 100)),
                            DataColumn(label: headerText(language.deliveryManCommission, 100)),
                            DataColumn(label: headerText(language.createDate, 100)),
                          ], rows: [
                            ...reportList.map((report) {
                              return DataRow(
                                cells: [
                                  DataCell(dataText(report.orderId.validate().toString(), 100).onTap(() {
                                    OrderDetailScreen(
                                      orderId: report.orderId.validate(),
                                    ).launch(context);
                                  })),
                                  DataCell(dataText("${report.orderTrackingId.validate().toString()}", 150)),
                                  DataCell(dataText(report.client.validate(), 80).onTap(() {
                                    UserDetailScreen(
                                      userId: report.clientId.validate(),
                                      userType: CLIENT,
                                    ).launch(context);
                                  })),
                                  DataCell(dataText(report.deliveryMan.validate(), 80).onTap(() {
                                    UserDetailScreen(
                                      userType: DELIVERYMAN,
                                      userId: report.deliveryManId.validate(),
                                    ).launch(context);
                                  })),
                                  if (endPoint == "cityreport-list") DataCell(dataText(report.city.validate(), 100)),
                                  if (endPoint == "countryreport-list") DataCell(dataText(report.country.validate(), 100)),
                                  DataCell(dataText(printAmount(report.totalAmount.validate()), 100)),
                                  DataCell(dataText(report.pickupDateTime != null ? dateFormat(report.pickupDateTime.toString() + "Z") : "-", 150)),
                                  DataCell(dataText(report.pickupDateTime != null ?dateFormat(report.deliveryDateTime.toString() + "Z") : "-", 150)),
                                  DataCell(dataText(report.commissionType.validate(), 100)),
                                  DataCell(dataText(printAmount(report.adminCommission.validate()), 100)),
                                  DataCell(dataText(printAmount(report.deliveryManCommission.validate()), 100)),
                                  DataCell(dataText(report.pickupDateTime != null ?dateFormat(report.createdAt.toString() + "Z") : "-", 100)),
                                ],
                              );
                            }).toList(),
                            DataRow(
                              cells: [
                                DataCell(Text(language.totalAmount, style: boldTextStyle())),
                                DataCell(Text('')),
                                DataCell(Text('')),
                                DataCell(Text('')),
                                if (endPoint == "cityreport-list") DataCell(Text('')),
                                if (endPoint == "countryreport-list") DataCell(Text('')),
                                DataCell(dataText(printAmount(data!.totalAmount.validate()), 100, isBoldText: true)),
                                DataCell(Text('')),
                                DataCell(Text('')),
                                DataCell(Text('')),
                                DataCell(dataText(printAmount(data!.totalAdminCommission.validate()), 100, isBoldText: true)),
                                DataCell(dataText(printAmount(data!.totalDeliveryManCommission.validate()), 100, isBoldText: true)),
                                DataCell(Text('')),
                              ],
                            ),
                          ]),
                        ),
                      ).expand(),
                  ],
                ),
              appStore.isLoading
                  ? loaderWidget()
                  : reportList.isEmpty
                      ? emptyWidget()
                      : SizedBox(),
            ],
          );
        }),
      ),
    );
  }
}
