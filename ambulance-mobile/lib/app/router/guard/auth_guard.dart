import 'package:ambulance/app/router/router.gr.dart';
import 'package:auto_route/auto_route.dart';
import 'package:dio/dio.dart';
import 'package:get_it/get_it.dart';

import '../../app_preferences.dart';

class AuthGuard extends AutoRouteGuard {
  @override
  void onNavigation(NavigationResolver resolver, StackRouter router) async {
    final appPreferences = GetIt.I<AppPreferences>();
    final dio = GetIt.I<Dio>();

    final token = appPreferences.authToken;
    dio.options.headers["Authorization"] = "Bearer $token";

    if (appPreferences.authenticated) {
      resolver.next(true);
    } else {
      router.replace(SignInRoute(onSignedIn: () {
        resolver.next(true);
        router.removeLast();
      }));
    }
  }
}
