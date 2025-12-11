// lib/models/food_vending/restaurant_model.dart
class Restaurant {
  final int id;
  final String name;
  final String description;
  final String address;
  final double latitude;
  final double longitude;
  final String imageUrl;
  final double rating;
  final int reviewCount;
  final String cuisineType;
  final List<String> paymentMethods;
  final String openingHours;
  final String closingHours;
  final bool isAvailable;
  final bool isFeatured;
  final double deliveryFee;
  final int estimatedDeliveryTime;
  final String deliveryRadius;
  final String contactNumber;
  final String email;
  final List<String> tags;
  final DateTime createdAt;
  final DateTime updatedAt;

  Restaurant({
    required this.id,
    required this.name,
    required this.description,
    required this.address,
    required this.latitude,
    required this.longitude,
    required this.imageUrl,
    required this.rating,
    required this.reviewCount,
    required this.cuisineType,
    required this.paymentMethods,
    required this.openingHours,
    required this.closingHours,
    required this.isAvailable,
    required this.isFeatured,
    required this.deliveryFee,
    required this.estimatedDeliveryTime,
    required this.deliveryRadius,
    required this.contactNumber,
    required this.email,
    required this.tags,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Restaurant.fromJson(Map<String, dynamic> json) {
    return Restaurant(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      address: json['address'] ?? '',
      latitude: (json['latitude'] is int)
          ? (json['latitude'] as int).toDouble()
          : json['latitude']?.toDouble() ?? 0.0,
      longitude: (json['longitude'] is int)
          ? (json['longitude'] as int).toDouble()
          : json['longitude']?.toDouble() ?? 0.0,
      imageUrl: json['image_url'] ?? '',
      rating: (json['rating'] is int)
          ? (json['rating'] as int).toDouble()
          : json['rating']?.toDouble() ?? 0.0,
      reviewCount: json['review_count'] ?? 0,
      cuisineType: json['cuisine_type'] ?? '',
      paymentMethods: List<String>.from(json['payment_methods'] ?? []),
      openingHours: json['opening_hours'] ?? '',
      closingHours: json['closing_hours'] ?? '',
      isAvailable: json['is_available'] ?? true,
      isFeatured: json['is_featured'] ?? false,
      deliveryFee: (json['delivery_fee'] is int)
          ? (json['delivery_fee'] as int).toDouble()
          : json['delivery_fee']?.toDouble() ?? 0.0,
      estimatedDeliveryTime: json['estimated_delivery_time'] ?? 30,
      deliveryRadius: json['delivery_radius'] ?? '5km',
      contactNumber: json['contact_number'] ?? '',
      email: json['email'] ?? '',
      tags: List<String>.from(json['tags'] ?? []),
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'address': address,
      'latitude': latitude,
      'longitude': longitude,
      'image_url': imageUrl,
      'rating': rating,
      'review_count': reviewCount,
      'cuisine_type': cuisineType,
      'payment_methods': paymentMethods,
      'opening_hours': openingHours,
      'closing_hours': closingHours,
      'is_available': isAvailable,
      'is_featured': isFeatured,
      'delivery_fee': deliveryFee,
      'estimated_delivery_time': estimatedDeliveryTime,
      'delivery_radius': deliveryRadius,
      'contact_number': contactNumber,
      'email': email,
      'tags': tags,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}

class FoodItem {
  final int id;
  final int restaurantId;
  final String name;
  final String description;
  final double price;
  final String imageUrl;
  final String category;
  final bool isAvailable;
  final bool isFeatured;
  final double rating;
  final int reviewCount;
  final String preparationTime;
  final List<String> ingredients;
  final bool isVegetarian;
  final bool isVegan;
  final bool isGlutenFree;
  final DateTime createdAt;
  final DateTime updatedAt;

  FoodItem({
    required this.id,
    required this.restaurantId,
    required this.name,
    required this.description,
    required this.price,
    required this.imageUrl,
    required this.category,
    required this.isAvailable,
    required this.isFeatured,
    required this.rating,
    required this.reviewCount,
    required this.preparationTime,
    required this.ingredients,
    required this.isVegetarian,
    required this.isVegan,
    required this.isGlutenFree,
    required this.createdAt,
    required this.updatedAt,
  });

  factory FoodItem.fromJson(Map<String, dynamic> json) {
    return FoodItem(
      id: json['id'] ?? 0,
      restaurantId: json['restaurant_id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      price: (json['price'] is int)
          ? (json['price'] as int).toDouble()
          : json['price']?.toDouble() ?? 0.0,
      imageUrl: json['image_url'] ?? '',
      category: json['category'] ?? '',
      isAvailable: json['is_available'] ?? true,
      isFeatured: json['is_featured'] ?? false,
      rating: (json['rating'] is int)
          ? (json['rating'] as int).toDouble()
          : json['rating']?.toDouble() ?? 0.0,
      reviewCount: json['review_count'] ?? 0,
      preparationTime: json['preparation_time'] ?? '20-30 mins',
      ingredients: List<String>.from(json['ingredients'] ?? []),
      isVegetarian: json['is_vegetarian'] ?? false,
      isVegan: json['is_vegan'] ?? false,
      isGlutenFree: json['is_gluten_free'] ?? false,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'restaurant_id': restaurantId,
      'name': name,
      'description': description,
      'price': price,
      'image_url': imageUrl,
      'category': category,
      'is_available': isAvailable,
      'is_featured': isFeatured,
      'rating': rating,
      'review_count': reviewCount,
      'preparation_time': preparationTime,
      'ingredients': ingredients,
      'is_vegetarian': isVegetarian,
      'is_vegan': isVegan,
      'is_gluten_free': isGlutenFree,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}