import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:firebase_auth/firebase_auth.dart';
import '../../main.dart';
import '../models/LoginResponse.dart';
import '../models/UserModel.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/StringExtensions.dart';

final FirebaseAuth _auth = FirebaseAuth.instance;

class AuthServices {
  Future<void> updateUserData(UserModel user) async {
    userService.updateDocument({
      'player_id': sharedPref.getString(PLAYER_ID),
      'updatedAt': Timestamp.now(),
    }, user.uid);
  }

  Future<User?> createAuthUser(String? email, String? password) async {
    User? userCredential;
    try {
      await _auth.createUserWithEmailAndPassword(email: email!, password: password!).then((value) {
        userCredential = value.user!;
      });
    } on FirebaseException catch (error) {
      if (error.code == "ERROR_EMAIL_ALREADY_IN_USE" || error.code == "account-exists-with-different-credential" || error.code == "email-already-in-use") {
        await _auth.signInWithEmailAndPassword(email: email!, password: password!).then((value) {
          userCredential = value.user!;
        });
      } else {
        toast(error.message);
      }
    }
    return userCredential;
  }

  Future<void> signUpWithEmailPassword(context,
      {String? name, String? email, String? password, LoginResponse? userData, String? mobileNumber, String? lName, String? userName, bool? isOTP, String? userType, bool isAddUser = false}) async {
    try {
      createAuthUser(email, password).then((user) async {
        if (user != null) {
          UserModel userModel = UserModel();

          /// Create user
          userModel.uid = user.uid;
          userModel.email = user.email;
          userModel.contactNumber = userData!.data!.contactNumber;
          userModel.name = userData.data!.name;
          userModel.username = userData.data!.username;
          userModel.userType = userData.data!.userType;
          userModel.longitude = userData.data!.longitude;
          userModel.latitude = userData.data!.longitude;
          userModel.countryName = userData.data!.countryName;
          userModel.cityName = userData.data!.cityName;
          userModel.status = userData.data!.status;
          userModel.playerId = userData.data!.playerId;
          userModel.profileImage = userData.data!.profileImage;
          userModel.createdAt = Timestamp.now().toDate().toString();
          userModel.updatedAt = Timestamp.now().toDate().toString();
          //  userModel.playerId = getStringAsync(USER_PLAYER_ID);
          await userService.addDocumentWithCustomId(user.uid, userModel.toJson()).then((value) async {
            sharedPref.setString(UID, user.uid);
            //updateProfile(uid: user.uid);
            // launchScreen(context, DashboardScreen());
          }).catchError((e) {
            appStore.setLoading(false);
            toast(e.toString());
          });
        } else {
          appStore.setLoading(false);
          throw language.somethingWentWrong;
        }
      });
    } on FirebaseException catch (error) {
      appStore.setLoading(false);
      toast(error.message);
    }
  }

  Future<void> signInWithEmailPassword(context, {required String email, required String password}) async {
    await _auth.signInWithEmailAndPassword(email: email, password: password).then((value) async {
      appStore.setLoading(true);
      final User user = value.user!;
      UserModel userModel = await userService.isUserExist(user.email);
      await updateUserData(userModel);
     // updateProfile(uid: userModel.uid);

      appStore.setLoading(true);
      //Login Details to SharedPreferences
      sharedPref.setString(UID, userModel.uid.validate());
      sharedPref.setString(USER_EMAIL, userModel.email.validate());
      sharedPref.setBool(IS_LOGGED_IN, true);
    }).catchError((e) {
      log(e.toString());
    });
  }
}
