class Order {
  final String id;
  final String orderId;
  final String userId;
  final String restaurantId;
  final String restaurantName;
  final String restaurantLogoUrl;
  final List<OrderItem> items;
  final double subtotal;
  final double tax;
  final double deliveryFee;
  final double tipAmount;
  final double totalAmount;
  final String currency;
  final String status; // pending, confirmed, preparing, out_for_delivery, delivered, cancelled
  final DateTime orderDate;
  final DateTime estimatedDeliveryTime;
  final String deliveryAddress;
  final String deliveryInstructions;
  final String paymentMethod;
  final String paymentStatus; // pending, paid, failed, refunded
  final String deliveryPersonId;
  final String deliveryPersonName;
  final String deliveryPersonPhone;
  final double deliveryPersonRating;
  final String specialRequests;
  final bool contactlessDelivery;

  Order({
    required this.id,
    required this.orderId,
    required this.userId,
    required this.restaurantId,
    required this.restaurantName,
    required this.restaurantLogoUrl,
    required this.items,
    required this.subtotal,
    required this.tax,
    required this.deliveryFee,
    required this.tipAmount,
    required this.totalAmount,
    required this.currency,
    required this.status,
    required this.orderDate,
    required this.estimatedDeliveryTime,
    required this.deliveryAddress,
    required this.deliveryInstructions,
    required this.paymentMethod,
    required this.paymentStatus,
    required this.deliveryPersonId,
    required this.deliveryPersonName,
    required this.deliveryPersonPhone,
    required this.deliveryPersonRating,
    required this.specialRequests,
    required this.contactlessDelivery,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    List<OrderItem> orderItems = [];
    if (json['items'] != null) {
      orderItems = (json['items'] as List)
          .map((item) => OrderItem.fromJson(item))
          .toList();
    }

    return Order(
      id: json['id'] ?? '',
      orderId: json['order_id'] ?? '',
      userId: json['user_id'] ?? '',
      restaurantId: json['restaurant_id'] ?? '',
      restaurantName: json['restaurant_name'] ?? '',
      restaurantLogoUrl: json['restaurant_logo_url'] ?? '',
      items: orderItems,
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0.0,
      tax: (json['tax'] as num?)?.toDouble() ?? 0.0,
      deliveryFee: (json['delivery_fee'] as num?)?.toDouble() ?? 0.0,
      tipAmount: (json['tip_amount'] as num?)?.toDouble() ?? 0.0,
      totalAmount: (json['total_amount'] as num?)?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      status: json['status'] ?? 'pending',
      orderDate: DateTime.parse(json['order_date'] ?? DateTime.now().toIso8601String()),
      estimatedDeliveryTime: DateTime.parse(json['estimated_delivery_time'] ?? DateTime.now().add(Duration(minutes: 30)).toIso8601String()),
      deliveryAddress: json['delivery_address'] ?? '',
      deliveryInstructions: json['delivery_instructions'] ?? '',
      paymentMethod: json['payment_method'] ?? '',
      paymentStatus: json['payment_status'] ?? 'pending',
      deliveryPersonId: json['delivery_person_id'] ?? '',
      deliveryPersonName: json['delivery_person_name'] ?? '',
      deliveryPersonPhone: json['delivery_person_phone'] ?? '',
      deliveryPersonRating: (json['delivery_person_rating'] as num?)?.toDouble() ?? 0.0,
      specialRequests: json['special_requests'] ?? '',
      contactlessDelivery: json['contactless_delivery'] ?? false,
    );
  }
}

class OrderItem {
  final String menuItemId;
  final String menuItemName;
  final String menuItemImage;
  final int quantity;
  final double unitPrice;
  final double total;
  final String specialInstructions;
  final List<String> addons;

  OrderItem({
    required this.menuItemId,
    required this.menuItemName,
    required this.menuItemImage,
    required this.quantity,
    required this.unitPrice,
    required this.total,
    required this.specialInstructions,
    required this.addons,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    List<String> addons = [];
    if (json['addons'] != null) {
      addons = (json['addons'] as List).map((item) => item.toString()).toList();
    }

    return OrderItem(
      menuItemId: json['menu_item_id'] ?? '',
      menuItemName: json['menu_item_name'] ?? '',
      menuItemImage: json['menu_item_image'] ?? '',
      quantity: json['quantity'] ?? 1,
      unitPrice: (json['unit_price'] as num?)?.toDouble() ?? 0.0,
      total: (json['total'] as num?)?.toDouble() ?? 0.0,
      specialInstructions: json['special_instructions'] ?? '',
      addons: addons,
    );
  }
}