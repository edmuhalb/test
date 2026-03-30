import 'package:ambulance/app/router/guard/auth_guard.dart';
import 'package:auto_route/auto_route.dart';

import 'router.gr.dart';

@AutoRouterConfig()
class AppRouter extends RootStackRouter {
  @override
  List<AutoRoute> get routes => [
        AutoRoute(
          page: CallsListRoute.page,
          initial: true,
          guards: [AuthGuard()],
        ),
        AutoRoute(
          page: MedicationsSelectionRoute.page,
          // initial: true,
          guards: [AuthGuard()],
        ),
        AutoRoute(
          page: SignInRoute.page,
        ),
        AutoRoute(
          page: AmbulanceCallDetailRoute.page,
          guards: [AuthGuard()],
        ),
        AutoRoute(
          page: ClientCallHistoryRoute.page,
          guards: [AuthGuard()],
        ),
        AutoRoute(
          page: TeamRoute.page,
          guards: [AuthGuard()],
        ),
        AutoRoute(
          page: ProfileRoute.page,
          guards: [AuthGuard()],
        ),
        AutoRoute(
          page: VersionUpdateRequirementRoute.page,
        ),
      ];
}
