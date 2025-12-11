// lib/screens/mobile_trading_screen.dart
import 'package:flutter/material.dart';
import 'package:syncfusion_flutter_charts/charts.dart';
import 'package:flutter_candlesticks/flutter_candlesticks.dart';
import '../services/crypto_p2p_service.dart';
import '../models/crypto_p2p/crypto_listing_model.dart';

class MobileTradingScreen extends StatefulWidget {
  final String? cryptoSymbol;
  final String? fiatSymbol;
  
  const MobileTradingScreen({Key? key, this.cryptoSymbol, this.fiatSymbol}) : super(key: key);

  @override
  _MobileTradingScreenState createState() => _MobileTradingScreenState();
}

class _MobileTradingScreenState extends State<MobileTradingScreen> {
  final CryptoP2PService _cryptoService = CryptoP2PService();
  String _selectedCrypto = 'BTC';
  String _selectedFiat = 'USD';
  String _selectedTimeframe = '1H';
  String _orderType = 'limit';
  String _tradeType = 'buy';
  
  double _amount = 0.0;
  double _price = 0.0;
  double _total = 0.0;
  
  List<TradingPairData> _tradingPairs = [];
  List<OHLCData> _candleData = [];
  List<ChartData> _lineData = [];
  
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _selectedCrypto = widget.cryptoSymbol ?? _selectedCrypto;
    _selectedFiat = widget.fiatSymbol ?? _selectedFiat;
    _loadTradingData();
  }

  Future<void> _loadTradingData() async {
    setState(() {
      _isLoading = true;
    });

    try {
      // Load trading pairs
      final pairs = await _cryptoService.getTradingPairs();
      setState(() {
        _tradingPairs = pairs.map((pair) => TradingPairData.fromMap(pair)).toList();
      });

      // Generate mock chart data
      _generateChartData();
    } catch (e) {
      print('Error loading trading data: $e');
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  void _generateChartData() {
    // Mock data for chart - in real app, this would come from API
    _candleData = [];
    _lineData = [];
    
    double basePrice = 60000.0;
    for (int i = 0; i < 20; i++) {
      double open = basePrice + (i * 100);
      double close = open + ((i % 2 == 0) ? 200 : -150);
      double high = (open > close) ? open + 300 : close + 300;
      double low = (open < close) ? open - 250 : close - 250;
      
      _candleData.add(OHLCData(
        date: DateTime.now().subtract(Duration(minutes: (20 - i) * 5)),
        open: open,
        high: high,
        low: low,
        close: close,
      ));
      
      _lineData.add(ChartData(
        x: DateTime.now().subtract(Duration(minutes: (20 - i) * 5)),
        y: close,
      ));
    }
  }

  void _updateTotal() {
    setState(() {
      _total = _amount * _price;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Mobile Trading'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_active),
            onPressed: () {
              Navigator.pushNamed(context, '/price-alerts');
            },
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadTradingData,
              child: SingleChildScrollView(
                child: Column(
                  children: [
                    // Trading pair selector
                    _buildTradingPairSelector(),
                    
                    const SizedBox(height: 16),
                    
                    // Price display
                    _buildPriceDisplay(),
                    
                    const SizedBox(height: 16),
                    
                    // Chart
                    _buildChart(),
                    
                    const SizedBox(height: 16),
                    
                    // Timeframe selector
                    _buildTimeframeSelector(),
                    
                    const SizedBox(height: 16),
                    
                    // Trading controls
                    _buildTradingControls(),
                    
                    const SizedBox(height: 16),
                    
                    // Order form
                    _buildOrderForm(),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildTradingPairSelector() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          Expanded(
            child: DropdownButtonFormField<String>(
              value: _selectedCrypto,
              decoration: const InputDecoration(
                labelText: 'Crypto',
                border: OutlineInputBorder(),
              ),
              items: [
                const DropdownMenuItem(value: 'BTC', child: Text('Bitcoin (BTC)')),
                const DropdownMenuItem(value: 'ETH', child: Text('Ethereum (ETH)')),
                const DropdownMenuItem(value: 'USDT', child: Text('Tether (USDT)')),
                const DropdownMenuItem(value: 'USDC', child: Text('USD Coin (USDC)')),
                const DropdownMenuItem(value: 'BNB', child: Text('Binance Coin (BNB)')),
                const DropdownMenuItem(value: 'XRP', child: Text('Ripple (XRP)')),
              ],
              onChanged: (value) {
                if (value != null) {
                  setState(() {
                    _selectedCrypto = value;
                  });
                  _loadTradingData();
                }
              },
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: DropdownButtonFormField<String>(
              value: _selectedFiat,
              decoration: const InputDecoration(
                labelText: 'Fiat',
                border: OutlineInputBorder(),
              ),
              items: const [
                DropdownMenuItem(value: 'USD', child: Text('US Dollar (USD)')),
                DropdownMenuItem(value: 'EUR', child: Text('Euro (EUR)')),
                DropdownMenuItem(value: 'NGN', child: Text('Nigerian Naira (NGN)')),
              ],
              onChanged: (value) {
                if (value != null) {
                  setState(() {
                    _selectedFiat = value;
                  });
                  _loadTradingData();
                }
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPriceDisplay() {
    double currentPrice = _candleData.isNotEmpty 
        ? _candleData.last.close 
        : 60000.0; // Default price
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        children: [
          Text(
            '${_selectedCrypto}/${_selectedFiat}',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Colors.grey[600],
            ),
          ),
          Text(
            '\$${currentPrice.toStringAsFixed(2)}',
            style: const TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
              color: Colors.black,
            ),
          ),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.trending_up,
                color: Colors.green,
                size: 16,
              ),
              const SizedBox(width: 4),
              Text(
                '+2.5%',
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.green,
                ),
              ),
              const SizedBox(width: 16),
              Text(
                '24h',
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey[600],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildChart() {
    return Container(
      height: 300,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Candlesticks(
        candleDatas: _candleData,
        volumePositiveColor: Colors.green,
        volumeNegativeColor: Colors.red,
        gridLines: true,
        onPan: (DragUpdateDetails details) {
          // Handle chart panning
        },
      ),
    );
  }

  Widget _buildTimeframeSelector() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: SegmentedButton<String>(
        segments: const [
          ButtonSegment(value: '1m', label: Text('1m')),
          ButtonSegment(value: '5m', label: Text('5m')),
          ButtonSegment(value: '15m', label: Text('15m')),
          ButtonSegment(value: '1H', label: Text('1H')),
          ButtonSegment(value: '4H', label: Text('4H')),
          ButtonSegment(value: '1D', label: Text('1D')),
        ],
        selected: {_selectedTimeframe},
        onSelectionChanged: (Set<String> newSelection) {
          setState(() {
            _selectedTimeframe = newSelection.first;
          });
          _loadTradingData();
        },
      ),
    );
  }

  Widget _buildTradingControls() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(
        children: [
          Expanded(
            child: ElevatedButton(
              onPressed: _tradeType == 'buy' ? null : () {
                setState(() {
                  _tradeType = 'buy';
                });
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: _tradeType == 'buy' ? Colors.green : Colors.grey[300],
                foregroundColor: _tradeType == 'buy' ? Colors.white : Colors.black,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                  side: BorderSide(
                    color: _tradeType == 'buy' ? Colors.green : Colors.grey,
                  ),
                ),
              ),
              child: Text(
                'BUY',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: _tradeType == 'buy' ? Colors.white : Colors.black,
                ),
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: ElevatedButton(
              onPressed: _tradeType == 'sell' ? null : () {
                setState(() {
                  _tradeType = 'sell';
                });
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: _tradeType == 'sell' ? Colors.red : Colors.grey[300],
                foregroundColor: _tradeType == 'sell' ? Colors.white : Colors.black,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                  side: BorderSide(
                    color: _tradeType == 'sell' ? Colors.red : Colors.grey,
                  ),
                ),
              ),
              child: Text(
                'SELL',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: _tradeType == 'sell' ? Colors.white : Colors.black,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOrderForm() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        children: [
          // Order type selector
          SegmentedButton<String>(
            segments: const [
              ButtonSegment(value: 'limit', label: Text('Limit')),
              ButtonSegment(value: 'market', label: Text('Market')),
            ],
            selected: {_orderType},
            onSelectionChanged: (Set<String> newSelection) {
              setState(() {
                _orderType = newSelection.first;
              });
            },
          ),
          
          const SizedBox(height: 16),
          
          // Price input (only for limit orders)
          if (_orderType == 'limit') ...[
            TextField(
              decoration: InputDecoration(
                labelText: 'Price (${_selectedFiat})',
                border: const OutlineInputBorder(),
                prefixText: '\$',
              ),
              keyboardType: TextInputType.number,
              onChanged: (value) {
                double? parsedValue = double.tryParse(value);
                if (parsedValue != null) {
                  setState(() {
                    _price = parsedValue;
                    _updateTotal();
                  });
                }
              },
              controller: TextEditingController(text: _price.toString()),
            ),
            const SizedBox(height: 12),
          ],
          
          // Amount input
          TextField(
            decoration: InputDecoration(
              labelText: 'Amount (${_selectedCrypto})',
              border: const OutlineInputBorder(),
            ),
            keyboardType: TextInputType.number,
            onChanged: (value) {
              double? parsedValue = double.tryParse(value);
              if (parsedValue != null) {
                setState(() {
                  _amount = parsedValue;
                  _updateTotal();
                });
              }
            },
            controller: TextEditingController(text: _amount.toString()),
          ),
          
          const SizedBox(height: 12),
          
          // Total display
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.grey[100],
              borderRadius: BorderRadius.circular(8),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Total:',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
                Text(
                  '\$${_total.toStringAsFixed(2)} ${_selectedFiat}',
                  style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
          
          const SizedBox(height: 16),
          
          // Quick amount buttons
          Wrap(
            spacing: 8,
            children: [0.1, 0.5, 1.0, 5.0].map((amount) {
              return FilterChip(
                label: Text('${amount}${_selectedCrypto}'),
                selected: false,
                onSelected: (selected) {
                  setState(() {
                    _amount = amount;
                    _updateTotal();
                  });
                },
              );
            }).toList(),
          ),
          
          const SizedBox(height: 16),
          
          // Place order button
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _amount > 0 && (_orderType == 'market' || _price > 0)
                  ? _placeOrder
                  : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: _tradeType == 'buy' ? Colors.green : Colors.red,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 16),
              ),
              child: Text(
                'PLACE ${_tradeType.toUpperCase()} ORDER',
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _placeOrder() async {
    // Show confirmation dialog
    bool confirmed = await showDialog<bool>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Confirm Order'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Type: ${_orderType.toUpperCase()}'),
              Text('Side: ${_tradeType.toUpperCase()}'),
              Text('Crypto: $amount $_selectedCrypto'),
              Text('Price: \$${_price.toStringAsFixed(2)}'),
              Text('Total: \$${_total.toStringAsFixed(2)}'),
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
                backgroundColor: _tradeType == 'buy' ? Colors.green : Colors.red,
              ),
              child: Text(
                'CONFIRM',
                style: const TextStyle(color: Colors.white),
              ),
            ),
          ],
        );
      },
    ) ?? false;

    if (confirmed) {
      // In a real app, this would place the actual order via the API
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Order placed successfully! ${_tradeType.toUpperCase()} $_amount $_selectedCrypto',
            style: const TextStyle(color: Colors.white),
          ),
          backgroundColor: Colors.green,
        ),
      );
    }
  }
}

// Data classes for the trading screen
class TradingPairData {
  final int id;
  final String baseSymbol;
  final String quoteSymbol;
  final double lastPrice;
  final double change24h;
  final double volume24h;

  TradingPairData({
    required this.id,
    required this.baseSymbol,
    required this.quoteSymbol,
    required this.lastPrice,
    required this.change24h,
    required this.volume24h,
  });

  factory TradingPairData.fromMap(Map<String, dynamic> map) {
    return TradingPairData(
      id: map['id'] ?? 0,
      baseSymbol: map['base_symbol'] ?? 'BTC',
      quoteSymbol: map['quote_symbol'] ?? 'USD',
      lastPrice: map['last_price']?.toDouble() ?? 0.0,
      change24h: map['change_24h']?.toDouble() ?? 0.0,
      volume24h: map['volume_24h']?.toDouble() ?? 0.0,
    );
  }
}

class OHLCData {
  DateTime date;
  double open;
  double high;
  double low;
  double close;

  OHLCData({
    required this.date,
    required this.open,
    required this.high,
    required this.low,
    required this.close,
  });
}

class ChartData {
  DateTime x;
  double y;

  ChartData({
    required this.x,
    required this.y,
  });
}