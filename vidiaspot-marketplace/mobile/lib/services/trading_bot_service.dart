// lib/services/trading_bot_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/crypto_p2p/crypto_listing_model.dart';

class TradingBotService {
  final String baseUrl = 'http://10.0.2.2:8000/api'; // Update to match your backend
  String? _authToken;

  TradingBotService() {
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

  // Create custom trading bot
  Future<TradingBot> createTradingBot({
    required String name,
    required String strategyType, // 'mean_reversion', 'momentum', 'arbitrage', etc.
    required String cryptoPair,
    required double initialInvestment,
    required Map<String, dynamic> parameters,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading-bots'),
      headers: getHeaders(),
      body: jsonEncode({
        'name': name,
        'strategy_type': strategyType,
        'crypto_pair': cryptoPair,
        'initial_investment': initialInvestment,
        'parameters': parameters,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return TradingBot.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create trading bot');
      }
    } else {
      throw Exception('Failed to create trading bot: ${response.statusCode}');
    }
  }

  // Get user's trading bots
  Future<List<TradingBot>> getUserTradingBots() async {
    final response = await http.get(
      Uri.parse('$baseUrl/trading-bots'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> bots = data['data'];
        return bots.map((json) => TradingBot.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to get trading bots');
      }
    } else {
      throw Exception('Failed to get trading bots: ${response.statusCode}');
    }
  }

  // Get trading bot by ID
  Future<TradingBot> getTradingBot(String botId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/trading-bots/$botId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return TradingBot.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to get trading bot');
      }
    } else {
      throw Exception('Failed to get trading bot: ${response.statusCode}');
    }
  }

  // Update trading bot configuration
  Future<TradingBot> updateTradingBot({
    required String botId,
    String? name,
    Map<String, dynamic>? parameters,
    bool? isActive,
  }) async {
    final response = await http.put(
      Uri.parse('$baseUrl/trading-bots/$botId'),
      headers: getHeaders(),
      body: jsonEncode({
        if (name != null) 'name': name,
        if (parameters != null) 'parameters': parameters,
        if (isActive != null) 'is_active': isActive,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return TradingBot.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to update trading bot');
      }
    } else {
      throw Exception('Failed to update trading bot: ${response.statusCode}');
    }
  }

  // Start/stop trading bot
  Future<bool> toggleTradingBot(String botId, bool start) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading-bots/$botId/${start ? 'start' : 'stop'}'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to toggle trading bot: ${response.statusCode}');
    }
  }

  // Delete trading bot
  Future<bool> deleteTradingBot(String botId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/trading-bots/$botId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to delete trading bot: ${response.statusCode}');
    }
  }

  // Get bot performance
  Future<BotPerformance> getBotPerformance(String botId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/trading-bots/$botId/performance'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return BotPerformance.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to get bot performance');
      }
    } else {
      throw Exception('Failed to get bot performance: ${response.statusCode}');
    }
  }

  // API access for developers
  Future<ApiAccess> getApiAccess() async {
    final response = await http.get(
      Uri.parse('$baseUrl/trading/api-access'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return ApiAccess.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to get API access');
      }
    } else {
      throw Exception('Failed to get API access: ${response.statusCode}');
    }
  }

  // Generate API key
  Future<ApiKey> generateApiKey({
    required String name,
    required List<String> permissions,
    DateTime? expirationDate,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading/api-keys'),
      headers: getHeaders(),
      body: jsonEncode({
        'name': name,
        'permissions': permissions,
        'expiration_date': expirationDate?.toIso8601String(),
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return ApiKey.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to generate API key');
      }
    } else {
      throw Exception('Failed to generate API key: ${response.statusCode}');
    }
  }

  // Webhook notifications
  Future<WebhookConfig> configureWebhook({
    required String eventType, // 'order_filled', 'price_alert', 'bot_performance', etc.
    required String url,
    bool? isActive,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading/webhooks'),
      headers: getHeaders(),
      body: jsonEncode({
        'event_type': eventType,
        'url': url,
        'is_active': isActive ?? true,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return WebhookConfig.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to configure webhook');
      }
    } else {
      throw Exception('Failed to configure webhook: ${response.statusCode}');
    }
  }

  // Automated strategy backtesting
  Future<BacktestResult> runBacktest({
    required Map<String, dynamic> strategy,
    required String cryptoPair,
    required DateTime startDate,
    required DateTime endDate,
    required double initialCapital,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading/backtest'),
      headers: getHeaders(),
      body: jsonEncode({
        'strategy': strategy,
        'crypto_pair': cryptoPair,
        'start_date': startDate.toIso8601String(),
        'end_date': endDate.toIso8601String(),
        'initial_capital': initialCapital,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return BacktestResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to run backtest');
      }
    } else {
      throw Exception('Failed to run backtest: ${response.statusCode}');
    }
  }

  // Strategy sharing marketplace
  Future<List<StrategyTemplate>> getAvailableStrategies() async {
    final response = await http.get(
      Uri.parse('$baseUrl/trading/strategies'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> strategies = data['data'];
        return strategies.map((json) => StrategyTemplate.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to get strategies');
      }
    } else {
      throw Exception('Failed to get strategies: ${response.statusCode}');
    }
  }

  // Technical indicator builders
  Future<List<IndicatorResult>> calculateIndicators({
    required String cryptoPair,
    required String timeframe,
    required List<String> indicators,
    int period = 30,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading/indicators'),
      headers: getHeaders(),
      body: jsonEncode({
        'crypto_pair': cryptoPair,
        'timeframe': timeframe,
        'indicators': indicators,
        'period': period,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> results = data['data'];
        return results.map((json) => IndicatorResult.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to calculate indicators');
      }
    } else {
      throw Exception('Failed to calculate indicators: ${response.statusCode}');
    }
  }

  // Custom alert systems
  Future<AlertConfig> createPriceAlert({
    required String cryptoPair,
    required String condition, // 'above', 'below', 'equals'
    required double targetPrice,
    String? notificationMethod, // 'push', 'email', 'webhook'
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading/alerts'),
      headers: getHeaders(),
      body: jsonEncode({
        'crypto_pair': cryptoPair,
        'condition': condition,
        'target_price': targetPrice,
        'notification_method': notificationMethod ?? 'push',
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return AlertConfig.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create price alert');
      }
    } else {
      throw Exception('Failed to create price alert: ${response.statusCode}');
    }
  }

  // Market making tools
  Future<MarketMakingResult> configureMarketMaking({
    required String cryptoPair,
    required double spreadPercent,
    required double orderSize,
    required int orderCount,
    required double minDistance,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/trading/market-making'),
      headers: getHeaders(),
      body: jsonEncode({
        'crypto_pair': cryptoPair,
        'spread_percent': spreadPercent,
        'order_size': orderSize,
        'order_count': orderCount,
        'min_distance': minDistance,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return MarketMakingResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to configure market making');
      }
    } else {
      throw Exception('Failed to configure market making: ${response.statusCode}');
    }
  }
}

// Data models for advanced trading features
class TradingBot {
  final String id;
  final String name;
  final String userId;
  final String strategyType;
  final String cryptoPair;
  final double initialInvestment;
  final double currentBalance;
  final bool isActive;
  final bool isRunning;
  final Map<String, dynamic> parameters;
  final double profitLoss;
  final double profitLossPercent;
  final int totalTrades;
  final double winRate;
  final DateTime createdAt;
  final DateTime updatedAt;

  TradingBot({
    required this.id,
    required this.name,
    required this.userId,
    required this.strategyType,
    required this.cryptoPair,
    required this.initialInvestment,
    required this.currentBalance,
    required this.isActive,
    required this.isRunning,
    required this.parameters,
    required this.profitLoss,
    required this.profitLossPercent,
    required this.totalTrades,
    required this.winRate,
    required this.createdAt,
    required this.updatedAt,
  });

  factory TradingBot.fromJson(Map<String, dynamic> json) {
    return TradingBot(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      userId: json['user_id'] ?? '',
      strategyType: json['strategy_type'] ?? '',
      cryptoPair: json['crypto_pair'] ?? '',
      initialInvestment: (json['initial_investment'] is int) 
          ? (json['initial_investment'] as int).toDouble() 
          : json['initial_investment']?.toDouble() ?? 0.0,
      currentBalance: (json['current_balance'] is int) 
          ? (json['current_balance'] as int).toDouble() 
          : json['current_balance']?.toDouble() ?? 0.0,
      isActive: json['is_active'] ?? false,
      isRunning: json['is_running'] ?? false,
      parameters: json['parameters'] ?? {},
      profitLoss: (json['profit_loss'] is int) 
          ? (json['profit_loss'] as int).toDouble() 
          : json['profit_loss']?.toDouble() ?? 0.0,
      profitLossPercent: (json['profit_loss_percent'] is int) 
          ? (json['profit_loss_percent'] as int).toDouble() 
          : json['profit_loss_percent']?.toDouble() ?? 0.0,
      totalTrades: json['total_trades'] ?? 0,
      winRate: (json['win_rate'] is int) 
          ? (json['win_rate'] as int).toDouble() 
          : json['win_rate']?.toDouble() ?? 0.0,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'user_id': userId,
      'strategy_type': strategyType,
      'crypto_pair': cryptoPair,
      'initial_investment': initialInvestment,
      'current_balance': currentBalance,
      'is_active': isActive,
      'is_running': isRunning,
      'parameters': parameters,
      'profit_loss': profitLoss,
      'profit_loss_percent': profitLossPercent,
      'total_trades': totalTrades,
      'win_rate': winRate,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}

class BotPerformance {
  final String botId;
  final double totalReturn;
  final double annualizedReturn;
  final double volatility;
  final double sharpeRatio;
  final double maxDrawdown;
  final int totalTrades;
  final double winRate;
  final double profitFactor;
  final List<TradeRecord> tradeHistory;
  final List<PerformanceMetric> performanceMetrics;
  final DateTime asOf;

  BotPerformance({
    required this.botId,
    required this.totalReturn,
    required this.annualizedReturn,
    required this.volatility,
    required this.sharpeRatio,
    required this.maxDrawdown,
    required this.totalTrades,
    required this.winRate,
    required this.profitFactor,
    required this.tradeHistory,
    required this.performanceMetrics,
    required this.asOf,
  });

  factory BotPerformance.fromJson(Map<String, dynamic> json) {
    return BotPerformance(
      botId: json['bot_id'] ?? '',
      totalReturn: (json['total_return'] is int) 
          ? (json['total_return'] as int).toDouble() 
          : json['total_return']?.toDouble() ?? 0.0,
      annualizedReturn: (json['annualized_return'] is int) 
          ? (json['annualized_return'] as int).toDouble() 
          : json['annualized_return']?.toDouble() ?? 0.0,
      volatility: (json['volatility'] is int) 
          ? (json['volatility'] as int).toDouble() 
          : json['volatility']?.toDouble() ?? 0.0,
      sharpeRatio: (json['sharpe_ratio'] is int) 
          ? (json['sharpe_ratio'] as int).toDouble() 
          : json['sharpe_ratio']?.toDouble() ?? 0.0,
      maxDrawdown: (json['max_drawdown'] is int) 
          ? (json['max_drawdown'] as int).toDouble() 
          : json['max_drawdown']?.toDouble() ?? 0.0,
      totalTrades: json['total_trades'] ?? 0,
      winRate: (json['win_rate'] is int) 
          ? (json['win_rate'] as int).toDouble() 
          : json['win_rate']?.toDouble() ?? 0.0,
      profitFactor: (json['profit_factor'] is int) 
          ? (json['profit_factor'] as int).toDouble() 
          : json['profit_factor']?.toDouble() ?? 0.0,
      tradeHistory: (json['trade_history'] as List?)
          ?.map((t) => TradeRecord.fromJson(t))
          .toList() ?? [],
      performanceMetrics: (json['performance_metrics'] as List?)
          ?.map((m) => PerformanceMetric.fromJson(m))
          .toList() ?? [],
      asOf: DateTime.parse(json['as_of'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'bot_id': botId,
      'total_return': totalReturn,
      'annualized_return': annualizedReturn,
      'volatility': volatility,
      'sharpe_ratio': sharpeRatio,
      'max_drawdown': maxDrawdown,
      'total_trades': totalTrades,
      'win_rate': winRate,
      'profit_factor': profitFactor,
      'trade_history': tradeHistory.map((t) => t.toJson()).toList(),
      'performance_metrics': performanceMetrics.map((m) => m.toJson()).toList(),
      'as_of': asOf.toIso8601String(),
    };
  }
}

class TradeRecord {
  final String tradeId;
  final String botId;
  final String type; // 'buy' or 'sell'
  final String cryptoPair;
  final double amount;
  final double price;
  final double fee;
  final double profitLoss;
  final DateTime executedAt;

  TradeRecord({
    required this.tradeId,
    required this.botId,
    required this.type,
    required this.cryptoPair,
    required this.amount,
    required this.price,
    required this.fee,
    required this.profitLoss,
    required this.executedAt,
  });

  factory TradeRecord.fromJson(Map<String, dynamic> json) {
    return TradeRecord(
      tradeId: json['trade_id'] ?? '',
      botId: json['bot_id'] ?? '',
      type: json['type'] ?? '',
      cryptoPair: json['crypto_pair'] ?? '',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      price: (json['price'] is int) 
          ? (json['price'] as int).toDouble() 
          : json['price']?.toDouble() ?? 0.0,
      fee: (json['fee'] is int) 
          ? (json['fee'] as int).toDouble() 
          : json['fee']?.toDouble() ?? 0.0,
      profitLoss: (json['profit_loss'] is int) 
          ? (json['profit_loss'] as int).toDouble() 
          : json['profit_loss']?.toDouble() ?? 0.0,
      executedAt: DateTime.parse(json['executed_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'trade_id': tradeId,
      'bot_id': botId,
      'type': type,
      'crypto_pair': cryptoPair,
      'amount': amount,
      'price': price,
      'fee': fee,
      'profit_loss': profitLoss,
      'executed_at': executedAt.toIso8601String(),
    };
  }
}

class PerformanceMetric {
  final String metricName;
  final double value;
  final String description;

  PerformanceMetric({
    required this.metricName,
    required this.value,
    required this.description,
  });

  factory PerformanceMetric.fromJson(Map<String, dynamic> json) {
    return PerformanceMetric(
      metricName: json['metric_name'] ?? '',
      value: (json['value'] is int) 
          ? (json['value'] as int).toDouble() 
          : json['value']?.toDouble() ?? 0.0,
      description: json['description'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'metric_name': metricName,
      'value': value,
      'description': description,
    };
  }
}

class ApiAccess {
  final bool isEnabled;
  final String apiKey;
  final String apiSecret;
  final List<String> permissions;
  final int rateLimit;
  final DateTime createdAt;
  final DateTime updatedAt;

  ApiAccess({
    required this.isEnabled,
    required this.apiKey,
    required this.apiSecret,
    required this.permissions,
    required this.rateLimit,
    required this.createdAt,
    required this.updatedAt,
  });

  factory ApiAccess.fromJson(Map<String, dynamic> json) {
    return ApiAccess(
      isEnabled: json['is_enabled'] ?? false,
      apiKey: json['api_key'] ?? '',
      apiSecret: json['api_secret'] ?? '',
      permissions: List<String>.from(json['permissions'] ?? []),
      rateLimit: json['rate_limit'] ?? 1000,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'is_enabled': isEnabled,
      'api_key': apiKey,
      'api_secret': apiSecret,
      'permissions': permissions,
      'rate_limit': rateLimit,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}

class ApiKey {
  final String id;
  final String name;
  final String key;
  final String secret;
  final List<String> permissions;
  final bool isActive;
  final DateTime createdAt;
  final DateTime? expirationDate;

  ApiKey({
    required this.id,
    required this.name,
    required this.key,
    required this.secret,
    required this.permissions,
    required this.isActive,
    required this.createdAt,
    this.expirationDate,
  });

  factory ApiKey.fromJson(Map<String, dynamic> json) {
    return ApiKey(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      key: json['key'] ?? '',
      secret: json['secret'] ?? '',
      permissions: List<String>.from(json['permissions'] ?? []),
      isActive: json['is_active'] ?? true,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      expirationDate: json['expiration_date'] != null 
          ? DateTime.parse(json['expiration_date']) 
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'key': key,
      'secret': secret,
      'permissions': permissions,
      'is_active': isActive,
      'created_at': createdAt.toIso8601String(),
      'expiration_date': expirationDate?.toIso8601String(),
    };
  }
}

class WebhookConfig {
  final String id;
  final String eventType;
  final String url;
  final bool isActive;
  final int failureCount;
  final DateTime lastAttempt;
  final DateTime createdAt;

  WebhookConfig({
    required this.id,
    required this.eventType,
    required this.url,
    required this.isActive,
    required this.failureCount,
    required this.lastAttempt,
    required this.createdAt,
  });

  factory WebhookConfig.fromJson(Map<String, dynamic> json) {
    return WebhookConfig(
      id: json['id'] ?? '',
      eventType: json['event_type'] ?? '',
      url: json['url'] ?? '',
      isActive: json['is_active'] ?? false,
      failureCount: json['failure_count'] ?? 0,
      lastAttempt: DateTime.parse(json['last_attempt'] ?? DateTime.now().toIso8601String()),
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_type': eventType,
      'url': url,
      'is_active': isActive,
      'failure_count': failureCount,
      'last_attempt': lastAttempt.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class BacktestResult {
  final String id;
  final String strategyName;
  final String cryptoPair;
  final DateTime startDate;
  final DateTime endDate;
  final double initialCapital;
  final double finalCapital;
  final double totalReturn;
  final double returnPercent;
  final int totalTrades;
  final double winRate;
  final double maxDrawdown;
  final List<TradeRecord> tradeHistory;
  final Map<String, dynamic> performanceMetrics;
  final DateTime completedAt;

  BacktestResult({
    required this.id,
    required this.strategyName,
    required this.cryptoPair,
    required this.startDate,
    required this.endDate,
    required this.initialCapital,
    required this.finalCapital,
    required this.totalReturn,
    required this.returnPercent,
    required this.totalTrades,
    required this.winRate,
    required this.maxDrawdown,
    required this.tradeHistory,
    required this.performanceMetrics,
    required this.completedAt,
  });

  factory BacktestResult.fromJson(Map<String, dynamic> json) {
    return BacktestResult(
      id: json['id'] ?? '',
      strategyName: json['strategy_name'] ?? '',
      cryptoPair: json['crypto_pair'] ?? '',
      startDate: DateTime.parse(json['start_date'] ?? DateTime.now().toIso8601String()),
      endDate: DateTime.parse(json['end_date'] ?? DateTime.now().toIso8601String()),
      initialCapital: (json['initial_capital'] is int) 
          ? (json['initial_capital'] as int).toDouble() 
          : json['initial_capital']?.toDouble() ?? 0.0,
      finalCapital: (json['final_capital'] is int) 
          ? (json['final_capital'] as int).toDouble() 
          : json['final_capital']?.toDouble() ?? 0.0,
      totalReturn: (json['total_return'] is int) 
          ? (json['total_return'] as int).toDouble() 
          : json['total_return']?.toDouble() ?? 0.0,
      returnPercent: (json['return_percent'] is int) 
          ? (json['return_percent'] as int).toDouble() 
          : json['return_percent']?.toDouble() ?? 0.0,
      totalTrades: json['total_trades'] ?? 0,
      winRate: (json['win_rate'] is int) 
          ? (json['win_rate'] as int).toDouble() 
          : json['win_rate']?.toDouble() ?? 0.0,
      maxDrawdown: (json['max_drawdown'] is int) 
          ? (json['max_drawdown'] as int).toDouble() 
          : json['max_drawdown']?.toDouble() ?? 0.0,
      tradeHistory: (json['trade_history'] as List?)
          ?.map((t) => TradeRecord.fromJson(t))
          .toList() ?? [],
      performanceMetrics: json['performance_metrics'] ?? {},
      completedAt: DateTime.parse(json['completed_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'strategy_name': strategyName,
      'crypto_pair': cryptoPair,
      'start_date': startDate.toIso8601String(),
      'end_date': endDate.toIso8601String(),
      'initial_capital': initialCapital,
      'final_capital': finalCapital,
      'total_return': totalReturn,
      'return_percent': returnPercent,
      'total_trades': totalTrades,
      'win_rate': winRate,
      'max_drawdown': maxDrawdown,
      'trade_history': tradeHistory.map((t) => t.toJson()).toList(),
      'performance_metrics': performanceMetrics,
      'completed_at': completedAt.toIso8601String(),
    };
  }
}

class StrategyTemplate {
  final String id;
  final String name;
  final String description;
  final String strategyType;
  final List<String> cryptoPairs;
  final bool isPublic;
  final int popularity;
  final double avgReturn;
  final Map<String, dynamic> parameters;
  final String author;
  final DateTime createdAt;

  StrategyTemplate({
    required this.id,
    required this.name,
    required this.description,
    required this.strategyType,
    required this.cryptoPairs,
    required this.isPublic,
    required this.popularity,
    required this.avgReturn,
    required this.parameters,
    required this.author,
    required this.createdAt,
  });

  factory StrategyTemplate.fromJson(Map<String, dynamic> json) {
    return StrategyTemplate(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      strategyType: json['strategy_type'] ?? '',
      cryptoPairs: List<String>.from(json['crypto_pairs'] ?? []),
      isPublic: json['is_public'] ?? false,
      popularity: json['popularity'] ?? 0,
      avgReturn: (json['avg_return'] is int) 
          ? (json['avg_return'] as int).toDouble() 
          : json['avg_return']?.toDouble() ?? 0.0,
      parameters: json['parameters'] ?? {},
      author: json['author'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'strategy_type': strategyType,
      'crypto_pairs': cryptoPairs,
      'is_public': isPublic,
      'popularity': popularity,
      'avg_return': avgReturn,
      'parameters': parameters,
      'author': author,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class IndicatorResult {
  final String indicatorName;
  final String cryptoPair;
  final String timeframe;
  final List<IndicatorValue> values;
  final Map<String, dynamic> metadata;

  IndicatorResult({
    required this.indicatorName,
    required this.cryptoPair,
    required this.timeframe,
    required this.values,
    required this.metadata,
  });

  factory IndicatorResult.fromJson(Map<String, dynamic> json) {
    return IndicatorResult(
      indicatorName: json['indicator_name'] ?? '',
      cryptoPair: json['crypto_pair'] ?? '',
      timeframe: json['timeframe'] ?? '',
      values: (json['values'] as List?)
          ?.map((v) => IndicatorValue.fromJson(v))
          .toList() ?? [],
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'indicator_name': indicatorName,
      'crypto_pair': cryptoPair,
      'timeframe': timeframe,
      'values': values.map((v) => v.toJson()).toList(),
      'metadata': metadata,
    };
  }
}

class IndicatorValue {
  final DateTime timestamp;
  final double value;
  final Map<String, dynamic> additionalValues;

  IndicatorValue({
    required this.timestamp,
    required this.value,
    required this.additionalValues,
  });

  factory IndicatorValue.fromJson(Map<String, dynamic> json) {
    return IndicatorValue(
      timestamp: DateTime.parse(json['timestamp'] ?? DateTime.now().toIso8601String()),
      value: (json['value'] is int) 
          ? (json['value'] as int).toDouble() 
          : json['value']?.toDouble() ?? 0.0,
      additionalValues: json['additional_values'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'timestamp': timestamp.toIso8601String(),
      'value': value,
      'additional_values': additionalValues,
    };
  }
}

class AlertConfig {
  final String id;
  final String cryptoPair;
  final String condition; // 'above', 'below', 'equals'
  final double targetPrice;
  final String notificationMethod;
  final bool isActive;
  final int triggerCount;
  final DateTime lastTriggered;
  final DateTime createdAt;

  AlertConfig({
    required this.id,
    required this.cryptoPair,
    required this.condition,
    required this.targetPrice,
    required this.notificationMethod,
    required this.isActive,
    required this.triggerCount,
    required this.lastTriggered,
    required this.createdAt,
  });

  factory AlertConfig.fromJson(Map<String, dynamic> json) {
    return AlertConfig(
      id: json['id'] ?? '',
      cryptoPair: json['crypto_pair'] ?? '',
      condition: json['condition'] ?? '',
      targetPrice: (json['target_price'] is int) 
          ? (json['target_price'] as int).toDouble() 
          : json['target_price']?.toDouble() ?? 0.0,
      notificationMethod: json['notification_method'] ?? '',
      isActive: json['is_active'] ?? false,
      triggerCount: json['trigger_count'] ?? 0,
      lastTriggered: DateTime.parse(json['last_triggered'] ?? DateTime.now().toIso8601String()),
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'crypto_pair': cryptoPair,
      'condition': condition,
      'target_price': targetPrice,
      'notification_method': notificationMethod,
      'is_active': isActive,
      'trigger_count': triggerCount,
      'last_triggered': lastTriggered.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class MarketMakingResult {
  final String id;
  final String cryptoPair;
  final double spreadPercent;
  final double orderSize;
  final int orderCount;
  final double minDistance;
  final String status;
  final List<Order> activeOrders;
  final DateTime createdAt;

  MarketMakingResult({
    required this.id,
    required this.cryptoPair,
    required this.spreadPercent,
    required this.orderSize,
    required this.orderCount,
    required this.minDistance,
    required this.status,
    required this.activeOrders,
    required this.createdAt,
  });

  factory MarketMakingResult.fromJson(Map<String, dynamic> json) {
    return MarketMakingResult(
      id: json['id'] ?? '',
      cryptoPair: json['crypto_pair'] ?? '',
      spreadPercent: (json['spread_percent'] is int) 
          ? (json['spread_percent'] as int).toDouble() 
          : json['spread_percent']?.toDouble() ?? 0.0,
      orderSize: (json['order_size'] is int) 
          ? (json['order_size'] as int).toDouble() 
          : json['order_size']?.toDouble() ?? 0.0,
      orderCount: json['order_count'] ?? 0,
      minDistance: (json['min_distance'] is int) 
          ? (json['min_distance'] as int).toDouble() 
          : json['min_distance']?.toDouble() ?? 0.0,
      status: json['status'] ?? 'active',
      activeOrders: (json['active_orders'] as List?)
          ?.map((o) => Order.fromJson(o))
          .toList() ?? [],
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'crypto_pair': cryptoPair,
      'spread_percent': spreadPercent,
      'order_size': orderSize,
      'order_count': orderCount,
      'min_distance': minDistance,
      'status': status,
      'active_orders': activeOrders.map((o) => o.toJson()).toList(),
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class Order {
  final String id;
  final String type; // 'buy' or 'sell'
  final double price;
  final double quantity;
  final String status; // 'open', 'filled', 'cancelled'
  final DateTime createdAt;

  Order({
    required this.id,
    required this.type,
    required this.price,
    required this.quantity,
    required this.status,
    required this.createdAt,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'] ?? '',
      type: json['type'] ?? '',
      price: (json['price'] is int) 
          ? (json['price'] as int).toDouble() 
          : json['price']?.toDouble() ?? 0.0,
      quantity: (json['quantity'] is int) 
          ? (json['quantity'] as int).toDouble() 
          : json['quantity']?.toDouble() ?? 0.0,
      status: json['status'] ?? 'open',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'type': type,
      'price': price,
      'quantity': quantity,
      'status': status,
      'created_at': createdAt.toIso8601String(),
    };
  }
}