import 'package:ambulance/app/app_version_manager.dart';
import 'package:ambulance/app/repositories/location/location.dart';
import 'package:ambulance/app/router/router.dart';
import 'package:ambulance/app/router/router.gr.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';
import 'package:ambulance/app/widgets/notification/notification.dart'
    as app_notification;
import 'package:toastification/toastification.dart';

final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();
final RouteObserver<ModalRoute<void>> routeObserver =
    RouteObserver<ModalRoute<void>>();

class AmbulanceApp extends StatefulWidget {
  const AmbulanceApp({super.key});

  @override
  State<AmbulanceApp> createState() => _AmbulanceAppState();
}

class _AmbulanceAppState extends State<AmbulanceApp> {
  final _appRouter = AppRouter();
  final _location = GetIt.I<AbstractLocationRepository>();
  final FlutterLocalNotificationsPlugin _notification =
      FlutterLocalNotificationsPlugin();

  @override
  void initState() {
    super.initState();
    _location.listenUserPosition();
    app_notification.Notification.initialize(_notification);
    _ensureCurrentAppVersionCompatible();
  }

  @override
  Widget build(BuildContext context) {
    return ToastificationWrapper(
      child: MaterialApp.router(
        theme: theme,
        debugShowCheckedModeBanner: false,
        locale: Locale('ru'),
        localizationsDelegates: const [
          GlobalMaterialLocalizations.delegate,
          GlobalWidgetsLocalizations.delegate,
          GlobalCupertinoLocalizations.delegate,
        ],
        builder: (context, child) => MediaQuery(
          data: MediaQuery.of(context).copyWith(
            alwaysUse24HourFormat: true,
            textScaler: const TextScaler.linear(1.0),
          ),
          child: child!,
        ),
        routerConfig: _appRouter.config(
          navigatorObservers: () => [
            routeObserver,
            TalkerRouteObserver(GetIt.I<Talker>()),
          ],
        ),
      ),
    );
  }

  void _ensureCurrentAppVersionCompatible() async {
    final versionInfo = await AppVersionManager.fetchVersionInfo();
    final curAppVersion = AppVersionManager.currentVersion;

    if (versionInfo.isNotCompatible(curAppVersion)) {
      _appRouter.push(VersionUpdateRequirementRoute(
        versionInfo: versionInfo,
      ));
      return;
    }

    if (versionInfo.canBeUpdated(curAppVersion)) {
      app_notification.Notification.showNotification(
        title: AppPhrases.updateApp,
        body: AppPhrases.appUpdateReleased,
        notificationsPlugin: _notification,
      );
    }
  }
}
