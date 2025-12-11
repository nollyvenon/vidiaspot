import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String _baseUrl = 'https://your-backend-api.com/api'; // Replace with actual backend URL
  
  // Get farmer's farm data
  Future<Map<String, dynamic>?> getFarmData(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-to-market/farm'),
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
  
  // Get farm products
  Future<List<dynamic>?> getFarmProducts(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-to-market/products'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load farm products');
      }
    } catch (e) {
      print('Error getting farm products: $e');
      return null;
    }
  }
  
  // Add new product
  Future<bool> addProduct(String token, Map<String, dynamic> productData) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/farm-to-market/products'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(productData),
      );
      
      return response.statusCode == 201;
    } catch (e) {
      print('Error adding product: $e');
      return false;
    }
  }
  
  // Update product
  Future<bool> updateProduct(String token, String productId, Map<String, dynamic> productData) async {
    try {
      final response = await http.put(
        Uri.parse('$_baseUrl/farm-to-market/products/$productId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(productData),
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error updating product: $e');
      return false;
    }
  }
  
  // Delete product
  Future<bool> deleteProduct(String token, String productId) async {
    try {
      final response = await http.delete(
        Uri.parse('$_baseUrl/farm-to-market/products/$productId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error deleting product: $e');
      return false;
    }
  }
  
  // Get pending orders
  Future<List<dynamic>?> getPendingOrders(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-to-market/orders/pending'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load pending orders');
      }
    } catch (e) {
      print('Error getting pending orders: $e');
      return null;
    }
  }
  
  // Update order status
  Future<bool> updateOrderStatus(String token, String orderId, String status) async {
    try {
      final response = await http.patch(
        Uri.parse('$_baseUrl/farm-to-market/orders/$orderId/status'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode({'status': status}),
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error updating order status: $e');
      return false;
    }
  }
  
  // Get farm analytics
  Future<Map<String, dynamic>?> getFarmAnalytics(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-to-market/analytics'),
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
  
  // Get farm profile
  Future<Map<String, dynamic>?> getFarmProfile(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-to-market/profile'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load farm profile');
      }
    } catch (e) {
      print('Error getting farm profile: $e');
      return null;
    }
  }
  
  // Update farm profile
  Future<bool> updateFarmProfile(String token, Map<String, dynamic> profileData) async {
    try {
      final response = await http.put(
        Uri.parse('$_baseUrl/farm-to-market/profile'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(profileData),
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error updating farm profile: $e');
      return false;
    }
  }
}