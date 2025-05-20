import 'dart:io';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../utils/Colors.dart';
import '../extensions/app_text_field.dart';
import '../extensions/common.dart';
import '../extensions/decorations.dart';
import '../main.dart';
import '../models/ClaimsListModel.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import 'ClaimsDetailScreen.dart';

class ClaimListScreen extends StatefulWidget {
  final String? status;

  ClaimListScreen({this.status});

  @override
  State<ClaimListScreen> createState() => _ClaimListScreenState();
}

class _ClaimListScreenState extends State<ClaimListScreen> {
  List<ClaimItem> claimList = [];
  ScrollController scrollController = ScrollController();
  int page = 1;
  int totalPage = 1;
  TextEditingController claimAmountController = TextEditingController();
  TextEditingController claimDescriptionController = TextEditingController();
  GlobalKey<FormState> claimFormKey = GlobalKey<FormState>();
  List<PlatformFile>? selectedFiles;

  Future<void> selectProofData(BuildContext context, int claimId) async {
    showDialog(
        context: context,
        barrierDismissible: true,
        builder: (BuildContext dialogContext) {
          return AlertDialog(
            content: StatefulBuilder(builder: (context, selectedImagesUpdate) {
              return Form(
                key: claimFormKey,
                child: SingleChildScrollView(
                  child: Container(
                    constraints: BoxConstraints(
                      minHeight: 200.0, // Set your minimum height here
                    ),
                    child: Stack(
                      children: [
                        !appStore.isLoading
                            ? Column(
                                mainAxisSize: MainAxisSize.min,
                                mainAxisAlignment: MainAxisAlignment.start,
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(language.claim, style: boldTextStyle(), textAlign: TextAlign.start),
                                  8.height,
                                  Divider(height: 1),
                                  8.height,
                                  Text(language.amount, style: boldTextStyle()),
                                  12.height,
                                  AppTextField(
                                    isValidationRequired: true,
                                    controller: claimAmountController,
                                    textFieldType: TextFieldType.NAME,
                                    errorThisFieldRequired: language.fieldRequiredMsg,
                                    decoration: commonInputDecoration(hintText: language.amount),
                                  ),
                                  8.height,
                                  Text(language.description, style: boldTextStyle()),
                                  12.height,
                                  AppTextField(
                                    isValidationRequired: true,
                                    controller: claimDescriptionController,
                                    textFieldType: TextFieldType.NAME,
                                    errorThisFieldRequired: language.fieldRequiredMsg,
                                    minLines: 4,
                                    maxLines: 8,
                                    decoration: commonInputDecoration(hintText: language.description),
                                  ),
                                  8.height,
                                  if (selectedFiles != null && selectedFiles!.length > 0) Text(language.selectedFiles, style: boldTextStyle()),
                                  if (selectedFiles != null && selectedFiles!.length > 0) 10.height,
                                  if (selectedFiles != null && selectedFiles!.length > 0)
                                    Container(
                                      width: context.width(),
                                      height: 120,
                                      child: ListView.builder(
                                        scrollDirection: Axis.horizontal,
                                        itemCount: selectedFiles!.length,
                                        itemBuilder: (context, index) {
                                          return buildFileWidget(selectedFiles![index]);
                                        },
                                      ),
                                    ),
                                  16.height,
                                  Row(
                                    children: [
                                      dialogPrimaryButton("Attach file", () async {
                                        if (selectedFiles != null) selectedFiles!.clear();
                                        FilePickerResult? result = await FilePicker.platform.pickFiles(
                                          allowMultiple: true,
                                          type: FileType.custom,
                                          allowedExtensions: ['jpg', 'jpeg', 'png', 'pdf'],
                                        );
                                        if (result != null) {
                                          selectedImagesUpdate(() {
                                            selectedFiles = result.files;
                                          });
                                        }
                                      }).expand(),
                                      6.width,
                                      dialogPrimaryButton(language.save, () async {
                                        if (claimFormKey.currentState!.validate()) {
                                          selectedImagesUpdate(() {
                                            appStore.setLoading(true);
                                          });
                                          hideKeyboard(context);

                                          MultipartRequest multiPartRequest = await getMultiPartRequest('status-details');
                                          multiPartRequest.fields['amount'] = claimAmountController.text;
                                          multiPartRequest.fields['description'] = claimDescriptionController.text;
                                          multiPartRequest.fields['claim_id'] = claimId.toString();
                                          if (selectedFiles != null && selectedFiles!.length > 0) {
                                            selectedFiles!.forEach((element) async {
                                              multiPartRequest.files.add(await MultipartFile.fromPath("attachment_resolve_file[]", element.path!));
                                            });
                                          }
                                          print("---------------request${multiPartRequest.toString()}");

                                          multiPartRequest.headers.addAll(buildHeaderTokens());
                                          sendMultiPartRequest(
                                            multiPartRequest,
                                            onSuccess: (data) async {
                                              print("onsuccess call");
                                              if (data != null && mounted) {
                                                appStore.setLoading(false);
                                                // toast(data["message"]);
                                                if (dialogContext.mounted) {
                                                  Navigator.pop(dialogContext);
                                                }
                                                init();
                                              }
                                            },
                                            onError: (error) {
                                              toast(error.toString(), print: true);
                                              appStore.setLoading(false);
                                            },
                                          ).catchError((e) {
                                            appStore.setLoading(false);
                                            toast(e.toString());
                                          });
                                          //   claimInsuranceVehicleApiCall();
                                        }
                                        finish(context, 0);
                                      }),
                                    ],
                                  ),
                                ],
                              )
                            : Observer(builder: (context) => loaderWidget().visible(appStore.isLoading)).center(),
                      ],
                    ),
                  ),
                ),
              );
            }),
          );
        });
  }

  Future<void> changeClaimStatusApi(int id, int status) async {
    Map req = {'id': id, 'status': status};
    appStore.setLoading(true);
    await changeClaimStatus(req).then((value) {
      appStore.setLoading(false);
      toast(value.message);
      page = 1;
      init();
      setState(() {});
    });
  }

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
    getClaimListApiCall();
  }

  Future<void> getClaimListApiCall() async {
    appStore.setLoading(true);
    await getClaimsList(page: page, status: widget.status.validate()).then((value) {
      appStore.setLoading(false);
      totalPage = value.pagination!.totalPages!.validate(value: 1);
      page = value.pagination!.currentPage!.validate(value: 1);
      if (page == 1) {
        claimList.clear();
      }
      value.data!.forEach((element) {
        claimList.add(element);
      });

      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      print("error ===> ${error.toString()}");
      appStore.setLoading(false);
    });
  }

  Widget buildFileWidget(PlatformFile file) {
    bool isImage = file.extension == 'jpg' || file.extension == 'jpeg' || file.extension == 'png';

    return Stack(
      children: [
        Container(
          width: 100,
          height: 100,
          decoration: boxDecorationWithRoundedCorners(border: Border.all(color: primaryColor)),
          child: isImage
              ? Image.file(
                  width: 100, height: 100,
                  File(file.path!), // File object for local image display
                  fit: BoxFit.cover,
                ).cornerRadiusWithClipRRect(10)
              : Center(
                  child: Icon(
                    Icons.picture_as_pdf,
                    size: 40,
                    color: Colors.red,
                  ),
                ),
        ).paddingOnly(left: 8, right: 8)
      ],
    );
  }

  Widget claimWidget(ClaimItem item) {
    return Container(
      margin: EdgeInsets.only(bottom: 16),
      padding: EdgeInsets.all(8),
      decoration: boxDecorationWithRoundedCorners(borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: appStore.isDarkMode ? Colors.grey.withOpacity(0.3) : primaryColor.withOpacity(0.4)), backgroundColor: Colors.transparent),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Row(
                children: [
                  Text("${language.id} :", style: boldTextStyle()),
                  Text(item.id.validate().toString(), style: boldTextStyle()),
                ],
              ),
              Spacer(),
              Container(
                alignment: Alignment.center,
                padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                margin: EdgeInsets.only(right: 4, left: appStore.selectedLanguage == "ar" ? 4 : 0),
                child: getClaimStatus(
                  item.status!.validate(),
                ),
                decoration: BoxDecoration(
                    border: Border.all(
                      color: getClaimStatusColor(item.status!.validate(), 0.6),
                    ),
                    color: getClaimStatusColor(item.status!.validate(), 0.15),
                    borderRadius: BorderRadius.circular(defaultRadius)),
              ),
              claimStatusButton(item, () {
                changeClaimStatusApi(item.id.validate(), 1);
              }, () {
                changeClaimStatusApi(item.id.validate(), 0);
              }, () {
                selectProofData(context, item.id.validate());
              }),
            ],
          ),
          8.height,
          Row(
            children: [
              Text(language.trackingNo, style: primaryTextStyle()),
              Text(item.trakingNo.validate(), style: primaryTextStyle()),
            ],
          ),
        ],
      ),
    ).onTap(() {
      ClaimDetailScreen(item).launch(context).then((val) {
        page = 1;
        init();
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBarWidget(language.claimHistory),
      body: WillPopScope(
        onWillPop: () {
          resetMenuIndex();
          Navigator.pop(context, true);
          return Future.value(true);
        },
        child: Observer(builder: (context) {
          return Stack(
            children: [
              claimList.isNotEmpty
                  ? ListView.builder(
                      itemCount: claimList.length,
                      shrinkWrap: true,
                      controller: scrollController,
                      padding: EdgeInsets.fromLTRB(16, 16, 16, 0),
                      itemBuilder: (context, index) {
                        ClaimItem item = claimList[index];
                        return claimWidget(item);
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

Widget claimStatusButton(ClaimItem item, Function approve, Function reject, Function selectProof) {
  return Row(
    children: [
      if (item.status.validate() == PENDING.toLowerCase()) ...[
        5.width,
        Container(
          decoration: boxDecorationWithRoundedCorners(borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: completedColor), backgroundColor: completedColor),
          padding: EdgeInsets.symmetric(horizontal: 4, vertical: 4),
          child: Icon(
            Icons.check,
            color: Colors.white,
            size: 20,
          ),
        ).onTap(() {
          approve.call();
        }),
        5.width,
        Container(
          decoration: boxDecorationWithRoundedCorners(borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: pendingColor), backgroundColor: pendingColor),
          padding: EdgeInsets.symmetric(horizontal: 4, vertical: 4),
          child: Icon(
            Icons.close,
            color: Colors.white,
            size: 20,
          ),
        ).onTap(() {
          reject.call();
        }),
      ],
      if (item.status.validate() == APPROVED) ...[
        5.width,
        Container(
          decoration: boxDecorationWithRoundedCorners(
            borderRadius: BorderRadius.circular(defaultRadius),
            border: Border.all(color: primaryColor),
            backgroundColor: primaryColor,
          ),
          padding: EdgeInsets.symmetric(horizontal: 4, vertical: 4),
          child: Icon(
            Icons.add,
            color: Colors.white,
            size: 20,
          ),
        ).onTap(() {
          selectProof.call();
        }),
      ],
    ],
  );
}
