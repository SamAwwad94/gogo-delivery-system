import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import '../screens/ClaimsListscreen.dart';
import '../screens/AppSettingsScreen.dart';
import '../screens/CityScreen.dart';
import '../screens/DeliveryPersonsLocationScreen.dart';
import '../screens/InvoiceSettingScreen.dart';
import '../screens/LanguageScreen.dart';
import '../screens/OrdersLocationScreen.dart';
import '../screens/PagesListscreen.dart';
import '../screens/PushNotificationListScreen.dart';
import '../screens/ReferenceProgramScreen.dart';
import '../screens/ReportScreen.dart';
import '../screens/SendPushNotificationScreen.dart';
import '../screens/WebsiteTrackOrderScreen.dart';
import '../screens/WebsiteWalkthroughScreen.dart';
import '../screens/WithdrawalRequestScreen.dart';

import '../main.dart';
import '../models/AppSettingModel.dart';
import '../models/LanguageDataModel.dart';
import '../models/models.dart';
import '../screens/ChangePasswordScreen.dart';
import '../screens/CountryScreen.dart';
import '../screens/CourierCompanyList.dart';
import '../screens/CustomerSupportScreen.dart';
import '../screens/DeliveryBoyFragment.dart';
import '../screens/DeliveryPersonDocumentScreen.dart';
import '../screens/DocumentScreen.dart';
import '../screens/ExtraChargesScreen.dart';
import '../screens/HomeFragment.dart';
import '../screens/OrderListFragment.dart';
import '../screens/ParcelTypeScreen.dart';
import '../screens/PaymentGatewayScreen.dart';
import '../screens/SettingFragment.dart';
import '../screens/ThemeScreen.dart';
import '../screens/UserListFragment.dart';
import '../screens/VehicleScreen.dart';
import '../screens/WebSiteInformationScreen.dart';
import '../screens/WebsiteAboutUsScreen.dart';
import '../screens/WebsiteClientReviewScreen.dart';
import '../screens/WebsiteContactUsScreen.dart';
import '../screens/WebsiteDeliveryPartnerScreen.dart';
import '../screens/WebsiteDownloadAppScreen.dart';
import '../screens/WebsitePrivacyPolicyScreen.dart';
import '../screens/WebsiteTermConditionScreen.dart';
import '../screens/WebsiteWhyDeliveryScreen.dart';
import '../utils/Constants.dart';
import 'Images.dart';

List<MenuItemModel> getAppDashboardItems() {
  List<MenuItemModel> list = [];
  list.add(MenuItemModel(index: DASHBOARD_INDEX, icon: AntDesign.home, title: language.dashboard, widget: HomeFragment()));
  list.add(MenuItemModel(index: ORDER_INDEX, icon: MaterialCommunityIcons.clipboard_text_outline, title: language.allOrder, widget: OrderListFragment()));
  list.add(MenuItemModel(index: USER_INDEX, icon: FontAwesome.user_o, title: language.users, widget: UserListFragment()));
  list.add(MenuItemModel(index: DELIVERY_PERSON_INDEX, icon: MaterialCommunityIcons.truck_delivery_outline, title: language.deliveryPerson, widget: DeliveryBoyFragment()));
  list.add(MenuItemModel(index: APP_SETTING_INDEX, icon: Ionicons.settings_outline, title: language.setting, widget: SettingFragment()));
  return list;
}

List<StaticPaymentModel> getStaticPaymentItems() {
  List<StaticPaymentModel> list = [];
  list.add(StaticPaymentModel(title: language.stripe, type: PAYMENT_GATEWAY_STRIPE));
  list.add(StaticPaymentModel(title: language.razorpay, type: PAYMENT_GATEWAY_RAZORPAY));
  list.add(StaticPaymentModel(title: language.payStack, type: PAYMENT_GATEWAY_PAYSTACK));
  list.add(StaticPaymentModel(title: language.flutterWave, type: PAYMENT_GATEWAY_FLUTTERWAVE));
  list.add(StaticPaymentModel(title: language.paypal, type: PAYMENT_GATEWAY_PAYPAL));
  list.add(StaticPaymentModel(title: language.payTabs, type: PAYMENT_GATEWAY_PAYTABS));
  list.add(StaticPaymentModel(title: language.myFatoorah, type: PAYMENT_GATEWAY_MYFATOORAH));
  // list.add(StaticPaymentModel(title: language.mercadoPago, type: PAYMENT_GATEWAY_MERCADOPAGO));
  list.add(StaticPaymentModel(title: language.paytm, type: PAYMENT_GATEWAY_PAYTM));
  return list;
}

List<LanguageDataModel> languageList() {
  return [
    LanguageDataModel(id: 1, name: 'English', subTitle: 'English', languageCode: 'en', fullLanguageCode: 'en-US', flag: 'assets/flag/ic_us.png'),
    LanguageDataModel(id: 2, name: 'Hindi', subTitle: 'हिंदी', languageCode: 'hi', fullLanguageCode: 'hi-IN', flag: 'assets/flag/ic_india.png'),
    LanguageDataModel(id: 3, name: 'Arabic', subTitle: 'عربي', languageCode: 'ar', fullLanguageCode: 'ar-AR', flag: 'assets/flag/ic_ar.png'),
    LanguageDataModel(id: 1, name: 'Spanish', subTitle: 'Española', languageCode: 'es', fullLanguageCode: 'es-ES', flag: 'assets/flag/ic_spain.png'),
    LanguageDataModel(id: 2, name: 'Afrikaans', subTitle: 'Afrikaans', languageCode: 'af', fullLanguageCode: 'af-AF', flag: 'assets/flag/ic_south_africa.png'),
    LanguageDataModel(id: 3, name: 'French', subTitle: 'Français', languageCode: 'fr', fullLanguageCode: 'fr-FR', flag: 'assets/flag/ic_france.png'),
    LanguageDataModel(id: 1, name: 'German', subTitle: 'Deutsch', languageCode: 'de', fullLanguageCode: 'de-DE', flag: 'assets/flag/ic_germany.png'),
    LanguageDataModel(id: 2, name: 'Indonesian', subTitle: 'bahasa Indonesia', languageCode: 'id', fullLanguageCode: 'id-ID', flag: 'assets/flag/ic_indonesia.png'),
    LanguageDataModel(id: 3, name: 'Portuguese', subTitle: 'Português', languageCode: 'pt', fullLanguageCode: 'pt-PT', flag: 'assets/flag/ic_portugal.png'),
    LanguageDataModel(id: 1, name: 'Turkish', subTitle: 'Türkçe', languageCode: 'tr', fullLanguageCode: 'tr-TR', flag: 'assets/flag/ic_turkey.png'),
    LanguageDataModel(id: 2, name: 'vietnamese', subTitle: 'Tiếng Việt', languageCode: 'vi', fullLanguageCode: 'vi-VI', flag: 'assets/flag/ic_vitnam.png'),
    LanguageDataModel(id: 3, name: 'Dutch', subTitle: 'Nederlands', languageCode: 'nl', fullLanguageCode: 'nl-NL', flag: 'assets/flag/ic_dutch.png'),
  ];
}

String? orderSettingStatus(String orderStatus) {
  if (orderStatus == ORDER_CREATED) {
    return language.create;
  } else if (orderStatus == ORDER_ACCEPTED) {
    return language.active;
  } else if (orderStatus == ORDER_ASSIGNED) {
    return language.courierAssigned;
  } else if (orderStatus == ORDER_TRANSFER) {
    return language.courierTransfer;
  } else if (orderStatus == ORDER_ARRIVED) {
    return language.courierArrived;
  } else if (orderStatus == ORDER_DELAYED) {
    return language.delayed;
  } else if (orderStatus == ORDER_CANCELLED) {
    return language.cancel;
  } else if (orderStatus == ORDER_PICKED_UP) {
    return language.courierPickedUp;
  } else if (orderStatus == ORDER_DEPARTED) {
    return language.courierDeparted;
  } else if (orderStatus == ORDER_PAYMENT) {
    return language.paymentStatusMessage;
  } else if (orderStatus == ORDER_FAIL) {
    return language.failed;
  } else if (orderStatus == ORDER_DELIVERED) {
    return language.completed;
  }
  return ORDER_CREATED;
}

Map<String, dynamic> getNotificationSetting() {
  List<NotificationSettings> list = [];
  list.add(NotificationSettings(active: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(create: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(courierAssigned: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(courierTransfer: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(courierArrived: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(delayed: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(cancelled: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(courierPickedUp: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(courierDeparted: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(completed: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(paymentStatusMessage: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));
  list.add(NotificationSettings(failed: Notifications(isOnesignalNotification: '0', isFirebaseNotification: '0')));

  Map<String, dynamic> map = Map.fromIterable(list, key: (e) => e.toJson().keys.first.toString(), value: (e) => e.toJson().values.first);

  return map;
}

List<MenuItemModel> getWebsiteSectionMenu() {
  List<MenuItemModel> list = [];
  list.add(MenuItemModel(index: WEBSITE_INFORMATION_INDEX, title: language.information, widget: WebSiteInformationScreen(), imagePath: ic_website_information));
  list.add(MenuItemModel(index: WEBSITE_WHY_DELIVERY_INDEX, title: language.whyDelivery, widget: WebsiteWhyDeliveryScreen(), imagePath: ic_vehicle));
  list.add(MenuItemModel(index: WEBSITE_CLIENT_REVIEW_INDEX, title: language.clientReview, widget: WebsiteClientReviewScreen(), imagePath: ic_website_client_review));
  list.add(MenuItemModel(index: WEBSITE_DOWNLOAD_APP_INDEX, title: language.downloadApp, widget: WebsiteDownloadAppScreen(), imagePath: ic_website_download_app));
  list.add(MenuItemModel(index: WEBSITE_DELIVERY_PARTNER_INDEX, title: language.deliveryPartner, widget: WebsiteDeliveryPartnerScreen(), imagePath: ic_delivery_boy_menu));
  list.add(MenuItemModel(index: WEBSITE_CONTACT_INFO_INDEX, title: language.contactInfo, widget: WebsiteContactUsScreen(), imagePath: ic_website_contact_info));
  list.add(MenuItemModel(index: WEBSITE_ABOUT_US_INDEX, title: language.aboutUs, widget: WebsiteAboutUsScreen(), imagePath: ic_website_about_us));
  list.add(MenuItemModel(index: WEBSITE_WALKTHROUGH_INDEX, title: language.walkThrough, widget: WebsiteWalkthroughScreen(), imagePath: ic_website_walk_through));
  list.add(MenuItemModel(index: WEBSITE_TRACK_ORDER_INDEX, title: language.trackOrder, widget: WebsiteTrackOrderScreen(), imagePath: ic_order));
  return list;
}

List<MenuItemModel> getMenuItems() {
  List<MenuItemModel> list = [];
  list.add(MenuItemModel(index: COUNTRY_INDEX, imagePath: ic_country, title: language.country, widget: CountryScreen()));
  list.add(MenuItemModel(index: CITY_INDEX, imagePath: ic_city, title: language.city, widget: CityScreen()));
  list.add(MenuItemModel(index: ORDER_LOCATION_INDEX, imagePath: ic_order_location, title: language.ordersLocation, widget: OrdersLocationScreen()));
  list.add(
    MenuItemModel(
      imagePath: ic_delivery_boy_menu,
      title: language.deliveryBoy,
      widget: DocumentScreen(),
      expandedIndex: DELIVERY_PERSON_EXPANDED_INDEX,
      expansionList: [
        MenuItemModel(index: DOCUMENT_INDEX, imagePath: ic_document, title: language.document, widget: DocumentScreen()),
        MenuItemModel(index: DELIVERY_PERSON_DOCUMENT_INDEX, imagePath: ic_delivery_document, title: language.deliveryPersonDocuments, widget: DeliveryPersonDocumentScreen()),
        MenuItemModel(index: DELIVERY_PERSON_LOCATION_INDEX, imagePath: ic_delivery_person_location, title: language.deliveryManLocation, widget: DeliveryLiveLocationScreen()),
      ],
    ),
  );
  list.add(
    MenuItemModel(
      imagePath: ic_settings,
      title: language.setting,
      widget: VehicleScreen(),
      expandedIndex: SETTING_EXPANDED_INDEX,
      expansionList: [
        MenuItemModel(index: VEHICLE_INDEX, imagePath: ic_vehicle, title: language.vehicle, widget: VehicleScreen()),
        MenuItemModel(index: EXTRA_CHARGES_INDEX, imagePath: ic_extra_charges, title: language.extraCharges, widget: ExtraChargesScreen()),
        MenuItemModel(index: PARCEL_TYPE_INDEX, imagePath: ic_parcel_type, title: language.parcelType, widget: ParcelTypeScreen()),
        MenuItemModel(index: PAYMENT_GATEWAY_INDEX, imagePath: ic_payment, title: language.paymentGateway, widget: PaymentGatewayScreen()),
      ],
    ),
  );
  // list.add(MenuItemModel(index: DELIVERY_PERSON_INDEX, imagePath: 'assets/icons/ic_delivery_boy.png', title: language.delivery_person, route: DeliveryBoyScreen.route));
  list.add(MenuItemModel(index: COURIER_COMPANY_INDEX, imagePath: ic_courier_company, title: language.courierCompany, widget: CourierCompanyListScreen()));
  list.add(MenuItemModel(
    imagePath: ic_withdrawal,
    title: language.withdrawRequest,
    widget: WithdrawalRequestScreen(),
    expandedIndex: WITHDRAW_INDEX,
    expansionList: [
      MenuItemModel(index: ALL_WITHDRAW_REQUEST, imagePath: ic_withdrawal, title: language.all, widget: WithdrawalRequestScreen()),
      MenuItemModel(index: PENDING_WITHDRAW_REQUEST, imagePath: ic_pending, title: language.pending, widget: WithdrawalRequestScreen(status: REQUESTED)),
      MenuItemModel(index: APPROVED_WITHDRAW_REQUEST, imagePath: ic_approved, title: language.approved, widget: WithdrawalRequestScreen(status: APPROVED)),
      MenuItemModel(index: CANCELLED_WITHDRAW_REQUEST, imagePath: ic_cancel, title: language.cancelled, widget: WithdrawalRequestScreen(status: DECLINE)),
    ],
  ));
  list.add(MenuItemModel(
    imagePath: ic_push_notification,
    title: language.pushNotification,
    widget: PushNotificationListScreen(),
    expandedIndex: PUSH_NOTIFICATION_INDEX,
    expansionList: [
      MenuItemModel(index: PUSH_NOTIFICATION_LIST_INDEX, imagePath: ic_push_notification, title: language.pushNotificationList, widget: PushNotificationListScreen()),
      MenuItemModel(index: SEND_PUSH_NOTIFICATION_INDEX, imagePath: ic_push_notification, title: language.sendPushNotification, widget: SendPushNotificationScreen()),
    ],
  ));
  list.add(MenuItemModel(index: INVOICE_SETTING_INDEX, imagePath: ic_invoice, title: language.invoiceSetting, widget: InvoiceSettingScreen()));
  list.add(MenuItemModel(index: APP_SETTING_INDEX, imagePath: ic_settings, title: language.appSetting, widget: AppSettingsScreen()));
  list.add(MenuItemModel(index: CHANGE_PASSWORD_INDEX, imagePath: ic_password, title: language.changePassword, widget: ChangePasswordScreen()));
  list.add(MenuItemModel(index: LANGUAGE_INDEX, imagePath: ic_language, title: language.language, widget: LanguageScreen()));
  list.add(MenuItemModel(index: THEME_INDEX, imagePath: ic_theme, title: language.theme, widget: ThemeScreen()));
  list.add(
    MenuItemModel(
      imagePath: ic_report,
      title: language.report,
      expandedIndex: REPORT_INDEX,
      widget: ReportScreen(),
      expansionList: [
        MenuItemModel(index: ORDER_REPORT_INDEX, imagePath: ic_report, title: language.orderReport, widget: ReportScreen(title: language.orderReport)),
        MenuItemModel(index: ADMIN_EARNING_REPORT_INDEX, imagePath: ic_report, title: language.adminEarningReport, widget: ReportScreen(title: language.adminEarningReport)),
        MenuItemModel(index: DELIVERYMAN_EARNING_REPORT_INDEX, imagePath: ic_report, title: language.deliveryManEarningReport, widget: ReportScreen(title: language.deliveryManEarningReport)),
        MenuItemModel(index: DELIVERYMAN_WISE_REPORT_INDEX, imagePath: ic_report, title: language.deliveryManWiseReport, widget: ReportScreen(title: language.deliveryManWiseReport)),
        MenuItemModel(index: USER_WISE_REPORT_INDEX, imagePath: ic_report, title: language.userWiseReport, widget: ReportScreen(title: language.userWiseReport)),
        MenuItemModel(index: CITY_WISE_REPORT_INDEX, imagePath: ic_report, title: language.cityWiseReport, widget: ReportScreen(title: language.cityWiseReport)),
        MenuItemModel(index: COUNTRY_WISE_REPORT_INDEX, imagePath: ic_report, title: language.countryWiseReport, widget: ReportScreen(title: language.countryWiseReport)),
      ],
    ),
  );
  list.add(MenuItemModel(index: REFERENCE_PARTNER_INDEX, imagePath: ic_invoice, title: language.referenceProgram, widget: ReferenceProgramScreen()));
  list.add(
    MenuItemModel(
      imagePath: ic_pages,
      title: language.pages,
      expandedIndex: PAGES_INDEX,
      widget: PagesListScreen(),
      expansionList: [
        MenuItemModel(index: PAGES_LIST_INDEX, imagePath: ic_pages, title: language.pagesList, widget: PagesListScreen()),
        MenuItemModel(index: WEBSITE_PRIVACY_POLICY_INDEX, imagePath: ic_pages, title: language.privacyPolicy, widget: WebsitePrivacyPolicyScreen()),
        MenuItemModel(index: WEBSITE_TERM_CONDITION_INDEX, imagePath: ic_pages, title: language.termCondition, widget: WebsiteTermConditionScreen()),
      ],
    ),
  );
  list.add(
    MenuItemModel(
      imagePath: ic_invoice,
      title: language.claimManagement,
      expandedIndex: CLAIMS_MANAGEMENT_INDEX,
      widget: CustomerSupportScreen(),
      expansionList: [
        MenuItemModel(index: ALL_CLAIMS_MANAGEMENT_INDEX, imagePath: ic_invoice, title: language.all, widget: ClaimListScreen()),
        MenuItemModel(index: PENDING_CLAIMS_MANAGEMENT_INDEX, imagePath: ic_pending, title: language.pending, widget: ClaimListScreen(status: SUPPORT_TICKET_STATUS_PENDING)),
        MenuItemModel(index: APPROVED_CLAIMS_MANAGEMENT_INDEX, imagePath: ic_approved, title: language.approved, widget: ClaimListScreen(status: APPROVED)),
        MenuItemModel(index: REJECTED_CLAIMS_MANAGEMENT_INDEX, imagePath: ic_cancel, title: language.rejected, widget: ClaimListScreen(status: REJECT)),
        MenuItemModel(index: CLOSED_CLAIMS_MANAGEMENT_INDEX, imagePath: ic_approved, title: language.closed, widget: ClaimListScreen(status: CLOSED)),
      ],
    ),
  );
  list.add(
    MenuItemModel(
      imagePath: ic_customer_support,
      title: language.customerSupport,
      expandedIndex: CUSTOMER_SUPPORT_INDEX,
      widget: CustomerSupportScreen(),
      expansionList: [
        MenuItemModel(index: CUSTOMER_SUPPORT_LIST_INDEX, imagePath: ic_customer_support, title:language.all, widget: CustomerSupportScreen()),
        MenuItemModel(index: PENDING_CUSTOMER_SUPPORT_LIST_INDEX, imagePath: ic_pending, title: language.pending, widget: CustomerSupportScreen(status: SUPPORT_TICKET_STATUS_PENDING)),
        MenuItemModel(index: INREVIEW_CUSTOMER_SUPPORT_LIST_INDEX, imagePath: ic_inReview, title: language.inReview, widget: CustomerSupportScreen(status: SUPPORT_TICKET_STATUS_IN_REVIEW)),
        MenuItemModel(index: RESOLVED_CUSTOMER_SUPPORT_LIST_INDEX, imagePath: ic_approved, title: language.resolved, widget: CustomerSupportScreen(status: SUPPORT_TICKET_RESOLVED)),
      ],
    ),
  );

  ///NEW ADDED LIST
  list.add(
    MenuItemModel(
      imagePath: ic_website_setting,
      title: language.websiteSection,
      expandedIndex: WEBSITE_SETTING_EXPANDED_INDEX,
      widget: WebSiteInformationScreen(),
      expansionList: [
        MenuItemModel(index: WEBSITE_INFORMATION_INDEX, imagePath: ic_website_information, title: language.information, widget: WebSiteInformationScreen()),
        MenuItemModel(index: WEBSITE_WHY_DELIVERY_INDEX, imagePath: ic_vehicle, title: language.whyDelivery, widget: WebsiteWhyDeliveryScreen()),
        MenuItemModel(index: WEBSITE_CLIENT_REVIEW_INDEX, imagePath: ic_website_client_review, title: language.clientReview, widget: WebsiteClientReviewScreen()),
        MenuItemModel(index: WEBSITE_DOWNLOAD_APP_INDEX, imagePath: ic_website_download_app, title: language.downloadApp, widget: WebsiteDownloadAppScreen()),
        MenuItemModel(index: WEBSITE_DELIVERY_PARTNER_INDEX, imagePath: ic_delivery_boy_menu, title: language.deliveryPartner, widget: WebsiteDeliveryPartnerScreen()),
        MenuItemModel(index: WEBSITE_CONTACT_INFO_INDEX, imagePath: ic_website_contact_info, title: language.contactInfo, widget: WebsiteContactUsScreen()),
        MenuItemModel(index: WEBSITE_ABOUT_US_INDEX, imagePath: ic_website_about_us, title: language.aboutUs, widget: WebsiteAboutUsScreen()),
        MenuItemModel(index: WEBSITE_WALKTHROUGH_INDEX, imagePath: ic_website_walk_through, title: language.walkThrough, widget: WebsiteWalkthroughScreen()),
        MenuItemModel(index: WEBSITE_TRACK_ORDER_INDEX, title: language.trackOrder, widget: WebsiteTrackOrderScreen(), imagePath: ic_order),
      ],
    ),
  );

  return list;
}
