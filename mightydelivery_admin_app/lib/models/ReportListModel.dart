import '../models/PaginationModel.dart';

class ReportListModel {
  PaginationModel? pagination;
  List<ReportData>? data;
  num? totalAdminCommission;
  num? totalDeliveryManCommission;
  num? totalAmount;

  ReportListModel({
    this.pagination,
    this.data,
    this.totalAdminCommission,
    this.totalDeliveryManCommission,
    this.totalAmount,
  });

  factory ReportListModel.fromJson(Map<String, dynamic> json) => ReportListModel(
        pagination: json["pagination"] == null ? null : PaginationModel.fromJson(json["pagination"]),
        data: json["data"] == null ? [] : List<ReportData>.from(json["data"]!.map((x) => ReportData.fromJson(x))),
        totalAdminCommission: json["total_admin_commission"],
        totalDeliveryManCommission: json["total_delivery_man_commission"],
        totalAmount: json["total_amount"],
      );

  Map<String, dynamic> toJson() => {
        "pagination": pagination?.toJson(),
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "total_admin_commission": totalAdminCommission,
        "total_delivery_man_commission": totalDeliveryManCommission,
        "total_amount": totalAmount,
      };
}

class ReportData {
  String? orderTrackingId;
  int? orderId;
  int? clientId;
  String? client;
  int? deliveryManId;
  String? deliveryMan;
  String? city;
  String? country;
  num? totalAmount;
  DateTime? pickupDateTime;
  DateTime? deliveryDateTime;
  String? commissionType;
  num? adminCommission;
  num? deliveryManCommission;
  DateTime? createdAt;
  DateTime? updatedAt;

  ReportData({
    this.orderTrackingId,
    this.orderId,
    this.clientId,
    this.client,
    this.deliveryManId,
    this.deliveryMan,
    this.city,
    this.country,
    this.totalAmount,
    this.pickupDateTime,
    this.deliveryDateTime,
    this.commissionType,
    this.adminCommission,
    this.deliveryManCommission,
    this.createdAt,
    this.updatedAt,
  });

  factory ReportData.fromJson(Map<String, dynamic> json) => ReportData(
        orderTrackingId: json["tracking_id"],
        orderId: json["order_id"],
        clientId: json["client_id"],
        client: json["client"],
        deliveryManId: json["delivery_man_id"],
        deliveryMan: json["delivery_man"],
        city: json["city"],
        country: json["country"],
        totalAmount: json["total_amount"]?.toDouble(),
        pickupDateTime: json["pickup_date_time"] == null ? null : DateTime.parse(json["pickup_date_time"]),
        deliveryDateTime: json["delivery_date_time"] == null ? null : DateTime.parse(json["delivery_date_time"]),
        commissionType: json["commission_type"],
        adminCommission: json["admin_commission"],
        deliveryManCommission: json["delivery_man_commission"],
        createdAt: json["created_at"] == null ? null : DateTime.parse(json["created_at"]),
        updatedAt: json["updated_at"] == null ? null : DateTime.parse(json["updated_at"]),
      );

  Map<String, dynamic> toJson() => {
        "tracking_id": orderTrackingId,
        "order_id": orderId,
        "client_id": clientId,
        "client": client,
        "delivery_man_id": deliveryManId,
        "delivery_man": deliveryMan,
        "city": city,
        "country": country,
        "total_amount": totalAmount,
        "pickup_date_time": pickupDateTime?.toIso8601String(),
        "delivery_date_time": deliveryDateTime?.toIso8601String(),
        "commission_type": commissionType,
        "admin_commission": adminCommission,
        "delivery_man_commission": deliveryManCommission,
        "created_at": createdAt?.toIso8601String(),
        "updated_at": updatedAt?.toIso8601String(),
      };
}
