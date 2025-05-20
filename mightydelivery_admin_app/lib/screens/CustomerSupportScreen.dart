import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../utils/Colors.dart';
import '../extensions/app_text_field.dart';
import '../extensions/decorations.dart';
import '../main.dart';
import '../models/CustomerSupportListModel.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import 'ChatWithUserScreen.dart';
import 'CustomerSupportDetailScreen.dart';

class CustomerSupportScreen extends StatefulWidget {
  final String? status;

  CustomerSupportScreen({this.status});

  @override
  State<CustomerSupportScreen> createState() => _CustomerSupportScreenState();
}

class _CustomerSupportScreenState extends State<CustomerSupportScreen> {
  List<CustomerSupport> supportList = [];
  ScrollController scrollController = ScrollController();
  int page = 1;
  int totalPage = 1;

  @override
  void initState() {
    super.initState();
    init();
    scrollController.addListener(() {
      if (scrollController.position.pixels == scrollController.position.maxScrollExtent && !appStore.isLoading) {
        if (page < totalPage) {
          page++;
          appStore.setLoading(true);
          init();
        }
      }
    });
  }

  void init() {
    getCustomerSupportListApi();
  }

  Future<void> getCustomerSupportListApi() async {
    appStore.setLoading(true);
    await getCustomerSupportList(page: page, status: widget.status.validate()).then((value) {
      appStore.setLoading(false);
      totalPage = value.pagination!.totalPages.validate(value: 1);
      page = value.pagination!.currentPage.validate(value: 1);
      if (page == 1) {
        supportList.clear();
      }
      supportList.addAll(value.customerSupport!);
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
    });
  }

  getStatus(String status) {
    if (status == SUPPORT_TICKET_STATUS_PENDING) {
      return Text(status, style: boldTextStyle(color: pendingColor));
    } else if (status == SUPPORT_TICKET_STATUS_IN_REVIEW) {
      return Text(status, style: boldTextStyle(color: pendingColor));
    } else {
      return Text(status, style: boldTextStyle(color: completedColor));
    }
  }

  getStatusColor(String status, double opacity) {
    if (status == SUPPORT_TICKET_STATUS_PENDING || status == SUPPORT_TICKET_STATUS_IN_REVIEW) {
      return pendingColor.withOpacity(opacity);
    } else {
      return completedColor.withOpacity(opacity);
    }
  }

  deleteCustomerSupportApiCall(int id) async {
    appStore.setLoading(true);
    await deleteCustomerSupport(id).then((value) {
      appStore.setLoading(false);
      getCustomerSupportListApi();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
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
        appBar: appBarWidget(language.customerSupport),
        body: Observer(builder: (context) {
          return Stack(
            children: [
              supportList.isNotEmpty
                  ? ListView.builder(
                      itemCount: supportList.length,
                      shrinkWrap: true,
                      controller: scrollController,
                      padding: EdgeInsets.fromLTRB(16, 16, 16, 0),
                      itemBuilder: (context, index) {
                        CustomerSupport item = supportList[index];
                        return Container(
                          decoration: boxDecorationWithRoundedCorners(border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
                          margin: EdgeInsets.only(bottom: 8),
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
                                    Text('#${item.supportId.validate().toString()}', style: boldTextStyle(color: appStore.isDarkMode ? Colors.white : primaryColor)),
                                    Spacer(),
                                    GestureDetector(
                                      child: Container(
                                        alignment: Alignment.center,
                                        padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                        margin: EdgeInsets.only(right: 8, left: appStore.selectedLanguage == "ar" ? 8 : 0),
                                        child: getStatus(item.status.validate()),
                                        decoration: BoxDecoration(
                                            border: Border.all(
                                              color: getStatusColor(item.status.validate(), 0.6),
                                            ),
                                            color: getStatusColor(item.status.validate(), 0.15),
                                            borderRadius: BorderRadius.circular(defaultRadius)),
                                      ),
                                      onTap: () {
                                        showDialog(
                                          context: context,
                                          builder: (context) {
                                            return CustomerSupportStatusDialog(
                                                supportId: item.supportId.validate(),
                                                onUpdate: () {
                                                  getCustomerSupportListApi();
                                                });
                                          },
                                        );
                                      },
                                    ),
                                    Row(
                                      children: [
                                        outlineActionIcon(context, Icons.chat, Colors.green, () {
                                          ChatWithUserScreen(item.supportChatHistory, item.supportId).launch(context);
                                        }),
                                        SizedBox(width: 8),
                                      ],
                                    ),
                                    outlineActionIcon(context, Icons.delete, Colors.red, () {
                                      commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () {
                                        finish(context);
                                        deleteCustomerSupportApiCall(item.supportId.validate());
                                      }, title: language.deleteCustomerSupport, subtitle: language.pageDeleteConfirmMessage);
                                    }),
                                  ],
                                ),
                              ),
                              Padding(
                                padding: EdgeInsets.all(12),
                                child: Row(
                                  children: [
                                    Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          children: [
                                            Text("${language.user} :", style: primaryTextStyle()),
                                            Text(item.userName.validate(value: "--"), style: primaryTextStyle()),
                                          ],
                                        ),
                                        8.height,
                                        Row(
                                          children: [
                                            Text("${language.supportType}", style: primaryTextStyle()),
                                            Text(item.supportType.validate(), style: primaryTextStyle()),
                                          ],
                                        ),
                                        8.height,
                                        Row(
                                          children: [
                                            Text('${language.message} :', style: primaryTextStyle()),
                                            Text(item.message.validate(), style: primaryTextStyle()),
                                          ],
                                        ),
                                        8.height,
                                        Row(
                                          children: [
                                            Text("${language.attachment} :", style: primaryTextStyle()),
                                            10.width,
                                            Text((item.video.isEmptyOrNull) ? language.viewPhoto : language.viewVideo)
                                                .onTap(() {
                                              CustomerSupportDetailsScreen(item.video.toString(), item.image.toString()).launch(context);
                                            }),
                                          ],
                                        ),
                                        if (item.resolutionDetail != null) 8.height,
                                        if (item.resolutionDetail != null)
                                          Row(
                                            children: [
                                              Text("${language.resolutionDetail} :", style: primaryTextStyle()),
                                              10.width,
                                              Text(item.resolutionDetail.validate(), style: primaryTextStyle()),
                                            ],
                                          ),
                                      ],
                                    ).expand(),
                                  ],
                                ).onTap(() {
                                  // todo
                                }),
                              ),
                            ],
                          ),
                        );
                      },
                    )
                  : !appStore.isLoading
                      ? emptyWidget()
                      : SizedBox(),
              loaderWidget().center().visible(appStore.isLoading),
            ],
          );
        }),
      ),
    );
  }
}

class CustomerSupportStatusDialog extends StatefulWidget {
  final int? supportId;
  final Function()? onUpdate;

  CustomerSupportStatusDialog({this.supportId, this.onUpdate});

  @override
  CustomerSupportStatusDialogState createState() => CustomerSupportStatusDialogState();
}

class CustomerSupportStatusDialogState extends State<CustomerSupportStatusDialog> {
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  TextEditingController resolutionDetailCont = TextEditingController();

  List<String> typeList = [SUPPORT_TICKET_STATUS_PENDING, SUPPORT_TICKET_STATUS_IN_REVIEW, SUPPORT_TICKET_RESOLVED];
  String selectedType = SUPPORT_TICKET_STATUS_PENDING;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    //
  }

  saveCustomerSupportStatusApiCall() async {
    Map req = {
      "resolution_detail": resolutionDetailCont.text.validate(),
      "status": selectedType,
    };
    appStore.setLoading(true);
    await CustomerSupportStatusSave(widget.supportId.validate(), req).then((value) {
      appStore.setLoading(false);
      toast(value.message.toString());
      Navigator.pop(context);
      widget.onUpdate?.call();
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  String printStatus(status) {
    if (status == SUPPORT_TICKET_STATUS_PENDING) {
      return language.pending;
    } else if (status == SUPPORT_TICKET_STATUS_IN_REVIEW) {
      return "InReview";
    } else if (status == SUPPORT_TICKET_RESOLVED) {
      return "Resolved";
    } else {
      return "";
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      actionsPadding: EdgeInsets.only(right: 16, bottom: 16),
      title: Text(language.customerSupportDetails,
          style: boldTextStyle(
            size: 20,
            color: appStore.isDarkMode ? Colors.white : primaryColor,
          )),
      content: Stack(
        children: [
          SizedBox(
            width: MediaQuery.of(context).size.width,
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(language.resolutionDetail, style: primaryTextStyle()),
                  SizedBox(height: 8),
                  AppTextField(
                    controller: resolutionDetailCont,
                    textFieldType: TextFieldType.OTHER,
                    decoration: commonInputDecoration(),
                    errorThisFieldRequired: language.field_required_msg,
                  ),
                  16.height,
                  Text(language.status, style: primaryTextStyle()),
                  SizedBox(height: 8),
                  DropdownButtonFormField<String>(
                    dropdownColor: Theme.of(context).cardColor,
                    value: selectedType,
                    decoration: commonInputDecoration(),
                    items: typeList.map<DropdownMenuItem<String>>((item) {
                      return DropdownMenuItem(value: item, child: Text(printStatus(item), style: primaryTextStyle()));
                    }).toList(),
                    onChanged: (value) {
                      selectedType = value.validate();
                      setState(() {});
                    },
                    validator: (value) {
                      if (selectedType.isEmptyOrNull) return language.field_required_msg;
                      return null;
                    },
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
      actions: <Widget>[
        dialogSecondaryButton(language.cancel, () {
          Navigator.pop(context);
        }),
        SizedBox(width: 4),
        dialogPrimaryButton(language.save, () async {
          if (_formKey.currentState!.validate()) {
            saveCustomerSupportStatusApiCall();
          }
        }).paddingOnly(left: appStore.selectedLanguage == "ar" ? 18 : 0, right: 8)
      ],
    );
  }
}
