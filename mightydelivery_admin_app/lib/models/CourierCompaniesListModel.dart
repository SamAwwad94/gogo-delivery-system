import '../models/PaginationModel.dart';

class CourierCompaniesList {
  int? status;
  PaginationModel? pagination;
  List<CourierCompany>? data;

  CourierCompaniesList({
    this.status,
    this.pagination,
    this.data,
  });

  factory CourierCompaniesList.fromJson(Map<String, dynamic> json) => CourierCompaniesList(
    status: json["status"],
    pagination: json["pagination"] == null ? null : PaginationModel.fromJson(json["pagination"]),
    data: json["data"] == null ? [] : List<CourierCompany>.from(json["data"]!.map((x) => CourierCompany.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "status": status,
    "pagination": pagination?.toJson(),
    "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
  };
}

class CourierCompany {
  int? id;
  String? name;
  String? link;
  String? image;
  DateTime? createdAt;
  DateTime? updatedAt;

  CourierCompany({
    this.id,
    this.name,
    this.link,
    this.image,
    this.createdAt,
    this.updatedAt,
  });

  factory CourierCompany.fromJson(Map<String, dynamic> json) => CourierCompany(
    id: json["id"],
    name: json["name"],
    link: json["link"],
    image: json["image"],
    createdAt: json["created_at"] == null ? null : DateTime.parse(json["created_at"]),
    updatedAt: json["updated_at"] == null ? null : DateTime.parse(json["updated_at"]),
  );

  Map<String, dynamic> toJson() => {
    "id": id,
    "name": name,
    "link": link,
    "image": image,
    "created_at": createdAt?.toIso8601String(),
    "updated_at": updatedAt?.toIso8601String(),
  };
}