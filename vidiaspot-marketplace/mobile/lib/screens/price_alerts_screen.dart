// lib/screens/price_alerts_screen.dart
import 'package:flutter/material.dart';
import '../services/push_notification_service.dart';

class PriceAlertsScreen extends StatefulWidget {
  const PriceAlertsScreen({Key? key}) : super(key: key);

  @override
  _PriceAlertsScreenState createState() => _PriceAlertsScreenState();
}

class _PriceAlertsScreenState extends State<PriceAlertsScreen> {
  final PushNotificationService _notificationService = PushNotificationService();
  List<PriceAlert> _alerts = [];
  
  // Mock data for price alerts
  @override
  void initState() {
    super.initState();
    _loadAlerts();
  }

  void _loadAlerts() {
    // Initialize with some mock alerts
    _alerts = [
      PriceAlert(
        id: 1,
        cryptoSymbol: 'BTC',
        targetPrice: 75000.0,
        currentPrice: 74250.0,
        alertDirection: 'above', // 'above' or 'below'
        isActive: true,
      ),
      PriceAlert(
        id: 2,
        cryptoSymbol: 'ETH',
        targetPrice: 3200.0,
        currentPrice: 3150.0,
        alertDirection: 'above',
        isActive: true,
      ),
      PriceAlert(
        id: 3,
        cryptoSymbol: 'USDT',
        targetPrice: 1.01,
        currentPrice: 1.002,
        alertDirection: 'above',
        isActive: false,
      ),
    ];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Price Alerts'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: _createNewAlert,
          ),
        ],
      ),
      body: ListView.builder(
        itemCount: _alerts.length,
        itemBuilder: (context, index) {
          return _buildAlertCard(_alerts[index]);
        },
      ),
    );
  }

  Widget _buildAlertCard(PriceAlert alert) {
    bool shouldTrigger = _shouldTriggerAlert(alert);
    
    return Card(
      margin: const EdgeInsets.all(8),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${alert.cryptoSymbol} Price Alert',
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Switch(
                  value: alert.isActive,
                  onChanged: (value) {
                    setState(() {
                      alert.isActive = value;
                    });
                    _toggleAlertSubscription(alert, value);
                  },
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              'Target: \$${alert.targetPrice.toStringAsFixed(2)}',
              style: const TextStyle(
                fontSize: 16,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Current: \$${alert.currentPrice.toStringAsFixed(2)}',
              style: TextStyle(
                fontSize: 16,
                color: shouldTrigger ? Colors.green : Colors.grey,
                fontWeight: shouldTrigger ? FontWeight.bold : FontWeight.normal,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Alert when price goes ${alert.alertDirection} target',
              style: const TextStyle(
                fontSize: 14,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                TextButton(
                  onPressed: () => _editAlert(alert),
                  child: const Text('Edit'),
                ),
                TextButton(
                  onPressed: () => _deleteAlert(alert.id),
                  child: const Text(
                    'Delete',
                    style: TextStyle(color: Colors.red),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  bool _shouldTriggerAlert(PriceAlert alert) {
    if (alert.alertDirection == 'above') {
      return alert.currentPrice >= alert.targetPrice;
    } else {
      return alert.currentPrice <= alert.targetPrice;
    }
  }

  void _createNewAlert() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return _createAlertDialog();
      },
    );
  }

  void _editAlert(PriceAlert alert) {
    // For now, just show the creation dialog again with the alert's values
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return _createAlertDialog(editingAlert: alert);
      },
    );
  }

  void _deleteAlert(int alertId) {
    setState(() {
      _alerts.removeWhere((alert) => alert.id == alertId);
    });
  }

  AlertDialog _createAlertDialog({PriceAlert? editingAlert}) {
    String cryptoSymbol = editingAlert?.cryptoSymbol ?? 'BTC';
    double targetPrice = editingAlert?.targetPrice ?? 0.0;
    String alertDirection = editingAlert?.alertDirection ?? 'above';
    bool isActive = editingAlert?.isActive ?? true;

    return AlertDialog(
      title: Text(editingAlert != null ? 'Edit Alert' : 'Create New Alert'),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            DropdownButtonFormField<String>(
              value: cryptoSymbol,
              decoration: const InputDecoration(
                labelText: 'Cryptocurrency',
              ),
              items: const [
                DropdownMenuItem(value: 'BTC', child: Text('Bitcoin (BTC)')),
                DropdownMenuItem(value: 'ETH', child: Text('Ethereum (ETH)')),
                DropdownMenuItem(value: 'USDT', child: Text('Tether (USDT)')),
                DropdownMenuItem(value: 'USDC', child: Text('USD Coin (USDC)')),
                DropdownMenuItem(value: 'BNB', child: Text('Binance Coin (BNB)')),
                DropdownMenuItem(value: 'XRP', child: Text('Ripple (XRP)')),
              ],
              onChanged: (value) {
                if (value != null) cryptoSymbol = value;
              },
            ),
            const SizedBox(height: 16),
            TextField(
              decoration: const InputDecoration(
                labelText: 'Target Price (USD)',
                prefixText: '\$',
              ),
              keyboardType: TextInputType.number,
              onChanged: (value) {
                double? parsedValue = double.tryParse(value);
                if (parsedValue != null) targetPrice = parsedValue;
              },
              controller: TextEditingController(text: targetPrice.toString()),
            ),
            const SizedBox(height: 16),
            SegmentedButton<String>(
              segments: const [
                ButtonSegment(value: 'above', label: Text('Above')),
                ButtonSegment(value: 'below', label: Text('Below')),
              ],
              selected: {alertDirection},
              onSelectionChanged: (newSelection) {
                alertDirection = newSelection.first;
              },
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
          onPressed: () {
            if (editingAlert != null) {
              // Update existing alert
              editingAlert.cryptoSymbol = cryptoSymbol;
              editingAlert.targetPrice = targetPrice;
              editingAlert.alertDirection = alertDirection;
              editingAlert.isActive = isActive;
            } else {
              // Create new alert
              int newId = _alerts.isEmpty ? 1 : _alerts.last.id + 1;
              _alerts.add(PriceAlert(
                id: newId,
                cryptoSymbol: cryptoSymbol,
                targetPrice: targetPrice,
                currentPrice: 0.0, // Will be updated with real-time data
                alertDirection: alertDirection,
                isActive: isActive,
              ));
            }
            Navigator.of(context).pop();
            setState(() {});
          },
          child: Text(editingAlert != null ? 'Save' : 'Create'),
        ),
      ],
    );
  }

  void _toggleAlertSubscription(PriceAlert alert, bool subscribe) async {
    if (subscribe) {
      await _notificationService.subscribeToPriceAlerts(alert.cryptoSymbol);
    } else {
      await _notificationService.unsubscribeFromPriceAlerts(alert.cryptoSymbol);
    }
  }
}

class PriceAlert {
  int id;
  String cryptoSymbol;
  double targetPrice;
  double currentPrice;
  String alertDirection; // 'above' or 'below'
  bool isActive;

  PriceAlert({
    required this.id,
    required this.cryptoSymbol,
    required this.targetPrice,
    required this.currentPrice,
    required this.alertDirection,
    required this.isActive,
  });
}