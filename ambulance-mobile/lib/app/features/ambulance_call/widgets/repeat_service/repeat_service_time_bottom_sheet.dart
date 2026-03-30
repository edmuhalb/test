import 'package:flutter/material.dart';

import '../../../../theme/theme.dart';
import '../../../../widgets/bottom_sheet/bottom_sheet_scaffold.dart';
import '../../../../widgets/button/button.dart';
import 'repeat_service_time_form.dart';

class RepeatServiceTimeBottomSheet extends StatefulWidget {
  final ValueChanged<RepeatServiceTimeBottomSheetState> onSubmitPressed;
  final int? initialHour;
  final int? initialMinute;

  const RepeatServiceTimeBottomSheet({
    super.key,
    this.initialHour,
    this.initialMinute,
    required this.onSubmitPressed,
  });

  @override
  State<RepeatServiceTimeBottomSheet> createState() =>
      RepeatServiceTimeBottomSheetState();
}

class RepeatServiceTimeBottomSheetState
    extends State<RepeatServiceTimeBottomSheet> {
  final _formKey = GlobalKey<RepeatServiceTimeFormState>();

  RepeatServiceTimeFormState get form => _formKey.currentState!;

  @override
  Widget build(BuildContext context) {
    return BottomSheetScaffold(
      title: AppPhrases.timeOfRepeatService,
      body: RepeatTimeForm(
        key: _formKey,
        initialHour: widget.initialHour,
        intialMinute: widget.initialMinute,
      ),
      bottomBar: Button(
        label: Text(AppPhrases.save),
        style: primaryButtonStyle,
        onPressed: () => widget.onSubmitPressed(this),
      ),
    );
  }
}
