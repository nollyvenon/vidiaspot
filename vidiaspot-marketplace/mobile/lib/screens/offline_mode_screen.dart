// lib/screens/offline_mode_screen.dart
import 'package:flutter/material.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import '../services/offline_sync_service.dart';
import '../models/crypto_p2p/crypto_listing_model.dart';
import '../models/crypto_p2p/crypto_trade_model.dart';

class OfflineModeScreen extends StatefulWidget {
  const OfflineModeScreen({Key? key}) : super(key: key);

  @override
  _OfflineModeScreenState createState() => _OfflineModeScreenState();
}

class _OfflineModeScreenState extends State<OfflineModeScreen> {
  final OfflineSyncService _offlineService = OfflineSyncService(null);
  bool _isOnline = false;
  bool _isLoading = true;
  Map<String, dynamic> _offlineStatus = {};
  List<CryptoListing> _cachedListings = [];
  List<CryptoTrade> _cachedTrades = [];

  @override
  void initState() {
    super.initState();
    _checkOfflineStatus();
    _loadOfflineData();
  }

  Future<void> _checkOfflineStatus() async {
    setState(() {
      _isLoading = true;
    });

    _isOnline = await _offlineService.isOnline();
    _offlineStatus = await _offlineService.getOfflineStatus();

    setState(() {
      _isLoading = false;
    });

    // Set up connectivity listener
    Connectivity().onConnectivityChanged.listen((ConnectivityResult result) {
      setState(() {
        _isOnline = result != ConnectivityResult.none;
      });
      
      // If we just came online, sync pending operations
      if (_isOnline) {
        _syncPendingOperations();
      }
    });
  }
  
  Future<void> _loadOfflineData() async {
    final listings = await _offlineService.getCachedListings();
    final trades = await _offlineService.getCachedTrades();
    
    if (mounted) {
      setState(() {
        _cachedListings = listings ?? [];
        _cachedTrades = trades ?? [];
      });
    }
  }

  Future<void> _syncPendingOperations() async {
    try {
      await _offlineService.syncPendingOperations();
      // Refresh the UI after sync
      await _loadOfflineData();
      _showSuccess('Sync completed successfully!');
    } catch (e) {
      _showError('Sync failed: $e');
    }
  }

  Future<void> _refreshCachedData() async {
    if (_isOnline) {
      setState(() {
        _isLoading = true;
      });
      
      try {
        await _offlineService.updateCachedData();
        await _loadOfflineData();
        _showSuccess('Data refreshed from server');
      } catch (e) {
        _showError('Failed to refresh data: $e');
      } finally {
        if (mounted) {
          setState(() {
            _isLoading = false;
          });
        }
      }
    } else {
      _showError('No internet connection available');
    }
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
        title: const Text('Offline Mode'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(
              _isOnline ? Icons.cloud_done : Icons.cloud_off,
              color: _isOnline ? Colors.green : Colors.red,
            ),
            onPressed: () {
              _checkOfflineStatus();
            },
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                await _refreshCachedData();
              },
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Connection status
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: _isOnline ? Colors.green[100] : Colors.red[100],
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        children: [
                          Icon(
                            _isOnline ? Icons.wifi : Icons.wifi_off,
                            color: _isOnline ? Colors.green : Colors.red,
                            size: 30,
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  _isOnline ? 'Online' : 'Offline',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                    color: _isOnline ? Colors.green : Colors.red,
                                  ),
                                ),
                                Text(
                                  _isOnline 
                                      ? 'Connected to internet' 
                                      : 'Working in offline mode',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: _isOnline ? Colors.green[700] : Colors.red[700],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Offline statistics
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Offline Statistics',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildStatRow(
                              'Cached Listings', 
                              _cachedListings.length.toString()
                            ),
                            _buildStatRow(
                              'Cached Trades', 
                              _cachedTrades.length.toString()
                            ),
                            _buildStatRow(
                              'Pending Operations', 
                              (_offlineStatus['pending_operations'] ?? 0).toString()
                            ),
                            if (_offlineStatus['lastSyncTime'] != null)
                              _buildStatRow(
                                'Last Sync', 
                                _formatDateTime(_offlineStatus['lastSyncTime'])
                              ),
                            _buildStatRow(
                              'Data Stale', 
                              (_offlineStatus['hasStaleData'] ?? false).toString()
                            ),
                          ],
                        ),
                      ),
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Cached listings section
                    if (_cachedListings.isNotEmpty) ...[
                      const Text(
                        'Cached Listings',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      ..._cachedListings.take(5).map((listing) => _buildCachedListingCard(listing)),
                      
                      if (_cachedListings.length > 5)
                        TextButton(
                          onPressed: () {},
                          child: const Text('View All'),
                        ),
                    ],
                    
                    const SizedBox(height: 16),
                    
                    // Cached trades section
                    if (_cachedTrades.isNotEmpty) ...[
                      const Text(
                        'Cached Trades',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      ..._cachedTrades.take(5).map((trade) => _buildCachedTradeCard(trade)),
                      
                      if (_cachedTrades.length > 5)
                        TextButton(
                          onPressed: () {},
                          child: const Text('View All'),
                        ),
                    ],
                    
                    const SizedBox(height: 16),
                    
                    // Actions based on connectivity
                    if (_isOnline) ...[
                      // Sync button when online
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _syncPendingOperations,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.blue,
                            foregroundColor: Colors.white,
                          ),
                          child: const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.sync),
                              SizedBox(width: 8),
                              Text('Sync Pending Operations'),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      SizedBox(
                        width: double.infinity,
                        child: OutlinedButton(
                          onPressed: _refreshCachedData,
                          child: const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.refresh),
                              SizedBox(width: 8),
                              Text('Refresh from Server'),
                            ],
                          ),
                        ),
                      ),
                    ] else ...[
                      // Message when offline
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.blue[50],
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Column(
                          children: [
                            Icon(Icons.offline_bolt, size: 40),
                            SizedBox(height: 8),
                            Text(
                              'Working Offline',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 8),
                            Text(
                              'You can continue using the app. Changes will be synced when you are back online.',
                              textAlign: TextAlign.center,
                              style: TextStyle(fontSize: 14),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStatRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(fontSize: 14),
          ),
          Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCachedListingCard(CryptoListing listing) {
    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: Padding(
        padding: const EdgeInsets.all(12),
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
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
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
              ],
            ),
            const SizedBox(height: 4),
            Text(
              'Price: ${listing.fiatCurrency} ${listing.pricePerUnit.toStringAsFixed(2)}',
              style: const TextStyle(
                fontSize: 14,
                color: Colors.green,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Available: ${listing.availableAmount.toStringAsFixed(4)} ${listing.cryptoCurrency}',
              style: const TextStyle(
                fontSize: 12,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Payment: ${listing.paymentMethods.join(', ')}',
              style: const TextStyle(
                fontSize: 12,
                color: Colors.grey,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCachedTradeCard(CryptoTrade trade) {
    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${trade.cryptoCurrency}/${trade.fiatCurrency}',
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: _getTradeStatusColor(trade.status),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    trade.status.toUpperCase(),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Text(
              'Amount: ${trade.cryptoAmount.toStringAsFixed(6)} ${trade.cryptoCurrency}',
              style: const TextStyle(
                fontSize: 14,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Value: ${trade.fiatCurrency} ${trade.fiatAmount.toStringAsFixed(2)}',
              style: const TextStyle(
                fontSize: 14,
                color: Colors.green,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Type: ${trade.tradeType.toUpperCase()}',
              style: const TextStyle(
                fontSize: 12,
                color: Colors.grey,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getTradeStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'completed':
        return Colors.green;
      case 'pending':
        return Colors.orange;
      case 'cancelled':
      case 'failed':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  String _formatDateTime(dynamic dateTime) {
    if (dateTime == null) return 'Never';
    if (dateTime is DateTime) {
      return '${dateTime.day}/${dateTime.month}/${dateTime.year}';
    }
    return dateTime.toString();
  }
}