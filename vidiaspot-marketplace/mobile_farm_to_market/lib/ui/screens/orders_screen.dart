import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/farm_order.dart';
import '../ui/providers/farm_provider.dart';

class OrdersScreen extends StatefulWidget {
  @override
  _OrdersScreenState createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> with TickerProviderStateMixin {
  late TabController _tabController;
  final List<String> _statusOptions = [
    'all',
    'pending', 
    'confirmed', 
    'preparing', 
    'out_for_delivery', 
    'delivered', 
    'cancelled'
  ];
  
  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _statusOptions.length, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final farmProvider = Provider.of<FarmProvider>(context);
    final allOrders = farmProvider.orders;
    
    String currentStatusFilter = _statusOptions[_tabController.index];
    List<FarmOrder> filteredOrders = allOrders;
    
    if (currentStatusFilter != 'all') {
      filteredOrders = allOrders.where((order) => order.status == currentStatusFilter).toList();
    }

    return Scaffold(
      appBar: AppBar(
        title: Text('Orders'),
        backgroundColor: Colors.green[400],
        foregroundColor: Colors.white,
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          tabs: _statusOptions.map((status) {
            String displayText = _getStatusDisplayText(status);
            int count = status == 'all' 
                ? allOrders.length 
                : allOrders.where((order) => order.status == status).length;
            
            return Tab(
              text: '$displayText ($count)',
            );
          }).toList(),
        ),
      ),
      body: farmProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                // Refresh orders
              },
              child: filteredOrders.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.shopping_bag_outlined,
                            size: 80,
                            color: Colors.grey[400],
                          ),
                          SizedBox(height: 20),
                          Text(
                            'No orders yet',
                            style: TextStyle(
                              fontSize: 18,
                              color: Colors.grey[600],
                            ),
                          ),
                          SizedBox(height: 10),
                          Text(
                            'Orders will appear here when customers place them',
                            style: TextStyle(
                              color: Colors.grey[500],
                            ),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      itemCount: filteredOrders.length,
                      itemBuilder: (context, index) {
                        return _buildOrderCard(filteredOrders[index]);
                      },
                    ),
            ),
    );
  }

  Widget _buildOrderCard(FarmOrder order) {
    Color statusColor = _getStatusColor(order.status);
    String statusText = _getStatusDisplayText(order.status);

    return Card(
      margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Order #${order.orderId}',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    statusText,
                    style: TextStyle(
                      color: statusColor,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
              ],
            ),
            SizedBox(height: 8),
            
            // Customer information
            Row(
              children: [
                Icon(Icons.person, size: 16, color: Colors.grey[600]),
                SizedBox(width: 5),
                Text(
                  order.customerName,
                  style: TextStyle(fontSize: 14, color: Colors.grey[700]),
                ),
              ],
            ),
            SizedBox(height: 5),
            Row(
              children: [
                Icon(Icons.email, size: 16, color: Colors.grey[600]),
                SizedBox(width: 5),
                Text(
                  order.customerEmail,
                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                ),
              ],
            ),
            SizedBox(height: 5),
            Row(
              children: [
                Icon(Icons.phone, size: 16, color: Colors.grey[600]),
                SizedBox(width: 5),
                Text(
                  order.customerPhone,
                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                ),
              ],
            ),
            SizedBox(height: 10),
            
            // Order items
            Container(
              padding: EdgeInsets.symmetric(horizontal: 8),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: order.items.map((item) {
                  return Padding(
                    padding: EdgeInsets.symmetric(vertical: 4),
                    child: Row(
                      children: [
                        Text(
                          '${item.quantity}x',
                          style: TextStyle(fontWeight: FontWeight.w500),
                        ),
                        SizedBox(width: 8),
                        Expanded(child: Text(item.productName)),
                        Text(
                          '\$${(item.unitPrice * item.quantity).toStringAsFixed(2)}',
                          style: TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  );
                }).toList(),
              ),
            ),
            SizedBox(height: 10),
            
            // Order totals
            Divider(),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Subtotal:'),
                Text('\$${order.subtotal.toStringAsFixed(2)}'),
              ],
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Tax:'),
                Text('\$${order.tax.toStringAsFixed(2)}'),
              ],
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Delivery Fee:'),
                Text('\$${order.deliveryFee.toStringAsFixed(2)}'),
              ],
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Total:',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                ),
                Text(
                  '\$${order.totalAmount.toStringAsFixed(2)}',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ],
            ),
            SizedBox(height: 10),
            
            // Delivery address
            Row(
              children: [
                Icon(Icons.location_on, size: 16, color: Colors.grey[600]),
                SizedBox(width: 5),
                Expanded(
                  child: Text(
                    order.deliveryAddress,
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
            SizedBox(height: 10),
            
            // Order date
            Row(
              children: [
                Icon(Icons.access_time, size: 16, color: Colors.grey[600]),
                SizedBox(width: 5),
                Text(
                  'Ordered: ${order.orderDate.toLocal().toString().split('.')[0]}',
                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                ),
              ],
            ),
            if (order.estimatedDeliveryTime != null) ...[
              SizedBox(height: 5),
              Row(
                children: [
                  Icon(Icons.alarm, size: 16, color: Colors.grey[600]),
                  SizedBox(width: 5),
                  Text(
                    'ETA: ${order.estimatedDeliveryTime!.toLocal().toString().split('.')[0]}',
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                  ),
                ],
              ),
            ],
            SizedBox(height: 15),
            
            // Action buttons based on status
            _buildActionButtons(order),
          ],
        ),
      ),
    );
  }

  Widget _buildActionButtons(FarmOrder order) {
    List<Widget> buttons = [];
    
    switch (order.status) {
      case 'pending':
        buttons = [
          Expanded(
            child: ElevatedButton(
              onPressed: () => _confirmOrder(order),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green[400],
              ),
              child: Text(
                'Confirm Order',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ),
          SizedBox(width: 10),
          Expanded(
            child: ElevatedButton(
              onPressed: () => _cancelOrder(order),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red[400],
              ),
              child: Text(
                'Cancel Order',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ),
        ];
        break;
      case 'confirmed':
        buttons = [
          Expanded(
            child: ElevatedButton(
              onPressed: () => _prepareOrder(order),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.orange[400],
              ),
              child: Text(
                'Start Preparing',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ),
        ];
        break;
      case 'preparing':
        buttons = [
          Expanded(
            child: ElevatedButton(
              onPressed: () => _markAsReady(order),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.blue[400],
              ),
              child: Text(
                'Mark as Ready',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ),
        ];
        break;
      case 'out_for_delivery':
        buttons = [
          Expanded(
            child: ElevatedButton(
              onPressed: () => _markAsDelivered(order),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green[600],
              ),
              child: Text(
                'Mark as Delivered',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ),
        ];
        break;
      case 'delivered':
        buttons = [
          Text(
            'Order Completed',
            style: TextStyle(color: Colors.green[600]),
          ),
        ];
        break;
      case 'cancelled':
        buttons = [
          Text(
            'Order Cancelled',
            style: TextStyle(color: Colors.red[600]),
          ),
        ];
        break;
    }
    
    return Row(
      children: buttons,
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending':
        return Colors.orange;
      case 'confirmed':
        return Colors.blue;
      case 'preparing':
        return Colors.yellow[700]!;
      case 'out_for_delivery':
        return Colors.purple;
      case 'delivered':
        return Colors.green;
      case 'cancelled':
        return Colors.red;
      case 'refunded':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  String _getStatusDisplayText(String status) {
    switch (status) {
      case 'pending':
        return 'Pending';
      case 'confirmed':
        return 'Confirmed';
      case 'preparing':
        return 'Preparing';
      case 'out_for_delivery':
        return 'Out for Delivery';
      case 'delivered':
        return 'Delivered';
      case 'cancelled':
        return 'Cancelled';
      case 'refunded':
        return 'Refunded';
      default:
        return status.replaceAll('_', ' ').toUpperCase();
    }
  }

  void _confirmOrder(FarmOrder order) {
    final farmProvider = Provider.of<FarmProvider>(context, listen: false);
    farmProvider.updateOrderStatus(order.id, 'confirmed');
    _showConfirmation('Order Confirmed');
  }

  void _cancelOrder(FarmOrder order) {
    final farmProvider = Provider.of<FarmProvider>(context, listen: false);
    farmProvider.updateOrderStatus(order.id, 'cancelled');
    _showConfirmation('Order Cancelled');
  }

  void _prepareOrder(FarmOrder order) {
    final farmProvider = Provider.of<FarmProvider>(context, listen: false);
    farmProvider.updateOrderStatus(order.id, 'preparing');
    _showConfirmation('Order Preparation Started');
  }

  void _markAsReady(FarmOrder order) {
    final farmProvider = Provider.of<FarmProvider>(context, listen: false);
    farmProvider.updateOrderStatus(order.id, 'out_for_delivery');
    _showConfirmation('Order Marked as Ready for Delivery');
  }

  void _markAsDelivered(FarmOrder order) {
    final farmProvider = Provider.of<FarmProvider>(context, listen: false);
    farmProvider.updateOrderStatus(order.id, 'delivered');
    _showConfirmation('Order Marked as Delivered');
  }

  void _showConfirmation(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
      ),
    );
  }
}