import 'package:ambulance/app/ambulance_app_options.dart';
import 'package:ambulance/app/app_preferences.dart';
import 'package:ambulance/firebase_options.dart';
import 'package:ambulance/app/repositories/clinics/default_clinics_repository.dart';
import 'package:ambulance/app/repositories/firebase/firebase_repository.dart';
import 'package:ambulance/app/repositories/location/abstract_location_repository.dart';
import 'package:ambulance/app/repositories/location/location_repository.dart';
import 'package:ambulance/app/repositories/services/abstract_services_repository.dart';
import 'package:ambulance/app/repositories/services/services_repository.dart';
import 'package:ambulance/app/repositories/team/team.dart';
import 'package:ambulance/app/repositories/version/abstract_version_repository.dart';
import 'package:ambulance/app/repositories/version/version_repository.dart';
import 'package:dio/dio.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:internet_connection_checker/internet_connection_checker.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:talker_bloc_logger/talker_bloc_logger_observer.dart';
import 'package:talker_dio_logger/talker_dio_logger_interceptor.dart';
import 'package:talker_flutter/talker_flutter.dart';

import 'api/rest_client.dart';
import 'repositories/ambulance_call/ambulance_call.dart';
import 'repositories/clinics/clinic_repository.dart';
import 'repositories/user/user.dart';
import 'theme/theme.dart';

class AmbulanceAppInitializer {
  static AmbulanceAppOptions? _config;

  static AmbulanceAppOptions? get config => _config;

  static Future<void> init(AmbulanceAppOptions config) async {
    if (_config != null) {
      throw Exception('Ambulance app already initialized');
    }

    await _init(_config = config);
  }

  static Future<void> _init(AmbulanceAppOptions config) async {
    await initializeDateFormatting(config.localeCode);

    final talker = _initTalker(config);
    final dio = _initDio(config, talker: talker);

    await _configureDependencyInjection(config, talker: talker, dio: dio);

    await _initFirebase(config);
  }

  static Talker _initTalker(AmbulanceAppOptions config) {
    final talker = TalkerFlutter.init();

    FlutterError.onError = (details) => GetIt.I<Talker>().handle(
          details.exception,
          details.stack,
        );

    Bloc.observer = TalkerBlocObserver(
      talker: talker,
      settings: _config!.talkerBlocLoggerSettings,
    );

    return talker;
  }

  static Dio _initDio(
    AmbulanceAppOptions config, {
    required Talker talker,
  }) {
    final dio = Dio(BaseOptions(
      baseUrl: config.apiBaseUrl,
      headers: config.dioHeaders,
    ));

    _initDioInterceptors(
      config,
      dio: dio,
      talker: talker,
    );

    return dio;
  }

  static void _initDioInterceptors(
    AmbulanceAppOptions config, {
    required Dio dio,
    required Talker talker,
  }) {
    dio.interceptors
      // TODO: extract auth interceptor
      ..add(InterceptorsWrapper(
        onError: (DioException e, ErrorInterceptorHandler handler) async {
          if (e.response?.statusCode == 401 || e.response?.statusCode == 403) {
            GetIt.I<AppPreferences>().removeAuthToken();
          }
          handler.reject(e);
        },
        onRequest: (options, handler) async {
          final bool hasConnection =
              await InternetConnectionChecker().hasConnection;

          if (!hasConnection) {
            return handler.reject(DioException(
              requestOptions: options,
              message: AppPhrases.noInternetConnection,
            ));
          }

          handler.next(options);
        },
      ))
      ..add(TalkerDioLogger(
        talker: talker,
        settings: config.talkerDioLoggerSettings,
      ));
  }

  static Future<FirebaseApp> _initFirebase(AmbulanceAppOptions config) async {
    final app = await Firebase.initializeApp(
      options: DefaultFirebaseOptions.currentPlatform,
    );

    await FirebaseRepository().initNotifications();

    return app;
  }

  static Future<void> _configureDependencyInjection(
    AmbulanceAppOptions config, {
    required Talker talker,
    required Dio dio,
  }) async {
    final sharedPreferences = await SharedPreferences.getInstance();
    final packageInfo = await PackageInfo.fromPlatform();

    GetIt.I.registerSingleton<PackageInfo>(packageInfo);
    GetIt.I.registerSingleton<AppPreferences>(
      AppPreferences(sharedPreferences),
    );
    GetIt.I.registerSingleton(talker);
    GetIt.I<Talker>().debug('Talker started...');

    GetIt.I.registerSingleton(RestClientV1(
      dio,
      // baseUrl: config.apiBaseUrl,
    ));

    GetIt.I.registerLazySingleton<AmbulanceCallRepository>(
        () => DefaultAmbulanceCallRepository(dio: dio));
    GetIt.I.registerLazySingleton<AbstractUserRepository>(
        () => UserRepository(dio: dio));
    GetIt.I.registerSingleton(dio);
    GetIt.I.registerLazySingleton<TeamRepository>(
        () => DefaultTeamRepository(dio: dio));
    GetIt.I.registerLazySingleton<AbstractLocationRepository>(
        () => LocationRepository(dio: dio));
    GetIt.I.registerLazySingleton<AbstractVersionRepository>(
        () => VersionRepository(dio: dio));
    GetIt.I.registerLazySingleton<AbstractServicesRepository>(
        () => ServicesRepository(dio: dio));
    GetIt.I.registerLazySingleton<ClinicRepository>(
        () => DefaultClinicRepository(dio: dio));
  }
}
