// lib/models/insurance_model.dart
class Insurance {
  final int id;
  final int userId; // Who purchased the insurance
  final int adId; // Which ad this insurance is for
  final int paymentTransactionId; // Reference to the payment transaction
  final String insuranceType; // 'device_protection', 'product_insurance', 'delivery_insurance', 'high_value_item'
  final String provider; // Insurance provider name
  final String policyNumber; // Policy number assigned by insurer
  final double premiumAmount; // Amount paid for insurance
  final double coverageAmount; // Maximum coverage amount
  final String status; // 'active', 'claimed', 'expired', 'cancelled'
  final String riskLevel; // 'low', 'medium', 'high'
  final DateTime effectiveFrom; // When coverage starts
  final DateTime effectiveUntil; // When coverage ends
  final String? exclusions; // Terms not covered
  final List<Map<String, dynamic>>? beneficiaries; // Who is covered
  final List<Map<String, dynamic>>? claimProcess; // Steps for claiming
  final DateTime? lastClaimDate; // When last claim was made
  final int totalClaims; // Number of claims made
  final List<Map<String, dynamic>>? documents; // Insurance documents
  final String? termsAndConditions; // Terms and conditions

  Insurance({
    required this.id,
    required this.userId,
    required this.adId,
    required this.paymentTransactionId,
    required this.insuranceType,
    required this.provider,
    required this.policyNumber,
    required this.premiumAmount,
    required this.coverageAmount,
    required this.status,
    required this.riskLevel,
    required this.effectiveFrom,
    required this.effectiveUntil,
    this.exclusions,
    this.beneficiaries,
    this.claimProcess,
    this.lastClaimDate,
    this.totalClaims = 0,
    this.documents,
    this.termsAndConditions,
  });

  factory Insurance.fromJson(Map<String, dynamic> json) {
    return Insurance(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? json['userId'] ?? 0,
      adId: json['ad_id'] ?? json['adId'] ?? 0,
      paymentTransactionId: json['payment_transaction_id'] ?? json['paymentTransactionId'] ?? 0,
      insuranceType: json['insurance_type'] ?? json['insuranceType'] ?? 'product_insurance',
      provider: json['provider'] ?? '',
      policyNumber: json['policy_number'] ?? json['policyNumber'] ?? '',
      premiumAmount: (json['premium_amount'] is int)
          ? (json['premium_amount'] as int).toDouble()
          : (json['premium_amount'] ?? 0.0),
      coverageAmount: (json['coverage_amount'] is int)
          ? (json['coverage_amount'] as int).toDouble()
          : (json['coverage_amount'] ?? 0.0),
      status: json['status'] ?? 'active',
      riskLevel: json['risk_level'] ?? json['riskLevel'] ?? 'medium',
      effectiveFrom: json['effective_from'] != null ? DateTime.parse(json['effective_from']) : DateTime.now(),
      effectiveUntil: json['effective_until'] != null ? DateTime.parse(json['effective_until']) : DateTime.now().add(Duration(days: 365)),
      exclusions: json['exclusions'],
      beneficiaries: List<Map<String, dynamic>>.from(json['beneficiaries'] ?? []),
      claimProcess: List<Map<String, dynamic>>.from(json['claim_process'] ?? []),
      lastClaimDate: json['last_claim_date'] != null ? DateTime.parse(json['last_claim_date']) : null,
      totalClaims: json['total_claims'] ?? 0,
      documents: List<Map<String, dynamic>>.from(json['documents'] ?? []),
      termsAndConditions: json['terms_and_conditions'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'ad_id': adId,
      'payment_transaction_id': paymentTransactionId,
      'insurance_type': insuranceType,
      'provider': provider,
      'policy_number': policyNumber,
      'premium_amount': premiumAmount,
      'coverage_amount': coverageAmount,
      'status': status,
      'risk_level': riskLevel,
      'effective_from': effectiveFrom.toIso8601String(),
      'effective_until': effectiveUntil.toIso8601String(),
      'exclusions': exclusions,
      'beneficiaries': beneficiaries,
      'claim_process': claimProcess,
      'last_claim_date': lastClaimDate?.toIso8601String(),
      'total_claims': totalClaims,
      'documents': documents,
      'terms_and_conditions': termsAndConditions,
    };
  }
}