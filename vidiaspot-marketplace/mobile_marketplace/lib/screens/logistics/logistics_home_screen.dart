// lib/screens/logistics/logistics_home_screen.dart
import 'package:flutter/material.dart';

class LogisticsHomeScreen extends StatefulWidget {
  const LogisticsHomeScreen({Key? key}) : super(key: key);

  @override
  _LogisticsHomeScreenState createState() => _LogisticsHomeScreenState();
}

class _LogisticsHomeScreenState extends State<LogisticsHomeScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Logistics & Supply Chain'),
        backgroundColor: Colors.teal,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Welcome message
              const Text(
                'Manage Your Supply Chain & Delivery',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),
              const Text(
                'Track shipments, manage inventory, and optimize logistics',
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey,
                ),
              ),
              const SizedBox(height: 20),

              // Quick stats
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.teal[50],
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: [
                    _buildStatCard('12', 'Active Shipments', Icons.local_shipping),
                    _buildStatCard('48', 'Delivered Today', Icons.done),
                    _buildStatCard('5', 'In Transit', Icons.directions),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              // Quick actions
              const Text(
                'Quick Actions',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _buildQuickAction(
                    icon: Icons.add_box,
                    label: 'Create Shipment',
                    color: Colors.teal,
                    onTap: () {
                      // Navigate to create shipment
                    },
                  ),
                  _buildQuickAction(
                    icon: Icons.track_changes,
                    label: 'Track Package',
                    color: Colors.blue,
                    onTap: () {
                      // Navigate to track package
                    },
                  ),
                  _buildQuickAction(
                    icon: Icons.route,
                    label: 'Route Plan',
                    color: Colors.orange,
                    onTap: () {
                      // Navigate to route planning
                    },
                  ),
                ],
              ),
              const SizedBox(height: 20),

              // Recent shipments
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Recent Shipments',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  TextButton(
                    onPressed: () {
                      // Navigate to all shipments
                    },
                    child: const Text('View All'),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              _buildShipmentList(),

              const SizedBox(height: 20),

              // Logistics services
              const Text(
                'Logistics Services',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),
              _buildServiceList(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatCard(String count, String title, IconData icon) {
    return Column(
      children: [
        Container(
          width: 60,
          height: 60,
          decoration: BoxDecoration(
            color: Colors.teal[100],
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: Colors.teal, size: 30),
        ),
        const SizedBox(height: 5),
        Text(
          count,
          style: const TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
          ),
        ),
        Text(
          title,
          style: const TextStyle(
            fontSize: 12,
            color: Colors.grey,
          ),
        ),
      ],
    );
  }

  Widget _buildQuickAction({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 100,
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 30),
            const SizedBox(height: 8),
            Text(
              label,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 12,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildShipmentList() {
    return Column(
      children: [
        _buildShipmentCard(
          'SH123456',
          'Electronics to Lagos',
          'In Transit',
          '2 stops remaining',
          Colors.orange,
        ),
        const SizedBox(height: 10),
        _buildShipmentCard(
          'SH789012',
          'Furniture to Abuja',
          'Processing',
          'Ready for pickup',
          Colors.blue,
        ),
        const SizedBox(height: 10),
        _buildShipmentCard(
          'SH345678',
          'Clothing to Port Harcourt',
          'Delivered',
          'Delivered today',
          Colors.green,
        ),
      ],
    );
  }

  Widget _buildShipmentCard(String id, String description, String status, String details, Color statusColor) {
    return Card(
      child: Container(
        padding: const EdgeInsets.all(15),
        child: Row(
          children: [
            Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(
                Icons.local_shipping,
                color: statusColor,
                size: 25,
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    id,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Text(
                    description,
                    style: const TextStyle(
                      fontSize: 14,
                      color: Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Text(
                    details,
                    style: const TextStyle(
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text(
                status,
                style: TextStyle(
                  color: statusColor,
                  fontWeight: FontWeight.w500,
                  fontSize: 12,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildServiceList() {
    return Column(
      children: [
        _buildServiceCard(
          'Last Mile Delivery',
          'Fast and reliable final delivery service',
          Icons.local_shipping,
          Colors.teal,
        ),
        const SizedBox(height: 10),
        _buildServiceCard(
          'Warehousing',
          'Secure storage and inventory management',
          Icons.inventory_2,
          Colors.blue,
        ),
        const SizedBox(height: 10),
        _buildServiceCard(
          'Cross Border',
          'International shipping solutions',
          Icons.language,
          Colors.green,
        ),
      ],
    );
  }

  Widget _buildServiceCard(String title, String description, IconData icon, Color color) {
    return Card(
      child: Container(
        padding: const EdgeInsets.all(15),
        child: Row(
          children: [
            Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(icon, color: color, size: 25),
            ),
            const SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Text(
                    description,
                    style: const TextStyle(
                      fontSize: 14,
                      color: Colors.grey,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios, size: 16),
          ],
        ),
      ),
    );
  }
}