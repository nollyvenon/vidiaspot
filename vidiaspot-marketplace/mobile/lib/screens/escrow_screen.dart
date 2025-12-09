// lib/screens/escrow_screen.dart
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/escrow_model.dart';
import '../services/smart_messaging_service.dart';

class EscrowScreen extends StatefulWidget {
  final int transactionId;
  final int adId;
  final int sellerId;
  final double amount;
  final String currency;

  const EscrowScreen({
    Key? key,
    required this.transactionId,
    required this.adId,
    required this.sellerId,
    required this.amount,
    this.currency = 'NGN',
  }) : super(key: key);

  @override
  _EscrowScreenState createState() => _EscrowScreenState();
}

class _EscrowScreenState extends State<EscrowScreen> {
  final SmartMessagingService _smartMessagingService = SmartMessagingService();
  Escrow? _escrow;
  bool _isLoading = true;
  bool _isReleasing = false;
  bool _isDisputing = false;

  @override
  void initState() {
    super.initState();
    _loadEscrow();
  }

  Future<void> _loadEscrow() async {
    setState(() {
      _isLoading = true;
    });

    try {
      // In a real implementation, we would fetch existing escrow if it exists
      // For now, create a new one
      final escrow = await _smartMessagingService.createEscrow(
        transactionId: widget.transactionId,
        adId: widget.adId,
        sellerUserId: widget.sellerId,
        amount: widget.amount,
        currency: widget.currency,
      );
      
      setState(() {
        _escrow = escrow;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to create escrow: $e')),
      );
    }
  }

  Future<void> _releaseFunds() async {
    if (_escrow == null) return;

    setState(() {
      _isReleasing = true;
    });

    try {
      final result = await _smartMessagingService.releaseEscrow(_escrow!.id);
      if (result['success']) {
        setState(() {
          _escrow = _escrow!.copyWith(status: 'released');
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Funds released successfully!')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to release funds: ${result['message']}')),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error releasing funds: $e')),
      );
    } finally {
      setState(() {
        _isReleasing = false;
      });
    }
  }

  Future<void> _openDispute() async {
    if (_escrow == null) return;

    await showDialog(
      context: context,
      builder: (context) => _DisputeDialog(
        onSubmit: (disputeDetails) async {
          setState(() {
            _isDisputing = true;
          });
          
          try {
            final result = await _smartMessagingService.resolveEscrowDispute(
              _escrow!.id,
              disputeDetails,
            );
            if (result['success']) {
              setState(() {
                _escrow = _escrow!.copyWith(
                  status: 'disputed',
                  disputeStatus: 'under_review',
                );
              });
              Navigator.pop(context);
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text('Dispute submitted successfully!')),
              );
            } else {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text('Failed to submit dispute: ${result['message']}')),
              );
            }
          } catch (e) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Error submitting dispute: $e')),
            );
          } finally {
            setState(() {
              _isDisputing = false;
            });
          }
        },
      ),
    );
  }

  Future<void> _verifyOnBlockchain() async {
    if (_escrow == null) return;

    try {
      final result = await _smartMessagingService.verifyEscrowOnBlockchain(_escrow!.id);
      if (result['success']) {
        setState(() {
          _escrow = _escrow!.copyWith(
            blockchainStatus: result['verification']['status'],
            blockchainVerificationData: result['verification'],
          );
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Verified on blockchain successfully!')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to verify on blockchain')),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error verifying on blockchain: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Escrow Protection'),
        backgroundColor: Colors.green,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : (_escrow == null
              ? Center(child: Text('Failed to load escrow information'))
              : Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Escrow status card
                      Container(
                        width: double.infinity,
                        padding: EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.green[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.green),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  'ESCROW STATUS',
                                  style: TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.green[700],
                                  ),
                                ),
                                Container(
                                  padding: EdgeInsets.symmetric(
                                      horizontal: 12, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: _escrow!.status == 'released'
                                        ? Colors.green
                                        : _escrow!.status == 'disputed'
                                            ? Colors.orange
                                            : Colors.blue,
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Text(
                                    _escrow!.status.toUpperCase(),
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            SizedBox(height: 12),
                            Text(
                              'Amount: ${NumberFormat.currency(locale: 'en_NG', symbol: '₦ ').format(_escrow!.amount)}',
                              style: TextStyle(
                                fontSize: 24,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 8),
                            Text(
                              'Protected by blockchain technology',
                              style: TextStyle(
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      ),
                      
                      SizedBox(height: 20),
                      
                      // Blockchain verification status
                      if (_escrow!.blockchainTransactionHash != null) ...[
                        Card(
                          child: Padding(
                            padding: EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Blockchain Verification',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                SizedBox(height: 8),
                                Row(
                                  children: [
                                    Icon(
                                      _escrow!.blockchainStatus == 'confirmed'
                                          ? Icons.check_circle
                                          : Icons.help_outline,
                                      color: _escrow!.blockchainStatus == 'confirmed'
                                          ? Colors.green
                                          : Colors.orange,
                                    ),
                                    SizedBox(width: 8),
                                    Text(
                                      _escrow!.blockchainStatus ?? 'Not verified',
                                      style: TextStyle(
                                        color: _escrow!.blockchainStatus == 'confirmed'
                                            ? Colors.green
                                            : Colors.orange,
                                        fontWeight: FontWeight.w500,
                                      ),
                                    ),
                                  ],
                                ),
                                SizedBox(height: 8),
                                if (_escrow!.blockchainTransactionHash != null) ...[
                                  Text(
                                    'Transaction Hash:',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                  SelectableText(
                                    _escrow!.blockchainTransactionHash!,
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontFamily: 'monospace',
                                      color: Colors.grey[800],
                                    ),
                                  ),
                                ],
                                SizedBox(height: 12),
                                ElevatedButton(
                                  onPressed: _verifyOnBlockchain,
                                  child: Text('Verify on Blockchain'),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: Colors.blue,
                                    foregroundColor: Colors.white,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        SizedBox(height: 20),
                      ],
                      
                      // Action buttons based on status
                      if (_escrow!.status == 'pending') ...[
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            onPressed: _isReleasing ? null : _releaseFunds,
                            icon: _isReleasing
                                ? SizedBox(
                                    width: 20,
                                    height: 20,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                    ),
                                  )
                                : Icon(Icons.done),
                            label: Text('Release Funds'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.green,
                              foregroundColor: Colors.white,
                              padding: EdgeInsets.symmetric(vertical: 16),
                            ),
                          ),
                        ),
                        SizedBox(height: 12),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            onPressed: _openDispute,
                            icon: Icon(Icons.report_problem),
                            label: Text('Raise Dispute'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.orange,
                              foregroundColor: Colors.white,
                              padding: EdgeInsets.symmetric(vertical: 16),
                            ),
                          ),
                        ),
                      ] else if (_escrow!.status == 'released') ...[
                        Card(
                          color: Colors.green[50],
                          child: Padding(
                            padding: EdgeInsets.all(16),
                            child: Column(
                              children: [
                                Icon(
                                  Icons.check_circle,
                                  color: Colors.green,
                                  size: 48,
                                ),
                                SizedBox(height: 8),
                                Text(
                                  'Funds Released',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.green[700],
                                  ),
                                ),
                                SizedBox(height: 8),
                                Text(
                                  'The funds have been successfully transferred to the seller.',
                                  textAlign: TextAlign.center,
                                ),
                              ],
                            ),
                          ),
                        ),
                      ] else if (_escrow!.status == 'disputed') ...[
                        Card(
                          color: Colors.orange[50],
                          child: Padding(
                            padding: EdgeInsets.all(16),
                            child: Column(
                              children: [
                                Icon(
                                  Icons.report_problem,
                                  color: Colors.orange,
                                  size: 48,
                                ),
                                SizedBox(height: 8),
                                Text(
                                  'Dispute Active',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.orange[700],
                                  ),
                                ),
                                SizedBox(height: 8),
                                Text(
                                  'This transaction is under review due to a dispute.',
                                  textAlign: TextAlign.center,
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                      
                      SizedBox(height: 16),
                      
                      // Information about escrow protection
                      Card(
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Icon(
                                Icons.shield,
                                color: Colors.blue,
                                size: 32,
                              ),
                              SizedBox(height: 8),
                              Text(
                                'How Escrow Protection Works',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              SizedBox(height: 8),
                              Text(
                                '• Your payment is securely held in escrow until you confirm receipt of the item\n• The seller receives payment only after your confirmation\n• In case of disputes, our AI system analyzes evidence\n• Blockchain technology verifies all transactions',
                                style: TextStyle(color: Colors.grey[700]),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                )),
    );
  }
}

// Helper extension to update escrow properties
extension EscrowExtension on Escrow {
  Escrow copyWith({
    int? id,
    int? transactionId,
    int? adId,
    int? buyerUserId,
    int? sellerUserId,
    double? amount,
    String? currency,
    String? status,
    String? disputeStatus,
    DateTime? releaseDate,
    DateTime? disputeResolvedAt,
    Map<String, dynamic>? disputeDetails,
    Map<String, dynamic>? releaseConditions,
    String? notes,
    String? blockchainTransactionHash,
    String? blockchainContractAddress,
    String? blockchainStatus,
    Map<String, dynamic>? blockchainVerificationData,
  }) {
    return Escrow(
      id: id ?? this.id,
      transactionId: transactionId ?? this.transactionId,
      adId: adId ?? this.adId,
      buyerUserId: buyerUserId ?? this.buyerUserId,
      sellerUserId: sellerUserId ?? this.sellerUserId,
      amount: amount ?? this.amount,
      currency: currency ?? this.currency,
      status: status ?? this.status,
      disputeStatus: disputeStatus ?? this.disputeStatus,
      releaseDate: releaseDate ?? this.releaseDate,
      disputeResolvedAt: disputeResolvedAt ?? this.disputeResolvedAt,
      disputeDetails: disputeDetails ?? this.disputeDetails,
      releaseConditions: releaseConditions ?? this.releaseConditions,
      notes: notes ?? this.notes,
      blockchainTransactionHash: blockchainTransactionHash ?? this.blockchainTransactionHash,
      blockchainContractAddress: blockchainContractAddress ?? this.blockchainContractAddress,
      blockchainStatus: blockchainStatus ?? this.blockchainStatus,
      blockchainVerificationData: blockchainVerificationData ?? this.blockchainVerificationData,
    );
  }
}

class _DisputeDialog extends StatefulWidget {
  final Function(Map<String, dynamic>) onSubmit;

  const _DisputeDialog({Key? key, required this.onSubmit}) : super(key: key);

  @override
  _DisputeDialogState createState() => _DisputeDialogState();
}

class _DisputeDialogState extends State<_DisputeDialog> {
  final _formKey = GlobalKey<FormState>();
  final _reasonController = TextEditingController();
  String _selectedParty = 'buyer';
  List<String> _evidence = [];

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  void _addEvidence() {
    if (_reasonController.text.isNotEmpty) {
      setState(() {
        _evidence.add(_reasonController.text);
        _reasonController.clear();
      });
    }
  }

  void _removeEvidence(int index) {
    setState(() {
      _evidence.removeAt(index);
    });
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: Text('Raise Dispute'),
      content: Container(
        width: double.maxFinite,
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Describe the issue with this transaction',
                style: TextStyle(fontWeight: FontWeight.w500),
              ),
              SizedBox(height: 16),
              
              // Reason for dispute
              TextFormField(
                controller: _reasonController,
                decoration: InputDecoration(
                  labelText: 'Reason for dispute',
                  hintText: 'Describe what went wrong...',
                  border: OutlineInputBorder(),
                ),
                maxLines: 3,
              ),
              SizedBox(height: 12),
              
              // Add evidence button
              TextButton.icon(
                onPressed: _addEvidence,
                icon: Icon(Icons.add),
                label: Text('Add Evidence'),
              ),
              
              // Evidence list
              if (_evidence.isNotEmpty) ...[
                SizedBox(height: 8),
                Container(
                  height: 150,
                  child: ListView.builder(
                    shrinkWrap: true,
                    itemCount: _evidence.length,
                    itemBuilder: (context, index) {
                      return Card(
                        child: ListTile(
                          title: Text(_evidence[index]),
                          trailing: IconButton(
                            icon: Icon(Icons.delete),
                            onPressed: () => _removeEvidence(index),
                          ),
                        ),
                      );
                    },
                  ),
                ),
              ],
              
              SizedBox(height: 16),
              
              // Party selector
              Text('Who is at fault?', style: TextStyle(fontWeight: FontWeight.w500)),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  Expanded(
                    child: RadioListTile<String>(
                      title: Text('Buyer'),
                      value: 'buyer',
                      groupValue: _selectedParty,
                      onChanged: (value) {
                        setState(() {
                          _selectedParty = value!;
                        });
                      },
                    ),
                  ),
                  Expanded(
                    child: RadioListTile<String>(
                      title: Text('Seller'),
                      value: 'seller',
                      groupValue: _selectedParty,
                      onChanged: (value) {
                        setState(() {
                          _selectedParty = value!;
                        });
                      },
                    ),
                  ),
                ],
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
          onPressed: () {
            if (_evidence.isNotEmpty) {
              widget.onSubmit({
                'reason': 'User reported issue',
                'evidence': _evidence.map((e) => {'description': e, 'party': _selectedParty}).toList(),
              });
            }
          },
          child: Text('Submit Dispute'),
        ),
      ],
    );
  }
}