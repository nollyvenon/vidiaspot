// lib/models/split_payment_model.dart
class SplitPayment {
  final int id;
  final int userId; // Who initiated the split payment
  final int adId; // Which ad this payment is for
  final int paymentTransactionId; // Reference to the main transaction
  final double totalAmount; // Total amount to be split
  final double amountPaid; // Amount already collected
  final double amountRemaining; // Amount still needed
  final String status; // 'active', 'completed', 'cancelled', 'expired'
  final String title; // Title for the split payment request
  final String? description; // Description of the reason
  final int participantCount; // Number of expected participants
  final DateTime? expiresAt; // When the split payment expires
  final List<Map<String, dynamic>>? participants; // Store participant details {user_id, amount, status}
  final List<Map<String, dynamic>>? paymentDetails; // Details of each payment
  final Map<String, dynamic>? settings; // {notify_on_join, auto_approve_join, etc.}

  SplitPayment({
    required this.id,
    required this.userId,
    required this.adId,
    required this.paymentTransactionId,
    required this.totalAmount,
    required this.amountPaid,
    required this.amountRemaining,
    required this.status,
    required this.title,
    this.description,
    required this.participantCount,
    this.expiresAt,
    this.participants,
    this.paymentDetails,
    this.settings,
  });

  factory SplitPayment.fromJson(Map<String, dynamic> json) {
    return SplitPayment(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? json['userId'] ?? 0,
      adId: json['ad_id'] ?? json['adId'] ?? 0,
      paymentTransactionId: json['payment_transaction_id'] ?? json['paymentTransactionId'] ?? 0,
      totalAmount: (json['total_amount'] is int)
          ? (json['total_amount'] as int).toDouble()
          : (json['total_amount'] ?? 0.0),
      amountPaid: (json['amount_paid'] is int)
          ? (json['amount_paid'] as int).toDouble()
          : (json['amount_paid'] ?? 0.0),
      amountRemaining: (json['amount_remaining'] is int)
          ? (json['amount_remaining'] as int).toDouble()
          : (json['amount_remaining'] ?? 0.0),
      status: json['status'] ?? 'active',
      title: json['title'] ?? '',
      description: json['description'],
      participantCount: json['participant_count'] ?? 0,
      expiresAt: json['expires_at'] != null ? DateTime.parse(json['expires_at']) : null,
      participants: List<Map<String, dynamic>>.from(json['participants'] ?? []),
      paymentDetails: List<Map<String, dynamic>>.from(json['payment_details'] ?? []),
      settings: json['settings'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'ad_id': adId,
      'payment_transaction_id': paymentTransactionId,
      'total_amount': totalAmount,
      'amount_paid': amountPaid,
      'amount_remaining': amountRemaining,
      'status': status,
      'title': title,
      'description': description,
      'participant_count': participantCount,
      'expires_at': expiresAt?.toIso8601String(),
      'participants': participants,
      'payment_details': paymentDetails,
      'settings': settings,
    };
  }
}