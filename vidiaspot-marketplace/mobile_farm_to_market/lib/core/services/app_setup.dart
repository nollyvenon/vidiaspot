import 'package:firebase_core/firebase_core.dart';

class AppSetup {
  static Future<void> initialize() async {
    await Firebase.initializeApp();
  }
}