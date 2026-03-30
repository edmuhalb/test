library;

import 'dart:async';

import 'package:ambulance/app/api/dto/order_item.dart';
import 'package:ambulance/app/api/dto/touch_to_call_request.dart';
import 'package:ambulance/app/api/rest_client.dart';
import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:ambulance/app/router/router.gr.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/utils/date_time_utils.dart';
import 'package:ambulance/app/widgets/bottom_sheet/custom_bottom_sheet.dart';
import 'package:ambulance/app/widgets/scaffold/scaffold_body.dart';
import 'package:ambulance/app/widgets/widgets.dart';
import 'package:auto_route/auto_route.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';
import 'package:toastification/toastification.dart';

import '../../../api/dto/ambulance_call_detail.dart';
import '../../../repositories/services/services.dart';
import '../../../widgets/scaffold/scaffold_bottom_bar.dart';
import '../widgets/widgets.dart';

part '_builder.dart';
part '_not_impl_status_builder.dart';
part '_assigned_status_builder.dart';
part '_accepted_status_builder.dart';
part '_departed_status_builder.dart';
part '_arrived_status_builder.dart';
part '_treating_status_builder.dart';
part '_receipt_builder.dart';
part '_completed_status_builder.dart';
part '_rejected_status_builder.dart';

@RoutePage()
class AmbulanceCallDetailScreen extends StatefulWidget {
  const AmbulanceCallDetailScreen({
    super.key,
    required this.call,
    required this.clientCalls,
  });

  final AmbulanceCall call;
  final List<ClientCall> clientCalls;

  @override
  State<AmbulanceCallDetailScreen> createState() =>
      _AmbulanceCallDetailScreenState();
}

class _AmbulanceCallDetailScreenState extends State<AmbulanceCallDetailScreen> {
  final api = GetIt.I<RestClientV1>();

  StateSetter rebuildBody = (VoidCallback _) {};
  StateSetter rebuildBottomBar = (VoidCallback _) {};

  AmbulanceCallDetail? _ambCallDetail;

  final _patientFormKey = GlobalKey<PatientFormState>();
  final _servicesCartKey = GlobalKey<CartWidgetState>();
  final _receiptExtraFormKey = GlobalKey<ReceiptExtraFormState>();

  bool _isTouchingToCall = false;
  bool _isStatusBeingUpdated = false;
  bool _isOrderBeingUpdated = false;

  bool _showReceipt = false;
  String? _receiptComment;
  bool? _advertised;

  int get ambCallId => widget.call.id;

  bool get isShiftOpen => _ambCallDetail?.isShiftOpen ?? false;

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) => AppFutureBuilder<AmbulanceCallDetail>(
        future: _ambCallDetail != null
            ? Future.value(_ambCallDetail!)
            : api.getAmbulanceCallDetail(ambCallId),
        progressBuilder: (context) => _buildScaffold(
          body: AppFutureBuilder.buildDefaultProgressWidget(),
        ),
        builder: (context, data) {
          _ambCallDetail = data;

          final ambCallStatus = _ambCallDetail!.status;
          final ambCallDetailsBuilder = _createCallDetailsBuilder(
            ambCallStatus,
          );

          return _buildScaffold(
            body: RefreshIndicator(
              color: AppColors.primary,
              backgroundColor: AppColors.card,
              onRefresh: _onRefresh,
              child: RepaintBoundary(
                child: StatefulBuilder(builder: (context, stateSetter) {
                  rebuildBody = stateSetter;
                  return ambCallDetailsBuilder.buildBody(context);
                }),
              ),
            ),
            bottomAppBar: RepaintBoundary(
              child: StatefulBuilder(builder: (context, stateSetter) {
                rebuildBottomBar = stateSetter;
                return ambCallDetailsBuilder.buildBottomBar(context) ??
                    Container();
              }),
            ),
          );
        },
        errorBuilder: (context, error) => _buildScaffold(
          body: ErrorView(
            errorMessage: error.toString(),
            onTryAgain: () => setState(() {}),
          ),
        ),
      );

  _Builder _createCallDetailsBuilder(String? ambCallStatus) =>
      switch (ambCallStatus) {
        AmbulanceCallStatus.assigned =>
          _AssignedStatusBuilder(screenState: this),
        AmbulanceCallStatus.accepted =>
          _AcceptedStatusBuilder(screenState: this),
        AmbulanceCallStatus.departed =>
          _DepartedStatusBuilder(screenState: this),
        AmbulanceCallStatus.arrived => _ArrivedStatusBuilder(
            screenState: this,
            patientFormKey: _patientFormKey,
          ),
        AmbulanceCallStatus.treating => _showReceipt
            ? _ReceiptBuilder(
                screenState: this,
                receiptExtraFormKey: _receiptExtraFormKey,
              )
            : _TreatingStatusBuilder(
                screenState: this,
                cartKey: _servicesCartKey,
              ),
        AmbulanceCallStatus.completed => _CompletedStatusBuilder(
            screenState: this,
            receiptExtraFormKey: _receiptExtraFormKey,
          ),
        AmbulanceCallStatus.rejected =>
          _RejectedStatusBuilder(screenState: this),
        _ => _NotImplStatusBuilder(screenState: this),
      };

  Widget _buildScaffold({
    required Widget body,
    Widget? bottomAppBar,
  }) =>
      Scaffold(
        extendBody: true,
        body: ScaffoldBody(
          child: Stack(
            clipBehavior: Clip.none,
            children: [
              body,
              Positioned(
                top: 1,
                left: -Sizes.p16,
                child: _buildCloseScreenButton(context),
              ),
            ],
          ),
        ),
        bottomNavigationBar: bottomAppBar,
      );

  Widget _buildCloseScreenButton(BuildContext context) => IconButton(
        padding: EdgeInsets.zero,
        color: AppColors.primary,
        icon: Icon(
          Icons.close,
          size: Sizes.p24,
        ),
        onPressed: () => AutoRouter.of(context).maybePop(),
      );

  Future _onRefresh() async => setState(() {});

  void _onTouchToCallPressed() =>
      _touchToCall().then(_onTouchedToCall).catchError(_onTouchToCallFailed);

  Future<void> _touchToCall() async {
    try {
      setState(() => _isTouchingToCall = true);
      await api.touchToCall(TouchToCallRequest(callId: ambCallId));
    } finally {
      setState(() => _isTouchingToCall = false);
    }
  }

  void _onTouchedToCall(void _) => _showSuccessToast(
        title: AppPhrases.touchToCallSuccess,
        description: AppPhrases.waitForIncomingCall,
      );

  void _onTouchToCallFailed(Object error, StackTrace stack) {
    _logError('Touch to call failed', error, stack);
    _showErrorToast(
      title: AppPhrases.touchToCallFailed,
      description: AppPhrases.tryAgainLater,
    );
  }

  void _onRejectCallPressed() => showCustomModalBottomSheet(
        context: context,
        showCloseButton: true,
        showDragHandle: true,
        isScrollControlled: true,
        builder: (context) => RejectAmbulanceCallBottomSheet(
          onContinueAmbulanceCallPressed: () => Navigator.of(context).pop(),
          onCancelAmbulanceCallPressed: (sheet) {
            if (!sheet.form.validate()) return;
            sheet.form.save();
            _submitCallRejection(sheet)
                .then(_onAmbulanceCallRejected)
                .catchError(_onAmbulanceCallRejectionFailed);
          },
        ),
      );

  Future<void> _submitCallRejection(
    RejectAmbulanceCallBottomSheetState sheet,
  ) async {
    try {
      sheet.submitting = true;
      await api.rejectAmbulanceCall(
        ambCallId,
        rejectionReason: sheet.form.rejectionReason!,
      );
    } finally {
      sheet.submitting = false;
    }
  }

  void _onAmbulanceCallRejected(void _) {
    Navigator.of(context).pop();

    toastification.show(
      title: Text(AppPhrases.ambulanceCallSuccessfullyRejected),
      type: ToastificationType.success,
      style: ToastificationStyle.simple,
      showIcon: false,
      showProgressBar: false,
      autoCloseDuration: const Duration(seconds: 5),
    );

    if (context.mounted) {
      AutoRouter.of(context).replace(CallsListRoute());
    }
  }

  void _onAmbulanceCallRejectionFailed(Object error, StackTrace stack) {
    _logError('Ambulance call rejection failed', error, stack);
    _showErrorToast(
      title: AppPhrases.callRejectionError,
      description: AppPhrases.tryAgainLater,
    );
  }

  void _onAcceptPressed() => _updateStatus(() async {
        _ambCallDetail = await api.acceptAmbulanceCall(ambCallId);
      }).then(_onStatusUpdated).catchError(
            (error, stack) => _onUpdateStatusFailed(
              status: AmbulanceCallStatus.accepted,
              error: error,
              stack: stack,
            ),
          );

  void _onDepartedPressed() async {
    final arrivalDateTime = await showCustomModalBottomSheet(
      context: context,
      showCloseButton: true,
      showDragHandle: true,
      isScrollControlled: true,
      builder: (context) => ArrivalTimeBottomSheet(
        onSubmitPressed: (sheet) =>
            Navigator.of(context).pop(sheet.arrivalDateTime),
      ),
    );

    if (arrivalDateTime == null) return;

    _updateStatus(() async {
      _ambCallDetail = await api.updateAmbulanceCallToDeparted(
        ambCallId,
        arrivalDateTime: arrivalDateTime,
      );
    }).then(_onStatusUpdated).catchError(
          (error, stack) => _onUpdateStatusFailed(
            status: AmbulanceCallStatus.departed,
            error: error,
            stack: stack,
          ),
        );
  }

  void _onArrivedPressed() {
    _updateStatus(() async {
      _ambCallDetail = await api.updateAmbulanceCallToArrived(ambCallId);
    }).then(_onStatusUpdated).catchError(
          (error, stack) => _onUpdateStatusFailed(
            status: AmbulanceCallStatus.arrived,
            error: error,
            stack: stack,
          ),
        );
  }

  void _onClientHistoryPressed() {
    AutoRouter.of(context).push(
      ClientCallHistoryRoute(
        ambCallId: ambCallId,
        clientId: _ambCallDetail!.client!.id,
      ),
    );
  }

  void _onStartTreatmentPressed() async {
    final patientForm = _patientFormKey.currentState!;

    if (!patientForm.validate()) return;
    patientForm.save();

    final endOfTreatmentDateTime = await showCustomModalBottomSheet(
      context: context,
      showCloseButton: true,
      showDragHandle: true,
      isScrollControlled: true,
      builder: (context) => EndOfTreatmentBottomSheet(
        onSubmitPressed: (sheet) =>
            Navigator.of(context).pop(sheet.endOfTreatmentDateTime),
      ),
    );

    if (endOfTreatmentDateTime == null) return;

    _updateStatus(() async {
      _ambCallDetail = await api.updateAmbulanceCallToTreating(
        ambCallId,
        endOfServiceDateTime: endOfTreatmentDateTime,
        dateOfBirth: patientForm.dateOfBirth!,
        patientFullName: patientForm.fullName!,
        note: patientForm.note,
        address: patientForm.address,
        addressInfo: patientForm.addressInfo,
        description: patientForm.description,
      );
    }).then(_onStatusUpdated).catchError(
          (error, stack) => _onUpdateStatusFailed(
            status: AmbulanceCallStatus.treating,
            error: error,
            stack: stack,
          ),
        );
  }

  void _onServicePutIntoCart(OrderItem orderItem) async {
    final cart = _servicesCartKey.currentState!;
    rebuildBottomBar(() {
      _isOrderBeingUpdated = true;
    });
    try {
      _ambCallDetail = await api.updateAmbulanceCallOrder(
        ambCallId,
        order: cart.items,
      );
    } catch (error, stack) {
      cart.remove(orderItem.service, silent: true);
      _logError('Failed to update call details when service was added to cart',
          error, stack);
      _showErrorToast(
        title: AppPhrases.addServiceToCartRequestError,
        description: AppPhrases.tryAgainLater,
      );
    } finally {
      rebuildBottomBar(() {
        _isOrderBeingUpdated = false;
      });
    }
  }

  void _onServiceRemovedFromCart(OrderItem orderItem) async {
    final cart = _servicesCartKey.currentState!;
    rebuildBottomBar(() {
      _isOrderBeingUpdated = true;
    });
    try {
      _ambCallDetail = await api.updateAmbulanceCallOrder(
        ambCallId,
        order: cart.items,
      );
    } catch (error, stack) {
      cart.put(orderItem, silent: true);
      _logError(
          'Failed to update call details when service was removed from cart',
          error,
          stack);
      _showErrorToast(
        title: AppPhrases.removeServiceFromCartRequestError,
        description: AppPhrases.tryAgainLater,
      );
    } finally {
      rebuildBottomBar(() {
        _isOrderBeingUpdated = false;
      });
    }
  }

  void _onGenerateReceiptPressed() => setState(() {
        _showReceipt = true;
      });

  void _onReceiptBackPressed() => setState(() {
        _showReceipt = false;

        final form = _receiptExtraFormKey.currentState!;

        if (form.validate()) {
          form.save();
        }

        _receiptComment = form.comment;
        _advertised = form.advertised;
      });

  void _onEndTheCallPressed() {
    final form = _receiptExtraFormKey.currentState!;
    if (!form.validate()) return;
    form.save();
    _receiptComment = form.comment;
    _advertised = form.advertised;

    _updateStatus(() async {
      _ambCallDetail = await api.finishAmbulanceCall(
        ambCallId,
        receiptComment: _receiptComment,
        advertised: _advertised,
      );
    }).then(_onAmbulandeCallCompleted).catchError(
          (error, stack) => _onUpdateStatusFailed(
            status: AmbulanceCallStatus.completed,
            error: error,
            stack: stack,
          ),
        );
  }

  void _onAmbulandeCallCompleted(void _) {
    _showSuccessToast(title: AppPhrases.ambulanceCallSuccessfullyCompleted);
    AutoRouter.of(context).replace(CallsListRoute());
  }

  Future<void> _updateStatus(
    AsyncCallback updater,
  ) async {
    rebuildBottomBar(() {
      _isStatusBeingUpdated = true;
    });

    try {
      await updater();
    } finally {
      rebuildBottomBar(() {
        _isStatusBeingUpdated = false;
      });
    }
  }

  void _onStatusUpdated(void _) => setState(() {});

  void _onUpdateStatusFailed({
    required String status,
    required Object error,
    required StackTrace stack,
  }) {
    _logError('Ambulance call status update to $status failed', error, stack);
    _showErrorToast(
      title: AppPhrases.serverRequestError,
      description: AppPhrases.tryAgainLater,
    );
  }

  void _logError(String message, Object? error, StackTrace? stack) {
    GetIt.I<Talker>().debug(message, error, stack);
    FirebaseCrashlytics.instance.recordError(error, stack);
  }

  void _showSuccessToast({
    required String title,
    String? description,
  }) =>
      toastification.show(
        title: Text(title),
        description: description != null ? Text(description) : null,
        type: ToastificationType.success,
        style: ToastificationStyle.flat,
        showIcon: false,
        showProgressBar: false,
        autoCloseDuration: const Duration(seconds: 5),
      );

  void _showErrorToast({
    required String title,
    String? description,
  }) =>
      toastification.show(
        title: Text(title),
        description: description != null ? Text(description) : null,
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
