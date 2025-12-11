import 'package:http/http.dart' as http;
import 'dart:convert';
import '../../../models/farmer_product.dart';

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
  
  // Get seller's restaurant data
  Future<Map<String, dynamic>?> getRestaurantData(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-seller/restaurant'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load restaurant data');
      }
    } catch (e) {
      print('Error getting restaurant data: $e');
      return null;
    }
  }
  
  // Get food analytics
  Future<Map<String, dynamic>?> getFoodAnalytics(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-seller/analytics'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load analytics');
      }
    } catch (e) {
      print('Error getting analytics: $e');
      return null;
    }
  }
  
  // Get recent orders
  Future<List<dynamic>?> getRecentOrders(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-seller/orders'),
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
  
  // Get menu items
  Future<List<dynamic>?> getMenuItems(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-seller/menu'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load menu items');
      }
    } catch (e) {
      print('Error getting menu items: $e');
      return null;
    }
  }
  
  // Update menu item
  Future<bool> updateMenuItem(String token, String itemId, Map<String, dynamic> data) async {
    try {
      final response = await http.put(
        Uri.parse('$_baseUrl/food-seller/menu/$itemId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(data),
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error updating menu item: $e');
      return false;
    }
  }
  
  // Add new menu item
  Future<bool> addMenuItem(String token, Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/food-seller/menu'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(data),
      );

      return response.statusCode == 201;
    } catch (e) {
      print('Error adding menu item: $e');
      return false;
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

  // Get farm analytics
  Future<Map<String, dynamic>?> getFarmAnalytics(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-products/analytics'),
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
}