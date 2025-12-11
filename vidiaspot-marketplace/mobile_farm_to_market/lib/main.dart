import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/services/app_setup.dart';
import 'core/services/auth_service.dart';
import 'core/services/api_service.dart';
import 'ui/screens/splash_screen.dart';
import 'ui/providers/farm_provider.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppSetup.initialize();

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => FarmProvider()),
        Provider(create: (_) => AuthService()),
        Provider(create: (_) => ApiService()),
      ],
      child: VidiaSpotFarmToMarketApp(),
    ),
  );
}

class VidiaSpotFarmToMarketApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VidiaSpot Farm To Market',
      theme: ThemeData(
        primarySwatch: Colors.green,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: SplashScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}