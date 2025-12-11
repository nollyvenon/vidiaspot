// lib/core/routes/app_router.dart
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../ui/screens/splash_screen.dart';
import '../ui/screens/auth/login_screen.dart';
import '../ui/screens/auth/register_screen.dart';
import '../ui/screens/home/home_screen.dart';
import '../ui/screens/farm_explorer/farm_explorer_screen.dart';
import '../ui/screens/farm_explorer/farm_product_detail_screen.dart';
import '../ui/screens/farm_explorer/farm_products_screen.dart';
import '../ui/screens/farm_explorer/farm_profile_screen.dart';
import '../ui/screens/favorites/favorites_screen.dart';
import '../ui/screens/cart/cart_screen.dart';
import '../ui/screens/orders/orders_screen.dart';
import '../ui/screens/profile/profile_screen.dart';
import '../ui/screens/search/farm_search_screen.dart';
import '../ui/screens/settings/settings_screen.dart';

final GoRouter appRouter = GoRouter(
  initialLocation: '/',
  routes: [
    GoRoute(
      path: '/',
      builder: (context, state) => SplashScreen(),
    ),
    GoRoute(
      path: '/login',
      builder: (context, state) => LoginScreen(),
    ),
    GoRoute(
      path: '/register',
      builder: (context, state) => RegisterScreen(),
    ),
    GoRoute(
      path: '/home',
      builder: (context, state) => HomeScreen(),
    ),
    GoRoute(
      path: '/farm-explorer',
      builder: (context, state) => FarmExplorerScreen(),
    ),
    GoRoute(
      path: '/farm-products',
      builder: (context, state) => FarmProductsScreen(),
    ),
    GoRoute(
      path: '/farm-products/:id',
      builder: (context, state) => FarmProductDetailScreen(productId: state.pathParameters['id']!),
    ),
    GoRoute(
      path: '/farm-profile/:id',
      builder: (context, state) => FarmProfileScreen(farmId: state.pathParameters['id']!),
    ),
    GoRoute(
      path: '/favorites',
      builder: (context, state) => FavoritesScreen(),
    ),
    GoRoute(
      path: '/cart',
      builder: (context, state) => CartScreen(),
    ),
    GoRoute(
      path: '/orders',
      builder: (context, state) => OrdersScreen(),
    ),
    GoRoute(
      path: '/profile',
      builder: (context, state) => ProfileScreen(),
    ),
    GoRoute(
      path: '/search',
      builder: (context, state) => FarmSearchScreen(),
    ),
    GoRoute(
      path: '/settings',
      builder: (context, state) => SettingsScreen(),
    ),
  ],
);