class FarmData {
  final String id;
  final String name;
  final String description;
  final String ownerName;
  final String email;
  final String phone;
  final String address;
  final double latitude;
  final double longitude;
  final String logoUrl;
  final String coverImage;
  final List<String> categories; // farm types: vegetables, fruits, livestock, etc.
  final String certification; // organic, conventional, etc.
  final int yearsInBusiness;
  final String operatingHours;
  final String deliveryRadius; // in km
  final bool acceptsOnlineOrders;
  final bool offersPickup;
  final bool offersDelivery;
  final String paymentMethods; // cash, card, mobile money
  final double rating;
  final int numReviews;
  final bool isActive;
  final DateTime createdAt;
  final DateTime updatedAt;

  FarmData({
    required this.id,
    required this.name,
    required this.description,
    required this.ownerName,
    required this.email,
    required this.phone,
    required this.address,
    required this.latitude,
    required this.longitude,
    required this.logoUrl,
    required this.coverImage,
    required this.categories,
    required this.certification,
    required this.yearsInBusiness,
    required this.operatingHours,
    required this.deliveryRadius,
    required this.acceptsOnlineOrders,
    required this.offersPickup,
    required this.offersDelivery,
    required this.paymentMethods,
    required this.rating,
    required this.numReviews,
    required this.isActive,
    required this.createdAt,
    required this.updatedAt,
  });

  factory FarmData.fromJson(Map<String, dynamic> json) {
    List<String> categories = [];
    if (json['categories'] != null) {
      categories = (json['categories'] as List).map((item) => item.toString()).toList();
    }

    return FarmData(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      ownerName: json['owner_name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      address: json['address'] ?? '',
      latitude: (json['latitude'] as num?)?.toDouble() ?? 0.0,
      longitude: (json['longitude'] as num?)?.toDouble() ?? 0.0,
      logoUrl: json['logo_url'] ?? '',
      coverImage: json['cover_image'] ?? '',
      categories: categories,
      certification: json['certification'] ?? '',
      yearsInBusiness: json['years_in_business'] ?? 0,
      operatingHours: json['operating_hours'] ?? '',
      deliveryRadius: json['delivery_radius'] ?? '10',
      acceptsOnlineOrders: json['accepts_online_orders'] ?? true,
      offersPickup: json['offers_pickup'] ?? true,
      offersDelivery: json['offers_delivery'] ?? false,
      paymentMethods: json['payment_methods'] ?? 'Cash, Card',
      rating: (json['rating'] as num?)?.toDouble() ?? 0.0,
      numReviews: json['num_reviews'] ?? 0,
      isActive: json['is_active'] ?? true,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
    );
  }
}