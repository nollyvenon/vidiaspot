import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/services/app_setup.dart';
import 'core/services/auth_service.dart';
import 'core/services/api_service.dart';
import 'ui/providers/farm_seller_provider.dart';
import 'core/routes/app_router.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppSetup.initialize();

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => FarmSellerProvider()),
        Provider(create: (_) => AuthService()),
        Provider(create: (_) => ApiService()),
      ],
      child: VidiaSpotFarmSellerApp(),
    ),
  );
}

class VidiaSpotFarmSellerApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'VidiaSpot Farm Seller',
      theme: ThemeData(
        primarySwatch: Colors.green,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      routerConfig: appRouter,
      debugShowCheckedModeBanner: false,
    );
  }
}