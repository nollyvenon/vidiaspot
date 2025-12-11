import 'package:firebase_core/firebase_core.dart';

class AppSetup {
  static Future<void> initialize() async {
    // Initialize any app-wide services
    await Firebase.initializeApp();
  }
}