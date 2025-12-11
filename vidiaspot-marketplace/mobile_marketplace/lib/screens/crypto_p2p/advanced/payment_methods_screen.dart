// lib/screens/crypto_p2p/advanced/payment_methods_screen.dart
import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import '../../../services/crypto_p2p_service.dart';

class PaymentMethodsScreen extends StatefulWidget {
  const PaymentMethodsScreen({Key? key}) : super(key: key);

  @override
  _PaymentMethodsScreenState createState() => _PaymentMethodsScreenState();
}

class _PaymentMethodsScreenState extends State<PaymentMethodsScreen> {
  final CryptoP2PService _cryptoP2PService = CryptoP2PService();
  List<Map<String, dynamic>> _paymentMethods = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadPaymentMethods();
  }

  void _loadPaymentMethods() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final methods = await _cryptoP2PService.getUserPaymentMethods();
      setState(() {
        _paymentMethods = methods;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error loading payment methods: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Payment Methods'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: () {
              _showAddPaymentMethodDialog();
            },
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadPaymentMethods,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          _loadPaymentMethods();
        },
        child: _isLoading
            ? _buildShimmerList()
            : _paymentMethods.isEmpty
                ? _buildEmptyState()
                : _buildPaymentMethodsList(),
      ),
    );
  }

  Widget _buildShimmerList() {
    return ListView.builder(
      itemCount: 5,
      itemBuilder: (context, index) {
        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              height: 100,
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          width: 120,
                          height: 16,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Container(
                          width: 180,
                          height: 14,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.payment,
            size: 64,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 16),
          const Text(
            'No payment methods added',
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey,
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Add a payment method to start trading',
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentMethodsList() {
    return ListView.builder(
      itemCount: _paymentMethods.length,
      itemBuilder: (context, index) {
        final method = _paymentMethods[index];
        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: ListTile(
            contentPadding: const EdgeInsets.all(16),
            leading: Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: _getMethodColor(method['payment_type']),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Icon(
                _getMethodIcon(method['payment_type']),
                color: Colors.white,
              ),
            ),
            title: Text(
              method['name'] ?? 'Unknown Payment Method',
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 4),
                Text(
                  '${method['account_name']} - ${method['account_number']}',
                  style: const TextStyle(
                    fontSize: 14,
                    color: Colors.grey,
                  ),
                ),
                if (method['bank_name'] != null)
                  Text(
                    method['bank_name'],
                    style: const TextStyle(
                      fontSize: 12,
                      color: Colors.grey,
                    ),
                  ),
              ],
            ),
            trailing: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Icon(
                  method['is_verified'] == true
                      ? Icons.verified_user
                      : Icons.pending,
                  color: method['is_verified'] == true ? Colors.green : Colors.orange,
                  size: 20,
                ),
                const SizedBox(height: 4),
                Icon(
                  method['is_active'] == true ? Icons.check_circle : Icons.cancel,
                  color: method['is_active'] == true ? Colors.green : Colors.red,
                  size: 20,
                ),
              ],
            ),
            onTap: () {
              _showPaymentMethodDetails(method);
            },
          ),
        );
      },
    );
  }

  Color _getMethodColor(String? paymentType) {
    switch (paymentType?.toLowerCase()) {
      case 'bank_transfer':
        return Colors.blue;
      case 'mobile_money':
        return Colors.green;
      case 'paypal':
        return Colors.orange;
      case 'credit_card':
      case 'debit_card':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  IconData _getMethodIcon(String? paymentType) {
    switch (paymentType?.toLowerCase()) {
      case 'bank_transfer':
        return Icons.account_balance;
      case 'mobile_money':
        return Icons.phone_android;
      case 'paypal':
        return Icons.paypal;
      case 'credit_card':
      case 'debit_card':
        return Icons.credit_card;
      default:
        return Icons.payment;
    }
  }

  void _showAddPaymentMethodDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        String paymentType = 'bank_transfer';
        String name = '';
        String accountName = '';
        String accountNumber = '';
        String bankName = '';
        String countryCode = 'US';

        return AlertDialog(
          title: const Text('Add Payment Method'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                DropdownButtonFormField<String>(
                  value: paymentType,
                  decoration: const InputDecoration(
                    labelText: 'Payment Type',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    DropdownMenuItem(
                      value: 'bank_transfer',
                      child: Row(
                        children: [
                          Icon(Icons.account_balance, color: Colors.blue),
                          const SizedBox(width: 10),
                          const Text('Bank Transfer'),
                        ],
                      ),
                    ),
                    DropdownMenuItem(
                      value: 'mobile_money',
                      child: Row(
                        children: [
                          Icon(Icons.phone_android, color: Colors.green),
                          const SizedBox(width: 10),
                          const Text('Mobile Money'),
                        ],
                      ),
                    ),
                    DropdownMenuItem(
                      value: 'paypal',
                      child: Row(
                        children: [
                          Icon(Icons.paypal, color: Colors.orange),
                          const SizedBox(width: 10),
                          const Text('PayPal'),
                        ],
                      ),
                    ),
                    DropdownMenuItem(
                      value: 'credit_card',
                      child: Row(
                        children: [
                          Icon(Icons.credit_card, color: Colors.red),
                          const SizedBox(width: 10),
                          const Text('Credit Card'),
                        ],
                      ),
                    ),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      paymentType = value;
                    }
                  },
                ),
                const SizedBox(height: 16),
                TextField(
                  decoration: const InputDecoration(
                    labelText: 'Method Name',
                    border: OutlineInputBorder(),
                  ),
                  onChanged: (value) => name = value,
                ),
                const SizedBox(height: 16),
                TextField(
                  decoration: const InputDecoration(
                    labelText: 'Account Name',
                    border: OutlineInputBorder(),
                  ),
                  onChanged: (value) => accountName = value,
                ),
                const SizedBox(height: 16),
                TextField(
                  decoration: const InputDecoration(
                    labelText: 'Account Number',
                    border: OutlineInputBorder(),
                  ),
                  onChanged: (value) => accountNumber = value,
                ),
                const SizedBox(height: 16),
                TextField(
                  decoration: const InputDecoration(
                    labelText: 'Bank Name (Optional)',
                    border: OutlineInputBorder(),
                  ),
                  onChanged: (value) => bankName = value,
                ),
                const SizedBox(height: 16),
                TextField(
                  decoration: const InputDecoration(
                    labelText: 'Country Code',
                    border: OutlineInputBorder(),
                  ),
                  onChanged: (value) => countryCode = value,
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () async {
                try {
                  await _cryptoP2PService.addPaymentMethod(
                    paymentType: paymentType,
                    name: name,
                    paymentDetails: {}, // In a real app, this would contain encrypted details
                    accountName: accountName,
                    accountNumber: accountNumber,
                    bankName: bankName.isEmpty ? null : bankName,
                    countryCode: countryCode,
                  );
                  
                  Navigator.of(context).pop();
                  _loadPaymentMethods(); // Refresh the list
                  
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Payment method added successfully'),
                      backgroundColor: Colors.green,
                    ),
                  );
                } catch (e) {
                  Navigator.of(context).pop();
                  
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Error adding payment method: $e'),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              },
              child: const Text('Add'),
            ),
          ],
        );
      },
    );
  }

  void _showPaymentMethodDetails(Map<String, dynamic> method) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(method['name']),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildDetailRow('Type', method['payment_type']),
              _buildDetailRow('Account Name', method['account_name']),
              _buildDetailRow('Account Number', method['account_number']),
              if (method['bank_name'] != null) _buildDetailRow('Bank', method['bank_name']),
              _buildDetailRow('Status', method['is_active'] ? 'Active' : 'Inactive'),
              _buildDetailRow('Verified', method['is_verified'] ? 'Yes' : 'No'),
              _buildDetailRow('Default', method['is_default'] ? 'Yes' : 'No'),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('Close'),
            ),
          ],
        );
      },
    );
  }

  Widget _buildDetailRow(String label, dynamic value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 80,
            child: Text(
              '$label: ',
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
          ),
          Expanded(
            child: Text(value?.toString() ?? 'N/A'),
          ),
        ],
      ),
    );
  }
}