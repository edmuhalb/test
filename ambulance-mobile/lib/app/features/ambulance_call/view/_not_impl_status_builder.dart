part of 'ambulance_call_detail_screen.dart';

class _NotImplStatusBuilder extends _Builder {
  _NotImplStatusBuilder({
    required super.screenState,
  });

  @override
  String get headerText => AppPhrases.error;

  @override
  bool get hasStatusInfographics => false;

  @override
  List<String>? get importantNotes => [
        AppPhrases.notImplementedAmbulanceCallStatus,
        '${AppPhrases.status}: ${ambCallDetails.status}',
      ];

  @override
  bool get hasForm => false;

  @override
  bool get hasActionMenu => false;
}
