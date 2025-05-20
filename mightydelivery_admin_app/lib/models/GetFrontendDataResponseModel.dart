class GetFrontendDataResponseModel {
  String? playStoreLink;
  String? appStoreLink;
  String? companyName;
  String? appName;
  String? downloadText;
  String? purchaseUrl;
  String? createOrderDescription;
  String? downloadFooterContent;
  String? appSsImage;
  String? appLogoImage;
  String? deliveryManImage;
  String? deliveryRoadImage;
  String? privacyPolicy;
  String? termAndCondition;
  Appsetting? appsetting;
  AboutUs? aboutUs;
  DownloadApp? downloadApp;
  ContactUs? contactUs;
  DeliveryPartner? deliveryPartner;
  WhyChoose? whyChoose;
  ClientsReviewNew? clientReview;
  List<Walkthrough>? walkthrough;
  TrackOrder? trackOrder;

  GetFrontendDataResponseModel(
      {this.playStoreLink,
      this.appStoreLink,
      this.companyName,
      this.appName,
      this.downloadText,
      this.purchaseUrl,
      this.createOrderDescription,
      this.downloadFooterContent,
      this.appSsImage,
      this.appLogoImage,
      this.deliveryManImage,
      this.deliveryRoadImage,
      this.privacyPolicy,
      this.termAndCondition,
      this.appsetting,
      this.aboutUs,
      this.downloadApp,
      this.contactUs,
      this.deliveryPartner,
      this.whyChoose,
      this.clientReview,
      this.walkthrough,
      this.trackOrder});

  GetFrontendDataResponseModel.fromJson(Map<String, dynamic> json) {
    playStoreLink = json['play_store_link'];
    appStoreLink = json['app_store_link'];
    companyName = (json['company_name'] != null) ? json['company_name'] : "XXXXXXXXXXXXXX";
    appName = (json['app_name'] != null) ? json['app_name'] : "XXXXXXX";
    downloadText = (json['download_text'] != null) ? json['download_text'] : "XXXXXXXXXXXXXX";
    purchaseUrl = json['purchase_url'];
    createOrderDescription = (json['create_order_description'] != null)
        ? json['create_order_description']
        : "XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX";
    downloadFooterContent = (json['download_footer_content'] != null) ? json['download_footer_content'] : "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    appSsImage = json['app_ss_image'];
    appLogoImage = json['app_logo_image'];
    deliveryManImage = json['delivery_man_image'];
    deliveryRoadImage = json['delivery_road_image'];
    privacyPolicy = (json['privacy_policy'] != null)
        ? json['privacy_policy']
        : "XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX";
    termAndCondition = (json['term_and_condition'] != null)
        ? json['term_and_condition']
        : "XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX";
    appsetting = json['appsetting'] != null ? new Appsetting.fromJson(json['appsetting']) : null;
    aboutUs = json['about_us'] != null ? new AboutUs.fromJson(json['about_us']) : null;
    downloadApp = json['download_app'] != null ? new DownloadApp.fromJson(json['download_app']) : null;
    contactUs = json['contact_us'] != null ? new ContactUs.fromJson(json['contact_us']) : null;
    deliveryPartner = json['delivery_partner'] != null ? new DeliveryPartner.fromJson(json['delivery_partner']) : null;
    whyChoose = json['why_choose'] != null ? new WhyChoose.fromJson(json['why_choose']) : null;
    clientReview = json['client_review'] != null ? new ClientsReviewNew.fromJson(json['client_review']) : null;
    trackOrder = json['track_order'] != null ? new TrackOrder.fromJson(json['track_order']) : null;
    if (json['walkthrough'] != null) {
      walkthrough = <Walkthrough>[];
      json['walkthrough'].forEach((v) {
        walkthrough!.add(new Walkthrough.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['play_store_link'] = this.playStoreLink;
    data['app_store_link'] = this.appStoreLink;
    data['company_name'] = this.companyName;
    data['app_name'] = this.appName;
    data['download_text'] = this.downloadText;
    data['purchase_url'] = this.purchaseUrl;
    data['create_order_description'] = this.createOrderDescription;
    data['download_footer_content'] = this.downloadFooterContent;
    data['app_ss_image'] = this.appSsImage;
    data['app_logo_image'] = this.appLogoImage;
    data['delivery_man_image'] = this.deliveryManImage;
    data['delivery_road_image'] = this.deliveryRoadImage;
    data['privacy_policy'] = this.privacyPolicy;
    data['term_and_condition'] = this.termAndCondition;
    if (this.appsetting != null) {
      data['appsetting'] = this.appsetting!.toJson();
    }
    if (this.aboutUs != null) {
      data['about_us'] = this.aboutUs!.toJson();
    }
    if (this.downloadApp != null) {
      data['download_app'] = this.downloadApp!.toJson();
    }
    if (this.contactUs != null) {
      data['contact_us'] = this.contactUs!.toJson();
    }
    if (this.deliveryPartner != null) {
      data['delivery_partner'] = this.deliveryPartner!.toJson();
    }
    if (this.whyChoose != null) {
      data['why_choose'] = this.whyChoose!.toJson();
    }
    if (this.clientReview != null) {
      data['client_review'] = this.clientReview!.toJson();
    }
    if (this.trackOrder != null) {
      data['track_order'] = this.trackOrder!.toJson();
    }
    if (this.walkthrough != null) {
      data['walkthrough'] = this.walkthrough!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class Appsetting {
  int? id;
  String? siteName;
  String? siteEmail;
  String? siteDescription;
  String? siteCopyright;
  String? facebookUrl;
  String? twitterUrl;
  String? linkedinUrl;
  String? instagramUrl;
  String? supportNumber;
  String? supportEmail;
  NotificationSettings? notificationSettings;
  int? autoAssign;
  String? distanceUnit;
  int? distance;
  int? otpVerifyOnPickupDelivery;
  String? currency;
  String? currencyCode;
  String? currencyPosition;
  int? isVehicleInOrder;
  String? createdAt;
  String? updatedAt;

  Appsetting(
      {this.id,
      this.siteName,
      this.siteEmail,
      this.siteDescription,
      this.siteCopyright,
      this.facebookUrl,
      this.twitterUrl,
      this.linkedinUrl,
      this.instagramUrl,
      this.supportNumber,
      this.supportEmail,
      this.notificationSettings,
      this.autoAssign,
      this.distanceUnit,
      this.distance,
      this.otpVerifyOnPickupDelivery,
      this.currency,
      this.currencyCode,
      this.currencyPosition,
      this.isVehicleInOrder,
      this.createdAt,
      this.updatedAt});

  Appsetting.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    siteName = (json['site_name'] != null) ? json['site_name'] : "XXXXXXXXX";
    siteEmail = (json['site_email'] != null) ? json['site_email'] : "XXXXXXXXXX XXXXXXX";
    siteDescription = (json['site_description'] != null) ? json['site_description'] : "XXXXXXXX XXXXXXXXX";
    siteCopyright = json['site_copyright'];
    facebookUrl = json['facebook_url'];
    twitterUrl = json['twitter_url'];
    linkedinUrl = json['linkedin_url'];
    instagramUrl = json['instagram_url'];
    supportNumber = (json['support_number'] != null) ? json['support_number'] : "XXXXXX XXXXXXXX";
    supportEmail = (json['support_email'] != null) ? json['support_email'] : "XXXXXX XXXXXXXX";
    notificationSettings = json['notification_settings'] != null ? new NotificationSettings.fromJson(json['notification_settings']) : null;
    autoAssign = json['auto_assign'];
    distanceUnit = json['distance_unit'];
    distance = json['distance'];
    otpVerifyOnPickupDelivery = json['otp_verify_on_pickup_delivery'];
    currency = json['currency'];
    currencyCode = json['currency_code'];
    currencyPosition = json['currency_position'];
    isVehicleInOrder = json['is_vehicle_in_order'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['site_name'] = this.siteName;
    data['site_email'] = this.siteEmail;
    data['site_description'] = this.siteDescription;
    data['site_copyright'] = this.siteCopyright;
    data['facebook_url'] = this.facebookUrl;
    data['twitter_url'] = this.twitterUrl;
    data['linkedin_url'] = this.linkedinUrl;
    data['instagram_url'] = this.instagramUrl;
    data['support_number'] = this.supportNumber;
    data['support_email'] = this.supportEmail;
    if (this.notificationSettings != null) {
      data['notification_settings'] = this.notificationSettings!.toJson();
    }
    data['auto_assign'] = this.autoAssign;
    data['distance_unit'] = this.distanceUnit;
    data['distance'] = this.distance;
    data['otp_verify_on_pickup_delivery'] = this.otpVerifyOnPickupDelivery;
    data['currency'] = this.currency;
    data['currency_code'] = this.currencyCode;
    data['currency_position'] = this.currencyPosition;
    data['is_vehicle_in_order'] = this.isVehicleInOrder;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}

class NotificationSettings {
  Active? active;
  Active? create;
  Active? failed;
  Active? delayed;
  Active? cancelled;
  Active? completed;
  Active? courierArrived;
  Active? courierAssigned;
  Active? courierDeparted;
  Active? courierTransfer;
  Active? courierPickedUp;
  Active? paymentStatusMessage;

  NotificationSettings(
      {this.active,
      this.create,
      this.failed,
      this.delayed,
      this.cancelled,
      this.completed,
      this.courierArrived,
      this.courierAssigned,
      this.courierDeparted,
      this.courierTransfer,
      this.courierPickedUp,
      this.paymentStatusMessage});

  NotificationSettings.fromJson(Map<String, dynamic> json) {
    active = json['active'] != null ? new Active.fromJson(json['active']) : null;
    create = json['create'] != null ? new Active.fromJson(json['create']) : null;
    failed = json['failed'] != null ? new Active.fromJson(json['failed']) : null;
    delayed = json['delayed'] != null ? new Active.fromJson(json['delayed']) : null;
    cancelled = json['cancelled'] != null ? new Active.fromJson(json['cancelled']) : null;
    completed = json['completed'] != null ? new Active.fromJson(json['completed']) : null;
    courierArrived = json['courier_arrived'] != null ? new Active.fromJson(json['courier_arrived']) : null;
    courierAssigned = json['courier_assigned'] != null ? new Active.fromJson(json['courier_assigned']) : null;
    courierDeparted = json['courier_departed'] != null ? new Active.fromJson(json['courier_departed']) : null;
    courierTransfer = json['courier_transfer'] != null ? new Active.fromJson(json['courier_transfer']) : null;
    courierPickedUp = json['courier_picked_up'] != null ? new Active.fromJson(json['courier_picked_up']) : null;
    paymentStatusMessage = json['payment_status_message'] != null ? new Active.fromJson(json['payment_status_message']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    if (this.active != null) {
      data['active'] = this.active!.toJson();
    }
    if (this.create != null) {
      data['create'] = this.create!.toJson();
    }
    if (this.failed != null) {
      data['failed'] = this.failed!.toJson();
    }
    if (this.delayed != null) {
      data['delayed'] = this.delayed!.toJson();
    }
    if (this.cancelled != null) {
      data['cancelled'] = this.cancelled!.toJson();
    }
    if (this.completed != null) {
      data['completed'] = this.completed!.toJson();
    }
    if (this.courierArrived != null) {
      data['courier_arrived'] = this.courierArrived!.toJson();
    }
    if (this.courierAssigned != null) {
      data['courier_assigned'] = this.courierAssigned!.toJson();
    }
    if (this.courierDeparted != null) {
      data['courier_departed'] = this.courierDeparted!.toJson();
    }
    if (this.courierTransfer != null) {
      data['courier_transfer'] = this.courierTransfer!.toJson();
    }
    if (this.courierPickedUp != null) {
      data['courier_picked_up'] = this.courierPickedUp!.toJson();
    }
    if (this.paymentStatusMessage != null) {
      data['payment_status_message'] = this.paymentStatusMessage!.toJson();
    }
    return data;
  }
}

class Active {
  String? iSFIREBASENOTIFICATION;
  String? iSONESIGNALNOTIFICATION;

  Active({this.iSFIREBASENOTIFICATION, this.iSONESIGNALNOTIFICATION});

  Active.fromJson(Map<String, dynamic> json) {
    iSFIREBASENOTIFICATION = json['IS_FIREBASE_NOTIFICATION'];
    iSONESIGNALNOTIFICATION = json['IS_ONESIGNAL_NOTIFICATION'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['IS_FIREBASE_NOTIFICATION'] = this.iSFIREBASENOTIFICATION;
    data['IS_ONESIGNAL_NOTIFICATION'] = this.iSONESIGNALNOTIFICATION;
    return data;
  }
}

class AboutUs {
  String? sortDes;
  String? longDes;
  String? downloadTitle;
  String? downloadSubtitle;
  String? aboutUsAppSs;

  AboutUs({this.sortDes, this.longDes, this.downloadTitle, this.downloadSubtitle, this.aboutUsAppSs});

  AboutUs.fromJson(Map<String, dynamic> json) {
    sortDes = (json['sort_des'] != null) ? json['sort_des'] : "XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX";
    longDes = (json['long_des'] != null)
        ? json['long_des']
        : "XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX, XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXXX XXXXXXXX XXXXXXXXXX XXXXXXX XXXXXXX";
    downloadTitle = (json['download_title'] != null) ? json['download_title'] : "XXXXXXXXXX XXXXXXX XXXXXXXX ";
    downloadSubtitle = (json['download_subtitle'] != null) ? json['download_subtitle'] : "XXXXXXXXXX XXXXXXX XXXXXXXX ";
    aboutUsAppSs = json['about_us_app_ss'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['sort_des'] = this.sortDes;
    data['long_des'] = this.longDes;
    data['download_title'] = this.downloadTitle;
    data['download_subtitle'] = this.downloadSubtitle;
    data['about_us_app_ss'] = this.aboutUsAppSs;
    return data;
  }
}

class DownloadApp {
  String? downloadTitle;
  String? downloadDescription;
  String? downloadAppLogo;

  DownloadApp({this.downloadTitle, this.downloadDescription, this.downloadAppLogo});

  DownloadApp.fromJson(Map<String, dynamic> json) {
    downloadTitle = (json['download_title'] != null) ? json['download_title'] : "XXXXXXXXXX XXXXXXX XXXXXXXX ";
    downloadDescription = (json['download_description'] != null) ? json['download_description'] : "XXXXXXXXXX XXXXXXX XXXXXXXX ";
    downloadAppLogo = json['download_app_logo'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['download_title'] = this.downloadTitle;
    data['download_description'] = this.downloadDescription;
    data['download_app_logo'] = this.downloadAppLogo;
    return data;
  }
}

class ContactUs {
  String? contactTitle;
  String? contactSubtitle;
  String? contactUsAppSs;

  ContactUs({this.contactTitle, this.contactSubtitle, this.contactUsAppSs});

  ContactUs.fromJson(Map<String, dynamic> json) {
    contactTitle = (json['contact_title'] != null) ? json['contact_title'] : "XXXXXXXXXX XXXXXXX XXXXXXXX ";
    contactSubtitle = (json['contact_subtitle'] != null) ? json['contact_subtitle'] : "XXXXXXXXXX XXXXXXX XXXXXXXX ";
    contactUsAppSs = json['contact_us_app_ss'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['contact_title'] = this.contactTitle;
    data['contact_subtitle'] = this.contactSubtitle;
    data['contact_us_app_ss'] = this.contactUsAppSs;
    return data;
  }
}

class DeliveryPartner {
  String? title;
  String? subtitle;
  String? image;
  List<Benefits>? benefits;

  DeliveryPartner({this.title, this.subtitle, this.image, this.benefits});

  DeliveryPartner.fromJson(Map<String, dynamic> json) {
    title = json['title'];
    subtitle = json['subtitle'];
    image = json['image'];
    if (json['benefits'] != null) {
      benefits = <Benefits>[];
      json['benefits'].forEach((v) {
        benefits!.add(new Benefits.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['title'] = this.title;
    data['subtitle'] = this.subtitle;
    data['image'] = this.image;
    if (this.benefits != null) {
      data['benefits'] = this.benefits!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class Benefits {
  int? id;
  String? title;
  String? subtitle;
  String? type;
  String? description;
  String? image;
  String? createdAt;
  String? updatedAt;

  Benefits({this.id, this.title, this.subtitle, this.type, this.description, this.image, this.createdAt, this.updatedAt});

  Benefits.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    title = json['title'];
    subtitle = json['subtitle'];
    type = json['type'];
    description = json['description'];
    image = json['frontend_data_image'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['title'] = this.title;
    data['subtitle'] = this.subtitle;
    data['type'] = this.type;
    data['description'] = this.description;
    data['frontend_data_image'] = this.image;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}

class WhyChoose {
  String? title;
  String? description;
  List<WhyChooseData>? data;

  WhyChoose({this.title, this.description, this.data});

  WhyChoose.fromJson(Map<String, dynamic> json) {
    title = (json['title'] != null) ? json['title'] : "XXXXXXXXXX XXXXXXX XXXXXXXX";
    description = (json['description'] != null) ? json['description'] : "XXXXXXXXXX XXXXXXX XXXXXXXX";
    if (json['data'] != null) {
      data = <WhyChooseData>[];
      json['data'].forEach((v) {
        data!.add(new WhyChooseData.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['title'] = this.title;
    data['description'] = this.description;
    if (this.data != null) {
      data['data'] = this.data!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class WhyChooseData {
  int? id;
  String? title;
  String? subtitle;
  String? type;
  String? description;
  String? image;
  String? createdAt;
  String? updatedAt;

  WhyChooseData({this.id, this.title, this.subtitle, this.type, this.description, this.image, this.createdAt, this.updatedAt});

  WhyChooseData.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    title = (json['title'] != null) ? json['title'] : "XXXXXXXXXX XXXXXXX XXXXXXXX";
    subtitle = (json['subtitle'] != null) ? json['subtitle'] : "XXXXXXXXXX XXXXXXX XXXXXXXX";
    type = json['type'];
    description = (json['description'] != null) ? json['description'] : "XXXXXXXXXX XXXXXXX XXXXXXXX";
    image = json['frontend_data_image'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['title'] = this.title;
    data['subtitle'] = this.subtitle;
    data['type'] = this.type;
    data['description'] = this.description;
    data['frontend_data_image'] = this.image;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}

class ClientsReviewNew {
  String? clientReviewTitle;
  List<ClientReviewData>? data;

  ClientsReviewNew({this.clientReviewTitle, this.data});

  ClientsReviewNew.fromJson(Map<String, dynamic> json) {
    clientReviewTitle = (json['client_review_title'] != null) ? json['client_review_title'] : "XXXXXXX XXXXXXX";
    if (json['data'] != null) {
      data = <ClientReviewData>[];
      json['data'].forEach((v) {
        data!.add(new ClientReviewData.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['client_review_title'] = this.clientReviewTitle;
    if (this.data != null) {
      data['data'] = this.data!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class ClientReviewData {
  int? id;
  String? name;
  String? email;
  String? type;
  String? review;
  String? image;
  String? createdAt;
  String? updatedAt;

  ClientReviewData({this.id, this.name, this.email, this.type, this.review, this.image, this.createdAt, this.updatedAt});

  ClientReviewData.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    name = (json['title'] != null) ? json['title'] : "XXXXXXXXXX XXXXXXX XXXXXXXX";
    email = (json['subtitle'] != null) ? json['subtitle'] : "XXXXXXXXXX XXXXXXX XXXXXXXX";
    type = json['type'];
    review = json['description'];
    image = json['frontend_data_image'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['title'] = this.name;
    data['subtitle'] = this.email;
    data['type'] = this.type;
    data['description'] = this.review;
    data['frontend_data_image'] = this.image;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}

class Walkthrough {
  int? id;
  String? title;
  String? subtitle;
  String? type;
  String? description;
  String? image;
  String? createdAt;
  String? updatedAt;

  Walkthrough({this.id, this.title, this.subtitle, this.type, this.description, this.image, this.createdAt, this.updatedAt});

  Walkthrough.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    title = json['title'];
    subtitle = json['subtitle'];
    type = json['type'];
    description = json['description'];
    image = json['frontend_data_image'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['title'] = this.title;
    data['subtitle'] = this.subtitle;
    data['type'] = this.type;
    data['description'] = this.description;
    data['frontend_data_image'] = this.image;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}

class TrackOrder {
  String? trackOrderTitle;
  String? trackOrderSubtitle;
  String? trackPageTitle;
  String? trackPageDescription;

  TrackOrder({
    this.trackOrderTitle,
    this.trackOrderSubtitle,
    this.trackPageTitle,
    this.trackPageDescription,
  });

  factory TrackOrder.fromJson(Map<String, dynamic> json) => TrackOrder(
    trackOrderTitle: json["track_order_title"],
    trackOrderSubtitle: json["track_order_subtitle"],
    trackPageTitle: json["track_page_title"],
    trackPageDescription: json["track_page_description"],
  );

  Map<String, dynamic> toJson() => {
    "track_order_title": trackOrderTitle,
    "track_order_subtitle": trackOrderSubtitle,
    "track_page_title": trackPageTitle,
    "track_page_description": trackPageDescription,
  };
}
