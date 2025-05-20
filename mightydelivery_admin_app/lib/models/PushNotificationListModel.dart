import '../models/PaginationModel.dart';

class PushNotificationListModel {
  PaginationModel? pagination;
  List<PushNotification>? data;

  PushNotificationListModel({
    this.pagination,
    this.data,
  });

  factory PushNotificationListModel.fromJson(Map<String, dynamic> json) => PushNotificationListModel(
    pagination: json["pagination"] == null ? null : PaginationModel.fromJson(json["pagination"]),
    data: json["data"] == null ? [] : List<PushNotification>.from(json["data"]!.map((x) => PushNotification.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "pagination": pagination?.toJson(),
    "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
  };
}

class PushNotification {
  int? id;
  String? title;
  String? message;
  int? forClient;
  int? forDeliveryMan;
  int? forAll;
  int? notificationCount;
  String? notificationImage;
  DateTime? createdAt;
  DateTime? updatedAt;

  PushNotification({
    this.id,
    this.title,
    this.message,
    this.forClient,
    this.forDeliveryMan,
    this.forAll,
    this.notificationCount,
    this.notificationImage,
    this.createdAt,
    this.updatedAt,
  });

  factory PushNotification.fromJson(Map<String, dynamic> json) => PushNotification(
    id: json["id"],
    title: json["title"],
    message: json["message"],
    forClient: json["for_client"],
    forDeliveryMan: json["for_delivery_man"],
    forAll: json["for_all"],
    notificationCount: json["notification_count"],
    notificationImage: json["notification_image"],
    createdAt: json["created_at"] == null ? null : DateTime.parse(json["created_at"]),
    updatedAt: json["updated_at"] == null ? null : DateTime.parse(json["updated_at"]),
  );

  Map<String, dynamic> toJson() => {
    "id": id,
    "title": title,
    "message": message,
    "for_client": forClient,
    "for_delivery_man": forDeliveryMan,
    "for_all": forAll,
    "notification_count": notificationCount,
    "notification_image": notificationImage,
    "created_at": createdAt?.toIso8601String(),
    "updated_at": updatedAt?.toIso8601String(),
  };
}
