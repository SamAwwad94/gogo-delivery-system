import '../network/RestApis.dart';

import '../main.dart';
import 'Extensions/app_common.dart';

getAllCountryApiCall() async{
  appStore.setLoading(true);
  await getCountryList().then((value) {
    appStore.setLoading(false);
    appStore.countryList.clear();
    appStore.countryList.addAll(value.data!);
  }).catchError((error) {
    appStore.setLoading(false);
    toast(error.toString());
  });
}
