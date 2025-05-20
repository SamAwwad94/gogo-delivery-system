import 'package:cloud_firestore/cloud_firestore.dart';
import '../models/UserModel.dart';
import '../utils/Constants.dart';
import 'BaseServices.dart';

class UserService extends BaseService {
  FirebaseFirestore fireStore = FirebaseFirestore.instance;

  UserService() {
    ref = fireStore.collection(USER_COLLECTION);
  }
  Stream<UserModel> singleUser(String? id, {String? searchText}) {
    return ref!.where('uid', isEqualTo: id).limit(1).snapshots().map((event) {
      return UserModel.fromJson(event.docs.first.data() as Map<String, dynamic>);
    });
  }
}

