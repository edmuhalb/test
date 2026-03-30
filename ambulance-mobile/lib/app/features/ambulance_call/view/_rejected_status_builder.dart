part of 'ambulance_call_detail_screen.dart';

class _RejectedStatusBuilder extends _Builder {
  _RejectedStatusBuilder({
    required super.screenState,
  });

  @override
  String get headerText => AppPhrases.ambulanceCallRejected;

  @override
  String? get infoText => screenState._ambCallDetail!.reasonForCancellation!.name;

  @override
  bool get hasStatusInfographics => false;

  @override
  bool get hasActionMenu => false;

  @override
  Widget buildInfo(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          AppPhrases.rejectionReason,
          style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: AppColors.alert,
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
}
