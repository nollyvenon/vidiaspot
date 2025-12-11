import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'delivery_package_detail_screen.dart';
import '../services/delivery_management/delivery_service.dart';

class DeliveryPackagesScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final deliveryService = Provider.of<DeliveryManagementService>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('Delivery Packages'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Consumer<DeliveryManagementService>(
        builder: (context, service, child) {
          final pendingPackages = service.getPackagesByStatus(DeliveryStatus.pending);
          final inTransitPackages = service.getPackagesByStatus(DeliveryStatus.inTransit);
          final outForDeliveryPackages = service.getPackagesByStatus(DeliveryStatus.outForDelivery);
          
          return DefaultTabController(
            length: 3,
            child: Column(
              children: [
                TabBar(
                  labelColor: Colors.blue,
                  tabs: [
                    Tab(text: 'Pending (${pendingPackages.length})'),
                    Tab(text: 'In Transit (${inTransitPackages.length})'),
                    Tab(text: 'Out for Delivery (${outForDeliveryPackages.length})'),
                  ],
                ),
                Expanded(
                  child: TabBarView(
                    children: [
                      _buildPackageList(context, pendingPackages, 'Pending'),
                      _buildPackageList(context, inTransitPackages, 'In Transit'),
                      _buildPackageList(context, outForDeliveryPackages, 'Out for Delivery'),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
  
  Widget _buildPackageList(BuildContext context, List<DeliveryPackage> packages, String status) {
    if (packages.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.local_shipping, size: 60, color: Colors.grey),
            SizedBox(height: 16),
            Text(
              'No packages $status',
              style: TextStyle(fontSize: 16, color: Colors.grey),
            ),
          ],
        ),
      );
    }
    
    return ListView.builder(
      itemCount: packages.length,
      itemBuilder: (context, index) {
        final package = packages[index];
        return Card(
          margin: EdgeInsets.all(8),
          child: ListTile(
            contentPadding: EdgeInsets.all(16),
            leading: Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: _getStatusColor(package.status),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(
                _getStatusIcon(package.status),
                color: Colors.white,
              ),
            ),
            title: Text(
              package.recipientName,
              style: TextStyle(fontWeight: FontWeight.bold),
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(package.deliveryAddress),
                Text('Tracking: ${package.trackingNumber}'),
                if (package.eta != null) Text('ETA: ${_formatDateTime(package.eta!)}'),
              ],
            ),
            trailing: Icon(Icons.arrow_forward_ios, size: 16),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => DeliveryPackageDetailScreen(deliveryPackage: package),
                ),
              );
            },
          ),
        );
      },
    );
  }
  
  Color _getStatusColor(DeliveryStatus status) {
    switch (status) {
      case DeliveryStatus.pending:
        return Colors.grey;
      case DeliveryStatus.inTransit:
        return Colors.blue;
      case DeliveryStatus.outForDelivery:
        return Colors.orange;
      case DeliveryStatus.delivered:
        return Colors.green;
      case DeliveryStatus.failed:
        return Colors.red;
      case DeliveryStatus.returned:
        return Colors.purple;
    }
  }
  
  IconData _getStatusIcon(DeliveryStatus status) {
    switch (status) {
      case DeliveryStatus.pending:
        return Icons.access_time;
      case DeliveryStatus.inTransit:
        return Icons.local_shipping;
      case DeliveryStatus.outForDelivery:
        return Icons.motorcycle;
      case DeliveryStatus.delivered:
        return Icons.check_circle;
      case DeliveryStatus.failed:
        return Icons.warning;
      case DeliveryStatus.returned:
        return Icons.undo;
    }
  }
  
  String _formatDateTime(DateTime dateTime) {
    return '${dateTime.hour.toString().padLeft(2, '0')}:${dateTime.minute.toString().padLeft(2, '0')}';
  }
}