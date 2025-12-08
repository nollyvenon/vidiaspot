// lib/services/payment_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/payment_method_model.dart';
import '../models/crypto_payment_model.dart';
import '../models/bnpl_model.dart';
import '../models/split_payment_model.dart';
import '../models/insurance_model.dart';

class PaymentService {
  final String baseUrl = 'http://10.0.2.2:8000'; // For Android emulator, adjust as needed
  String? _authToken;

  PaymentService() {
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

  // Add a new payment method
  Future<PaymentMethod> addPaymentMethod({
    required String methodType,
    required String methodName,
    required String provider,
    required String identifier,
    Map<String, dynamic>? details,
    bool isDefault = false,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/methods'),
      headers: getHeaders(),
      body: jsonEncode({
        'type': methodType,
        'name': methodName,
        'provider': provider,
        'identifier': identifier,
        'details': details ?? {},
        'is_default': isDefault,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return PaymentMethod.fromJson(data['payment_method']);
      } else {
        throw Exception(data['message'] ?? 'Failed to add payment method');
      }
    } else {
      throw Exception('Failed to add payment method: ${response.statusCode}');
    }
  }

  // Get user's payment methods
  Future<List<PaymentMethod>> getUserPaymentMethods() async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-payments/methods'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['payment_methods'] as List)
            .map((json) => PaymentMethod.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load payment methods');
      }
    } else {
      throw Exception('Failed to load payment methods: ${response.statusCode}');
    }
  }

  // Set a payment method as default
  Future<bool> setDefaultPaymentMethod(int paymentMethodId) async {
    final response = await http.put(
      Uri.parse('$baseUrl/advanced-payments/methods/$paymentMethodId/default'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to set default payment method: ${response.statusCode}');
    }
  }

  // Process cryptocurrency payment
  Future<CryptoPayment> processCryptocurrencyPayment({
    required int transactionId,
    required String currency,
    required String walletAddress,
    required double amountCrypto,
    required double amountNgn,
    required double exchangeRate,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/cryptocurrency'),
      headers: getHeaders(),
      body: jsonEncode({
        'transaction_id': transactionId,
        'currency': currency.toUpperCase(),
        'wallet_address': walletAddress,
        'amount_crypto': amountCrypto,
        'amount_ngn': amountNgn,
        'exchange_rate': exchangeRate,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return CryptoPayment.fromJson(data['payment']);
      } else {
        throw Exception(data['message'] ?? 'Failed to process cryptocurrency payment');
      }
    } else {
      throw Exception('Failed to process cryptocurrency payment: ${response.statusCode}');
    }
  }

  // Get supported cryptocurrencies
  Future<List<String>> getSupportedCryptocurrencies() async {
    final response = await http.get(
      Uri.parse('$baseUrl/advanced-payments/cryptocurrency/supported'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return List<String>.from(data['currencies']);
      } else {
        throw Exception(data['message'] ?? 'Failed to get supported cryptocurrencies');
      }
    } else {
      throw Exception('Failed to get supported cryptocurrencies: ${response.statusCode}');
    }
  }

  // Process Buy Now Pay Later
  Future<BuyNowPayLater> processBuyNowPayLater({
    required int adId,
    required int transactionId,
    required String provider,
    required double totalAmount,
    double downPayment = 0,
    int installmentCount = 4,
    required double installmentAmount,
    String frequency = 'month',
    required DateTime firstPaymentDate,
    double aprRate = 0,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/bnpl'),
      headers: getHeaders(),
      body: jsonEncode({
        'ad_id': adId,
        'transaction_id': transactionId,
        'provider': provider,
        'total_amount': totalAmount,
        'down_payment': downPayment,
        'installment_count': installmentCount,
        'installment_amount': installmentAmount,
        'frequency': frequency,
        'first_payment_date': firstPaymentDate.toIso8601String(),
        'apr_rate': aprRate,
        'provider_details': {},
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return BuyNowPayLater.fromJson(data['bnpl']);
      } else {
        throw Exception(data['message'] ?? 'Failed to process BNPL');
      }
    } else {
      throw Exception('Failed to process BNPL: ${response.statusCode}');
    }
  }

  // Process split payment
  Future<SplitPayment> processSplitPayment({
    required int adId,
    required int transactionId,
    required double totalAmount,
    required String title,
    required int participantCount,
    String? description,
    int expiresInDays = 30,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/split'),
      headers: getHeaders(),
      body: jsonEncode({
        'ad_id': adId,
        'transaction_id': transactionId,
        'total_amount': totalAmount,
        'title': title,
        'participant_count': participantCount,
        'description': description ?? '',
        'expires_in_days': expiresInDays,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SplitPayment.fromJson(data['split_payment']);
      } else {
        throw Exception(data['message'] ?? 'Failed to process split payment');
      }
    } else {
      throw Exception('Failed to process split payment: ${response.statusCode}');
    }
  }

  // Join a split payment
  Future<SplitPayment> joinSplitPayment(int splitPaymentId, double amount) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/split/$splitPaymentId/join'),
      headers: getHeaders(),
      body: jsonEncode({
        'amount': amount,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SplitPayment.fromJson(data['split_payment']);
      } else {
        throw Exception(data['message'] ?? 'Failed to join split payment');
      }
    } else {
      throw Exception('Failed to join split payment: ${response.statusCode}');
    }
  }

  // Process insurance
  Future<Insurance> processInsurance({
    required int adId,
    required int transactionId,
    required String type,
    required String provider,
    required double premiumAmount,
    required double coverageAmount,
    required String riskLevel,
    required DateTime effectiveFrom,
    required DateTime effectiveUntil,
    String? terms,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/insurance'),
      headers: getHeaders(),
      body: jsonEncode({
        'ad_id': adId,
        'transaction_id': transactionId,
        'type': type,
        'provider': provider,
        'premium_amount': premiumAmount,
        'coverage_amount': coverageAmount,
        'risk_level': riskLevel,
        'effective_from': effectiveFrom.toIso8601String(),
        'effective_until': effectiveUntil.toIso8601String(),
        'terms': terms ?? '',
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Insurance.fromJson(data['insurance']);
      } else {
        throw Exception(data['message'] ?? 'Failed to process insurance');
      }
    } else {
      throw Exception('Failed to process insurance: ${response.statusCode}');
    }
  }

  // Calculate tax
  Future<Map<String, dynamic>> calculateTax(double amount, String location) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/tax/calculate'),
      headers: getHeaders(),
      body: jsonEncode({
        'amount': amount,
        'location': location,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['tax_calculation'];
      } else {
        throw Exception(data['message'] ?? 'Failed to calculate tax');
      }
    } else {
      throw Exception('Failed to calculate tax: ${response.statusCode}');
    }
  }

  // Process mobile money payment
  Future<Map<String, dynamic>> processMobileMoneyPayment({
    required String provider,
    required double amount,
    required String receiverPhone,
    required String reference,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/advanced-payments/mobile-money'),
      headers: getHeaders(),
      body: jsonEncode({
        'provider': provider,
        'amount': amount,
        'receiver_phone': receiverPhone,
        'reference': reference,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['result'];
      } else {
        throw Exception(data['message'] ?? 'Failed to process mobile money payment');
      }
    } else {
      throw Exception('Failed to process mobile money payment: ${response.statusCode}');
    }
  }
}