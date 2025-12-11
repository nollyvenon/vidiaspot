import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/services/auth_service.dart';
import '../providers/shop_owner_provider.dart';
import '../../core/services/api_service.dart';
import 'package:syncfusion_flutter_charts/charts.dart';
import 'orders_screen.dart';
import 'products_screen.dart';
import 'analytics_screen.dart';
import 'inventory_screen.dart';
import 'customer_management_screen.dart';
import 'store_settings_screen.dart';
import 'login_screen.dart';

class ShopOwnerDashboard extends StatefulWidget {
  @override
  _ShopOwnerDashboardState createState() => _ShopOwnerDashboardState();
}

class _ShopOwnerDashboardState extends State<ShopOwnerDashboard> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    DashboardScreen(),
    OrdersScreen(),
    ProductsScreen(),
    InventoryScreen(),
    AnalyticsScreen(),
    CustomerManagementScreen(),
    StoreSettingsScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _loadDashboardData();
  }

  Future<void> _loadDashboardData() async {
    final apiService = Provider.of<ApiService>(context, listen: false);
    final shopOwnerProvider = Provider.of<ShopOwnerProvider>(context, listen: false);
    final authService = Provider.of<AuthService>(context, listen: false);

    if (authService.token != null) {
      shopOwnerProvider.setLoading(true);

      // Load shop data
      final shopData = await apiService.getShopData(authService.token!);
      if (shopData != null) {
        // In a real app, you would convert the map to a ShopData object
      }

      // Load analytics data
      final analyticsData = await apiService.getShopAnalytics(authService.token!);
      if (analyticsData != null) {
        // In a real app, you would convert the map to an AnalyticsData object
      }

      // Load recent orders
      final ordersData = await apiService.getRecentOrders(authService.token!);
      if (ordersData != null) {
        // In a real app, you would convert the list to Order objects
      }

      // Load products
      final productsData = await apiService.getProducts(authService.token!);
      if (productsData != null) {
        // In a real app, you would convert the list to Product objects
      }

      shopOwnerProvider.setLoading(false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Shop Dashboard'),
        backgroundColor: Colors.blue,
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
        items: [
          BottomNavigationBarItem(
            icon: Icon(Icons.dashboard),
            label: 'Dashboard',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.shopping_bag),
            label: 'Orders',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.inventory),
            label: 'Products',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.inventory_2),
            label: 'Inventory',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.bar_chart),
            label: 'Analytics',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.people),
            label: 'Customers',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.settings),
            label: 'Settings',
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
    final shopOwnerProvider = Provider.of<ShopOwnerProvider>(context);
    
    if (shopOwnerProvider.isLoading) {
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
              'Welcome back, Shop Owner!',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            Text(
              'Here\'s what\'s happening with your store today.',
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
                  '\$${shopOwnerProvider.totalRevenue.toStringAsFixed(2)}',
                  Icons.attach_money,
                  Colors.green,
                ),
                _buildMetricCard(
                  context,
                  'Total Orders',
                  shopOwnerProvider.totalOrders.toString(),
                  Icons.shopping_bag,
                  Colors.blue,
                ),
                _buildMetricCard(
                  context,
                  'Products',
                  shopOwnerProvider.totalProducts.toString(),
                  Icons.inventory,
                  Colors.orange,
                ),
                _buildMetricCard(
                  context,
                  'Pending Orders',
                  shopOwnerProvider.pendingOrders.toString(),
                  Icons.pending_actions,
                  Colors.amber,
                ),
              ],
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
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                itemCount: 5, // Show 5 recent orders
                itemBuilder: (context, index) {
                  return _buildOrderCard(
                    orderId: '#ORD${1000 + index}',
                    customer: 'Customer ${index + 1}',
                    amount: (99.99 + index * 10).toStringAsFixed(2),
                    status: index % 3 == 0 ? 'Pending' : index % 3 == 1 ? 'Shipped' : 'Delivered',
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

  Widget _buildOrderCard({
    required String orderId,
    required String customer,
    required String amount,
    required String status,
  }) {
    Color statusColor = status == 'Pending' 
        ? Colors.orange 
        : status == 'Shipped' 
            ? Colors.blue 
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

