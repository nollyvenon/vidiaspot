import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'delivery_packages_screen.dart';
import 'delivery_route_screen.dart';
import 'delivery_messages_screen.dart';
import '../providers/app_state_provider.dart';
import '../services/delivery_management/delivery_service.dart';
import '../services/logistics/route_optimization_service.dart';
import '../services/delivery_management/communication_service.dart';

class DeliveryDashboardScreen extends StatefulWidget {
  @override
  _DeliveryDashboardScreenState createState() => _DeliveryDashboardScreenState();
}

class _DeliveryDashboardScreenState extends State<DeliveryDashboardScreen> {
  int _currentIndex = 0;
  
  final List<Widget> _screens = [
    DeliveryPackagesScreen(),
    DeliveryRouteScreen(),
    DeliveryMessagesScreen(),
    ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    final appState = Provider.of<AppStateProvider>(context);
    final deliveryService = Provider.of<DeliveryManagementService>(context);
    final communicationService = Provider.of<CommunicationService>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('Delivery Dashboard'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(appState.isOnline ? Icons.radio_button_on : Icons.radio_button_off),
            onPressed: () {
              appState.toggleOnlineStatus();
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(appState.isOnline ? 'Now Online' : 'Now Offline'),
                ),
              );
            },
          ),
        ],
      ),
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
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
            icon: Icon(Icons.local_shipping),
            label: 'Packages',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.route),
            label: 'Route',
          ),
          BottomNavigationBarItem(
            icon: Stack(
              children: [
                Icon(Icons.message),
                if (communicationService.getUnreadCount() > 0)
                  Positioned(
                    right: 0,
                    top: 0,
                    child: Container(
                      padding: EdgeInsets.all(2),
                      decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      constraints: BoxConstraints(
                        minWidth: 14,
                        minHeight: 14,
                      ),
                      child: Text(
                        '${communicationService.getUnreadCount()}',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 8,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  ),
              ],
            ),
            label: 'Messages',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            label: 'Profile',
          ),
        ],
      ),
    );
  }
}

class ProfileScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final appState = Provider.of<AppStateProvider>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('Driver Profile'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    CircleAvatar(
                      radius: 50,
                      child: Icon(Icons.person, size: 50),
                    ),
                    SizedBox(height: 16),
                    Text(
                      'Delivery Driver',
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                    ),
                    Text('Driver ID: DRV-${DateTime.now().millisecondsSinceEpoch}'),
                  ],
                ),
              ),
            ),
            
            SizedBox(height: 20),
            
            Text(
              'Today\'s Stats',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            SizedBox(height: 10),
            
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    _buildStatCard('Delivered', appState.completedDeliveries.toString(), Icons.check_circle),
                    _buildStatCard('Pending', appState.pendingDeliveries.toString(), Icons.pending),
                    _buildStatCard('Earnings', '\$${(appState.completedDeliveries * 5).toStringAsFixed(2)}', Icons.attach_money),
                  ],
                ),
              ),
            ),
            
            SizedBox(height: 20),
            
            Text(
              'Status',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            SizedBox(height: 10),
            
            Card(
              child: SwitchListTile(
                title: Text('Accepting Deliveries'),
                value: appState.isOnline,
                onChanged: (value) {
                  appState.toggleOnlineStatus();
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon) {
    return Column(
      children: [
        Icon(icon, size: 30),
        Text(title),
        Text(
          value,
          style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
        ),
      ],
    );
  }
}