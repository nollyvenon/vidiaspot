// lib/models/ecommerce/order_model.dart
class Order {
  final int id;
  final String orderNumber;
  final int userId;
  final List<OrderItem> items;
  final double subtotal;
  final double tax;
  final double shipping;
  final double discount;
  final double total;
  final String currency;
  final String status;
  final String paymentStatus;
  final String paymentMethod;
  final String shippingAddress;
  final String billingAddress;
  final String customerName;
  final String customerEmail;
  final String customerPhone;
  final DateTime createdAt;
  final DateTime updatedAt;
  final DateTime? shippedAt;
  final DateTime? deliveredAt;
  final String trackingNumber;
  final String shippingProvider;

  Order({
    required this.id,
    required this.orderNumber,
    required this.userId,
    required this.items,
    required this.subtotal,
    required this.tax,
    required this.shipping,
    required this.discount,
    required this.total,
    required this.currency,
    required this.status,
    required this.paymentStatus,
    required this.paymentMethod,
    required this.shippingAddress,
    required this.billingAddress,
    required this.customerName,
    required this.customerEmail,
    required this.customerPhone,
    required this.createdAt,
    required this.updatedAt,
    this.shippedAt,
    this.deliveredAt,
    required this.trackingNumber,
    required this.shippingProvider,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'] ?? 0,
      orderNumber: json['order_number'] ?? '',
      userId: json['user_id'] ?? 0,
      items: (json['items'] as List<dynamic>?)
              ?.map((item) => OrderItem.fromJson(item))
              .toList() ??
          [],
      subtotal: (json['subtotal'] is int)
          ? (json['subtotal'] as int).toDouble()
          : json['subtotal']?.toDouble() ?? 0.0,
      tax: (json['tax'] is int)
          ? (json['tax'] as int).toDouble()
          : json['tax']?.toDouble() ?? 0.0,
      shipping: (json['shipping'] is int)
          ? (json['shipping'] as int).toDouble()
          : json['shipping']?.toDouble() ?? 0.0,
      discount: (json['discount'] is int)
          ? (json['discount'] as int).toDouble()
          : json['discount']?.toDouble() ?? 0.0,
      total: (json['total'] is int)
          ? (json['total'] as int).toDouble()
          : json['total']?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      status: json['status'] ?? 'pending',
      paymentStatus: json['payment_status'] ?? 'pending',
      paymentMethod: json['payment_method'] ?? '',
      shippingAddress: json['shipping_address'] ?? '',
      billingAddress: json['billing_address'] ?? '',
      customerName: json['customer_name'] ?? '',
      customerEmail: json['customer_email'] ?? '',
      customerPhone: json['customer_phone'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      shippedAt: json['shipped_at'] != null ? DateTime.parse(json['shipped_at']) : null,
      deliveredAt: json['delivered_at'] != null ? DateTime.parse(json['delivered_at']) : null,
      trackingNumber: json['tracking_number'] ?? '',
      shippingProvider: json['shipping_provider'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'order_number': orderNumber,
      'user_id': userId,
      'items': items.map((item) => item.toJson()).toList(),
      'subtotal': subtotal,
      'tax': tax,
      'shipping': shipping,
      'discount': discount,
      'total': total,
      'currency': currency,
      'status': status,
      'payment_status': paymentStatus,
      'payment_method': paymentMethod,
      'shipping_address': shippingAddress,
      'billing_address': billingAddress,
      'customer_name': customerName,
      'customer_email': customerEmail,
      'customer_phone': customerPhone,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'shipped_at': shippedAt?.toIso8601String(),
      'delivered_at': deliveredAt?.toIso8601String(),
      'tracking_number': trackingNumber,
      'shipping_provider': shippingProvider,
    };
  }
}

class OrderItem {
  final int id;
  final int productId;
  final String productName;
  final String productImage;
  final double price;
  final int quantity;
  final double total;

  OrderItem({
    required this.id,
    required this.productId,
    required this.productName,
    required this.productImage,
    required this.price,
    required this.quantity,
    required this.total,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'] ?? 0,
      productId: json['product_id'] ?? 0,
      productName: json['product_name'] ?? '',
      productImage: json['product_image'] ?? '',
      price: (json['price'] is int)
          ? (json['price'] as int).toDouble()
          : json['price']?.toDouble() ?? 0.0,
      quantity: json['quantity'] ?? 1,
      total: (json['total'] is int)
          ? (json['total'] as int).toDouble()
          : json['total']?.toDouble() ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_id': productId,
      'product_name': productName,
      'product_image': productImage,
      'price': price,
      'quantity': quantity,
      'total': total,
    };
  }
}