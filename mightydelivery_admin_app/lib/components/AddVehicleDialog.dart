import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import '../extensions/colors.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/num_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../models/VehicleModel.dart';
import '../utils/Extensions/StringExtensions.dart';

import '../main.dart';
import '../models/CityListModel.dart';
import '../network/RestApis.dart';
import '../utils/Colors.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/app_textfield.dart';

class AddVehicleDialog extends StatefulWidget {
  static String tag = '/AppAddCityDialog';
  final VehicleData? vehicleData;
  final Function()? onUpdate;

  const AddVehicleDialog({Key? key, this.vehicleData, this.onUpdate}) : super(key: key);

  @override
  State<AddVehicleDialog> createState() => _AddVehicleDialogState();
}

class _AddVehicleDialogState extends State<AddVehicleDialog> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  TextEditingController titleController = TextEditingController();
  TextEditingController cityTitleController = TextEditingController();
  TextEditingController typeController = TextEditingController();
  TextEditingController sizeController = TextEditingController();
  TextEditingController capacityController = TextEditingController();
  TextEditingController descriptionController = TextEditingController();
  TextEditingController basePriceController = TextEditingController();
  TextEditingController minKmController = TextEditingController();
  TextEditingController perKmChargeController = TextEditingController();

  int? selectedCountryId;
  int? selectedCityId;

  String selectedCityName = "";
  List<String> cityId = [];
  List<CityData> cityList = [];

  bool isCheck = false;
  List<String> cityName = [];

  String vehicleType = 'city_wise';

  List<String> vehicleTypeList = ['city_wise', 'all'];

  bool isUpdate = false;

  XFile? imageVehicle;
  Uint8List? image;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    afterBuildCreated(() {
      appStore.setLoading(true);
    });
    await getCityListApiCall();
    isUpdate = widget.vehicleData != null;
    if (isUpdate) {
      if (widget.vehicleData!.type == 'city_wise') {
        cityList.forEach((element) {
          cityId = widget.vehicleData!.cityIds!.map((e) => e.toString()).toList();
          widget.vehicleData!.cityText!.forEach((key, value) {
            if (element.id == key.toInt()) {
              element.isCheck = true;
              cityTitleController.text = cityTitleController.text + value + ',';
              cityName.add(value);
            }
          });
        });
      }

      titleController.text = widget.vehicleData!.title!;
      sizeController.text = widget.vehicleData!.size!;
      descriptionController.text = widget.vehicleData!.description!;
      capacityController.text = widget.vehicleData!.capacity!;
      vehicleType = widget.vehicleData!.type.isEmptyOrNull ? 'city_wise' : widget.vehicleData!.type.toString();
      basePriceController.text = widget.vehicleData!.price.validate().toString();
      minKmController.text = widget.vehicleData!.minKm.validate().toString();
      perKmChargeController.text = widget.vehicleData!.perKmCharge.validate().toString();
      setState(() {});
    }
    appStore.setLoading(false);
  }

  getCityListApiCall() async {
    appStore.setLoading(true);
    await getCityList(countryId: selectedCountryId).then((value) {
      appStore.setLoading(false);
      cityList.clear();
      cityList.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      appStore.setLoading(false);
      toast('${error.toString()}');
    });
  }

  addVehicleApi() async {
    if (_formKey.currentState!.validate()) {
      if (vehicleType == 'city_wise' && cityId.isEmpty) {
        return toast(language.pleaseSelectCity);
      }
      String? _cityId = "[";
      for (String _data in cityId) {
        _cityId = "$_cityId \"$_data\",";
        if (vehicleType == 'city_wise' && cityId.isEmpty) {
          return toast(language.pleaseSelectCity);
        }
      }
      _cityId = "$_cityId]".replaceAll(",]", "]");
      log('$_cityId');
      if (isUpdate ? widget.vehicleData!.vehicleImage!.isNotEmpty : imageVehicle != null) {
        addVehicle(
          id: isUpdate ? widget.vehicleData!.id : null,
          title: titleController.text,
          capacity: capacityController.text,
          cityId: _cityId,
          description: descriptionController.text,
          vehicleImage: isUpdate ? widget.vehicleData!.vehicleImage : imageVehicle!.path.split('/').last,
          type: vehicleType,
          size: sizeController.text.trim(),
          image: image,
          status: isUpdate ? widget.vehicleData!.status : 1,
          price: num.parse(basePriceController.text),
          minKm: num.parse(minKmController.text),
          perKmCharge: num.parse(perKmChargeController.text),
        ).then((value) {
          toast(isUpdate ? language.vehicleUpdateSuccessfully : language.vehicleAddedSuccessfully);
          appStore.setLoading(false);
          widget.onUpdate!.call();
          Navigator.pop(context);
        }).catchError((error) {
          appStore.setLoading(false);
          log(error.toString());
          toast(error.toString());
        });
      } else {
        toast(language.pleaseSelectImage);
      }
    }
  }

  Widget vehicleImage() {
    if (image != null) {
      return ClipRRect(borderRadius: BorderRadius.circular(10), child: Image.memory(image!, height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center));
    } else {
      return ClipRRect(
        borderRadius: BorderRadius.circular(10),
        child: commonCachedNetworkImage(isUpdate ? widget.vehicleData!.vehicleImage.validate() : 'https://meetmighty.com/mobile/mighty-local-delivery/images/default.png', fit: BoxFit.cover, height: 100, width: 100),
      );
    }
  }

  Future<void> getImage() async {
    imageVehicle = null;
    imageVehicle = await ImagePicker().pickImage(source: ImageSource.gallery, imageQuality: 100);
    image = await imageVehicle!.readAsBytes();
    setState(() {});
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      //  contentPadding: EdgeInsets.all(16),
      //  titlePadding: EdgeInsets.only(left: 16, right: 8, top: 8),
      title: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(isUpdate ? language.update_vehicle : language.add_vehicle, style: boldTextStyle(color: appStore.isDarkMode ? white : primaryColor, size: 20)),
          Icon(Icons.close).onTap(() {
            Navigator.pop(context);
          })
        ],
      ),
      content: SingleChildScrollView(
        child: Container(
          width: context.width() * 100,
          height: context.height() / 1.5,
          child: Stack(
            children: [
              Form(
                key: _formKey,
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(language.vehicle_name, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      AppTextField(
                        controller: titleController,
                        textFieldType: TextFieldType.NAME,
                        decoration: commonInputDecoration(),
                        textInputAction: TextInputAction.next,
                        errorThisFieldRequired: language.fieldRequiredMsg,
                      ),
                      if (vehicleType == 'city_wise') SizedBox(width: 10),
                      if (vehicleType == 'city_wise')
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            8.height,
                            Text(language.selectCity, style: primaryTextStyle()),
                            SizedBox(height: 8),
                            AppTextField(
                              controller: cityTitleController,
                              textFieldType: TextFieldType.NAME,
                              decoration: commonInputDecoration(),
                              textInputAction: TextInputAction.next,
                              errorThisFieldRequired: language.field_required_msg,
                              readOnly: true,
                              isValidationRequired: false,
                              onTap: () {
                                showDialog(
                                  context: context,
                                  builder: (_) => StatefulBuilder(builder: (BuildContext context, StateSetter setState) {
                                    return AlertDialog(
                                      actions: [
                                        dialogSecondaryButton(language.cancel, () {
                                          Navigator.pop(context);
                                        }),
                                        SizedBox(width: 4),
                                        dialogPrimaryButton(language.add, () {
                                          cityTitleController.clear();
                                          String data = "";
                                          cityName.forEach((element1) {
                                            data = data + element1 + ",";
                                          });
                                          data = data.substring(0, data.length - 1);
                                          cityTitleController.text = data;

                                          Navigator.pop(context);
                                        }),
                                      ],
                                      content: Container(
                                        height: 300,
                                        width: 100,
                                        child: SingleChildScrollView(
                                          child: Column(
                                            mainAxisSize: MainAxisSize.min,
                                            children: cityList.map((e) {
                                              return CheckboxListTile(
                                                title: Text(e.name.validate(), style: primaryTextStyle()),
                                                value: e.isCheck,
                                                onChanged: (val) {
                                                  e.isCheck = !e.isCheck;
                                                  if (e.isCheck) {
                                                    cityName.add(e.name!);
                                                    cityId.add(e.id.toString());
                                                  } else {
                                                    cityName.remove(e.name!);
                                                    cityId.remove(e.id.toString());
                                                  }
                                                  setState(() {});
                                                },
                                              );
                                            }).toList(),
                                          ),
                                        ),
                                      ),
                                    );
                                  }),
                                );
                              },
                            ),
                          ],
                        ),
                      SizedBox(height: 8),
                      Text(language.type, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      DropdownButtonFormField<String>(
                        dropdownColor: Theme.of(context).cardColor,
                        value: vehicleType,
                        decoration: commonInputDecoration(),
                        items: vehicleTypeList.map<DropdownMenuItem<String>>((item) {
                          return DropdownMenuItem(value: item, child: Text(item, style: primaryTextStyle()));
                        }).toList(),
                        onChanged: (value) {
                          vehicleType = value.validate();
                          setState(() {});
                        },
                        validator: (value) {
                          if (vehicleType.isEmptyOrNull) return language.field_required_msg;
                          return null;
                        },
                      ),
                      8.height,
                      Text(language.vehicle_capacity, style: primaryTextStyle()),
                      8.height,
                      AppTextField(
                        controller: capacityController,
                        textFieldType: TextFieldType.NAME,
                        decoration: commonInputDecoration(),
                        textInputAction: TextInputAction.next,
                        errorThisFieldRequired: language.field_required_msg,
                      ),
                      8.height,
                      Text(language.vehicle_size, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      AppTextField(
                        controller: sizeController,
                        textFieldType: TextFieldType.NAME,
                        decoration: commonInputDecoration(),
                        textInputAction: TextInputAction.next,
                        errorThisFieldRequired: language.field_required_msg,
                      ),
                      8.height,
                      Text(language.description, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      AppTextField(
                        controller: descriptionController,
                        textFieldType: TextFieldType.NAME,
                        decoration: commonInputDecoration(),
                        textInputAction: TextInputAction.next,
                        errorThisFieldRequired: language.field_required_msg,
                      ),
                      8.height,
                      Text(language.basePrice, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      AppTextField(
                        controller: basePriceController,
                        textFieldType: TextFieldType.PHONE,
                        decoration: commonInputDecoration(),
                        textInputAction: TextInputAction.done,
                        errorThisFieldRequired: language.field_required_msg,
                      ),
                      8.height,
                      Text(language.minimumKm, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      AppTextField(
                        controller: minKmController,
                        textFieldType: TextFieldType.PHONE,
                        decoration: commonInputDecoration(),
                        textInputAction: TextInputAction.done,
                        errorThisFieldRequired: language.field_required_msg,
                      ),
                      8.height,
                      Text(language.perKmCharge, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      AppTextField(
                        controller: perKmChargeController,
                        textFieldType: TextFieldType.PHONE,
                        decoration: commonInputDecoration(),
                        textInputAction: TextInputAction.done,
                        errorThisFieldRequired: language.field_required_msg,
                      ),
                      SizedBox(height: 8),
                      Text(language.vehicle_image, style: primaryTextStyle()),
                      SizedBox(height: 8),
                      Row(
                        children: [
                          SizedBox(
                            width: 120,
                            height: 40,
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8.0), side: BorderSide(color: appStore.isDarkMode ? Colors.white12 : viewLineColor)), elevation: 0, backgroundColor: Colors.transparent, shadowColor: Colors.transparent),
                              child: Text(language.select_file, style: boldTextStyle(color: Colors.grey)),
                              onPressed: () {
                                getImage();
                              },
                            ),
                          ),
                          SizedBox(width: 8),
                          vehicleImage(),
                        ],
                      ),
                      SizedBox(height: 30),
                      Row(
                        children: [
                          Expanded(
                            child: dialogSecondaryButton(language.cancel, () {
                              Navigator.pop(context);
                            }),
                          ),
                          SizedBox(width: 16),
                          Expanded(
                            child: dialogPrimaryButton(isUpdate ? language.update : language.add, () {
                              if (sharedPref.getString(USER_TYPE) == DEMO_ADMIN) {
                                toast(language.demoAdminMsg);
                              } else {
                                addVehicleApi();
                                //isUpdate ? addVehicleApi(widget.vehicleData!.id, true) : addVehicleApi(0, false);
                              }
                            }),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              Visibility(visible: appStore.isLoading, child: Positioned.fill(child: loaderWidget())),
            ],
          ),
        ),
      ),
    );
  }
}
