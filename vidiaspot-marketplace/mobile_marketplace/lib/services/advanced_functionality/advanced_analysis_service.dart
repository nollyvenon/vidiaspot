// lib/services/advanced_functionality/advanced_analysis_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../../models/advanced_functionality/social_sentiment_model.dart';

class AdvancedAnalysisService {
  final String baseUrl = 'http://10.0.2.2:8000/api';
  String? _authToken;

  AdvancedAnalysisService() {
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

  // Get social sentiment analysis
  Future<SocialSentiment> getSocialSentiment({
    required String cryptoSymbol,
  }) async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-analysis/social-sentiment/$cryptoSymbol'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SocialSentiment.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load social sentiment');
      }
    } else {
      throw Exception('Failed to load social sentiment: ${response.statusCode}');
    }
  }

  // Get whale transaction alerts
  Future<List<WhaleTransaction>> getWhaleTransactions({
    String? cryptoSymbol,
    String? category, // 'whale', 'dolphin', 'shark'
    String? transactionType,
    DateTime? since,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/advanced-analysis/whale-transactions?page=$page&per_page=$perPage';
    if (cryptoSymbol != null) url += '&crypto_symbol=$cryptoSymbol';
    if (category != null) url += '&category=$category';
    if (transactionType != null) url += '&transaction_type=$transactionType';
    if (since != null) url += '&since=${since.toIso8601String()}';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> transactions = data['data'];
        return transactions.map((json) => WhaleTransaction.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load whale transactions');
      }
    } else {
      throw Exception('Failed to load whale transactions: ${response.statusCode}');
    }
  }

  // Get market manipulation detection
  Future<List<MarketManipulationDetection>> getMarketManipulationDetection({
    String? cryptoSymbol,
    String? detectionType,
    String? status,
    DateTime? since,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/advanced-analysis/market-manipulation?page=$page&per_page=$perPage';
    if (cryptoSymbol != null) url += '&crypto_symbol=$cryptoSymbol';
    if (detectionType != null) url += '&detection_type=$detectionType';
    if (status != null) url += '&status=$status';
    if (since != null) url += '&since=${since.toIso8601String()}';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> detections = data['data'];
        return detections.map((json) => MarketManipulationDetection.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load market manipulation detections');
      }
    } else {
      throw Exception('Failed to load market manipulation detections: ${response.statusCode}');
    }
  }

  // Get arbitrage opportunities
  Future<List<ArbitrageOpportunity>> getArbitrageOpportunities({
    String? cryptoSymbol,
    double? minProfitPercent,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/advanced-analysis/arbitrage-opportunities?page=$page&per_page=$perPage';
    if (cryptoSymbol != null) url += '&crypto_symbol=$cryptoSymbol';
    if (minProfitPercent != null) url += '&min_profit_percent=$minProfitPercent';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> opportunities = data['data'];
        return opportunities.map((json) => ArbitrageOpportunity.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load arbitrage opportunities');
      }
    } else {
      throw Exception('Failed to load arbitrage opportunities: ${response.statusCode}');
    }
  }

  // Get user's portfolio rebalancing options
  Future<List<PortfolioRebalancing>> getPortfolioRebalancingOptions() async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-analysis/portfolio-rebalancing'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> rebalancing = data['data'];
        return rebalancing.map((json) => PortfolioRebalancing.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load portfolio rebalancing options');
      }
    } else {
      throw Exception('Failed to load portfolio rebalancing options: ${response.statusCode}');
    }
  }

  // Create a new portfolio rebalancing strategy
  Future<PortfolioRebalancing> createPortfolioRebalancing({
    required Map<String, double> targetAllocation,
    required String strategy,
    required String frequency,
    required double tolerancePercent,
    required double rebalancingThreshold,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-analysis/portfolio-rebalancing'),
      headers: getHeaders(),
      body: jsonEncode({
        'target_allocation': targetAllocation,
        'strategy': strategy,
        'frequency': frequency,
        'tolerance_percent': tolerancePercent,
        'rebalancing_threshold': rebalancingThreshold,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return PortfolioRebalancing.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create portfolio rebalancing');
      }
    } else {
      throw Exception('Failed to create portfolio rebalancing: ${response.statusCode}');
    }
  }

  // Execute portfolio rebalancing
  Future<bool> executePortfolioRebalancing({
    required String rebalancingId,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-analysis/portfolio-rebalancing/$rebalancingId/execute'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to execute portfolio rebalancing: ${response.statusCode}');
    }
  }

  // Get tax loss harvesting opportunities
  Future<List<TaxLossHarvesting>> getTaxLossHarvestingOpportunities({
    String? cryptoSymbol,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/advanced-analysis/tax-loss-harvesting?page=$page&per_page=$perPage';
    if (cryptoSymbol != null) url += '&crypto_symbol=$cryptoSymbol';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> harvesting = data['data'];
        return harvesting.map((json) => TaxLossHarvesting.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load tax loss harvesting opportunities');
      }
    } else {
      throw Exception('Failed to load tax loss harvesting opportunities: ${response.statusCode}');
    }
  }

  // Execute tax loss harvesting
  Future<bool> executeTaxLossHarvesting({
    required String harvestingId,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-analysis/tax-loss-harvesting/$harvestingId/execute'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to execute tax loss harvesting: ${response.statusCode}');
    }
  }

  // Get recurring orders
  Future<List<RecurringOrder>> getRecurringOrders({
    String? type,
    String? status,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/advanced-analysis/recurring-orders?page=$page&per_page=$perPage';
    if (type != null) url += '&type=$type';
    if (status != null) url += '&status=$status';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> orders = data['data'];
        return orders.map((json) => RecurringOrder.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load recurring orders');
      }
    } else {
      throw Exception('Failed to load recurring orders: ${response.statusCode}');
    }
  }

  // Create a recurring order
  Future<RecurringOrder> createRecurringOrder({
    required String type,
    required String cryptoSymbol,
    required String fiatSymbol,
    required double amount,
    required String amountType,
    required String frequency,
    required String strategy,
    DateTime? startTime,
    DateTime? endTime,
    int? maxExecutions,
    double? limitPrice,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-analysis/recurring-orders'),
      headers: getHeaders(),
      body: jsonEncode({
        'type': type,
        'crypto_symbol': cryptoSymbol,
        'fiat_symbol': fiatSymbol,
        'amount': amount,
        'amount_type': amountType,
        'frequency': frequency,
        'strategy': strategy,
        'start_time': startTime?.toIso8601String() ?? DateTime.now().toIso8601String(),
        'end_time': endTime?.toIso8601String(),
        'max_executions': maxExecutions,
        'limit_price': limitPrice,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return RecurringOrder.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create recurring order');
      }
    } else {
      throw Exception('Failed to create recurring order: ${response.statusCode}');
    }
  }

  // Update recurring order
  Future<RecurringOrder> updateRecurringOrder({
    required String orderId,
    double? amount,
    String? status, // 'active', 'paused', 'cancelled'
    DateTime? endTime,
    int? maxExecutions,
  }) async {
    final response = await http.put(
      Uri.parse('$baseUrl/advanced-analysis/recurring-orders/$orderId'),
      headers: getHeaders(),
      body: jsonEncode({
        if (amount != null) 'amount': amount,
        if (status != null) 'status': status,
        if (endTime != null) 'end_time': endTime.toIso8601String(),
        if (maxExecutions != null) 'max_executions': maxExecutions,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return RecurringOrder.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to update recurring order');
      }
    } else {
      throw Exception('Failed to update recurring order: ${response.statusCode}');
    }
  }

  // Delete recurring order
  Future<bool> deleteRecurringOrder(String orderId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/advanced-analysis/recurring-orders/$orderId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to delete recurring order: ${response.statusCode}');
    }
  }

  // Get market sentiment by source
  Future<Map<String, dynamic>> getMarketSentimentBySource({
    required String cryptoSymbol,
  }) async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-analysis/social-sentiment/$cryptoSymbol/by-source'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to load market sentiment by source');
      }
    } else {
      throw Exception('Failed to load market sentiment by source: ${response.statusCode}');
    }
  }

  // Get large transaction alerts
  Future<List<WhaleTransaction>> getLargeTransactionAlerts({
    double? minAmountUSD,
    String? cryptoSymbol,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/advanced-analysis/large-transactions?page=$page&per_page=$perPage';
    if (minAmountUSD != null) url += '&min_amount_usd=$minAmountUSD';
    if (cryptoSymbol != null) url += '&crypto_symbol=$cryptoSymbol';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> transactions = data['data'];
        return transactions.map((json) => WhaleTransaction.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load large transaction alerts');
      }
    } else {
      throw Exception('Failed to load large transaction alerts: ${response.statusCode}');
    }
  }

  // Set up alerts for whale transactions
  Future<bool> setUpWhaleAlert({
    required String cryptoSymbol,
    required double minAmountUSD,
    required String notificationMethod, // 'push', 'email', 'webhook'
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-analysis/whale-alerts'),
      headers: getHeaders(),
      body: jsonEncode({
        'crypto_symbol': cryptoSymbol,
        'min_amount_usd': minAmountUSD,
        'notification_method': notificationMethod,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to set up whale alert: ${response.statusCode}');
    }
  }

  // Get all user alerts
  Future<List<Map<String, dynamic>>> getUserAlerts() async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-analysis/user-alerts'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return List<Map<String, dynamic>>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load user alerts');
      }
    } else {
      throw Exception('Failed to load user alerts: ${response.statusCode}');
    }
  }
}