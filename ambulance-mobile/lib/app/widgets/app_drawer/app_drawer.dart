library;

import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

import '../../api/dto/transport_report_detail.dart';
import '../../api/rest_client.dart';
import '../../features/team/widgets/widgets.dart';
import '../../theme/theme.dart';
import '../button/button.dart';

import 'package:ambulance/app/repositories/team/team.dart';
import 'package:ambulance/app/router/router.gr.dart';
import 'package:auto_route/auto_route.dart';
import 'package:dio/dio.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:get_it/get_it.dart';
import 'package:intl/intl.dart';
import 'package:jwt_decoder/jwt_decoder.dart';
import 'package:talker_flutter/talker_flutter.dart';
import 'package:toastification/toastification.dart';

import '../../app_version_manager.dart';
import '../../app_preferences.dart';
import '../bottom_sheet/custom_bottom_sheet.dart';

part '_app_drawer_header.dart';
part '_app_drawer_menu.dart';
part '_app_drawer_footer.dart';

class AppDrawer extends StatefulWidget {
  const AppDrawer({super.key});

  @override
  State<AppDrawer> createState() => _AppDrawerState();
}

class _AppDrawerState extends State<AppDrawer> {
  final dio = GetIt.I<Dio>();
  final api = GetIt.I<RestClientV1>();
  // TODO: figure out
  // final _notification = FlutterLocalNotificationsPlugin();
  final _teamRepository = GetIt.I<TeamRepository>();
  late Future<Team?> _teamFuture;
  Map<String, dynamic>? _userData;

  int get userId => userData["id"];
  String get username => userData["name"];
  Map<String, dynamic> get userData {
    return _userData ??= _getUserDataFromAuthToken();
  }

  @override
  void initState() {
    super.initState();
    // TODO: figure out
    // app_notification.Notification.initialize(_notification);
    _teamFuture = _fetchTeam();
  }

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: Column(
        children: [
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                _AppDrawerHeader(title: username),
                _AppDrawerMenu(
                  onAmbulanceTeamTapped: _gotoAmbulanceShift,
                  onAmbulanceCallsTapped: _gotoAmbulanceCalls,
                  onProfileTapped: _gotoProfile,
                  onSignOutTapped: _onSignOutTileTapped,
                  teamFuture: _teamFuture,
                  onFinishShiftTapped: _onFinishShiftTapped,
                ),
              ],
            ),
          ),
          _AppDrawerFooter(),
        ],
      ),
    );
  }

  void _gotoAmbulanceShift() {
    AutoRouter.of(context).maybePop(context);
    AutoRouter.of(context).replace(TeamRoute());
  }

  void _gotoAmbulanceCalls() {
    AutoRouter.of(context).maybePop(context);
    AutoRouter.of(context).replace(const CallsListRoute());
  }

  void _gotoProfile() {
    AutoRouter.of(context).maybePop(context);
    AutoRouter.of(context).push(const ProfileRoute());
  }

  void _onSignOutTileTapped() async {
    final team = await _teamFuture;

    if (team == null) {
      _signOut();
      return;
    }

    final doSignOut = await _remindToCloseShift(team.id);

    if (doSignOut == true) {
      _signOut();
    }
  }

  void _onFinishShiftTapped() async {
    final team = await _teamFuture;
    final isShiftFinished =
        team != null ? await _showShiftClosureForm(team.id) : false;

    if (isShiftFinished == true) {
      _gotoAmbulanceShift();
    }
  }

  _remindToCloseShift(int teamId) => showCustomModalBottomSheet(
        context: context,
        showDragHandle: true,
        showCloseButton: true,
        useSafeArea: true,
        builder: (context) => CloseShiftReminderBottomSheet(
          onSignOutPressed: () => Navigator.of(context).pop(true),
          onCloseShiftPressed: () async {
            final isShiftFinished = await _showShiftClosureForm(teamId);

            if (isShiftFinished == true && context.mounted) {
              Navigator.of(context).pop(true);
            }
          },
        ),
      );

  _showShiftClosureForm(int teamId) => showCustomModalBottomSheet(
        context: context,
        showCloseButton: true,
        showDragHandle: true,
        isScrollControlled: true,
        builder: (context) => ShiftClosureBottomSheet(
          onCancelPressed: () => Navigator.of(context).pop(false),
          onSubmitPressed: (sheet) {
            if (sheet.shiftClosureForm.validate()) {
              sheet.shiftClosureForm.save();
              _submitShiftClosure(sheet, teamId: teamId)
                  .then(_onShiftClosed)
                  .onError(_onShiftClosingFailed);
            }
          },
        ),
      );

  Future<void> _submitShiftClosure(
    ShiftClosureBottomSheetState sheet, {
    required int teamId,
  }) async {
    try {
      sheet.submitting = true;
      await api.finishShift(
        teamId: teamId,
        transportReport: TransportReportDetail(
          mileage: sheet.shiftClosureForm.mileage!,
          toolRoad: sheet.shiftClosureForm.tollRoad!,
          parkingFees: sheet.shiftClosureForm.parkingFees!,
          files: sheet.shiftClosureForm.files,
        ),
      );
    } finally {
      sheet.submitting = false;
    }
  }

  void _onShiftClosed(void _) {
    toastification.show(
      title: Text(AppPhrases.shiftSuccessfullyClosed),
      type: ToastificationType.success,
      style: ToastificationStyle.simple,
      showIcon: false,
      showProgressBar: false,
      autoCloseDuration: const Duration(seconds: 5),
    );

    if (context.mounted) {
      setState(() {
        _teamFuture = _fetchTeam();
      });

      Navigator.of(context).pop(true);
    }
  }

  void _onShiftClosingFailed(Object error, StackTrace stack) {
    GetIt.I<Talker>().debug('Shift closing failed', error, stack);
    FirebaseCrashlytics.instance.recordError(error, stack);

    toastification.show(
      title: Text(AppPhrases.finishShiftClosureError),
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

  void _signOut() {
    GetIt.I<AppPreferences>().clear();

    _disablePushNotifications();

    final router = context.router;
    router.replace(SignInRoute(onSignedIn: () {
      router.replace(const CallsListRoute());
    }));
  }

  Map<String, dynamic> _getUserDataFromAuthToken() {
    final appPreferences = GetIt.I<AppPreferences>();
    final token = appPreferences.authToken;

    if(token == null) {
      _signOut();  
    }

    return JwtDecoder.decode(token!);
  }

  Future<Team?> _fetchTeam() async {
    final dateFormat = DateFormat("yyyy-MM-ddTHH:mm:ss+03:00");
    final now = DateTime.now();
    final beginningOfYesterday = DateTime(now.year, now.month, now.day)
        .subtract(const Duration(days: 1));
    final endOfToday = DateTime(now.year, now.month, now.day, 23, 59, 59);

    final teams = await _teamRepository.getTeamList({
      'admin.id': userId,
      'pagination': false,
      'plannedStartAt[after]': dateFormat.format(beginningOfYesterday),
      'plannedStartAt[before]': dateFormat.format(endOfToday),
      'order[plannedStartAt]': "asc"
    });

    final workTeamList = teams.where((element) => element.status == "work");

    if (workTeamList.isNotEmpty) {
      return workTeamList.first;
    }

    final scheduledTeamList =
        teams.where((element) => element.status == "scheduled");

    if (scheduledTeamList.isNotEmpty) {
      return scheduledTeamList.first;
    }

    return null;
  }

  Future<void> _disablePushNotifications() async {
    try {
      final fcmToken = await FirebaseMessaging.instance.getToken();

      if (fcmToken == null) {
        throw Exception('FCM token is missing');
      }

      await dio.delete('devices/$fcmToken');
    } catch (error, stack) {
      GetIt.I<Talker>().debug('Disable push notifications error', error, stack);
      FirebaseCrashlytics.instance.recordError(error, stack);
    }
  }
}
