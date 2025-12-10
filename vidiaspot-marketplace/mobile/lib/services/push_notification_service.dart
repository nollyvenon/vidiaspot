// lib/services/push_notification_service.dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';

class PushNotificationService {
  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;

  // Request permission for notifications
  Future<bool?> requestNotificationPermission() async {
    NotificationSettings settings = await _firebaseMessaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
      announcement: false,
    );
    
    return settings.authorizationStatus == AuthorizationStatus.authorized ||
           settings.authorizationStatus == AuthorizationStatus.provisional;
  }

  // Get device token
  Future<String?> getToken() async {
    return await _firebaseMessaging.getToken();
  }

  // Subscribe to topic for price alerts
  Future<void> subscribeToPriceAlerts(String cryptoSymbol) async {
    await _firebaseMessaging.subscribeToTopic('price_alerts_$cryptoSymbol');
  }

  // Unsubscribe from topic
  Future<void> unsubscribeFromPriceAlerts(String cryptoSymbol) async {
    await _firebaseMessaging.unsubscribeFromTopic('price_alerts_$cryptoSymbol');
  }

  // Subscribe to all price alerts
  Future<void> subscribeToAllPriceAlerts() async {
    // Subscribe to various crypto price alert topics
    const List<String> cryptoSymbols = ['BTC', 'ETH', 'USDT', 'USDC', 'BNB', 'XRP', 'ADA'];
    for (String symbol in cryptoSymbols) {
      await _firebaseMessaging.subscribeToTopic('price_alerts_$symbol');
    }
  }

  // Initialize push notifications
  Future<void> initialize() async {
    // Request permission
    bool? permissionGranted = await requestNotificationPermission();
    
    if (permissionGranted == true) {
      // Get token
      String? token = await getToken();
      print('FCM Token: $token');
      
      // Subscribe to price alerts
      await subscribeToAllPriceAlerts();
      
      // Handle foreground messages
      FirebaseMessaging.onMessage.listen((RemoteMessage message) {
        print('Foreground message received: ${message.notification?.title}');
        _showNotification(message);
      });
      
      // Handle background messages
      FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
        print('Background message opened: ${message.notification?.title}');
        // Handle navigation to relevant screen based on message data
        _handleNotificationTap(message);
      });
    }
  }

  void _showNotification(RemoteMessage message) {
    // Show local notification when app is in foreground
    if (message.notification != null) {
      // Here we could use a plugin like flutter_local_notifications
      // to show a local notification while the app is in foreground
      print('Notification received: ${message.notification!.title} - ${message.notification!.body}');
    }
  }

  void _handleNotificationTap(RemoteMessage message) {
    // Handle navigation based on notification data
    if (message.data['type'] == 'price_alert') {
      // Navigate to price chart or trading screen
      String cryptoSymbol = message.data['symbol'] ?? '';
      String direction = message.data['direction'] ?? '';
      double price = double.tryParse(message.data['price'] ?? '0.0') ?? 0.0;
      
      print('Price alert tapped: $cryptoSymbol $direction at $price');
      // Implement navigation to relevant screen
    } else if (message.data['type'] == 'trade_update') {
      // Navigate to trade details
      String tradeId = message.data['trade_id'] ?? '';
      print('Trade update: $tradeId');
    }
  }

  // Configure notification settings
  void configureNotificationSettings() {
    _firebaseMessaging.setForegroundNotificationPresentationOptions(
      alert: true,
      badge: true,
      sound: true,
    );
  }

  // Set up message handlers
  void setupMessageHandlers() {
    // Handle messages when app is terminated
    FirebaseMessaging.onBackgroundMessage(_handleBackgroundMessage);
  }

  // Background message handler
  static Future<void> _handleBackgroundMessage(RemoteMessage message) async {
    print('Handling background message: ${message.notification?.title}');
    // Handle background message processing
    // This could include updating local database, showing local notifications, etc.
  }
}