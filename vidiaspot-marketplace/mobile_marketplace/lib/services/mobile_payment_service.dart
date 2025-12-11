// lib/services/mobile_payment_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class MobilePaymentService {
  final String baseUrl = 'http://10.0.2.2:8000/api'; // Update to match your backend
  String? _authToken;

  MobilePaymentService() {
    _loadAuthToken();
  }

  Future<void> _loadAuthToken() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    _authToken = prefs.getString('auth_token');
  }

  Map<String, String> getHeaders() {
    Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (_authToken != null) {
      headers['Authorization'] = 'Bearer $_authToken';
    }

    return headers;
  }

  // Get available payment methods
  Future<List<PaymentMethod>> getAvailablePaymentMethods() async {
    final response = await http.get(
      Uri.parse('$baseUrl/mobile-payment/methods'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> methods = data['data'];
        return methods.map((json) => PaymentMethod.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load payment methods');
      }
    } else {
      throw Exception('Failed to load payment methods: ${response.statusCode}');
    }
  }

  // Add a new payment method
  Future<PaymentMethod> addPaymentMethod({
    required String methodType,
    required String name,
    required Map<String, String> details,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/mobile-payment/methods'),
      headers: getHeaders(),
      body: jsonEncode({
        'method_type': methodType,
        'name': name,
        'details': details,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return PaymentMethod.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to add payment method');
      }
    } else {
      throw Exception('Failed to add payment method: ${response.statusCode}');
    }
  }

  // Process a mobile payment
  Future<PaymentResult> processMobilePayment({
    required String methodId,
    required double amount,
    required String currency,
    required String description,
    Map<String, String>? paymentDetails,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/mobile-payment/process'),
      headers: getHeaders(),
      body: jsonEncode({
        'method_id': methodId,
        'amount': amount,
        'currency': currency,
        'description': description,
        'payment_details': paymentDetails ?? {},
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return PaymentResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Payment failed');
      }
    } else {
      throw Exception('Payment processing failed: ${response.statusCode}');
    }
  }

  // Verify payment status
  Future<PaymentResult> verifyPayment(String transactionId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/mobile-payment/verify/$transactionId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return PaymentResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to verify payment');
      }
    } else {
      throw Exception('Failed to verify payment: ${response.statusCode}');
    }
  }

  // Get payment history
  Future<List<PaymentHistory>> getPaymentHistory() async {
    final response = await http.get(
      Uri.parse('$baseUrl/mobile-payment/history'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> history = data['data'];
        return history.map((json) => PaymentHistory.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load payment history');
      }
    } else {
      throw Exception('Failed to load payment history: ${response.statusCode}');
    }
  }

  // Get mobile money providers
  Future<List<MobileMoneyProvider>> getMobileMoneyProviders() async {
    final response = await http.get(
      Uri.parse('$baseUrl/mobile-payment/providers'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> providers = data['data'];
        return providers.map((json) => MobileMoneyProvider.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load mobile money providers');
      }
    } else {
      throw Exception('Failed to load mobile money providers: ${response.statusCode}');
    }
  }

  // Initiate mobile money payment
  Future<PaymentResult> initiateMobileMoneyPayment({
    required String providerId,
    required String phoneNumber,
    required double amount,
    required String currency,
    required String description,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/mobile-payment/mobile-money'),
      headers: getHeaders(),
      body: jsonEncode({
        'provider_id': providerId,
        'phone_number': phoneNumber,
        'amount': amount,
        'currency': currency,
        'description': description,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return PaymentResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Mobile money payment failed');
      }
    } else {
      throw Exception('Mobile money payment failed: ${response.statusCode}');
    }
  }
}

// Data models for mobile payments
class PaymentMethod {
  final String id;
  final String methodType;
  final String name;
  final Map<String, String> details;
  final bool isDefault;
  final DateTime createdAt;

  PaymentMethod({
    required this.id,
    required this.methodType,
    required this.name,
    required this.details,
    required this.isDefault,
    required this.createdAt,
  });

  factory PaymentMethod.fromJson(Map<String, dynamic> json) {
    return PaymentMethod(
      id: json['id'] ?? '',
      methodType: json['method_type'] ?? '',
      name: json['name'] ?? '',
      details: Map<String, String>.from(json['details'] ?? {}),
      isDefault: json['is_default'] ?? false,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'method_type': methodType,
      'name': name,
      'details': details,
      'is_default': isDefault,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class PaymentResult {
  final String transactionId;
  final String status;
  final String message;
  final double amount;
  final String currency;
  final Map<String, dynamic>? details;

  PaymentResult({
    required this.transactionId,
    required this.status,
    required this.message,
    required this.amount,
    required this.currency,
    this.details,
  });

  factory PaymentResult.fromJson(Map<String, dynamic> json) {
    return PaymentResult(
      transactionId: json['transaction_id'] ?? '',
      status: json['status'] ?? 'pending',
      message: json['message'] ?? '',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      details: json['details'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transactionId,
      'status': status,
      'message': message,
      'amount': amount,
      'currency': currency,
      'details': details,
    };
  }
}

class PaymentHistory {
  final String transactionId;
  final String status;
  final double amount;
  final String currency;
  final String description;
  final String methodType;
  final DateTime createdAt;
  final Map<String, dynamic>? details;

  PaymentHistory({
    required this.transactionId,
    required this.status,
    required this.amount,
    required this.currency,
    required this.description,
    required this.methodType,
    required this.createdAt,
    this.details,
  });

  factory PaymentHistory.fromJson(Map<String, dynamic> json) {
    return PaymentHistory(
      transactionId: json['transaction_id'] ?? '',
      status: json['status'] ?? 'completed',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      description: json['description'] ?? '',
      methodType: json['method_type'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      details: json['details'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transactionId,
      'status': status,
      'amount': amount,
      'currency': currency,
      'description': description,
      'method_type': methodType,
      'created_at': createdAt.toIso8601String(),
      'details': details,
    };
  }
}

class MobileMoneyProvider {
  final String id;
  final String name;
  final String countryCode;
  final String currency;
  final Map<String, dynamic> config;

  MobileMoneyProvider({
    required this.id,
    required this.name,
    required this.countryCode,
    required this.currency,
    required this.config,
  });

  factory MobileMoneyProvider.fromJson(Map<String, dynamic> json) {
    return MobileMoneyProvider(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      countryCode: json['country_code'] ?? '',
      currency: json['currency'] ?? 'USD',
      config: json['config'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'country_code': countryCode,
      'currency': currency,
      'config': config,
    };
  }
}