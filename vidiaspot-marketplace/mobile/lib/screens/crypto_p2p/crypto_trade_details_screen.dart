// lib/screens/crypto_p2p/crypto_trade_details_screen.dart
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/crypto_p2p_service.dart';
import '../../models/crypto_p2p/crypto_trade_model.dart';

class CryptoTradeDetailsScreen extends StatefulWidget {
  final CryptoTrade trade;
  
  const CryptoTradeDetailsScreen({
    Key? key,
    required this.trade,
  }) : super(key: key);

  @override
  _CryptoTradeDetailsScreenState createState() => _CryptoTradeDetailsScreenState();
}

class _CryptoTradeDetailsScreenState extends State<CryptoTradeDetailsScreen> {
  final CryptoP2PService _cryptoP2PService = CryptoP2PService();
  late CryptoTrade _currentTrade;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _currentTrade = widget.trade;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Trade #${_currentTrade.tradeReference}'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: ListView(
          children: [
            // Trade Status and Info
            _buildTradeStatusCard(),
            
            const SizedBox(height: 20),
            
            // Trade Details
            _buildTradeDetailsCard(),
            
            const SizedBox(height: 20),
            
            // Payment Info
            _buildPaymentInfoCard(),
            
            const SizedBox(height: 20),
            
            // Actions
            _buildActionButtons(),
            
            const SizedBox(height: 20),
            
            // Security and Escrow Info
            _buildSecurityInfoCard(),
          ],
        ),
      ),
    );
  }

  Widget _buildTradeStatusCard() {
    Color statusColor;
    String statusText;
    
    switch (_currentTrade.status) {
      case 'pending':
        statusColor = Colors.orange;
        statusText = 'Pending';
        break;
      case 'in_escrow':
        statusColor = Colors.blue;
        statusText = 'In Escrow';
        break;
      case 'payment_confirmed':
        statusColor = Colors.purple;
        statusText = 'Payment Confirmed';
        break;
      case 'completed':
        statusColor = Colors.green;
        statusText = 'Completed';
        break;
      case 'cancelled':
        statusColor = Colors.red;
        statusText = 'Cancelled';
        break;
      default:
        statusColor = Colors.grey;
        statusText = _currentTrade.status;
    }

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: statusColor.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: statusColor),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: statusColor,
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(
              _getStatusIcon(),
              color: Colors.white,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Status: $statusText',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: statusColor,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Escrow: ${_currentTrade.escrowStatus}',
                  style: const TextStyle(
                    fontSize: 14,
                    color: Colors.grey,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTradeDetailsCard() {
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
          _buildDetailRow('Trade Type:', _currentTrade.tradeType.toUpperCase()),
          _buildDetailRow('Crypto:', '${_currentTrade.cryptoAmount} ${_currentTrade.cryptoCurrency}'),
          _buildDetailRow('Fiat:', '${_currentTrade.fiatCurrency} ${_currentTrade.fiatAmount.toStringAsFixed(2)}'),
          _buildDetailRow('Exchange Rate:', '${_currentTrade.fiatCurrency} ${_currentTrade.exchangeRate.toStringAsFixed(2)} per ${_currentTrade.cryptoCurrency}'),
          _buildDetailRow('Security Level:', '${_currentTrade.securityLevel}'),
        ],
      ),
    );
  }

  Widget _buildPaymentInfoCard() {
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
            'Payment Information',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          _buildDetailRow('Payment Method:', _formatPaymentMethod(_currentTrade.paymentMethod)),
          _buildDetailRow('Escrow Address:', _currentTrade.escrowAddress),
          if (_currentTrade.paymentConfirmedAt != null)
            _buildDetailRow('Payment Confirmed:', DateFormat('MMM dd, yyyy - HH:mm').format(_currentTrade.paymentConfirmedAt!)),
          if (_currentTrade.tradeCompletedAt != null)
            _buildDetailRow('Trade Completed:', DateFormat('MMM dd, yyyy - HH:mm').format(_currentTrade.tradeCompletedAt!)),
        ],
      ),
    );
  }

  Widget _buildSecurityInfoCard() {
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
            'Security Information',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          _buildDetailRow('Verification Required:', _currentTrade.verificationRequired ? 'Yes' : 'No'),
          _buildDetailRow('Security Level:', _currentTrade.securityLevel.toString()),
          if (_currentTrade.disputeId != null)
            _buildDetailRow('Dispute ID:', _currentTrade.disputeId.toString()),
          if (_currentTrade.disputeResolution != null)
            _buildDetailRow('Dispute Resolution:', _currentTrade.disputeResolution!),
        ],
      ),
    );
  }

  Widget _buildActionButtons() {
    final isBuyer = _currentTrade.buyerId == _getCurrentUserId(); // Placeholder
    final canConfirmPayment = isBuyer && _currentTrade.status == 'pending';
    final canReleaseCrypto = !isBuyer && _currentTrade.status == 'payment_confirmed';
    
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
            'Actions',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          if (canConfirmPayment)
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _confirmPayment,
                child: _isLoading 
                    ? const CircularProgressIndicator()
                    : const Text('Confirm Payment'),
              ),
            )
          else if (canReleaseCrypto)
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _releaseCrypto,
                child: _isLoading 
                    ? const CircularProgressIndicator()
                    : const Text('Release Crypto'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                ),
              ),
            )
          else
            const Text(
              'No actions available at this time',
              style: TextStyle(
                color: Colors.grey,
                fontStyle: FontStyle.italic,
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              label,
              style: const TextStyle(
                fontWeight: FontWeight.w500,
                color: Colors.grey,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }

  IconData _getStatusIcon() {
    switch (_currentTrade.status) {
      case 'pending':
        return Icons.hourglass_empty;
      case 'in_escrow':
        return Icons.lock;
      case 'payment_confirmed':
        return Icons.check_circle;
      case 'completed':
        return Icons.done_all;
      case 'cancelled':
        return Icons.cancel;
      default:
        return Icons.info;
    }
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

  int _getCurrentUserId() {
    // Placeholder - in a real app, this would return the current user's ID
    return 1;
  }

  void _confirmPayment() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final updatedTrade = await _cryptoP2PService.confirmPayment(_currentTrade.id);
      
      setState(() {
        _currentTrade = updatedTrade;
      });

      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Payment confirmed successfully!'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Failed to confirm payment: $e'),
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

  void _releaseCrypto() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final updatedTrade = await _cryptoP2PService.releaseCrypto(_currentTrade.id);
      
      setState(() {
        _currentTrade = updatedTrade;
      });

      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Crypto released successfully!'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Failed to release crypto: $e'),
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