import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/services/auth_service.dart';
import '../ui/providers/food_seller_provider.dart';
import '../core/services/api_service.dart';
import 'package:syncfusion_flutter_charts/charts.dart';
import 'orders_screen.dart';
import 'menu_management_screen.dart';
import 'analytics_screen.dart';
import 'restaurant_profile_screen.dart';
import 'farm_products_management_screen.dart';

class SellerDashboard extends StatefulWidget {
  @override
  _SellerDashboardState createState() => _SellerDashboardState();
}

class _SellerDashboardState extends State<SellerDashboard> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    DashboardScreen(),
    OrdersScreen(),
    MenuManagementScreen(),
    FarmProductsManagementScreen(),
    AnalyticsScreen(),
    RestaurantProfileScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _loadDashboardData();
  }

  Future<void> _loadDashboardData() async {
    final apiService = Provider.of<ApiService>(context, listen: false);
    final foodSellerProvider = Provider.of<FoodSellerProvider>(context, listen: false);
    final authService = Provider.of<AuthService>(context, listen: false);
    
    if (authService.token != null) {
      foodSellerProvider.setLoading(true);
      
      // Load restaurant data
      final restaurantData = await apiService.getRestaurantData(authService.token!);
      if (restaurantData != null) {
        // In a real app, you would convert the map to a RestaurantData object
      }
      
      // Load analytics data
      final analyticsData = await apiService.getFoodAnalytics(authService.token!);
      if (analyticsData != null) {
        // In a real app, you would convert the map to an AnalyticsData object
      }
      
      // Load recent orders
      final ordersData = await apiService.getRecentOrders(authService.token!);
      if (ordersData != null) {
        // In a real app, you would convert the list to Order objects
      }
      
      // Load menu items
      final menuData = await apiService.getMenuItems(authService.token!);
      if (menuData != null) {
        // In a real app, you would convert the list to MenuItem objects
      }
      
      foodSellerProvider.setLoading(false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Restaurant Dashboard'),
        backgroundColor: Colors.orange[400],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.notifications),
            onPressed: () {
              // Notifications functionality
            },
          ),
          PopupMenuButton(
            icon: Icon(Icons.account_circle),
            itemBuilder: (context) => [
              PopupMenuItem(
                value: 'profile',
                child: Text('Profile'),
              ),
              PopupMenuItem(
                value: 'settings',
                child: Text('Settings'),
              ),
              PopupMenuItem(
                value: 'logout',
                child: Text('Logout'),
              ),
            ],
            onSelected: (value) {
              if (value == 'logout') {
                _handleLogout();
              }
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
        type: BottomNavigationBarType.shifting, // Changed to shifting to handle more items better
        items: [
          BottomNavigationBarItem(
            icon: Icon(Icons.dashboard),
            label: 'Dashboard',
            backgroundColor: Colors.orange[400],
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.shopping_bag),
            label: 'Orders',
            backgroundColor: Colors.orange[400],
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.restaurant_menu),
            label: 'Menu',
            backgroundColor: Colors.orange[400],
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.local_florist),
            label: 'Farm',
            backgroundColor: Colors.green[600], // Different color for farm products
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.bar_chart),
            label: 'Analytics',
            backgroundColor: Colors.orange[400],
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.store),
            label: 'Profile',
            backgroundColor: Colors.orange[400],
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

class DashboardScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final foodSellerProvider = Provider.of<FoodSellerProvider>(context);
    
    if (foodSellerProvider.isLoading) {
      return Center(child: CircularProgressIndicator());
    }

    return SingleChildScrollView(
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Welcome message
            Text(
              'Welcome back!',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            Text(
              'Here\'s what\'s happening with your restaurant today.',
              style: TextStyle(
                fontSize: 16,
                color: Colors.grey[600],
              ),
            ),
            SizedBox(height: 20),
            
            // Key metrics
            Wrap(
              spacing: 16,
              runSpacing: 16,
              children: [
                _buildMetricCard(
                  context,
                  'Total Revenue',
                  '\$${foodSellerProvider.totalRevenue.toStringAsFixed(2)}',
                  Icons.attach_money,
                  Colors.green,
                ),
                _buildMetricCard(
                  context,
                  'Total Orders',
                  foodSellerProvider.totalOrders.toString(),
                  Icons.shopping_bag,
                  Colors.blue,
                ),
                _buildMetricCard(
                  context,
                  'Menu Items',
                  foodSellerProvider.totalMenuItems.toString(),
                  Icons.restaurant_menu,
                  Colors.orange,
                ),
                _buildMetricCard(
                  context,
                  'Farm Products',
                  foodSellerProvider.totalFarmProducts.toString(),
                  Icons.local_florist,
                  Colors.lightGreen,
                ),
              ],
            ),
            SizedBox(height: 20),

            // Additional metrics for farm products
            Container(
              padding: EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey[100],
                borderRadius: BorderRadius.circular(10),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  _buildSmallMetricCard(
                    'Organic Items',
                    foodSellerProvider.organicFarmProducts.length.toString(),
                    Icons.eco,
                    Colors.green,
                  ),
                  _buildSmallMetricCard(
                    'Non-Organic',
                    foodSellerProvider.nonOrganicFarmProducts.length.toString(),
                    Icons.grass,
                    Colors.brown,
                  ),
                ],
              ),
            ),
            SizedBox(height: 20),
            
            // Recent Orders
            Text(
              'Recent Orders',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            Container(
              height: 200,
              child: foodSellerProvider.recentOrders.isEmpty
                  ? Center(child: Text('No recent orders'))
                  : ListView.builder(
                      scrollDirection: Axis.horizontal,
                      itemCount: foodSellerProvider.recentOrders.length > 5 ? 5 : foodSellerProvider.recentOrders.length,
                      itemBuilder: (context, index) {
                        var order = foodSellerProvider.recentOrders[index];
                        return _buildOrderCard(
                          orderId: order.orderId,
                          customer: order.customerName,
                          amount: order.totalAmount.toStringAsFixed(2),
                          status: order.status,
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMetricCard(
    BuildContext context,
    String title,
    String value,
    IconData icon,
    Color color,
  ) {
    return Expanded(
      child: Card(
        elevation: 4,
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(icon, color: color),
                  ),
                  SizedBox(width: 10),
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey[600],
                    ),
                  ),
                ],
              ),
              SizedBox(height: 10),
              Text(
                value,
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSmallMetricCard(String title, String value, IconData icon, Color color) {
    return Column(
      children: [
        Icon(icon, color: color, size: 30),
        SizedBox(height: 8),
        Text(
          value,
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        Text(
          title,
          style: TextStyle(
            fontSize: 12,
            color: Colors.grey[600],
          ),
        ),
      ],
    );
  }

  Widget _buildOrderCard({
    required String orderId,
    required String customer,
    required String amount,
    required String status,
  }) {
    Color statusColor = status == 'pending'
        ? Colors.orange
        : status == 'preparing'
            ? Colors.blue
            : status == 'out_for_delivery'
                ? Colors.purple
                : Colors.green;

    return Container(
      width: 250,
      margin: EdgeInsets.only(right: 10),
      child: Card(
        elevation: 3,
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    orderId,
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                  Container(
                    padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      status,
                      style: TextStyle(
                        color: statusColor,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 10),
              Text(
                customer,
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey[700],
                ),
              ),
              SizedBox(height: 10),
              Text(
                '\$$amount',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class LoginScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Login'),
      ),
      body: Center(
        child: Text('Login Screen'),
      ),
    );
  }
}