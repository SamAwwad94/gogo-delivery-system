import '../models/PaginationModel.dart';

class ReferenceListModel {
  PaginationModel? pagination;
  List<ReferenceData>? data;

  ReferenceListModel({
    this.pagination,
    this.data,
  });

  factory ReferenceListModel.fromJson(Map<String, dynamic> json) => ReferenceListModel(
    pagination: json["pagination"] == null ? null : PaginationModel.fromJson(json["pagination"]),
    data: json["data"] == null ? [] : List<ReferenceData>.from(json["data"]!.map((x) => ReferenceData.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "pagination": pagination?.toJson(),
    "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
  };
}

class ReferenceData {
  int? id;
  String? name;
  String? email;
  String? username;
  int? status;
  String? userType;
  int? countryId;
  String? countryName;
  int? cityId;
  String? cityName;
  dynamic address;
  String? contactNumber;
  String? profileImage;
  dynamic loginType;
  dynamic latitude;
  dynamic longitude;
  String? uid;
  dynamic playerId;
  dynamic fcmToken;
  dynamic lastNotificationSeen;
  int? isVerifiedDeliveryMan;
  DateTime? createdAt;
  DateTime? updatedAt;
  dynamic deletedAt;
  dynamic userBankAccount;
  DateTime? otpVerifyAt;
  DateTime? emailVerifiedAt;
  DateTime? documentVerifiedAt;
  String? appVersion;
  String? appSource;
  DateTime? lastActivedAt;
  bool? isEmailVerification;
  bool? isMobileVerification;
  bool? isDocumentVerification;
  String? referralCode;
  String? partnerReferralCode;
  dynamic vehicleId;
  dynamic deliverymanVehicleHistory;

  ReferenceData({
    this.id,
    this.name,
    this.email,
    this.username,
    this.status,
    this.userType,
    this.countryId,
    this.countryName,
    this.cityId,
    this.cityName,
    this.address,
    this.contactNumber,
    this.profileImage,
    this.loginType,
    this.latitude,
    this.longitude,
    this.uid,
    this.playerId,
    this.fcmToken,
    this.lastNotificationSeen,
    this.isVerifiedDeliveryMan,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
    this.userBankAccount,
    this.otpVerifyAt,
    this.emailVerifiedAt,
    this.documentVerifiedAt,
    this.appVersion,
    this.appSource,
    this.lastActivedAt,
    this.isEmailVerification,
    this.isMobileVerification,
    this.isDocumentVerification,
    this.referralCode,
    this.partnerReferralCode,
    this.vehicleId,
    this.deliverymanVehicleHistory,
  });

  factory ReferenceData.fromJson(Map<String, dynamic> json) => ReferenceData(
    id: json["id"],
    name: json["name"],
    email: json["email"],
    username: json["username"],
    status: json["status"],
    userType: json["user_type"],
    countryId: json["country_id"],
    countryName: json["country_name"],
    cityId: json["city_id"],
    cityName: json["city_name"],
    address: json["address"],
    contactNumber: json["contact_number"],
    profileImage: json["profile_image"],
    loginType: json["login_type"],
    latitude: json["latitude"],
    longitude: json["longitude"],
    uid: json["uid"],
    playerId: json["player_id"],
    fcmToken: json["fcm_token"],
    lastNotificationSeen: json["last_notification_seen"],
    isVerifiedDeliveryMan: json["is_verified_delivery_man"],
    createdAt: json["created_at"] == null ? null : DateTime.parse(json["created_at"]),
    updatedAt: json["updated_at"] == null ? null : DateTime.parse(json["updated_at"]),
    deletedAt: json["deleted_at"],
    userBankAccount: json["user_bank_account"],
    otpVerifyAt: json["otp_verify_at"] == null ? null : DateTime.parse(json["otp_verify_at"]),
    emailVerifiedAt: json["email_verified_at"] == null ? null : DateTime.parse(json["email_verified_at"]),
    documentVerifiedAt: json["document_verified_at"] == null ? null : DateTime.parse(json["document_verified_at"]),
    appVersion: json["app_version"],
    appSource: json["app_source"],
    lastActivedAt: json["last_actived_at"] == null ? null : DateTime.parse(json["last_actived_at"]),
    isEmailVerification: json["is_email_verification"],
    isMobileVerification: json["is_mobile_verification"],
    isDocumentVerification: json["is_document_verification"],
    referralCode: json["referral_code"],
    partnerReferralCode: json["partner_referral_code"],
    vehicleId: json["vehicle_id"],
    deliverymanVehicleHistory: json["DeliverymanVehicleHistory"],
  );

  Map<String, dynamic> toJson() => {
    "id": id,
    "name": name,
    "email": email,
    "username": username,
    "status": status,
    "user_type": userType,
    "country_id": countryId,
    "country_name": countryName,
    "city_id": cityId,
    "city_name": cityName,
    "address": address,
    "contact_number": contactNumber,
    "profile_image": profileImage,
    "login_type": loginType,
    "latitude": latitude,
    "longitude": longitude,
    "uid": uid,
    "player_id": playerId,
    "fcm_token": fcmToken,
    "last_notification_seen": lastNotificationSeen,
    "is_verified_delivery_man": isVerifiedDeliveryMan,
    "created_at": createdAt?.toIso8601String(),
    "updated_at": updatedAt?.toIso8601String(),
    "deleted_at": deletedAt,
    "user_bank_account": userBankAccount,
    "otp_verify_at": otpVerifyAt?.toIso8601String(),
    "email_verified_at": emailVerifiedAt?.toIso8601String(),
    "document_verified_at": documentVerifiedAt?.toIso8601String(),
    "app_version": appVersion,
    "app_source": appSource,
    "last_actived_at": lastActivedAt?.toIso8601String(),
    "is_email_verification": isEmailVerification,
    "is_mobile_verification": isMobileVerification,
    "is_document_verification": isDocumentVerification,
    "referral_code": referralCode,
    "partner_referral_code": partnerReferralCode,
    "vehicle_id": vehicleId,
    "DeliverymanVehicleHistory": deliverymanVehicleHistory,
  };
}


