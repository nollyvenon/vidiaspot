import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/services/app_setup.dart';
import 'core/services/auth_service.dart';
import 'core/services/api_service.dart';
import 'ui/providers/food_buyer_provider.dart';
import 'core/routes/app_router.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppSetup.initialize();

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => FoodBuyerProvider()),
        Provider(create: (_) => AuthService()),
        Provider(create: (_) => ApiService()),
      ],
      child: VidiaSpotFoodBuyerApp(),
    ),
  );
}

class VidiaSpotFoodBuyerApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'VidiaSpot Food Buyer',
      theme: ThemeData(
        primarySwatch: Colors.green,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      routerConfig: appRouter,
      debugShowCheckedModeBanner: false,
    );
  }
}