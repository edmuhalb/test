import 'package:flutter/material.dart';

import '../../../../theme/theme.dart';
import '../../../../widgets/bottom_sheet/bottom_sheet_scaffold.dart';
import '../../../../widgets/button/button.dart';
import 'arrival_time_form.dart';

class ArrivalTimeBottomSheet extends StatefulWidget {
  final ValueChanged<ArrivalTimeBottomSheetState> onSubmitPressed;

  const ArrivalTimeBottomSheet({
    super.key,
    required this.onSubmitPressed,
  });

  @override
  State<ArrivalTimeBottomSheet> createState() => ArrivalTimeBottomSheetState();
}

class ArrivalTimeBottomSheetState extends State<ArrivalTimeBottomSheet> {
  final _formKey = GlobalKey<ArrivalTimeFormState>();
  bool _submitting = false;

  bool get submitting => _submitting;

  set submitting(bool value) {
    if (_submitting == value) return;
    setState(() {
      _submitting = value;
    });
  }

  ArrivalTimeFormState get form => _formKey.currentState!;

  DateTime get arrivalDateTime {
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
        title: AppPhrases.estimatedTimeOfArrival,
        body: ArrivalTimeForm(
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
