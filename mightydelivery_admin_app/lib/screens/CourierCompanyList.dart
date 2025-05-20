import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../screens/AddEditCourierCompanyScreen.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/CourierCompaniesListModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';

class CourierCompanyListScreen extends StatefulWidget {
  @override
  CourierCompanyListScreenState createState() => CourierCompanyListScreenState();
}

class CourierCompanyListScreenState extends State<CourierCompanyListScreen> {
  ScrollController scrollController = ScrollController();
  int currentPage = 1;
  int totalPage = 1;
  int perPage = 10;

  List<CourierCompany> courierCompaniesList = [];

  @override
  void initState() {
    super.initState();
    init();
    scrollController.addListener(() {
      if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
        if (currentPage < totalPage) {
          currentPage++;
          setState(() {});
          getCourierCompaniesListApiCall();
        }
      }
    });
  }

  void init() async {
    afterBuildCreated(() {
      appStore.setLoading(true);
      getCourierCompaniesListApiCall();
    });
  }

  getCourierCompaniesListApiCall() async {
    appStore.setLoading(true);
    await getCourierCompaniesList(currentPage).then((value) {
      appStore.setLoading(false);

      totalPage = value.pagination!.totalPages!;
      currentPage = value.pagination!.currentPage!;
      if (currentPage == 1) {
        courierCompaniesList.clear();
      }
      value.data!.forEach((element) {
        courierCompaniesList.add(element);
      });
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteCourierCompaniesApiCall(int id) async {
    appStore.setLoading(true);
    await deleteCourierCompanies(id).then((value) {
      appStore.setLoading(false);
      getCourierCompaniesListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

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
        appBar: appBarWidget(
          language.courierCompanyList,
        ),
        body: Observer(builder: (context) {
          return Stack(
            fit: StackFit.expand,
            children: [
              ListView.builder(
                controller: scrollController,
                padding: EdgeInsets.only(left: 16, top: 16, right: 16),
                itemCount: courierCompaniesList.length,
                itemBuilder: (context, index) {
                  CourierCompany mData = courierCompaniesList[index];
                  return Container(
                    margin: EdgeInsets.only(bottom: 16),
                    decoration: containerDecoration(),
                    padding: EdgeInsets.all(12),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            border: Border.all(width: 1, color: Colors.grey.withOpacity(0.3)),
                            color: appStore.isDarkMode ? scaffoldColorDark : Colors.white,
                            borderRadius: BorderRadius.circular(16),
                          ),
                          padding: EdgeInsets.all(8),
                          child: commonCachedNetworkImage('${mData.image.validate()}', fit: BoxFit.fitHeight, height: 50, width: 50),
                        ),
                        SizedBox(width: 8),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                crossAxisAlignment: CrossAxisAlignment.end,
                                children: [
                                  Expanded(child: Text('${mData.name ?? ""}', style: boldTextStyle())),
                                  outlineActionIcon(context, Icons.delete, Colors.red, () async {
                                    commonConfirmationDialog(
                                      context,
                                      DIALOG_TYPE_DELETE,
                                      () {
                                        finish(context);
                                        deleteCourierCompaniesApiCall(mData.id.validate());
                                      },
                                      title: language.deleteCourierCompany,
                                      subtitle: language.pageDeleteConfirmMessage,
                                    );
                                  }),
                                  8.width,
                                  outlineActionIcon(context, Icons.edit, Colors.green, () async {
                                    await launchScreen(
                                      context,
                                      AddEditCourierCompanyScreen(
                                        data: mData,
                                        onUpdate: () {
                                          getCourierCompaniesListApiCall();
                                        },
                                      ),
                                    );
                                  }),
                                ],
                              ),
                              SizedBox(height: 10),
                              Text(
                                '${mData.link.validate()}',
                                style: primaryTextStyle(size: 14),
                                maxLines: 2,
                              ).onTap(() {
                                // todo navigation
                              })
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
              Positioned(
                bottom: 16,
                right: 16,
                child: FloatingActionButton(
                    backgroundColor: primaryColor,
                    child: Icon(Icons.add, color: Colors.white),
                    onPressed: () {
                      AddEditCourierCompanyScreen(
                        onUpdate: () {
                          getCourierCompaniesListApiCall();
                        },
                      ).launch(context);
                    }),
              ),
              appStore.isLoading
                  ? loaderWidget()
                  : courierCompaniesList.isEmpty
                      ? emptyWidget()
                      : SizedBox(),
            ],
          );
        }),
      ),
    );
  }
}
