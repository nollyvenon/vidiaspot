// lib/services/offline_sync_service.dart
import 'package:hive/hive.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import '../models/crypto_p2p/crypto_listing_model.dart';
import '../models/crypto_p2p/crypto_trade_model.dart';
import '../services/crypto_p2p_service.dart';
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class OfflineSyncService {
  static const String _lastSyncKey = 'last_sync_timestamp';
  static const String _pendingOperationsKey = 'pending_operations';
  
  final CryptoP2PService _cryptoService;
  Box? _offlineDataBox;
  
  OfflineSyncService(this._cryptoService);

  Future<void> initialize() async {
    // Initialize Hive box for offline data
    if (!Hive.isAdapterRegistered(CryptoListingAdapter())) {
      Hive.registerAdapter(CryptoListingAdapter());
    }
    if (!Hive.isAdapterRegistered(CryptoTradeAdapter())) {
      Hive.registerAdapter(CryptoTradeAdapter());
    }
    
    _offlineDataBox = await Hive.openBox('offline_data');
  }

  // Check if device is online
  Future<bool> isOnline() async {
    var connectivityResult = await Connectivity().checkConnectivity();
    return connectivityResult != ConnectivityResult.none;
  }

  // Cache data for offline access
  Future<void> cacheListings(List<CryptoListing> listings) async {
    if (_offlineDataBox != null) {
      // Store listings in offline cache
      await _offlineDataBox!.put('cached_listings', listings);
      await _offlineDataBox!.put('listings_timestamp', DateTime.now().millisecondsSinceEpoch);
    }
  }

  // Cache user trades
  Future<void> cacheTrades(List<CryptoTrade> trades) async {
    if (_offlineDataBox != null) {
      await _offlineDataBox!.put('cached_trades', trades);
      await _offlineDataBox!.put('trades_timestamp', DateTime.now().millisecondsSinceEpoch);
    }
  }

  // Get cached listings
  Future<List<CryptoListing>?> getCachedListings() async {
    if (_offlineDataBox != null) {
      var cachedListings = _offlineDataBox!.get('cached_listings');
      if (cachedListings != null) {
        return List<CryptoListing>.from(cachedListings);
      }
    }
    return null;
  }

  // Get cached trades
  Future<List<CryptoTrade>?> getCachedTrades() async {
    if (_offlineDataBox != null) {
      var cachedTrades = _offlineDataBox!.get('cached_trades');
      if (cachedTrades != null) {
        return List<CryptoTrade>.from(cachedTrades);
      }
    }
    return null;
  }

  // Add pending operation for sync when online
  Future<void> addPendingOperation(String operation, Map<String, dynamic> data) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    List<String>? pendingOps = prefs.getStringList(_pendingOperationsKey) ?? [];
    
    Map<String, dynamic> operationData = {
      'operation': operation,
      'data': data,
      'timestamp': DateTime.now().millisecondsSinceEpoch,
    };
    
    pendingOps.add(jsonEncode(operationData));
    await prefs.setStringList(_pendingOperationsKey, pendingOps);
  }

  // Sync pending operations when online
  Future<void> syncPendingOperations() async {
    bool online = await isOnline();
    if (!online) return;
    
    SharedPreferences prefs = await SharedPreferences.getInstance();
    List<String>? pendingOps = prefs.getStringList(_pendingOperationsKey) ?? [];
    
    for (String operationStr in pendingOps) {
      try {
        Map<String, dynamic> operation = jsonDecode(operationStr);
        String opType = operation['operation'];
        Map<String, dynamic> opData = operation['data'];
        
        // Process the pending operation based on type
        await _processPendingOperation(opType, opData);
      } catch (e) {
        print('Error processing pending operation: $e');
      }
    }
    
    // Clear processed operations
    await prefs.setStringList(_pendingOperationsKey, []);
  }

  // Process a single pending operation
  Future<void> _processPendingOperation(String operation, Map<String, dynamic> data) async {
    switch (operation) {
      case 'create_listing':
        await _cryptoService.createListing(
          cryptoCurrency: data['cryptoCurrency'],
          fiatCurrency: data['fiatCurrency'],
          tradeType: data['tradeType'],
          pricePerUnit: data['pricePerUnit'],
          minTradeAmount: data['minTradeAmount'],
          maxTradeAmount: data['maxTradeAmount'],
          paymentMethods: List<String>.from(data['paymentMethods']),
          negotiable: data['negotiable'] ?? false,
          tradingFeePercent: data['tradingFeePercent']?.toDouble(),
          tradingFeeFixed: data['tradingFeeFixed']?.toDouble(),
        );
        break;
      case 'initiate_trade':
        await _cryptoService.initiateTrade(
          listingId: data['listingId'],
          cryptoAmount: data['cryptoAmount'],
          paymentMethod: data['paymentMethod'],
        );
        break;
      case 'confirm_payment':
        await _cryptoService.confirmPayment(data['tradeId']);
        break;
      case 'release_crypto':
        await _cryptoService.releaseCrypto(data['tradeId']);
        break;
      default:
        print('Unknown operation: $operation');
    }
  }

  // Sync data when connection is restored
  Future<void> startPeriodicSync() async {
    // This could be implemented with a background task
    // For now, we'll just check periodically when app is active
    await Future.delayed(Duration(seconds: 30)); // Wait 30 seconds before checking
    if (await isOnline()) {
      await syncPendingOperations();
      await updateCachedData();
    }
  }

  // Update cached data from server when online
  Future<void> updateCachedData() async {
    bool online = await isOnline();
    if (!online) return;
    
    try {
      // Update listings cache
      List<CryptoListing> listings = await _cryptoService.getActiveListings();
      await cacheListings(listings);
      
      // Update trades cache
      List<CryptoTrade> trades = await _cryptoService.getUserTrades();
      await cacheTrades(trades);
      
      SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.setInt(_lastSyncKey, DateTime.now().millisecondsSinceEpoch);
    } catch (e) {
      print('Error updating cached data: $e');
    }
  }

  // Get last sync timestamp
  Future<DateTime?> getLastSyncTime() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    int? timestamp = prefs.getInt(_lastSyncKey);
    if (timestamp != null) {
      return DateTime.fromMillisecondsSinceEpoch(timestamp);
    }
    return null;
  }

  // Check if cached data is stale
  Future<bool> isCachedDataStale({int maxAgeMinutes = 10}) async {
    DateTime? lastSync = await getLastSyncTime();
    if (lastSync == null) return true;
    
    Duration age = DateTime.now().difference(lastSync);
    return age.inMinutes > maxAgeMinutes;
  }

  // Close the service and cleanup
  Future<void> close() async {
    if (_offlineDataBox != null && _offlineDataBox!.isOpen) {
      await _offlineDataBox!.close();
    }
  }

  // Check if we have any offline data available
  Future<bool> hasOfflineData() async {
    if (_offlineDataBox == null) return false;
    
    var listings = await getCachedListings();
    var trades = await getCachedTrades();
    
    return (listings != null && listings.isNotEmpty) || 
           (trades != null && trades.isNotEmpty);
  }

  // Clear offline cache
  Future<void> clearOfflineCache() async {
    if (_offlineDataBox != null) {
      await _offlineDataBox!.clear();
    }
  }

  // Get offline status information
  Future<Map<String, dynamic>> getOfflineStatus() async {
    bool online = await isOnline();
    bool hasOfflineData = await this.hasOfflineData();
    bool staleData = await isCachedDataStale();
    
    return {
      'isOnline': online,
      'hasOfflineData': hasOfflineData,
      'hasStaleData': staleData,
      'lastSyncTime': await getLastSyncTime(),
    };
  }
}

// Hive adapters for our models (these would normally be in separate files)
class CryptoListingAdapter extends TypeAdapter<CryptoListing> {
  @override
  final int typeId = 1;

  @override
  CryptoListing read(BinaryReader reader) {
    return CryptoListing(
      id: reader.readInt(),
      userId: reader.readInt(),
      cryptoCurrency: reader.readString(),
      fiatCurrency: reader.readString(),
      tradeType: reader.readString(),
      pricePerUnit: reader.readDouble(),
      minTradeAmount: reader.readDouble(),
      maxTradeAmount: reader.readDouble(),
      availableAmount: reader.readDouble(),
      paymentMethods: reader.readList().cast<String>(),
      tradingFeePercent: reader.readDouble(),
      tradingFeeFixed: reader.readDouble(),
      location: reader.readString(),
      locationRadius: reader.readDouble(),
      tradingTerms: reader.readList().cast<String>(),
      negotiable: reader.readBool(),
      autoAccept: reader.readBool(),
      autoReleaseTimeHours: reader.readInt(),
      verificationLevelRequired: reader.readInt(),
      tradeSecurityLevel: reader.readInt(),
      reputationScore: reader.readDouble(),
      tradeCount: reader.readInt(),
      completionRate: reader.readDouble(),
      onlineStatus: reader.readBool(),
      status: reader.readString(),
      isPublic: reader.readBool(),
      featured: reader.readBool(),
      pinned: reader.readBool(),
    );
  }

  @override
  void write(BinaryWriter writer, CryptoListing obj) {
    writer.writeInt(obj.id);
    writer.writeInt(obj.userId);
    writer.writeString(obj.cryptoCurrency);
    writer.writeString(obj.fiatCurrency);
    writer.writeString(obj.tradeType);
    writer.writeDouble(obj.pricePerUnit);
    writer.writeDouble(obj.minTradeAmount);
    writer.writeDouble(obj.maxTradeAmount);
    writer.writeDouble(obj.availableAmount);
    writer.writeList(obj.paymentMethods);
    writer.writeDouble(obj.tradingFeePercent);
    writer.writeDouble(obj.tradingFeeFixed);
    writer.writeString(obj.location);
    writer.writeDouble(obj.locationRadius);
    writer.writeList(obj.tradingTerms);
    writer.writeBool(obj.negotiable);
    writer.writeBool(obj.autoAccept);
    writer.writeInt(obj.autoReleaseTimeHours);
    writer.writeInt(obj.verificationLevelRequired);
    writer.writeInt(obj.tradeSecurityLevel);
    writer.writeDouble(obj.reputationScore);
    writer.writeInt(obj.tradeCount);
    writer.writeDouble(obj.completionRate);
    writer.writeBool(obj.onlineStatus);
    writer.writeString(obj.status);
    writer.writeBool(obj.isPublic);
    writer.writeBool(obj.featured);
    writer.writeBool(obj.pinned);
  }
}

class CryptoTradeAdapter extends TypeAdapter<CryptoTrade> {
  @override
  final int typeId = 2;

  @override
  CryptoTrade read(BinaryReader reader) {
    return CryptoTrade(
      id: reader.readInt(),
      listingId: reader.readInt(),
      buyerId: reader.readInt(),
      sellerId: reader.readInt(),
      tradeType: reader.readString(),
      cryptoCurrency: reader.readString(),
      fiatCurrency: reader.readString(),
      cryptoAmount: reader.readDouble(),
      fiatAmount: reader.readDouble(),
      exchangeRate: reader.readDouble(),
      paymentMethod: reader.readString(),
      status: reader.readString(),
      escrowAddress: reader.readString(),
      tradeReference: reader.readString(),
      escrowStatus: reader.readString(),
      securityLevel: reader.readInt(),
      verificationRequired: reader.readBool(),
    );
  }

  @override
  void write(BinaryWriter writer, CryptoTrade obj) {
    writer.writeInt(obj.id);
    writer.writeInt(obj.listingId);
    writer.writeInt(obj.buyerId);
    writer.writeInt(obj.sellerId);
    writer.writeString(obj.tradeType);
    writer.writeString(obj.cryptoCurrency);
    writer.writeString(obj.fiatCurrency);
    writer.writeDouble(obj.cryptoAmount);
    writer.writeDouble(obj.fiatAmount);
    writer.writeDouble(obj.exchangeRate);
    writer.writeString(obj.paymentMethod);
    writer.writeString(obj.status);
    writer.writeString(obj.escrowAddress);
    writer.writeString(obj.tradeReference);
    writer.writeString(obj.escrowStatus);
    writer.writeInt(obj.securityLevel);
    writer.writeBool(obj.verificationRequired);
  }
}