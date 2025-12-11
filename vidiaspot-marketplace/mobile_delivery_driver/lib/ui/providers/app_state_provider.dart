import 'package:flutter/foundation.dart';

class AppStateProvider extends ChangeNotifier {
  bool _isOnline = true;
  int _pendingDeliveries = 0;
  int _activeDeliveries = 0;
  int _completedDeliveries = 0;
  
  bool get isOnline => _isOnline;
  int get pendingDeliveries => _pendingDeliveries;
  int get activeDeliveries => _activeDeliveries;
  int get completedDeliveries => _completedDeliveries;
  
  void toggleOnlineStatus() {
    _isOnline = !_isOnline;
    notifyListeners();
  }
  
  void updatePendingDeliveries(int count) {
    _pendingDeliveries = count;
    notifyListeners();
  }
  
  void updateActiveDeliveries(int count) {
    _activeDeliveries = count;
    notifyListeners();
  }
  
  void updateCompletedDeliveries(int count) {
    _completedDeliveries = count;
    notifyListeners();
  }
}