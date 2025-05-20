import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:html_editor_enhanced/html_editor.dart';
import '../extensions/extension_util/bool_extensions.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../utils/Colors.dart';
import 'package:permission_handler/permission_handler.dart';

import '../components/RequiredValidation.dart';
import '../extensions/app_text_field.dart';
import '../main.dart';
import '../models/PageListModel.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Extensions/app_common.dart';

class AddPageScreen extends StatefulWidget {
  final PageData? page;
  final bool? isUpdate;

  AddPageScreen({this.page, this.isUpdate = false});

  @override
  State<AddPageScreen> createState() => _AddPageScreenState();
}

class _AddPageScreenState extends State<AddPageScreen> {
  HtmlEditorController controller = HtmlEditorController();
  TextEditingController titleController = TextEditingController();
  bool isPrmissionGranted = false;
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    if (widget.page != null) {
      controller.setText(widget.page!.description.validate());
      titleController.text = widget.page!.title.validate();
    }
  }

  Future<bool> requestPermissions() async {
    final status = await Permission.storage.request();
    isPrmissionGranted = status.isGranted;
    setState(() {});
    return status.isGranted;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      resizeToAvoidBottomInset: true,
      appBar: appBarWidget(widget.isUpdate.validate() ? language.updatePages : language.addPages,
          actions: [
            GestureDetector(
              child: Text(language.clear, style: boldTextStyle(color: Colors.white)).paddingOnly(right: 18, left: appStore.isDarkMode ? 16 : 0),
              onTap: () {
                controller.clear();
                setState(() {});
              },
            )
          ]),
      body: Stack(
        children: [
          ListView(
            shrinkWrap: true,
            // crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              RequiredValidation(required: true, titleText: language.title),
              8.height,
              Form(
                key: _formKey,
                child: AppTextField(
                  controller: titleController,
                  textFieldType: TextFieldType.OTHER,
                  decoration: commonInputDecoration(),
                  textInputAction: TextInputAction.next,
                  maxLines: 1,
                  minLines: 1,
                  validator: (s) {
                    if (s!.trim().isEmpty) return language.field_required_msg;
                    return null;
                  },
                ),
              ),
              16.height,
              RequiredValidation(titleText: language.description),
              8.height,
              HtmlEditor(
                controller: controller,
                htmlEditorOptions: HtmlEditorOptions(
                  hint: language.description,
                  shouldEnsureVisible: true,
                  // autoAdjustHeight: true,
                  // androidUseHybridComposition: true,
                ),
                htmlToolbarOptions: HtmlToolbarOptions(
                  renderBorder: true,
                // textStyle: appStore.isDarkMode ? TextStyle(color: Colors.white) : null,
                  // buttonColor: appStore.isDarkMode ?  Colors.white : null,
                  // dropdownBackgroundColor: appStore.isDarkMode ? Colors.black : null,
                  dropdownBoxDecoration: appStore.isDarkMode ? BoxDecoration(color: Colors.white) : null,
                  gridViewHorizontalSpacing: 0.5,
                  toolbarPosition: ToolbarPosition.aboveEditor,
                  toolbarType: ToolbarType.nativeGrid,
                  buttonBorderColor: primaryColor,
                  defaultToolbarButtons: [
                    StyleButtons(),
                    FontSettingButtons(),
                    InsertButtons(video: false, audio: false, hr: false),
                    // OtherButtons(),
                  ],
                  onButtonPressed: (ButtonType type, bool? status, Function? updateStatus) async {
                    print("button '${type}' pressed, the current selected status is $status");
                    if (type == ButtonType.picture) {
                      await requestPermissions();
                      if (!isPrmissionGranted) {
                        return false;
                      }
                    }
                    return true;
                  },
                  /*   mediaLinkInsertInterceptor: (String url, InsertFileType type) async {
                    bool status = await requestPermissions();
                    if (status) {
                      print(url);
                      return true;
                    } else {
                      print('Permission denied for image insertion');
                      return false;
                    }
                  },
                  mediaUploadInterceptor: (file, InsertFileType type) async {
                    bool status = await requestPermissions();
                    if (status) {
                      print(file.name); // filename
                      print(file.size); // size in bytes
                      print(file.extension); // file extension (e.g., jpeg or mp4)
                      return true;
                    } else {
                      print('Permission denied for image upload');
                      // Optionally, show a dialog to the user explaining why permission is needed
                      return false;
                    }
                  },*/
                ),
                callbacks: Callbacks(
                  onInit: () async {
                    if (widget.page != null) {
                      controller.setText(widget.page!.description.validate());
                    }
                  },
                ),
                otherOptions: OtherOptions(
                  height: context.height() * 0.85,
                  decoration: BoxDecoration(
                    color: appStore.isDarkMode ? Colors.white: null,
                    border: Border.all(width: 1, color: primaryColor),
                    borderRadius: BorderRadius.circular(13),
                  ),
                ),
              ),
            ],
          ).paddingAll(8),
          Observer(builder: (context) => Visibility(visible: appStore.isLoading, child: Positioned.fill(child: loaderWidget()))),
        ],
      ),
      bottomNavigationBar: Padding(
        padding: EdgeInsets.all(13),
        child: dialogPrimaryButton(widget.isUpdate.validate() ? language.update : language.save, () async {
          String? description = await controller.getText();
          print("data ====> ${description}");
          if (_formKey.currentState!.validate()) {
            updateOrAddPagesApiCall(description);
          }
        }),
      ),
    );
  }

  updateOrAddPagesApiCall(String description) async {
    Map req = {
      "title": titleController.text.validate(),
      "description": description,
    };
    appStore.setLoading(true);
    if (widget.isUpdate.validate()) {
      await updatePages(widget.page!.id.validate(), req).then((value) {
        appStore.setLoading(false);
        toast(value.message.toString());
        Navigator.pop(context);
      }).catchError((error) {
        appStore.setLoading(false);
        toast(error.toString());
      });
    } else {
      await savePages(req).then((value) {
        appStore.setLoading(false);
        toast(value.message.toString());
        Navigator.pop(context, true);
      }).catchError((error) {
        appStore.setLoading(false);
        toast(error.toString());
      });
    }
  }
}

