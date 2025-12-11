import 'dart:async';
import 'package:geolocator/geolocator.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:flutter_tts/flutter_tts.dart';
import 'dart:convert';
import '../services/logistics/traffic_weather_service.dart';
import '../services/logistics/voice_navigation_service.dart';
import '../services/logistics/offline_map_service.dart';

class NavigationService {
  static const String _googleMapsApiKey = 'YOUR_GOOGLE_MAPS_API_KEY'; // This should be configured securely
  static const String _trafficApiUrl = 'https://routes.googleapis.com/directions/v2:computeRoutes';

  // Core navigation components
  Completer<GoogleMapController>? _mapControllerCompleter;
  GoogleMapController? _mapController;

  // Location tracking
  Position? _currentPosition;
  LatLng? _destination;

  // Route information
  List<LatLng> _polylineCoordinates = [];
  String _distance = '';
  String _duration = '';

  // Navigation state
  bool _isNavigating = false;
  bool _trafficEnabled = true;
  String _routeMode = 'fastest'; // fastest, shortest, eco
  String _preferredLanguage = 'en-US';

  // Services
  VoiceNavigationService? _voiceNavigationService;
  TrafficWeatherService? _trafficWeatherService;
  OfflineMapService? _offlineMapService;

  // Stream controllers
  StreamController<Position>? _positionStreamController;
  StreamController<RouteAdjustment>? _routeAdjustmentStreamController;

  // Getters
  List<LatLng> get polylineCoordinates => _polylineCoordinates;
  String get distance => _distance;
  String get duration => _duration;
  bool get trafficEnabled => _trafficEnabled;
  String get routeMode => _routeMode;
  bool get isNavigating => _isNavigating;
  Stream<RouteAdjustment> get routeAdjustmentStream => _routeAdjustmentStreamController!.stream;

  // Initialize the navigation service
  Future<void> initialize({
    VoiceNavigationService? voiceService,
    TrafficWeatherService? trafficService,
    OfflineMapService? offlineService,
  }) async {
    _voiceNavigationService = voiceService ?? VoiceNavigationService();
    _trafficWeatherService = trafficService ?? TrafficWeatherService();
    _offlineMapService = offlineService ?? OfflineMapService();

    // Initialize dependent services
    await _voiceNavigationService!.initialize();
    await _offlineMapService!.initialize();

    // Set up stream controllers
    _positionStreamController = StreamController<Position>.broadcast();
    _routeAdjustmentStreamController = StreamController<RouteAdjustment>.broadcast();

    // Initialize location services
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw Exception('Location services are disabled.');
    }

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        throw Exception('Location permissions are denied');
      }
    }

    if (permission == LocationPermission.deniedForever) {
      throw Exception('Location permissions are permanently denied');
    }

    // Start tracking position
    Geolocator.getPositionStream(
      locationSettings: LocationSettings(
        accuracy: LocationAccuracy.high,
        distanceFilter: 10,
      ),
    ).listen((Position position) {
      _currentPosition = position;
      _positionStreamController?.add(position);

      // Check for route adjustments
      _checkForRouteAdjustments();
    });
  }

  // Set destination and calculate route
  Future<void> setDestination(LatLng destination) async {
    _destination = destination;
    await _calculateRoute();

    // Announce destination in voice
    await _voiceNavigationService?.speak("Navigating to destination");
  }

  // Calculate route based on current settings
  Future<void> _calculateRoute() async {
    if (_currentPosition == null || _destination == null) {
      return;
    }

    // Calculate route using Google Maps API
    final origin = LatLng(_currentPosition!.latitude, _currentPosition!.longitude);

    // In a real app, this would call Google's Directions API
    // For now, we'll use a simplified implementation
    await _calculateRoutePolyline(origin, _destination!);
  }

  // Calculate polyline for the route
  Future<void> _calculateRoutePolyline(LatLng start, LatLng end) async {
    // This is a simplified implementation
    // In a real app, we would use Google Maps Directions API
    final polylinePoints = PolylinePoints();

    final result = await polylinePoints.getRouteBetweenCoordinates(
      _googleMapsApiKey,
      PointLatLng(start.latitude, start.longitude),
      PointLatLng(end.latitude, end.longitude),
      travelMode: TravelMode.driving,
    );

    if (result.points.isNotEmpty) {
      _polylineCoordinates = result.points.map((point) {
        return LatLng(point.latitude, point.longitude);
      }).toList();
    }

    // Calculate distance and duration (simplified)
    double distanceInMeters = Geolocator.distanceBetween(
      start.latitude, start.longitude,
      end.latitude, end.longitude,
    );

    _distance = (distanceInMeters / 1000).toStringAsFixed(2) + ' km';
    // Assuming average speed of 40 km/h for duration estimate
    double durationInHours = distanceInMeters / 1000 / 40;
    _duration = (durationInHours * 60).round().toString() + ' min';
  }

  // Toggle traffic overlay
  void toggleTraffic() {
    _trafficEnabled = !_trafficEnabled;
  }

  // Set route mode
  void setRouteMode(String mode) {
    _routeMode = mode;
    // Recalculate route with new mode
    if (_destination != null) {
      _calculateRoute();
    }
  }

  // Set preferred language for voice navigation
  void setLanguage(String languageCode) {
    _preferredLanguage = languageCode;
    _voiceNavigationService?.setLanguage(languageCode);
  }

  // Get alternative routes
  Future<List<Map<String, dynamic>>> getAlternativeRoutes() async {
    // In a real implementation, this would fetch alternative routes from the API
    // Returning mock data for now
    return [
      {
        'name': 'Fastest Route',
        'distance': '12.5 km',
        'duration': '35 min',
        'polyline': _polylineCoordinates,
      },
      {
        'name': 'Shortest Route',
        'distance': '10.2 km',
        'duration': '45 min',
        'polyline': _polylineCoordinates, // Mock data
      },
      {
        'name': 'Eco Route',
        'distance': '11.8 km',
        'duration': '40 min',
        'polyline': _polylineCoordinates, // Mock data
      },
    ];
  }

  // Start turn-by-turn navigation
  void startNavigation() {
    _isNavigating = true;

    // Announce start of navigation
    _voiceNavigationService?.speak("Starting navigation");
  }

  // Stop navigation
  void stopNavigation() {
    _isNavigating = false;
    _voiceNavigationService?.stop();
  }

  // Check for route adjustments based on traffic and weather
  Future<void> _checkForRouteAdjustments() async {
    if (!_isNavigating || _currentPosition == null || _destination == null) {
      return;
    }

    try {
      final adjustment = await _trafficWeatherService?.getDynamicRouteAdjustment(
        originLat: _currentPosition!.latitude,
        originLng: _currentPosition!.longitude,
        destLat: _destination!.latitude,
        destLng: _destination!.longitude,
      );

      if (adjustment != null && adjustment.needsAdjustment) {
        _routeAdjustmentStreamController?.add(adjustment);

        // Announce the adjustment
        _voiceNavigationService?.speak("Traffic or weather conditions require a route adjustment");
      }
    } catch (e) {
      // Handle error but continue navigation
      print("Error checking for route adjustments: $e");
    }
  }

  // Get current position stream
  Stream<Position> get positionStream => _positionStreamController!.stream;

  // Clean up resources
  void dispose() {
    _positionStreamController?.close();
    _routeAdjustmentStreamController?.close();
    stopNavigation();
  }
}