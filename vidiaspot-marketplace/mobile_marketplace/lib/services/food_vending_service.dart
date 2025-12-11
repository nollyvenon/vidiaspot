// lib/services/food_vending_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/food_vending/restaurant_model.dart';

class FoodVendingService {
  final String baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: 'http://10.0.2.2:8000/api'); // Updated to match backend API routes
  String? _authToken;

  FoodVendingService() {
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

  // Get all restaurants
  Future<List<Restaurant>> getRestaurants({
    String? cuisineType,
    String? search,
    double? minRating,
    bool? isAvailable,
    double? latitude,
    double? longitude,
    double? maxDistance,
    int page = 1,
    int perPage = 12,
  }) async {
    String url = '$baseUrl/food-vending/restaurants?page=$page&per_page=$perPage';

    List<String> queryParams = [];
    if (cuisineType != null) queryParams.add('cuisine_type=$cuisineType');
    if (search != null) queryParams.add('search=$search');
    if (minRating != null) queryParams.add('min_rating=$minRating');
    if (isAvailable != null) queryParams.add('is_available=$isAvailable');
    if (latitude != null) queryParams.add('latitude=$latitude');
    if (longitude != null) queryParams.add('longitude=$longitude');
    if (maxDistance != null) queryParams.add('max_distance=$maxDistance');

    if (queryParams.isNotEmpty) {
      url += '&' + queryParams.join('&');
    }

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> restaurantsJson = data['data']['restaurants'] ?? data['data'];
        return restaurantsJson.map((json) => Restaurant.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load restaurants');
      }
    } else {
      throw Exception('Failed to load restaurants: ${response.statusCode}');
    }
  }

  // Get a single restaurant
  Future<Restaurant> getRestaurant(int restaurantId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/food-vending/restaurants/$restaurantId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Restaurant.fromJson(data['data']);
    } else {
      throw Exception('Failed to load restaurant: ${response.statusCode}');
    }
  }

  // Get food items for a restaurant
  Future<List<FoodItem>> getRestaurantFoodItems(int restaurantId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/food-vending/restaurants/$restaurantId/menu'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> foodItemsJson = data['data']['menu_items'] ?? data['data'];
        return foodItemsJson.map((json) => FoodItem.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load menu items');
      }
    } else {
      throw Exception('Failed to load menu items: ${response.statusCode}');
    }
  }

  // Get food items by category
  Future<List<FoodItem>> getFoodItemsByCategory(String category, {int page = 1, int perPage = 12}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/food-vending/food-items/category/$category?page=$page&per_page=$perPage'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> foodItemsJson = data['data']['food_items'] ?? data['data'];
        return foodItemsJson.map((json) => FoodItem.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load food items');
      }
    } else {
      throw Exception('Failed to load food items: ${response.statusCode}');
    }
  }

  // Get featured restaurants
  Future<List<Restaurant>> getFeaturedRestaurants() async {
    final response = await http.get(
      Uri.parse('$baseUrl/food-vending/restaurants/featured'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> restaurantsJson = data['data'] ?? [];
        return restaurantsJson.map((json) => Restaurant.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load featured restaurants');
      }
    } else {
      throw Exception('Failed to load featured restaurants: ${response.statusCode}');
    }
  }

  // Create a food order
  Future<Map<String, dynamic>> createOrder({
    required int restaurantId,
    required List<Map<String, dynamic>> items,
    required String deliveryAddress,
    required String deliveryInstructions,
    required String paymentMethod,
    String? promoCode,
    String? notes,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/food-vending/orders'),
      headers: getHeaders(),
      body: jsonEncode({
        'restaurant_id': restaurantId,
        'items': items,
        'delivery_address': deliveryAddress,
        'delivery_instructions': deliveryInstructions,
        'payment_method': paymentMethod,
        'promo_code': promoCode,
        'notes': notes,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to create order');
      }
    } else {
      throw Exception('Failed to create order: ${response.statusCode}');
    }
  }

  // Get user's food orders
  Future<List<Map<String, dynamic>>> getUserOrders({
    String? status,
    DateTime? fromDate,
    DateTime? toDate,
    int page = 1,
    int perPage = 10,
  }) async {
    String url = '$baseUrl/food-vending/orders?page=$page&per_page=$perPage';

    List<String> queryParams = [];
    if (status != null) queryParams.add('status=$status');
    if (fromDate != null) queryParams.add('from_date=${fromDate.toIso8601String()}');
    if (toDate != null) queryParams.add('to_date=${toDate.toIso8601String()}');

    if (queryParams.isNotEmpty) {
      url += '&' + queryParams.join('&');
    }

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> ordersJson = data['data']['orders'] ?? data['data'];
        return ordersJson.cast<Map<String, dynamic>>();
      } else {
        throw Exception(data['message'] ?? 'Failed to load user orders');
      }
    } else {
      throw Exception('Failed to load user orders: ${response.statusCode}');
    }
  }

  // Get order details
  Future<Map<String, dynamic>> getOrder(int orderId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/food-vending/orders/$orderId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to load order details');
      }
    } else {
      throw Exception('Failed to load order details: ${response.statusCode}');
    }
  }

  // Get order tracking information
  Future<Map<String, dynamic>> getOrderTracking(int orderId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/food-vending/orders/$orderId/tracking'),
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

  // Cancel an order
  Future<bool> cancelOrder(int orderId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/food-vending/orders/$orderId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to cancel order: ${response.statusCode}');
    }
  }

  // Update order status
  Future<Map<String, dynamic>> updateOrderStatus(int orderId, String status) async {
    final response = await http.put(
      Uri.parse('$baseUrl/food-vending/orders/$orderId'),
      headers: getHeaders(),
      body: jsonEncode({
        'status': status,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to update order status');
      }
    } else {
      throw Exception('Failed to update order status: ${response.statusCode}');
    }
  }

  // Search for restaurants or food items
  Future<Map<String, dynamic>> search({
    required String query,
    double? latitude,
    double? longitude,
    int page = 1,
    int perPage = 12,
  }) async {
    String url = '$baseUrl/food-vending/search?q=$query&page=$page&per_page=$perPage';

    if (latitude != null && longitude != null) {
      url += '&latitude=$latitude&longitude=$longitude';
    }

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Search failed');
      }
    } else {
      throw Exception('Search failed: ${response.statusCode}');
    }
  }
}