// lib/models/crypto_p2p/crypto_trade_transaction_model.dart
class CryptoTradeTransaction {
  final int id;
  final int tradeId;
  final int userId;
  final String transactionType;
  final String cryptoCurrency;
  final String fiatCurrency;
  final double cryptoAmount;
  final double fiatAmount;
  final double exchangeRate;
  final double transactionFee;
  final String paymentMethod;
  final Map<String, dynamic>? paymentDetails;
  final String? transactionHash;
  final String blockchain;
  final int? blockNumber;
  final String fromAddress;
  final String toAddress;
  final String status;
  final DateTime? confirmedAt;
  final int confirmationBlocks;
  final String? errorMessage;
  final Map<String, dynamic>? metadata;

  CryptoTradeTransaction({
    required this.id,
    required this.tradeId,
    required this.userId,
    required this.transactionType,
    required this.cryptoCurrency,
    required this.fiatCurrency,
    required this.cryptoAmount,
    required this.fiatAmount,
    required this.exchangeRate,
    required this.transactionFee,
    required this.paymentMethod,
    this.paymentDetails,
    this.transactionHash,
    required this.blockchain,
    this.blockNumber,
    required this.fromAddress,
    required this.toAddress,
    required this.status,
    this.confirmedAt,
    required this.confirmationBlocks,
    this.errorMessage,
    this.metadata,
  });

  factory CryptoTradeTransaction.fromJson(Map<String, dynamic> json) {
    return CryptoTradeTransaction(
      id: json['id'] ?? 0,
      tradeId: json['trade_id'] ?? 0,
      userId: json['user_id'] ?? 0,
      transactionType: json['transaction_type'] ?? '',
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
      transactionFee: (json['transaction_fee'] is int) 
          ? (json['transaction_fee'] as int).toDouble() 
          : json['transaction_fee']?.toDouble() ?? 0.0,
      paymentMethod: json['payment_method'] ?? '',
      paymentDetails: json['payment_details'] ?? {},
      transactionHash: json['transaction_hash'],
      blockchain: json['blockchain'] ?? 'ethereum',
      blockNumber: json['block_number'],
      fromAddress: json['from_address'] ?? '',
      toAddress: json['to_address'] ?? '',
      status: json['status'] ?? 'pending',
      confirmedAt: json['confirmed_at'] != null ? DateTime.parse(json['confirmed_at']) : null,
      confirmationBlocks: json['confirmation_blocks'] ?? 0,
      errorMessage: json['error_message'],
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'trade_id': tradeId,
      'user_id': userId,
      'transaction_type': transactionType,
      'crypto_currency': cryptoCurrency,
      'fiat_currency': fiatCurrency,
      'crypto_amount': cryptoAmount,
      'fiat_amount': fiatAmount,
      'exchange_rate': exchangeRate,
      'transaction_fee': transactionFee,
      'payment_method': paymentMethod,
      'payment_details': paymentDetails,
      'transaction_hash': transactionHash,
      'blockchain': blockchain,
      'block_number': blockNumber,
      'from_address': fromAddress,
      'to_address': toAddress,
      'status': status,
      'confirmed_at': confirmedAt?.toIso8601String(),
      'confirmation_blocks': confirmationBlocks,
      'error_message': errorMessage,
      'metadata': metadata,
    };
  }
}