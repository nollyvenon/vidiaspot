import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';

class FirebaseService {
  static final FirebaseService _instance = FirebaseService._internal();
  factory FirebaseService() => _instance;
  FirebaseService._internal();

  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;

  // Request notification permission
  Future<bool?> requestNotificationPermission() async {
    NotificationSettings settings = await _firebaseMessaging.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );
    return settings.authorizationStatus == AuthorizationStatus.authorized;
  }

  // Get FCM token
  Future<String?> getFcmToken() async {
    return await _firebaseMessaging.getToken();
  }

  // Subscribe to topic
  Future<void> subscribeToTopic(String topic) async {
    await _firebaseMessaging.subscribeToTopic(topic);
  }

  // Unsubscribe from topic
  Future<void> unsubscribeFromTopic(String topic) async {
    await _firebaseMessaging.unsubscribeFromTopic(topic);
  }

  // Configure message handling
  void configureMessaging(
    Function(RemoteMessage message)? onMessage,
    Function(RemoteMessage message)? onMessageOpenedApp,
  ) {
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      debugPrint('Foreground message received: ${message.notification?.title}');
      if (onMessage != null) {
        onMessage(message);
      }
    });

    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      debugPrint('Message opened app: ${message.notification?.title}');
      if (onMessageOpenedApp != null) {
        onMessageOpenedApp(message);
      }
    });
  }

  // Initialize Firebase service
  Future<void> initialize() async {
    // Request permission
    await requestNotificationPermission();

    // Get initial FCM token
    String? token = await getFcmToken();
    debugPrint('FCM Token: $token');

    // Handle token refresh
    FirebaseMessaging.onTokenRefresh.listen((token) {
      debugPrint('New FCM Token: $token');
      // Send token to server
    });
  }
}