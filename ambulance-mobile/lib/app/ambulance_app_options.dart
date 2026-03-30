import 'package:dio/dio.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:talker_bloc_logger/talker_bloc_logger_settings.dart';
import 'package:talker_dio_logger/talker_dio_logger_settings.dart';

class AmbulanceAppOptions {
  final String baseUrl;
  final String apiPath;
  final TalkerBlocLoggerSettings talkerBlocLoggerSettings;
  final TalkerDioLoggerSettings talkerDioLoggerSettings;
  final Map<String, dynamic>? dioHeaders;
  final FirebaseOptions? firebaseOptions;
  final String localeCode;

  const AmbulanceAppOptions({
    // TODO: prod/dev variables on launch
    // this.baseUrl = 'https://dev.reset-med.ru/app',
    this.baseUrl = 'https://reset-m.ru/app',
    this.apiPath = '/api/',
    this.talkerBlocLoggerSettings = const TalkerBlocLoggerSettings(
      printStateFullData: false,
      printEventFullData: false,
    ),
    this.talkerDioLoggerSettings = const TalkerDioLoggerSettings(
      printResponseData: false,
    ),
    this.dioHeaders = const {
      Headers.acceptHeader: 'application/json',
      Headers.contentTypeHeader: 'application/json; charset=utf-8',
    },
    this.firebaseOptions,
    this.localeCode = 'ru_RU',
  });

  String get apiBaseUrl => '$baseUrl$apiPath';
}
