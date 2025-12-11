class Order {
  final String id;
  final String orderId;
  final String customerId;
  final String customerName;
  final String customerEmail;
  final String customerPhone;
  final String status; // pending, confirmed, prepared, out_for_delivery, delivered, cancelled
  final double totalAmount;
  final String currency;
  final DateTime orderDate;
  final DateTime estimatedDeliveryTime;
  final String deliveryAddress;
  final String paymentMethod;
  final String paymentStatus; // pending, paid, failed, refunded
  final List<OrderItem> items;
  final String restaurantId;
  final String deliveryPersonId;
  final double tipAmount;
  final double deliveryFee;

  Order({
    required this.id,
    required this.orderId,
    required this.customerId,
    required this.customerName,
    required this.customerEmail,
    required this.customerPhone,
    required this.status,
    required this.totalAmount,
    required this.currency,
    required this.orderDate,
    required this.estimatedDeliveryTime,
    required this.deliveryAddress,
    required this.paymentMethod,
    required this.paymentStatus,
    required this.items,
    required this.restaurantId,
    required this.deliveryPersonId,
    required this.tipAmount,
    required this.deliveryFee,
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
      customerId: json['customer_id'] ?? '',
      customerName: json['customer_name'] ?? '',
      customerEmail: json['customer_email'] ?? '',
      customerPhone: json['customer_phone'] ?? '',
      status: json['status'] ?? 'pending',
      totalAmount: (json['total_amount'] as num?)?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      orderDate: DateTime.parse(json['order_date'] ?? DateTime.now().toIso8601String()),
      estimatedDeliveryTime: DateTime.parse(json['estimated_delivery_time'] ?? DateTime.now().toIso8601String()),
      deliveryAddress: json['delivery_address'] ?? '',
      paymentMethod: json['payment_method'] ?? '',
      paymentStatus: json['payment_status'] ?? 'pending',
      items: orderItems,
      restaurantId: json['restaurant_id'] ?? '',
      deliveryPersonId: json['delivery_person_id'] ?? '',
      tipAmount: (json['tip_amount'] as num?)?.toDouble() ?? 0.0,
      deliveryFee: (json['delivery_fee'] as num?)?.toDouble() ?? 0.0,
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