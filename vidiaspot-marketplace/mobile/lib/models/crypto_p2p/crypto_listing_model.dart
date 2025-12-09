// lib/models/crypto_p2p/crypto_listing_model.dart
class CryptoListing {
  final int id;
  final int userId;
  final String cryptoCurrency;
  final String fiatCurrency;
  final String tradeType;
  final double pricePerUnit;
  final double minTradeAmount;
  final double maxTradeAmount;
  final double availableAmount;
  final List<String> paymentMethods;
  final double tradingFeePercent;
  final double tradingFeeFixed;
  final String location;
  final double locationRadius;
  final List<String> tradingTerms;
  final bool negotiable;
  final bool autoAccept;
  final int autoReleaseTimeHours;
  final int verificationLevelRequired;
  final int tradeSecurityLevel;
  final double reputationScore;
  final int tradeCount;
  final double completionRate;
  final bool onlineStatus;
  final String status;
  final bool isPublic;
  final bool featured;
  final bool pinned;
  final DateTime? expiresAt;
  final Map<String, dynamic>? metadata;

  CryptoListing({
    required this.id,
    required this.userId,
    required this.cryptoCurrency,
    required this.fiatCurrency,
    required this.tradeType,
    required this.pricePerUnit,
    required this.minTradeAmount,
    required this.maxTradeAmount,
    required this.availableAmount,
    required this.paymentMethods,
    required this.tradingFeePercent,
    required this.tradingFeeFixed,
    required this.location,
    required this.locationRadius,
    required this.tradingTerms,
    required this.negotiable,
    required this.autoAccept,
    required this.autoReleaseTimeHours,
    required this.verificationLevelRequired,
    required this.tradeSecurityLevel,
    required this.reputationScore,
    required this.tradeCount,
    required this.completionRate,
    required this.onlineStatus,
    required this.status,
    required this.isPublic,
    required this.featured,
    required this.pinned,
    this.expiresAt,
    this.metadata,
  });

  factory CryptoListing.fromJson(Map<String, dynamic> json) {
    return CryptoListing(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? 0,
      cryptoCurrency: json['crypto_currency'] ?? '',
      fiatCurrency: json['fiat_currency'] ?? 'NGN',
      tradeType: json['trade_type'] ?? 'buy',
      pricePerUnit: (json['price_per_unit'] is int) 
          ? (json['price_per_unit'] as int).toDouble() 
          : json['price_per_unit']?.toDouble() ?? 0.0,
      minTradeAmount: (json['min_trade_amount'] is int) 
          ? (json['min_trade_amount'] as int).toDouble() 
          : json['min_trade_amount']?.toDouble() ?? 0.0,
      maxTradeAmount: (json['max_trade_amount'] is int) 
          ? (json['max_trade_amount'] as int).toDouble() 
          : json['max_trade_amount']?.toDouble() ?? 0.0,
      availableAmount: (json['available_amount'] is int) 
          ? (json['available_amount'] as int).toDouble() 
          : json['available_amount']?.toDouble() ?? 0.0,
      paymentMethods: List<String>.from(json['payment_methods'] ?? []),
      tradingFeePercent: (json['trading_fee_percent'] is int) 
          ? (json['trading_fee_percent'] as int).toDouble() 
          : json['trading_fee_percent']?.toDouble() ?? 0.0,
      tradingFeeFixed: (json['trading_fee_fixed'] is int) 
          ? (json['trading_fee_fixed'] as int).toDouble() 
          : json['trading_fee_fixed']?.toDouble() ?? 0.0,
      location: json['location'] ?? '',
      locationRadius: (json['location_radius'] is int) 
          ? (json['location_radius'] as int).toDouble() 
          : json['location_radius']?.toDouble() ?? 0.0,
      tradingTerms: List<String>.from(json['trading_terms'] ?? []),
      negotiable: json['negotiable'] ?? false,
      autoAccept: json['auto_accept'] ?? false,
      autoReleaseTimeHours: json['auto_release_time_hours'] ?? 24,
      verificationLevelRequired: json['verification_level_required'] ?? 1,
      tradeSecurityLevel: json['trade_security_level'] ?? 1,
      reputationScore: (json['reputation_score'] is int) 
          ? (json['reputation_score'] as int).toDouble() 
          : json['reputation_score']?.toDouble() ?? 0.0,
      tradeCount: json['trade_count'] ?? 0,
      completionRate: (json['completion_rate'] is int) 
          ? (json['completion_rate'] as int).toDouble() 
          : json['completion_rate']?.toDouble() ?? 0.0,
      onlineStatus: json['online_status'] ?? true,
      status: json['status'] ?? 'active',
      isPublic: json['is_public'] ?? true,
      featured: json['featured'] ?? false,
      pinned: json['pinned'] ?? false,
      expiresAt: json['expires_at'] != null ? DateTime.parse(json['expires_at']) : null,
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'crypto_currency': cryptoCurrency,
      'fiat_currency': fiatCurrency,
      'trade_type': tradeType,
      'price_per_unit': pricePerUnit,
      'min_trade_amount': minTradeAmount,
      'max_trade_amount': maxTradeAmount,
      'available_amount': availableAmount,
      'payment_methods': paymentMethods,
      'trading_fee_percent': tradingFeePercent,
      'trading_fee_fixed': tradingFeeFixed,
      'location': location,
      'location_radius': locationRadius,
      'trading_terms': tradingTerms,
      'negotiable': negotiable,
      'auto_accept': autoAccept,
      'auto_release_time_hours': autoReleaseTimeHours,
      'verification_level_required': verificationLevelRequired,
      'trade_security_level': tradeSecurityLevel,
      'reputation_score': reputationScore,
      'trade_count': tradeCount,
      'completion_rate': completionRate,
      'online_status': onlineStatus,
      'status': status,
      'is_public': isPublic,
      'featured': featured,
      'pinned': pinned,
      'expires_at': expiresAt?.toIso8601String(),
      'metadata': metadata,
    };
  }
}