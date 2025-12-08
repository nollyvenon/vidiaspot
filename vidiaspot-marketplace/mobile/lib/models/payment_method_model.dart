// lib/models/payment_method_model.dart
class PaymentMethod {
  final int id;
  final int userId;
  final String methodType; // 'credit_card', 'paypal', 'bitcoin', 'ethereum', 'mpesa', 'mobile_money', 'qr_code', 'klarna', 'afterpay'
  final String methodName;
  final String? provider; // Provider name like 'Visa', 'Bitcoin', 'MPesa'
  final String? identifier; // Tokenized/encrypted identifier for payment method
  final Map<String, dynamic>? details; // Provider-specific details
  final bool isDefault;
  final bool isActive;
  final DateTime? expiresAt;

  PaymentMethod({
    required this.id,
    required this.userId,
    required this.methodType,
    required this.methodName,
    this.provider,
    this.identifier,
    this.details,
    this.isDefault = false,
    this.isActive = true,
    this.expiresAt,
  });

  factory PaymentMethod.fromJson(Map<String, dynamic> json) {
    return PaymentMethod(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? json['userId'] ?? 0,
      methodType: json['method_type'] ?? '',
      methodName: json['method_name'] ?? '',
      provider: json['provider'],
      identifier: json['identifier'],
      details: json['details'],
      isDefault: json['is_default'] ?? false,
      isActive: json['is_active'] ?? true,
      expiresAt: json['expires_at'] != null ? DateTime.parse(json['expires_at']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'method_type': methodType,
      'method_name': methodName,
      'provider': provider,
      'identifier': identifier,
      'details': details,
      'is_default': isDefault,
      'is_active': isActive,
      'expires_at': expiresAt?.toIso8601String(),
    };
  }
}