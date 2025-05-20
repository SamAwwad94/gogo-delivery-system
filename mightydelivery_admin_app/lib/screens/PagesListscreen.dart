import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../network/RestApis.dart';
import '../screens/AddPageScreen.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../extensions/widgets.dart';
import '../main.dart';
import '../models/PageListModel.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';

class PagesListScreen extends StatefulWidget {
  const PagesListScreen({super.key});

  @override
  State<PagesListScreen> createState() => _PagesListScreenState();
}

class _PagesListScreenState extends State<PagesListScreen> {
  ScrollController controller = ScrollController();
  int currentPage = 1;
  int totalPage = 1;
  int currentIndex = 1;
  TextEditingController searchPageCont = TextEditingController();

  List<PageData> pagesList = [];
  bool isSelectAll = false;
  List<int> pageChecked = [];
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
        }
      }
    });
  }

  void init() async {
    appStore.setLoading(true);
    afterBuildCreated(() {
      getPagesListApiCall();
    });
  }

  getPagesListApiCall() async {
    appStore.setLoading(true);
    await getPagesList(currentPage).then((value) {
      totalPage = value.pagination!.totalPages!;
      currentPage = value.pagination!.currentPage!;
      pagesList.clear();
      pagesList.addAll(value.data!);
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
    });
  }

  deletePagesApiCall(int id) async {
    appStore.setLoading(true);
    await deletePages(id).then((value) {
      appStore.setLoading(false);
      getPagesListApiCall();
      toast(value.message.toString());
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  updateStatusApiCall(int id, bool status) async {
    Map req = {
      "status": status == true ? 1 : 0,
    };
    appStore.setLoading(true);
    await updatePages(id, req).then((value) {
      appStore.setLoading(false);
      getPagesListApiCall();
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
        pageChecked.remove(id);
      });
    } else {
      setState(() {
        pageChecked.add(id);
      });
    }
  }

  /*//Select all Checkbox
  _onChangedProperty() {
    pageChecked.clear();
    for (int i = 0; i < pagesList.length; i++) {
      if (isSelectAll == true) {
        pageChecked.add(pagesList[i].id!);
      } else {
        if (pageChecked.isNotEmpty) {
          pageChecked.remove(pagesList[i].id!);
        }
      }
    }
    setState(() {});
  }*/

  @override
  Widget build(BuildContext context) {
    return Observer(
      builder: (_) => WillPopScope(
        onWillPop: () {
          resetMenuIndex();
          Navigator.pop(context, true);
          return Future.value(true);
        },
        child: Scaffold(
          backgroundColor: appStore.isDarkMode ? black : whiteColor,
          appBar: appBarWidget(
            language.pagesList,
          ),
          body: Stack(
            fit: StackFit.expand,
            children: [
              Column(
                children: [
                  16.height,
                  ListView.builder(
                      padding: EdgeInsets.only(left: 16, right: 16),
                      controller: controller,
                      itemCount: pagesList.length,
                      itemBuilder: (context, i) {
                        PageData mData = pagesList[i];
                        return GestureDetector(
                          onTap: () async {},
                          onLongPress: () {
                            setState(() {
                              _onSelected(pageChecked.contains(mData.id), mData.id!);
                            });
                          },
                          child: Container(
                            margin: EdgeInsets.only(bottom: 16),
                            decoration: boxDecorationWithRoundedCorners(
                                backgroundColor: pageChecked.contains(mData.id)
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
                                      Row(
                                        children: [
                                          outlineActionIcon(context, Icons.edit, Colors.green, () {
                                            AddPageScreen(
                                              isUpdate: true,
                                              page: mData,
                                            ).launch(context).then((val) {
                                              if(val != null && val) {
                                                init();
                                              }
                                            });
                                            // HtmlEditorExample(title: "hello",).launch(context);
                                          }),
                                          SizedBox(width: 8),
                                        ],
                                      ),
                                      outlineActionIcon(context, Icons.delete, Colors.red, () {
                                        commonConfirmationDialog(context, DIALOG_TYPE_DELETE, () {
                                          finish(context);
                                          deletePagesApiCall(mData.id.validate());
                                        }, title: language.deletePages, subtitle: language.pageDeleteConfirmMessage);
                                      }),
                                    ],
                                  ),
                                ),
                                Padding(
                                  padding: EdgeInsets.all(12),
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text('${mData.title.validate()}', style: boldTextStyle()),
                                      Transform.scale(
                                          scale: 0.7,
                                          child: Switch(
                                            value: mData.status.validate() == 1,
                                            onChanged: (value) {
                                              mData.status = value == true ? 1 : 0;
                                              updateStatusApiCall(mData.id.validate(), value);
                                              setState(() {});
                                            },
                                          )),
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
                    AddPageScreen().launch(context).then((val) {
                      if(val != null && val) {
                        init();
                      }
                    });
                    // HtmlEditorExample(title: 'hgh',).launch(context);
                  },
                ),
              ),
              appStore.isLoading
                  ? loaderWidget()
                  : pagesList.isEmpty
                      ? emptyWidget()
                      : SizedBox()
            ],
          ),
        ),
      ),
    );
  }
}
