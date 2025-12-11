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
  
  // Get nearby restaurants
  Future<List<dynamic>?> getNearbyRestaurants(String token, double lat, double lng) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-buyer/restaurants?lat=$lat&lng=$lng'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load restaurants');
      }
    } catch (e) {
      print('Error getting restaurants: $e');
      return null;
    }
  }
  
  // Search for restaurants
  Future<List<dynamic>?> searchRestaurants(String token, String query) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-buyer/restaurants/search?q=$query'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to search restaurants');
      }
    } catch (e) {
      print('Error searching restaurants: $e');
      return null;
    }
  }
  
  // Get restaurant details
  Future<Map<String, dynamic>?> getRestaurantDetails(String token, String restaurantId) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-buyer/restaurants/$restaurantId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load restaurant details');
      }
    } catch (e) {
      print('Error getting restaurant details: $e');
      return null;
    }
  }
  
  // Get menu items for a restaurant
  Future<List<dynamic>?> getMenuItems(String token, String restaurantId) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-buyer/restaurants/$restaurantId/menu'),
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
  
  // Place order
  Future<Map<String, dynamic>?> placeOrder(String token, Map<String, dynamic> orderData) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/food-buyer/orders'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(orderData),
      );
      
      if (response.statusCode == 201) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to place order');
      }
    } catch (e) {
      print('Error placing order: $e');
      return null;
    }
  }
  
  // Get user orders
  Future<List<dynamic>?> getUserOrders(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-buyer/orders'),
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
  
  // Add review/rating for a restaurant
  Future<bool> addReview(String token, String restaurantId, Map<String, dynamic> reviewData) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/food-buyer/restaurants/$restaurantId/reviews'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(reviewData),
      );
      
      return response.statusCode == 201;
    } catch (e) {
      print('Error adding review: $e');
      return false;
    }
  }
  
  // Update profile
  Future<bool> updateProfile(String token, Map<String, dynamic> profileData) async {
    try {
      final response = await http.put(
        Uri.parse('$_baseUrl/food-buyer/profile'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(profileData),
      );
      
      return response.statusCode == 200;
    } catch (e) {
      print('Error updating profile: $e');
      return false;
    }
  }
  
  // Get user profile
  Future<Map<String, dynamic>?> getUserProfile(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/food-buyer/profile'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load profile');
      }
    } catch (e) {
      print('Error getting profile: $e');
      return null;
    }
  }

  // Get farm products
  Future<List<FarmerProduct>?> getFarmProducts(String token, {String? location, bool? isOrganic, String? harvestSeason, String? farmName, String? search, double? latitude, double? longitude, double? radius}) async {
    try {
      String url = '$_baseUrl/farm-products';
      List<String> queryParams = [];

      if (location != null && location.isNotEmpty) {
        queryParams.add('farm_location=$location');
      }
      if (isOrganic != null) {
        queryParams.add('is_organic=$isOrganic');
      }
      if (harvestSeason != null && harvestSeason.isNotEmpty) {
        queryParams.add('harvest_season=$harvestSeason');
      }
      if (farmName != null && farmName.isNotEmpty) {
        queryParams.add('farm_name=$farmName');
      }
      if (search != null && search.isNotEmpty) {
        queryParams.add('search=$search');
      }
      // Add proximity search parameters
      if (latitude != null && longitude != null) {
        queryParams.add('lat=$latitude');
        queryParams.add('lng=$longitude');
        if (radius != null) {
          queryParams.add('radius=$radius');
        }
      }

      if (queryParams.isNotEmpty) {
        url += '?${queryParams.join('&')}';
      }

      final response = await http.get(
        Uri.parse(url),
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

  // Get nearby farm products based on user's location
  Future<List<FarmerProduct>?> getNearbyFarmProducts(String token, double lat, double lng, {double radius = 50.0}) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-products?lat=$lat&lng=$lng&radius=${radius}'),
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
        throw Exception('Failed to load nearby farm products');
      }
    } catch (e) {
      print('Error getting nearby farm products: $e');
      return null;
    }
    return null;
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

  // Get nearby farm products
  Future<List<FarmerProduct>?> getNearbyFarmProducts(String token, double lat, double lng) async {
    try {
      // Note: This requires a custom endpoint on the backend for location-based searching
      // For now, we'll use the generic search with location filter
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-products?location=$lat,$lng'),
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
        throw Exception('Failed to load nearby farm products');
      }
    } catch (e) {
      print('Error getting nearby farm products: $e');
      return null;
    }
    return null;
  }

  // Search farm products
  Future<List<FarmerProduct>?> searchFarmProducts(String token, String query) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/farm-products?search=$query'),
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
        throw Exception('Failed to search farm products');
      }
    } catch (e) {
      print('Error searching farm products: $e');
      return null;
    }
    return null;
  }

  // Add to cart (for farm products)
  Future<bool> addToCart(String token, String productId, int quantity) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/cart/add'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode({
          'ad_id': productId,
          'quantity': quantity,
        }),
      );

      return response.statusCode == 200;
    } catch (e) {
      print('Error adding to cart: $e');
      return false;
    }
  }

  // Get user cart
  Future<List<dynamic>?> getUserCart(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/cart'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load cart');
      }
    } catch (e) {
      print('Error getting cart: $e');
      return null;
    }
  }

  // Place order for farm products
  Future<Map<String, dynamic>?> placeFarmOrder(String token, Map<String, dynamic> orderData) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/cart/checkout'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(orderData),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to place order');
      }
    } catch (e) {
      print('Error placing farm order: $e');
      return null;
    }
  }
}