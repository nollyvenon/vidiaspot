import 'package:flutter/material.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:provider/provider.dart';
import 'core/services/app_setup.dart';
import 'core/services/location_service.dart';
import 'core/services/notification_service.dart';
import 'core/services/auth_service.dart';
import 'ui/screens/delivery_dashboard_screen.dart';
import 'ui/providers/app_state_provider.dart';
import 'services/logistics/route_optimization_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  await AppSetup.initialize();

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AppStateProvider()),
        ChangeNotifierProvider(create: (_) => RouteOptimizationService()),
        Provider(create: (_) => LocationService()),
        Provider(create: (_) => NotificationService()),
        Provider(create: (_) => AuthService()),
      ],
      child: VidiaSpotDeliveryDriverApp(),
    ),
  );
}

class VidiaSpotDeliveryDriverApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VidiaSpot Delivery Driver',
      theme: ThemeData(
        primarySwatch: Colors.blue,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: DeliveryDashboardScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}