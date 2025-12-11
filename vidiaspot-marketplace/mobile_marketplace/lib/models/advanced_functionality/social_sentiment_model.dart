// lib/models/advanced_functionality/social_sentiment_model.dart
class SocialSentiment {
  final String cryptoSymbol;
  final double sentimentScore; // -1 to 1, where -1 is very negative, 1 is very positive
  final int totalPosts;
  final int positivePosts;
  final int negativePosts;
  final int neutralPosts;
  final double volatilityImpact; // potential impact on price volatility
  final DateTime analyzedAt;
  final Map<String, dynamic> sources; // breakdown by source (Twitter, Reddit, etc.)
  final String trendDirection; // 'bullish', 'bearish', 'neutral'
  final double confidenceLevel; // 0-1 confidence in the analysis

  SocialSentiment({
    required this.cryptoSymbol,
    required this.sentimentScore,
    required this.totalPosts,
    required this.positivePosts,
    required this.negativePosts,
    required this.neutralPosts,
    required this.volatilityImpact,
    required this.analyzedAt,
    required this.sources,
    required this.trendDirection,
    required this.confidenceLevel,
  });

  factory SocialSentiment.fromJson(Map<String, dynamic> json) {
    return SocialSentiment(
      cryptoSymbol: json['crypto_symbol'] ?? '',
      sentimentScore: (json['sentiment_score'] is int)
          ? (json['sentiment_score'] as int).toDouble()
          : json['sentiment_score']?.toDouble() ?? 0.0,
      totalPosts: json['total_posts'] ?? 0,
      positivePosts: json['positive_posts'] ?? 0,
      negativePosts: json['negative_posts'] ?? 0,
      neutralPosts: json['neutral_posts'] ?? 0,
      volatilityImpact: (json['volatility_impact'] is int)
          ? (json['volatility_impact'] as int).toDouble()
          : json['volatility_impact']?.toDouble() ?? 0.0,
      analyzedAt: DateTime.parse(json['analyzed_at'] ?? DateTime.now().toIso8601String()),
      sources: json['sources'] ?? {},
      trendDirection: json['trend_direction'] ?? 'neutral',
      confidenceLevel: (json['confidence_level'] is int)
          ? (json['confidence_level'] as int).toDouble()
          : json['confidence_level']?.toDouble() ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'crypto_symbol': cryptoSymbol,
      'sentiment_score': sentimentScore,
      'total_posts': totalPosts,
      'positive_posts': positivePosts,
      'negative_posts': negativePosts,
      'neutral_posts': neutralPosts,
      'volatility_impact': volatilityImpact,
      'analyzed_at': analyzedAt.toIso8601String(),
      'sources': sources,
      'trend_direction': trendDirection,
      'confidence_level': confidenceLevel,
    };
  }
}

class WhaleTransaction {
  final String id;
  final String fromAddress;
  final String toAddress;
  final String cryptoSymbol;
  final double amount;
  final double valueUSD;
  final String transactionType; // 'transfer', 'exchange', 'swap', 'stake', 'unstake'
  final String category; // 'whale', 'dolphin', 'shark' based on amount
  final String blockchain;
  final DateTime timestamp;
  final String transactionHash;
  final double gasFee;
  final String label; // 'top_holder', 'exchange', 'miner', etc.
  final bool isAlertTriggered; // whether this transaction triggered an alert
  final double priceAtTime; // price of the crypto at transaction time

  WhaleTransaction({
    required this.id,
    required this.fromAddress,
    required this.toAddress,
    required this.cryptoSymbol,
    required this.amount,
    required this.valueUSD,
    required this.transactionType,
    required this.category,
    required this.blockchain,
    required this.timestamp,
    required this.transactionHash,
    required this.gasFee,
    required this.label,
    required this.isAlertTriggered,
    required this.priceAtTime,
  });

  factory WhaleTransaction.fromJson(Map<String, dynamic> json) {
    return WhaleTransaction(
      id: json['id'] ?? '',
      fromAddress: json['from_address'] ?? '',
      toAddress: json['to_address'] ?? '',
      cryptoSymbol: json['crypto_symbol'] ?? '',
      amount: (json['amount'] is int)
          ? (json['amount'] as int).toDouble()
          : json['amount']?.toDouble() ?? 0.0,
      valueUSD: (json['value_usd'] is int)
          ? (json['value_usd'] as int).toDouble()
          : json['value_usd']?.toDouble() ?? 0.0,
      transactionType: json['transaction_type'] ?? 'transfer',
      category: json['category'] ?? 'dolphin',
      blockchain: json['blockchain'] ?? '',
      timestamp: DateTime.parse(json['timestamp'] ?? DateTime.now().toIso8601String()),
      transactionHash: json['transaction_hash'] ?? '',
      gasFee: (json['gas_fee'] is int)
          ? (json['gas_fee'] as int).toDouble()
          : json['gas_fee']?.toDouble() ?? 0.0,
      label: json['label'] ?? 'unknown',
      isAlertTriggered: json['is_alert_triggered'] ?? false,
      priceAtTime: (json['price_at_time'] is int)
          ? (json['price_at_time'] as int).toDouble()
          : json['price_at_time']?.toDouble() ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'from_address': fromAddress,
      'to_address': toAddress,
      'crypto_symbol': cryptoSymbol,
      'amount': amount,
      'value_usd': valueUSD,
      'transaction_type': transactionType,
      'category': category,
      'blockchain': blockchain,
      'timestamp': timestamp.toIso8601String(),
      'transaction_hash': transactionHash,
      'gas_fee': gasFee,
      'label': label,
      'is_alert_triggered': isAlertTriggered,
      'price_at_time': priceAtTime,
    };
  }
}

class MarketManipulationDetection {
  final String id;
  final String cryptoSymbol;
  final String detectionType; // 'pump_dump', 'wash_trading', 'spoofing', 'frontrunning'
  final double confidenceScore; // 0-1 confidence in detection
  final double severityLevel; // 0-10 severity
  final String status; // 'detected', 'investigating', 'resolved', 'false_positive'
  final DateTime detectedAt;
  final DateTime? resolvedAt;
  final String description;
  final List<String> involvedAddresses;
  final double volumeChange; // percentage change in volume
  final double priceChange; // percentage change in price
  final String exchange;
  final Map<String, dynamic> evidence;
  final String reportUrl; // URL to detailed report

  MarketManipulationDetection({
    required this.id,
    required this.cryptoSymbol,
    required this.detectionType,
    required this.confidenceScore,
    required this.severityLevel,
    required this.status,
    required this.detectedAt,
    this.resolvedAt,
    required this.description,
    required this.involvedAddresses,
    required this.volumeChange,
    required this.priceChange,
    required this.exchange,
    required this.evidence,
    required this.reportUrl,
  });

  factory MarketManipulationDetection.fromJson(Map<String, dynamic> json) {
    return MarketManipulationDetection(
      id: json['id'] ?? '',
      cryptoSymbol: json['crypto_symbol'] ?? '',
      detectionType: json['detection_type'] ?? 'unknown',
      confidenceScore: (json['confidence_score'] is int)
          ? (json['confidence_score'] as int).toDouble()
          : json['confidence_score']?.toDouble() ?? 0.0,
      severityLevel: (json['severity_level'] is int)
          ? (json['severity_level'] as int).toDouble()
          : json['severity_level']?.toDouble() ?? 0.0,
      status: json['status'] ?? 'detected',
      detectedAt: DateTime.parse(json['detected_at'] ?? DateTime.now().toIso8601String()),
      resolvedAt: json['resolved_at'] != null ? DateTime.parse(json['resolved_at']) : null,
      description: json['description'] ?? '',
      involvedAddresses: List<String>.from(json['involved_addresses'] ?? []),
      volumeChange: (json['volume_change'] is int)
          ? (json['volume_change'] as int).toDouble()
          : json['volume_change']?.toDouble() ?? 0.0,
      priceChange: (json['price_change'] is int)
          ? (json['price_change'] as int).toDouble()
          : json['price_change']?.toDouble() ?? 0.0,
      exchange: json['exchange'] ?? 'all',
      evidence: json['evidence'] ?? {},
      reportUrl: json['report_url'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'crypto_symbol': cryptoSymbol,
      'detection_type': detectionType,
      'confidence_score': confidenceScore,
      'severity_level': severityLevel,
      'status': status,
      'detected_at': detectedAt.toIso8601String(),
      'resolved_at': resolvedAt?.toIso8601String(),
      'description': description,
      'involved_addresses': involvedAddresses,
      'volume_change': volumeChange,
      'price_change': priceChange,
      'exchange': exchange,
      'evidence': evidence,
      'report_url': reportUrl,
    };
  }
}

class ArbitrageOpportunity {
  final String id;
  final String cryptoSymbol;
  final String exchange1;
  final String exchange2;
  final double price1;
  final double price2;
  final double priceDifference;
  final double percentageDifference;
  final double volume1;
  final double volume2;
  final double potentialProfit;
  final double transactionCosts;
  final double netProfit;
  final String status; // 'active', 'executed', 'expired', 'insufficient_liquidity'
  final DateTime detectedAt;
  final DateTime? executedAt;
  final double minSpread; // minimum spread for opportunity to be valid
  final String pair; // trading pair (e.g., 'BTC/USDT')
  final double timeToExecuteSeconds; // estimated time to execute

  ArbitrageOpportunity({
    required this.id,
    required this.cryptoSymbol,
    required this.exchange1,
    required this.exchange2,
    required this.price1,
    required this.price2,
    required this.priceDifference,
    required this.percentageDifference,
    required this.volume1,
    required this.volume2,
    required this.potentialProfit,
    required this.transactionCosts,
    required this.netProfit,
    required this.status,
    required this.detectedAt,
    this.executedAt,
    required this.minSpread,
    required this.pair,
    required this.timeToExecuteSeconds,
  });

  factory ArbitrageOpportunity.fromJson(Map<String, dynamic> json) {
    return ArbitrageOpportunity(
      id: json['id'] ?? '',
      cryptoSymbol: json['crypto_symbol'] ?? '',
      exchange1: json['exchange1'] ?? '',
      exchange2: json['exchange2'] ?? '',
      price1: (json['price1'] is int)
          ? (json['price1'] as int).toDouble()
          : json['price1']?.toDouble() ?? 0.0,
      price2: (json['price2'] is int)
          ? (json['price2'] as int).toDouble()
          : json['price2']?.toDouble() ?? 0.0,
      priceDifference: (json['price_difference'] is int)
          ? (json['price_difference'] as int).toDouble()
          : json['price_difference']?.toDouble() ?? 0.0,
      percentageDifference: (json['percentage_difference'] is int)
          ? (json['percentage_difference'] as int).toDouble()
          : json['percentage_difference']?.toDouble() ?? 0.0,
      volume1: (json['volume1'] is int)
          ? (json['volume1'] as int).toDouble()
          : json['volume1']?.toDouble() ?? 0.0,
      volume2: (json['volume2'] is int)
          ? (json['volume2'] as int).toDouble()
          : json['volume2']?.toDouble() ?? 0.0,
      potentialProfit: (json['potential_profit'] is int)
          ? (json['potential_profit'] as int).toDouble()
          : json['potential_profit']?.toDouble() ?? 0.0,
      transactionCosts: (json['transaction_costs'] is int)
          ? (json['transaction_costs'] as int).toDouble()
          : json['transaction_costs']?.toDouble() ?? 0.0,
      netProfit: (json['net_profit'] is int)
          ? (json['net_profit'] as int).toDouble()
          : json['net_profit']?.toDouble() ?? 0.0,
      status: json['status'] ?? 'active',
      detectedAt: DateTime.parse(json['detected_at'] ?? DateTime.now().toIso8601String()),
      executedAt: json['executed_at'] != null ? DateTime.parse(json['executed_at']) : null,
      minSpread: (json['min_spread'] is int)
          ? (json['min_spread'] as int).toDouble()
          : json['min_spread']?.toDouble() ?? 0.0,
      pair: json['pair'] ?? '',
      timeToExecuteSeconds: (json['time_to_execute_seconds'] is int)
          ? (json['time_to_execute_seconds'] as int).toDouble()
          : json['time_to_execute_seconds']?.toDouble() ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'crypto_symbol': cryptoSymbol,
      'exchange1': exchange1,
      'exchange2': exchange2,
      'price1': price1,
      'price2': price2,
      'price_difference': priceDifference,
      'percentage_difference': percentageDifference,
      'volume1': volume1,
      'volume2': volume2,
      'potential_profit': potentialProfit,
      'transaction_costs': transactionCosts,
      'net_profit': netProfit,
      'status': status,
      'detected_at': detectedAt.toIso8601String(),
      'executed_at': executedAt?.toIso8601String(),
      'min_spread': minSpread,
      'pair': pair,
      'time_to_execute_seconds': timeToExecuteSeconds,
    };
  }
}

class PortfolioRebalancing {
  final String id;
  final int userId;
  final Map<String, double> targetAllocation; // symbol: percentage
  final Map<String, double> currentAllocation; // symbol: percentage
  final Map<String, double> targetWeights; // symbol: target weight
  final Map<String, double> currentWeights; // symbol: current weight
  final double tolerancePercent; // how much deviation is acceptable
  final String strategy; // 'equal_weight', 'market_cap_weighted', 'volatility_weighted'
  final double rebalancingThreshold; // when to trigger rebalancing
  final String frequency; // 'daily', 'weekly', 'monthly', 'quarterly', 'trigger_based'
  final bool isActive;
  final DateTime createdAt;
  final DateTime? lastRebalancedAt;
  final List<RebalancingTransaction> transactions; // transactions needed to rebalance
  final double estimatedCosts; // estimated transaction costs
  final double netEffect; // estimated net effect on portfolio

  PortfolioRebalancing({
    required this.id,
    required this.userId,
    required this.targetAllocation,
    required this.currentAllocation,
    required this.targetWeights,
    required this.currentWeights,
    required this.tolerancePercent,
    required this.strategy,
    required this.rebalancingThreshold,
    required this.frequency,
    required this.isActive,
    required this.createdAt,
    this.lastRebalancedAt,
    required this.transactions,
    required this.estimatedCosts,
    required this.netEffect,
  });

  factory PortfolioRebalancing.fromJson(Map<String, dynamic> json) {
    return PortfolioRebalancing(
      id: json['id'] ?? '',
      userId: json['user_id'] ?? 0,
      targetAllocation: Map<String, double>.from(json['target_allocation'] ?? {}),
      currentAllocation: Map<String, double>.from(json['current_allocation'] ?? {}),
      targetWeights: Map<String, double>.from(json['target_weights'] ?? {}),
      currentWeights: Map<String, double>.from(json['current_weights'] ?? {}),
      tolerancePercent: (json['tolerance_percent'] is int)
          ? (json['tolerance_percent'] as int).toDouble()
          : json['tolerance_percent']?.toDouble() ?? 0.0,
      strategy: json['strategy'] ?? 'equal_weight',
      rebalancingThreshold: (json['rebalancing_threshold'] is int)
          ? (json['rebalancing_threshold'] as int).toDouble()
          : json['rebalancing_threshold']?.toDouble() ?? 0.0,
      frequency: json['frequency'] ?? 'monthly',
      isActive: json['is_active'] ?? false,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      lastRebalancedAt: json['last_rebalanced_at'] != null ? DateTime.parse(json['last_rebalanced_at']) : null,
      transactions: (json['transactions'] as List?)
          ?.map((t) => RebalancingTransaction.fromJson(t))
          .toList() ?? [],
      estimatedCosts: (json['estimated_costs'] is int)
          ? (json['estimated_costs'] as int).toDouble()
          : json['estimated_costs']?.toDouble() ?? 0.0,
      netEffect: (json['net_effect'] is int)
          ? (json['net_effect'] as int).toDouble()
          : json['net_effect']?.toDouble() ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'target_allocation': targetAllocation,
      'current_allocation': currentAllocation,
      'target_weights': targetWeights,
      'current_weights': currentWeights,
      'tolerance_percent': tolerancePercent,
      'strategy': strategy,
      'rebalancing_threshold': rebalancingThreshold,
      'frequency': frequency,
      'is_active': isActive,
      'created_at': createdAt.toIso8601String(),
      'last_rebalanced_at': lastRebalancedAt?.toIso8601String(),
      'transactions': transactions.map((t) => t.toJson()).toList(),
      'estimated_costs': estimatedCosts,
      'net_effect': netEffect,
    };
  }
}

class RebalancingTransaction {
  final String fromSymbol;
  final String toSymbol;
  final double fromAmount;
  final double toAmount;
  final double estimatedPrice;
  final double estimatedCost;
  final String status; // 'pending', 'executed', 'failed'

  RebalancingTransaction({
    required this.fromSymbol,
    required this.toSymbol,
    required this.fromAmount,
    required this.toAmount,
    required this.estimatedPrice,
    required this.estimatedCost,
    required this.status,
  });

  factory RebalancingTransaction.fromJson(Map<String, dynamic> json) {
    return RebalancingTransaction(
      fromSymbol: json['from_symbol'] ?? '',
      toSymbol: json['to_symbol'] ?? '',
      fromAmount: (json['from_amount'] is int)
          ? (json['from_amount'] as int).toDouble()
          : json['from_amount']?.toDouble() ?? 0.0,
      toAmount: (json['to_amount'] is int)
          ? (json['to_amount'] as int).toDouble()
          : json['to_amount']?.toDouble() ?? 0.0,
      estimatedPrice: (json['estimated_price'] is int)
          ? (json['estimated_price'] as int).toDouble()
          : json['estimated_price']?.toDouble() ?? 0.0,
      estimatedCost: (json['estimated_cost'] is int)
          ? (json['estimated_cost'] as int).toDouble()
          : json['estimated_cost']?.toDouble() ?? 0.0,
      status: json['status'] ?? 'pending',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'from_symbol': fromSymbol,
      'to_symbol': toSymbol,
      'from_amount': fromAmount,
      'to_amount': toAmount,
      'estimated_price': estimatedPrice,
      'estimated_cost': estimatedCost,
      'status': status,
    };
  }
}

class TaxLossHarvesting {
  final String id;
  final int userId;
  final String cryptoSymbol;
  final double amount;
  final double purchasePrice;
  final double currentPrice;
  final double unrealizedLoss;
  final DateTime purchaseDate;
  final bool isEligibleForHarvesting;
  final double potentialTaxSavings;
  final String taxBracket;
  final double washSaleRuleDays; // days to avoid wash sale rule
  final DateTime earliestHarvestDate;
  final String status; // 'potential', 'recommended', 'executed', 'skipped'
  final DateTime createdAt;
  final DateTime? executedAt;

  TaxLossHarvesting({
    required this.id,
    required this.userId,
    required this.cryptoSymbol,
    required this.amount,
    required this.purchasePrice,
    required this.currentPrice,
    required this.unrealizedLoss,
    required this.purchaseDate,
    required this.isEligibleForHarvesting,
    required this.potentialTaxSavings,
    required this.taxBracket,
    required this.washSaleRuleDays,
    required this.earliestHarvestDate,
    required this.status,
    required this.createdAt,
    this.executedAt,
  });

  factory TaxLossHarvesting.fromJson(Map<String, dynamic> json) {
    return TaxLossHarvesting(
      id: json['id'] ?? '',
      userId: json['user_id'] ?? 0,
      cryptoSymbol: json['crypto_symbol'] ?? '',
      amount: (json['amount'] is int)
          ? (json['amount'] as int).toDouble()
          : json['amount']?.toDouble() ?? 0.0,
      purchasePrice: (json['purchase_price'] is int)
          ? (json['purchase_price'] as int).toDouble()
          : json['purchase_price']?.toDouble() ?? 0.0,
      currentPrice: (json['current_price'] is int)
          ? (json['current_price'] as int).toDouble()
          : json['current_price']?.toDouble() ?? 0.0,
      unrealizedLoss: (json['unrealized_loss'] is int)
          ? (json['unrealized_loss'] as int).toDouble()
          : json['unrealized_loss']?.toDouble() ?? 0.0,
      purchaseDate: DateTime.parse(json['purchase_date'] ?? DateTime.now().toIso8601String()),
      isEligibleForHarvesting: json['is_eligible_for_harvesting'] ?? false,
      potentialTaxSavings: (json['potential_tax_savings'] is int)
          ? (json['potential_tax_savings'] as int).toDouble()
          : json['potential_tax_savings']?.toDouble() ?? 0.0,
      taxBracket: json['tax_bracket'] ?? '25%',
      washSaleRuleDays: (json['wash_sale_rule_days'] is int)
          ? (json['wash_sale_rule_days'] as int).toDouble()
          : json['wash_sale_rule_days']?.toDouble() ?? 30.0,
      earliestHarvestDate: DateTime.parse(json['earliest_harvest_date'] ?? DateTime.now().toIso8601String()),
      status: json['status'] ?? 'potential',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      executedAt: json['executed_at'] != null ? DateTime.parse(json['executed_at']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'crypto_symbol': cryptoSymbol,
      'amount': amount,
      'purchase_price': purchasePrice,
      'current_price': currentPrice,
      'unrealized_loss': unrealizedLoss,
      'purchase_date': purchaseDate.toIso8601String(),
      'is_eligible_for_harvesting': isEligibleForHarvesting,
      'potential_tax_savings': potentialTaxSavings,
      'tax_bracket': taxBracket,
      'wash_sale_rule_days': washSaleRuleDays,
      'earliest_harvest_date': earliestHarvestDate.toIso8601String(),
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'executed_at': executedAt?.toIso8601String(),
    };
  }
}

class RecurringOrder {
  final String id;
  final int userId;
  final String type; // 'buy', 'sell'
  final String cryptoSymbol;
  final String fiatSymbol;
  final double amount; // amount in crypto or fiat depending on order type
  final String amountType; // 'fixed_crypto', 'fixed_fiat', 'percentage_portfolio'
  final String frequency; // 'daily', 'weekly', 'monthly', 'bi_weekly'
  final DateTime startTime;
  final DateTime? endTime; // null if indefinite
  final String status; // 'active', 'paused', 'completed', 'cancelled'
  final double nextExecutionAmount; // amount for next execution
  final DateTime nextExecutionTime;
  final int executedCount; // number of times executed
  final int maxExecutions; // maximum number of executions (null for unlimited)
  final String strategy; // 'market_price', 'limit_price', 'dollar_cost_average'
  final double? limitPrice; // for limit orders
  final double totalAmountExecuted; // total amount executed so far
  final double totalCost; // total cost so far
  final DateTime createdAt;
  final DateTime updatedAt;

  RecurringOrder({
    required this.id,
    required this.userId,
    required this.type,
    required this.cryptoSymbol,
    required this.fiatSymbol,
    required this.amount,
    required this.amountType,
    required this.frequency,
    required this.startTime,
    this.endTime,
    required this.status,
    required this.nextExecutionAmount,
    required this.nextExecutionTime,
    required this.executedCount,
    required this.maxExecutions,
    required this.strategy,
    this.limitPrice,
    required this.totalAmountExecuted,
    required this.totalCost,
    required this.createdAt,
    required this.updatedAt,
  });

  factory RecurringOrder.fromJson(Map<String, dynamic> json) {
    return RecurringOrder(
      id: json['id'] ?? '',
      userId: json['user_id'] ?? 0,
      type: json['type'] ?? 'buy',
      cryptoSymbol: json['crypto_symbol'] ?? '',
      fiatSymbol: json['fiat_symbol'] ?? 'USD',
      amount: (json['amount'] is int)
          ? (json['amount'] as int).toDouble()
          : json['amount']?.toDouble() ?? 0.0,
      amountType: json['amount_type'] ?? 'fixed_fiat',
      frequency: json['frequency'] ?? 'monthly',
      startTime: DateTime.parse(json['start_time'] ?? DateTime.now().toIso8601String()),
      endTime: json['end_time'] != null ? DateTime.parse(json['end_time']) : null,
      status: json['status'] ?? 'active',
      nextExecutionAmount: (json['next_execution_amount'] is int)
          ? (json['next_execution_amount'] as int).toDouble()
          : json['next_execution_amount']?.toDouble() ?? 0.0,
      nextExecutionTime: DateTime.parse(json['next_execution_time'] ?? DateTime.now().toIso8601String()),
      executedCount: json['executed_count'] ?? 0,
      maxExecutions: json['max_executions'] ?? 0,
      strategy: json['strategy'] ?? 'market_price',
      limitPrice: (json['limit_price'] is int)
          ? (json['limit_price'] as int).toDouble()
          : json['limit_price']?.toDouble(),
      totalAmountExecuted: (json['total_amount_executed'] is int)
          ? (json['total_amount_executed'] as int).toDouble()
          : json['total_amount_executed']?.toDouble() ?? 0.0,
      totalCost: (json['total_cost'] is int)
          ? (json['total_cost'] as int).toDouble()
          : json['total_cost']?.toDouble() ?? 0.0,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'type': type,
      'crypto_symbol': cryptoSymbol,
      'fiat_symbol': fiatSymbol,
      'amount': amount,
      'amount_type': amountType,
      'frequency': frequency,
      'start_time': startTime.toIso8601String(),
      'end_time': endTime?.toIso8601String(),
      'status': status,
      'next_execution_amount': nextExecutionAmount,
      'next_execution_time': nextExecutionTime.toIso8601String(),
      'executed_count': executedCount,
      'max_executions': maxExecutions,
      'strategy': strategy,
      'limit_price': limitPrice,
      'total_amount_executed': totalAmountExecuted,
      'total_cost': totalCost,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}