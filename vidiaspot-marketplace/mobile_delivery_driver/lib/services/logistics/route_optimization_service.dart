import 'package:flutter/foundation.dart';
import 'dart:math' as math;

class RouteOptimizationService extends ChangeNotifier {
  // List of delivery stops to optimize
  List<DeliveryStop> _deliveryStops = [];
  List<DeliveryStop> _optimizedRoute = [];

  // Vehicle constraints
  double _maxWeightCapacity = 1000.0; // kg
  double _maxVolumeCapacity = 10.0; // cubic meters
  double _maxStopsPerRoute = 20;

  // Getters
  List<DeliveryStop> get deliveryStops => _deliveryStops;
  List<DeliveryStop> get optimizedRoute => _optimizedRoute;
  double get maxWeightCapacity => _maxWeightCapacity;
  double get maxVolumeCapacity => _maxVolumeCapacity;
  double get maxStopsPerRoute => _maxStopsPerRoute;

  // Setters for vehicle constraints
  set maxWeightCapacity(double value) {
    _maxWeightCapacity = value;
    notifyListeners();
  }

  set maxVolumeCapacity(double value) {
    _maxVolumeCapacity = value;
    notifyListeners();
  }

  set maxStopsPerRoute(int value) {
    _maxStopsPerRoute = value;
    notifyListeners();
  }

  // Add a delivery stop
  void addDeliveryStop(DeliveryStop stop) {
    _deliveryStops.add(stop);
    notifyListeners();
  }

  // Remove a delivery stop
  void removeDeliveryStop(DeliveryStop stop) {
    _deliveryStops.remove(stop);
    notifyListeners();
  }

  // Clear all stops
  void clearAllStops() {
    _deliveryStops.clear();
    _optimizedRoute.clear();
    notifyListeners();
  }

  // Optimize the route using multiple algorithms based on requirements
  void optimizeRoute({String optimizationType = 'time'}) {
    switch(optimizationType) {
      case 'time':
        _optimizeByTime();
        break;
      case 'distance':
        _optimizeByDistance();
        break;
      case 'fuel':
        _optimizeByFuel();
        break;
      case 'capacity':
        _optimizeByCapacity();
        break;
      default:
        _optimizeByTime();
    }

    notifyListeners();
  }

  // Optimize route by time (nearest neighbor with time windows)
  void _optimizeByTime() {
    if (_deliveryStops.isEmpty) {
      _optimizedRoute = [];
      return;
    }

    // Start from current location
    List<DeliveryStop> unvisited = List.from(_deliveryStops);
    _optimizedRoute = [];

    // Nearest neighbor algorithm with time windows
    DeliveryStop currentStop = unvisited.removeAt(0);
    _optimizedRoute.add(currentStop);

    while (unvisited.isNotEmpty) {
      DeliveryStop? nextStop = _findOptimalNextStop(currentStop, unvisited, 'time');
      if (nextStop != null) {
        _optimizedRoute.add(nextStop);
        unvisited.remove(nextStop);
        currentStop = nextStop;
      } else {
        // If no stop can be found with time constraints, just find the nearest
        DeliveryStop nearest = unvisited[0];
        double minDistance = _calculateDistance(currentStop, nearest);

        for (int i = 1; i < unvisited.length; i++) {
          double distance = _calculateDistance(currentStop, unvisited[i]);
          if (distance < minDistance) {
            minDistance = distance;
            nearest = unvisited[i];
          }
        }

        _optimizedRoute.add(nearest);
        unvisited.remove(nearest);
        currentStop = nearest;
      }
    }
  }

  // Optimize route by distance (shortest path)
  void _optimizeByDistance() {
    if (_deliveryStops.isEmpty) {
      _optimizedRoute = [];
      return;
    }

    List<DeliveryStop> unvisited = List.from(_deliveryStops);
    _optimizedRoute = [];

    DeliveryStop currentStop = unvisited.removeAt(0);
    _optimizedRoute.add(currentStop);

    while (unvisited.isNotEmpty) {
      DeliveryStop nearest = unvisited[0];
      double minDistance = _calculateDistance(currentStop, nearest);

      for (int i = 1; i < unvisited.length; i++) {
        double distance = _calculateDistance(currentStop, unvisited[i]);
        if (distance < minDistance) {
          minDistance = distance;
          nearest = unvisited[i];
        }
      }

      _optimizedRoute.add(nearest);
      unvisited.remove(nearest);
      currentStop = nearest;
    }
  }

  // Optimize route by fuel consumption
  void _optimizeByFuel() {
    if (_deliveryStops.isEmpty) {
      _optimizedRoute = [];
      return;
    }

    // For fuel optimization, we might consider road type, terrain, traffic
    // For simplicity, we'll use distance as a proxy for fuel consumption
    _optimizeByDistance();
  }

  // Optimize route by capacity constraints
  void _optimizeByCapacity() {
    if (_deliveryStops.isEmpty) {
      _optimizedRoute = [];
      return;
    }

    List<DeliveryStop> unvisited = List.from(_deliveryStops);
    _optimizedRoute = [];

    // Group stops by capacity constraints
    double currentWeight = 0;
    double currentVolume = 0;
    int currentStops = 0;

    // Start with first stop
    if (unvisited.isNotEmpty) {
      DeliveryStop firstStop = unvisited.removeAt(0);
      currentWeight += firstStop.weight;
      currentVolume += firstStop.volume;
      currentStops++;
      _optimizedRoute.add(firstStop);
    }

    // Continue adding stops while within capacity
    while (unvisited.isNotEmpty) {
      DeliveryStop? nextStop = _findNextCapacityCompatibleStop(
        _optimizedRoute.last,
        unvisited,
        currentWeight,
        currentVolume,
        currentStops
      );

      if (nextStop != null) {
        currentWeight += nextStop.weight;
        currentVolume += nextStop.volume;
        currentStops++;
        _optimizedRoute.add(nextStop);
        unvisited.remove(nextStop);
      } else {
        // No more stops can be added within capacity
        break;
      }
    }
  }

  // Find the next stop based on optimization criteria
  DeliveryStop? _findOptimalNextStop(DeliveryStop currentStop, List<DeliveryStop> candidates, String criteria) {
    DeliveryStop? bestCandidate;
    double bestValue = criteria == 'time' ? double.infinity : 0;

    for (DeliveryStop candidate in candidates) {
      double value = _calculateOptimizationValue(currentStop, candidate, criteria);

      if (criteria == 'time' && value < bestValue) {
        bestValue = value;
        bestCandidate = candidate;
      } else if (criteria == 'distance' && value < bestValue) {
        bestValue = value;
        bestCandidate = candidate;
      }
    }

    return bestCandidate;
  }

  // Calculate value for optimization criteria
  double _calculateOptimizationValue(DeliveryStop currentStop, DeliveryStop candidate, String criteria) {
    double distance = _calculateDistance(currentStop, candidate);

    if (criteria == 'time') {
      // Consider travel time and service time
      double travelTime = distance / 40 * 60; // Assuming 40 km/h average speed
      double serviceTime = candidate.getEstimatedDeliveryTime();
      return travelTime + serviceTime;
    } else {
      return distance;
    }
  }

  // Find next stop compatible with capacity constraints
  DeliveryStop? _findNextCapacityCompatibleStop(
    DeliveryStop currentStop,
    List<DeliveryStop> candidates,
    double currentWeight,
    double currentVolume,
    int currentStops
  ) {
    for (DeliveryStop candidate in candidates) {
      if (currentWeight + candidate.weight <= _maxWeightCapacity &&
          currentVolume + candidate.volume <= _maxVolumeCapacity &&
          currentStops + 1 <= _maxStopsPerRoute) {
        return candidate;
      }
    }
    return null;
  }

  // Calculate distance between two stops (simplified using Haversine formula)
  double _calculateDistance(DeliveryStop stop1, DeliveryStop stop2) {
    double lat1 = stop1.latitude;
    double lon1 = stop1.longitude;
    double lat2 = stop2.latitude;
    double lon2 = stop2.longitude;

    var p = 0.017453292519943295; // Pi/180
    double a = 0.5 -
        math.cos((lat2 - lat1) * p) / 2 +
        math.cos(lat1 * p) * math.cos(lat2 * p) *
        (1 - math.cos((lon2 - lon1) * p)) / 2;

    return 12742 * math.asin(math.sqrt(a)); // 2 * R; R = 6371 km
  }

  // Get estimated total distance of the route
  double getEstimatedTotalDistance() {
    if (_optimizedRoute.length < 2) return 0.0;

    double totalDistance = 0.0;
    for (int i = 0; i < _optimizedRoute.length - 1; i++) {
      totalDistance += _calculateDistance(_optimizedRoute[i], _optimizedRoute[i + 1]);
    }

    return totalDistance;
  }

  // Get estimated total time of the route
  double getEstimatedTotalTime() {
    if (_optimizedRoute.isEmpty) return 0.0;

    double totalDistance = getEstimatedTotalDistance();
    double travelTime = totalDistance / 40 * 60; // Assuming 40 km/h average speed

    // Add service time for each stop
    double serviceTime = 0.0;
    for (var stop in _optimizedRoute) {
      serviceTime += stop.getEstimatedDeliveryTime();
    }

    return travelTime + serviceTime;
  }

  // Get fuel consumption estimate
  double getEstimatedFuelConsumption() {
    // Assuming 10 km per liter for a delivery van
    double distance = getEstimatedTotalDistance();
    return distance / 10;
  }

  // Get capacity utilization
  double getWeightUtilization() {
    double totalWeight = 0.0;
    for (var stop in _optimizedRoute) {
      totalWeight += stop.weight;
    }
    return totalWeight / _maxWeightCapacity * 100;
  }

  double getVolumeUtilization() {
    double totalVolume = 0.0;
    for (var stop in _optimizedRoute) {
      totalVolume += stop.volume;
    }
    return totalVolume / _maxVolumeCapacity * 100;
  }

  int getStopUtilization() {
    return (_optimizedRoute.length / _maxStopsPerRoute * 100).round();
  }

  // Check if route is within capacity constraints
  bool isRouteWithinCapacity() {
    double totalWeight = 0.0;
    double totalVolume = 0.0;

    for (var stop in _optimizedRoute) {
      totalWeight += stop.weight;
      totalVolume += stop.volume;
    }

    return totalWeight <= _maxWeightCapacity &&
           totalVolume <= _maxVolumeCapacity &&
           _optimizedRoute.length <= _maxStopsPerRoute;
  }
}

class DeliveryStop {
  final String id;
  final String address;
  final double latitude;
  final double longitude;
  final String customerName;
  final String customerPhone;
  final String packageDetails;
  final DateTime? timeWindowStart;
  final DateTime? timeWindowEnd;
  final int priority; // 1 (highest) to 5 (lowest)
  final double weight; // package weight in kg
  final double volume; // package volume in cubic meters
  final bool isDelivered;
  final bool requiresSignature;
  final bool requiresPhoto;
  final double? sizeRestriction; // Maximum size restriction for the vehicle (length/width/height in meters)

  DeliveryStop({
    required this.id,
    required this.address,
    required this.latitude,
    required this.longitude,
    this.customerName = '',
    this.customerPhone = '',
    this.packageDetails = '',
    this.timeWindowStart,
    this.timeWindowEnd,
    this.priority = 1,
    this.weight = 0.0,
    this.volume = 0.0,
    this.isDelivered = false,
    this.requiresSignature = true,
    this.requiresPhoto = true,
    this.sizeRestriction,
  });

  double getEstimatedDeliveryTime() {
    // Simplified calculation based on priority and package size
    // Higher priority means faster service time
    double baseTime = 15.0; // 15 minutes base time
    double priorityAdjustment = (5 - priority) * 2.0; // Higher priority = less time
    double sizeAdjustment = (weight / 10) + (volume * 5); // Bigger packages take more time

    return baseTime + priorityAdjustment + sizeAdjustment;
  }

  // Check if the stop is within the time window
  bool isWithinTimeWindow(DateTime currentTime) {
    if (timeWindowStart == null || timeWindowEnd == null) {
      return true; // No time window specified
    }

    return currentTime.isAfter(timeWindowStart!) &&
           currentTime.isBefore(timeWindowEnd!);
  }
}