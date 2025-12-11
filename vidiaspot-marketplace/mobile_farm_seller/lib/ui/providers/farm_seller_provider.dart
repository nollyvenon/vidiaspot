import 'package:flutter/foundation.dart';
import 'farmer_product.dart';

class FarmSellerProvider with ChangeNotifier {
  Map<String, dynamic>? _farmData;
  List<FarmerProduct> _farmProducts = [];
  List<Map<String, dynamic>> _farmOrders = [];
  Map<String, dynamic>? _analyticsData;

  Map<String, dynamic>? get farmData => _farmData;
  List<FarmerProduct> get farmProducts => _farmProducts;
  List<Map<String, dynamic>> get farmOrders => _farmOrders;
  Map<String, dynamic>? get analyticsData => _analyticsData;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  void setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }

  void setFarmData(Map<String, dynamic>? farmData) {
    _farmData = farmData;
    notifyListeners();
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

  void setFarmOrders(List<Map<String, dynamic>> orders) {
    _farmOrders = orders;
    notifyListeners();
  }

  void setAnalyticsData(Map<String, dynamic>? analyticsData) {
    _analyticsData = analyticsData;
    notifyListeners();
  }

  // Calculate farm metrics
  double get totalRevenue {
    // In a real implementation, this would be calculated from completed orders
    return _analyticsData?['total_revenue']?.toDouble() ?? 0.0;
  }

  int get totalProducts {
    return _farmProducts.length;
  }

  int get totalOrders {
    return _farmOrders.length;
  }

  int get activeProducts {
    return _farmProducts.where((product) => product.status == 'active').length;
  }

  double get avgQualityRating {
    if (_farmProducts.isEmpty) return 0.0;
    final ratings = _farmProducts
        .where((product) => product.qualityRating != null)
        .map((product) => product.qualityRating!)
        .toList();
    if (ratings.isEmpty) return 0.0;
    return ratings.reduce((a, b) => a + b) / ratings.length;
  }

  int get organicProductsCount {
    return _farmProducts.where((product) => product.isOrganic).length;
  }

  double get avgSustainabilityScore {
    if (_farmProducts.isEmpty) return 0.0;
    final scores = _farmProducts
        .where((product) => product.sustainabilityScore != null)
        .map((product) => product.sustainabilityScore!)
        .toList();
    if (scores.isEmpty) return 0.0;
    return scores.reduce((a, b) => a + b) / scores.length;
  }
}