import 'CreateOrderDetailResponse.dart';
import 'VehicleModel.dart';

class OrderModel {
  String? orderTrackingId;
  int? id;
  int? clientId;
  String? clientName;
  String? date;
  PickupPoint? pickupPoint;
  PickupPoint? deliveryPoint;
  int? countryId;
  String? countryName;
  int? cityId;
  String? cityName;
  String? parcelType;
  num? totalWeight;
  num? totalDistance;
  String? pickupDatetime;
  String? deliveryDatetime;
  int? parentOrderId;
  String? status;
  int? paymentId;
  String? paymentType;
  String? paymentStatus;
  String? paymentCollectFrom;
  int? deliveryManId;
  String? deliveryManName;
  num? fixedCharges;
  var extraCharges;
  List<ExtraCharges>? extraChargesList;
  num? totalAmount;
  String? reason;
  int? pickupConfirmByClient;
  int? pickupConfirmByDeliveryMan;
  String? pickupTimeSignature;
  String? deliveryTimeSignature;
  String? deletedAt;
  bool? returnOrderId;
  num? weightCharge;
  num? insuranceCharge;
  num? distanceCharge;
  num? vehicleCharge;
  num? totalParcel;
  num? baseTotal;
  int? autoAssign;
  int? isShipped;
  List<dynamic>? cancelledDeliverManIds;
  String? createdAt;
  String? readableDate;
  int? vehicleId;
  VehicleData? vehicleData;
  String? vehicleImage;
  String? invoice;
  CityDetail? cityDetails;


  OrderModel({
    this.orderTrackingId,
    this.id,
    this.clientId,
    this.clientName,
    this.date,
    this.pickupPoint,
    this.deliveryPoint,
    this.countryId,
    this.countryName,
    this.cityId,
    this.cityName,
    this.parcelType,
    this.totalWeight,
    this.totalDistance,
    this.pickupDatetime,
    this.deliveryDatetime,
    this.parentOrderId,
    this.status,
    this.paymentId,
    this.paymentType,
    this.paymentStatus,
    this.paymentCollectFrom,
    this.deliveryManId,
    this.deliveryManName,
    this.fixedCharges,
    this.extraCharges,
    this.extraChargesList,
    this.totalAmount,
    this.reason,
    this.pickupConfirmByClient,
    this.pickupConfirmByDeliveryMan,
    this.pickupTimeSignature,
    this.deliveryTimeSignature,
    this.deletedAt,
    this.returnOrderId,
    this.weightCharge,
    this.insuranceCharge,
    this.distanceCharge,
    this.vehicleCharge,
    this.totalParcel,
    this.baseTotal,
    this.autoAssign,
    this.isShipped,
    this.cancelledDeliverManIds,
    this.createdAt,
    this.readableDate,
    this.vehicleId,
    this.vehicleData,
    this.vehicleImage,
    this.invoice,
    this.cityDetails,
  });

  OrderModel.fromJson(Map<String, dynamic> json) {
    orderTrackingId = json['order_tracking_id'];
    id = json['id'];
    clientId = json['client_id'];
    clientName = json['client_name'];
    date = json['date'];
    pickupPoint = json['pickup_point'] != null ? new PickupPoint.fromJson(json['pickup_point']) : null;
    deliveryPoint = json['delivery_point'] != null ? new PickupPoint.fromJson(json['delivery_point']) : null;
    countryId = json['country_id'];
    countryName = json['country_name'];
    cityId = json['city_id'];
    cityName = json['city_name'];
    parcelType = json['parcel_type'];
    totalWeight = json['total_weight'];
    totalDistance = json['total_distance'];
    pickupDatetime = json['pickup_datetime'];
    deliveryDatetime = json['delivery_datetime'];
    parentOrderId = json['parent_order_id'];
    status = json['status'];
    paymentId = json['payment_id'];
    paymentType = json['payment_type'];
    paymentStatus = json['payment_status'];
    paymentCollectFrom = json['payment_collect_from'];
    deliveryManId = json['delivery_man_id'];
    deliveryManName = json['delivery_man_name'];
    fixedCharges = json['fixed_charges'];
    extraCharges = json['extra_charges'];
    extraChargesList = json['extra_charges_list'];
    totalAmount = json['total_amount'];
    reason = json['reason'];
    pickupConfirmByClient = json['pickup_confirm_by_client'];
    pickupConfirmByDeliveryMan = json['pickup_confirm_by_delivery_man'];
    pickupTimeSignature = json['pickup_time_signature'];
    deliveryTimeSignature = json['delivery_time_signature'];
    deletedAt = json['deleted_at'];
    returnOrderId = json['return_order_id'];
    weightCharge = json['weight_charge'];
    insuranceCharge = json['insurance_charge'];
    distanceCharge = json['distance_charge'];
    vehicleCharge = json['vehicle_charge'];
    totalParcel = json['total_parcel'];
    baseTotal = json['base_total'];
    autoAssign = json['auto_assign'];
    isShipped = json['is_shipped'];
    cancelledDeliverManIds = json['cancelled_delivery_man_ids'];
    createdAt = json['created_at'];
    readableDate = json['readable_date'];
    vehicleId = json['vehicle_id'];
    vehicleData = json['vehicle_data'] != null
        ? new VehicleData.fromJson(json['vehicle_data'])
        : null;
    vehicleImage = json['vehicle_image'];
    invoice = json['invoice'];
    cityDetails = json['city_details_list'] != null ? CityDetail.fromJson(json['city_details_list']) : null;

  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['order_tracking_id'] = this.orderTrackingId;
    data['id'] = this.id;
    data['client_id'] = this.clientId;
    data['client_name'] = this.clientName;
    data['date'] = this.date;
    if (this.pickupPoint != null) {
      data['pickup_point'] = this.pickupPoint!.toJson();
    }
    if (this.deliveryPoint != null) {
      data['delivery_point'] = this.deliveryPoint!.toJson();
    }
    data['country_id'] = this.countryId;
    data['country_name'] = this.countryName;
    data['city_id'] = this.cityId;
    data['city_name'] = this.cityName;
    data['parcel_type'] = this.parcelType;
    data['total_weight'] = this.totalWeight;
    data['total_distance'] = this.totalDistance;
    data['pickup_datetime'] = this.pickupDatetime;
    data['delivery_datetime'] = this.deliveryDatetime;
    data['parent_order_id'] = this.parentOrderId;
    data['status'] = this.status;
    data['payment_id'] = this.paymentId;
    data['payment_type'] = this.paymentType;
    data['payment_status'] = this.paymentStatus;
    data['payment_collect_from'] = this.paymentCollectFrom;
    data['delivery_man_id'] = this.deliveryManId;
    data['delivery_man_name'] = this.deliveryManName;
    data['fixed_charges'] = this.fixedCharges;
    data['extra_charges'] = this.extraCharges;
    data['extra_charges_list'] = this.extraChargesList;
    data['total_amount'] = this.totalAmount;
    data['reason'] = this.reason;
    data['pickup_confirm_by_client'] = this.pickupConfirmByClient;
    data['pickup_confirm_by_delivery_man'] = this.pickupConfirmByDeliveryMan;
    data['pickup_time_signature'] = this.pickupTimeSignature;
    data['delivery_time_signature'] = this.deliveryTimeSignature;
    data['deleted_at'] = this.deletedAt;
    data['return_order_id'] = this.returnOrderId;
    data['weight_charge'] = this.weightCharge;
    data['insurance_charge'] = this.insuranceCharge;
    data['distance_charge'] = this.distanceCharge;
    data['vehicle_charge'] = this.vehicleCharge;
    data['total_parcel'] = this.totalParcel;
    data['base_total'] = this.baseTotal;
    data['auto_assign'] = this.autoAssign;
    data['is_shipped'] = this.isShipped;
    data['cancelled_delivery_man_ids'] = this.cancelledDeliverManIds;
    data['created_at'] = this.createdAt;
    data['readable_date'] = this.readableDate;
    data['vehicle_id'] = this.vehicleId;
    if (this.vehicleData != null) {
      data['vehicle_data'] = this.vehicleData!.toJson();
    }
    data['vehicle_image'] = this.vehicleImage;
    data['invoice'] = this.invoice;
    data['city_details_list'] = this.cityDetails;

    return data;
  }
}

class PickupPoint {
  String? address;
  String? latitude;
  String? longitude;
  String? description;
  String? contactNumber;
  String? startTime;
  String? endTime;

  PickupPoint({this.address, this.latitude, this.longitude, this.description, this.contactNumber, this.startTime, this.endTime});

  PickupPoint.fromJson(Map<String, dynamic> json) {
    address = json['address'];
    latitude = json['latitude'];
    longitude = json['longitude'];
    description = json['description'];
    contactNumber = json['contact_number'];
    startTime = json['start_time'];
    endTime = json['end_time'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['address'] = this.address;
    data['latitude'] = this.latitude;
    data['longitude'] = this.longitude;
    data['description'] = this.description;
    data['contact_number'] = this.contactNumber;
    data['start_time'] = this.startTime;
    data['end_time'] = this.endTime;
    return data;
  }
}