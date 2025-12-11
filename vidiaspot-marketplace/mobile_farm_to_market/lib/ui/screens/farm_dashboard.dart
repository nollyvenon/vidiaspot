import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/services/auth_service.dart';
import '../ui/providers/farm_provider.dart';
import '../core/services/api_service.dart';
import 'package:syncfusion_flutter_charts/charts.dart';
import 'products_screen.dart';
import 'orders_screen.dart';
import 'analytics_screen.dart';
import 'farm_profile_screen.dart';

class FarmDashboard extends StatefulWidget {
  @override
  _FarmDashboardState createState() => _FarmDashboardState();
}

class _FarmDashboardState extends State<FarmDashboard> {
  int _currentIndex = 0;
  
  final List<Widget> _screens = [
    DashboardScreen(),
    ProductsScreen(),
    OrdersScreen(),
    AnalyticsScreen(),
    FarmProfileScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _loadDashboardData();
  }

  Future<void> _loadDashboardData() async {
    final apiService = Provider.of<ApiService>(context, listen: false);
    final farmProvider = Provider.of<FarmProvider>(context, listen: false);
    final authService = Provider.of<AuthService>(context, listen: false);
    
    if (authService.token != null) {
      farmProvider.setLoading(true);
      
      // Load farm data
      final farmData = await apiService.getFarmData(authService.token!);
      if (farmData != null) {
        // In a real app, you would convert the map to a FarmData object
      }
      
      // Load farm products
      final productsData = await apiService.getFarmProducts(authService.token!);
      if (productsData != null) {
        // In a real app, you would convert the list to FarmProduct objects
      }
      
      // Load pending orders
      final ordersData = await apiService.getPendingOrders(authService.token!);
      if (ordersData != null) {
        // In a real app, you would convert the list to FarmOrder objects
      }
      
      // Load analytics data
      final analyticsData = await apiService.getFarmAnalytics(authService.token!);
      if (analyticsData != null) {
        // In a real app, you would convert the map to a FarmAnalytics object
      }
      
      farmProvider.setLoading(false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Farm Dashboard'),
        backgroundColor: Colors.green[400],
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
            icon: Icon(Icons.inventory),
            label: 'Products',
          ),
          BottomNavigationBarItem(
            icon: Badge(
              badgeContent: Text(
                Provider.of<FarmProvider>(context).pendingOrderCount.toString(),
              ),
              child: Icon(Icons.shopping_bag),
            ),
            label: 'Orders',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.bar_chart),
            label: 'Analytics',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.storefront),
            label: 'Farm',
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
    final farmProvider = Provider.of<FarmProvider>(context);
    
    if (farmProvider.isLoading) {
      return Center(child: CircularProgressIndicator());
    }

    return Padding(
      padding: EdgeInsets.all(16),
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Welcome message
            Text(
              'Welcome Back!',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            Text(
              'Here\'s what\'s happening with your farm today.',
              style: TextStyle(
                fontSize: 16,
                color: Colors.grey[600],
              ),
            ),
            SizedBox(height: 20),
            
            // Key metrics
            Wrap(
              spacing: 10,
              runSpacing: 10,
              children: [
                _buildMetricCard(
                  context,
                  'Total Revenue',
                  '\$${farmProvider.totalRevenue.toStringAsFixed(2)}',
                  Icons.attach_money,
                  Colors.green,
                ),
                _buildMetricCard(
                  context,
                  'Total Products',
                  farmProvider.totalProducts.toString(),
                  Icons.inventory,
                  Colors.blue,
                ),
                _buildMetricCard(
                  context,
                  'Pending Orders',
                  farmProvider.pendingOrderCount.toString(),
                  Icons.pending_actions,
                  Colors.orange,
                ),
                _buildMetricCard(
                  context,
                  'Completed Orders',
                  farmProvider.completedOrderCount.toString(),
                  Icons.check_circle,
                  Colors.teal,
                ),
              ],
            ),
            SizedBox(height: 20),
            
            // Quick actions
            Text(
              'Quick Actions',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            Wrap(
              spacing: 10,
              runSpacing: 10,
              children: [
                _buildQuickActionCard(
                  context,
                  'Add Product',
                  Icons.add_shopping_cart,
                  Colors.green[200]!,
                  () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => ProductFormScreen()),
                    );
                  },
                ),
                _buildQuickActionCard(
                  context,
                  'View Orders',
                  Icons.shopping_bag,
                  Colors.blue[200]!,
                  () {
                    // Navigate to orders screen
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => OrdersScreen()),
                    );
                  },
                ),
                _buildQuickActionCard(
                  context,
                  'Manage Farm',
                  Icons.storefront,
                  Colors.orange[200]!,
                  () {
                    // Navigate to farm profile
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => FarmProfileScreen()),
                    );
                  },
                ),
                _buildQuickActionCard(
                  context,
                  'Analytics',
                  Icons.bar_chart,
                  Colors.purple[200]!,
                  () {
                    // Navigate to analytics
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => AnalyticsScreen()),
                    );
                  },
                ),
              ],
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

  Widget _buildQuickActionCard(
    BuildContext context,
    String title,
    IconData icon,
    Color color,
    VoidCallback onTap,
  ) {
    return GestureDetector(
      onTap: onTap,
      child: Card(
        elevation: 4,
        child: Container(
          width: MediaQuery.of(context).size.width / 2 - 20,
          child: Padding(
            padding: EdgeInsets.all(16),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  width: 50,
                  height: 50,
                  decoration: BoxDecoration(
                    color: color,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(icon, color: Colors.white),
                ),
                SizedBox(height: 10),
                Text(
                  title,
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}