import 'package:flutter/foundation.dart';
import '../models/restaurant.dart';
import '../models/menu_item.dart';
import '../models/order.dart';
import '../models/user_profile.dart';
import '../models/farmer_product.dart';

class FoodBuyerProvider with ChangeNotifier {
  List<Restaurant> _restaurants = [];
  List<MenuItem> _cartItems = [];
  List<Order> _userOrders = [];
  List<FarmerProduct> _farmProducts = [];
  UserProfile? _userProfile;

  List<Restaurant> get restaurants => _restaurants;
  List<MenuItem> get cartItems => _cartItems;
  List<Order> get userOrders => _userOrders;
  List<FarmerProduct> get farmProducts => _farmProducts;
  UserProfile? get userProfile => _userProfile;

  double get cartTotal {
    return _cartItems.fold(0, (sum, item) => sum + (item.price * item.cartQuantity));
  }

  int get cartItemCount {
    return _cartItems.fold(0, (sum, item) => sum + item.cartQuantity);
  }
  
  bool _isLoading = false;
  bool get isLoading => _isLoading;
  
  void setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
  
  void setRestaurants(List<Restaurant> restaurants) {
    _restaurants = restaurants;
    notifyListeners();
  }

  void setFarmProducts(List<FarmerProduct> farmProducts) {
    _farmProducts = farmProducts;
    notifyListeners();
  }

  void addFarmProduct(FarmerProduct product) {
    _farmProducts.add(product);
    notifyListeners();
  }

  void updateFarmProduct(String productId, FarmerProduct updatedProduct) {
    int index = _farmProducts.indexWhere((product) => product.id == productId);
    if (index != -1) {
      _farmProducts[index] = updatedProduct;
      notifyListeners();
    }
  }

  void removeFarmProduct(String productId) {
    _farmProducts.removeWhere((product) => product.id == productId);
    notifyListeners();
  }

  void setUserOrders(List<Order> orders) {
    _userOrders = orders;
    notifyListeners();
  }

  void setUserProfile(UserProfile? profile) {
    _userProfile = profile;
    notifyListeners();
  }
  
  // Cart management
  void addToCart(MenuItem item) {
    int existingIndex = _cartItems.indexWhere((cartItem) => cartItem.id == item.id);
    
    if (existingIndex != -1) {
      _cartItems[existingIndex] = _cartItems[existingIndex].copyWith(
        cartQuantity: _cartItems[existingIndex].cartQuantity + 1,
      );
    } else {
      _cartItems.add(item.copyWith(cartQuantity: 1));
    }
    
    notifyListeners();
  }
  
  void removeFromCart(String itemId) {
    _cartItems.removeWhere((item) => item.id == itemId);
    notifyListeners();
  }
  
  void updateCartItemQuantity(String itemId, int quantity) {
    int index = _cartItems.indexWhere((item) => item.id == itemId);
    if (index != -1) {
      if (quantity <= 0) {
        _cartItems.removeAt(index);
      } else {
        _cartItems[index] = _cartItems[index].copyWith(cartQuantity: quantity);
      }
      notifyListeners();
    }
  }
  
  void clearCart() {
    _cartItems.clear();
    notifyListeners();
  }
  
  // Order management
  void addOrder(Order order) {
    _userOrders.insert(0, order);
    notifyListeners();
  }
  
  void updateOrder(String orderId, Order updatedOrder) {
    int index = _userOrders.indexWhere((order) => order.id == orderId);
    if (index != -1) {
      _userOrders[index] = updatedOrder;
      notifyListeners();
    }
  }
  
  // Restaurant management
  void addRestaurant(Restaurant restaurant) {
    _restaurants.add(restaurant);
    notifyListeners();
  }
  
  void updateRestaurant(String restaurantId, Restaurant updatedRestaurant) {
    int index = _restaurants.indexWhere((restaurant) => restaurant.id == restaurantId);
    if (index != -1) {
      _restaurants[index] = updatedRestaurant;
      notifyListeners();
    }
  }
}