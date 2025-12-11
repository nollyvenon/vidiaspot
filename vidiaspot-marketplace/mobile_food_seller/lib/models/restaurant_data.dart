class RestaurantData {
  final String id;
  final String name;
  final String description;
  final String logoUrl;
  final String bannerImage;
  final String ownerName;
  final String email;
  final String phone;
  final String address;
  final List<String> categories; // food types
  final double rating;
  final int numRatings;
  final String currency;
  final bool isActive;
  final bool acceptsOrders;
  final DateTime createdAt;
  final List<String> operatingHours; // daily operating hours
  final String deliveryRadius; // in km

  RestaurantData({
    required this.id,
    required this.name,
    required this.description,
    required this.logoUrl,
    required this.bannerImage,
    required this.ownerName,
    required this.email,
    required this.phone,
    required this.address,
    required this.categories,
    required this.rating,
    required this.numRatings,
    required this.currency,
    required this.isActive,
    required this.acceptsOrders,
    required this.createdAt,
    required this.operatingHours,
    required this.deliveryRadius,
  });

  factory RestaurantData.fromJson(Map<String, dynamic> json) {
    List<String> categories = [];
    if (json['categories'] != null) {
      categories = (json['categories'] as List).map((item) => item.toString()).toList();
    }
    
    List<String> operatingHours = [];
    if (json['operating_hours'] != null) {
      operatingHours = (json['operating_hours'] as List).map((item) => item.toString()).toList();
    }

    return RestaurantData(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      logoUrl: json['logo_url'] ?? '',
      bannerImage: json['banner_image'] ?? '',
      ownerName: json['owner_name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      address: json['address'] ?? '',
      categories: categories,
      rating: (json['rating'] as num?)?.toDouble() ?? 0.0,
      numRatings: json['num_ratings'] ?? 0,
      currency: json['currency'] ?? 'USD',
      isActive: json['is_active'] ?? true,
      acceptsOrders: json['accepts_orders'] ?? true,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      operatingHours: operatingHours,
      deliveryRadius: json['delivery_radius'] ?? '10',
    );
  }
}