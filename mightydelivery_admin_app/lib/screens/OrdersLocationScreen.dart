import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../utils/Extensions/ResponsiveWidget.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../../main.dart';
import '../../models/OrderModel.dart';
import '../../network/RestApis.dart';
import '../../utils/Constants.dart';
import '../extensions/widgets.dart';
import '../utils/Common.dart';
import '../utils/Extensions/app_common.dart';

class OrdersLocationScreen extends StatefulWidget {
  static String route = '/admin/ordersmap';

  @override
  OrdersLocationScreenState createState() => OrdersLocationScreenState();
}

class OrdersLocationScreenState extends State<OrdersLocationScreen> {
  GoogleMapController? controller;
  List<OrderModel> orderData = [];
  List<Marker> markers = [];

  late BitmapDescriptor sourceIcon;
  late BitmapDescriptor destinationIcon;

  List<String> statusList = [ORDER_ACCEPTED, ORDER_ASSIGNED, ORDER_ARRIVED, ORDER_PICKED_UP, ORDER_DEPARTED];
  List<String> selectedStatusList = [ORDER_ACCEPTED, ORDER_ASSIGNED, ORDER_ARRIVED, ORDER_PICKED_UP, ORDER_DEPARTED];

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    sourceIcon = await BitmapDescriptor.fromAssetImage(ImageConfiguration(devicePixelRatio: 1), 'assets/icons/ic_pickup_pin.png', mipmaps: false);
    destinationIcon = await BitmapDescriptor.fromAssetImage(ImageConfiguration(devicePixelRatio: 1), 'assets/icons/ic_drop_pin.png', mipmaps: false);
    getOrderListApi();
  }

  Future<void> onMapCreated(GoogleMapController cont) async {
    if (markers.isNotEmpty) {
      cont.moveCamera(CameraUpdate.newLatLngZoom(LatLng(markers.first.position.latitude, markers.first.position.longitude), 5));
    }
  }

  getOrderListApi() async {
    appStore.setLoading(true);
    await getAllOrder(perPage: -1, isTrashed: false, orderStatus: makeStringFromList(itemList: selectedStatusList)).then((value) {
      appStore.setLoading(false);
      orderData = value.data ?? [];
      markers.clear();
      orderData.map((e) {
        if (e.status == ORDER_ACCEPTED || e.status == ORDER_ASSIGNED || e.status == ORDER_ARRIVED) {
          markers.add(
            Marker(
              markerId: MarkerId(e.id.toString()),
              position: LatLng(double.parse(e.pickupPoint!.latitude.validate()), double.parse(e.pickupPoint!.longitude.validate())),
              infoWindow: InfoWindow(title: e.pickupPoint!.address, snippet: orderStatus(e.status.validate())),
              icon: sourceIcon,
            ),
          );
        } else if (e.status == ORDER_PICKED_UP || e.status == ORDER_DEPARTED) {
          markers.add(
            Marker(
              markerId: MarkerId(e.id.toString()),
              position: LatLng(double.parse(e.deliveryPoint!.latitude.validate()), double.parse(e.deliveryPoint!.longitude.validate())),
              infoWindow: InfoWindow(title: e.deliveryPoint!.address, snippet: orderStatus(e.status.validate())),
              icon: destinationIcon,
            ),
          );
        }
        setState(() {});
      }).toList();
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () {
        resetMenuIndex();
        Navigator.pop(context, true);
        return Future.value(true);
      },
      child: Scaffold(
        appBar: appBarWidget(language.ordersLocation),
        body: Stack(
          children: [
            GoogleMap(
                markers: markers.map((e) => e).toSet(),
                mapType: MapType.normal,
                initialCameraPosition: CameraPosition(
                  target: markers.isNotEmpty ? LatLng(markers.first.position.latitude, markers.first.position.latitude) : LatLng(23.022505, 72.5713621),
                  zoom: 5,
                ),
                onMapCreated: onMapCreated),
            Container(
              padding: EdgeInsets.all(8),
              color: Theme.of(context).scaffoldBackgroundColor,
              width: MediaQuery.of(context).size.width,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Wrap(
                    spacing: 12,
                    runSpacing: 4,
                    children: statusList.map((item) {
                      return Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          SizedBox(
                            height: 30,
                            width: 30,
                            child: Checkbox(
                              value: selectedStatusList.contains(item),
                              onChanged: (value) {
                                if (value.validate()) {
                                  selectedStatusList.add(item);
                                } else {
                                  selectedStatusList.remove(item);
                                }
                                setState(() {});
                                getOrderListApi();
                              },
                            ),
                          ),
                          Text(orderStatus(item), style: primaryTextStyle()),
                        ],
                      );
                    }).toList(),
                  ),
                  SizedBox(height: 12),
                  Wrap(
                    spacing: 16,
                    runSpacing: 16,
                    children: [
                      Row(mainAxisSize: MainAxisSize.min, children: [
                        Image.asset('assets/icons/ic_pickup_pin.png', height: 26, width: 26),
                        SizedBox(width: 8),
                        Text(language.pickup, style: primaryTextStyle()),
                      ]),
                      Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Image.asset('assets/icons/ic_drop_pin.png', height: 26, width: 26),
                          SizedBox(width: 8),
                          Text(language.destination, style: primaryTextStyle()),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
        /*  body: Stack(
          children: [
            SingleChildScrollView(
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Wrap(
                    children: statusList.map((item) {
                      return Padding(
                        padding: EdgeInsets.only(right: 16),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Checkbox(
                              value: selectedStatusList.contains(item),
                              onChanged: (value) {
                                if (value.validate()) {
                                  selectedStatusList.add(item);
                                } else {
                                  selectedStatusList.remove(item);
                                }
                                setState(() {});
                                getOrderListApi();
                              },
                            ),
                            Text(orderStatus(item)),
                          ],
                        ),
                      );
                    }).toList(),
                  ),
                  SizedBox(height: 16),
                  Container(
                    height: MediaQuery.of(context).size.height - 150,
                    padding: EdgeInsets.all(16),
                    decoration: BoxDecoration(color: appStore.isDarkMode ? scaffoldColorDark : Colors.white, borderRadius: BorderRadius.circular(defaultRadius), boxShadow: commonBoxShadow()),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Wrap(
                          spacing: 16,
                          runSpacing: 16,
                          children: [
                            Row(mainAxisSize: MainAxisSize.min, children: [
                              Image.asset('assets/icons/ic_pickup_pin.png', height: 26, width: 26),
                              SizedBox(width: 8),
                              Text('Pickup Location', style: primaryTextStyle()),
                            ]),
                            Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Image.asset('assets/icons/ic_drop_pin.png', height: 26, width: 26),
                                SizedBox(width: 8),
                                Text('Destination Location', style: primaryTextStyle()),
                              ],
                            ),
                          ],
                        ),
                        SizedBox(height: 16),
                        Expanded(
                          child: GoogleMap(
                              markers: markers.map((e) => e).toSet(),
                              mapType: MapType.normal,
                              initialCameraPosition: CameraPosition(
                                target: markers.isNotEmpty ? LatLng(markers.first.position.latitude, markers.first.position.latitude) : LatLng(23.022505, 72.5713621),
                                zoom: 5,
                              ),
                              onMapCreated: onMapCreated),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            Observer(builder: (context) => Visibility(visible:appStore.isLoading,child: loaderWidget())),
          ],
        ),*/
      ),
    );
  }
}
