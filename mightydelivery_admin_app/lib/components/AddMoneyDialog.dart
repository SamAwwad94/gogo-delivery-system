import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../utils/Colors.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../main.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';
import '../utils/Extensions/shared_pref.dart';

class AddWalletDialog extends StatefulWidget {
  final int? userId;
  final Function()? onUpdate;

  AddWalletDialog({this.userId, this.onUpdate});

  @override
  AddWalletDialogState createState() => AddWalletDialogState();
}

class AddWalletDialogState extends State<AddWalletDialog> {
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  TextEditingController amountCont = TextEditingController();

  List<String> typeList = [CREDIT, DEBIT];
  String selectedType = CREDIT;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    //
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Future<void> saveWalletApi() async {
    appStore.setLoading(true);
    Map req = {
      "user_id": widget.userId,
      "type": selectedType,
      "amount": double.parse(amountCont.text),
      "transaction_type": selectedType == CREDIT ? TRANSACTION_TOPUP : TRANSACTION_CORRECTION,
      "currency": appStore.currencyCode,
    };
    await saveWallet(req).then((value) {
      appStore.setLoading(false);
      widget.onUpdate?.call();
      toast(value.message);
    }).catchError((error) {
      toast(error.toString());
      appStore.setLoading(false);
    });
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      actionsPadding: EdgeInsets.only(right: 16, bottom: 16),
      title: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(language.addWallet, style: boldTextStyle(size: 20, color: appStore.isDarkMode ? white : primaryColor)),
          Icon(Icons.close).onTap(() {
            Navigator.pop(context);
          })
        ],
      ),
      content: SizedBox(
        width: MediaQuery.of(context).size.width,
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(language.type, style: primaryTextStyle()),
              SizedBox(height: 8),
              DropdownButtonFormField<String>(
                dropdownColor: Theme.of(context).cardColor,
                value: selectedType,
                decoration: commonInputDecoration(),
                items: typeList.map<DropdownMenuItem<String>>((item) {
                  return DropdownMenuItem(value: item, child: Text(printType(item), style: primaryTextStyle()));
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
              SizedBox(height: 16),
              Text(language.amount, style: primaryTextStyle()),
              SizedBox(height: 8),
              AppTextField(
                controller: amountCont,
                textFieldType: TextFieldType.PHONE,
                inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                decoration: commonInputDecoration(),
                errorThisFieldRequired: language.field_required_msg,
              ),
            ],
          ),
        ),
      ),
      actions: <Widget>[
        dialogSecondaryButton(language.cancel, () {
          Navigator.pop(context);
        }),
        SizedBox(width: 4),
        dialogPrimaryButton(language.save, () async {
          if (_formKey.currentState!.validate()) {
            if (getStringAsync(USER_TYPE) == DEMO_ADMIN) {
              toast(language.demoAdminMsg);
            } else {
              Navigator.pop(context);
              await saveWalletApi();
            }
          }
        }).paddingOnly(left:appStore.selectedLanguage=="ar"?  18:0,right: 8)
      ],
    );
  }
}
