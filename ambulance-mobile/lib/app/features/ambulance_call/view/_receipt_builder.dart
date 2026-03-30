part of 'ambulance_call_detail_screen.dart';

class _ReceiptBuilder extends _Builder {
  _ReceiptBuilder({
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
  String get headerText => AppPhrases.receiptForServices;

  @override
  Widget buildStatusInfographics(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primary,
          ),
          child: SvgPicture.asset(
            AppImages.question,
            colorFilter: ColorFilter.mode(
              AppColors.primaryButtonForeground,
              BlendMode.srcIn,
            ),
            width: Sizes.p16,
            height: Sizes.p16,
          ),
        ),
        Container(
          width: 6,
          height: 4,
          color: AppColors.primary,
        ),
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primary,
          ),
          child: SvgPicture.asset(
            AppImages.hourglass,
            colorFilter: ColorFilter.mode(
              AppColors.primaryButtonForeground,
              BlendMode.srcIn,
            ),
            width: Sizes.p16,
            height: Sizes.p16,
          ),
        ),
        Container(
          width: 6,
          height: 4,
          color: AppColors.primary,
        ),
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primary,
          ),
          child: SvgPicture.asset(
            AppImages.movingCar,
            colorFilter: ColorFilter.mode(
              AppColors.primaryButtonForeground,
              BlendMode.srcIn,
            ),
            width: Sizes.p16,
            height: Sizes.p16,
          ),
        ),
        Container(
          width: 6,
          height: 4,
          color: AppColors.primary,
        ),
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primary,
          ),
          child: SvgPicture.asset(
            AppImages.info,
            colorFilter: ColorFilter.mode(
              AppColors.primaryButtonForeground,
              BlendMode.srcIn,
            ),
            width: Sizes.p16,
            height: Sizes.p16,
          ),
        ),
        Container(
          width: 6,
          height: 4,
          color: AppColors.primary,
        ),
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primary,
          ),
          child: SvgPicture.asset(
            AppImages.syringe,
            colorFilter: ColorFilter.mode(
              AppColors.primaryButtonForeground,
              BlendMode.srcIn,
            ),
            width: Sizes.p16,
            height: Sizes.p16,
          ),
        ),
        Container(
          width: 6,
          height: 4,
          color: AppColors.primary,
        ),
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primary,
          ),
          child: SvgPicture.asset(
            AppImages.ruble,
            colorFilter: ColorFilter.mode(
              AppColors.primaryButtonForeground,
              BlendMode.srcIn,
            ),
            width: Sizes.p16,
            height: Sizes.p16,
          ),
        ),
      ],
    );
  }

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
        ),
        Gaps.h24,
        buildCallToClientCard(),
      ],
    );
  }

  @override
  Widget buildBottomBar(BuildContext context) => ScaffoldBottomBar(
        elevation: 3,
        borderRadius: Sizes.p16,
        padding: EdgeInsets.all(Sizes.p16),
        child: Row(
          children: [
            Flexible(
              child: Button(
                label: Text(AppPhrases.back),
                style: secondaryMediumButtonStyle,
                onPressed: screenState._onReceiptBackPressed,
              ),
            ),
            Gaps.w8,
            Flexible(
              child: Button(
                label: Text(AppPhrases.endTheCall),
                style: primaryMediumButtonStyle,
                isLoading: screenState._isStatusBeingUpdated,
                onPressed: screenState._onEndTheCallPressed,
              ),
            ),
          ],
        ),
      );
}
