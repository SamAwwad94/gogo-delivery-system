import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import 'package:url_launcher/url_launcher.dart';

import '../components/AddUserDialog.dart';
import '../components/UserTypeComponent.dart';
import '../extensions/app_text_field.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/UserModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/StringExtensions.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';
import 'NotificationScreen.dart';
import 'UserDetailScreen.dart';

class UserListFragment extends StatefulWidget {
  @override
  UserListFragmentState createState() => UserListFragmentState();
}

class UserListFragmentState extends State<UserListFragment> {
  ScrollController controller = ScrollController();
  int currentPage = 1;
  int totalPage = 1;
  int currentIndex = 1;
  TextEditingController searchUserCont = TextEditingController();

  List<UserModel> userData = [];
  bool isSelectAll = false;
  List<int> userChecked = [];
  String? userType;


  @override
  void initState() {
    super.initState();
    init();
    controller.addListener(() {
      if (controller.position.pixels == controller.position.maxScrollExtent) {
        if (currentPage < totalPage) {
          currentPage++;
          setState(() {});
          getUserListApiCall();
        }
      }
    });
  }

  void init() async {
    appStore.setLoading(true);
    afterBuildCreated(() {
      getUserListApiCall();
    });
  }

  getUserListApiCall({String? searchText}) async {
    appStore.setLoading(true);
    await getAllUserList(type: CLIENT, page: currentPage,user_status: getStatusByUserType(), searchText: searchText).then((value) {
      totalPage = value.pagination!.totalPages!;
      currentPage = value.pagination!.currentPage!;
      if (currentPage == 1) {
        userData.clear();
      }
      userData.addAll(value.data!);
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
    });
  }

  String? getStatusByUserType() {
    print("user tyep ===>$userType");
    if (userType == language.active) {
      return "active";
    } else if (userType == "InActive") {
      return "inactive";
    } else if (userType == language.pending) {
      return "pending";
    } else {
      return null;
    }
  }

  updateStatusApiCall(UserModel userData) async {
    Map req = {
      "id": userData.id,
      "status": userData.status == 1 ? 0 : 1,
    };
    appStore.setLoading(true);
    await updateUserStatus(req).then((value) {
      appStore.setLoading(false);
      getUserListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteUserApiCall(int id) async {
    Map req = {"id": id};
    appStore.setLoading(true);
    await deleteUser(req).then((value) {
      appStore.setLoading(false);
      getUserListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  deleteMultipleUserApiCall(List<int> userdata) async {
    Map req = {
      "ids": userChecked,
    };
    appStore.setLoading(true);
    await multipleDeleteUser(req).then((value) {
      appStore.setLoading(false);
      getUserListApiCall();
      userChecked.clear();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  restoreUserApiCall({@required int? id, @required String? type}) async {
    Map req = {"id": id, "type": type};
    appStore.setLoading(true);
    await userAction(req).then((value) {
      appStore.setLoading(false);
      getUserListApiCall();
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

  _onSelected(bool selected, int id) {
    if (selected == true) {
      setState(() {
        userChecked.remove(id);
      });
    } else {
      setState(() {
        userChecked.add(id);
      });
    }
  }

  //Select all Checkbox
  _onChangedProperty() {
    userChecked.clear();
    for (int i = 0; i < userData.length; i++) {
      if (isSelectAll == true) {
        userChecked.add(userData[i].id!);
      } else {
        if (userChecked.isNotEmpty) {
          userChecked.remove(userData[i].id!);
        }
      }
    }
    setState(() {});
  }

  mobileNumberVerification({int? userId}) {
    Map req = {
      "id": userId,
      "otp_verify_at": DateTime.now().toString(),
    };
    updateUserStatus(req).then((value) {
      finish(context);
      toast(value.message);
      getUserListApiCall();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Observer(
      builder: (_) => Scaffold(
        backgroundColor: appStore.isDarkMode ? black : whiteColor,
        appBar: appBarWidget(language.users, showBack: false, actions: [
          Icon(MaterialCommunityIcons.select_multiple, color: white, size: 26).paddingRight(10).onTap(() {
            setState(() {
              isSelectAll = !isSelectAll;
              _onChangedProperty();
            });
          }).visible(userChecked.length > 0),
          Icon(MaterialCommunityIcons.delete, color: white, size: 24).paddingRight(0).onTap(() {
            commonConfirmationDialog(
              context,
              DIALOG_TYPE_DELETE,
              title: language.deleteUser,
              subtitle: language.deleteSelectedUsers,
              () {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  Navigator.pop(context);
                  deleteMultipleUserApiCall(userChecked);
                }
              },
            );
          }).visible(userChecked.length > 0),
          Stack(
            children: [
              Icon(Ionicons.md_options_outline, color: Colors.white),
            ],
          ).withWidth(20).paddingSymmetric(horizontal: 5).onTap(() async {
            final result = await showDialog(
              context: context,
              builder: (BuildContext dialogContext) {
                currentPage = 1;
                return UserTypeComponent(
                  userType: userType.validate(),
                );
              },
            );
            if (result != null) {
              userType = result["type"];
              getUserListApiCall();
            }
          }, splashColor: Colors.transparent, hoverColor: Colors.transparent, highlightColor: Colors.transparent),
          Observer(
            builder: (_) => SizedBox(
              width: 55,
              child: Stack(
                children: [
                  Align(
                    alignment: AlignmentDirectional.center,
                    child: Icon(Icons.notifications, color: Colors.white),
                  ),
                  if (appStore.allUnreadCount != 0)
                    Positioned(
                      right: 10,
                      top: 8,
                      child: Container(
                        height: 20,
                        width: 20,
                        alignment: Alignment.center,
                        decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle),
                        child: Observer(builder: (_) {
                          return Text('${appStore.allUnreadCount < 99 ? appStore.allUnreadCount : '99+'}', style: primaryTextStyle(size: appStore.allUnreadCount > 99 ? 9 : 12, color: Colors.white));
                        }),
                      ),
                    ),
                ],
              ).onTap(() {
                Navigator.push(context, MaterialPageRoute(builder: (_) => NotificationScreen()));
              }),
            ),
          ),
        ]),
        body: Stack(
          fit: StackFit.expand,
          children: [
            Column(
              children: [
                16.height,
                AppTextField(
                  textFieldType: TextFieldType.NAME,
                  controller: searchUserCont,
                  decoration: commonInputDecoration(prefixIcon: Icon(Icons.search), hintText: language.search),
                  onChanged: (v) async {
                    currentPage = 1;
                    getUserListApiCall(searchText: v);
                  },
                ).paddingOnly(left: 16, right: 16),
                16.height,
                ListView.builder(
                    padding: EdgeInsets.only(left: 16, right: 16),
                    controller: controller,
                    itemCount: userData.length,
                    itemBuilder: (context, i) {
                      UserModel mData = userData[i];
                      return GestureDetector(
                        onTap: () async {
                          bool? res = await launchScreen(context, UserDetailScreen(userId: mData.id, userType: mData.userType));
                          if (res ?? false) {
                            currentPage = 1;
                            getUserListApiCall();
                          }
                        },
                        onLongPress: () {
                          setState(() {
                            _onSelected(userChecked.contains(mData.id), mData.id!);
                          });
                        },
                        child: Container(
                          margin: EdgeInsets.only(bottom: 16),
                          decoration: boxDecorationWithRoundedCorners(
                              backgroundColor: userChecked.contains(mData.id)
                                  ? Colors.red.shade200.withOpacity(0.2)
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
                                    Text('#${mData.id ?? "-"}', style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor)),
                                    Spacer(),
                                    mData.deletedAt == null
                                        ? mData.otpVerifyAt.isEmptyOrNull
                                            ? outlineActionIcon(context, Icons.mobile_friendly, appStore.isDarkMode ? white : primaryColor, () {
                                                commonConfirmationDialog(context, DIALOG_TYPE_VERIFY, () {
                                                  if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                                    toast(language.demo_admin_msg);
                                                  } else {
                                                    mobileNumberVerification(userId: mData.id);
                                                  }
                                                }, title: '${language.mobileNumberVerify} ?', subtitle: '${mData.contactNumber}\n${language.mobileNumberVerifyMsg}');
                                              })
                                            : SizedBox()
                                        : Text('-', textAlign: TextAlign.center),
                                    8.width,
                                    GestureDetector(
                                      child: Container(
                                        alignment: Alignment.center,
                                        padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                        margin: EdgeInsets.only(right: 8, left: appStore.selectedLanguage == "ar" ? 8 : 0),
                                        child: Text(
                                          '${mData.status == 1 ? language.enable : language.disable}',
                                          style: primaryTextStyle(
                                              color: mData.status == 1
                                                  ? appStore.isDarkMode
                                                      ? white
                                                      : primaryColor
                                                  : Colors.red,
                                              size: 14),
                                        ),
                                        decoration: BoxDecoration(
                                            border: Border.all(
                                                color: mData.status == 1
                                                    ? appStore.isDarkMode
                                                        ? white
                                                        : primaryColor.withOpacity(0.6)
                                                    : Colors.red.withOpacity(0.6)),
                                            color: mData.status == 1 ? primaryColor.withOpacity(0.15) : Colors.red.withOpacity(0.15),
                                            borderRadius: BorderRadius.circular(defaultRadius)),
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
                                              }, title: mData.status != 1 ? language.enableUser : language.disableUser, subtitle: mData.status != 1 ? language.enableUserMsg : language.disableUserMsg)
                                            : toast(language.youCannotUpdateStatusRecordDeleted);
                                      },
                                    ),
                                    Row(
                                      children: [
                                        outlineActionIcon(context, mData.deletedAt == null ? Icons.edit : Icons.restore, Colors.green, () {
                                          mData.deletedAt == null
                                              ? showDialog(
                                                  context: context,
                                                  useSafeArea: true,
                                                  barrierDismissible: false,
                                                  builder: (BuildContext dialogContext) {
                                                    return AddUserDialog(
                                                      userData: mData,
                                                      userType: CLIENT,
                                                      onUpdate: () {
                                                        currentPage = 1;
                                                        init();
                                                        setState(() {});
                                                      },
                                                    );
                                                  },
                                                )
                                              : commonConfirmationDialog(context, DIALOG_TYPE_RESTORE, () {
                                                  if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                                    toast(language.demoAdminMsg);
                                                  } else {
                                                    Navigator.pop(context);
                                                    restoreUserApiCall(id: mData.id, type: RESTORE);
                                                  }
                                                }, title: language.restoreUser, subtitle: language.restoreUserMsg);
                                        }),
                                        SizedBox(width: 8),
                                      ],
                                    ),
                                    outlineActionIcon(context, mData.deletedAt == null ? Icons.delete : Icons.delete_forever, Colors.red, () {
                                      commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () {
                                        if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                          toast(language.demoAdminMsg);
                                        } else {
                                          Navigator.pop(context);
                                          mData.deletedAt == null ? deleteUserApiCall(mData.id!) : restoreUserApiCall(id: mData.id, type: FORCE_DELETE);
                                        }
                                      }, isForceDelete: mData.deletedAt != null, title: language.deleteUser, subtitle: language.deleteUserMsg);
                                    }),
                                  ],
                                ),
                              ),
                              Padding(
                                padding: EdgeInsets.all(12),
                                child: Column(
                                  children: [
                                    Row(
                                      children: [
                                        Container(
                                          height: 60,
                                          width: 60,
                                          decoration: BoxDecoration(
                                            border: Border.all(color: Colors.grey.withOpacity(0.15)),
                                            shape: BoxShape.circle,
                                            image: DecorationImage(image: NetworkImage('${mData.profileImage!}'), fit: BoxFit.cover),
                                          ),
                                        ),
                                        SizedBox(width: 8),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text('${mData.name ?? ""}', style: boldTextStyle()),
                                              SizedBox(height: 6),
                                              Text(mData.email.validate(), style: secondaryTextStyle()),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                    SizedBox(height: 8),
                                    GestureDetector(
                                      onTap: () {
                                        launchUrl(Uri.parse('tel:${mData.contactNumber.validate()}'));
                                      },
                                      child: Row(
                                        children: [
                                          Icon(Icons.call, color: Colors.green, size: 20),
                                          SizedBox(width: 8),
                                          Text(mData.contactNumber.validate(), style: primaryTextStyle(size: 14)),
                                        ],
                                      ),
                                    ).visible(!mData.contactNumber.isEmptyOrNull),
                                    SizedBox(height: 8),
                                    if (mData.cityName != null || mData.countryName != null)
                                      Row(
                                        children: [
                                          Icon(Icons.location_city, color: appStore.isDarkMode ? white : primaryColor, size: 20),
                                          SizedBox(width: 8),
                                          Text(mData.cityName.validate() + " ," + mData.countryName.validate(), style: primaryTextStyle(size: 14)),
                                        ],
                                      ),
                                    if (mData.cityName != null || mData.countryName != null) SizedBox(height: 8),
                                    Row(
                                      children: [
                                        Icon(
                                          Entypo.calendar,
                                          color: appStore.isDarkMode ? white : primaryColor,
                                          size: 20,
                                        ),
                                        SizedBox(width: 8),
                                        Text(printDate(mData.createdAt.validate()), style: primaryTextStyle(size: 14)),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    }).expand(),
              ],
            ),
            Positioned(
              bottom: 16,
              right: 16,
              child: FloatingActionButton(
                backgroundColor: primaryColor,
                child: Icon(Icons.add, color: Colors.white),
                onPressed: () {
                  showDialog(
                    context: context,
                    barrierDismissible: false,
                    builder: (BuildContext dialogContext) {
                      return AddUserDialog(
                        userType: CLIENT,
                        onUpdate: () {
                          currentPage = 1;
                          init();
                          // setState(() {});
                        },
                      );
                    },
                  );
                },
              ),
            ),
            appStore.isLoading
                ? loaderWidget()
                : userData.isEmpty
                    ? emptyWidget()
                    : SizedBox()
          ],
        ),
      ),
    );
  }
}
