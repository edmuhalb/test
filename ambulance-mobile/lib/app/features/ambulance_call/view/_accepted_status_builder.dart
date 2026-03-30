part of 'ambulance_call_detail_screen.dart';

class _AcceptedStatusBuilder extends _Builder {
  _AcceptedStatusBuilder({
    required super.screenState,
  });

  @override
  String get headerText => AppPhrases.waitingForDeparture;

  @override
  String get infoText => AppPhrases.assignedStatusAmbulanceCallInfo;

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
                label: Text(AppPhrases.departed),
                isLoading: screenState._isStatusBeingUpdated,
                style: primaryMediumButtonStyle,
                onPressed: screenState._onDepartedPressed,
              ),
            ),
          ],
        ),
      );
}
