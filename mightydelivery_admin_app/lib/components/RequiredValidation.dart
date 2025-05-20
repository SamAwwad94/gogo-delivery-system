import 'package:flutter/material.dart';

import '../extensions/colors.dart';
import '../utils/Extensions/app_common.dart';

class RequiredValidation extends StatefulWidget {
  final String? titleText;
  final bool required;
  const RequiredValidation({this.required = false, this.titleText});

  @override
  State<RequiredValidation> createState() => _RequiredValidationState();
}

class _RequiredValidationState extends State<RequiredValidation> {
  @override
  Widget build(BuildContext context) {
    return RichText(
      text: TextSpan(
        style: primaryTextStyle(),
        children: <TextSpan>[
          TextSpan(text: widget.titleText, style: primaryTextStyle()),
          widget.required ? TextSpan(text: ' *', style: secondaryTextStyle(color: redColor)) : TextSpan(),
        ],
      ),
    );
  }
}
