// lib/services/crypto_p2p_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/crypto_p2p/crypto_listing_model.dart';
import '../models/crypto_p2p/crypto_trade_model.dart';
import '../models/crypto_p2p/crypto_trade_transaction_model.dart';

class CryptoP2PService {
  final String baseUrl = 'http://10.0.2.2:8000'; // For Android emulator, adjust as needed
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

  // Get all active crypto listings
  Future<List<CryptoListing>> getActiveListings({
    String? cryptoCurrency,
    String? fiatCurrency = 'NGN',
    String? tradeType,
    String? search,
    int perPage = 12,
  }) async {
    String url = '$baseUrl/advanced-payments/crypto-p2p/listings?page=1&per_page=$perPage';
    
    if (cryptoCurrency != null) url += '&crypto_currency=$cryptoCurrency';
    if (fiatCurrency != null) url += '&fiat_currency=$fiatCurrency';
    if (tradeType != null) url += '&trade_type=$tradeType';
    if (search != null) url += '&search=$search';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return (data['listings']['data'] as List)
            .map((json) => CryptoListing.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load listings');
      }
    } else {
      throw Exception('Failed to load listings: ${response.statusCode}');
    }
  }

  // Create a new crypto listing
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
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/listings'),
      headers: getHeaders(),
      body: jsonEncode({
        'crypto_currency': cryptoCurrency.toUpperCase(),
        'fiat_currency': fiatCurrency.toUpperCase(),
        'trade_type': tradeType.toLowerCase(),
        'price_per_unit': pricePerUnit,
        'min_trade_amount': minTradeAmount,
        'max_trade_amount': maxTradeAmount,
        'available_amount': availableAmount,
        'payment_methods': paymentMethods,
        'trading_fee_percent': tradingFeePercent ?? 0,
        'trading_fee_fixed': tradingFeeFixed ?? 0,
        'negotiable': negotiable,
        'auto_accept': autoAccept,
        'verification_level_required': verificationLevelRequired,
        'trade_security_level': tradeSecurityLevel,
        'is_public': isPublic,
        'location': location,
        'location_radius': locationRadius,
        'trading_terms': tradingTerms ?? [],
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return CryptoListing.fromJson(data['listing']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create listing');
      }
    } else {
      throw Exception('Failed to create listing: ${response.statusCode}');
    }
  }

  // Get a specific listing
  Future<CryptoListing> getListing(int listingId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/listings/$listingId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return CryptoListing.fromJson(data);
    } else {
      throw Exception('Failed to load listing: ${response.statusCode}');
    }
  }

  // Get user's listings
  Future<List<CryptoListing>> getUserListings() async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/my-listings'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['listings'] as List)
            .map((json) => CryptoListing.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load user listings');
      }
    } else {
      throw Exception('Failed to load user listings: ${response.statusCode}');
    }
  }

  // Initiate a trade with a listing
  Future<CryptoTrade> initiateTrade({
    required int listingId,
    required double cryptoAmount,
    required String paymentMethod,
    Map<String, dynamic>? paymentDetails,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/listings/$listingId/trade'),
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
        return CryptoTrade.fromJson(data['trade']);
      } else {
        throw Exception(data['message'] ?? 'Failed to initiate trade');
      }
    } else {
      throw Exception('Failed to initiate trade: ${response.statusCode}');
    }
  }

  // Get a specific trade
  Future<CryptoTrade> getTrade(int tradeId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/trades/$tradeId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return CryptoTrade.fromJson(data);
    } else {
      throw Exception('Failed to load trade: ${response.statusCode}');
    }
  }

  // Get user's trades
  Future<List<CryptoTrade>> getUserTrades({
    String? status,
    String? tradeType,
    String? cryptoCurrency,
    int perPage = 10,
  }) async {
    String url = '$baseUrl/advanced-payments/crypto-p2p/my-trades?page=1&per_page=$perPage';
    
    if (status != null) url += '&status=$status';
    if (tradeType != null) url += '&trade_type=$tradeType';
    if (cryptoCurrency != null) url += '&crypto_currency=$cryptoCurrency';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return (data['trades']['data'] as List)
            .map((json) => CryptoTrade.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load trades');
      }
    } else {
      throw Exception('Failed to load trades: ${response.statusCode}');
    }
  }

  // Confirm payment for a trade
  Future<CryptoTrade> confirmPayment(int tradeId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/trades/$tradeId/confirm-payment'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return CryptoTrade.fromJson(data['trade']);
      } else {
        throw Exception(data['message'] ?? 'Failed to confirm payment');
      }
    } else {
      throw Exception('Failed to confirm payment: ${response.statusCode}');
    }
  }

  // Release crypto for a trade (seller action)
  Future<CryptoTrade> releaseCrypto(int tradeId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/trades/$tradeId/release-crypto'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return CryptoTrade.fromJson(data['trade']);
      } else {
        throw Exception(data['message'] ?? 'Failed to release crypto');
      }
    } else {
      throw Exception('Failed to release crypto: ${response.statusCode}');
    }
  }

  // Get matching listings for a trade
  Future<List<CryptoListing>> getMatchingListings({
    required String cryptoCurrency,
    required String fiatCurrency,
    required String tradeType,
    required double amount,
  }) async {
    String url = '$baseUrl/advanced-payments/crypto-p2p/matching-listings'
        '?crypto_currency=$cryptoCurrency'
        '&fiat_currency=$fiatCurrency'
        '&trade_type=$tradeType'
        '&amount=$amount';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return (data['listings'] as List)
            .map((json) => CryptoListing.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to get matching listings');
      }
    } else {
      throw Exception('Failed to get matching listings: ${response.statusCode}');
    }
  }

  // Get trade statistics for the user
  Future<Map<String, dynamic>> getTradeStatistics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/statistics'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['statistics'];
      } else {
        throw Exception(data['message'] ?? 'Failed to get trade statistics');
      }
    } else {
      throw Exception('Failed to get trade statistics: ${response.statusCode}');
    }
  }

  // Delete a listing
  Future<bool> deleteListing(int listingId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/advanced-payments/crypto-p2p/listings/$listingId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to delete listing: ${response.statusCode}');
    }
  }
}