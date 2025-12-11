import 'package:flutter/foundation.dart';
import '../../models/shop_data.dart';
import '../../models/analytics_data.dart';
import '../../models/order.dart';
import '../../models/product.dart';

class ShopOwnerProvider with ChangeNotifier {
  ShopData? _shopData;
  AnalyticsData? _analyticsData;
  List<Order> _recentOrders = [];
  List<Product> _products = [];
  
  ShopData? get shopData => _shopData;
  AnalyticsData? get analyticsData => _analyticsData;
  List<Order> get recentOrders => _recentOrders;
  List<Product> get products => _products;
  
  bool _isLoading = false;
  bool get isLoading => _isLoading;
  
  void setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
  
  void setShopData(ShopData? shopData) {
    _shopData = shopData;
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
  
  void setProducts(List<Product> products) {
    _products = products;
    notifyListeners();
  }
  
  void addProduct(Product product) {
    _products.add(product);
    notifyListeners();
  }
  
  void updateProduct(String id, Product updatedProduct) {
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
  
  int get totalProducts {
    return _products.length;
  }
  
  int get pendingOrders {
    return _recentOrders.where((order) => order.status == 'pending').length;
  }
}