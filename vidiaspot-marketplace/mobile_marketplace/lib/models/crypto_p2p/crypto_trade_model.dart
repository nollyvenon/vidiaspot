// lib/models/crypto_p2p/crypto_trade_model.dart
class CryptoTrade {
  final int id;
  final int listingId;
  final int buyerId;
  final int sellerId;
  final String tradeType;
  final String cryptoCurrency;
  final String fiatCurrency;
  final double cryptoAmount;
  final double fiatAmount;
  final double exchangeRate;
  final String paymentMethod;
  final String status;
  final String escrowAddress;
  final String tradeReference;
  final Map<String, dynamic>? paymentDetails;
  final String escrowStatus;
  final DateTime? paymentConfirmedAt;
  final DateTime? cryptoReleasedAt;
  final DateTime? tradeCompletedAt;
  final int? disputeId;
  final DateTime? disputeResolvedAt;
  final String? disputeResolution;
  final int? buyerRating;
  final int? sellerRating;
  final String? buyerReview;
  final String? sellerReview;
  final int securityLevel;
  final Map<String, dynamic>? tradeLimits;
  final bool verificationRequired;
  final Map<String, dynamic>? metadata;

  CryptoTrade({
    required this.id,
    required this.listingId,
    required this.buyerId,
    required this.sellerId,
    required this.tradeType,
    required this.cryptoCurrency,
    required this.fiatCurrency,
    required this.cryptoAmount,
    required this.fiatAmount,
    required this.exchangeRate,
    required this.paymentMethod,
    required this.status,
    required this.escrowAddress,
    required this.tradeReference,
    this.paymentDetails,
    required this.escrowStatus,
    this.paymentConfirmedAt,
    this.cryptoReleasedAt,
    this.tradeCompletedAt,
    this.disputeId,
    this.disputeResolvedAt,
    this.disputeResolution,
    this.buyerRating,
    this.sellerRating,
    this.buyerReview,
    this.sellerReview,
    required this.securityLevel,
    this.tradeLimits,
    required this.verificationRequired,
    this.metadata,
  });

  factory CryptoTrade.fromJson(Map<String, dynamic> json) {
    return CryptoTrade(
      id: json['id'] ?? 0,
      listingId: json['listing_id'] ?? 0,
      buyerId: json['buyer_id'] ?? 0,
      sellerId: json['seller_id'] ?? 0,
      tradeType: json['trade_type'] ?? 'buy',
      cryptoCurrency: json['crypto_currency'] ?? '',
      fiatCurrency: json['fiat_currency'] ?? 'NGN',
      cryptoAmount: (json['crypto_amount'] is int) 
          ? (json['crypto_amount'] as int).toDouble() 
          : json['crypto_amount']?.toDouble() ?? 0.0,
      fiatAmount: (json['fiat_amount'] is int) 
          ? (json['fiat_amount'] as int).toDouble() 
          : json['fiat_amount']?.toDouble() ?? 0.0,
      exchangeRate: (json['exchange_rate'] is int) 
          ? (json['exchange_rate'] as int).toDouble() 
          : json['exchange_rate']?.toDouble() ?? 0.0,
      paymentMethod: json['payment_method'] ?? '',
      status: json['status'] ?? 'pending',
      escrowAddress: json['escrow_address'] ?? '',
      tradeReference: json['trade_reference'] ?? '',
      paymentDetails: json['payment_details'] ?? {},
      escrowStatus: json['escrow_status'] ?? 'awaiting_deposit',
      paymentConfirmedAt: json['payment_confirmed_at'] != null ? DateTime.parse(json['payment_confirmed_at']) : null,
      cryptoReleasedAt: json['crypto_released_at'] != null ? DateTime.parse(json['crypto_released_at']) : null,
      tradeCompletedAt: json['trade_completed_at'] != null ? DateTime.parse(json['trade_completed_at']) : null,
      disputeId: json['dispute_id'],
      disputeResolvedAt: json['dispute_resolved_at'] != null ? DateTime.parse(json['dispute_resolved_at']) : null,
      disputeResolution: json['dispute_resolution'],
      buyerRating: json['buyer_rating'],
      sellerRating: json['seller_rating'],
      buyerReview: json['buyer_review'],
      sellerReview: json['seller_review'],
      securityLevel: json['security_level'] ?? 1,
      tradeLimits: json['trade_limits'] ?? {},
      verificationRequired: json['verification_required'] ?? false,
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'listing_id': listingId,
      'buyer_id': buyerId,
      'seller_id': sellerId,
      'trade_type': tradeType,
      'crypto_currency': cryptoCurrency,
      'fiat_currency': fiatCurrency,
      'crypto_amount': cryptoAmount,
      'fiat_amount': fiatAmount,
      'exchange_rate': exchangeRate,
      'payment_method': paymentMethod,
      'status': status,
      'escrow_address': escrowAddress,
      'trade_reference': tradeReference,
      'payment_details': paymentDetails,
      'escrow_status': escrowStatus,
      'payment_confirmed_at': paymentConfirmedAt?.toIso8601String(),
      'crypto_released_at': cryptoReleasedAt?.toIso8601String(),
      'trade_completed_at': tradeCompletedAt?.toIso8601String(),
      'dispute_id': disputeId,
      'dispute_resolved_at': disputeResolvedAt?.toIso8601String(),
      'dispute_resolution': disputeResolution,
      'buyer_rating': buyerRating,
      'seller_rating': sellerRating,
      'buyer_review': buyerReview,
      'seller_review': sellerReview,
      'security_level': securityLevel,
      'trade_limits': tradeLimits,
      'verification_required': verificationRequired,
      'metadata': metadata,
    };
  }
}