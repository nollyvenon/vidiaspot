import 'package:flutter/foundation.dart';
import '../models/farm_data.dart';
import '../models/farm_product.dart';
import '../models/farm_order.dart';
import '../models/farm_analytics.dart';

class FarmProvider with ChangeNotifier {
  FarmData? _farmData;
  List<FarmProduct> _products = [];
  List<FarmOrder> _orders = [];
  FarmAnalytics? _analyticsData;
  
  FarmData? get farmData => _farmData;
  List<FarmProduct> get products => _products;
  List<FarmOrder> get orders => _orders;
  FarmAnalytics? get analyticsData => _analyticsData;
  
  bool _isLoading = false;
  bool get isLoading => _isLoading;
  
  void setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
  
  void setFarmData(FarmData? farmData) {
    _farmData = farmData;
    notifyListeners();
  }
  
  void setProducts(List<FarmProduct> products) {
    _products = products;
    notifyListeners();
  }
  
  void setOrders(List<FarmOrder> orders) {
    _orders = orders;
    notifyListeners();
  }
  
  void setAnalyticsData(FarmAnalytics? analyticsData) {
    _analyticsData = analyticsData;
    notifyListeners();
  }
  
  // Product management
  void addProduct(FarmProduct product) {
    _products.add(product);
    notifyListeners();
  }
  
  void updateProduct(String id, FarmProduct updatedProduct) {
    int index = _products.indexWhere((product) => product.id == id);
    if (index != -1) {
      _products[index] = updatedProduct;
      notifyListeners();
    }
  }
  
  void removeProduct(String id) {
    _products.removeWhere((product) => product.id == id);
    notifyListeners();
  }
  
  // Order management
  void updateOrderStatus(String orderId, String status) {
    int index = _orders.indexWhere((order) => order.id == orderId);
    if (index != -1) {
      _orders[index] = _orders[index].copyWith(status: status);
      notifyListeners();
    }
  }
  
  // Get pending orders
  List<FarmOrder> get pendingOrders {
    return _orders.where((order) => order.status == 'pending').toList();
  }
  
  List<FarmOrder> get confirmedOrders {
    return _orders.where((order) => order.status == 'confirmed').toList();
  }
  
  List<FarmOrder> get completedOrders {
    return _orders.where((order) => order.status == 'completed').toList();
  }
  
  // Calculate dashboard metrics
  double get totalRevenue {
    if (_analyticsData != null) {
      return _analyticsData!.totalRevenue;
    }
    // Calculate from completed orders if analytics not loaded yet
    return _orders
        .where((order) => order.status == 'completed')
        .fold(0, (sum, order) => sum + order.totalAmount);
  }
  
  int get totalProducts {
    return _products.length;
  }
  
  int get pendingOrderCount {
    return _orders.where((order) => order.status == 'pending').length;
  }
  
  int get completedOrderCount {
    return _orders.where((order) => order.status == 'completed').length;
  }
}