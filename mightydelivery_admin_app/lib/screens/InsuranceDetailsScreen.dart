import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:html_editor_enhanced/html_editor.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/widgets.dart';

import '../main.dart';
import '../models/PageResponseModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';

class InsuranceDetailsScreen extends StatefulWidget {
  String insuranceDescription;

  InsuranceDetailsScreen(this.insuranceDescription, {super.key});

  @override
  State<InsuranceDetailsScreen> createState() => _InsuranceDetailsScreenState();
}

class _InsuranceDetailsScreenState extends State<InsuranceDetailsScreen> {
  PageResponse? data;
  String title = '';
  String description = '';
  HtmlEditorController insuranceDetailController = HtmlEditorController();

  @override
  void initState() {
    super.initState();
    getInsuranceDetails();
  }

  getInsuranceDetails() async {
    appStore.setLoading(true);
    await getPageDetailsById(id: widget.insuranceDescription).then((value) {
      title = value.data!.title!;
      description = value.data!.description!;
      print("-------------------------$description");
      appStore.setLoading(false);
      setState(() {});
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: appBarWidget((title.isNotEmpty) ? title.toString() : ""),
        body: Stack(
          children: [
            description != null && description.isNotEmpty
                ? SingleChildScrollView(
                    child: HtmlEditor(
                      controller: insuranceDetailController,
                      htmlEditorOptions: HtmlEditorOptions(
                        hint: language.description,
                        shouldEnsureVisible: true,
                        disabled: true,
                      ),
                      htmlToolbarOptions: HtmlToolbarOptions(
                        // renderBorder: true,
                        dropdownBoxDecoration: appStore.isDarkMode ? BoxDecoration(color: Colors.white) : null,
                        gridViewHorizontalSpacing: 0.5,
                        toolbarPosition: ToolbarPosition.aboveEditor,
                        toolbarType: ToolbarType.nativeGrid,
                        buttonBorderColor: primaryColor,
                        defaultToolbarButtons: [
                          StyleButtons(style: false),
                          FontSettingButtons(fontName: false, fontSize: false, fontSizeUnit: false),
                          InsertButtons(video: false, audio: false, hr: false, picture: false, table: false, link: false),
                        ],
                      ),
                      callbacks: Callbacks(
                        onInit: () async {
                          if (description != null) {
                            insuranceDetailController.setText(description.validate());
                          }
                        },
                      ),
                      otherOptions: OtherOptions(
                        height: context.height() * 0.85,
                        decoration: BoxDecoration(
                          color: appStore.isDarkMode ? Colors.white : null,
                          border: Border.all(width: 1, color: primaryColor),
                          borderRadius: BorderRadius.circular(13),
                        ),
                      ),
                    ),
                  )
                : !appStore.isLoading
                    ? emptyWidget()
                    : SizedBox(),
            if (appStore.isLoading)
              Center(
                child: loaderWidget(),
              )
          ],
        ));
  }
}
