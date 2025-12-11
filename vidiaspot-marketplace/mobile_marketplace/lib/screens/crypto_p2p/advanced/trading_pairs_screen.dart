// lib/screens/crypto_p2p/advanced/trading_pairs_screen.dart
import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import '../../../services/crypto_p2p_service.dart';

class TradingPairsScreen extends StatefulWidget {
  const TradingPairsScreen({Key? key}) : super(key: key);

  @override
  _TradingPairsScreenState createState() => _TradingPairsScreenState();
}

class _TradingPairsScreenState extends State<TradingPairsScreen> {
  final CryptoP2PService _cryptoP2PService = CryptoP2PService();
  List<Map<String, dynamic>> _tradingPairs = [];
  bool _isLoading = true;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _loadTradingPairs();
  }

  void _loadTradingPairs() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final pairs = await _cryptoP2PService.getTradingPairs();
      setState(() {
        _tradingPairs = pairs;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error loading trading pairs: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  List<Map<String, dynamic>> _getFilteredPairs() {
    if (_searchQuery.isEmpty) {
      return _tradingPairs;
    }

    return _tradingPairs
        .where((pair) => pair['pair_name']
            .toLowerCase()
            .contains(_searchQuery.toLowerCase()))
        .toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Trading Pairs'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadTradingPairs,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          _loadTradingPairs();
        },
        child: Column(
          children: [
            // Search bar
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: TextField(
                decoration: InputDecoration(
                  hintText: 'Search trading pairs...',
                  prefixIcon: const Icon(Icons.search),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  filled: true,
                  fillColor: Colors.grey[100],
                ),
                onChanged: (value) {
                  setState(() {
                    _searchQuery = value;
                  });
                },
              ),
            ),
            const SizedBox(height: 10),
            
            // Trading pairs list
            Expanded(
              child: _isLoading
                  ? _buildShimmerList()
                  : _getFilteredPairs().isEmpty
                      ? _buildEmptyState()
                      : _buildTradingPairsList(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildShimmerList() {
    return ListView.builder(
      itemCount: 10,
      itemBuilder: (context, index) {
        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              height: 80,
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Container(
                    width: 50,
                    height: 50,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          width: 100,
                          height: 16,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Container(
                          width: 150,
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
    return const Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.hourglass_empty,
            size: 64,
            color: Colors.grey,
          ),
          SizedBox(height: 16),
          Text(
            'No trading pairs found',
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTradingPairsList() {
    return ListView.builder(
      itemCount: _getFilteredPairs().length,
      itemBuilder: (context, index) {
        final pair = _getFilteredPairs()[index];
        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: ListTile(
            contentPadding: const EdgeInsets.all(16),
            leading: Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: Colors.blue[100],
                borderRadius: BorderRadius.circular(25),
              ),
              child: Icon(
                Icons.currency_bitcoin,
                color: Colors.blue,
              ),
            ),
            title: Text(
              pair['pair_name'] ?? 'Unknown Pair',
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
                  'Symbol: ${pair['symbol']}',
                  style: const TextStyle(
                    fontSize: 12,
                    color: Colors.grey,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Min Qty: ${pair['min_quantity']} | Max Qty: ${pair['max_quantity']}',
                  style: const TextStyle(
                    fontSize: 12,
                    color: Colors.grey,
                  ),
                ),
              ],
            ),
            trailing: Icon(
              pair['is_active'] == true ? Icons.check_circle : Icons.cancel,
              color: pair['is_active'] == true ? Colors.green : Colors.red,
            ),
            onTap: () {
              // Navigate to order book for this pair
              // Navigator.push(
              //   context,
              //   MaterialPageRoute(
              //     builder: (context) => OrderBookScreen(pairId: pair['id']),
              //   ),
              // );
            },
          ),
        );
      },
    );
  }
}