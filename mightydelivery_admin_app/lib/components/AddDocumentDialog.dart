import 'package:flutter/material.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';

import '../extensions/colors.dart';
import '../main.dart';
import '../models/DocumentListModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';

class AddDocumentDialog extends StatefulWidget {
  static String tag = '/AppAddDocumentDialog';

  final DocumentData? documentData;
  final Function()? onUpdate;

  AddDocumentDialog({this.documentData, this.onUpdate});

  @override
  AddDocumentDialogState createState() => AddDocumentDialogState();
}

class AddDocumentDialogState extends State<AddDocumentDialog> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  TextEditingController nameController = TextEditingController();

  bool isUpdate = false;
  bool isRequired = false;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    isUpdate = widget.documentData != null;
    if (isUpdate) {
      nameController.text = widget.documentData!.name!;
      isRequired = widget.documentData!.isRequired == 1;
    }
  }

  addDocumentApiCall() async {
    if (_formKey.currentState!.validate()) {
      Navigator.pop(context);
      Map req = {
        "id": isUpdate ? widget.documentData!.id : "",
        "name": nameController.text,
        "is_required": isRequired ? 1 : 0,
      };
      appStore.setLoading(true);
      await addDocument(req).then((value) {
        appStore.setLoading(false);
        toast(value.message.toString());
        widget.onUpdate!.call();
      }).catchError((error) {
        appStore.setLoading(false);
        toast(error.toString());
      });
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      // contentPadding: EdgeInsets.all(16),
      //titlePadding: EdgeInsets.only(left: 16, right: 8, top: 8),
      title: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(isUpdate ? language.updateDocument : language.addDocument, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor, size: 20)),
          Icon(Icons.close).onTap(() {
            Navigator.pop(context);
          })
        ],
      ),
      content: SingleChildScrollView(
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              8.height,
              Text(language.name, style: primaryTextStyle()),
              SizedBox(height: 8),
              AppTextField(
                controller: nameController,
                textFieldType: TextFieldType.NAME,
                decoration: commonInputDecoration(),
                textInputAction: TextInputAction.next,
                validator: (s) {
                  if (s!.trim().isEmpty) return language.fieldRequiredMsg;
                  return null;
                },
              ),
              CheckboxListTile(
                value: isRequired,
                onChanged: (value) {
                  isRequired = value!;
                  setState(() {});
                },
                activeColor: primaryColor,
                title: Text(language.required, style: primaryTextStyle()),
                controlAffinity: ListTileControlAffinity.leading,
                contentPadding: EdgeInsets.zero,
              ),
              20.height,
              Row(
                children: [
                  Expanded(
                    child: dialogSecondaryButton(language.cancel, () {
                      Navigator.pop(context);
                    }),
                  ),
                  SizedBox(width: 16),
                  Expanded(
                    child: dialogPrimaryButton(isUpdate ? language.update : language.add, () {
                      if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                        toast(language.demoAdminMsg);
                      } else {
                        addDocumentApiCall();
                      }
                    }),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
