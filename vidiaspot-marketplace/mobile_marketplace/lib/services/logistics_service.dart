// lib/services/logistics_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/logistics/shipment_model.dart';

class LogisticsService {
  final String baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: 'http://10.0.2.2:8000/api'); // Updated to match backend API routes
  String? _authToken;

  LogisticsService() {
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

  // Create a new shipment
  Future<Shipment> createShipment({
    required String shipmentType,
    required String senderName,
    required String senderAddress,
    required String recipientName,
    required String recipientAddress,
    required double weight,
    required String contentDescription,
    String? specialInstructions,
    bool requiresSignature = false,
    bool insurance = false,
    bool fragile = false,
    bool express = false,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/logistics/shipments'),
      headers: getHeaders(),
      body: jsonEncode({
        'shipment_type': shipmentType,
        'sender_name': senderName,
        'sender_address': senderAddress,
        'recipient_name': recipientName,
        'recipient_address': recipientAddress,
        'weight': weight,
        'content_description': contentDescription,
        'special_instructions': specialInstructions ?? '',
        'requires_signature': requiresSignature,
        'insurance': insurance,
        'fragile': fragile,
        'express': express,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Shipment.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create shipment');
      }
    } else {
      throw Exception('Failed to create shipment: ${response.statusCode}');
    }
  }

  // Get all user's shipments
  Future<List<Shipment>> getUserShipments({
    String? status,
    DateTime? fromDate,
    DateTime? toDate,
    String? trackingNumber,
    int page = 1,
    int perPage = 10,
  }) async {
    String url = '$baseUrl/logistics/shipments?page=$page&per_page=$perPage';

    List<String> queryParams = [];
    if (status != null) queryParams.add('status=$status');
    if (fromDate != null) queryParams.add('from_date=${fromDate.toIso8601String()}');
    if (toDate != null) queryParams.add('to_date=${toDate.toIso8601String()}');
    if (trackingNumber != null) queryParams.add('tracking_number=$trackingNumber');

    if (queryParams.isNotEmpty) {
      url += '&' + queryParams.join('&');
    }

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> shipmentsJson = data['data']['shipments'] ?? data['data'];
        return shipmentsJson.map((json) => Shipment.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load shipments');
      }
    } else {
      throw Exception('Failed to load shipments: ${response.statusCode}');
    }
  }

  // Get a specific shipment by ID
  Future<Shipment> getShipment(int shipmentId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/logistics/shipments/$shipmentId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Shipment.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load shipment');
      }
    } else {
      throw Exception('Failed to load shipment: ${response.statusCode}');
    }
  }

  // Get shipment by tracking number
  Future<Shipment> getShipmentByTrackingNumber(String trackingNumber) async {
    final response = await http.get(
      Uri.parse('$baseUrl/logistics/shipments/track/$trackingNumber'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Shipment.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load shipment');
      }
    } else {
      throw Exception('Failed to load shipment: ${response.statusCode}');
    }
  }

  // Get shipment tracking events
  Future<List<ShipmentEvent>> getShipmentTrackingEvents(int shipmentId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/logistics/shipments/$shipmentId/tracking-events'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        List<dynamic> eventsJson = data['data'] ?? [];
        return eventsJson.map((json) => ShipmentEvent.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load tracking events');
      }
    } else {
      throw Exception('Failed to load tracking events: ${response.statusCode}');
    }
  }

  // Update shipment status
  Future<Shipment> updateShipmentStatus(int shipmentId, String status, {String? notes}) async {
    final response = await http.put(
      Uri.parse('$baseUrl/logistics/shipments/$shipmentId'),
      headers: getHeaders(),
      body: jsonEncode({
        'status': status,
        'notes': notes,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Shipment.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to update shipment status');
      }
    } else {
      throw Exception('Failed to update shipment status: ${response.statusCode}');
    }
  }

  // Cancel a shipment
  Future<bool> cancelShipment(int shipmentId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/logistics/shipments/$shipmentId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to cancel shipment: ${response.statusCode}');
    }
  }

  // Calculate shipping cost
  Future<Map<String, dynamic>> calculateShippingCost({
    required double weight,
    required String originLocation,
    required String destinationLocation,
    String? packageType = 'package',
    bool express = false,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/logistics/calculate-cost'),
      headers: getHeaders(),
      body: jsonEncode({
        'weight': weight,
        'origin_location': originLocation,
        'destination_location': destinationLocation,
        'package_type': packageType,
        'express': express,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to calculate shipping cost');
      }
    } else {
      throw Exception('Failed to calculate shipping cost: ${response.statusCode}');
    }
  }

  // Get shipping providers
  Future<List<Map<String, dynamic>>> getShippingProviders() async {
    final response = await http.get(
      Uri.parse('$baseUrl/logistics/providers'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['data'] as List).cast<Map<String, dynamic>>();
      } else {
        throw Exception(data['message'] ?? 'Failed to load shipping providers');
      }
    } else {
      throw Exception('Failed to load shipping providers: ${response.statusCode}');
    }
  }

  // Update delivery address
  Future<Shipment> updateDeliveryAddress(int shipmentId, String newAddress) async {
    final response = await http.patch(
      Uri.parse('$baseUrl/logistics/shipments/$shipmentId/address'),
      headers: getHeaders(),
      body: jsonEncode({
        'new_address': newAddress,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Shipment.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to update delivery address');
      }
    } else {
      throw Exception('Failed to update delivery address: ${response.statusCode}');
    }
  }

  // Get delivery options
  Future<List<Map<String, dynamic>>> getDeliveryOptions({
    required String origin,
    required String destination,
    required double weight,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/logistics/delivery-options'),
      headers: getHeaders(),
      body: jsonEncode({
        'origin': origin,
        'destination': destination,
        'weight': weight,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['data'] as List).cast<Map<String, dynamic>>();
      } else {
        throw Exception(data['message'] ?? 'Failed to get delivery options');
      }
    } else {
      throw Exception('Failed to get delivery options: ${response.statusCode}');
    }
  }
}