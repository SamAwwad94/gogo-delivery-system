import 'package:flutter/material.dart';

class MenuItemModel {
  int? index;
  String? imagePath;
  String? title;
  Widget? widget;
  String? subtitle;
  bool? mISCheck;
  IconData? icon;
  int? expandedIndex;
  List<MenuItemModel>? expansionList;

  MenuItemModel({
    this.index,
    this.imagePath,
    this.title,
    this.widget,
    this.subtitle,
    this.mISCheck = false,
    this.icon,
    this.expandedIndex,
    this.expansionList,
  });
}

class StaticPaymentModel {
  String? title;
  String? type;

  StaticPaymentModel({@required this.title, @required this.type});
}

class FilterAttributeModel {
  String? orderStatus;
  String? orderType;
  String? fromDate;
  String? toDate;

  FilterAttributeModel({this.orderStatus,this.orderType, this.fromDate, this.toDate});

  FilterAttributeModel.fromJson(Map<String, dynamic> json) {
    orderStatus = json['order_status'];
    orderType = json['order_type'];
    fromDate = json['from_date'];
    toDate = json['to_date'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['order_status'] = this.orderStatus;
    data['order_type'] = this.orderType;
    data['from_date'] = this.fromDate;
    data['to_date'] = this.toDate;
    return data;
  }
}
