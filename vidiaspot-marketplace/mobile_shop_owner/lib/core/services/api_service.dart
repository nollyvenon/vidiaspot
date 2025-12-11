import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String _baseUrl = 'https://your-backend-api.com/api'; // Replace with actual backend URL
  
  // Get shop owner's shop data
  Future<Map<String, dynamic>?> getShopData(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/shop-owner/shop'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load shop data');
      }
    } catch (e) {
      print('Error getting shop data: $e');
      return null;
    }
  }
  
  // Get shop analytics
  Future<Map<String, dynamic>?> getShopAnalytics(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/shop-owner/analytics'),
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
        Uri.parse('$_baseUrl/shop-owner/orders'),
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
  
  // Get products
  Future<List<dynamic>?> getProducts(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/shop-owner/products'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load products');
      }
    } catch (e) {
      print('Error getting products: $e');
      return null;
    }
  }
  
  // Update product
  Future<bool> updateProduct(String token, String productId, Map<String, dynamic> data) async {
    try {
      final response = await http.put(
        Uri.parse('$_baseUrl/shop-owner/products/$productId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(data),
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error updating product: $e');
      return false;
    }
  }
  
  // Add new product
  Future<bool> addProduct(String token, Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/shop-owner/products'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(data),
      );
      
      return response.statusCode == 201;
    } catch (e) {
      print('Error adding product: $e');
      return false;
    }
  }
}