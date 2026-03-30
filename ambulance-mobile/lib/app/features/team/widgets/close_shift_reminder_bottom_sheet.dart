import 'package:flutter/material.dart';

import '../../../theme/theme.dart';
import '../../../widgets/bottom_sheet/bottom_sheet_scaffold.dart';
import '../../../widgets/button/button.dart';

class CloseShiftReminderBottomSheet extends StatelessWidget {
  final VoidCallback? onSignOutPressed;
  final VoidCallback? onCloseShiftPressed;

  const CloseShiftReminderBottomSheet({
    super.key,
    this.onSignOutPressed,
    this.onCloseShiftPressed,
  });

  @override
  Widget build(BuildContext context) {
    return BottomSheetScaffold(
      title: AppPhrases.shiftIsNotClosed,
      body: Center(
        child: Text(
          AppPhrases.shiftClosureMotivation,
        ),
      ),
      bottomBar: Row(
        children: [
          Flexible(
            child: Button(
              label: Text(AppPhrases.signOut),
              style: secondaryMediumButtonStyle,
              onPressed: onSignOutPressed,
            ),
          ),
          Gaps.w8,
          Flexible(
            child: Button(
              label: Text(AppPhrases.closeWorkingShift),
              style: primaryMediumButtonStyle,
              onPressed: onCloseShiftPressed,
            ),
          ),
        ],
      ),
    );
  }
}
