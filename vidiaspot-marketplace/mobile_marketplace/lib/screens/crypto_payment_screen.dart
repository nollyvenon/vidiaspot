// lib/screens/crypto_payment_screen.dart
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/crypto_payment_model.dart';
import '../services/payment_service.dart';

class CryptoPaymentScreen extends StatefulWidget {
  final int transactionId;
  final double amountNgn;

  const CryptoPaymentScreen({
    Key? key,
    required this.transactionId,
    required this.amountNgn,
  }) : super(key: key);

  @override
  _CryptoPaymentScreenState createState() => _CryptoPaymentScreenState();
}

class _CryptoPaymentScreenState extends State<CryptoPaymentScreen> {
  final PaymentService _paymentService = PaymentService();
  final TextEditingController _walletAddressController = TextEditingController();
  String _selectedCrypto = 'BTC';
  double _exchangeRate = 0.0;
  double _amountCrypto = 0.0;
  bool _isCalculating = false;
  List<String> _supportedCryptos = ['BTC', 'ETH', 'USDT', 'USDC', 'BNB'];
  bool _loadingSupportedCryptos = true;

  @override
  void initState() {
    super.initState();
    _loadSupportedCryptocurrencies();
    _calculateAmounts();
  }

  @override
  void dispose() {
    _walletAddressController.dispose();
    super.dispose();
  }

  Future<void> _loadSupportedCryptocurrencies() async {
    setState(() {
      _loadingSupportedCryptos = false;
    });
  }

  void _calculateAmounts() {
    setState(() {
      _isCalculating = true;
    });

    // In a real app, this would fetch live exchange rates
    // For demo purposes, using mock rates
    const mockRates = {
      'BTC': 1500000.0, // 1 BTC = 1,500,000 NGN
      'ETH': 200000.0,  // 1 ETH = 200,000 NGN
      'USDT': 1560.0,   // 1 USDT = 1,560 NGN
      'USDC': 1560.0,   // 1 USDC = 1,560 NGN
      'BNB': 450000.0,  // 1 BNB = 450,000 NGN
    };

    setState(() {
      _exchangeRate = mockRates[_selectedCrypto] ?? 0.0;
      _amountCrypto = _exchangeRate > 0 ? widget.amountNgn / _exchangeRate : 0.0;
      _isCalculating = false;
    });
  }

  Future<void> _processCryptoPayment() async {
    if (_walletAddressController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Please enter your wallet address')),
      );
      return;
    }

    try {
      final cryptoPayment = await _paymentService.processCryptocurrencyPayment(
        transactionId: widget.transactionId,
        currency: _selectedCrypto,
        walletAddress: _walletAddressController.text,
        amountCrypto: _amountCrypto,
        amountNgn: widget.amountNgn,
        exchangeRate: _exchangeRate,
      );

      // Show success dialog
      await showDialog(
        context: context,
        builder: (context) => AlertDialog(
          title: Text('Payment Initiated'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Send ${_amountCrypto.toStringAsFixed(8)} $_selectedCrypto to:'),
              SizedBox(height: 8),
              Container(
                padding: EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.grey[100],
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  _walletAddressController.text,
                  style: TextStyle(fontFamily: 'monospace'),
                ),
              ),
              SizedBox(height: 8),
              Text(
                'Amount: ₦${NumberFormat('#,##0.00').format(widget.amountNgn)}',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
              SizedBox(height: 8),
              Text(
                'Exchange Rate: 1 $_selectedCrypto = ₦${NumberFormat('#,##0.00').format(_exchangeRate)}',
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('OK'),
            ),
          ],
        ),
      );
      
      // Navigate back or to success page
      Navigator.pop(context, cryptoPayment);
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to process cryptocurrency payment: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Cryptocurrency Payment'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Amount to Pay',
                      style: TextStyle(fontSize: 16, color: Colors.grey[600]),
                    ),
                    Text(
                      '₦${NumberFormat('#,##0.00').format(widget.amountNgn)}',
                      style: TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: Colors.green[700],
                      ),
                    ),
                    SizedBox(height: 16),
                    Text(
                      'Select Cryptocurrency',
                      style: TextStyle(fontWeight: FontWeight.w500),
                    ),
                    SizedBox(height: 8),
                    _loadingSupportedCryptos
                        ? LinearProgressIndicator()
                        : DropdownButtonFormField<String>(
                            value: _selectedCrypto,
                            decoration: InputDecoration(
                              border: OutlineInputBorder(),
                            ),
                            items: _supportedCryptos.map((crypto) {
                              return DropdownMenuItem(
                                value: crypto,
                                child: Text(crypto),
                              );
                            }).toList(),
                            onChanged: (value) {
                              if (value != null) {
                                setState(() {
                                  _selectedCrypto = value;
                                });
                                _calculateAmounts();
                              }
                            },
                          ),
                    SizedBox(height: 16),
                    Text(
                      'Your Wallet Address',
                      style: TextStyle(fontWeight: FontWeight.w500),
                    ),
                    SizedBox(height: 8),
                    TextFormField(
                      controller: _walletAddressController,
                      decoration: InputDecoration(
                        hintText: 'Enter your ${_selectedCrypto} wallet address',
                        border: OutlineInputBorder(),
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Please enter your wallet address';
                        }
                        return null;
                      },
                    ),
                    SizedBox(height: 16),
                    Card(
                      color: Colors.grey[50],
                      child: Padding(
                        padding: EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Payment Details',
                              style: TextStyle(fontWeight: FontWeight.bold),
                            ),
                            Divider(),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('NGN Amount:'),
                                Text(
                                  '₦${NumberFormat('#,##0.00').format(widget.amountNgn)}',
                                  style: TextStyle(fontWeight: FontWeight.w500),
                                ),
                              ],
                            ),
                            SizedBox(height: 8),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('Crypto Amount:'),
                                _isCalculating
                                    ? SizedBox(
                                        width: 20,
                                        height: 20,
                                        child: CircularProgressIndicator(strokeWidth: 2),
                                      )
                                    : Text(
                                        '${_amountCrypto.toStringAsFixed(8)} $_selectedCrypto',
                                        style: TextStyle(fontWeight: FontWeight.w500),
                                      ),
                              ],
                            ),
                            SizedBox(height: 8),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('Exchange Rate:'),
                                Text(
                                  '1 $_selectedCrypto = ₦${NumberFormat('#,##0.00').format(_exchangeRate)}',
                                  style: TextStyle(fontWeight: FontWeight.w500),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            SizedBox(height: 16),
            ElevatedButton(
              onPressed: _processCryptoPayment,
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green,
                foregroundColor: Colors.white,
                padding: EdgeInsets.symmetric(vertical: 16),
              ),
              child: Text(
                'Process Cryptocurrency Payment',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
      ),
    );
  }
}