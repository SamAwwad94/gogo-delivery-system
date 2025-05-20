import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:http/http.dart';
import '../extensions/decorations.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import 'package:url_launcher/url_launcher.dart';

import '../extensions/colors.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/DeliveryDocumentListModel.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';

class DeliveryPersonDocumentScreen extends StatefulWidget {
  static String tag = '/DeliveryPersonDocumentScreen';

  final int? deliveryManId;

  DeliveryPersonDocumentScreen({this.deliveryManId});

  @override
  DeliveryPersonDocumentScreenState createState() => DeliveryPersonDocumentScreenState();
}

class DeliveryPersonDocumentScreenState extends State<DeliveryPersonDocumentScreen> {
  ScrollController scrollController = ScrollController();

  int currentPage = 1;
  int totalPage = 1;
  int perPage = 10;
  bool isSelectAll = false;
  List<int> documentsChecked = [];

  List<DeliveryDocumentData> deliveryDocList = [];
  List<String> document = [PENDING, APPROVEDText, REJECTED];
  int selected = 0;

  @override
  void initState() {
    super.initState();
    init();
    scrollController.addListener(() {
      if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
        if (currentPage < totalPage) {
          currentPage++;
          setState(() {});
          getDocumentListApiCall();
        }
      }
    });
  }

  Future<void> init() async {
    afterBuildCreated(() {
      appStore.setLoading(true);
      getDocumentListApiCall();
    });
  }

  /// Verify Documents
  verifyDocument(int docId) async {
    print("document ${docId.toString()} ${selected.toString()}");
    MultipartRequest multiPartRequest = await getMultiPartRequest('delivery-man-document-save');
    multiPartRequest.fields["id"] = docId.toString();
    multiPartRequest.fields["is_verified"] = selected.toString();
    multiPartRequest.headers.addAll(buildHeaderTokens());
    print("document -> ${multiPartRequest.fields.toString()}");
    appStore.setLoading(true);
    sendMultiPartRequest(
      multiPartRequest,
      onSuccess: (data) async {
        appStore.setLoading(false);
        getDocumentListApiCall();
      },
      onError: (error) {
        print("error ${error.toString()}");
        toast(error.toString());
        appStore.setLoading(false);
      },
    ).catchError((e) {
      appStore.setLoading(false);
      toast(e.toString());
    });
  }

  /// Delivery Document List
  getDocumentListApiCall() async {
    appStore.setLoading(true);
    await getDeliveryDocumentList(page: currentPage, isDeleted: true, deliveryManId: widget.deliveryManId, perPage: perPage).then((value) {
      appStore.setLoading(false);

      totalPage = value.pagination!.totalPages!;
      currentPage = value.pagination!.currentPage!;

      deliveryDocList.clear();
      deliveryDocList.addAll(value.data!);
      if (currentPage != 1 && deliveryDocList.isEmpty) {
        currentPage -= 1;
        getDocumentListApiCall();
      }
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  deleteMultipleDeliveryBoyDocumentsApiCall(List<int> userdata) async {
    Map req = {
      "ids": documentsChecked,
    };
    appStore.setLoading(true);
    await multipleDeleteDocumentList(req).then((value) {
      appStore.setLoading(false);
      getDocumentListApiCall();
      documentsChecked.clear();
      //   deletealltext = true;
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  int docDataValue(String name) {
    if (name == PENDING) {
      return 0;
    } else if (name == APPROVEDText) {
      return 1;
    } else if (name == REJECTED) {
      return 2;
    }
    return 0;
  }

  // Single Select Checkbox
  _onSelected(bool selected, int id) {
    if (selected == true) {
      setState(() {
        documentsChecked.remove(id);
      });
    } else {
      setState(() {
        documentsChecked.add(id);
      });
    }
  }

  //Select all Checkbox
  _onChangedProperty() {
    documentsChecked.clear();
    for (int i = 0; i < deliveryDocList.length; i++) {
      if (isSelectAll == true) {
        documentsChecked.add(deliveryDocList[i].id!);
      } else {
        if (documentsChecked.isNotEmpty) {
          documentsChecked.remove(deliveryDocList[i].id!);
        }
      }
    }
    setState(() {});
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
        appBar: appBarWidget(language.deliveryPersonDocuments, actions: [
          Icon(MaterialCommunityIcons.select_multiple, color: white, size: 24).paddingRight(8).onTap(() {
            setState(() {
              isSelectAll = !isSelectAll;
              _onChangedProperty();
            });
          }).visible(documentsChecked.length > 0),
          Icon(MaterialCommunityIcons.delete, color: white, size: 24).paddingRight(16).paddingLeft(appStore.selectedLanguage == "ar" ? 16 : 0).onTap(() {
            commonConfirmationDialog(
              context,
              DIALOG_TYPE_DELETE,
              title: language.deleteDeliveryBoyDocs,
              subtitle: language.deleteDeliveryBoyDocsMsg,
              () {
                if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                  toast(language.demo_admin_msg);
                } else {
                  Navigator.pop(context);
                  deleteMultipleDeliveryBoyDocumentsApiCall(documentsChecked);
                }
              },
            );
          }).visible(documentsChecked.length > 0),
        ]),
        body: Observer(builder: (context) {
          return Stack(
            fit: StackFit.expand,
            children: [
              Column(
                children: [
                  10.height,
                  ListView.builder(
                    padding: EdgeInsets.only(left: 16, top: 16, right: 16),
                    itemCount: deliveryDocList.length,
                    itemBuilder: (context, index) {
                      DeliveryDocumentData mData = deliveryDocList[index];
                      return InkWell(
                        onLongPress: () {
                          setState(() {
                            _onSelected(documentsChecked.contains(mData.id), mData.id!);
                          });
                        },
                        child: Container(
                          decoration: boxDecorationWithRoundedCorners(
                              backgroundColor: documentsChecked.contains(mData.id)
                                  ? Colors.red.shade100.withOpacity(0.2)
                                  : appStore.isDarkMode
                                      ? black
                                      : white,
                              border: Border.all(color: Colors.grey.withOpacity(0.3), width: 1)),
                          padding: EdgeInsets.all(12),
                          child: Row(
                            children: [
                              GestureDetector(
                                child: mData.deliveryManDocument!.contains('.pdf')
                                    ? Container(
                                        height: 80,
                                        width: 80,
                                        decoration: containerDecoration(),
                                        child: Icon(Icons.picture_as_pdf, color: Colors.red, size: 50),
                                      )
                                    : ClipRRect(
                                        borderRadius: BorderRadius.circular(16),
                                        child: commonCachedNetworkImage(mData.deliveryManDocument ?? "", fit: BoxFit.cover, height: 80, width: 80),
                                      ),
                                onTap: () async {
                                  launchUrl(
                                    Uri.parse('${mData.deliveryManDocument ?? ""}'),
                                    mode: LaunchMode.externalApplication,
                                  );
                                },
                              ),
                              SizedBox(width: 10),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      crossAxisAlignment: CrossAxisAlignment.end,
                                      children: [
                                        Expanded(child: Text('${mData.documentName ?? ""}', style: boldTextStyle())),
                                        SizedBox(
                                          width: context.width() * 0.30,
                                          child: DropdownButtonFormField<int>(
                                            isExpanded: true,
                                            dropdownColor: Theme.of(context).cardColor,
                                            decoration: commonInputDecoration(),
                                            isDense: true,
                                            value: mData.isVerified ?? selected,
                                            items: document.map((e) {
                                              return DropdownMenuItem(
                                                child: Text(documentData(e), style: secondaryTextStyle(color: textPrimaryColorGlobal)),
                                                value: docDataValue(e),
                                              );
                                            }).toList(),
                                            onChanged: (int? val) {
                                              selected = val!;
                                              if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
                                                toast(language.demo_admin_msg);
                                              } else {
                                                verifyDocument(mData.id!);
                                              }
                                              setState(() {});
                                            },
                                          ),
                                        ),
                                      ],
                                    ),
                                    SizedBox(height: 6),
                                    Row(
                                      children: [
                                        Icon(Icons.person_outline_sharp, size: 18, color: Colors.grey),
                                        SizedBox(width: 4),
                                        Text(mData.deliveryManName ?? "", style: secondaryTextStyle()).expand(),
                                        // Spacer(),
                                        Text('${language.id}: #${mData.id ?? ""}', style: secondaryTextStyle()),
                                      ],
                                    ),
                                    SizedBox(height: 6),
                                    Row(
                                      children: [
                                        Icon(Icons.calendar_today, size: 18, color: Colors.grey),
                                        SizedBox(width: 4),
                                        Text(printDate(mData.createdAt!), style: secondaryTextStyle()),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ).paddingBottom(16),
                      );
                    },
                  ).expand(),
                ],
              ),
              appStore.isLoading
                  ? loaderWidget()
                  : deliveryDocList.isEmpty
                      ? emptyWidget()
                      : SizedBox(),
            ],
          );
        }),
      ),
    );
  }
}
