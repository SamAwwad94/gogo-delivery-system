import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../components/AddVehicleDialog.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';

import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/VehicleModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class VehicleScreen extends StatefulWidget {
  const VehicleScreen({Key? key}) : super(key: key);

  @override
  State<VehicleScreen> createState() => _VehicleScreenState();
}

class _VehicleScreenState extends State<VehicleScreen> {
  ScrollController controller = ScrollController();

  int currentPage = 1;
  int totalPage = 1;
  bool isSelectAll = false;
  List<int> vehicleChecked = [];

  List<VehicleData> vehicleList = [];
  TextEditingController searchVehicleCont = TextEditingController();
  @override
  void initState() {
    super.initState();
    init();
    controller.addListener(() {
      if (controller.position.pixels == controller.position.maxScrollExtent) {
        if (currentPage < totalPage) {
          currentPage++;
          setState(() {});
          getVehicleListApiCall();
        }
      }
    });
  }

  Future<void> init() async {
    // appStore.setSelectedMenuIndex(VEHICLE_INDEX);
    getVehicleListApiCall();
  }

  getVehicleListApiCall({String? search}) async {
    appStore.setLoading(true);

    await getVehicleList(page: currentPage, isDeleted: true, searchText: search).then((value) {
      appStore.setLoading(false);
      totalPage = value.pagination!.totalPages!;
      if (currentPage == 1) {
        vehicleList.clear();
      }
      vehicleList.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      log(error.toString());
      toast(error.toString());
    });
  }

  deleteVehicleApi(int id) async {
    appStore.setLoading(true);
    await deleteVehicle(id).then((value) {
      appStore.setLoading(false);
      getVehicleListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  restoreVehicleApiCall({@required int? id, @required String? type}) async {
    Map req = {"id": id, "type": type};
    appStore.setLoading(true);
    await vehicleAction(req).then((value) {
      appStore.setLoading(false);
      getVehicleListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteMultipleVehicleApiCall(List<int> cityData) async {
    Map req = {
      "ids": vehicleChecked,
    };
    appStore.setLoading(true);
    await multipleDeleteVehicle(req).then((value) {
      appStore.setLoading(false);
      getVehicleListApiCall();
      vehicleChecked.clear();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  updateStatusApiCall(VehicleData vehicleData) async {
    appStore.setLoading(true);
    await addVehicle(
      id: vehicleData.id,
      status: vehicleData.status == 1 ? 0 : 1,
      size: vehicleData.size,
      vehicleImage: vehicleData.vehicleImage,
      title: vehicleData.title,
      capacity: vehicleData.capacity,
      description: vehicleData.description,
      type: vehicleData.type,
      price: vehicleData.price,
      minKm: vehicleData.minKm,
      perKmCharge: vehicleData.perKmCharge,
    ).then((value) {
      appStore.setLoading(false);
      getVehicleListApiCall();
      log('${value.message.toString()}');
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      log('${error.toString()}');
    });
  }

  _onSelected(bool selected, int id) {
    if (selected == true) {
      setState(() {
        vehicleChecked.remove(id);
      });
    } else {
      setState(() {
        vehicleChecked.add(id);
      });
    }
  }

  //Select all Checkbox
  _onChangedProperty() {
    vehicleChecked.clear();
    for (int i = 0; i < vehicleList.length; i++) {
      if (isSelectAll == true) {
        vehicleChecked.add(vehicleList[i].id!);
      } else {
        if (vehicleChecked.isNotEmpty) {
          vehicleChecked.remove(vehicleList[i].id!);
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
        appBar: appBarWidget(language.vehicle, actions: [
          Icon(MaterialCommunityIcons.select_multiple, color: white, size: 28).paddingAll(0).onTap(() {
            setState(() {
              isSelectAll = !isSelectAll;
              _onChangedProperty();
            });
          }).visible(vehicleChecked.length > 0),
          Icon(MaterialCommunityIcons.delete, color: white, size: 28).paddingAll(8).onTap(() {
            commonConfirmationDialog(
              context,
              DIALOG_TYPE_DELETE,
              title: language.deleteVehicles,
              subtitle: language.doYouWantToDeleteSelectedVehicles,
              () {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  Navigator.pop(context);
                  deleteMultipleVehicleApiCall(vehicleChecked);
                }
              },
            );
          }).visible(vehicleChecked.length > 0),
          addButton(
            language.add,
            () {
              showDialog(
                context: context,
                barrierDismissible: false,
                builder: (BuildContext dialogContext) {
                  return AddVehicleDialog(onUpdate: () {
                    currentPage = 1;
                    getVehicleListApiCall();
                    ();
                  });
                },
              );
            },
          ).paddingOnly(left: appStore.selectedLanguage == "ar" ? 16 : 0),
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
                    controller: searchVehicleCont,
                    decoration: commonInputDecoration(prefixIcon: Icon(Icons.search), hintText: language.search),
                    onChanged: (v) async {
                      getVehicleListApiCall(search: v);
                    },
                  ).paddingOnly(left: 16, right: 16),
                  16.height,
                  ListView.builder(
                    controller: controller,
                    padding: EdgeInsets.only(left: 16, right: 16),
                    itemCount: vehicleList.length,
                    itemBuilder: (context, index) {
                      VehicleData mData = vehicleList[index];
                      return InkWell(
                        onLongPress: () {
                          setState(() {
                            _onSelected(vehicleChecked.contains(mData.id), mData.id!);
                          });
                        },
                        child: Container(
                          margin: EdgeInsets.only(bottom: 16),
                          decoration: boxDecorationWithRoundedCorners(
                              backgroundColor: vehicleChecked.contains(mData.id)
                                  ? Colors.red.shade100.withOpacity(0.2)
                                  : mData.deletedAt != null
                                      ? Colors.red.shade100.withOpacity(0.2)
                                      : appStore.isDarkMode
                                          ? Colors.black
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
                                    Text('${mData.title ?? "-"}', style: boldTextStyle()),
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
                                              },
                                                title: mData.status != 1 ? language.enableCity : language.disable_vehicle,
                                                subtitle: mData.status != 1 ? language.enableCityMsg : language.disable_vehicle_msg)
                                            : toast(language.youCannotUpdateStatusRecordDeleted);
                                      },
                                    ),
                                    outlineActionIcon(context, mData.deletedAt == null ? Icons.edit : Icons.restore, Colors.green, () {
                                      mData.deletedAt == null
                                          ? showDialog(
                                              context: context,
                                              barrierDismissible: false,
                                              builder: (BuildContext dialogContext) {
                                                return AddVehicleDialog(
                                                  vehicleData: mData,
                                                  onUpdate: () {
                                                    currentPage = 1;
                                                    getVehicleListApiCall();
                                                  },
                                                );
                                              },
                                            )
                                          : commonConfirmationDialog(context, DIALOG_TYPE_RESTORE, () {
                                              if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                                toast(language.demoAdminMsg);
                                              } else {
                                                Navigator.pop(context);
                                                restoreVehicleApiCall(id: mData.id, type: RESTORE);
                                              }
                                            }, title: language.restoreVehicle, subtitle: language.restoreVehicleMsg);
                                    }),
                                    SizedBox(width: 8),
                                    outlineActionIcon(context, mData.deletedAt == null ? Icons.delete : Icons.delete_forever, Colors.red, () {
                                      commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () {
                                        if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                          toast(language.demoAdminMsg);
                                        } else {
                                          Navigator.pop(context);
                                          mData.deletedAt == null ? deleteVehicleApi(mData.id!) : restoreVehicleApiCall(id: mData.id, type: FORCE_DELETE);
                                        }
                                      }, isForceDelete: mData.deletedAt != null, title: language.delete_vehicle, subtitle: language.deleteVehicleMsg);
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
                                        Text(language.vehicle_name, style: primaryTextStyle(size: 14)),
                                        Text('${mData.title}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text('${language.vehicle_size}', style: primaryTextStyle(size: 14)),
                                        Text('${mData.size}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text('${language.vehicle_capacity} ', style: primaryTextStyle(size: 14)),
                                        Text('${mData.capacity}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.description, style: primaryTextStyle(size: 14)),
                                        Text('${(mData.description)}', style: secondaryTextStyle()),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.price, style: primaryTextStyle(size: 14)),
                                        Text('${(mData.price)}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.minimumDistance, style: primaryTextStyle(size: 14)),
                                        Text('${(mData.minKm)}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.perKmCharge, style: primaryTextStyle(size: 14)),
                                        Text('${(mData.perKmCharge)}', style: boldTextStyle(size: 15)),
                                      ],
                                    ),
                                    Divider(thickness: 0.9, height: 20),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(language.status, style: primaryTextStyle(size: 14)),
                                        Text('${mData.status} ', style: boldTextStyle(size: 15)),
                                      ],
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
                  : vehicleList.isEmpty
                      ? emptyWidget()
                      : SizedBox(),
            ],
          );
        }),
      ),
    );
  }
}
