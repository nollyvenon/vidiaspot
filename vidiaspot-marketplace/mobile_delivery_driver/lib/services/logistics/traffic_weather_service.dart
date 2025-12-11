import 'dart:convert';
import 'package:http/http.dart' as http;

class TrafficWeatherService {
  static const String _trafficApiKey = 'YOUR_TRAFFIC_API_KEY'; // This should be configured securely
  static const String _weatherApiKey = 'YOUR_WEATHER_API_KEY'; // This should be configured securely
  
  static const String _trafficApiUrl = 'https://api.traffic.com/route';
  static const String _weatherApiUrl = 'https://api.openweathermap.org/data/2.5/weather';
  
  // Get traffic information for a route
  Future<TrafficInfo> getTrafficInfo(double originLat, double originLng, double destLat, double destLng) async {
    try {
      final response = await http.get(
        Uri.parse('$_trafficApiUrl?origin=$originLat,$originLng&destination=$destLat,$destLng&key=$_trafficApiKey'),
      );
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return TrafficInfo.fromJson(data);
      } else {
        throw Exception('Failed to load traffic info');
      }
    } catch (e) {
      // Return default traffic info if API call fails
      return TrafficInfo(
        status: 'normal',
        averageSpeed: 40.0,
        estimatedTime: 0,
        congestionLevel: 1,
      );
    }
  }
  
  // Get weather information for a location
  Future<WeatherInfo> getWeatherInfo(double lat, double lng) async {
    try {
      final response = await http.get(
        Uri.parse('$_weatherApiUrl?lat=$lat&lon=$lng&appid=$_weatherApiKey&units=metric'),
      );
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return WeatherInfo.fromJson(data);
      } else {
        throw Exception('Failed to load weather info');
      }
    } catch (e) {
      // Return default weather info if API call fails
      return WeatherInfo(
        temperature: 25.0,
        description: 'Clear',
        humidity: 60,
        windSpeed: 5.0,
      );
    }
  }
  
  // Get road conditions and alerts
  Future<List<RoadAlert>> getRoadAlerts(double lat, double lng, double radius) async {
    // In a real implementation, this would connect to a traffic alert service
    // Mock implementation for now
    return [
      RoadAlert(
        type: 'construction',
        title: 'Road Construction',
        description: 'Lane closure on Main St due to construction',
        location: '${lat.toStringAsFixed(4)},${lng.toStringAsFixed(4)}',
        severity: 'medium',
      ),
      RoadAlert(
        type: 'closure',
        title: 'Road Closure',
        description: 'Bridge closed for maintenance',
        location: '${(lat + 0.01).toStringAsFixed(4)},${(lng - 0.01).toStringAsFixed(4)}',
        severity: 'high',
      ),
    ];
  }
  
  // Get dynamic route adjustment based on traffic and weather
  Future<RouteAdjustment> getDynamicRouteAdjustment({
    required double originLat,
    required double originLng, 
    required double destLat, 
    required double destLng,
  }) async {
    final trafficInfo = await getTrafficInfo(originLat, originLng, destLat, destLng);
    final weatherInfo = await getWeatherInfo(originLat, originLng);
    final roadAlerts = await getRoadAlerts(originLat, originLng, 5.0); // 5km radius
    
    // Determine if route adjustment is needed
    bool needsAdjustment = trafficInfo.congestionLevel > 3 || 
                           (weatherInfo.windSpeed > 15.0) || // Strong winds
                           roadAlerts.any((alert) => alert.severity == 'high');
    
    return RouteAdjustment(
      needsAdjustment: needsAdjustment,
      trafficInfo: trafficInfo,
      weatherInfo: weatherInfo,
      roadAlerts: roadAlerts,
      alternativeRoutes: needsAdjustment ? await _getAlternativeRoutes(originLat, originLng, destLat, destLng) : [],
    );
  }
  
  // Get alternative routes (mock implementation)
  Future<List<RouteOption>> _getAlternativeRoutes(double originLat, double originLng, double destLat, double destLng) async {
    // In a real implementation, this would get alternative routes
    return [
      RouteOption(
        id: 'alt1',
        distance: 15.5,
        duration: 45,
        description: 'Avoid highway due to traffic',
      ),
      RouteOption(
        id: 'alt2',
        distance: 16.2,
        duration: 42,
        description: 'Scenic route with less traffic',
      ),
    ];
  }
}

class TrafficInfo {
  final String status; // normal, light, moderate, heavy, severe
  final double averageSpeed; // km/h
  final int estimatedTime; // seconds
  final int congestionLevel; // 1-5 scale
  
  TrafficInfo({
    required this.status,
    required this.averageSpeed,
    required this.estimatedTime,
    required this.congestionLevel,
  });
  
  factory TrafficInfo.fromJson(Map<String, dynamic> json) {
    return TrafficInfo(
      status: json['status'] ?? 'normal',
      averageSpeed: (json['average_speed'] as num?)?.toDouble() ?? 40.0,
      estimatedTime: json['estimated_time'] ?? 0,
      congestionLevel: json['congestion_level'] ?? 1,
    );
  }
}

class WeatherInfo {
  final double temperature; // Celsius
  final String description;
  final int humidity; // percentage
  final double windSpeed; // m/s
  
  WeatherInfo({
    required this.temperature,
    required this.description,
    required this.humidity,
    required this.windSpeed,
  });
  
  factory WeatherInfo.fromJson(Map<String, dynamic> json) {
    return WeatherInfo(
      temperature: (json['main']['temp'] as num?)?.toDouble() ?? 25.0,
      description: json['weather'][0]['description'] ?? 'Clear',
      humidity: json['main']['humidity'] ?? 60,
      windSpeed: (json['wind']['speed'] as num?)?.toDouble() ?? 5.0,
    );
  }
}

class RoadAlert {
  final String type; // construction, closure, accident, weather
  final String title;
  final String description;
  final String location; // coordinates as string
  final String severity; // low, medium, high
  
  RoadAlert({
    required this.type,
    required this.title,
    required this.description,
    required this.location,
    required this.severity,
  });
}

class RouteAdjustment {
  final bool needsAdjustment;
  final TrafficInfo trafficInfo;
  final WeatherInfo weatherInfo;
  final List<RoadAlert> roadAlerts;
  final List<RouteOption> alternativeRoutes;
  
  RouteAdjustment({
    required this.needsAdjustment,
    required this.trafficInfo,
    required this.weatherInfo,
    required this.roadAlerts,
    required this.alternativeRoutes,
  });
}

class RouteOption {
  final String id;
  final double distance; // km
  final int duration; // minutes
  final String description;
  
  RouteOption({
    required this.id,
    required this.distance,
    required this.duration,
    required this.description,
  });
}