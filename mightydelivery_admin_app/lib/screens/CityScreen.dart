import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';

import '../components/AddCityDialog.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/CityListModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class CityScreen extends StatefulWidget {
  static String tag = '/CityComponent';

  @override
  CityScreenState createState() => CityScreenState();
}

class CityScreenState extends State<CityScreen> {
  ScrollController controller = ScrollController();

  int currentPage = 1;
  int totalPage = 1;
  TextEditingController searchcityCont = TextEditingController();
  List<CityData> cityList = [];
  bool isSelectAll = false;
  List<int> citiesChecked = [];
  @override
  void initState() {
    super.initState();
    init();
    controller.addListener(() {
      if (controller.position.pixels == controller.position.maxScrollExtent) {
        if (currentPage < totalPage) {
          currentPage++;
          setState(() {});
          getCityListApiCall();
        }
      }
    });
  }

  Future<void> init() async {
    afterBuildCreated(() {
      appStore.setLoading(true);
      getCityListApiCall();
    });
  }

  getCityListApiCall({String? search}) async {
    appStore.setLoading(true);
    await getCityList(
      page: currentPage,
      isDeleted: true,
      searchText: search,
    ).then((value) {
      appStore.setLoading(false);
      totalPage = value.pagination!.totalPages!;
      if (currentPage == 1) {
        cityList.clear();
      }
      cityList.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteCityApiCall(int id) async {
    appStore.setLoading(true);
    await deleteCity(id).then((value) {
      appStore.setLoading(false);
      currentPage = 1;
      getCityListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  restoreCityApiCall({@required int? id, @required String? type}) async {
    Map req = {"id": id, "type": type};
    appStore.setLoading(true);
    await cityAction(req).then((value) {
      appStore.setLoading(false);
      currentPage = 1;
      getCityListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteMultipleCityApiCall(List<int> cityData) async {
    Map req = {
      "ids": citiesChecked,
    };
    appStore.setLoading(true);
    await multipleDeleteCity(req).then((value) {
      appStore.setLoading(false);
      getCityListApiCall();
      citiesChecked.clear();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  updateStatusApiCall(CityData cityData) async {
    Map req = {
      "id": cityData.id,
      "status": cityData.status == 1 ? 0 : 1,
    };
    appStore.setLoading(true);
    await addCity(req).then((value) {
      appStore.setLoading(false);
      currentPage = 1;
      getCityListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  _onSelected(bool selected, int id) {
    if (selected == true) {
      setState(() {
        citiesChecked.remove(id);
      });
    } else {
      setState(() {
        citiesChecked.add(id);
      });
    }
  }

  //Select all Checkbox
  _onChangedProperty() {
    citiesChecked.clear();
    for (int i = 0; i < cityList.length; i++) {
      if (isSelectAll == true) {
        citiesChecked.add(cityList[i].id!);
      } else {
        if (citiesChecked.isNotEmpty) {
          citiesChecked.remove(cityList[i].id!);
        }
      }
    }
    setState(() {});
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
        appBar: appBarWidget(language.city, actions: [
          // Icon(MaterialCommunityIcons.plus, color: white, size: 28).paddingOnly(right: userChecked.length > 0 ? 8 : 18).onTap(() {
          //   showDialog(
          //     context: context,
          //     barrierDismissible: false,
          //     builder: (BuildContext dialogContext) {
          //       return AddCityDialog(
          //         onUpdate: () {
          //           currentPage = 1;
          //           getCityListApiCall();
          //         },
          //       );
          //     },
          //   );
          // }),
          Icon(MaterialCommunityIcons.select_multiple, color: white, size: 26).onTap(() {
            setState(() {
              isSelectAll = !isSelectAll;
              _onChangedProperty();
            });
          }).visible(citiesChecked.length > 0),
          Icon(MaterialCommunityIcons.delete, color: white, size: 24).paddingAll(8).onTap(() {
            commonConfirmationDialog(
              context,
              DIALOG_TYPE_DELETE,
              title: language.deleteCities,
              subtitle: language.doYouWantToDeleteAllSelectedCity,
              () {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  Navigator.pop(context);
                  deleteMultipleCityApiCall(citiesChecked);
                }
              },
            );
          }).visible(citiesChecked.length > 0),
          addButton(
            language.add,
            () {
              showDialog(
                context: context,
                barrierDismissible: false,
                builder: (BuildContext dialogContext) {
                  return AddCityDialog(onUpdate: () {
                    currentPage = 1;
                    getCityListApiCall();
                    ();
                  });
                },
              );
            },
          ).paddingOnly(left: appStore.selectedLanguage == "ar" ? 12 : 0),
        ]),
        body: Observer(builder: (context) {
          return Stack(
            fit: StackFit.expand,
            children: [
              Column(
                children: [
                  16.height,
                  AppTextField(
                    textFieldType: TextFieldType.NAME,
                    controller: searchcityCont,
                    decoration: commonInputDecoration(prefixIcon: Icon(Icons.search), hintText: language.search),
                    onChanged: (v) async {
                      getCityListApiCall(search: v);
                    },
                  ).paddingOnly(left: 16, right: 16),
                  16.height,
                  ListView.builder(
                    controller: controller,
                    padding: EdgeInsets.only(left: 16, right: 16),
                    itemCount: cityList.length,
                    itemBuilder: (context, index) {
                      CityData mData = cityList[index];
                      return InkWell(
                        onLongPress: () {
                          setState(() {
                            _onSelected(citiesChecked.contains(mData.id), mData.id!);
                          });
                        },
                        child: Container(
                          margin: EdgeInsets.only(bottom: 16),
                          decoration: boxDecorationWithRoundedCorners(
                              backgroundColor: citiesChecked.contains(mData.id)
                                  ? Colors.red.shade100.withOpacity(0.2)
                                  : mData.deletedAt != null
                                      ? Colors.red.shade200.withOpacity(0.2)
                                      : appStore.isDarkMode
                                          ? textPrimaryColor
                                          : white,
                              border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
                          child: Column(
                            children: [
                              Container(
                                decoration: BoxDecoration(
                                  color: primaryColor.withOpacity(0.2),
                                  borderRadius: BorderRadius.only(topLeft: Radius.circular(8), topRight: Radius.circular(8)),
                                ),
                                padding: EdgeInsets.all(12),
                                child: Row(
                                  children: [
                                    Text('#${mData.id}', style: boldTextStyle()),
                                    SizedBox(width: 8),
                                    Text('${mData.name ?? "-"}', style: boldTextStyle()),
                                    Spacer(),
                                    GestureDetector(
                                      child: Container(
                                        alignment: Alignment.center,
                                        padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                        margin: EdgeInsets.only(right: 8, left: appStore.selectedLanguage == "ar" ? 8 : 0),
                                        decoration: BoxDecoration(
                                            border: Border.all(
                                                color: mData.status == 1
                                                    ? appStore.isDarkMode
                                                        ? white
                                                        : primaryColor.withOpacity(0.6)
                                                    : Colors.red.withOpacity(0.6)),
                                            color: mData.status == 1 ? primaryColor.withOpacity(0.15) : Colors.red.withOpacity(0.15),
                                            borderRadius: BorderRadius.circular(defaultRadius)),
                                        child: Text(
                                          '${mData.status == 1 ? language.enable : language.disable}',
                                          style: primaryTextStyle(
                                              color: mData.status == 1
                                                  ? appStore.isDarkMode
                                                      ? white
                                                      : primaryColor
                                                  : Colors.red),
                                        ),
                                      ),
                                      onTap: () {
                                        mData.deletedAt == null
                                            ? commonConfirmationDialog(context, mData.status == 1 ? DIALOG_TYPE_DISABLE : DIALOG_TYPE_ENABLE, () {
                                                if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                                  toast(language.demoAdminMsg);
                                                } else {
                                                  Navigator.pop(context);
                                                  updateStatusApiCall(mData);
                                                }
                                              }, title: mData.status != 1 ? language.enableCity : language.disableCity, subtitle: mData.status != 1 ? language.enableCityMsg : language.disableCityMsg)
                                            : toast(language.youCannotUpdateStatusRecordDeleted);
                                      },
                                    ),
                                    outlineActionIcon(context, mData.deletedAt == null ? Icons.edit : Icons.restore, Colors.green, () {
                                      mData.deletedAt == null
                                          ? showDialog(
                                              context: context,
                                              barrierDismissible: false,
                                              builder: (BuildContext dialogContext) {
                                                return AddCityDialog(
                                                  cityData: mData,
                                                  onUpdate: () {
                                                    currentPage = 1;
                                                    getCityListApiCall();
                                                  },
                                                );
                                              },
                                            )
                                          : commonConfirmationDialog(context, DIALOG_TYPE_RESTORE, () {
                                              if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                                toast(language.demoAdminMsg);
                                              } else {
                                                Navigator.pop(context);
                                                restoreCityApiCall(id: mData.id, type: RESTORE);
                                              }
                                            }, title: language.restoreCity, subtitle: language.restoreCityMsg);
                                    }),
                                    SizedBox(width: 8),
                                    outlineActionIcon(context, mData.deletedAt == null ? Icons.delete : Icons.delete_forever, Colors.red, () {
                                      commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () {
                                        if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                          toast(language.demoAdminMsg);
                                        } else {
                                          Navigator.pop(context);
                                          mData.deletedAt == null ? deleteCityApiCall(mData.id!) : restoreCityApiCall(id: mData.id, type: FORCE_DELETE);
                                        }
                                      }, isForceDelete: mData.deletedAt != null, title: language.deleteCity, subtitle: language.deleteCityMsg);
                                    }),
                                  ],
                                ),
                              ),
                              SizedBox(height: 8),
                              Padding(
                                padding: EdgeInsets.all(12),
                                child: Column(
                                  children: [
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.countryName, style: primaryTextStyle(size: 14)),
                                        Text('${mData.countryName}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        mData.country == null
                                            ? Text('${language.minimumDistance} ()', style: primaryTextStyle(size: 14))
                                            : Text('${language.minimumDistance} (${mData.country!.distanceType})', style: primaryTextStyle(size: 14)),
                                        Text('${mData.minDistance}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        mData.country == null
                                            ? Text('${language.minimumWeight} ()', style: primaryTextStyle(size: 14))
                                            : Text('${language.minimumWeight} (${mData.country!.weightType})', style: primaryTextStyle(size: 14)),
                                        Text('${mData.minWeight}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.createdDate, style: primaryTextStyle(size: 14)),
                                        Text('${printDate(mData.createdAt.validate())}', style: secondaryTextStyle()),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.adminCommission, style: primaryTextStyle(size: 14)),
                                        Text('${mData.adminCommission} ${mData.commissionType == CHARGE_TYPE_PERCENTAGE ? '%' : ''}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    IntrinsicHeight(
                                      child: Row(
                                        children: [
                                          Expanded(
                                            child: Column(
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              children: [
                                                Text(
                                                  language.fixedCharge,
                                                  textAlign: TextAlign.center,
                                                  style: primaryTextStyle(size: 14),
                                                ),
                                                SizedBox(height: 4),
                                                Text('${mData.fixedCharges ?? 0}', style: boldTextStyle(size: 15)),
                                              ],
                                            ),
                                          ),
                                          VerticalDivider(thickness: 0.9),
                                          Expanded(
                                            child: Column(
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              children: [
                                                Text(
                                                  language.cancelCharge,
                                                  textAlign: TextAlign.center,
                                                  style: primaryTextStyle(size: 14),
                                                ),
                                                SizedBox(height: 4),
                                                Text('${mData.cancelCharges ?? 0}', style: boldTextStyle(size: 15)),
                                              ],
                                            ),
                                          ),
                                          VerticalDivider(thickness: 0.9),
                                          Expanded(
                                            child: Column(
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              children: [
                                                Text(
                                                  language.perDistanceCharge,
                                                  textAlign: TextAlign.center,
                                                  style: primaryTextStyle(size: 14),
                                                ),
                                                SizedBox(height: 4),
                                                Text('${mData.perDistanceCharges ?? 0}', style: boldTextStyle(size: 15)),
                                              ],
                                            ),
                                          ),
                                          VerticalDivider(thickness: 0.9),
                                          Expanded(
                                            child: Column(
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              children: [
                                                Text(
                                                  language.perWeightCharge,
                                                  textAlign: TextAlign.center,
                                                  style: primaryTextStyle(size: 14),
                                                ),
                                                SizedBox(height: 4),
                                                Text('${mData.perWeightCharges ?? 0}', style: boldTextStyle(size: 15)),
                                              ],
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ).expand(),
                ],
              ),
              appStore.isLoading
                  ? loaderWidget()
                  : cityList.isEmpty
                      ? emptyWidget()
                      : SizedBox(),
            ],
          );
        }),
      ),
    );
  }
}
