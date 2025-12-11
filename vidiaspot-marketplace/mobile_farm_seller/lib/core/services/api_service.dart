import 'package:http/http.dart' as http;
import 'dart:convert';
import '../../models/farmer_product.dart';

class ApiService {
  static String get baseUrl {
    // Use environment variables with fallbacks
    String apiHost = const String.fromEnvironment('API_HOST', defaultValue: '10.0.2.2');
    String apiPort = const String.fromEnvironment('API_PORT', defaultValue: '8000');
    String apiBase = const String.fromEnvironment('API_BASE_URL', defaultValue: 'http://$apiHost:$apiPort/api');
    return apiBase;
  }

  // Internal getter for the base URL
  static const String _baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: 'http://${String.fromEnvironment('API_HOST', defaultValue: '10.0.2.2')}:${String.fromEnvironment('API_PORT', defaultValue: '8000')}/api');

  // Get farm seller's farm data
  Future<Map<String, dynamic>?> getFarmData(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-seller/farm'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load farm data');
      }
    } catch (e) {
      print('Error getting farm data: $e');
      return null;
    }
  }

  // Get farm analytics
  Future<Map<String, dynamic>?> getFarmAnalytics(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-product-reports/farmer-productivity?user_id=${_getUserIdFromToken(token)}'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load farm analytics');
      }
    } catch (e) {
      print('Error getting farm analytics: $e');
      return null;
    }
  }

  // Get farm products for seller
  Future<List<FarmerProduct>?> getMyFarmProducts(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-products/my-farm-products'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['data'] != null && data['data'] is List) {
          return data['data'].map((item) => FarmerProduct.fromJson(item)).toList();
        }
      } else {
        throw Exception('Failed to load farm products');
      }
    } catch (e) {
      print('Error getting farm products: $e');
      return null;
    }
    return null;
  }

  // Get farm orders
  Future<List<dynamic>?> getFarmOrders(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-seller/orders'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load orders');
      }
    } catch (e) {
      print('Error getting orders: $e');
      return null;
    }
  }

  // Add new farm product
  Future<bool> addFarmProduct(String token, Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/farm-products'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(data),
      );

      return response.statusCode == 201;
    } catch (e) {
      print('Error adding farm product: $e');
      return false;
    }
  }

  // Update farm product
  Future<bool> updateFarmProduct(String token, String productId, Map<String, dynamic> data) async {
    try {
      final response = await http.put(
        Uri.parse('$_baseUrl/farm-products/$productId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(data),
      );

      return response.statusCode == 200;
    } catch (e) {
      print('Error updating farm product: $e');
      return false;
    }
  }

  // Delete farm product
  Future<bool> deleteFarmProduct(String token, String productId) async {
    try {
      final response = await http.delete(
        Uri.parse('$_baseUrl/farm-products/$productId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      return response.statusCode == 200;
    } catch (e) {
      print('Error deleting farm product: $e');
      return false;
    }
  }

  // Get farm product details
  Future<FarmerProduct?> getFarmProductDetails(String token, String productId) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-products/$productId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['data'] != null) {
          return FarmerProduct.fromJson(data['data']);
        }
      } else {
        throw Exception('Failed to load farm product details');
      }
    } catch (e) {
      print('Error getting farm product details: $e');
      return null;
    }
    return null;
  }

  // Get farm reports
  Future<Map<String, dynamic>?> getFarmReports(String token, {String? startDate, String? endDate}) async {
    try {
      String url = '$_baseUrl/farm-product-reports/performance-summary';
      if (startDate != null) url += '?start_date=$startDate';
      if (endDate != null) url += startDate != null ? '&end_date=$endDate' : '?end_date=$endDate';

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load reports');
      }
    } catch (e) {
      print('Error getting farm reports: $e');
      return null;
    }
  }

  // Helper function to get user ID from token (simplified)
  String _getUserIdFromToken(String token) {
    // In a real implementation, you would decode the JWT token to get the user ID
    return '1'; // Placeholder
  }
}