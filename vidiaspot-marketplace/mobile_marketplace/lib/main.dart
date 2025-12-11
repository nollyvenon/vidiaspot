import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'services/translation_service.dart';
import 'services/settings_service.dart';
import 'services/cache_service.dart';
import 'screens/home_screen.dart';
import 'screens/search_screen.dart';
import 'screens/profile_screen.dart';
import 'screens/post_ad_screen.dart';
import 'screens/messages_screen.dart';

// Crypto P2P screens
import 'screens/crypto_p2p/crypto_p2p_home_screen.dart';
import 'screens/crypto_p2p/create_crypto_listing_screen.dart';
import 'screens/crypto_p2p/initiate_crypto_trade_screen.dart';
import 'screens/crypto_p2p/crypto_trade_details_screen.dart';
// Crypto P2P Advanced screens
import 'screens/crypto_p2p/advanced/trading_pairs_screen.dart';
import 'screens/crypto_p2p/advanced/payment_methods_screen.dart';
import 'screens/crypto_p2p/advanced/verification_status_screen.dart';
// E-commerce screens
import 'screens/ecommerce/ecommerce_home_screen.dart';
// Food Vending screens
import 'screens/food_vending/food_vending_home_screen.dart';
// Farm Products screens
import 'screens/farm_products/farm_products_home_screen.dart';
// Logistics screens
import 'screens/logistics/logistics_home_screen.dart';
// Marketplace modules screen
import 'screens/marketplace_modules_screen.dart';
// IoT screens
import 'screens/iot/iot_home_screen.dart';
// NFT screens
import 'screens/nft/nft_marketplace_home_screen.dart';
import 'models/crypto_p2p/crypto_listing_model.dart';
import 'models/crypto_p2p/crypto_trade_model.dart';

import 'screens/comprehensive_messaging_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize services
  await SettingsService().init();
  await CacheService().init();
  // FirebaseService will be initialized in native app only

  runApp(const VidiaSpotApp());
}

class VidiaSpotApp extends StatelessWidget {
  const VidiaSpotApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VidiaSpot Marketplace',
      theme: ThemeData(
        primarySwatch: Colors.blue,
        visualDensity: VisualDensity.adaptivePlatformDensity,
        useMaterial3: true,
      ),
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      supportedLocales: const [
        Locale('en', 'US'), // English
        Locale('fr', 'FR'), // French
        Locale('pt', 'PT'), // Portuguese
        Locale('ar', 'AR'), // Arabic
        Locale('es', 'ES'), // Spanish
        Locale('de', 'DE'), // German
        Locale('zh', 'CN'), // Chinese
        Locale('yo', 'NG'), // Yoruba
        Locale('ig', 'NG'), // Igbo
        Locale('ha', 'NG'), // Hausa
      ],
      home: const MainScreen(),
      routes: {
        '/crypto-p2p': (context) => const CryptoP2PHomeScreen(),
        '/create-listing': (context) => const CreateCryptoListingScreen(),
        '/initiate-trade': (context) {
          final args = ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
          return InitiateCryptoTradeScreen(listing: args['listing'] as CryptoListing);
        },
        '/trade-details': (context) {
          final args = ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
          return CryptoTradeDetailsScreen(trade: args['trade'] as CryptoTrade);
        },
        '/trading-pairs': (context) => const TradingPairsScreen(),
        '/payment-methods': (context) => const PaymentMethodsScreen(),
        '/verification-status': (context) => const VerificationStatusScreen(),
        '/ecommerce': (context) => const EcommerceHomeScreen(),
        '/food-vending': (context) => const FoodVendingHomeScreen(),
        '/farm-products': (context) => const FarmProductsHomeScreen(),
        '/logistics': (context) => const LogisticsHomeScreen(),
        '/marketplace-modules': (context) => const MarketplaceModulesScreen(),
        '/iot': (context) => const IoTHomeScreen(),
      },
      debugShowCheckedModeBanner: false,
    );
  }
}

class MainScreen extends StatefulWidget {
  const MainScreen({Key? key}) : super(key: key);

  @override
  _MainScreenState createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const HomeScreen(),
    const SearchScreen(),
    const PostAdScreen(),
    const ComprehensiveMessagingScreen(), // Changed to comprehensive messaging screen
    const ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _selectedIndex,
        children: _screens,
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.pushNamed(context, '/marketplace-modules');
        },
        child: const Icon(Icons.apps),
        backgroundColor: Colors.blue,
      ),
      bottomNavigationBar: BottomNavigationBar(
        items: const <BottomNavigationBarItem>[
          BottomNavigationBarItem(
            icon: Icon(Icons.home),
            label: 'Home',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.search),
            label: 'Search',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.add_circle),
            label: 'Post',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.message),
            label: 'Messages',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            label: 'Profile',
          ),
        ],
        currentIndex: _selectedIndex,
        selectedItemColor: Colors.blue,
        onTap: (index) {
          setState(() {
            _selectedIndex = index;
          });
        },
      ),
    );
  }
}