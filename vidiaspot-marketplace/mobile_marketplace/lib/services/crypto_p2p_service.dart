// lib/services/crypto_p2p_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/crypto_p2p/crypto_listing_model.dart';
import '../models/crypto_p2p/crypto_trade_model.dart';
import '../models/crypto_p2p/crypto_trade_transaction_model.dart';

class CryptoP2PService {
  final String baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: 'http://10.0.2.2:8000/api'); // Updated to match backend API routes
  String? _authToken;

  CryptoP2PService() {
    _loadAuthToken();
  }

  Future<void> _loadAuthToken() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    _authToken = prefs.getString('auth_token');
  }

  Map<String, String> getHeaders() {
    Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (_authToken != null) {
      headers['Authorization'] = 'Bearer $_authToken';
    }

    return headers;
  }

  // Get all active crypto orders (listings)
  Future<List<CryptoListing>> getActiveListings({
    String? cryptoCurrency,
    String? fiatCurrency = 'NGN',
    String? tradeType,
    String? search,
    int perPage = 12,
  }) async {
    String url = '$baseUrl/p2p-crypto/orders';

    // Add query parameters for filtering
    List<String> queryParams = [];
    if (cryptoCurrency != null) queryParams.add('crypto_currency_id=$cryptoCurrency');
    if (tradeType != null) queryParams.add('order_type=$tradeType');

    if (queryParams.isNotEmpty) {
      url += '?' + queryParams.join('&');
    }

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        // Convert the response to match our model expectations
        List<dynamic> rawOrders = data['data'];
        return rawOrders.map((json) => _convertOrderToCryptoListing(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load listings');
      }
    } else {
      throw Exception('Failed to load listings: ${response.statusCode}');
    }
  }

  // Get available crypto currencies for P2P trading
  Future<List<Map<String, dynamic>>> getAvailableCurrencies() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/currencies'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return List<Map<String, dynamic>>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load currencies');
      }
    } else {
      throw Exception('Failed to load currencies: ${response.statusCode}');
    }
  }

  // Get available trading pairs
  Future<List<Map<String, dynamic>>> getTradingPairs() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/trading-pairs'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return List<Map<String, dynamic>>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load trading pairs');
      }
    } else {
      throw Exception('Failed to load trading pairs: ${response.statusCode}');
    }
  }

  // Get order book for a trading pair
  Future<Map<String, dynamic>> getOrderBook(int pairId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/trading-pairs/$pairId/orderbook'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Map<String, dynamic>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load order book');
      }
    } else {
      throw Exception('Failed to load order book: ${response.statusCode}');
    }
  }

  // Create a new trading order
  Future<Map<String, dynamic>> createTradingOrder({
    required int tradingPairId,
    required String orderType,
    required String side,
    required double quantity,
    double? price,
    double? stopPrice,
    String? timeInForce,
    String? goodTillDate,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/p2p-crypto/trading-orders'),
      headers: getHeaders(),
      body: jsonEncode({
        'trading_pair_id': tradingPairId,
        'order_type': orderType,
        'side': side,
        'quantity': quantity,
        'price': price,
        'stop_price': stopPrice,
        'time_in_force': timeInForce,
        'good_till_date': goodTillDate,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Map<String, dynamic>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create trading order');
      }
    } else {
      throw Exception('Failed to create trading order: ${response.statusCode}');
    }
  }

  // Get user's trading orders
  Future<List<Map<String, dynamic>>> getUserTradingOrders({
    String? status,
    int? tradingPairId,
    String? orderType,
  }) async {
    String url = '$baseUrl/p2p-crypto/trading-orders';

    List<String> queryParams = [];
    if (status != null) queryParams.add('status=$status');
    if (tradingPairId != null) queryParams.add('trading_pair_id=$tradingPairId');
    if (orderType != null) queryParams.add('order_type=$orderType');

    if (queryParams.isNotEmpty) {
      url += '?' + queryParams.join('&');
    }

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        // Extract the 'data' list from the paginated response
        Map<String, dynamic> paginatedData = Map<String, dynamic>.from(data);
        List<dynamic> orders = paginatedData['data'] is List
            ? paginatedData['data']
            : [];
        return orders.cast<Map<String, dynamic>>();
      } else {
        throw Exception(data['message'] ?? 'Failed to load trading orders');
      }
    } else {
      throw Exception('Failed to load trading orders: ${response.statusCode}');
    }
  }

  // Get user's trade history
  Future<List<Map<String, dynamic>>> getUserTradeHistory() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/trade-history'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        // Extract the 'data' list from the paginated response
        Map<String, dynamic> paginatedData = Map<String, dynamic>.from(data);
        List<dynamic> executions = paginatedData['data'] is List
            ? paginatedData['data']
            : [];
        return executions.cast<Map<String, dynamic>>();
      } else {
        throw Exception(data['message'] ?? 'Failed to load trade history');
      }
    } else {
      throw Exception('Failed to load trade history: ${response.statusCode}');
    }
  }

  // Get user's payment methods
  Future<List<Map<String, dynamic>>> getUserPaymentMethods() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/payment-methods'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return List<Map<String, dynamic>>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load payment methods');
      }
    } else {
      throw Exception('Failed to load payment methods: ${response.statusCode}');
    }
  }

  // Add a new payment method
  Future<Map<String, dynamic>> addPaymentMethod({
    required String paymentType,
    required String name,
    required Map<String, dynamic> paymentDetails,
    required String accountName,
    required String accountNumber,
    String? bankName,
    String? countryCode = 'US',
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/p2p-crypto/payment-methods'),
      headers: getHeaders(),
      body: jsonEncode({
        'payment_type': paymentType,
        'name': name,
        'payment_details': paymentDetails,
        'account_name': accountName,
        'account_number': accountNumber,
        'bank_name': bankName,
        'country_code': countryCode,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Map<String, dynamic>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to add payment method');
      }
    } else {
      throw Exception('Failed to add payment method: ${response.statusCode}');
    }
  }

  // Get user verification status
  Future<Map<String, dynamic>> getUserVerificationStatus() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/verification-status'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Map<String, dynamic>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load verification status');
      }
    } else {
      throw Exception('Failed to load verification status: ${response.statusCode}');
    }
  }

  // Get user reputation
  Future<Map<String, dynamic>> getUserReputation() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/reputation'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Map<String, dynamic>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load reputation data');
      }
    } else {
      throw Exception('Failed to load reputation data: ${response.statusCode}');
    }
  }

  // Helper method to convert backend order format to our app's listing format
  CryptoListing _convertOrderToCryptoListing(Map<String, dynamic> orderJson) {
    // This is a mock conversion - in a real implementation, this would map the actual fields
    // from our backend P2pCryptoOrder model to the expected CryptoListing format
    return CryptoListing(
      id: orderJson['id'] ?? 0,
      userId: orderJson['seller_id'] ?? orderJson['buyer_id'] ?? 0,
      cryptoCurrency: orderJson['crypto_currency']?['symbol'] ?? 'BTC',
      fiatCurrency: 'USD', // Default for our implementation
      tradeType: orderJson['order_type'] ?? 'sell',
      pricePerUnit: (orderJson['price_per_unit'] is int)
          ? (orderJson['price_per_unit'] as int).toDouble()
          : orderJson['price_per_unit']?.toDouble() ?? 0.0,
      minTradeAmount: 0.0, // Backend doesn't have min/max trade amounts for orders
      maxTradeAmount: 1000000.0, // Placeholder
      availableAmount: (orderJson['amount'] is int)
          ? (orderJson['amount'] as int).toDouble()
          : orderJson['amount']?.toDouble() ?? 0.0,
      paymentMethods: [orderJson['payment_method'] ?? 'Bank Transfer'],
      tradingFeePercent: 0.0, // Placeholder
      tradingFeeFixed: 0.0, // Placeholder
      location: 'Online', // Placeholder
      locationRadius: 0.0, // Placeholder
      tradingTerms: [orderJson['terms_and_conditions'] ?? 'Standard terms apply'],
      negotiable: false, // Backend doesn't have negotiable field
      autoAccept: false, // Placeholder
      autoReleaseTimeHours: 24, // Placeholder
      verificationLevelRequired: 1, // Placeholder
      tradeSecurityLevel: 1, // Placeholder
      reputationScore: 4.8, // Placeholder
      tradeCount: 12, // Placeholder
      completionRate: 98.5, // Placeholder
      onlineStatus: true, // Placeholder
      status: orderJson['status'] ?? 'active',
      isPublic: true, // Placeholder
      featured: false, // Placeholder
      pinned: false, // Placeholder
      expiresAt: null, // Placeholder
      metadata: orderJson, // Store original data in metadata
    );
  }

  // Create a new crypto order
  Future<CryptoListing> createListing({
    required String cryptoCurrency,
    required String fiatCurrency,
    required String tradeType,
    required double pricePerUnit,
    required double minTradeAmount,
    required double maxTradeAmount,
    double? availableAmount,
    required List<String> paymentMethods,
    double? tradingFeePercent,
    double? tradingFeeFixed,
    bool negotiable = false,
    bool autoAccept = false,
    int verificationLevelRequired = 1,
    int tradeSecurityLevel = 1,
    bool isPublic = true,
    String? location,
    double? locationRadius,
    List<String>? tradingTerms,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/p2p-crypto/orders'),
      headers: getHeaders(),
      body: jsonEncode({
        'crypto_currency_id': cryptoCurrency.toUpperCase(), // In our backend, this should be the ID
        'order_type': tradeType.toLowerCase(),
        'amount': availableAmount ?? maxTradeAmount, // Use maxTradeAmount as default amount
        'price_per_unit': pricePerUnit,
        'payment_method': paymentMethods.isNotEmpty ? paymentMethods[0] : 'Bank Transfer',
        'terms_and_conditions': tradingTerms?.join('\n') ?? '',
        'additional_notes': 'Created via mobile app',
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        // Convert the created order to our listing format
        return _convertOrderToCryptoListing(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create listing');
      }
    } else {
      throw Exception('Failed to create listing: ${response.statusCode}');
    }
  }

  // Get a specific order by ID
  Future<CryptoListing> getListing(int listingId) async {
    // Since we don't have a direct endpoint for single order, we'll call getActiveListings
    // and filter for the specific order. In a real implementation, we'd need to create this endpoint.
    final orders = await getActiveListings();
    final order = orders.firstWhere((element) => element.id == listingId, orElse: () => orders.first);
    return order;
  }

  // Get user's orders (listings)
  Future<List<CryptoListing>> getUserListings() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/orders/my'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        // Convert the response to match our model expectations
        List<dynamic> rawOrders = data['data'];
        return rawOrders.map((json) => _convertOrderToCryptoListing(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load user orders');
      }
    } else {
      throw Exception('Failed to load user orders: ${response.statusCode}');
    }
  }

  // Match an order (initiate a trade)
  Future<CryptoTrade> initiateTrade({
    required int listingId,
    required double cryptoAmount,
    required String paymentMethod,
    Map<String, dynamic>? paymentDetails,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/p2p-crypto/orders/$listingId/match'),
      headers: getHeaders(),
      body: jsonEncode({
        'crypto_amount': cryptoAmount,
        'payment_method': paymentMethod,
        'payment_details': paymentDetails ?? {},
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        // Convert the matched order to a trade format
        return _convertOrderToCryptoTrade(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to match order');
      }
    } else {
      throw Exception('Failed to match order: ${response.statusCode}');
    }
  }

  // Helper method to convert backend order format to our app's trade format
  CryptoTrade _convertOrderToCryptoTrade(Map<String, dynamic> orderJson) {
    return CryptoTrade(
      id: orderJson['id'] ?? 0,
      sellerId: orderJson['seller_id'] ?? 0,
      buyerId: orderJson['buyer_id'] ?? 0,
      cryptoCurrencyId: orderJson['crypto_currency_id'] ?? 0,
      cryptoCurrency: orderJson['crypto_currency']?['symbol'] ?? 'BTC',
      cryptoAmount: (orderJson['amount'] is int)
          ? (orderJson['amount'] as int).toDouble()
          : orderJson['amount']?.toDouble() ?? 0.0,
      pricePerUnit: (orderJson['price_per_unit'] is int)
          ? (orderJson['price_per_unit'] as int).toDouble()
          : orderJson['price_per_unit']?.toDouble() ?? 0.0,
      totalAmount: (orderJson['total_amount'] is int)
          ? (orderJson['total_amount'] as int).toDouble()
          : orderJson['total_amount']?.toDouble() ?? 0.0,
      paymentMethod: orderJson['payment_method'] ?? 'Bank Transfer',
      status: orderJson['status'] ?? 'pending',
      matchedAt: orderJson['matched_at'] != null ? DateTime.parse(orderJson['matched_at']) : null,
      completedAt: orderJson['completed_at'] != null ? DateTime.parse(orderJson['completed_at']) : null,
      paymentTransactionId: orderJson['payment_transaction_id'],
      cryptoTransactionId: orderJson['crypto_transaction_id'],
      termsAndConditions: orderJson['terms_and_conditions'] ?? '',
      additionalNotes: orderJson['additional_notes'] ?? '',
      reputationScore: 4.8, // Placeholder
      tradeCount: 12, // Placeholder
      disputeId: null, // Placeholder
      disputeStatus: null, // Placeholder
      disputeType: null, // Placeholder
      disputeDescription: null, // Placeholder
      escrowStatus: 'held', // Placeholder
      escrowReleasedAt: null, // Placeholder
      escrowRefundedAt: null, // Placeholder
      paymentConfirmedAt: null, // Placeholder
      cryptoReleasedAt: null, // Placeholder
      tradeSecurityLevel: 3, // Placeholder
      autoReleaseTimeHours: 24, // Placeholder
      lastActivityAt: DateTime.now(), // Placeholder
    );
  }

  // Get a specific trade (order) by ID
  Future<CryptoTrade> getTrade(int tradeId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto/orders/$tradeId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return _convertOrderToCryptoTrade(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load trade');
      }
    } else {
      throw Exception('Failed to load trade: ${response.statusCode}');
    }
  }

  // Get user's trades (orders)
  Future<List<CryptoTrade>> getUserTrades({
    String? status,
    String? tradeType,
    String? cryptoCurrency,
    int perPage = 10,
  }) async {
    String url = '$baseUrl/p2p-crypto/orders/my';

    // Add query parameters for filtering
    List<String> queryParams = [];
    if (status != null) queryParams.add('status=$status');

    if (queryParams.isNotEmpty) {
      url += '?' + queryParams.join('&');
    }

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        // Convert the response to match our model expectations
        List<dynamic> rawOrders = data['data'];
        return rawOrders.map((json) => _convertOrderToCryptoTrade(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load trades');
      }
    } else {
      throw Exception('Failed to load trades: ${response.statusCode}');
    }
  }

  // Process payment for an order
  Future<CryptoTrade> confirmPayment(int tradeId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/p2p-crypto/orders/$tradeId/payment'),
      headers: getHeaders(),
      body: jsonEncode({
        'payment_method': 'Bank Transfer', // Default payment method
        'amount': 0.0, // Will be calculated on backend
        'proof_of_payment': 'Mobile payment confirmation',
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return _convertOrderToCryptoTrade(data['data']['order']);
      } else {
        throw Exception(data['message'] ?? 'Failed to process payment');
      }
    } else {
      throw Exception('Failed to process payment: ${response.statusCode}');
    }
  }

  // Release crypto from escrow (seller action)
  Future<CryptoTrade> releaseCrypto(int tradeId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/p2p-crypto/orders/$tradeId/release-escrow'),
      headers: getHeaders(),
      body: jsonEncode({
        'notes': 'Released from mobile app',
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return _convertOrderToCryptoTrade(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to release crypto from escrow');
      }
    } else {
      throw Exception('Failed to release crypto from escrow: ${response.statusCode}');
    }
  }

  // Get matching listings for a trade (not directly implemented in our backend)
  // Instead, we'll return active orders that match the criteria
  Future<List<CryptoListing>> getMatchingListings({
    required String cryptoCurrency,
    required String fiatCurrency,
    required String tradeType,
    required double amount,
  }) async {
    // For now, return all active orders of the requested crypto currency
    // with the opposite trade type (if user wants to buy, show sell orders)
    String oppositeTradeType = tradeType == 'buy' ? 'sell' : 'buy';
    final orders = await getActiveListings(
      cryptoCurrency: cryptoCurrency,
      tradeType: oppositeTradeType,
    );
    return orders;
  }

  // Get trade statistics for the user
  Future<Map<String, dynamic>> getTradeStatistics() async {
    // Since we don't have a specific statistics endpoint, we'll return mock data
    // based on the user's orders
    try {
      final userOrders = await getUserListings();

      int completedOrders = userOrders.where((order) => order.status == 'completed').length;
      int activeOrders = userOrders.where((order) => order.status == 'active').length;

      // Calculate total traded amount
      double totalTraded = userOrders
          .where((order) => order.status == 'completed')
          .fold(0.0, (sum, order) => sum + (order.pricePerUnit * order.availableAmount));

      return {
        'success': true,
        'statistics': {
          'total_orders': userOrders.length,
          'completed_orders': completedOrders,
          'active_orders': activeOrders,
          'total_traded_amount': totalTraded,
        }
      };
    } catch (e) {
      throw Exception('Failed to get trade statistics: $e');
    }
  }

  // Cancel an order
  Future<bool> deleteListing(int listingId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/p2p-crypto/orders/$listingId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to cancel order: ${response.statusCode}');
    }
  }
}