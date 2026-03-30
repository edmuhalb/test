import 'dart:async';
import 'dart:convert';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:ambulance/app/widgets/widgets.dart';


Future<void> handleMessage(RemoteMessage? message) async {
  final notification = message?.notification;

  if (notification == null) {
    return;
  }

  final notificationsPlugin = FlutterLocalNotificationsPlugin();

  Notification.showNotification(
    id: message.hashCode,
    title: '${notification.title}',
    body: '${notification.body}',
    notificationsPlugin: notificationsPlugin,
    payload: jsonEncode(message?.data),
  );
}

Future<void> handleBackgroundMessage(RemoteMessage? message) async {
  handleMessage(message);
}

Future<void> handleLocalMessage(RemoteMessage? message) async {
  handleMessage(message);
}

Future<void> initPushNotifications() async {
  await FirebaseMessaging.instance.setForegroundNotificationPresentationOptions(
    alert: true,
    badge: true,
    sound: true,
  );

  FirebaseMessaging.instance.getInitialMessage().then(handleBackgroundMessage);
  FirebaseMessaging.onMessageOpenedApp.listen(handleBackgroundMessage);
  FirebaseMessaging.onBackgroundMessage(handleBackgroundMessage);
  FirebaseMessaging.onMessage.listen(handleLocalMessage);
}

class FirebaseRepository {
  final _firebaseMessaging = FirebaseMessaging.instance;
  final _notification = FlutterLocalNotificationsPlugin();

  Future<void> initNotifications() async {
    await _firebaseMessaging.requestPermission();

    initPushNotifications();
    // Local natifications
    Notification.initialize(_notification);
  }
}
