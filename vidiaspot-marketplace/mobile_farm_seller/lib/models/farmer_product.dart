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
  final int? freshnessDays;
  final double? qualityRating;
  final List<String>? seasonalAvailability;
  final String? certificationType;
  final String? certificationBody;
  final List<String>? farmPractices;
  final List<String>? deliveryOptions;
  final double? minimumOrder;
  final String? packagingType;
  final int? shelfLife;
  final String? storageInstructions;
  final List<String>? farmCertifications;
  final bool? pesticideUse;
  final String? irrigationMethod;
  final String? soilType;
  final double? sustainabilityScore;
  final double? carbonFootprint;
  final bool? farmTourAvailable;
  final String? farmStory;
  final String? farmerName;
  final String? farmerImage;
  final String? farmerBio;
  final String? harvestMethod;
  final String? postHarvestHandling;
  final int? supplyCapacity;
  final double? shippingAvailability;
  final double? localDeliveryRadius;
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
    this.freshnessDays,
    this.qualityRating,
    this.seasonalAvailability,
    this.certificationType,
    this.certificationBody,
    this.farmPractices,
    this.deliveryOptions,
    this.minimumOrder,
    this.packagingType,
    this.shelfLife,
    this.storageInstructions,
    this.farmCertifications,
    this.pesticideUse,
    this.irrigationMethod,
    this.soilType,
    this.sustainabilityScore,
    this.carbonFootprint,
    this.farmTourAvailable,
    this.farmStory,
    this.farmerName,
    this.farmerImage,
    this.farmerBio,
    this.harvestMethod,
    this.postHarvestHandling,
    this.supplyCapacity,
    this.shippingAvailability,
    this.localDeliveryRadius,
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
      freshnessDays: json['freshness_days'],
      qualityRating: (json['quality_rating'] is num) ? (json['quality_rating'] as num).toDouble() : null,
      seasonalAvailability: (json['seasonal_availability'] as List?)?.cast<String>(),
      certificationType: json['certification_type'],
      certificationBody: json['certification_body'],
      farmPractices: (json['farm_practices'] as List?)?.cast<String>(),
      deliveryOptions: (json['delivery_options'] as List?)?.cast<String>(),
      minimumOrder: (json['minimum_order'] is num) ? (json['minimum_order'] as num).toDouble() : null,
      packagingType: json['packaging_type'],
      shelfLife: json['shelf_life'],
      storageInstructions: json['storage_instructions'],
      farmCertifications: (json['farm_certifications'] as List?)?.cast<String>(),
      pesticideUse: json['pesticide_use'],
      irrigationMethod: json['irrigation_method'],
      soilType: json['soil_type'],
      sustainabilityScore: (json['sustainability_score'] is num) ? (json['sustainability_score'] as num).toDouble() : null,
      carbonFootprint: (json['carbon_footprint'] is num) ? (json['carbon_footprint'] as num).toDouble() : null,
      farmTourAvailable: json['farm_tour_available'] ?? false,
      farmStory: json['farm_story'],
      farmerName: json['farmer_name'],
      farmerImage: json['farmer_image'],
      farmerBio: json['farmer_bio'],
      harvestMethod: json['harvest_method'],
      postHarvestHandling: json['post_harvest_handling'],
      supplyCapacity: json['supply_capacity'],
      shippingAvailability: (json['shipping_availability'] is num) ? (json['shipping_availability'] as num).toDouble() : null,
      localDeliveryRadius: (json['local_delivery_radius'] is num) ? (json['local_delivery_radius'] as num).toDouble() : null,
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
      'freshness_days': freshnessDays,
      'quality_rating': qualityRating,
      'seasonal_availability': seasonalAvailability,
      'certification_type': certificationType,
      'certification_body': certificationBody,
      'farm_practices': farmPractices,
      'delivery_options': deliveryOptions,
      'minimum_order': minimumOrder,
      'packaging_type': packagingType,
      'shelf_life': shelfLife,
      'storage_instructions': storageInstructions,
      'farm_certifications': farmCertifications,
      'pesticide_use': pesticideUse,
      'irrigation_method': irrigationMethod,
      'soil_type': soilType,
      'sustainability_score': sustainabilityScore,
      'carbon_footprint': carbonFootprint,
      'farm_tour_available': farmTourAvailable,
      'farm_story': farmStory,
      'farmer_name': farmerName,
      'farmer_image': farmerImage,
      'farmer_bio': farmerBio,
      'harvest_method': harvestMethod,
      'post_harvest_handling': postHarvestHandling,
      'supply_capacity': supplyCapacity,
      'shipping_availability': shippingAvailability,
      'local_delivery_radius': localDeliveryRadius,
      'user': user,
      'category': category,
      'images': images,
    };
  }
}