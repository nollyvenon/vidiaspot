// lib/models/bnpl_model.dart
class BuyNowPayLater {
  final int id;
  final int userId;
  final int adId;
  final int paymentTransactionId;
  final String provider; // 'klarna', 'afterpay', 'paypal_credit', etc.
  final double totalAmount;
  final double downPayment;
  final int installmentCount;
  final double installmentAmount;
  final String frequency; // 'week', 'month'
  final String status; // 'pending_approval', 'approved', 'active', 'completed', 'failed', 'cancelled'
  final DateTime? firstPaymentDate;
  final DateTime? lastPaymentDate;
  final DateTime? nextPaymentDate;
  final DateTime? completionDate;
  final Map<String, dynamic>? providerDetails;
  final List<Map<String, dynamic>>? paymentSchedule; // [{installment_number, amount, due_date, status, paid_at, payment_reference}]
  final Map<String, dynamic>? checks; // Credit checks and approvals
  final double? aprRate; // Annual percentage rate
  final String? agreementUrl; // Link to the agreement

  BuyNowPayLater({
    required this.id,
    required this.userId,
    required this.adId,
    required this.paymentTransactionId,
    required this.provider,
    required this.totalAmount,
    required this.downPayment,
    required this.installmentCount,
    required this.installmentAmount,
    required this.frequency,
    required this.status,
    this.firstPaymentDate,
    this.lastPaymentDate,
    this.nextPaymentDate,
    this.completionDate,
    this.providerDetails,
    this.paymentSchedule,
    this.checks,
    this.aprRate,
    this.agreementUrl,
  });

  factory BuyNowPayLater.fromJson(Map<String, dynamic> json) {
    return BuyNowPayLater(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? json['userId'] ?? 0,
      adId: json['ad_id'] ?? json['adId'] ?? 0,
      paymentTransactionId: json['payment_transaction_id'] ?? json['paymentTransactionId'] ?? 0,
      provider: json['provider'] ?? '',
      totalAmount: (json['total_amount'] is int)
          ? (json['total_amount'] as int).toDouble()
          : (json['total_amount'] ?? 0.0),
      downPayment: json['down_payment'] != null
          ? ((json['down_payment'] is int)
              ? (json['down_payment'] as int).toDouble()
              : json['down_payment'])
          : 0.0,
      installmentCount: json['installment_count'] ?? 4,
      installmentAmount: (json['installment_amount'] is int)
          ? (json['installment_amount'] as int).toDouble()
          : (json['installment_amount'] ?? 0.0),
      frequency: json['frequency'] ?? 'month',
      status: json['status'] ?? 'pending_approval',
      firstPaymentDate: json['first_payment_date'] != null ? DateTime.parse(json['first_payment_date']) : null,
      lastPaymentDate: json['last_payment_date'] != null ? DateTime.parse(json['last_payment_date']) : null,
      nextPaymentDate: json['next_payment_date'] != null ? DateTime.parse(json['next_payment_date']) : null,
      completionDate: json['completion_date'] != null ? DateTime.parse(json['completion_date']) : null,
      providerDetails: json['provider_details'],
      paymentSchedule: List<Map<String, dynamic>>.from(json['payment_schedule'] ?? []),
      checks: json['checks'],
      aprRate: json['apr_rate'] != null
          ? ((json['apr_rate'] is int)
              ? (json['apr_rate'] as int).toDouble()
              : json['apr_rate'])
          : null,
      agreementUrl: json['agreement_url'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'ad_id': adId,
      'payment_transaction_id': paymentTransactionId,
      'provider': provider,
      'total_amount': totalAmount,
      'down_payment': downPayment,
      'installment_count': installmentCount,
      'installment_amount': installmentAmount,
      'frequency': frequency,
      'status': status,
      'first_payment_date': firstPaymentDate?.toIso8601String(),
      'last_payment_date': lastPaymentDate?.toIso8601String(),
      'next_payment_date': nextPaymentDate?.toIso8601String(),
      'completion_date': completionDate?.toIso8601String(),
      'provider_details': providerDetails,
      'payment_schedule': paymentSchedule,
      'checks': checks,
      'apr_rate': aprRate,
      'agreement_url': agreementUrl,
    };
  }
}