part of 'ambulance_call_detail_screen.dart';

abstract class _Builder {
  final _AmbulanceCallDetailScreenState screenState;

  _Builder({
    required this.screenState,
  });

  AmbulanceCallDetail get ambCallDetails => screenState._ambCallDetail!;

  bool get hasHeader => headerText != null && headerText!.isNotEmpty;
  bool get hasStatusInfographics => true;
  bool get hasImportantNotes =>
      importantNotes != null && importantNotes!.isNotEmpty;
  bool get hasForm => true;
  bool get hasActionMenu => true;
  bool get hasInfo => infoText != null && infoText!.isNotEmpty;

  String? get headerText => null;
  List<String>? get importantNotes => null;
  String? get infoText => null;

  Widget buildBody(BuildContext context) {
    return SingleChildScrollView(
      // TODO: refactor SafeAreas
      child: SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Gaps.h16,
            if (hasHeader) ...[
              buildHeader(context),
              Gaps.h16,
              Gaps.h4,
            ],
            if (hasStatusInfographics) ...[
              buildStatusInfographics(context),
              Gaps.h16,
            ],
            if (hasImportantNotes) ...[
              buildImportantNotes(context),
              Gaps.h16,
            ],
            if (hasForm) ...[
              buildForm(context),
              Gaps.h16,
            ],
            if (hasActionMenu) ...[
              buildActionMenu(context),
              Gaps.h16,
            ],
            if (hasInfo) ...[
              Gaps.h24,
              buildInfo(context),
              Gaps.h16,
            ],
          ],
        ),
      ),
    );
  }

  Widget buildHeader(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Text(
          headerText!,
          style: Theme.of(context).textTheme.headlineLarge,
        ),
      ],
    );
  }

  Widget buildStatusInfographics(BuildContext context) => const Placeholder(
        fallbackHeight: Sizes.p40,
      );

  Widget buildImportantNotes(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        for (String note in importantNotes ?? []) ...[
          Text(
            note,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: AppColors.alert,
                ),
          ),
        ],
        Gaps.h16,
        Divider(),
      ],
    );
  }

  Widget buildForm(BuildContext context) {
    final meetDateTime = DateTime.parse(ambCallDetails.dateTime!);
    final formattedMeetDate = dateOnlyFormat.format(meetDateTime);
    final formattedMeetTime = timeOnlyFormat.format(meetDateTime);
    final meetTimeFieldValue =
        '$formattedMeetDate ${AppPhrases.atTime} $formattedMeetTime';
    var addressFieldValue = ambCallDetails.address;
    if (ambCallDetails.addressInfo != null) {
      addressFieldValue += ' ${ambCallDetails.addressInfo}';
    }

    return Form(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          TextFormField(
            initialValue: meetTimeFieldValue,
            enabled: false,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.clientIsWaitingForTheTeam}:',
            ),
          ),
          Gaps.h16,
          TextFormField(
            initialValue: ambCallDetails.client?.name,
            enabled: false,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.clientFullName}:',
            ),
          ),
          Gaps.h16,
          TextFormField(
            initialValue: addressFieldValue,
            maxLines: null,
            enabled: false,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.address}:',
            ),
          ),
          Gaps.h16,
          TextFormField(
            initialValue: ambCallDetails.description,
            maxLines: null,
            enabled: false,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.comment}:',
            ),
          ),
        ],
      ),
    );
  }

  Widget buildActionMenu(BuildContext context) {
    return buildCallToClientCard();
  }

  Material buildCallToClientCard() {
    return Material(
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
    );
  }

  Widget buildInfo(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          AppPhrases.information,
          style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: AppColors.success,
              ),
        ),
        Gaps.h8,
        Text(
          infoText!,
          style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: AppColors.text,
              ),
        ),
      ],
    );
  }

  Widget? buildBottomBar(BuildContext context) => null;
}
