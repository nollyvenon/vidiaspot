// lib/services/marketplace_modules_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class MarketplaceModulesService {
  final String baseUrl = 'http://10.0.2.2:8000/api'; // Updated to match backend API routes
  String? _authToken;

  MarketplaceModulesService() {
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

  // Get all available marketplace modules
  Future<List<Map<String, dynamic>>> getMarketplaceModules() async {
    final response = await http.get(
      Uri.parse('$baseUrl/marketplace-modules'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        List<dynamic> modules = data['data']['modules'];
        return modules.map((module) => Map<String, dynamic>.from(module)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load marketplace modules');
      }
    } else {
      throw Exception('Failed to load marketplace modules: ${response.statusCode}');
    }
  }

  // Get P2P crypto marketplace information
  Future<Map<String, dynamic>> getP2pCryptoMarketplaceInfo() async {
    final response = await http.get(
      Uri.parse('$baseUrl/p2p-crypto-marketplace'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Map<String, dynamic>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load P2P crypto marketplace info');
      }
    } else {
      throw Exception('Failed to load P2P crypto marketplace info: ${response.statusCode}');
    }
  }

  // Get landing page information
  Future<Map<String, dynamic>> getLandingPageData() async {
    final response = await http.get(
      Uri.parse('$baseUrl/landing'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Map<String, dynamic>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load landing page data');
      }
    } else {
      throw Exception('Failed to load landing page data: ${response.statusCode}');
    }
  }
}