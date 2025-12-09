// lib/models/crypto_payment_model.dart
class CryptoPayment {
  final int id;
  final int userId;
  final int paymentTransactionId;
  final String cryptoCurrency; // 'BTC', 'ETH', 'USDT', etc.
  final String walletAddress;
  final double amountCrypto; // Amount in cryptocurrency
  final double amountNgn; // Equivalent amount in naira
  final double exchangeRate;
  final String? transactionHash; // Blockchain transaction hash
  final String status; // 'pending', 'confirmed', 'failed', 'expired'
  final String? network; // 'bitcoin', 'ethereum', etc.
  final DateTime? confirmedAt;
  final DateTime? expiresAt;
  final Map<String, dynamic>? rawData; // Raw blockchain data

  CryptoPayment({
    required this.id,
    required this.userId,
    required this.paymentTransactionId,
    required this.cryptoCurrency,
    required this.walletAddress,
    required this.amountCrypto,
    required this.amountNgn,
    required this.exchangeRate,
    this.transactionHash,
    this.status = 'pending',
    this.network,
    this.confirmedAt,
    this.expiresAt,
    this.rawData,
  });

  factory CryptoPayment.fromJson(Map<String, dynamic> json) {
    return CryptoPayment(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? json['userId'] ?? 0,
      paymentTransactionId: json['payment_transaction_id'] ?? json['paymentTransactionId'] ?? 0,
      cryptoCurrency: json['crypto_currency'] ?? json['cryptoCurrency'] ?? '',
      walletAddress: json['wallet_address'] ?? json['walletAddress'] ?? '',
      amountCrypto: (json['amount_crypto'] is int) 
          ? (json['amount_crypto'] as int).toDouble() 
          : (json['amount_crypto'] ?? 0.0),
      amountNgn: (json['amount_ngn'] is int) 
          ? (json['amount_ngn'] as int).toDouble() 
          : (json['amount_ngn'] ?? 0.0),
      exchangeRate: (json['exchange_rate'] is int) 
          ? (json['exchange_rate'] as int).toDouble() 
          : (json['exchange_rate'] ?? 0.0),
      transactionHash: json['transaction_hash'],
      status: json['status'] ?? 'pending',
      network: json['network'],
      confirmedAt: json['confirmed_at'] != null ? DateTime.parse(json['confirmed_at']) : null,
      expiresAt: json['expires_at'] != null ? DateTime.parse(json['expires_at']) : null,
      rawData: json['raw_data'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'payment_transaction_id': paymentTransactionId,
      'crypto_currency': cryptoCurrency,
      'wallet_address': walletAddress,
      'amount_crypto': amountCrypto,
      'amount_ngn': amountNgn,
      'exchange_rate': exchangeRate,
      'transaction_hash': transactionHash,
      'status': status,
      'network': network,
      'confirmed_at': confirmedAt?.toIso8601String(),
      'expires_at': expiresAt?.toIso8601String(),
      'raw_data': rawData,
    };
  }
}