import 'dart:io';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../models/UserModel.dart';
import '../utils/Colors.dart';
import 'package:paginate_firestore/paginate_firestore.dart';

import '../../main.dart';
import '../extensions/colors.dart';
import '../extensions/decorations.dart';
import '../models/ChatMessageModel.dart';
import '../models/FileModel.dart';
import '../services/ChatMessgaeService.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import '../utils/Extensions/shared_pref.dart';
import 'ChatItemWidget.dart';

class ChatScreen extends StatefulWidget {
  final UserModel userData;

  ChatScreen({required this.userData});

  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  String id = '';
  var messageCont = TextEditingController();
  var messageFocus = FocusNode();
  bool isMe = false;

  @override
  void initState() {
    super.initState();
    init();
  }

  UserModel sender = UserModel(
    name: getStringAsync(USER_NAME),
    profileImage: appStore.userProfile,
    uid: getStringAsync(UID),
    playerId: getStringAsync(PLAYER_ID),
  );

  init() async {
    log("userList");
    log(widget.userData.toJson());
    id = getStringAsync(UID);
    mIsEnterKey = getBoolAsync(IS_ENTER_KEY, defaultValue: false);
    print(mIsEnterKey);
    mSelectedImage = getStringAsync(SELECTED_WALLPAPER, defaultValue: "assets/default_wallpaper.png");
    chatMessageService = ChatMessageService();
    chatMessageService.setUnReadStatusToTrue(senderId: sender.uid!, receiverId: widget.userData.uid!);
    setState(() {});
  }

  sendMessage({FilePickerResult? result}) async {
    if (result == null) {
      if (messageCont.text.trim().isEmpty) {
        messageFocus.requestFocus();
        return;
      }
    }
    ChatMessageModel data = ChatMessageModel();
    data.receiverId = widget.userData.uid;
    data.senderId = getStringAsync(UID);
    data.message = messageCont.text;
    data.isMessageRead = false;
    data.createdAt = DateTime.now().millisecondsSinceEpoch;

    if (widget.userData.uid == getStringAsync(UID)) {
      //
    }
    if (result != null) {
      if (result.files.single.path.isImage) {
        data.messageType = MessageType.IMAGE.name;
      } else {
        data.messageType = MessageType.TEXT.name;
      }
    } else {
      data.messageType = MessageType.TEXT.name;
    }

    notificationService.sendPushNotifications(getStringAsync(USER_NAME), messageCont.text, receiverPlayerId: widget.userData.playerId).catchError(log);
    messageCont.clear();
    setState(() {});
    return await chatMessageService.addMessage(data).then((value) async {
      if (result != null) {
        FileModel fileModel = FileModel();
        fileModel.id = value.id;
        fileModel.file = File(result.files.single.path!);
        fileList.add(fileModel);

        setState(() {});
      }

      await chatMessageService.addMessageToDb(value, data, sender, widget.userData, image: result != null ? File(result.files.single.path!) : null).then((value) {});

      userService.fireStore
          .collection(USER_COLLECTION)
          .doc(getIntAsync(USER_ID).toString())
          .collection(CONTACT_COLLECTION)
          .doc(widget.userData.uid!)
          .update({'lastMessageTime': DateTime.now().millisecondsSinceEpoch}).catchError((e) {
        log(e);
      });
      userService.fireStore
          .collection(USER_COLLECTION)
          .doc(widget.userData.uid!)
          .collection(CONTACT_COLLECTION)
          .doc(getIntAsync(USER_ID).toString())
          .update({'lastMessageTime': DateTime.now().millisecondsSinceEpoch}).catchError((e) {
        log(e);
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    log(widget.userData.uid);
    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Row(
          children: [
            Icon(Icons.arrow_back, color: whiteColor).paddingSymmetric(vertical: 16).onTap(() => finish(context)),
            10.width,
            CircleAvatar(backgroundColor: context.cardColor, backgroundImage: NetworkImage(widget.userData.profileImage.validate()), minRadius: 20),
            10.width,
            Text(widget.userData.name.validate(), style: TextStyle(color: whiteColor)).paddingSymmetric(vertical: 16).expand(),
          ],
        ),
        backgroundColor: context.primaryColor,
      ),
      body: Container(
        height: context.height(),
        width: context.width(),
        child: Stack(
          children: [
            Container(
              height: context.height(),
              width: context.width(),
              child: PaginateFirestore(
                reverse: true,
                isLive: true,
                padding: EdgeInsets.only(left: 8, top: 8, right: 8, bottom: 0),
                physics: BouncingScrollPhysics(),
                query: chatMessageService.chatMessagesWithPagination(currentUserId: getStringAsync(UID), receiverUserId: widget.userData.uid!),
                itemsPerPage: PER_PAGE_CHAT_COUNT,
                shrinkWrap: true,
                onEmpty: Offstage(),
                itemBuilderType: PaginateBuilderType.listView,
                itemBuilder: (context, snap, index) {
                  ChatMessageModel data = ChatMessageModel.fromJson(snap[index].data() as Map<String, dynamic>);
                  data.isMe = data.senderId == sender.uid;
                  return ChatItemWidget(data: data);
                },
              ).paddingBottom(76),
            ),
            Positioned(
              bottom: 16,
              left: 16,
              right: 16,
              child: Container(
                decoration: boxDecorationWithShadow(
                  borderRadius: BorderRadius.circular(30),
                  spreadRadius: 1,
                  blurRadius: 1,
                  backgroundColor: context.cardColor,
                ),
                padding: EdgeInsets.only(left: 8, right: 8),
                child: Row(
                  children: [
                    TextField(
                      controller: messageCont,
                      decoration: InputDecoration(
                        border: InputBorder.none,
                        hintText: language.writeAMessage,
                        hintStyle: secondaryTextStyle(),
                        contentPadding: EdgeInsets.symmetric(vertical: 18, horizontal: 4),
                      ),
                      cursorColor: appStore.isDarkMode ? Colors.white : Colors.black,
                      focusNode: messageFocus,
                      textCapitalization: TextCapitalization.sentences,
                      keyboardType: TextInputType.multiline,
                      minLines: 1,
                      style: primaryTextStyle(),
                      textInputAction: mIsEnterKey ? TextInputAction.send : TextInputAction.newline,
                      onSubmitted: (s) {
                        sendMessage();
                      },
                      cursorHeight: 20,
                      maxLines: 5,
                    ).expand(),
                    IconButton(
                      icon: Icon(Icons.send, color: primaryColor),
                      onPressed: () {
                        sendMessage();
                      },
                    )
                  ],
                ),
                width: context.width(),
              ),
            )
          ],
        ),
      ),
    );
  }
}
