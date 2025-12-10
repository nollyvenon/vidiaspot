// lib/screens/crypto_p2p/crypto_p2p_home_screen.dart
import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import '../../services/crypto_p2p_service.dart';
import '../../models/crypto_p2p/crypto_listing_model.dart';

class CryptoP2PHomeScreen extends StatefulWidget {
  const CryptoP2PHomeScreen({Key? key}) : super(key: key);

  @override
  _CryptoP2PHomeScreenState createState() => _CryptoP2PHomeScreenState();
}

class _CryptoP2PHomeScreenState extends State<CryptoP2PHomeScreen> {
  final CryptoP2PService _cryptoP2PService = CryptoP2PService();
  List<CryptoListing> _listings = [];
  List<CryptoListing> _featuredListings = [];
  bool _isLoading = true;
  String _selectedCrypto = 'BTC';
  String _selectedFiat = 'NGN';
  String _tradeType = 'sell'; // Default to buy BTC with NGN

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  void _loadData() async {
    setState(() {
      _isLoading = true;
    });

    try {
      // Load active listings
      final listings = await _cryptoP2PService.getActiveListings(
        cryptoCurrency: _selectedCrypto,
        fiatCurrency: _selectedFiat,
        tradeType: _tradeType,
      );
      setState(() {
        _listings = listings;
      });

      // Load featured listings
      final featuredListings = await _cryptoP2PService.getActiveListings(
        cryptoCurrency: _selectedCrypto,
        fiatCurrency: _selectedFiat,
        tradeType: _tradeType,
        perPage: 6,
      );
      setState(() {
        _featuredListings = featuredListings;
      });
    } catch (e) {
      // Handle error
      print('Error loading data: $e');
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Crypto P2P Marketplace'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          _loadData();
        },
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Market Overview
                _buildMarketOverview(),
                
                const SizedBox(height: 20),
                
                // Filters
                _buildFilters(),
                
                const SizedBox(height: 20),
                
                // Quick Actions
                _buildQuickActions(),
                
                const SizedBox(height: 20),
                
                // Featured Listings
                Text(
                  'Featured Listings',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                
                const SizedBox(height: 10),
                
                _isLoading 
                    ? _buildShimmerListings()
                    : _featuredListings.isEmpty
                        ? _buildEmptyState()
                        : _buildFeaturedListings(),
                
                const SizedBox(height: 20),
                
                // All Listings
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'All Listings',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    TextButton(
                      onPressed: () {
                        // Navigate to all listings
                      },
                      child: const Text('View All'),
                    ),
                  ],
                ),
                
                const SizedBox(height: 10),
                
                _isLoading 
                    ? _buildShimmerListings()
                    : _listings.isEmpty
                        ? _buildEmptyState()
                        : _buildAllListings(),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildMarketOverview() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.blue[50],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Market Overview',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
              IconButton(
                onPressed: () {
                  // Refresh market data
                },
                icon: const Icon(Icons.refresh),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildMarketStat('BTC/NGN', '775,000', Colors.green),
              _buildMarketStat('ETH/NGN', '280,000', Colors.blue),
              _buildMarketStat('USDT/NGN', '1,400', Colors.orange),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMarketStat(String pair, String price, Color color) {
    return Column(
      children: [
        Text(
          pair,
          style: const TextStyle(
            fontSize: 12,
            color: Colors.grey,
          ),
        ),
        Text(
          price,
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        const Icon(
          Icons.trending_up,
          size: 16,
          color: Colors.green,
        ),
      ],
    );
  }

  Widget _buildFilters() {
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
            'Filters',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: DropdownButtonFormField<String>(
                  value: _selectedCrypto,
                  decoration: const InputDecoration(
                    labelText: 'Crypto',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    DropdownMenuItem(value: 'BTC', child: Text('Bitcoin (BTC)')),
                    DropdownMenuItem(value: 'ETH', child: Text('Ethereum (ETH)')),
                    DropdownMenuItem(value: 'USDT', child: Text('Tether (USDT)')),
                    DropdownMenuItem(value: 'USDC', child: Text('USD Coin (USDC)')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        _selectedCrypto = value;
                      });
                      _loadData();
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
                  items: [
                    DropdownMenuItem(value: 'NGN', child: Text('Nigerian Naira (NGN)')),
                    DropdownMenuItem(value: 'USD', child: Text('US Dollar (USD)')),
                    DropdownMenuItem(value: 'EUR', child: Text('Euro (EUR)')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        _selectedFiat = value;
                      });
                      _loadData();
                    }
                  },
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: SegmentedButton<String>(
                  segments: const [
                    ButtonSegment(
                      value: 'buy',
                      label: Text('Buy'),
                      icon: Icon(Icons.trending_down),
                    ),
                    ButtonSegment(
                      value: 'sell',
                      label: Text('Sell'),
                      icon: Icon(Icons.trending_up),
                    ),
                  ],
                  selected: {_tradeType},
                  onSelectionChanged: (Set<String> newSelection) {
                    setState(() {
                      _tradeType = newSelection.first;
                    });
                    _loadData();
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActions() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          const Text(
            'Quick Actions',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Column(
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  _buildQuickAction(
                    icon: Icons.add,
                    label: 'Create Listing',
                    color: Colors.blue,
                    onTap: () {
                      Navigator.pushNamed(context, '/create-listing');
                    },
                  ),
                  _buildQuickAction(
                    icon: Icons.compare_arrows,
                    label: 'Find Trade',
                    color: Colors.green,
                    onTap: () {
                      Navigator.pushNamed(context, '/find-trade');
                    },
                  ),
                  _buildQuickAction(
                    icon: Icons.account_balance_wallet,
                    label: 'My Trades',
                    color: Colors.orange,
                    onTap: () {
                      Navigator.pushNamed(context, '/my-trades');
                    },
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  _buildQuickAction(
                    icon: Icons.auto_graph,
                    label: 'Trading',
                    color: Colors.purple,
                    onTap: () {
                      Navigator.pushNamed(context, '/trading-pairs');
                    },
                  ),
                  _buildQuickAction(
                    icon: Icons.payment,
                    label: 'Payments',
                    color: Colors.teal,
                    onTap: () {
                      Navigator.pushNamed(context, '/payment-methods');
                    },
                  ),
                  _buildQuickAction(
                    icon: Icons.verified_user,
                    label: 'Verify',
                    color: Colors.amber,
                    onTap: () {
                      Navigator.pushNamed(context, '/verification-status');
                    },
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickAction({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 100,
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 30),
            const SizedBox(height: 8),
            Text(
              label,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 12,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFeaturedListings() {
    return Container(
      height: 200,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: _featuredListings.length,
        itemBuilder: (context, index) {
          final listing = _featuredListings[index];
          return _buildListingCard(listing, isFeatured: true);
        },
      ),
    );
  }

  Widget _buildAllListings() {
    return Container(
      height: 300,
      child: ListView.builder(
        itemCount: _listings.length,
        itemBuilder: (context, index) {
          final listing = _listings[index];
          return _buildListingCard(listing);
        },
      ),
    );
  }

  Widget _buildListingCard(CryptoListing listing, {bool isFeatured = false}) {
    return Card(
      margin: const EdgeInsets.only(right: 10),
      child: Container(
        width: 280,
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${listing.cryptoCurrency}/${listing.fiatCurrency}',
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                if (isFeatured)
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 2,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.amber,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: const Text(
                      'Featured',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              'Price: ${listing.fiatCurrency} ${listing.pricePerUnit.toStringAsFixed(2)}',
              style: const TextStyle(
                fontSize: 14,
                color: Colors.green,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Min: ${listing.fiatCurrency} ${listing.minTradeAmount.toStringAsFixed(2)} | Max: ${listing.fiatCurrency} ${listing.maxTradeAmount.toStringAsFixed(2)}',
              style: const TextStyle(
                fontSize: 12,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Payment: ${listing.paymentMethods.join(', ')}',
              style: const TextStyle(
                fontSize: 12,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: listing.tradeType == 'buy' ? Colors.green : Colors.red,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    listing.tradeType.toUpperCase(),
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
                  '${listing.reputationScore.toStringAsFixed(1)} (${listing.tradeCount} trades)',
                  style: const TextStyle(
                    fontSize: 12,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            ElevatedButton(
              onPressed: () {
                // Navigate to initiate trade screen
                Navigator.pushNamed(
                  context,
                  '/initiate-trade',
                  arguments: {'listing': listing},
                );
              },
              child: Text(
                listing.tradeType == 'buy' ? 'Sell ${listing.cryptoCurrency}' : 'Buy ${listing.cryptoCurrency}',
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildShimmerListings() {
    return Container(
      height: 400,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: 3,
        itemBuilder: (context, index) {
          return Card(
            margin: const EdgeInsets.only(right: 10),
            child: Shimmer.fromColors(
              baseColor: Colors.grey[300]!,
              highlightColor: Colors.grey[100]!,
              child: Container(
                width: 280,
                height: 180,
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
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
                    const SizedBox(height: 8),
                    Container(
                      width: 200,
                      height: 14,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                    const SizedBox(height: 8),
                    Container(
                      width: 180,
                      height: 14,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      width: 80,
                      height: 30,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildEmptyState() {
    return Container(
      height: 200,
      child: const Center(
        child: Text('No listings found'),
      ),
    );
  }
}