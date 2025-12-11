import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/services/app_setup.dart';
import 'core/services/auth_service.dart';
import 'core/services/api_service.dart';
import 'ui/screens/splash_screen.dart';
import 'ui/providers/food_seller_provider.dart';
import 'ui/screens/farm_products_management_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppSetup.initialize();

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => FoodSellerProvider()),
        Provider(create: (_) => AuthService()),
        Provider(create: (_) => ApiService()),
      ],
      child: VidiaSpotFoodSellerApp(),
    ),
  );
}

class VidiaSpotFoodSellerApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VidiaSpot Food Seller',
      theme: ThemeData(
        primarySwatch: Colors.orange,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: SplashScreen(),
      routes: {
        '/farm-products': (context) => FarmProductsManagementScreen(),
      },
      debugShowCheckedModeBanner: false,
    );
  }
}