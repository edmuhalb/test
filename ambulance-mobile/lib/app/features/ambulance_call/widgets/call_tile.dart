import 'package:ambulance/app/api/dto/touch_to_call_request.dart';
import 'package:ambulance/app/api/rest_client.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/widgets/widgets.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:flutter/material.dart';
import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';
import 'package:toastification/toastification.dart';

class CallTile extends StatefulWidget {
  final AmbulanceCallDetails callDetails;

  const CallTile({
    super.key,
    required this.callDetails,
  });

  @override
  State<CallTile> createState() => _CallTileState();
}

class _CallTileState extends State<CallTile> {
  bool _isTouchingToCall = false;

  final api = GetIt.I<RestClientV1>();

  AmbulanceCallDetails get ambCallDetails => widget.callDetails;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(5),
        border: Border.all(width: 2, color: AppColors.scaffold),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          TileHeader(
            direction: 'row',
            title: 'Клиент:',
            crossAxisAlignment: CrossAxisAlignment.center,
            content: Padding(
              padding: EdgeInsets.only(left: Sizes.p8),
              child: Button.icon(
                label: Text(ambCallDetails.name ?? ''),
                icon: const Icon(Icons.phone),
                style: secondaryButtonStyle,
                isLoading: _isTouchingToCall,
                onPressed: () => _touchToCall()
                    .then(_onTouchedToCall)
                    .onError(_onTouchToCallFailed),
              ),
            ),
          ),
          TileSection(
              title: 'Адрес:',
              content:
                  '${ambCallDetails.address ?? ''} ${ambCallDetails.addressInfo ?? ''}'),
          TileSection(
              title: 'Нозология:',
              content: ambCallDetails.nosology,
              direction: 'row'),
          const TileSection(title: 'Х3:', direction: 'row'),
          TileSection(
              title: 'Примечание:', content: ambCallDetails.description),
        ],
      ),
    );
  }

  Future<void> _touchToCall() async {
    try {
      setState(() => _isTouchingToCall = true);
      await api.touchToCall(TouchToCallRequest(callId: ambCallDetails.id));
    } finally {
      setState(() => _isTouchingToCall = false);
    }
  }

  void _onTouchedToCall(void _) {
    toastification.show(
      title: Text(AppPhrases.touchToCallSuccess),
      description: Text(AppPhrases.waitForIncomingCall),
      type: ToastificationType.success,
      style: ToastificationStyle.flat,
      showIcon: false,
      showProgressBar: false,
      autoCloseDuration: const Duration(seconds: 5),
    );
  }

  void _onTouchToCallFailed(Object error, StackTrace stack) {
    GetIt.I<Talker>().debug('Touch to call failed', error, stack);
    FirebaseCrashlytics.instance.recordError(error, stack);

    toastification.show(
      title: Text(AppPhrases.touchToCallFailed),
      description: Text(AppPhrases.tryAgainLater),
      type: ToastificationType.error,
      style: ToastificationStyle.fillColored,
      backgroundColor: AppColors.errorToastBackground,
      primaryColor: AppColors.errorToastBackground,
      foregroundColor: AppColors.errorToastText,
      showIcon: false,
      showProgressBar: false,
      autoCloseDuration: const Duration(seconds: 5),
    );
  }
}
