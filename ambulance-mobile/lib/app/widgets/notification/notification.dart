import 'dart:async';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class Notification {
  static Future<void> initialize(
      FlutterLocalNotificationsPlugin notificationsPlugin) async {
    const setting = InitializationSettings(
      android: AndroidInitializationSettings('@drawable/ic_launcher'),
      iOS: DarwinInitializationSettings(
        requestAlertPermission: true,
        requestBadgePermission: true,
        requestSoundPermission: true,
      ),
    );
    await notificationsPlugin.initialize(setting);
  }

  static Future<void> showNotification({
    int id = 0,
    required String title,
    required String body,
    String? payload,
    required FlutterLocalNotificationsPlugin notificationsPlugin,
  }) async {
    const androidPlatformChanellSpecifies = AndroidNotificationDetails(
      'high_importance_chanell',
      'channelName',
      // playSound: true,
      // sound: RawResourceAndroidNotificationSound('notification'),
      importance: Importance.max,
      priority: Priority.high,
    );

    const notificationDetails = NotificationDetails(
      android: androidPlatformChanellSpecifies,
    );

    await notificationsPlugin.show(
      id,
      title,
      body,
      notificationDetails,
    );
  }
}
