class FarmOrder {
  final String id;
  final String orderId;
  final String customerId;
  final String customerName;
  final String customerEmail;
  final String customerPhone;
  final String status; // pending, confirmed, preparing, out_for_delivery, delivered, cancelled, refunded
  final List<OrderItem> items;
  final double subtotal;
  final double tax;
  final double deliveryFee;
  final double tipAmount;
  final double totalAmount;
  final String currency;
  final DateTime orderDate;
  final DateTime estimatedDeliveryTime;
  final String deliveryAddress;
  final String deliveryInstructions;
  final String paymentMethod;
  final String paymentStatus;
  final String deliveryPersonId;
  final String deliveryPersonName;
  final String deliveryPersonPhone;
  final String specialRequests;
  final bool contactlessDelivery;
  final String farmId;

  FarmOrder({
    required this.id,
    required this.orderId,
    required this.customerId,
    required this.customerName,
    required this.customerEmail,
    required this.customerPhone,
    required this.status,
    required this.items,
    required this.subtotal,
    required this.tax,
    required this.deliveryFee,
    required this.tipAmount,
    required this.totalAmount,
    required this.currency,
    required this.orderDate,
    required this.estimatedDeliveryTime,
    required this.deliveryAddress,
    required this.deliveryInstructions,
    required this.paymentMethod,
    required this.paymentStatus,
    required this.deliveryPersonId,
    required this.deliveryPersonName,
    required this.deliveryPersonPhone,
    required this.specialRequests,
    required this.contactlessDelivery,
    required this.farmId,
  });

  FarmOrder copyWith({
    String? id,
    String? orderId,
    String? customerId,
    String? customerName,
    String? customerEmail,
    String? customerPhone,
    String? status,
    List<OrderItem>? items,
    double? subtotal,
    double? tax,
    double? deliveryFee,
    double? tipAmount,
    double? totalAmount,
    String? currency,
    DateTime? orderDate,
    DateTime? estimatedDeliveryTime,
    String? deliveryAddress,
    String? deliveryInstructions,
    String? paymentMethod,
    String? paymentStatus,
    String? deliveryPersonId,
    String? deliveryPersonName,
    String? deliveryPersonPhone,
    String? specialRequests,
    bool? contactlessDelivery,
    String? farmId,
  }) {
    return FarmOrder(
      id: id ?? this.id,
      orderId: orderId ?? this.orderId,
      customerId: customerId ?? this.customerId,
      customerName: customerName ?? this.customerName,
      customerEmail: customerEmail ?? this.customerEmail,
      customerPhone: customerPhone ?? this.customerPhone,
      status: status ?? this.status,
      items: items ?? this.items,
      subtotal: subtotal ?? this.subtotal,
      tax: tax ?? this.tax,
      deliveryFee: deliveryFee ?? this.deliveryFee,
      tipAmount: tipAmount ?? this.tipAmount,
      totalAmount: totalAmount ?? this.totalAmount,
      currency: currency ?? this.currency,
      orderDate: orderDate ?? this.orderDate,
      estimatedDeliveryTime: estimatedDeliveryTime ?? this.estimatedDeliveryTime,
      deliveryAddress: deliveryAddress ?? this.deliveryAddress,
      deliveryInstructions: deliveryInstructions ?? this.deliveryInstructions,
      paymentMethod: paymentMethod ?? this.paymentMethod,
      paymentStatus: paymentStatus ?? this.paymentStatus,
      deliveryPersonId: deliveryPersonId ?? this.deliveryPersonId,
      deliveryPersonName: deliveryPersonName ?? this.deliveryPersonName,
      deliveryPersonPhone: deliveryPersonPhone ?? this.deliveryPersonPhone,
      specialRequests: specialRequests ?? this.specialRequests,
      contactlessDelivery: contactlessDelivery ?? this.contactlessDelivery,
      farmId: farmId ?? this.farmId,
    );
  }

  factory FarmOrder.fromJson(Map<String, dynamic> json) {
    List<OrderItem> items = [];
    if (json['items'] != null) {
      items = (json['items'] as List)
          .map((item) => OrderItem.fromJson(item))
          .toList();
    }

    return FarmOrder(
      id: json['id'] ?? '',
      orderId: json['order_id'] ?? '',
      customerId: json['customer_id'] ?? '',
      customerName: json['customer_name'] ?? '',
      customerEmail: json['customer_email'] ?? '',
      customerPhone: json['customer_phone'] ?? '',
      status: json['status'] ?? 'pending',
      items: items,
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0.0,
      tax: (json['tax'] as num?)?.toDouble() ?? 0.0,
      deliveryFee: (json['delivery_fee'] as num?)?.toDouble() ?? 0.0,
      tipAmount: (json['tip_amount'] as num?)?.toDouble() ?? 0.0,
      totalAmount: (json['total_amount'] as num?)?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      orderDate: DateTime.parse(json['order_date'] ?? DateTime.now().toIso8601String()),
      estimatedDeliveryTime: DateTime.parse(json['estimated_delivery_time'] ?? DateTime.now().toIso8601String()),
      deliveryAddress: json['delivery_address'] ?? '',
      deliveryInstructions: json['delivery_instructions'] ?? '',
      paymentMethod: json['payment_method'] ?? '',
      paymentStatus: json['payment_status'] ?? 'pending',
      deliveryPersonId: json['delivery_person_id'] ?? '',
      deliveryPersonName: json['delivery_person_name'] ?? '',
      deliveryPersonPhone: json['delivery_person_phone'] ?? '',
      specialRequests: json['special_requests'] ?? '',
      contactlessDelivery: json['contactless_delivery'] ?? false,
      farmId: json['farm_id'] ?? '',
    );
  }
}

class OrderItem {
  final String productId;
  final String productName;
  final String productImage;
  final int quantity;
  final double unitPrice;
  final double total;
  final String specialInstructions;
  final List<String> addons;

  OrderItem({
    required this.productId,
    required this.productName,
    required this.productImage,
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
      productId: json['product_id'] ?? '',
      productName: json['product_name'] ?? '',
      productImage: json['product_image'] ?? '',
      quantity: json['quantity'] ?? 1,
      unitPrice: (json['unit_price'] as num?)?.toDouble() ?? 0.0,
      total: (json['total'] as num?)?.toDouble() ?? 0.0,
      specialInstructions: json['special_instructions'] ?? '',
      addons: addons,
    );
  }
}