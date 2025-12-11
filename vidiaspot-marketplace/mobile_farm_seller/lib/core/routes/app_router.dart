// lib/core/routes/app_router.dart
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../ui/screens/splash_screen.dart';
import '../ui/screens/auth/login_screen.dart';
import '../ui/screens/auth/register_screen.dart';
import '../ui/screens/dashboard/dashboard_screen.dart';
import '../ui/screens/products/products_management_screen.dart';
import '../ui/screens/products/add_edit_product_screen.dart';
import '../ui/screens/analytics/analytics_screen.dart';
import '../ui/screens/orders/orders_screen.dart';
import '../ui/screens/profile/profile_screen.dart';
import '../ui/screens/settings/settings_screen.dart';
import '../ui/screens/farm_profile/farm_profile_screen.dart';
import '../ui/screens/communications/communications_screen.dart';
import '../ui/screens/reports/reports_screen.dart';

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
      path: '/dashboard',
      builder: (context, state) => DashboardScreen(),
    ),
    GoRoute(
      path: '/products',
      builder: (context, state) => ProductsManagementScreen(),
    ),
    GoRoute(
      path: '/products/add',
      builder: (context, state) => AddEditProductScreen(),
    ),
    GoRoute(
      path: '/products/edit/:id',
      builder: (context, state) => AddEditProductScreen(productId: state.pathParameters['id']!),
    ),
    GoRoute(
      path: '/analytics',
      builder: (context, state) => AnalyticsScreen(),
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
      path: '/farm-profile',
      builder: (context, state) => FarmProfileScreen(),
    ),
    GoRoute(
      path: '/communications',
      builder: (context, state) => CommunicationsScreen(),
    ),
    GoRoute(
      path: '/reports',
      builder: (context, state) => ReportsScreen(),
    ),
    GoRoute(
      path: '/settings',
      builder: (context, state) => SettingsScreen(),
    ),
  ],
);