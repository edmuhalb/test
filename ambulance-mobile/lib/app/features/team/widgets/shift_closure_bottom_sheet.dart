import 'package:flutter/material.dart';

import 'shift_closure_form.dart';
import '../../../theme/theme.dart';
import '../../../widgets/bottom_sheet/bottom_sheet_scaffold.dart';
import '../../../widgets/button/button.dart';

class ShiftClosureBottomSheet extends StatefulWidget {
  final VoidCallback? onCancelPressed;
  final ValueChanged<ShiftClosureBottomSheetState>? onSubmitPressed;

  const ShiftClosureBottomSheet({
    super.key,
    this.onCancelPressed,
    this.onSubmitPressed,
  });

  @override
  State<ShiftClosureBottomSheet> createState() =>
      ShiftClosureBottomSheetState();
}

class ShiftClosureBottomSheetState extends State<ShiftClosureBottomSheet> {
  final _shiftClosureFormKey = GlobalKey<ShiftClosureFormState>();
  bool _submitting = false;

  bool get submitting => _submitting;

  set submitting(bool value) {
    if (_submitting == value) return;
    setState(() {
      _submitting = value;
    });
  }

  ShiftClosureFormState get shiftClosureForm =>
      _shiftClosureFormKey.currentState!;

  @override
  Widget build(BuildContext context) {
    return BottomSheetScaffold(
      body: ShiftClosureForm(
        key: _shiftClosureFormKey,
      ),
      bottomBar: Row(
        children: [
          Flexible(
            child: Button(
              label: Text(AppPhrases.cancel),
              style: secondaryMediumButtonStyle,
              onPressed: submitting ? null : widget.onCancelPressed,
            ),
          ),
          Gaps.w8,
          Flexible(
            child: Button(
              label: Text(AppPhrases.finishWorkingShift),
              isLoading: submitting,
              style: primaryMediumButtonStyle,
              onPressed:
                  submitting ? null : () => widget.onSubmitPressed?.call(this),
            ),
          ),
        ],
      ),
    );
  }
}
