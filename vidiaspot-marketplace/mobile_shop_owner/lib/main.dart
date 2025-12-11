import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/services/app_setup.dart';
import 'core/services/auth_service.dart';
import 'core/services/api_service.dart';
import 'ui/screens/splash_screen.dart';
import 'ui/providers/shop_owner_provider.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppSetup.initialize();

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => ShopOwnerProvider()),
        Provider(create: (_) => AuthService()),
        Provider(create: (_) => ApiService()),
      ],
      child: VidiaSpotShopOwnerApp(),
    ),
  );
}

class VidiaSpotShopOwnerApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VidiaSpot Shop Owner',
      theme: ThemeData(
        primarySwatch: Colors.blue,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: SplashScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}