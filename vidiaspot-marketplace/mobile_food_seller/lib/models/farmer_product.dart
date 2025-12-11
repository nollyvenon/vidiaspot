class FarmerProduct {
  final String id;
  final String title;
  final String description;
  final double price;
  final String currency;
  final String condition;
  final String status;
  final String location;
  final double? latitude;
  final double? longitude;
  final String? contactPhone;
  final bool negotiable;
  final int viewCount;
  final DateTime? expiresAt;
  final bool directFromFarm;
  final String? farmName;
  final bool isOrganic;
  final DateTime? harvestDate;
  final String? farmLocation;
  final double? farmLatitude;
  final double? farmLongitude;
  final String? certification;
  final String? harvestSeason;
  final double? farmSize;
  final Map<String, dynamic>? user;
  final Map<String, dynamic>? category;
  final List<Map<String, dynamic>> images;

  FarmerProduct({
    required this.id,
    required this.title,
    required this.description,
    required this.price,
    required this.currency,
    required this.condition,
    required this.status,
    required this.location,
    this.latitude,
    this.longitude,
    this.contactPhone,
    required this.negotiable,
    required this.viewCount,
    this.expiresAt,
    required this.directFromFarm,
    this.farmName,
    required this.isOrganic,
    this.harvestDate,
    this.farmLocation,
    this.farmLatitude,
    this.farmLongitude,
    this.certification,
    this.harvestSeason,
    this.farmSize,
    this.user,
    this.category,
    required this.images,
  });

  factory FarmerProduct.fromJson(Map<String, dynamic> json) {
    return FarmerProduct(
      id: json['id'].toString(),
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      price: (json['price'] ?? 0).toDouble(),
      currency: json['currency']['code'] ?? 'NGN',
      condition: json['condition'] ?? '',
      status: json['status'] ?? '',
      location: json['location'] ?? '',
      latitude: json['latitude']?.toDouble(),
      longitude: json['longitude']?.toDouble(),
      contactPhone: json['contact_phone'] ?? json['contactPhone'],
      negotiable: json['negotiable'] ?? false,
      viewCount: json['view_count'] ?? json['viewCount'] ?? 0,
      expiresAt: json['expires_at'] != null ? DateTime.parse(json['expires_at']) : null,
      directFromFarm: json['direct_from_farm'] ?? json['directFromFarm'] ?? false,
      farmName: json['farm_name'] ?? json['farmName'],
      isOrganic: json['is_organic'] ?? json['isOrganic'] ?? false,
      harvestDate: json['harvest_date'] != null ? DateTime.parse(json['harvest_date']) : null,
      farmLocation: json['farm_location'] ?? json['farmLocation'],
      farmLatitude: json['farm_latitude']?.toDouble(),
      farmLongitude: json['farm_longitude']?.toDouble(),
      certification: json['certification'],
      harvestSeason: json['harvest_season'] ?? json['harvestSeason'],
      farmSize: json['farm_size']?.toDouble(),
      user: json['user'],
      category: json['category'],
      images: List<Map<String, dynamic>>.from(json['images'] ?? []),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'price': price,
      'currency': currency,
      'condition': condition,
      'status': status,
      'location': location,
      'latitude': latitude,
      'longitude': longitude,
      'contact_phone': contactPhone,
      'negotiable': negotiable,
      'view_count': viewCount,
      'expires_at': expiresAt?.toIso8601String(),
      'direct_from_farm': directFromFarm,
      'farm_name': farmName,
      'is_organic': isOrganic,
      'harvest_date': harvestDate?.toIso8601String(),
      'farm_location': farmLocation,
      'farm_latitude': farmLatitude,
      'farm_longitude': farmLongitude,
      'certification': certification,
      'harvest_season': harvestSeason,
      'farm_size': farmSize,
      'user': user,
      'category': category,
      'images': images,
    };
  }
}