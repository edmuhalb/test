part of 'ambulance_call_detail_screen.dart';

class _TreatingStatusBuilder extends _Builder {
  final _serviceRepository = GetIt.I<AbstractServicesRepository>();
  final GlobalKey<CartWidgetState>? cartKey;

  _TreatingStatusBuilder({
    required super.screenState,
    this.cartKey,
  }) {
    fullNameController.text = ambCallDetails.fio ?? '';
    final dateOfBirth = ageToDateOfBirth(
      age: ambCallDetails.age,
      fallbackAge: averagePatientAge,
    );
    dateOfBirthController.text = dateOnlyFormat.format(dateOfBirth);
    patientCommentController.text = ambCallDetails.note ?? '';
  }

  final fullNameController = TextEditingController();
  final dateOfBirthController = TextEditingController();
  final addressController = TextEditingController();
  final patientCommentController = TextEditingController();

  final orderCommentController = TextEditingController();

  @override
  String? get headerText => AppPhrases.orderServices;

  @override
  String get infoText => AppPhrases.treatingStatusAmbulanceCallInfo;

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
          height: Sizes.p4,
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
          height: Sizes.p4,
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
          height: Sizes.p4,
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
          height: Sizes.p4,
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
          height: Sizes.p4,
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
          child: FutureBuilder(
            // TODO: use api v1 to fetch services
            future: _serviceRepository.getServiceList(),
            builder: (context, snapshot) {
              // TODO: try again if failed to fetch data
              if (snapshot.connectionState == ConnectionState.waiting) {
                return AnimatedSize(
                  duration: themeAnimationDuration,
                  child: AnimatedSwitcher(
                    duration: themeAnimationDuration,
                    child: Row(
                      key: ValueKey('progress'),
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        SizedBox(
                          height: Sizes.p40,
                          width: Sizes.p40,
                          child: Center(
                            child: SizedBox(
                              height: Sizes.p24,
                              width: Sizes.p24,
                              child: CircularProgressIndicator(
                                strokeWidth: 3,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              }

              if (snapshot.connectionState == ConnectionState.done &&
                  snapshot.hasData &&
                  snapshot.data != null) {
                return AnimatedSize(
                  duration: themeAnimationDuration,
                  child: AnimatedSwitcher(
                    duration: themeAnimationDuration,
                    child: CartWidget(
                      key: cartKey,
                      initialCart: {
                        ...{for (var x in snapshot.data!) x: null},
                        ...{
                          for (var x in screenState._ambCallDetail!.services) x.service: x
                        }
                      },
                      didPutCartItem: screenState._onServicePutIntoCart,
                      didRemoveCartItem: screenState._onServiceRemovedFromCart,
                    ),
                  ),
                );
              }

              return AnimatedSize(
                duration: themeAnimationDuration,
                child: AnimatedSwitcher(
                  duration: themeAnimationDuration,
                  child: Container(
                    key: ValueKey('hidden'),
                  ),
                ),
              );
            },
          ),
        ),
        Gaps.h16,
        _buildClientHistoryButton(),
        Gaps.h16,
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
                label: Text(AppPhrases.cancelCall),
                style: secondaryMediumButtonStyle,
                onPressed: screenState._onRejectCallPressed,
              ),
            ),
            Gaps.w8,
            Flexible(
              child: Button(
                label: Text(AppPhrases.generateReceipt),
                style: primaryMediumButtonStyle,
                enabled: !screenState._isOrderBeingUpdated,
                onPressed: screenState._onGenerateReceiptPressed,
              ),
            ),
          ],
        ),
      );

  Button _buildClientHistoryButton() {
    return Button.icon(
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
    );
  }
}
