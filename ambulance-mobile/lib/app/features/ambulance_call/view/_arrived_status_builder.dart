part of 'ambulance_call_detail_screen.dart';

class _ArrivedStatusBuilder extends _Builder {
  final GlobalKey<PatientFormState> patientFormKey;

  _ArrivedStatusBuilder({
    required super.screenState,
    required this.patientFormKey,
  });

  @override
  String get headerText => AppPhrases.checkInClient;

  @override
  List<String> get importantNotes => [
        if (ambCallDetails.currentNoBusinessCards)
          AppPhrases.doNotLeaveBusinessCards,
        if (ambCallDetails.currentPartnerHospitalization)
          AppPhrases.hospitalizationWithPartner,
        if (ambCallDetails.doNotHospitalize) AppPhrases.doNotHospitalize,
        if (ambCallDetails.personal) AppPhrases.personal,
      ];

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
          color: AppColors.primaryButtonForeground,
        ),
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primaryButtonForeground,
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
          color: AppColors.primaryButtonForeground,
        ),
        Container(
          width: Sizes.p32 + Sizes.p4,
          height: Sizes.p32 + Sizes.p4,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p24),
            color: AppColors.primaryButtonForeground,
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
  Widget buildForm(BuildContext context) => PatientForm(
        key: patientFormKey,
        initialFullName: ambCallDetails.fio,
        initialDateOfBirth: ambCallDetails.age != null
            ? ageToDateOfBirth(
                age: ambCallDetails.age,
                fallbackAge: averagePatientAge,
              )
            : null,
        initialNote: ambCallDetails.note,
        initialAddress: ambCallDetails.address,
        initialAddressInfo: ambCallDetails.addressInfo,
        initialDescription: ambCallDetails.description,
      );

  @override
  Widget buildActionMenu(BuildContext context) {
    return Column(
      children: [
        Material(
          type: MaterialType.card,
          elevation: 1,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(Sizes.p16),
          ),
          child: Button.icon(
            label: Text(AppPhrases.callToTheClient),
            icon: const Icon(Icons.phone),
            style: secondaryMediumButtonStyle.copyWith(
              foregroundColor: const WidgetStatePropertyAll(
                AppColors.success,
              ),
              backgroundColor: const WidgetStatePropertyAll(
                AppColors.buttonBackground,
              ),
            ),
            isLoading: screenState._isTouchingToCall,
            onPressed: screenState._onTouchToCallPressed,
          ),
        ),
        Gaps.h16,
        Button.icon(
          onPressed: () => screenState._onClientHistoryPressed(),
          icon: Icon(Icons.chevron_right),
          iconAlignment: IconAlignment.end,
          label: Row(
            children: [
              Text(
                AppPhrases.clientHistory,
                style: TextStyle(fontWeight: FontWeight.w500),
              ),
            ],
          ),
          style: ElevatedButton.styleFrom(
            padding: EdgeInsets.only(
              left: Sizes.p16,
              right: Sizes.p12,
            ),
          ),
        ),
      ],
    );
  }

  @override
  Widget buildBottomBar(BuildContext context) => ScaffoldBottomBar(
        elevation: 0,
        borderRadius: Sizes.p16,
        padding: EdgeInsets.all(Sizes.p16),
        child: Row(
          children: [
            Flexible(
              child: Button(
                label: Text(AppPhrases.cancelCall),
                style: secondaryMediumButtonStyle,
                enabled: !screenState._isStatusBeingUpdated,
                onPressed: screenState._onRejectCallPressed,
              ),
            ),
            Gaps.w8,
            Flexible(
              child: Button(
                label: Text(AppPhrases.startTreatment),
                style: primaryMediumButtonStyle,
                isLoading: screenState._isStatusBeingUpdated,
                onPressed: screenState._onStartTreatmentPressed,
              ),
            ),
          ],
        ),
      );
}
