import 'package:flutter/foundation.dart';
import '../models/restaurant_data.dart';
import '../models/analytics_data.dart';
import '../models/order.dart';
import '../models/menu_item.dart';
import '../../../models/farmer_product.dart';

class FoodSellerProvider with ChangeNotifier {
  RestaurantData? _restaurantData;
  AnalyticsData? _analyticsData;
  List<Order> _recentOrders = [];
  List<MenuItem> _menuItems = [];
  List<FarmerProduct> _farmProducts = [];

  RestaurantData? get restaurantData => _restaurantData;
  AnalyticsData? get analyticsData => _analyticsData;
  List<Order> get recentOrders => _recentOrders;
  List<MenuItem> get menuItems => _menuItems;
  List<FarmerProduct> get farmProducts => _farmProducts;
  
  bool _isLoading = false;
  bool get isLoading => _isLoading;
  
  void setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
  
  void setRestaurantData(RestaurantData? restaurantData) {
    _restaurantData = restaurantData;
    notifyListeners();
  }
  
  void setAnalyticsData(AnalyticsData? analyticsData) {
    _analyticsData = analyticsData;
    notifyListeners();
  }
  
  void setRecentOrders(List<Order> orders) {
    _recentOrders = orders;
    notifyListeners();
  }
  
  void setMenuItems(List<MenuItem> items) {
    _menuItems = items;
    notifyListeners();
  }
  
  void addMenuItem(MenuItem item) {
    _menuItems.add(item);
    notifyListeners();
  }
  
  void updateMenuItem(String id, MenuItem updatedItem) {
    int index = _menuItems.indexWhere((item) => item.id == id);
    if (index != -1) {
      _menuItems[index] = updatedItem;
      notifyListeners();
    }
  }
  
  void removeMenuItem(String id) {
    _menuItems.removeWhere((item) => item.id == id);
    notifyListeners();
  }
  
  // Calculate dashboard metrics
  double get totalRevenue {
    if (_analyticsData != null) {
      return _analyticsData!.totalRevenue;
    }
    return 0.0;
  }
  
  int get totalOrders {
    if (_analyticsData != null) {
      return _analyticsData!.totalOrders;
    }
    return 0;
  }
  
  int get totalMenuItems {
    return _menuItems.length;
  }
  
  int get pendingOrders {
    return _recentOrders.where((order) => order.status == 'pending').length;
  }

  void setFarmProducts(List<FarmerProduct> products) {
    _farmProducts = products;
    notifyListeners();
  }

  void addFarmProduct(FarmerProduct product) {
    _farmProducts.add(product);
    notifyListeners();
  }

  void updateFarmProduct(String id, FarmerProduct updatedProduct) {
    int index = _farmProducts.indexWhere((product) => product.id == id);
    if (index != -1) {
      _farmProducts[index] = updatedProduct;
      notifyListeners();
    }
  }

  void removeFarmProduct(String id) {
    _farmProducts.removeWhere((product) => product.id == id);
    notifyListeners();
  }

  int get totalFarmProducts {
    return _farmProducts.length;
  }

  List<FarmerProduct> get organicFarmProducts {
    return _farmProducts.where((product) => product.isOrganic).toList();
  }

  List<FarmerProduct> get nonOrganicFarmProducts {
    return _farmProducts.where((product) => !product.isOrganic).toList();
  }
}