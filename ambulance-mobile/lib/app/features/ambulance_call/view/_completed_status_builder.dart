part of 'ambulance_call_detail_screen.dart';

class _CompletedStatusBuilder extends _Builder {
  _CompletedStatusBuilder({
    required super.screenState,
    required this.receiptExtraFormKey,
  }) {
    fullNameController.text = ambCallDetails.fio ?? '';
    final dateOfBirth = ageToDateOfBirth(
      age: ambCallDetails.age,
      fallbackAge: averagePatientAge,
    );
    dateOfBirthController.text = dateOnlyFormat.format(dateOfBirth);
    patientCommentController.text = ambCallDetails.note ?? '';
  }

  final GlobalKey<ReceiptExtraFormState> receiptExtraFormKey;
  final fullNameController = TextEditingController();
  final dateOfBirthController = TextEditingController();
  final addressController = TextEditingController();
  final patientCommentController = TextEditingController();
  final orderCommentController = TextEditingController();

  @override
  String get headerText => AppPhrases.ambulanceCallCompleted;

  @override
  bool get hasStatusInfographics => false;

  @override
  Widget buildForm(BuildContext context) {
    var addressFieldValue = ambCallDetails.address;

    if (ambCallDetails.addressInfo != null) {
      addressFieldValue += ' ${ambCallDetails.addressInfo}';
    }

    return Form(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          TextFormField(
            readOnly: true,
            controller: fullNameController,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.patientFullName}:',
            ),
          ),
          Gaps.h16,
          TextFormField(
            readOnly: true,
            controller: dateOfBirthController,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.dateOfBirth}:',
              suffixIconConstraints: BoxConstraints(
                maxHeight: Sizes.p32,
              ),
            ),
          ),
          Gaps.h16,
          TextFormField(
            readOnly: true,
            initialValue: addressFieldValue,
            maxLines: null,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.address}:',
            ),
          ),
          Gaps.h16,
          TextFormField(
            readOnly: true,
            controller: patientCommentController,
            maxLines: null,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.comment}:',
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget buildActionMenu(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Card(
          color: AppColors.card,
          elevation: 0,
          margin: EdgeInsets.zero,
          child: CartWidget(
            readOnly: true,
            initialCart: {
              for (var x in screenState._ambCallDetail!.services) x.service: x
            },
          ),
        ),
        Gaps.h24,
        ReceiptExtraForm(
          key: receiptExtraFormKey,
          initialComment: screenState._receiptComment,
          initialAdvertesed: screenState._advertised,
          readOnly: true,
        ),
        if (screenState.isShiftOpen) ...[
          Gaps.h24,
          buildCallToClientCard(),
        ]
      ],
    );
  }
}
