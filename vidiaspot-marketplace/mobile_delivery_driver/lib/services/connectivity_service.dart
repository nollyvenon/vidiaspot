import 'package:connectivity_plus/connectivity_plus.dart';

class ConnectivityService {
  static final ConnectivityService _instance = ConnectivityService._internal();
  factory ConnectivityService() => _instance;
  ConnectivityService._internal();

  Stream<ConnectivityResult> get connectivityStream => Connectivity().onConnectivityChanged;

  // Check current connectivity status
  Future<ConnectivityResult> checkConnectivity() async {
    return await Connectivity().checkConnectivity();
  }

  // Determine if connection is slow
  bool isSlowConnection(ConnectivityResult result) {
    switch (result) {
      case ConnectivityResult.mobile:
        // Could implement more sophisticated logic based on mobile network type
        return true;
      case ConnectivityResult.wifi:
        return false;
      case ConnectivityResult.ethernet:
        return false;
      case ConnectivityResult.none:
        return true;
      case ConnectivityResult.vpn:
        return false; // assuming VPN runs over good connection
      case ConnectivityResult.bluetooth:
        return true;
      default:
        return true;
    }
  }

  // Get connection type string
  String getConnectionTypeString(ConnectivityResult result) {
    switch (result) {
      case ConnectivityResult.wifi:
        return 'WiFi';
      case ConnectivityResult.mobile:
        return 'Mobile';
      case ConnectivityResult.ethernet:
        return 'Ethernet';
      case ConnectivityResult.vpn:
        return 'VPN';
      case ConnectivityResult.bluetooth:
        return 'Bluetooth';
      case ConnectivityResult.none:
        return 'No Connection';
      default:
        return 'Unknown';
    }
  }

  // Should use low quality images based on connection
  bool shouldUseLowQualityImages(ConnectivityResult result) {
    return isSlowConnection(result);
  }

  // Should reduce animations based on connection
  bool shouldReduceAnimations(ConnectivityResult result) {
    // This could be based on connection speed or user preference
    return isSlowConnection(result);
  }

  // Should use offline mode
  bool shouldUseOfflineMode(ConnectivityResult result) {
    return result == ConnectivityResult.none;
  }
}