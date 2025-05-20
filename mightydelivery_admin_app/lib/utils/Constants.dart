import 'package:flutter/material.dart';
import '../extensions/extension_util/int_extensions.dart';

//var errorThisFieldRequired = 'This field is required';
const currencySymbolDefault = '₹';
const currencyCodeDefault = 'INR';

/// Don't add slash at the end of the url
const DOMAIN_URL = 'https://meetmighty.com/mobile/delivery-admin';
const mBaseUrl = "$DOMAIN_URL/api/";

const googleMapAPIKey = 'YOUR GOOGLE MAP KEY';

const mOneSignalAppIdAdmin = 'YOUR ONESIGNAL APP ID ADMIN';
//region  firebase data  for firebase_options.dart
const String FIREBASE_API_KEY = "YOUR FIREBASE API KEY";
const String FIREBASE_APP_ID = "YOUR FIREBASE APP ID";
const String FIREBASE_MESSAGING_SENDER_ID = " YOUR FIREBASE MESSAGING SENDER ID";
const String FIREBASE_PROJECT_ID = "YOUR FIREBASE PROJECT ID ";
const String FIREBASE_STORAGE_BUCKET = "YOUR FIREBASE STORAGE BUCKET";
//endregion
//for ios
const String ANDROID_CLIENT_ID = 'YOUR ANDROID_CLIENT_ID';
const String IOS_CLIENT_ID = 'YOUR IOS_CLIENT_ID';
const String IOS_BUNDLE_ID = 'YOUR IOS_BUNDLE_ID';
//endregion

String defaultPhoneCode = "+91";

const minContactLength = 10;
const maxContactLength = 14;
const digitAfterDecimal = 2;

const mAppName = "";
const defaultLanguage = "en";

///FireBase Collection Name
const MESSAGES_COLLECTION = "messages";
const CONTACT_COLLECTION = "contact";
const CHAT_DATA_IMAGES = "chatImages";

const IS_ENTER_KEY = "IS_ENTER_KEY";
const SELECTED_WALLPAPER = "SELECTED_WALLPAPER";
const PER_PAGE_CHAT_COUNT = 50;

const TEXT = "TEXT";
const IMAGE = "IMAGE";

const VIDEO = "VIDEO";
const AUDIO = "AUDIO";

const UID = 'UID';
const mAppIconUrl = "assets/app_logo_white.png";
const FIREBASE_UID = 'FIREBASE_UID';

const mOneSignalAppId = 'YOUR ONESIGNAL APP ID';
const mOneSignalRestKey = 'YOUR ONESIGNAL REST API KEY';
const mOneSignalChannelId = 'YOUR ONESIGNAL CHANNEL ID';

const USER_COLLECTION = "users";

const CLIENT = 'client';
const DELIVERYMAN = 'delivery_man';

const passwordLengthGlobal = 6;
const defaultRadius = 8.0;
const defaultSmallRadius = 6.0;

const textPrimarySizeGlobal = 16.00;
const textBoldSizeGlobal = 16.00;
const textSecondarySizeGlobal = 14.00;
const borderRadius = 16.00;

double tabletBreakpointGlobal = 600.0;
double desktopBreakpointGlobal = 720.0;
double statisticsItemWidth = 230.0;

const RESTORE = 'restore';
const FORCE_DELETE = 'forcedelete';

const CHARGE_TYPE_FIXED = 'fixed';
const CHARGE_TYPE_PERCENTAGE = 'percentage';

const DISTANCE_UNIT_KM = 'km';
const DISTANCE_UNIT_MILE = 'mile';

const PAYMENT_GATEWAY_STRIPE = 'stripe';
const PAYMENT_GATEWAY_RAZORPAY = 'razorpay';
const PAYMENT_GATEWAY_PAYSTACK = 'paystack';
const PAYMENT_GATEWAY_FLUTTERWAVE = 'flutterwave';
const PAYMENT_GATEWAY_PAYPAL = 'paypal';
const PAYMENT_GATEWAY_PAYTABS = 'paytabs';
//const PAYMENT_GATEWAY_MERCADOPAGO = 'mercadopago';
const PAYMENT_GATEWAY_PAYTM = 'paytm';
const PAYMENT_GATEWAY_MYFATOORAH = 'myfatoorah';
const PAYMENT_TYPE_CASH = 'cash';
const PAYMENT_TYPE_WALLET = 'wallet';

const DECLINE = 'decline';
const REQUESTED = 'requested';
const APPROVED = 'approved';

const ORDER_DRAFT = 'draft';
const ORDER_DEPARTED = 'courier_departed';
const ORDER_ACCEPTED = 'active';
const ORDER_CANCELLED = 'cancelled';
const ORDER_DELAYED = 'delayed';
const ORDER_ASSIGNED = 'courier_assigned';
const ORDER_ARRIVED = 'courier_arrived';
const ORDER_PICKED_UP = 'courier_picked_up';
const ORDER_DELIVERED = 'completed';
const ORDER_CREATED = 'create';
const ORDER_TRANSFER = 'courier_transfer';
const ORDER_PAYMENT = 'payment_status_message';
const ORDER_FAIL = 'failed';
const ORDER_SHIPPED = 'shipped';

const ALL_ORDER = "all";
const SCHEDULE_ORDER = "schedule";
const DRAFT_ORDER = "draft";
const TODAY_ORDER = "today";
const PENDING_ORDER = "pending";
const INPROGRESS_ORDER = "inprogress";
const COMPLETED_ORDER = "completed";
const CANCELLED_ORDER = "cancelled";
const SHIPPED_ORDER = "shipped";

const TRANSACTION_ORDER_FEE = "order_fee";
const TRANSACTION_TOPUP = "topup";
const TRANSACTION_ORDER_CANCEL_CHARGE = "order_cancel_charge";
const TRANSACTION_ORDER_CANCEL_REFUND = "order_cancel_refund";
const TRANSACTION_CORRECTION = "correction";
const TRANSACTION_COMMISSION = "commission";
const TRANSACTION_WITHDRAW = "withdraw";

const DIALOG_TYPE_DELETE = 'Delete';
const DIALOG_TYPE_RESTORE = 'Restore';
const DIALOG_TYPE_ENABLE = 'Enable';
const DIALOG_TYPE_DISABLE = 'Disable';
const DIALOG_TYPE_ASSIGN = 'Assign';
const DIALOG_TYPE_TRANSFER = 'Transfer';
const DIALOG_TYPE_VERIFY = 'Verify';

const CREDIT = 'credit';
const DEBIT = 'debit';

const TOKEN = 'TOKEN';
const IS_LOGGED_IN = 'IS_LOGGED_IN';
const USER_ID = 'USER_ID';
const USER_TYPE = 'USER_TYPE';
const USER_EMAIL = 'USER_EMAIL';
const USER_PASSWORD = 'USER_PASSWORD';
const NAME = 'NAME';
const USER_PROFILE_PHOTO = 'USER_PROFILE_PHOTO';
const USER_CONTACT_NUMBER = 'USER_CONTACT_NUMBER';
const USER_NAME = 'USER_NAME';
const USER_ADDRESS = 'USER_ADDRESS';
const REMEMBER_ME = 'REMEMBER_ME';
const FILTER_DATA = 'FILTER_DATA';
const RECENT_ADDRESS_LIST = 'RECENT_ADDRESS_LIST';

const PAYMENT_ON_PICKUP = 'on_pickup';
const PAYMENT_ON_DELIVERY = 'on_delivery';

const DEMO_ADMIN = 'demo_admin';
const ADMIN = 'admin';
const FCM_TOKEN = 'FCM_TOKEN';
const PLAYER_ID = 'PLAYER_ID';

// Payment status
const PAYMENT_PENDING = 'pending';
const PAYMENT_FAILED = 'failed';
const PAYMENT_PAID = 'paid';

const THEME_MODE_INDEX = 'theme_mode_index';
const SELECTED_LANGUAGE_CODE = 'selected_language_code';

const default_Language = 'en';

//region LiveStream Keys
const streamLanguage = 'streamLanguage';
const streamDarkMode = 'streamDarkMode';

const FIXED_CHARGES = "fixed_charges";
const MIN_DISTANCE = "min_distance";
const MIN_WEIGHT = "min_weight";
const PER_DISTANCE_CHARGE = "per_distance_charges";
const PER_WEIGHT_CHARGE = "per_weight_charges";
const EXPANDED_INDEX = 'EXPANDED_INDEX';
// Menu Index
//expanded index

const DELIVERY_PERSON_EXPANDED_INDEX = 1;
const SETTING_EXPANDED_INDEX = 2;
const WEBSITE_SETTING_EXPANDED_INDEX = 3;

const DASHBOARD_INDEX = 0;
const ORDER_INDEX = 1;
const USER_INDEX = 2;
const DELIVERY_PERSON_INDEX = 3;
const APP_SETTING_INDEX = 4;
const COUNTRY_INDEX = 5;
const CITY_INDEX = 6;
const ORDER_LOCATION_INDEX = 7;
const DOCUMENT_INDEX = 8;
const DELIVERY_PERSON_DOCUMENT_INDEX = 9;
const DELIVERY_PERSON_LOCATION_INDEX = 10;
const VEHICLE_INDEX = 11;
const EXTRA_CHARGES_INDEX = 12;
const PARCEL_TYPE_INDEX = 13;
const PAYMENT_GATEWAY_INDEX = 14;
const WITHDRAW_INDEX = 15;
const INVOICE_SETTING_INDEX = 16;
const CHANGE_PASSWORD_INDEX = 17;
const LANGUAGE_INDEX = 18;
const THEME_INDEX = 19;
const WEBSITE_INFORMATION_INDEX = 20;
const WEBSITE_WHY_DELIVERY_INDEX = 21;
const WEBSITE_CLIENT_REVIEW_INDEX = 22;
const WEBSITE_DOWNLOAD_APP_INDEX = 23;
const WEBSITE_CONTACT_INFO_INDEX = 24;
const WEBSITE_DELIVERY_PARTNER_INDEX = 25;
const WEBSITE_ABOUT_US_INDEX = 26;
const WEBSITE_PRIVACY_POLICY_INDEX = 27;
const WEBSITE_TERM_CONDITION_INDEX = 28;
const WEBSITE_WALKTHROUGH_INDEX = 29;
const WEBSITE_TRACK_ORDER_INDEX = 42;
const REPORT_INDEX = 30;
const ORDER_REPORT_INDEX = 31;
const ADMIN_EARNING_REPORT_INDEX = 32;
const DELIVERYMAN_EARNING_REPORT_INDEX = 33;
const DELIVERYMAN_WISE_REPORT_INDEX = 34;
const USER_WISE_REPORT_INDEX = 35;
const CITY_WISE_REPORT_INDEX = 36;
const COUNTRY_WISE_REPORT_INDEX = 37;
const ALL_WITHDRAW_REQUEST = 38;
const PENDING_WITHDRAW_REQUEST = 39;
const APPROVED_WITHDRAW_REQUEST = 40;
const CANCELLED_WITHDRAW_REQUEST = 41;
const PUSH_NOTIFICATION_INDEX = 43;
const PUSH_NOTIFICATION_LIST_INDEX = 44;
const SEND_PUSH_NOTIFICATION_INDEX = 45;
const PAGES_INDEX = 46;
const PAGES_LIST_INDEX = 47;
const CUSTOMER_SUPPORT_INDEX = 48;
const CUSTOMER_SUPPORT_LIST_INDEX = 49;
const PENDING_CUSTOMER_SUPPORT_LIST_INDEX = 50;
const INREVIEW_CUSTOMER_SUPPORT_LIST_INDEX = 51;
const RESOLVED_CUSTOMER_SUPPORT_LIST_INDEX = 52;
const COURIER_COMPANY_INDEX = 53;
const REFERENCE_PARTNER_INDEX = 54;
const CLAIMS_MANAGEMENT_INDEX = 55;
const REJECTED_CLAIMS_MANAGEMENT_INDEX = 56;
const APPROVED_CLAIMS_MANAGEMENT_INDEX = 57;
const PENDING_CLAIMS_MANAGEMENT_INDEX = 58;
const CLOSED_CLAIMS_MANAGEMENT_INDEX = 59;
const ALL_CLAIMS_MANAGEMENT_INDEX = 60;

class AppThemeMode {
  final int themeModeLight = 1;
  final int themeModeDark = 2;
  final int themeModeSystem = 0;
}

enum MessageType {
  TEXT,
  IMAGE,
  VIDEO,
  AUDIO,
}

extension MessageExtension on MessageType {
  String? get name {
    switch (this) {
      case MessageType.TEXT:
        return 'TEXT';
      case MessageType.IMAGE:
        return 'IMAGE';
      case MessageType.VIDEO:
        return 'VIDEO';
      case MessageType.AUDIO:
        return 'AUDIO';
      default:
        return null;
    }
  }
}

// Currency Position
const CURRENCY_POSITION_LEFT = 'left';
const CURRENCY_POSITION_RIGHT = 'right';

const MONTHLY_ORDER_COUNT = 'monthly_order_count';
const MONTHLY_PAYMENT_CANCELLED_REPORT = 'monthly_payment_cancelled_report';
const MONTHLY_PAYMENT_COMPLETED_REPORT = 'monthly_payment_completed_report';

const ThemeModeLight = 0;
const ThemeModeDark = 1;
const ThemeModeSystem = 2;

const LANGUAGE = "LANGUAGE";

const DEFAULT_LANGUAGE = 'en';
const OTP_VERIFIED = "OTP_VERIFIED";

// SharedReference keys
const IS_FIRST_TIME = 'IS_FIRST_TIME';

const IS_USER_SIGNUP = 'IS_USER_SIGNUP';

const USER_TOKEN = 'USER_TOKEN';

class DefaultValues {
  final String defaultLanguage = "en";
}

DefaultValues defaultValues = DefaultValues();
int defaultElevation = 4;
double defaultBlurRadius = 4.0;
double defaultSpreadRadius = 0.5;
double defaultAppBarElevation = 1.0;

Color? defaultInkWellSplashColor;
Color? defaultInkWellHoverColor;
Color? defaultInkWellHighlightColor;
double? defaultInkWellRadius;
Color defaultLoaderBgColorGlobal = Colors.white;
Color? defaultLoaderAccentColorGlobal;

String? fontFamilyBoldGlobal;
String? fontFamilyPrimaryGlobal;
String? fontFamilySecondaryGlobal;
FontWeight fontWeightBoldGlobal = FontWeight.bold;
FontWeight fontWeightPrimaryGlobal = FontWeight.normal;
FontWeight fontWeightSecondaryGlobal = FontWeight.normal;
bool enableAppButtonScaleAnimationGlobal = true;
bool forceEnableDebug = false;

int? appButtonScaleAnimationDurationGlobal;
ShapeBorder? defaultAppButtonShapeBorder;

double defaultAppButtonRadius = 10.0;
double defaultAppButtonElevation = 4.0;
Duration pageRouteTransitionDurationGlobal = 400.milliseconds;

var errorSomethingWentWrong = 'Something Went Wrong';
var errorInternetNotAvailable = 'Your internet is not working';
var errorNotAllow = 'Sorry, You are not allowed';

const playStoreBaseURL = 'https://play.google.com/store/apps/details?id=';
const appStoreBaseURL = 'https://apps.apple.com/in/app/';

var errorMessage = 'Please try again';

var customDialogHeight = 140.0;
var customDialogWidth = 220.0;

const MAIL_TO_PREFIX = 'mailto:';
const TEL_PREFIX = 'tel:';

const ORDER_PREFIX = 'prefix';

const facebookBaseURL = 'https://www.facebook.com/';
const instagramBaseURL = 'https://www.instagram.com/';
const linkedinBaseURL = 'https://www.linkedin.com/in/';
const twitterBaseURL = 'https://twitter.com/';
const youtubeBaseURL = 'https://www.youtube.com/';
const redditBaseURL = 'https://reddit.com/r/';
const telegramBaseURL = 'https://t.me/';
const facebookMessengerURL = 'https://m.me/';
const whatsappURL = 'https://wa.me/';
const googleDriveURL = 'https://docs.google.com/viewer?url=';

const spacingControlHalf = 2;
const spacingControl = 4;
const spacingStandard = 8;
const spacingStandardNew = 16;
const spacingMedium = 20;
const spacingLarge = 26;
const spacingXL = 30;
const spacingXXL = 34;

const isDarkModeOnPref = 'isDarkModeOnPref';
ShapeBorder? defaultDialogShape;
//region status

const PENDING = 'Pending';
const APPROVEDText = 'Approved';
const REJECTED = 'Rejected';
const CLOSED = 'close';
const REJECT = 'reject';
//endregion
//region frontendData

const WHY_CHOOSE = "why_choose";
const PARTNER_BENEFITS = "partner_benefits";
const CLIENT_REVIEW = "client_review";
const WALKTHROUGH = "walkthrough";

const CITY_NOT_FOUND_EXCEPTION = "City has been not found.";
const double MILES_PER_KM = 0.621371;

//region country symnbol and code
const currencySymbol = '₹';
const currencyCode = 'INR';
//endregion
const PAYMENT_GATEWAY_LIST = 'PAYMENT_GATEWAY_LIST';

const SUPPORT_TICKET_STATUS_PENDING = 'pending';
const SUPPORT_TICKET_STATUS_IN_REVIEW = 'inreview';
const SUPPORT_TICKET_RESOLVED = 'resolved';
