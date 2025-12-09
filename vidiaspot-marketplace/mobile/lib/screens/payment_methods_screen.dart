// lib/screens/payment_methods_screen.dart
import 'package:flutter/material.dart';
import '../models/payment_method_model.dart';
import '../services/payment_service.dart';

class PaymentMethodsScreen extends StatefulWidget {
  const PaymentMethodsScreen({Key? key}) : super(key: key);

  @override
  _PaymentMethodsScreenState createState() => _PaymentMethodsScreenState();
}

class _PaymentMethodsScreenState extends State<PaymentMethodsScreen> {
  final PaymentService _paymentService = PaymentService();
  List<PaymentMethod> _paymentMethods = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadPaymentMethods();
  }

  Future<void> _loadPaymentMethods() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final methods = await _paymentService.getUserPaymentMethods();
      setState(() {
        _paymentMethods = methods;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to load payment methods: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Payment Methods'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _showAddPaymentMethodDialog,
        child: Icon(Icons.add),
        backgroundColor: Colors.green,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadPaymentMethods,
              child: _paymentMethods.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.payment,
                            size: 80,
                            color: Colors.grey[300],
                          ),
                          SizedBox(height: 20),
                          Text(
                            'No payment methods added',
                            style: TextStyle(
                              fontSize: 18,
                              color: Colors.grey[600],
                            ),
                          ),
                          SizedBox(height: 10),
                          Text(
                            'Add a payment method to get started',
                            style: TextStyle(
                              color: Colors.grey[500],
                            ),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      itemCount: _paymentMethods.length,
                      itemBuilder: (context, index) {
                        final method = _paymentMethods[index];
                        return Card(
                          margin: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                          child: ListTile(
                            leading: Container(
                              width: 50,
                              height: 50,
                              decoration: BoxDecoration(
                                color: Colors.blue[100],
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: _getPaymentMethodIcon(method.methodType),
                            ),
                            title: Text(method.methodName),
                            subtitle: Text('${method.provider} • ${method.isDefault ? 'Default' : ''}'),
                            trailing: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                if (!method.isDefault)
                                  IconButton(
                                    icon: Icon(Icons.star_border, color: Colors.grey),
                                    onPressed: () => _setDefaultPaymentMethod(method.id),
                                    tooltip: 'Set as default',
                                  ),
                                if (method.isDefault)
                                  Icon(Icons.star, color: Colors.amber),
                                PopupMenuButton(
                                  onSelected: (value) {
                                    if (value == 'delete') {
                                      _deletePaymentMethod(method.id);
                                    }
                                  },
                                  itemBuilder: (context) => [
                                    PopupMenuItem(
                                      value: 'delete',
                                      child: Row(
                                        children: [
                                          Icon(Icons.delete, color: Colors.red),
                                          SizedBox(width: 8),
                                          Text('Remove'),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
            ),
    );
  }

  Widget _getPaymentMethodIcon(String methodType) {
    switch (methodType.toLowerCase()) {
      case 'credit_card':
      case 'debit_card':
        return Icon(Icons.credit_card, color: Colors.blue);
      case 'paypal':
        return Icon(Icons.attach_money, color: Colors.blue[800]);
      case 'bitcoin':
      case 'cryptocurrency':
        return Icon(Icons.currency_bitcoin, color: Colors.orange);
      case 'ethereum':
        return Icon(Icons.circle, color: Colors.blueGrey);
      case 'mpesa':
        return Icon(Icons.mobile_friendly, color: Colors.green);
      case 'mobile_money':
        return Icon(Icons.phone_android, color: Colors.teal);
      case 'klarna':
      case 'afterpay':
        return Icon(Icons.schedule_send, color: Colors.purple);
      default:
        return Icon(Icons.payment, color: Colors.grey);
    }
  }

  Future<void> _showAddPaymentMethodDialog() async {
    final formKey = GlobalKey<FormState>();
    final TextEditingController nameController = TextEditingController();
    final TextEditingController providerController = TextEditingController();
    final TextEditingController identifierController = TextEditingController();
    String selectedMethodType = 'credit_card';
    bool isDefault = false;

    await showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Add Payment Method'),
        content: SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                DropdownButtonFormField<String>(
                  value: selectedMethodType,
                  decoration: InputDecoration(labelText: 'Payment Type'),
                  items: [
                    DropdownMenuItem(value: 'credit_card', child: Text('Credit/Debit Card')),
                    DropdownMenuItem(value: 'paypal', child: Text('PayPal')),
                    DropdownMenuItem(value: 'bitcoin', child: Text('Bitcoin')),
                    DropdownMenuItem(value: 'ethereum', child: Text('Ethereum')),
                    DropdownMenuItem(value: 'mpesa', child: Text('M-Pesa')),
                    DropdownMenuItem(value: 'mobile_money', child: Text('Mobile Money')),
                    DropdownMenuItem(value: 'klarna', child: Text('Klarna')),
                    DropdownMenuItem(value: 'afterpay', child: Text('Afterpay')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        selectedMethodType = value;
                      });
                    }
                  },
                ),
                SizedBox(height: 16),
                TextFormField(
                  controller: nameController,
                  decoration: InputDecoration(labelText: 'Method Name (e.g., "My Visa Card")'),
                  validator: (value) => value?.isEmpty == true ? 'Name is required' : null,
                ),
                SizedBox(height: 16),
                TextFormField(
                  controller: providerController,
                  decoration: InputDecoration(labelText: 'Provider (e.g., Visa, PayPal)'),
                ),
                SizedBox(height: 16),
                TextFormField(
                  controller: identifierController,
                  decoration: InputDecoration(
                    labelText: 'Identifier',
                    helperText: 'Card number, wallet address, phone number',
                  ),
                  validator: (value) => value?.isEmpty == true ? 'Identifier is required' : null,
                ),
                SizedBox(height: 16),
                SwitchListTile(
                  title: Text('Set as Default'),
                  value: isDefault,
                  onChanged: (value) {
                    setState(() {
                      isDefault = value;
                    });
                  },
                ),
              ],
            ),
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              if (formKey.currentState!.validate()) {
                try {
                  await _paymentService.addPaymentMethod(
                    methodType: selectedMethodType,
                    methodName: nameController.text,
                    provider: providerController.text,
                    identifier: identifierController.text,
                    isDefault: isDefault,
                  );
                  
                  Navigator.pop(context);
                  _loadPaymentMethods(); // Reload the list
                  
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Payment method added successfully')),
                  );
                } catch (e) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Failed to add payment method: $e')),
                  );
                }
              }
            },
            child: Text('Add'),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.green,
              foregroundColor: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _setDefaultPaymentMethod(int methodId) async {
    try {
      await _paymentService.setDefaultPaymentMethod(methodId);
      _loadPaymentMethods(); // Reload to reflect changes
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Default payment method updated')),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to set default payment method: $e')),
      );
    }
  }

  Future<void> _deletePaymentMethod(int methodId) async {
    // In a real app, you'd implement delete functionality
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Delete functionality would be implemented here')),
    );
  }
}