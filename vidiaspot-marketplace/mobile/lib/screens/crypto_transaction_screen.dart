// lib/screens/crypto_transaction_screen.dart
import 'package:flutter/material.dart';
import '../services/qr_code_service.dart';
import 'qr_code_scanner_screen.dart';

class CryptoTransactionScreen extends StatefulWidget {
  final String? initialAddress;
  final String? initialCryptoSymbol;
  
  const CryptoTransactionScreen({Key? key, this.initialAddress, this.initialCryptoSymbol}) 
      : super(key: key);

  @override
  _CryptoTransactionScreenState createState() => _CryptoTransactionScreenState();
}

class _CryptoTransactionScreenState extends State<CryptoTransactionScreen> {
  String _recipientAddress = '';
  String _cryptoSymbol = 'BTC';
  double _amount = 0.0;
  String _description = '';
  String _referenceId = '';
  
  // Form controllers
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _amountController = TextEditingController();
  final TextEditingController _descriptionController = TextEditingController();
  
  bool _isSending = false;

  @override
  void initState() {
    super.initState();
    _recipientAddress = widget.initialAddress ?? '';
    _cryptoSymbol = widget.initialCryptoSymbol ?? 'BTC';
    
    _addressController.text = _recipientAddress;
  }

  @override
  void dispose() {
    _addressController.dispose();
    _amountController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  Future<void> _scanQRCode() async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => QrCodeScannerScreen(),
      ),
    );

    if (result != null && result is Map<String, dynamic>) {
      if (result['type'] == 'crypto_address') {
        setState(() {
          _recipientAddress = result['address'] ?? '';
          _cryptoSymbol = result['cryptoSymbol'] ?? 'BTC';
          _amount = result['amount'] ?? 0.0;
          _addressController.text = _recipientAddress;
          _amountController.text = _amount.toString();
        });
      } else if (result['type'] == 'crypto_payment') {
        setState(() {
          _recipientAddress = result['address'] ?? '';
          _cryptoSymbol = result['cryptoSymbol'] ?? 'BTC';
          _amount = result['amount'] ?? 0.0;
          _addressController.text = _recipientAddress;
          _amountController.text = _amount.toString();
        });
      }
    }
  }

  Future<void> _sendTransaction() async {
    if (_recipientAddress.isEmpty || _amount <= 0) {
      _showError('Please enter a valid address and amount');
      return;
    }

    // Show confirmation dialog
    bool confirmed = await showDialog<bool>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Confirm Transaction'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Sending $_amount $_cryptoSymbol'),
              Text('To: $_recipientAddress'),
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
      setState(() {
        _isSending = true;
      });

      try {
        // Simulate sending transaction
        await Future.delayed(const Duration(seconds: 2));

        // In a real app, this would call the backend API to send the transaction
        // For now, we'll just show success
        if (mounted) {
          _showSuccess('Transaction sent successfully!');
          setState(() {
            _isSending = false;
          });
          
          // Clear form after successful transaction
          Future.delayed(const Duration(seconds: 2), () {
            if (mounted) {
              setState(() {
                _recipientAddress = '';
                _amount = 0.0;
                _description = '';
                _addressController.clear();
                _amountController.clear();
                _descriptionController.clear();
              });
            }
          });
        }
      } catch (e) {
        if (mounted) {
          _showError('Error sending transaction: $e');
          setState(() {
            _isSending = false;
          });
        }
      }
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(color: Colors.white)),
        backgroundColor: Colors.red,
        duration: const Duration(seconds: 3),
      ),
    );
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(color: Colors.white)),
        backgroundColor: Colors.green,
        duration: const Duration(seconds: 3),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Send $_cryptoSymbol'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // QR Code Scanner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(
                children: [
                  const Text(
                    'Scan QR Code',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Scan a QR code containing a crypto address or payment request',
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey[600],
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 12),
                  ElevatedButton.icon(
                    onPressed: _scanQRCode,
                    icon: const Icon(Icons.qr_code_scanner),
                    label: const Text('Scan QR Code'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.blue,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(
                        horizontal: 20,
                        vertical: 12,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Payment form
            Text(
              'Send $_cryptoSymbol',
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 16),
            
            // Recipient address field
            TextField(
              controller: _addressController,
              decoration: InputDecoration(
                labelText: 'Recipient Address',
                border: const OutlineInputBorder(),
                hintText: 'Enter crypto address',
                suffixIcon: IconButton(
                  icon: const Icon(Icons.paste),
                  onPressed: () async {
                    // In a real app, this would paste from clipboard
                  },
                ),
              ),
              onChanged: (value) {
                setState(() {
                  _recipientAddress = value;
                });
              },
            ),
            
            const SizedBox(height: 16),
            
            // Amount field
            TextField(
              controller: _amountController,
              decoration: InputDecoration(
                labelText: 'Amount',
                border: const OutlineInputBorder(),
                hintText: 'Enter amount to send',
                prefixText: '$_cryptoSymbol ',
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
            
            // Description field
            TextField(
              controller: _descriptionController,
              decoration: const InputDecoration(
                labelText: 'Description (Optional)',
                border: OutlineInputBorder(),
                hintText: 'Enter payment description',
              ),
              maxLines: 3,
              onChanged: (value) {
                setState(() {
                  _description = value;
                });
              },
            ),
            
            const SizedBox(height: 24),
            
            // Preview of transaction
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey[100],
                borderRadius: BorderRadius.circular(8),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Transaction Preview',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Amount:'),
                      Text(
                        '$_amount $_cryptoSymbol',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('To:'),
                      Expanded(
                        child: Text(
                          _recipientAddress.length > 20
                              ? '${_recipientAddress.substring(0, 10)}...${_recipientAddress.substring(_recipientAddress.length - 10)}'
                              : _recipientAddress,
                          style: const TextStyle(fontSize: 14),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  if (_description.isNotEmpty) ...[
                    const SizedBox(height: 4),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Description:'),
                        Expanded(
                          child: Text(
                            _description,
                            style: const TextStyle(fontSize: 14),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Send button
            SizedBox(
              width: double.infinity,
              height: 60,
              child: ElevatedButton(
                onPressed: _isSending ? null : _sendTransaction,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                  foregroundColor: Colors.white,
                ),
                child: _isSending
                    ? const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          SizedBox(
                            width: 16,
                            height: 16,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          ),
                          SizedBox(width: 12),
                          Text(
                            'Sending...',
                            style: TextStyle(fontSize: 16),
                          ),
                        ],
                      )
                    : const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.send),
                          SizedBox(width: 12),
                          Text(
                            'Send Transaction',
                            style: TextStyle(fontSize: 16),
                          ),
                        ],
                      ),
              ),
            ),
            
            const SizedBox(height: 16),
            
            // Quick amount buttons
            const Text('Quick Amounts', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: [0.1, 0.5, 1.0, 5.0].map((amount) {
                return FilterChip(
                  label: Text('${amount}$_cryptoSymbol'),
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
    );
  }
}