import 'dart:math';

import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:flutter/services.dart';
import 'package:html/parser.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';

import '../../main.dart';
import '../utils/Colors.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Images.dart';
import 'confirmation_dialog.dart';

PageRouteAnimation? pageRouteAnimationGlobal;

/// Make any variable nullable
T? makeNullable<T>(T? value) => value;

/// Enum for page route
//enum PageRouteAnimation { Fade, Scale, Rotate, Slide, SlideBottomTop }

/// has match return bool for pattern matching
bool hasMatch(String? s, String p) {
  return (s == null) ? false : RegExp(p).hasMatch(s);
}

List<String> rtlLanguage = ['ar', 'ur'];

/// Show SnackBar
void snackBar(
    BuildContext context, {
      String title = '',
      Widget? content,
      SnackBarAction? snackBarAction,
      Function? onVisible,
      Color? textColor,
      Color? backgroundColor,
      EdgeInsets? margin,
      EdgeInsets? padding,
      Animation<double>? animation,
      double? width,
      ShapeBorder? shape,
      Duration? duration,
      SnackBarBehavior? behavior,
      double? elevation,
    }) {
  if (title.isEmpty && content == null) {
    print('SnackBar message is empty');
  } else {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: backgroundColor,
        action: snackBarAction,
        margin: margin,
        animation: animation,
        width: width,
        shape: shape,
        duration: duration ?? 4.seconds,
        behavior: margin != null ? SnackBarBehavior.floating : behavior,
        elevation: elevation,
        onVisible: onVisible?.call(),
        content: content ??
            Padding(
              padding: padding ?? EdgeInsets.symmetric(vertical: 4),
              child: Text(
                title,
                style: primaryTextStyle(color: textColor ?? Colors.white),
              ),
            ),
      ),
    );
  }
}

/// Hide soft keyboard
void hideKeyboard(context) => FocusScope.of(context).requestFocus(FocusNode());

/// Returns a string from Clipboard
Future<String> paste() async {
  ClipboardData? data = await Clipboard.getData('text/plain');
  return data?.text?.toString() ?? "";
}

/// Returns a string from Clipboard
Future<dynamic> pasteObject() async {
  ClipboardData? data = await Clipboard.getData('text/plain');
  return data;
}

/// Enum for Link Provider
enum LinkProvider {
  PLAY_STORE,
  APPSTORE,
  FACEBOOK,
  INSTAGRAM,
  LINKEDIN,
  TWITTER,
  YOUTUBE,
  REDDIT,
  TELEGRAM,
  WHATSAPP,
  FB_MESSENGER,
  GOOGLE_DRIVE
}

/// Use getSocialMediaLink function to build social media links
String getSocialMediaLink(LinkProvider linkProvider, {String url = ''}) {
  switch (linkProvider) {
    case LinkProvider.PLAY_STORE:
      return "$playStoreBaseURL$url";
    case LinkProvider.APPSTORE:
      return "$appStoreBaseURL$url";
    case LinkProvider.FACEBOOK:
      return "$facebookBaseURL$url";
    case LinkProvider.INSTAGRAM:
      return "$instagramBaseURL$url";
    case LinkProvider.LINKEDIN:
      return "$linkedinBaseURL$url";
    case LinkProvider.TWITTER:
      return "$twitterBaseURL$url";
    case LinkProvider.YOUTUBE:
      return "$youtubeBaseURL$url";
    case LinkProvider.REDDIT:
      return "$redditBaseURL$url";
    case LinkProvider.TELEGRAM:
      return "$telegramBaseURL$url";
    case LinkProvider.FB_MESSENGER:
      return "$facebookMessengerURL$url";
    case LinkProvider.WHATSAPP:
      return "$whatsappURL$url";
    case LinkProvider.GOOGLE_DRIVE:
      return "$googleDriveURL$url";
  }
}

const double degrees2Radians = pi / 180.0;

double radians(double degrees) => degrees * degrees2Radians;

void afterBuildCreated(Function()? onCreated) {
  makeNullable(SchedulerBinding.instance)!
      .addPostFrameCallback((_) => onCreated?.call());
}

Widget dialogAnimatedWrapperWidget({
  required Animation<double> animation,
  required Widget child,
  required DialogAnimation dialogAnimation,
  required Curve curve,
}) {
  switch (dialogAnimation) {
    case DialogAnimation.ROTATE:
      return Transform.rotate(
        angle: radians(animation.value * 360),
        child: Opacity(
          opacity: animation.value,
          child: FadeTransition(opacity: animation, child: child),
        ),
      );

    case DialogAnimation.SLIDE_TOP_BOTTOM:
      final curvedValue = curve.transform(animation.value) - 1.0;

      return Transform(
        transform: Matrix4.translationValues(0.0, curvedValue * 300, 0.0),
        child: Opacity(
          opacity: animation.value,
          child: FadeTransition(opacity: animation, child: child),
        ),
      );

    case DialogAnimation.SCALE:
      return Transform.scale(
        scale: animation.value,
        child: FadeTransition(opacity: animation, child: child),
      );

    case DialogAnimation.SLIDE_BOTTOM_TOP:
      return SlideTransition(
        position: Tween(begin: Offset(0, 1), end: Offset.zero)
            .chain(CurveTween(curve: curve))
            .animate(animation),
        child: Opacity(
          opacity: animation.value,
          child: FadeTransition(opacity: animation, child: child),
        ),
      );

    case DialogAnimation.SLIDE_LEFT_RIGHT:
      return SlideTransition(
        position: Tween(begin: Offset(1.0, 0.0), end: Offset.zero)
            .chain(CurveTween(curve: curve))
            .animate(animation),
        child: Opacity(
          opacity: animation.value,
          child: FadeTransition(opacity: animation, child: child),
        ),
      );

    case DialogAnimation.SLIDE_RIGHT_LEFT:
      return SlideTransition(
        position: Tween(begin: Offset(-1, 0), end: Offset.zero)
            .chain(CurveTween(curve: curve))
            .animate(animation),
        child: Opacity(
          opacity: animation.value,
          child: FadeTransition(opacity: animation, child: child),
        ),
      );

    case DialogAnimation.DEFAULT:
      return FadeTransition(opacity: animation, child: child);
  }
}

// Route<T> buildPageRoute<T>(
//   Widget child,
//   PageRouteAnimation? pageRouteAnimation,
//   Duration? duration,
// ) {
//   if (pageRouteAnimation != null) {
//     if (pageRouteAnimation == PageRouteAnimation.Fade) {
//       return PageRouteBuilder(
//         pageBuilder: (c, a1, a2) => child,
//         transitionsBuilder: (c, anim, a2, child) {
//           return FadeTransition(opacity: anim, child: child);
//         },
//         transitionDuration: duration ?? pageRouteTransitionDurationGlobal,
//       );
//     } else if (pageRouteAnimation == PageRouteAnimation.Rotate) {
//       return PageRouteBuilder(
//         pageBuilder: (c, a1, a2) => child,
//         transitionsBuilder: (c, anim, a2, child) {
//           return RotationTransition(child: child, turns: ReverseAnimation(anim));
//         },
//         transitionDuration: duration ?? pageRouteTransitionDurationGlobal,
//       );
//     } else if (pageRouteAnimation == PageRouteAnimation.Scale) {
//       return PageRouteBuilder(
//         pageBuilder: (c, a1, a2) => child,
//         transitionsBuilder: (c, anim, a2, child) {
//           return ScaleTransition(child: child, scale: anim);
//         },
//         transitionDuration: duration ?? pageRouteTransitionDurationGlobal,
//       );
//     } else if (pageRouteAnimation == PageRouteAnimation.Slide) {
//       return PageRouteBuilder(
//         pageBuilder: (c, a1, a2) => child,
//         transitionsBuilder: (c, anim, a2, child) {
//           return SlideTransition(
//             child: child,
//             position: Tween(
//               begin: Offset(1.0, 0.0),
//               end: Offset(0.0, 0.0),
//             ).animate(anim),
//           );
//         },
//         transitionDuration: duration ?? pageRouteTransitionDurationGlobal,
//       );
//     } else if (pageRouteAnimation == PageRouteAnimation.SlideBottomTop) {
//       return PageRouteBuilder(
//         pageBuilder: (c, a1, a2) => child,
//         transitionsBuilder: (c, anim, a2, child) {
//           return SlideTransition(
//             child: child,
//             position: Tween(
//               begin: Offset(0.0, 1.0),
//               end: Offset(0.0, 0.0),
//             ).animate(anim),
//           );
//         },
//         transitionDuration: duration ?? pageRouteTransitionDurationGlobal,
//       );
//     }
//   }
//   return MaterialPageRoute<T>(builder: (_) => child);
// }

EdgeInsets dynamicAppButtonPadding(BuildContext context) {
  if (context.isDesktop()) {
    return EdgeInsets.symmetric(vertical: 20, horizontal: 20);
  } else if (context.isTablet()) {
    return EdgeInsets.symmetric(vertical: 16, horizontal: 16);
  } else {
    return EdgeInsets.symmetric(vertical: 12, horizontal: 16);
  }
}

enum BottomSheetDialog { Dialog, BottomSheet }

// Future<dynamic> showBottomSheetOrDialog({
//   required BuildContext context,
//   required Widget child,
//   BottomSheetDialog bottomSheetDialog = BottomSheetDialog.Dialog,
// }) {
//   if (bottomSheetDialog == BottomSheetDialog.BottomSheet) {
//     return showModalBottomSheet(context: context, builder: (_) => child);
//   } else {
//     return showInDialog(context, builder: (_) => child);
//   }
// }

bool get isRTL => rtlLanguage.contains(appStore.selectedLanguage);

/// mailto: function to open native email app
Uri mailTo({
  required List<String> to,
  String subject = '',
  String body = '',
  List<String> cc = const [],
  List<String> bcc = const [],
}) {
  String _subject = '';
  if (subject.isNotEmpty) _subject = '&subject=$subject';

  String _body = '';
  if (body.isNotEmpty) _body = '&body=$body';

  String _cc = '';
  if (cc.isNotEmpty) _cc = '&cc=${cc.join(',')}';

  String _bcc = '';
  if (bcc.isNotEmpty) _bcc = '&bcc=${bcc.join(',')}';

  return Uri(
    scheme: 'mailto',
    query: 'to=${to.join(',')}$_subject$_body$_cc$_bcc',
  );
}

Widget dotIndicator(list, i, {bool isPersonal = false}) {
  return SizedBox(
    height: 16,
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.center,
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(
        list.length,
            (ind) {
          return Container(
            height: 4,
            width: i == ind ? 30 : 12,
            margin: EdgeInsets.all(4),
            decoration: BoxDecoration(
                color: primaryColor,
                // i == ind
                //     ? appStore.isDarkMode == true
                //     ? Colors.white
                //     : primaryColor
                //     : Colors.grey.withOpacity(0.5),
                borderRadius: radius(4)),
          );
        },
      ),
    ),
  );
}

/// returns true if network is available
Future<bool> isNetworkAvailable() async {
  var connectivityResult = await Connectivity().checkConnectivity();
  return connectivityResult != ConnectivityResult.none;
}

get getContext => navigatorKey.currentState?.overlay?.context;

Future<T?> push<T>(
    Widget widget, {
      bool isNewTask = false,
      PageRouteAnimation? pageRouteAnimation,
      Duration? duration,
    }) async {
  if (isNewTask) {
    return await Navigator.of(getContext).pushAndRemoveUntil(
      buildPageRoute(widget, pageRouteAnimation, duration),
          (route) => false,
    );
  } else {
    return await Navigator.of(getContext).push(
      buildPageRoute(widget, pageRouteAnimation, duration),
    );
  }
}

String parseHtmlString(String? htmlString) {
  return parse(parse(htmlString).body!.text).documentElement!.text;
}

/// Dispose current screen or close current dialog
void pop([Object? object]) {
  if (Navigator.canPop(getContext)) Navigator.pop(getContext, object);
}

List<Map<String, String>> getPackagingSymbols() {
  return [
    {
      'title': "This Way Up",
      'image': ic_this_way_up,
      'description': language.thisWayUp,
      'key': 'this_way_up',
    },
    {
      'title': "Do Not Stack",
      'image': ic_do_not_stack,
      'description': language.doNotStack,
      'key': 'do_not_stack',
    },
    {
      'title': "Temperature-Sensitive",
      'image': ic_temperature_sensitive,
      'description': language.temperatureSensitive,
      'key': 'temperature_sensitive',
    },
    {
      'title': "Do Not Use Hooks",
      'image': ic_do_not_hook,
      'description': language.donotUseHooks,
      'key': 'do_not_use_hooks',
    },
    {
      'title': "Explosive Material",
      'image': ic_explosive_material,
      'description': language.explosiveMaterial,
      'key': 'explosive_material',
    },
    {
      'title': "Hazardous Material",
      'image': ic_hazard,
      'description':language.hazardousMaterial,
      'key': 'hazardous_material',
    },
    {
      'title': "Bike Delivery",
      'image': ic_bike_delivery,
      'description':language.bikeDelivery,
      'key': 'bike_delivery',
    },
    {
      'title': "Keep Dry",
      'image': ic_keep_dry,
      'description': language.keepDry,
      'key': 'keep_dry',
    },
    {
      'title': "Perishable",
      'image': ic_perishable,
      'description': language.perishable,
      'key': 'perishable',
    },
    {
      'title': "Recycle",
      'image': ic_recycle,
      'description': language.recycle,
      'key': 'recycle',
    },
    {
      'title': "Do Not Open with Sharp Objects",
      'image': ic_do_not_open_with_sharp_object,
      'description': language.doNotOpenWithSharpObjects,
      'key': 'do_not_open_with_sharp_objects',
    },
    {
      'title': "Fragile (Handle with Care)",
      'image': ic_fragile,
      'description': language.fragile,
      'key': 'fragile',
    },
  ];
}

getClaimStatus(String status) {
  if (status == PENDING.toLowerCase()) {
    return Text(status, style: boldTextStyle(color: pendingColor));
  }  else if (status == APPROVED) {
    return Text(status, style: boldTextStyle(color: completedColor));
  } else if (status == REJECT) {
    return Text(status, style: boldTextStyle(color: pendingColor));
  } else {
    return Text(status, style: boldTextStyle(color: completedColor));
  }
}

getClaimStatusColor(String status, double opacity) {
  if (status == PENDING.toLowerCase() || status == REJECT) {
    return pendingColor.withOpacity(opacity);
  } else {
    return completedColor.withOpacity(opacity);
  }
}
