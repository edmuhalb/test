import 'dart:async';

import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';

import 'app/ambulance_app.dart';
import 'app/ambulance_app_options.dart';
import 'app/ambulance_app_initializer.dart';

void main() async {
  runZonedGuarded(() async {
    WidgetsFlutterBinding.ensureInitialized();
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    SystemChrome.setSystemUIOverlayStyle(
      SystemUiOverlayStyle(
        systemNavigationBarColor: Colors.transparent,
        systemNavigationBarIconBrightness: Brightness.light,
      ),
    );

    await AmbulanceAppInitializer.init(
      const AmbulanceAppOptions(),
    );

    runApp(const AmbulanceApp());
  }, (exception, stack) {
    GetIt.I<Talker>().handle(exception, stack);
    FirebaseCrashlytics.instance.recordError(exception, stack);
  });
}
