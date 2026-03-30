import 'package:ambulance/app/features/ambulance_call/widgets/reject_ambulance_call/reject_ambulance_call_form.dart';
import 'package:flutter/material.dart';

import '../../../../theme/theme.dart';
import '../../../../widgets/bottom_sheet/bottom_sheet_scaffold.dart';
import '../../../../widgets/button/button.dart';

class RejectAmbulanceCallBottomSheet extends StatefulWidget {
  final ValueChanged<RejectAmbulanceCallBottomSheetState>?
      onCancelAmbulanceCallPressed;
  final VoidCallback? onContinueAmbulanceCallPressed;

  const RejectAmbulanceCallBottomSheet({
    super.key,
    this.onCancelAmbulanceCallPressed,
    this.onContinueAmbulanceCallPressed,
  });

  @override
  State<RejectAmbulanceCallBottomSheet> createState() =>
      RejectAmbulanceCallBottomSheetState();
}

class RejectAmbulanceCallBottomSheetState
    extends State<RejectAmbulanceCallBottomSheet> {
  final _formKey = GlobalKey<RejectAmbulanceCallFormState>();
  bool _submitting = false;

  bool get submitting => _submitting;

  set submitting(bool value) {
    if (_submitting == value) return;
    setState(() {
      _submitting = value;
    });
  }

  RejectAmbulanceCallFormState get form => _formKey.currentState!;

  @override
  Widget build(BuildContext context) {
    return BottomSheetScaffold(
      title: AppPhrases.specifyCancelationReason,
      body: RejectAmbulanceCallForm(
        key: _formKey,
      ),
      bottomBar: Row(
        children: [
          Flexible(
            child: Button(
              label: Text(AppPhrases.cancelAmbulanceCall),
              isLoading: submitting,
              style: secondaryMediumButtonStyle,
              onPressed: submitting
                  ? null
                  : () {
                      FocusScope.of(context).unfocus();
                      widget.onCancelAmbulanceCallPressed?.call(this);
                    },
            ),
          ),
          Gaps.w8,
          Flexible(
            child: Button(
              label: Text(AppPhrases.continueAmbulanceCall),
              style: primaryMediumButtonStyle,
              onPressed: submitting
                  ? null
                  : () {
                      FocusScope.of(context).unfocus();
                      widget.onContinueAmbulanceCallPressed?.call();
                    },
            ),
          ),
        ],
      ),
    );
  }
}
