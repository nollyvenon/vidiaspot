// lib/models/escrow_model.dart
class Escrow {
  final int id;
  final int transactionId;
  final int adId;
  final int buyerUserId;
  final int sellerUserId;
  final double amount;
  final String currency;
  final String status;
  final String? disputeStatus;
  final DateTime? releaseDate;
  final DateTime? disputeResolvedAt;
  final Map<String, dynamic>? disputeDetails;
  final Map<String, dynamic>? releaseConditions;
  final String? notes;
  final String? blockchainTransactionHash;
  final String? blockchainContractAddress;
  final String? blockchainStatus;
  final Map<String, dynamic>? blockchainVerificationData;

  Escrow({
    required this.id,
    required this.transactionId,
    required this.adId,
    required this.buyerUserId,
    required this.sellerUserId,
    required this.amount,
    this.currency = 'NGN',
    this.status = 'pending',
    this.disputeStatus,
    this.releaseDate,
    this.disputeResolvedAt,
    this.disputeDetails,
    this.releaseConditions,
    this.notes,
    this.blockchainTransactionHash,
    this.blockchainContractAddress,
    this.blockchainStatus,
    this.blockchainVerificationData,
  });

  factory Escrow.fromJson(Map<String, dynamic> json) {
    return Escrow(
      id: json['id'] ?? 0,
      transactionId: json['transaction_id'] ?? json['transactionId'] ?? 0,
      adId: json['ad_id'] ?? json['adId'] ?? 0,
      buyerUserId: json['buyer_user_id'] ?? json['buyerUserId'] ?? 0,
      sellerUserId: json['seller_user_id'] ?? json['sellerUserId'] ?? 0,
      amount: (json['amount'] is int) ? (json['amount'] as int).toDouble() : (json['amount'] ?? 0.0),
      currency: json['currency'] ?? 'NGN',
      status: json['status'] ?? 'pending',
      disputeStatus: json['dispute_status'] ?? json['disputeStatus'],
      releaseDate: json['release_date'] != null ? DateTime.parse(json['release_date']) : null,
      disputeResolvedAt: json['dispute_resolved_at'] != null ? DateTime.parse(json['dispute_resolved_at']) : null,
      disputeDetails: json['dispute_details'] ?? json['disputeDetails'],
      releaseConditions: json['release_conditions'] ?? json['releaseConditions'],
      notes: json['notes'],
      blockchainTransactionHash: json['blockchain_transaction_hash'] ?? json['blockchainTransactionHash'],
      blockchainContractAddress: json['blockchain_contract_address'] ?? json['blockchainContractAddress'],
      blockchainStatus: json['blockchain_status'] ?? json['blockchainStatus'],
      blockchainVerificationData: json['blockchain_verification_data'] ?? json['blockchainVerificationData'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'transaction_id': transactionId,
      'ad_id': adId,
      'buyer_user_id': buyerUserId,
      'seller_user_id': sellerUserId,
      'amount': amount,
      'currency': currency,
      'status': status,
      'dispute_status': disputeStatus,
      'release_date': releaseDate?.toIso8601String(),
      'dispute_resolved_at': disputeResolvedAt?.toIso8601String(),
      'dispute_details': disputeDetails,
      'release_conditions': releaseConditions,
      'notes': notes,
      'blockchain_transaction_hash': blockchainTransactionHash,
      'blockchain_contract_address': blockchainContractAddress,
      'blockchain_status': blockchainStatus,
      'blockchain_verification_data': blockchainVerificationData,
    };
  }
}