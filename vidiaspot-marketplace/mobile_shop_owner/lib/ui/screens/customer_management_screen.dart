import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/shop_owner_provider.dart';

class CustomerManagementScreen extends StatefulWidget {
  @override
  _CustomerManagementScreenState createState() => _CustomerManagementScreenState();
}

class _CustomerManagementScreenState extends State<CustomerManagementScreen> {
  List<Customer> _customers = [
    Customer(
      id: 'cust1',
      name: 'John Doe',
      email: 'john@example.com',
      phone: '+1234567890',
      totalOrders: 5,
      totalSpent: 245.99,
      lastOrderDate: DateTime.now().subtract(Duration(days: 5)),
      status: 'Active',
    ),
    Customer(
      id: 'cust2',
      name: 'Jane Smith',
      email: 'jane@example.com',
      phone: '+0987654321',
      totalOrders: 3,
      totalSpent: 189.50,
      lastOrderDate: DateTime.now().subtract(Duration(days: 12)),
      status: 'Active',
    ),
    Customer(
      id: 'cust3',
      name: 'Bob Johnson',
      email: 'bob@example.com',
      phone: '+1122334455',
      totalOrders: 8,
      totalSpent: 657.25,
      lastOrderDate: DateTime.now().subtract(Duration(days: 2)),
      status: 'VIP',
    ),
  ];

  String _searchQuery = '';
  String _filterOption = 'all'; // all, active, inactive, vip

  @override
  Widget build(BuildContext context) {
    List<Customer> filteredCustomers = _filterCustomers();

    return Scaffold(
      appBar: AppBar(
        title: Text('Customers'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.search),
            onPressed: () {
              showSearch(
                context: context,
                delegate: _CustomerSearchDelegate(_searchQuery),
              ).then((value) {
                if (value != null) {
                  setState(() {
                    _searchQuery = value;
                  });
                }
              });
            },
          ),
        ],
      ),
      body: Column(
        children: [
          // Filter options
          Container(
            padding: EdgeInsets.all(10),
            child: Row(
              children: [
                Expanded(
                  child: DropdownButtonFormField<String>(
                    value: _filterOption,
                    decoration: InputDecoration(
                      labelText: 'Filter',
                      border: OutlineInputBorder(),
                    ),
                    items: [
                      DropdownMenuItem(value: 'all', child: Text('All Customers')),
                      DropdownMenuItem(value: 'active', child: Text('Active')),
                      DropdownMenuItem(value: 'inactive', child: Text('Inactive')),
                      DropdownMenuItem(value: 'vip', child: Text('VIP')),
                    ],
                    onChanged: (value) {
                      setState(() {
                        _filterOption = value ?? 'all';
                      });
                    },
                  ),
                ),
                SizedBox(width: 10),
                ElevatedButton(
                  onPressed: () {
                    // Refresh customer list
                  },
                  child: Icon(Icons.refresh),
                ),
              ],
            ),
          ),
          // Main content
          Expanded(
            child: RefreshIndicator(
              onRefresh: () async {
                // Refresh customer list
              },
              child: filteredCustomers.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.people_alt_outlined,
                            size: 80,
                            color: Colors.grey,
                          ),
                          SizedBox(height: 20),
                          Text(
                            'No customers found',
                            style: TextStyle(fontSize: 18, color: Colors.grey),
                          ),
                          SizedBox(height: 10),
                          Text(
                            'Your customers will appear here',
                            style: TextStyle(color: Colors.grey[600]),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      itemCount: filteredCustomers.length,
                      itemBuilder: (context, index) {
                        return _buildCustomerCard(filteredCustomers[index]);
                      },
                    ),
            ),
          ),
        ],
      ),
    );
  }

  List<Customer> _filterCustomers() {
    List<Customer> filtered = _customers;

    if (_searchQuery.isNotEmpty) {
      filtered = filtered.where((customer) =>
          customer.name.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          customer.email.toLowerCase().contains(_searchQuery.toLowerCase())).toList();
    }

    switch (_filterOption) {
      case 'active':
        filtered = filtered.where((customer) => customer.status.toLowerCase() != 'inactive').toList();
        break;
      case 'inactive':
        filtered = filtered.where((customer) => customer.status.toLowerCase() == 'inactive').toList();
        break;
      case 'vip':
        filtered = filtered.where((customer) => customer.status.toLowerCase() == 'vip').toList();
        break;
      default:
        // Show all customers
        break;
    }

    return filtered;
  }

  Widget _buildCustomerCard(Customer customer) {
    return Card(
      margin: EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      child: Padding(
        padding: EdgeInsets.all(15),
        child: Row(
          children: [
            Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.blue[100],
              ),
              child: Center(
                child: Text(
                  customer.name.split(' ').map((n) => n[0]).take(2).join().toUpperCase(),
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    color: Colors.blue[700],
                  ),
                ),
              ),
            ),
            SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        customer.name,
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                        ),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: _getStatusColor(customer.status),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          customer.status,
                          style: TextStyle(
                            color: _getStatusTextColor(customer.status),
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 5),
                  Text(
                    customer.email,
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 12,
                    ),
                  ),
                  SizedBox(height: 5),
                  Text(
                    customer.phone,
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 12,
                    ),
                  ),
                  SizedBox(height: 10),
                  Row(
                    children: [
                      _buildStatChip('Orders: ${customer.totalOrders}', Icons.shopping_bag),
                      SizedBox(width: 5),
                      _buildStatChip('Spent: \$${customer.totalSpent.toStringAsFixed(2)}', Icons.attach_money),
                      SizedBox(width: 5),
                      _buildStatChip(
                          'Last: ${_formatDate(customer.lastOrderDate)}', Icons.calendar_today),
                    ],
                  ),
                ],
              ),
            ),
            IconButton(
              icon: Icon(Icons.message, color: Colors.blue),
              onPressed: () {
                _sendMessageToCustomer(customer);
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatChip(String label, IconData icon) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: Colors.grey[200],
        borderRadius: BorderRadius.circular(10),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: Colors.grey[700]),
          SizedBox(width: 3),
          Text(
            label,
            style: TextStyle(
              fontSize: 10,
              color: Colors.grey[700],
            ),
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'vip':
        return Colors.purple[100]!;
      case 'active':
        return Colors.green[100]!;
      case 'inactive':
        return Colors.grey[300]!;
      default:
        return Colors.grey[200]!;
    }
  }

  Color _getStatusTextColor(String status) {
    switch (status.toLowerCase()) {
      case 'vip':
        return Colors.purple[700]!;
      case 'active':
        return Colors.green[700]!;
      case 'inactive':
        return Colors.grey[700]!;
      default:
        return Colors.grey[700]!;
    }
  }

  String _formatDate(DateTime date) {
    final now = DateTime.now();
    final difference = now.difference(date);

    if (difference.inDays == 0) {
      return 'Today';
    } else if (difference.inDays == 1) {
      return 'Yesterday';
    } else if (difference.inDays < 7) {
      return '${difference.inDays} days ago';
    } else {
      return '${date.day}/${date.month}/${date.year}';
    }
  }

  void _sendMessageToCustomer(Customer customer) {
    // Implementation for sending message to customer
    print('Sending message to ${customer.name}');
  }
}

class Customer {
  final String id;
  final String name;
  final String email;
  final String phone;
  final int totalOrders;
  final double totalSpent;
  final DateTime lastOrderDate;
  final String status; // Active, Inactive, VIP

  Customer({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.totalOrders,
    required this.totalSpent,
    required this.lastOrderDate,
    required this.status,
  });
}

class _CustomerSearchDelegate extends SearchDelegate<String> {
  final String initialQuery;

  _CustomerSearchDelegate(this.initialQuery) : super(searchFieldLabel: 'Search customers...');

  @override
  List<Widget> buildActions(BuildContext context) {
    return [
      IconButton(
        icon: Icon(Icons.clear),
        onPressed: () {
          query = '';
        },
      ),
    ];
  }

  @override
  Widget buildLeading(BuildContext context) {
    return IconButton(
      icon: AnimatedIcon(
        icon: AnimatedIcons.menu_arrow,
        progress: transitionAnimation,
      ),
      onPressed: () {
        close(context, query);
      },
    );
  }

  @override
  Widget buildResults(BuildContext context) {
    return query.isEmpty
        ? Container()
        : Container(
            child: Column(
              children: [
                Padding(
                  padding: EdgeInsets.all(10),
                  child: Text('Search results for: "$query"'),
                ),
              ],
            ),
          );
  }

  @override
  Widget buildSuggestions(BuildContext context) {
    return Container();
  }
}