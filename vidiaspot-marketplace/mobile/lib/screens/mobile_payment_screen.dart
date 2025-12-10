// lib/screens/mobile_payment_screen.dart
import 'package:flutter/material.dart';
import '../services/mobile_payment_service.dart';

class MobilePaymentScreen extends StatefulWidget {
  final double? initialAmount;
  final String? initialCurrency;
  
  const MobilePaymentScreen({Key? key, this.initialAmount, this.initialCurrency}) 
      : super(key: key);

  @override
  _MobilePaymentScreenState createState() => _MobilePaymentScreenState();
}

class _MobilePaymentScreenState extends State<MobilePaymentScreen> {
  final MobilePaymentService _paymentService = MobilePaymentService();
  
  String _selectedMethod = '';
  String _selectedProvider = '';
  double _amount = 0.0;
  String _currency = 'USD';
  String _description = '';
  String _phoneNumber = '';
  
  List<PaymentMethod> _paymentMethods = [];
  List<MobileMoneyProvider> _providers = [];
  bool _isLoading = true;
  
  // Controllers
  final TextEditingController _amountController = TextEditingController();
  final TextEditingController _descriptionController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _amount = widget.initialAmount ?? 0.0;
    _currency = widget.initialCurrency ?? 'USD';
    
    _amountController.text = _amount.toString();
    _loadPaymentData();
  }

  Future<void> _loadPaymentData() async {
    setState(() {
      _isLoading = true;
    });

    try {
      // Load payment methods and providers
      final methods = await _paymentService.getAvailablePaymentMethods();
      final providers = await _paymentService.getMobileMoneyProviders();
      
      setState(() {
        _paymentMethods = methods;
        _providers = providers;
        _isLoading = false;
        
        // Set default values if available
        if (methods.isNotEmpty) {
          _selectedMethod = methods[0].id;
        }
        if (providers.isNotEmpty) {
          _selectedProvider = providers[0].id;
        }
      });
    } catch (e) {
      print('Error loading payment data: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error loading payment options: $e'),
            backgroundColor: Colors.red,
          ),
        );
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  Future<void> _processPayment() async {
    if (_amount <= 0) {
      _showError('Please enter a valid amount');
      return;
    }

    if (_selectedMethod.isEmpty) {
      _showError('Please select a payment method');
      return;
    }

    // Show confirmation dialog
    bool confirmed = await showDialog<bool>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Confirm Payment'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Amount: \$${_amount.toStringAsFixed(2)}'),
              Text('Currency: $_currency'),
              Text('Method: ${_getPaymentMethodName(_selectedMethod)}'),
              if (_description.isNotEmpty) Text('Description: $_description'),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.of(context).pop(true),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green,
              ),
              child: const Text(
                'CONFIRM',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ],
        );
      },
    ) ?? false;

    if (confirmed) {
      try {
        // Process the payment
        PaymentResult result = await _paymentService.processMobilePayment(
          methodId: _selectedMethod,
          amount: _amount,
          currency: _currency,
          description: _description,
        );

        if (result.status == 'success' || result.status == 'pending') {
          _showSuccess('Payment initiated successfully!');
          
          // Navigate to payment status screen
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => PaymentStatusScreen(
                transactionId: result.transactionId,
                amount: result.amount,
                currency: result.currency,
                status: result.status,
              ),
            ),
          );
        } else {
          _showError('Payment failed: ${result.message}');
        }
      } catch (e) {
        _showError('Payment processing failed: $e');
      }
    }
  }

  Future<void> _processMobileMoneyPayment() async {
    if (_amount <= 0) {
      _showError('Please enter a valid amount');
      return;
    }

    if (_phoneNumber.isEmpty) {
      _showError('Please enter a phone number');
      return;
    }

    if (_selectedProvider.isEmpty) {
      _showError('Please select a mobile money provider');
      return;
    }

    // Show confirmation dialog
    bool confirmed = await showDialog<bool>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Confirm Mobile Money Payment'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Amount: \$${_amount.toStringAsFixed(2)}'),
              Text('Currency: $_currency'),
              Text('Provider: ${_getProviderName(_selectedProvider)}'),
              Text('Phone: $_phoneNumber'),
              if (_description.isNotEmpty) Text('Description: $_description'),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.of(context).pop(true),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green,
              ),
              child: const Text(
                'CONFIRM',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ],
        );
      },
    ) ?? false;

    if (confirmed) {
      try {
        // Process mobile money payment
        PaymentResult result = await _paymentService.initiateMobileMoneyPayment(
          providerId: _selectedProvider,
          phoneNumber: _phoneNumber,
          amount: _amount,
          currency: _currency,
          description: _description,
        );

        if (result.status == 'success' || result.status == 'pending') {
          _showSuccess('Mobile money payment initiated!');
          
          // Navigate to payment status screen
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => PaymentStatusScreen(
                transactionId: result.transactionId,
                amount: result.amount,
                currency: result.currency,
                status: result.status,
              ),
            ),
          );
        } else {
          _showError('Mobile money payment failed: ${result.message}');
        }
      } catch (e) {
        _showError('Mobile money payment failed: $e');
      }
    }
  }

  String _getPaymentMethodName(String methodId) {
    PaymentMethod? method = _paymentMethods.firstWhere(
      (m) => m.id == methodId,
      orElse: () => PaymentMethod(
        id: '',
        methodType: 'Unknown',
        name: 'Unknown',
        details: {},
        isDefault: false,
        createdAt: DateTime.now(),
      ),
    );
    return method.name;
  }

  String _getProviderName(String providerId) {
    MobileMoneyProvider? provider = _providers.firstWhere(
      (p) => p.id == providerId,
      orElse: () => MobileMoneyProvider(
        id: '',
        name: 'Unknown',
        countryCode: 'Unknown',
        currency: 'Unknown',
        config: {},
      ),
    );
    return provider.name;
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(color: Colors.white)),
        backgroundColor: Colors.red,
      ),
    );
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(color: Colors.white)),
        backgroundColor: Colors.green,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Mobile Payment'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Padding(
              padding: const EdgeInsets.all(16),
              child: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Payment amount section
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Payment Amount',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 16),
                            TextField(
                              controller: _amountController,
                              decoration: InputDecoration(
                                labelText: 'Amount',
                                border: const OutlineInputBorder(),
                                hintText: 'Enter amount to pay',
                                prefixText: '\$',
                              ),
                              keyboardType: TextInputType.number,
                              onChanged: (value) {
                                double? parsedValue = double.tryParse(value);
                                setState(() {
                                  _amount = parsedValue ?? 0.0;
                                });
                              },
                            ),
                            const SizedBox(height: 16),
                            TextField(
                              controller: _descriptionController,
                              decoration: const InputDecoration(
                                labelText: 'Description',
                                border: OutlineInputBorder(),
                                hintText: 'Purpose of payment',
                              ),
                              maxLines: 2,
                              onChanged: (value) {
                                setState(() {
                                  _description = value;
                                });
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Mobile money section
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Mobile Money Payment',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 16),
                            DropdownButtonFormField<String>(
                              value: _selectedProvider.isEmpty && _providers.isNotEmpty 
                                  ? _providers[0].id 
                                  : _selectedProvider,
                              decoration: const InputDecoration(
                                labelText: 'Mobile Money Provider',
                                border: OutlineInputBorder(),
                              ),
                              items: _providers.map((provider) {
                                return DropdownMenuItem(
                                  value: provider.id,
                                  child: Text(provider.name),
                                );
                              }).toList(),
                              onChanged: (value) {
                                if (value != null) {
                                  setState(() {
                                    _selectedProvider = value;
                                  });
                                }
                              },
                            ),
                            const SizedBox(height: 16),
                            TextField(
                              controller: _phoneController,
                              decoration: const InputDecoration(
                                labelText: 'Phone Number',
                                border: OutlineInputBorder(),
                                hintText: 'Enter mobile money number',
                                prefix: Icon(Icons.phone),
                              ),
                              keyboardType: TextInputType.phone,
                              onChanged: (value) {
                                setState(() {
                                  _phoneNumber = value;
                                });
                              },
                            ),
                            const SizedBox(height: 16),
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton(
                                onPressed: _phoneNumber.isEmpty || _amount <= 0
                                    ? null
                                    : _processMobileMoneyPayment,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.green,
                                  foregroundColor: Colors.white,
                                ),
                                child: const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.mobile_friendly),
                                    SizedBox(width: 8),
                                    Text('Send Mobile Money'),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Traditional payment methods section
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Other Payment Methods',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 16),
                            if (_paymentMethods.isNotEmpty)
                              DropdownButtonFormField<String>(
                                value: _selectedMethod.isEmpty 
                                    ? _paymentMethods.first.id 
                                    : _selectedMethod,
                                decoration: const InputDecoration(
                                  labelText: 'Payment Method',
                                  border: OutlineInputBorder(),
                                ),
                                items: _paymentMethods.map((method) {
                                  return DropdownMenuItem(
                                    value: method.id,
                                    child: Text('${method.name} (${method.methodType})'),
                                  );
                                }).toList(),
                                onChanged: (value) {
                                  if (value != null) {
                                    setState(() {
                                      _selectedMethod = value;
                                    });
                                  }
                                },
                              )
                            else
                              const Text('No payment methods available'),
                            const SizedBox(height: 16),
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton(
                                onPressed: _selectedMethod.isEmpty || _amount <= 0
                                    ? null
                                    : _processPayment,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.blue,
                                  foregroundColor: Colors.white,
                                ),
                                child: const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.payment),
                                    SizedBox(width: 8),
                                    Text('Process Payment'),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Quick payment buttons
                    const Text(
                      'Quick Amounts',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      children: [10.0, 25.0, 50.0, 100.0].map((amount) {
                        return FilterChip(
                          label: Text('\$$amount'),
                          selected: _amount == amount,
                          onSelected: (selected) {
                            setState(() {
                              _amount = amount;
                              _amountController.text = amount.toString();
                            });
                          },
                        );
                      }).toList(),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}

class PaymentStatusScreen extends StatefulWidget {
  final String transactionId;
  final double amount;
  final String currency;
  final String status;
  
  const PaymentStatusScreen({
    Key? key,
    required this.transactionId,
    required this.amount,
    required this.currency,
    required this.status,
  }) : super(key: key);

  @override
  _PaymentStatusScreenState createState() => _PaymentStatusScreenState();
}

class _PaymentStatusScreenState extends State<PaymentStatusScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Payment Status'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Status icon and message
            Icon(
              widget.status == 'success' || widget.status == 'completed'
                  ? Icons.check_circle
                  : widget.status == 'pending'
                      ? Icons.hourglass_empty
                      : Icons.error,
              size: 100,
              color: widget.status == 'success' || widget.status == 'completed'
                  ? Colors.green
                  : widget.status == 'pending'
                      ? Colors.orange
                      : Colors.red,
            ),
            const SizedBox(height: 24),
            Text(
              widget.status.toUpperCase(),
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: widget.status == 'success' || widget.status == 'completed'
                    ? Colors.green
                    : widget.status == 'pending'
                        ? Colors.orange
                        : Colors.red,
              ),
            ),
            const SizedBox(height: 16),
            Text(
              'Transaction ID: ${widget.transactionId}',
              style: const TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 8),
            Text(
              'Amount: ${widget.currency} ${widget.amount.toStringAsFixed(2)}',
              style: const TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.blue,
                  foregroundColor: Colors.white,
                ),
                child: const Text('Back to Payments'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}