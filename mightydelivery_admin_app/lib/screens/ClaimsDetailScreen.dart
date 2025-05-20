import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:intl/intl.dart';
import '../extensions/extension_util/context_extensions.dart';
import '../extensions/extension_util/int_extensions.dart';
import '../extensions/extension_util/string_extensions.dart';
import '../extensions/extension_util/widget_extensions.dart';
import '../extensions/widgets.dart';
import '../utils/Colors.dart';
import '../extensions/app_text_field.dart';
import '../extensions/common.dart';
import '../extensions/decorations.dart';
import '../main.dart';
import '../models/ClaimsListModel.dart';
import '../network/NetworkUtils.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/Constants.dart';
import '../utils/Extensions/app_common.dart';
import 'ClaimsListscreen.dart';
import 'DisplayAttachmentViewscreen.dart';

class ClaimDetailScreen extends StatefulWidget {
  ClaimItem item;
  ClaimDetailScreen(this.item);

  @override
  State<StatefulWidget> createState() {
    return _ClaimDetailScreenState();
  }
}

class _ClaimDetailScreenState extends State<ClaimDetailScreen> {
   TextEditingController claimAmountController = TextEditingController();
   TextEditingController claimDescriptionController = TextEditingController();
   GlobalKey<FormState> claimFormKey = GlobalKey<FormState>();
   List<PlatformFile>? selectedFiles;

  @override
  void initState() {
    super.initState();
  }

   Future<void> changeClaimStatusApi(int id, int status) async {
     Map req = {'id': id, 'status': status};
     appStore.setLoading(true);
     await changeClaimStatus(req).then((value) {
       appStore.setLoading(false);
       toast(value.message);
       Navigator.pop(context);
     });
   }
   Future<void> selectProofData(BuildContext context, int claimId) async {

     showDialog(
         context: context,
         barrierDismissible: true,
         builder: (BuildContext dialogContext)
         {
           return AlertDialog(
             content: StatefulBuilder(builder: (context, selectedImagesUpdate) {
               return Form(
                 key: claimFormKey,
                 child: SingleChildScrollView(
                   child: Container(
                     constraints: BoxConstraints(
                       minHeight: 200.0, // Set your minimum height here
                     ),
                     child: Stack(
                       children: [
                         !appStore.isLoading
                             ? Column(
                           mainAxisSize: MainAxisSize.min,
                           mainAxisAlignment: MainAxisAlignment.start,
                           crossAxisAlignment: CrossAxisAlignment.start,
                           children: [
                             Text(language.claim ,style: boldTextStyle(), textAlign: TextAlign.start),
                             8.height,
                             Divider(height: 1),
                             8.height,
                             Text(language.amount, style: boldTextStyle()),
                             12.height,
                             AppTextField(
                               isValidationRequired: true,
                               controller: claimAmountController,
                               textFieldType: TextFieldType.NAME,
                               errorThisFieldRequired: language.fieldRequiredMsg,
                               decoration: commonInputDecoration(hintText: language.amount),
                             ),
                             8.height,
                             Text(language.description, style: boldTextStyle()),
                             12.height,
                             AppTextField(
                               isValidationRequired: true,
                               controller: claimDescriptionController,
                               textFieldType: TextFieldType.NAME,
                               errorThisFieldRequired: language.fieldRequiredMsg,
                               minLines: 4,
                               maxLines: 8,
                               decoration: commonInputDecoration(hintText: language.description),
                             ),
                             8.height,
                             if (selectedFiles != null && selectedFiles!.length > 0)
                               Text(language.selectedFiles, style: boldTextStyle()),
                             if (selectedFiles != null && selectedFiles!.length > 0) 10.height,
                             if (selectedFiles != null && selectedFiles!.length > 0)
                               Container(
                                 width: context.width(),
                                 height: 120,
                                 child: ListView.builder(
                                   scrollDirection: Axis.horizontal,
                                   itemCount: selectedFiles!.length,
                                   itemBuilder: (context, index) {
                                     return buildFileWidget(selectedFiles![index].path.validate(), widget.item.id.validate());
                                   },
                                 ),
                               ),
                             16.height,
                             Row(
                               children: [
                                 dialogPrimaryButton(language.attachFile, () async {
                                   if (selectedFiles != null) selectedFiles!.clear();
                                   FilePickerResult? result = await FilePicker.platform.pickFiles(
                                     allowMultiple: true,
                                     type: FileType.custom,
                                     allowedExtensions: ['jpg', 'jpeg', 'png', 'pdf'],
                                   );
                                   if (result != null) {
                                     selectedImagesUpdate(() {
                                       selectedFiles = result.files;
                                     });
                                   }
                                 }).expand(),
                                 6.width,
                                 dialogPrimaryButton(language.save, () async {
                                   if (claimFormKey.currentState!.validate()) {
                                     selectedImagesUpdate(() {
                                       appStore.setLoading(true);
                                     });
                                     hideKeyboard(context);

                                     MultipartRequest multiPartRequest = await getMultiPartRequest('status-details');
                                     multiPartRequest.fields['amount'] = claimAmountController.text;
                                     multiPartRequest.fields['description'] = claimDescriptionController.text;
                                     multiPartRequest.fields['claim_id'] = claimId.toString();
                                     if (selectedFiles != null && selectedFiles!.length > 0) {
                                       selectedFiles!.forEach((element) async {
                                         multiPartRequest.files
                                             .add(await MultipartFile.fromPath("attachment_resolve_file[]", element.path!));
                                       });
                                     }
                                     print("---------------request${multiPartRequest.toString()}");

                                     multiPartRequest.headers.addAll(buildHeaderTokens());
                                     sendMultiPartRequest(
                                       multiPartRequest,
                                       onSuccess: (data) async {
                                         if (data != null) {
                                           appStore.setLoading(false);
                                           toast(data["message"]);
                                           if (dialogContext.mounted) {
                                             Navigator.pop(dialogContext);
                                           }
                                           if (context.mounted) {
                                             Navigator.pop(context);
                                           }
                                           print(data.toString());
                                         }
                                       },
                                       onError: (error) {
                                         toast(error.toString(), print: true);
                                         appStore.setLoading(false);
                                       },
                                     ).catchError((e) {
                                       appStore.setLoading(false);
                                       toast(e.toString());
                                     });
                                   }
                                   // finish(context, 0);
                                 }),
                               ],
                             ),
                           ],
                         )
                             : Observer(builder: (context) => loaderWidget().visible(appStore.isLoading)).center(),
                       ],
                     ),
                   ),
                 ),
               );}
             ),
           );
         });
   }

  Widget buildFileWidget(String url, int id) {
    bool isImage = url.contains('jpg') || url.contains('jpeg') || url.contains('png');

    return Stack(
      children: [
        Container(
          width: 180,
          height: 180,
          decoration: boxDecorationWithRoundedCorners(border: Border.all(color: primaryColor)),
          child: isImage
              ? commonCachedNetworkImage(url, width: 180, height: 180).cornerRadiusWithClipRRect(10)

              : Center(
            child: Icon(
              Icons.picture_as_pdf,
              size: 40,
              color: Colors.red,
            ),
          ),
        ).paddingOnly(left: 8, right: 8).onTap(() {
          DisplayAttachmentViewScreen(
            isPhoto: isImage,
            value: url,
            id: id,
          ).launch(context);
        })
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Observer(builder: (context){
      return Scaffold(
        appBar: appBarWidget(widget.item.trakingNo.validate()),
        body: Stack(
          children: [
            SingleChildScrollView(
              padding: EdgeInsets.only(left: 16, right: 16, top: 16, bottom: 100),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: EdgeInsets.only(
                      left: 16,
                      right: 16,
                      top: 10,
                      bottom: 10,
                    ),
                    decoration: boxDecorationWithRoundedCorners(
                        borderRadius: BorderRadius.circular(defaultRadius),
                        border: Border.all(color: primaryColor.withOpacity(0.3)),
                        backgroundColor: Colors.transparent),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('${DateFormat('dd MMM yyyy').format(DateTime.parse("${widget.item.createdAt!}").toLocal())} ',
                            style: primaryTextStyle(size: 14))
                            .expand(),
                        Container(
                          alignment: Alignment.center,
                          padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          margin: EdgeInsets.only(right: 4, left: appStore.selectedLanguage == "ar" ? 4 : 0),
                          child: getClaimStatus(
                            widget.item.status!.validate(),
                          ),
                          decoration: BoxDecoration(
                              border: Border.all(
                                color: getClaimStatusColor(widget.item.status!.validate(), 0.6),
                              ),
                              color: getClaimStatusColor(widget.item.status!.validate(), 0.15),
                              borderRadius: BorderRadius.circular(defaultRadius)),
                        ),
                        claimStatusButton(widget.item, () {
                          changeClaimStatusApi(widget.item.id.validate(),1);
                        }, () {
                          changeClaimStatusApi(widget.item.id.validate(),0);
                        }, () {
                          selectProofData(context, widget.item.id.validate());
                        }),
                      ],
                    ),
                  ),
                  16.height,
                  Text(
                    language.proofValue,
                    style: boldTextStyle(),
                  ),
                  8.height,
                  Container(
                      decoration: boxDecorationWithRoundedCorners(
                          borderRadius: BorderRadius.circular(defaultRadius),
                          border: Border.all(color: primaryColor.withOpacity(0.3)),
                          backgroundColor: Colors.transparent),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            width: context.width(),
                            decoration: BoxDecoration(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.all(12),
                            child: Text(widget.item.profValue.validate(), style: primaryTextStyle()),
                          ),
                        ],
                      )),
                  16.height,
                  Text(
                    "proofDetails",
                    style: boldTextStyle(),
                  ),
                  8.height,
                  Container(
                      decoration: boxDecorationWithRoundedCorners(
                          borderRadius: BorderRadius.circular(defaultRadius),
                          border: Border.all(color: primaryColor.withOpacity(0.3)),
                          backgroundColor: Colors.transparent),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            width: context.width(),
                            decoration: BoxDecoration(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.all(12),
                            child: Text(
                              widget.item.profValue!,
                              style: primaryTextStyle(),
                              overflow: TextOverflow.ellipsis,
                              maxLines: 2,
                            ),
                          ),
                        ],
                      )),
                  16.height,
                  if (widget.item.attachmentFile != null && widget.item.attachmentFile!.length > 0)
                    Text("Atachments", style: boldTextStyle()),
                  if (widget.item.attachmentFile != null && widget.item.attachmentFile!.length > 0) 8.height,
                  if (widget.item.attachmentFile != null && widget.item.attachmentFile!.length > 0)
                    Container(
                      width: context.width(),
                      height: 180,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: widget.item.attachmentFile!.length,
                        itemBuilder: (context, index) {
                          return buildFileWidget(widget.item.attachmentFile![index], widget.item.id.validate());
                        },
                      ),
                    ),
                  if(widget.item.claimsHistory != null && widget.item.claimsHistory!.isNotEmpty) ...[
                    16.height,
                    Text(
                      language.approvedAmount,
                      style: boldTextStyle(),
                    ),
                    8.height,
                    Container(
                        decoration: boxDecorationWithRoundedCorners(
                            borderRadius: BorderRadius.circular(defaultRadius),
                            border: Border.all(color: primaryColor.withOpacity(0.3)),
                            backgroundColor: Colors.transparent),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              width: context.width(),
                              decoration: BoxDecoration(borderRadius: BorderRadius.circular(8)),
                              padding: EdgeInsets.all(12),
                              child: Text(widget.item.claimsHistory![0].amount.validate().toString(), style: primaryTextStyle()),
                            ),
                          ],
                        )),
                    16.height,
                    Text(
                      language.description,
                      style: boldTextStyle(),
                    ),
                    8.height,
                    Container(
                        decoration: boxDecorationWithRoundedCorners(
                            borderRadius: BorderRadius.circular(defaultRadius),
                            border: Border.all(color: primaryColor.withOpacity(0.3)),
                            backgroundColor: Colors.transparent),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              width: context.width(),
                              decoration: BoxDecoration(borderRadius: BorderRadius.circular(8)),
                              padding: EdgeInsets.all(12),
                              child: Text(widget.item.claimsHistory![0].description.validate().toString(), style: primaryTextStyle()),
                            ),
                          ],
                        )),

                    if(widget.item.claimsHistory![0].attachmentFile != null && widget.item.claimsHistory![0].attachmentFile!.length >0)...[
                      16.height,
                      Text(
                        language.attachment,
                        style: boldTextStyle(),
                      ),
                      8.height,
                      Container(
                        width: context.width(),
                        height: 180,
                        child: ListView.builder(
                          scrollDirection: Axis.horizontal,
                          itemCount: widget.item.claimsHistory![0].attachmentFile!.length,
                          itemBuilder: (context, index) {
                            return buildFileWidget(widget.item.claimsHistory![0].attachmentFile![index], widget.item.id.validate());
                          },
                        ),
                      ),
                    ],
                  ],
                ],
              ),
            ),
            loaderWidget().center().visible(appStore.isLoading),
          ],
        ),
      );}
    );
  }
}
