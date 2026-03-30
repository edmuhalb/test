import 'package:ambulance/app/features/ambulance_call/widgets/end_of_treatment_time/end_of_treatment_time_form.dart';
import 'package:flutter/material.dart';

import '../../../../theme/theme.dart';
import '../../../../widgets/bottom_sheet/bottom_sheet_scaffold.dart';
import '../../../../widgets/button/button.dart';

class EndOfTreatmentBottomSheet extends StatefulWidget {
  final ValueChanged<EndOfTreatmentBottomSheetState> onSubmitPressed;

  const EndOfTreatmentBottomSheet({
    super.key,
    required this.onSubmitPressed,
  });

  @override
  State<EndOfTreatmentBottomSheet> createState() =>
      EndOfTreatmentBottomSheetState();
}

class EndOfTreatmentBottomSheetState extends State<EndOfTreatmentBottomSheet> {
  final _formKey = GlobalKey<EndOfTreatmentTimeFormState>();
  bool _submitting = false;

  bool get submitting => _submitting;

  set submitting(bool value) {
    if (_submitting == value) return;
    setState(() {
      _submitting = value;
    });
  }

  EndOfTreatmentTimeFormState get form => _formKey.currentState!;

  DateTime get endOfTreatmentDateTime {
    final now = DateTime.now();
    DateTime result = now.copyWith(
      hour: form.hour,
      minute: form.minute,
    );

    if (result.isBefore(now)) {
      result = result.add(Duration(days: 1));
    }

    return result;
  }

  @override
  Widget build(BuildContext context) => BottomSheetScaffold(
        title: AppPhrases.whatTimeWillYouFinish,
        body: EndOfTreatmentTimeForm(
          key: _formKey,
        ),
        bottomBar: Button(
          label: Text(AppPhrases.save),
          isLoading: submitting,
          style: primaryMediumButtonStyle,
          onPressed: () => widget.onSubmitPressed(this),
        ),
      );
}
