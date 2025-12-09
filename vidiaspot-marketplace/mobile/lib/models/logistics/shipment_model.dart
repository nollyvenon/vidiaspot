// lib/models/logistics/shipment_model.dart
class Shipment {
  final int id;
  final String trackingNumber;
  final String shipmentType;
  final int userId;
  final String senderName;
  final String senderAddress;
  final String recipientName;
  final String recipientAddress;
  final double weight;
  final String weightUnit;
  final double length;
  final double width;
  final double height;
  final String dimensionsUnit;
  final String packageType;
  final String contentDescription;
  final double declaredValue;
  final String currency;
  final String status;
  final String statusDescription;
  final DateTime createdAt;
  final DateTime updatedAt;
  final DateTime? shippedAt;
  final DateTime? deliveredAt;
  final DateTime? estimatedDelivery;
  final String originLocation;
  final String destinationLocation;
  final String currentLocation;
  final double shippingCost;
  final String shippingMethod;
  final String shippingProvider;
  final String paymentStatus;
  final String specialInstructions;
  final List<ShipmentEvent> events;
  final bool requiresSignature;
  final bool insurance;
  final bool fragile;
  final bool express;

  Shipment({
    required this.id,
    required this.trackingNumber,
    required this.shipmentType,
    required this.userId,
    required this.senderName,
    required this.senderAddress,
    required this.recipientName,
    required this.recipientAddress,
    required this.weight,
    required this.weightUnit,
    required this.length,
    required this.width,
    required this.height,
    required this.dimensionsUnit,
    required this.packageType,
    required this.contentDescription,
    required this.declaredValue,
    required this.currency,
    required this.status,
    required this.statusDescription,
    required this.createdAt,
    required this.updatedAt,
    this.shippedAt,
    this.deliveredAt,
    this.estimatedDelivery,
    required this.originLocation,
    required this.destinationLocation,
    required this.currentLocation,
    required this.shippingCost,
    required this.shippingMethod,
    required this.shippingProvider,
    required this.paymentStatus,
    required this.specialInstructions,
    required this.events,
    required this.requiresSignature,
    required this.insurance,
    required this.fragile,
    required this.express,
  });

  factory Shipment.fromJson(Map<String, dynamic> json) {
    return Shipment(
      id: json['id'] ?? 0,
      trackingNumber: json['tracking_number'] ?? '',
      shipmentType: json['shipment_type'] ?? '',
      userId: json['user_id'] ?? 0,
      senderName: json['sender_name'] ?? '',
      senderAddress: json['sender_address'] ?? '',
      recipientName: json['recipient_name'] ?? '',
      recipientAddress: json['recipient_address'] ?? '',
      weight: (json['weight'] is int)
          ? (json['weight'] as int).toDouble()
          : json['weight']?.toDouble() ?? 0.0,
      weightUnit: json['weight_unit'] ?? 'kg',
      length: (json['length'] is int)
          ? (json['length'] as int).toDouble()
          : json['length']?.toDouble() ?? 0.0,
      width: (json['width'] is int)
          ? (json['width'] as int).toDouble()
          : json['width']?.toDouble() ?? 0.0,
      height: (json['height'] is int)
          ? (json['height'] as int).toDouble()
          : json['height']?.toDouble() ?? 0.0,
      dimensionsUnit: json['dimensions_unit'] ?? 'cm',
      packageType: json['package_type'] ?? '',
      contentDescription: json['content_description'] ?? '',
      declaredValue: (json['declared_value'] is int)
          ? (json['declared_value'] as int).toDouble()
          : json['declared_value']?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      status: json['status'] ?? 'pending',
      statusDescription: json['status_description'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      shippedAt: json['shipped_at'] != null ? DateTime.parse(json['shipped_at']) : null,
      deliveredAt: json['delivered_at'] != null ? DateTime.parse(json['delivered_at']) : null,
      estimatedDelivery: json['estimated_delivery'] != null ? DateTime.parse(json['estimated_delivery']) : null,
      originLocation: json['origin_location'] ?? '',
      destinationLocation: json['destination_location'] ?? '',
      currentLocation: json['current_location'] ?? '',
      shippingCost: (json['shipping_cost'] is int)
          ? (json['shipping_cost'] as int).toDouble()
          : json['shipping_cost']?.toDouble() ?? 0.0,
      shippingMethod: json['shipping_method'] ?? '',
      shippingProvider: json['shipping_provider'] ?? '',
      paymentStatus: json['payment_status'] ?? 'pending',
      specialInstructions: json['special_instructions'] ?? '',
      events: (json['events'] as List<dynamic>?)
              ?.map((event) => ShipmentEvent.fromJson(event))
              .toList() ??
          [],
      requiresSignature: json['requires_signature'] ?? false,
      insurance: json['insurance'] ?? false,
      fragile: json['fragile'] ?? false,
      express: json['express'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'tracking_number': trackingNumber,
      'shipment_type': shipmentType,
      'user_id': userId,
      'sender_name': senderName,
      'sender_address': senderAddress,
      'recipient_name': recipientName,
      'recipient_address': recipientAddress,
      'weight': weight,
      'weight_unit': weightUnit,
      'length': length,
      'width': width,
      'height': height,
      'dimensions_unit': dimensionsUnit,
      'package_type': packageType,
      'content_description': contentDescription,
      'declared_value': declaredValue,
      'currency': currency,
      'status': status,
      'status_description': statusDescription,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'shipped_at': shippedAt?.toIso8601String(),
      'delivered_at': deliveredAt?.toIso8601String(),
      'estimated_delivery': estimatedDelivery?.toIso8601String(),
      'origin_location': originLocation,
      'destination_location': destinationLocation,
      'current_location': currentLocation,
      'shipping_cost': shippingCost,
      'shipping_method': shippingMethod,
      'shipping_provider': shippingProvider,
      'payment_status': paymentStatus,
      'special_instructions': specialInstructions,
      'events': events.map((event) => event.toJson()).toList(),
      'requires_signature': requiresSignature,
      'insurance': insurance,
      'fragile': fragile,
      'express': express,
    };
  }
}

class ShipmentEvent {
  final int id;
  final String eventType;
  final String description;
  final String location;
  final double latitude;
  final double longitude;
  final DateTime timestamp;
  final String status;

  ShipmentEvent({
    required this.id,
    required this.eventType,
    required this.description,
    required this.location,
    required this.latitude,
    required this.longitude,
    required this.timestamp,
    required this.status,
  });

  factory ShipmentEvent.fromJson(Map<String, dynamic> json) {
    return ShipmentEvent(
      id: json['id'] ?? 0,
      eventType: json['event_type'] ?? '',
      description: json['description'] ?? '',
      location: json['location'] ?? '',
      latitude: (json['latitude'] is int)
          ? (json['latitude'] as int).toDouble()
          : json['latitude']?.toDouble() ?? 0.0,
      longitude: (json['longitude'] is int)
          ? (json['longitude'] as int).toDouble()
          : json['longitude']?.toDouble() ?? 0.0,
      timestamp: DateTime.parse(json['timestamp'] ?? DateTime.now().toIso8601String()),
      status: json['status'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_type': eventType,
      'description': description,
      'location': location,
      'latitude': latitude,
      'longitude': longitude,
      'timestamp': timestamp.toIso8601String(),
      'status': status,
    };
  }
}