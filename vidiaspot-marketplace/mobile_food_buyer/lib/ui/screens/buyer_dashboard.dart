import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/services/auth_service.dart';
import '../ui/providers/food_buyer_provider.dart';
import '../core/services/api_service.dart';
import 'dashboard_screen.dart';
import 'restaurant_list_screen.dart';
import 'cart_screen.dart';
import 'order_history_screen.dart';
import 'profile_screen.dart';
import 'search_screen.dart';

class BuyerDashboard extends StatefulWidget {
  @override
  _BuyerDashboardState createState() => _BuyerDashboardState();
}

class _BuyerDashboardState extends State<BuyerDashboard> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    DashboardScreen(),
    SearchScreen(),
    CartScreen(),
    OrderHistoryScreen(),
    ProfileScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _loadDashboardData();
  }

  Future<void> _loadDashboardData() async {
    final apiService = Provider.of<ApiService>(context, listen: false);
    final foodBuyerProvider = Provider.of<FoodBuyerProvider>(context, listen: false);
    final authService = Provider.of<AuthService>(context, listen: false);

    if (authService.token != null) {
      foodBuyerProvider.setLoading(true);

      // Load user profile
      final profileData = await apiService.getUserProfile(authService.token!);
      if (profileData != null) {
        // In a real app, you would convert the map to a UserProfile object
      }

      // Load user orders
      final ordersData = await apiService.getUserOrders(authService.token!);
      if (ordersData != null) {
        // In a real app, you would convert the list to Order objects
      }

      foodBuyerProvider.setLoading(false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('VidiaSpot Food Buyer'),
        backgroundColor: Colors.green[600],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.notifications),
            onPressed: () {
              // Notifications functionality
            },
          ),
          IconButton(
            icon: Icon(Icons.location_on),
            onPressed: () {
              // Location selection functionality
            },
          ),
        ],
      ),
      body: _screens[_currentIndex],
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        currentIndex: _currentIndex,
        onTap: (index) {
          setState(() {
            _currentIndex = index;
          });
        },
        items: [
          BottomNavigationBarItem(
            icon: Icon(Icons.home),
            label: 'Home',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.search),
            label: 'Search',
          ),
          BottomNavigationBarItem(
            icon: Badge(
              badgeContent: Text(
                Provider.of<FoodBuyerProvider>(context).cartItemCount.toString(),
                style: TextStyle(color: Colors.white),
              ),
              child: Icon(Icons.shopping_cart),
            ),
            label: 'Cart',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.history),
            label: 'Orders',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            label: 'Profile',
          ),
        ],
      ),
    );
  }

  void _handleLogout() async {
    final authService = Provider.of<AuthService>(context, listen: false);
    await authService.logout();

    Navigator.of(context).pushReplacement(
      MaterialPageRoute(builder: (context) => LoginScreen()),
    );
  }
}