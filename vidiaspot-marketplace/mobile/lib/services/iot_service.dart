// lib/services/iot_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/iot/iot_device_model.dart';

class IoTService {
  final String baseUrl = 'http://10.0.2.2:8000'; // For Android emulator, adjust as needed
  String? _authToken;

  IoTService() {
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

  // Register a new IoT device
  Future<IoTDevice> registerDevice({
    required String deviceId,
    required String name,
    required String deviceType,
    String? brand,
    String? model,
    Map<String, dynamic>? specs,
    List<String>? protocols,
    String? firmwareVersion,
    String? location,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/iot/devices'),
      headers: getHeaders(),
      body: jsonEncode({
        'device_id': deviceId,
        'name': name,
        'type': deviceType,
        'brand': brand,
        'model': model,
        'specs': specs,
        'protocols': protocols,
        'firmware': firmwareVersion,
        'location': location,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return IoTDevice.fromJson(data['device']);
    } else {
      throw Exception('Failed to register device: ${response.statusCode}');
    }
  }

  // Connect to an IoT device
  Future<IoTDevice> connectDevice(String deviceId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/iot/devices/$deviceId/connect'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return IoTDevice.fromJson(data['device']);
    } else {
      throw Exception('Failed to connect device: ${response.statusCode}');
    }
  }

  // Disconnect from an IoT device
  Future<IoTDevice> disconnectDevice(String deviceId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/iot/devices/$deviceId/disconnect'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return IoTDevice.fromJson(data['device']);
    } else {
      throw Exception('Failed to disconnect device: ${response.statusCode}');
    }
  }

  // Get all connected IoT devices
  Future<List<IoTDevice>> getConnectedDevices() async {
    final response = await http.get(
      Uri.parse('$baseUrl/iot/devices/connected'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return (data['devices'] as List)
          .map((json) => IoTDevice.fromJson(json))
          .toList();
    } else {
      throw Exception('Failed to get connected devices: ${response.statusCode}');
    }
  }

  // Get all smart home IoT devices
  Future<List<IoTDevice>> getSmartHomeDevices() async {
    final response = await http.get(
      Uri.parse('$baseUrl/iot/devices/smart-home'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return (data['devices'] as List)
          .map((json) => IoTDevice.fromJson(json))
          .toList();
    } else {
      throw Exception('Failed to get smart home devices: ${response.statusCode}');
    }
  }

  // Get device status
  Future<IoTDevice> getDeviceStatus(String deviceId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/iot/devices/$deviceId/status'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return IoTDevice.fromJson(data['device']);
    } else {
      throw Exception('Failed to get device status: ${response.statusCode}');
    }
  }

  // Update device settings
  Future<IoTDevice> updateDeviceSettings(
    String deviceId,
    Map<String, dynamic> settings,
  ) async {
    final response = await http.put(
      Uri.parse('$baseUrl/iot/devices/$deviceId/settings'),
      headers: getHeaders(),
      body: jsonEncode(settings),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return IoTDevice.fromJson(data['device']);
    } else {
      throw Exception('Failed to update device settings: ${response.statusCode}');
    }
  }

  // Control device (turn on/off, adjust settings, etc.)
  Future<Map<String, dynamic>> controlDevice(
    String deviceId,
    String command,
    Map<String, dynamic>? parameters,
  ) async {
    final response = await http.post(
      Uri.parse('$baseUrl/iot/devices/$deviceId/control'),
      headers: getHeaders(),
      body: jsonEncode({
        'command': command,
        'parameters': parameters ?? {},
      }),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to control device: ${response.statusCode}');
    }
  }

  // Get device history/data
  Future<List<Map<String, dynamic>>> getDeviceHistory(
    String deviceId, {
    DateTime? from,
    DateTime? to,
    int limit = 50,
  }) async {
    String url = '$baseUrl/iot/devices/$deviceId/history?limit=$limit';
    if (from != null) url += '&from=${from.toIso8601String()}';
    if (to != null) url += '&to=${to.toIso8601String()}';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return List<Map<String, dynamic>>.from(data['history']);
    } else {
      throw Exception('Failed to get device history: ${response.statusCode}');
    }
  }
}