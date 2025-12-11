import 'package:shared_preferences/shared_preferences.dart';

class AuthService {
  static const String _tokenKey = 'food_seller_token';
  static const String _userKey = 'food_seller_user';
  
  String? _token;
  Map<String, dynamic>? _user;

  bool get isAuthenticated => _token != null;

  Future<bool> login(String email, String password) async {
    // In a real app, this would call your backend API
    // For now, we'll simulate a successful login
    
    _token = 'mock_food_seller_token';
    _user = {
      'id': 'food_seller_1',
      'email': email,
      'name': 'Food Seller',
      'restaurant_id': 'rest_123',
      'role': 'food_seller'
    };
    
    // Save to local storage
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, _token!);
    await prefs.setString(_userKey, _user.toString());
    
    return true;
  }

  Future<void> logout() async {
    _token = null;
    _user = null;
    
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userKey);
  }

  Future<void> loadStoredCredentials() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    _token = prefs.getString(_tokenKey);
    String? userStr = prefs.getString(_userKey);
    
    // Parse user string if needed
    if (userStr != null) {
      // In a real app, you would parse the user data properly
      _user = {
        'id': 'food_seller_1',
        'email': 'seller@example.com',
        'name': 'Food Seller',
        'restaurant_id': 'rest_123',
        'role': 'food_seller'
      };
    }
  }
  
  String? get token => _token;
  Map<String, dynamic>? get user => _user;
}