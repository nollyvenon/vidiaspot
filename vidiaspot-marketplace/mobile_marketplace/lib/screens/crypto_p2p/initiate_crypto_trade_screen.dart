// lib/screens/crypto_p2p/initiate_crypto_trade_screen.dart
import 'package:flutter/material.dart';
import '../../services/crypto_p2p_service.dart';
import '../../models/crypto_p2p/crypto_listing_model.dart';
import '../../models/crypto_p2p/crypto_trade_model.dart';

class InitiateCryptoTradeScreen extends StatefulWidget {
  final CryptoListing listing;
  
  const InitiateCryptoTradeScreen({
    Key? key,
    required this.listing,
  }) : super(key: key);

  @override
  _InitiateCryptoTradeScreenState createState() => _InitiateCryptoTradeScreenState();
}

class _InitiateCryptoTradeScreenState extends State<InitiateCryptoTradeScreen> {
  final _formKey = GlobalKey<FormState>();
  final CryptoP2PService _cryptoP2PService = CryptoP2PService();
  
  double _cryptoAmount = 0.0;
  String _paymentMethod = '';
  bool _isLoading = false;
  List<String> _availablePaymentMethods = [];
  
  @override
  void initState() {
    super.initState();
    _availablePaymentMethods = widget.listing.paymentMethods;
    if (_availablePaymentMethods.isNotEmpty) {
      _paymentMethod = _availablePaymentMethods.first;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Trade ${widget.listing.cryptoCurrency}'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              // Listing Summary
              _buildListingSummary(),
              
              const SizedBox(height: 20),
              
              // Trade Details
              _buildTradeDetails(),
              
              const SizedBox(height: 20),
              
              // Payment Method
              _buildPaymentMethodSelector(),
              
              const SizedBox(height: 30),
              
              // Trade Summary
              _buildTradeSummary(),
              
              const SizedBox(height: 30),
              
              // Initiate Trade Button
              _buildInitiateTradeButton(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildListingSummary() {
    final isBuyer = widget.listing.tradeType == 'sell'; // If listing is 'sell', then trader is 'buying'
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Listing Details',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Price per unit:',
                style: TextStyle(fontSize: 14),
              ),
              Text(
                '${widget.listing.fiatCurrency} ${widget.listing.pricePerUnit.toStringAsFixed(2)}',
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Colors.green,
                ),
              ),
            ],
          ),
          const SizedBox(height: 5),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Min trade:',
                style: TextStyle(fontSize: 14),
              ),
              Text(
                '${widget.listing.fiatCurrency} ${widget.listing.minTradeAmount.toStringAsFixed(2)}',
                style: const TextStyle(fontSize: 14),
              ),
            ],
          ),
          const SizedBox(height: 5),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Max trade:',
                style: TextStyle(fontSize: 14),
              ),
              Text(
                '${widget.listing.fiatCurrency} ${widget.listing.maxTradeAmount.toStringAsFixed(2)}',
                style: const TextStyle(fontSize: 14),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 8,
                  vertical: 4,
                ),
                decoration: BoxDecoration(
                  color: isBuyer ? Colors.green : Colors.red,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  isBuyer ? 'Buy ${widget.listing.cryptoCurrency}' : 'Sell ${widget.listing.cryptoCurrency}',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              const Icon(
                Icons.star,
                color: Colors.amber,
                size: 16,
              ),
              Text(
                'Score: ${widget.listing.reputationScore.toStringAsFixed(1)} (${widget.listing.tradeCount} trades)',
                style: const TextStyle(
                  fontSize: 12,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildTradeDetails() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Trade Details',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          TextFormField(
            decoration: InputDecoration(
              labelText: 'Amount of ${widget.listing.cryptoCurrency} to ${widget.listing.tradeType == 'sell' ? 'buy' : 'sell'}',
              border: const OutlineInputBorder(),
              prefixText: '${widget.listing.cryptoCurrency} ',
            ),
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            validator: (value) {
              if (value == null || value.isEmpty) {
                return 'Please enter an amount';
              }
              final amount = double.tryParse(value);
              if (amount == null || amount <= 0) {
                return 'Please enter a valid amount';
              }
              
              final fiatAmount = amount * widget.listing.pricePerUnit;
              if (fiatAmount < widget.listing.minTradeAmount) {
                return 'Trade amount below minimum (${widget.listing.fiatCurrency} ${widget.listing.minTradeAmount.toStringAsFixed(2)})';
              }
              
              if (fiatAmount > widget.listing.maxTradeAmount) {
                return 'Trade amount above maximum (${widget.listing.fiatCurrency} ${widget.listing.maxTradeAmount.toStringAsFixed(2)})';
              }
              
              // Check available amount if it's a sell listing
              if (widget.listing.tradeType == 'sell' && amount > widget.listing.availableAmount) {
                return 'Not enough available amount';
              }
              
              return null;
            },
            onChanged: (value) {
              final amount = double.tryParse(value);
              if (amount != null) {
                setState(() {
                  _cryptoAmount = amount;
                });
              }
            },
          ),
          const SizedBox(height: 10),
          Text(
            'Equivalent: ${widget.listing.fiatCurrency} ${(widget.listing.pricePerUnit * _cryptoAmount).toStringAsFixed(2)}',
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w500,
              color: Colors.blue[700],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentMethodSelector() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Payment Method',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          if (_availablePaymentMethods.isNotEmpty)
            DropdownButtonFormField<String>(
              value: _paymentMethod,
              decoration: const InputDecoration(
                labelText: 'Select Payment Method',
                border: OutlineInputBorder(),
              ),
              items: _availablePaymentMethods.map((method) {
                return DropdownMenuItem(
                  value: _formatPaymentMethod(method),
                  child: Text(_formatPaymentMethod(method)),
                );
              }).toList(),
              onChanged: (value) {
                if (value != null) {
                  setState(() {
                    _paymentMethod = value;
                  });
                }
              },
            )
          else
            const Text(
              'No payment methods available for this listing',
              style: TextStyle(color: Colors.red),
            ),
        ],
      ),
    );
  }

  Widget _buildTradeSummary() {
    final fiatAmount = _cryptoAmount * widget.listing.pricePerUnit;
    final isBuyer = widget.listing.tradeType == 'sell';
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.blue[50],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Trade Summary',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                isBuyer 
                    ? 'You will receive:' 
                    : 'You will pay:',
                style: const TextStyle(fontSize: 14),
              ),
              Text(
                '${widget.listing.cryptoCurrency} ${_cryptoAmount.toStringAsFixed(6)}',
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: 5),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                isBuyer 
                    ? 'You will pay:' 
                    : 'You will receive:',
                style: const TextStyle(fontSize: 14),
              ),
              Text(
                '${widget.listing.fiatCurrency} ${fiatAmount.toStringAsFixed(2)}',
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: 5),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Payment method:',
                style: TextStyle(fontSize: 14),
              ),
              Text(
                _formatPaymentMethod(_paymentMethod),
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildInitiateTradeButton() {
    return SizedBox(
      height: 50,
      child: ElevatedButton(
        onPressed: _isLoading ? null : _initiateTrade,
        child: _isLoading 
            ? const CircularProgressIndicator()
            : Text('Initiate ${widget.listing.tradeType == 'sell' ? 'Purchase' : 'Sale'}'),
      ),
    );
  }

  String _formatPaymentMethod(String method) {
    switch (method) {
      case 'bank_transfer':
        return 'Bank Transfer';
      case 'mobile_money':
        return 'Mobile Money';
      case 'cash':
        return 'Cash';
      case 'online_wallet':
        return 'Online Wallet';
      default:
        return method;
    }
  }

  void _initiateTrade() async {
    if (_formKey.currentState!.validate()) {
      setState(() {
        _isLoading = true;
      });

      try {
        final trade = await _cryptoP2PService.initiateTrade(
          listingId: widget.listing.id,
          cryptoAmount: _cryptoAmount,
          paymentMethod: _paymentMethod.toLowerCase().replaceAll(' ', '_'),
        );

        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Trade initiated successfully!'),
              backgroundColor: Colors.green,
            ),
          );
          
          // Navigate to trade details screen
          Navigator.pushReplacementNamed(
            context,
            '/trade-details',
            arguments: {'trade': trade},
          );
        }
      } catch (e) {
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Failed to initiate trade: $e'),
              backgroundColor: Colors.red,
            ),
          );
        }
      } finally {
        if (context.mounted) {
          setState(() {
            _isLoading = false;
          });
        }
      }
    }
  }
}