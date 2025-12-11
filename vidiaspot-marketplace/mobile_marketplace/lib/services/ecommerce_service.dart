// lib/services/ecommerce_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/ecommerce/product_model.dart';
import '../models/ecommerce/order_model.dart';

class EcommerceService {
  final String baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: 'http://10.0.2.2:8000/api'); // Updated to match backend API routes
  String? _authToken;

  EcommerceService() {
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

  // Get all products
  Future<List<Product>> getProducts({
    int? categoryId,
    String? search,
    String? sortBy = 'created_at',
    String? sortOrder = 'desc',
    int page = 1,
    int perPage = 12,
  }) async {
    String url = '$baseUrl/ecommerce/products?page=$page&per_page=$perPage&sort_by=$sortBy&sort_order=$sortOrder';

    if (categoryId != null) url += '&category_id=$categoryId';
    if (search != null) url += '&search=$search';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> productsJson = data['data']['products'] ?? data['data'];
        return productsJson.map((json) => Product.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load products');
      }
    } else {
      throw Exception('Failed to load products: ${response.statusCode}');
    }
  }

  // Get a single product
  Future<Product> getProduct(int productId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/products/$productId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Product.fromJson(data['data']);
    } else {
      throw Exception('Failed to load product: ${response.statusCode}');
    }
  }

  // Get featured products
  Future<List<Product>> getFeaturedProducts() async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/products/featured'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> productsJson = data['data'] ?? [];
        return productsJson.map((json) => Product.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load featured products');
      }
    } else {
      throw Exception('Failed to load featured products: ${response.statusCode}');
    }
  }

  // Get user's orders
  Future<List<Order>> getUserOrders({
    String? status,
    DateTime? fromDate,
    DateTime? toDate,
    int page = 1,
    int perPage = 10,
  }) async {
    String url = '$baseUrl/ecommerce/orders?page=$page&per_page=$perPage';

    if (status != null) url += '&status=$status';
    if (fromDate != null) url += '&from_date=${fromDate.toIso8601String()}';
    if (toDate != null) url += '&to_date=${toDate.toIso8601String()}';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> ordersJson = data['data']['orders'] ?? data['data'];
        return ordersJson.map((json) => Order.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load user orders');
      }
    } else {
      throw Exception('Failed to load user orders: ${response.statusCode}');
    }
  }

  // Get a single order
  Future<Order> getOrder(int orderId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/orders/$orderId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Order.fromJson(data['data']);
    } else {
      throw Exception('Failed to load order: ${response.statusCode}');
    }
  }

  // Create a new order
  Future<Order> createOrder({
    required List<Map<String, dynamic>> items,
    required String shippingAddress,
    required String billingAddress,
    required String paymentMethod,
    double? discount,
    String? notes,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/ecommerce/orders'),
      headers: getHeaders(),
      body: jsonEncode({
        'items': items,
        'shipping_address': shippingAddress,
        'billing_address': billingAddress,
        'payment_method': paymentMethod,
        'discount': discount ?? 0,
        'notes': notes ?? '',
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Order.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create order');
      }
    } else {
      throw Exception('Failed to create order: ${response.statusCode}');
    }
  }

  // Update order status
  Future<Order> updateOrderStatus(int orderId, String status) async {
    final response = await http.put(
      Uri.parse('$baseUrl/ecommerce/orders/$orderId'),
      headers: getHeaders(),
      body: jsonEncode({
        'status': status,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Order.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to update order status');
      }
    } else {
      throw Exception('Failed to update order status: ${response.statusCode}');
    }
  }

  // Cancel an order
  Future<bool> cancelOrder(int orderId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/ecommerce/orders/$orderId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to cancel order: ${response.statusCode}');
    }
  }

  // Get order tracking information
  Future<Map<String, dynamic>> getOrderTracking(int orderId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/orders/$orderId/tracking'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to get tracking information');
      }
    } else {
      throw Exception('Failed to get tracking information: ${response.statusCode}');
    }
  }

  // Add item to cart (placeholder - would connect to cart API if available)
  Future<bool> addToCart(int productId, int quantity) async {
    // This is a placeholder implementation
    // In a real app, this would connect to a cart API endpoint
    return true;
  }

  // Get cart items (placeholder - would connect to cart API if available)
  Future<List<Map<String, dynamic>>> getCartItems() async {
    // This is a placeholder implementation
    // In a real app, this would connect to a cart API endpoint
    return [];
  }
}