class Order {
  final String id;
  final String orderId;
  final String customerId;
  final String customerName;
  final String customerEmail;
  final String customerPhone;
  final String status; // pending, confirmed, shipped, delivered, cancelled, refunded
  final double totalAmount;
  final String currency;
  final DateTime orderDate;
  final String shippingAddress;
  final String paymentMethod;
  final String paymentStatus; // pending, paid, failed, refunded
  final List<OrderItem> items;
  final String shopId;

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
    required this.shippingAddress,
    required this.paymentMethod,
    required this.paymentStatus,
    required this.items,
    required this.shopId,
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
      shippingAddress: json['shipping_address'] ?? '',
      paymentMethod: json['payment_method'] ?? '',
      paymentStatus: json['payment_status'] ?? 'pending',
      items: orderItems,
      shopId: json['shop_id'] ?? '',
    );
  }
}

class OrderItem {
  final String productId;
  final String productName;
  final String productImage;
  final int quantity;
  final double price;
  final double total;

  OrderItem({
    required this.productId,
    required this.productName,
    required this.productImage,
    required this.quantity,
    required this.price,
    required this.total,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      productId: json['product_id'] ?? '',
      productName: json['product_name'] ?? '',
      productImage: json['product_image'] ?? '',
      quantity: json['quantity'] ?? 0,
      price: (json['price'] as num?)?.toDouble() ?? 0.0,
      total: (json['total'] as num?)?.toDouble() ?? 0.0,
    );
  }
}