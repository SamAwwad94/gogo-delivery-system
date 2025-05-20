import 'package:cloud_firestore/cloud_firestore.dart';
import '../main.dart';
import '../utils/Constants.dart';
import '../models/UserModel.dart';
import '../utils/Extensions/app_common.dart';

abstract class BaseService {
  CollectionReference? ref;

  BaseService({this.ref});

  Future<DocumentReference> addDocument(Map data) async {
    var doc = await ref!.add(data);
    doc.update({'uid': doc.id});
    print("----->id");
    print("Add id ${doc.id}");
    sharedPref.setString(FIREBASE_UID, doc.id);
    var map = Map<String, dynamic>();
    map = {'uid': doc.id};
    addDocumentWithCustomId(doc.id, map);
    return doc;
  }

  Future<DocumentReference> addDocumentWithCustomId(String id, Map<String, dynamic> data) async {
    var doc = ref!.doc(id);
    return await doc.set(data).then((value) {
      log('Added: $data');
      return doc;
    }).catchError((e) {
      log(e);
      throw e;
    });
  }

  updateDocument(Map<String, dynamic> data, String? id) {
    ref!.doc(id).update(data);
    sharedPref.setString(FIREBASE_UID, id.toString());
  }

  Future<void> removeDocument(String id) => ref!.doc(id).delete();

  Future<UserModel> isUserExist(String? email) async {
    Query query = ref!.limit(1).where('email', isEqualTo: email);
    var res = await query.get();
    if (res.docs.isNotEmpty) {
      print(res.docs);
      print("--->");
      sharedPref.setString(UID, res.docs.first.id);

      return UserModel.fromJson(res.docs.first.data() as Map<String, dynamic>);
    } else {
      throw "User Not Found";
    }
  }

  Future<List<UserModel>> getList() async {
    try {
      QuerySnapshot querySnapshot = await ref!.get();
      List<UserModel> userList = querySnapshot.docs.map((e) {
        Map<String, dynamic> documentData = e.data() as Map<String, dynamic>;

        return UserModel.fromJson(documentData);
      }).toList();
      return userList;
    } catch (e) {
      print('Error retrieving collection data: $e');
      return [];
    }
  }
}
