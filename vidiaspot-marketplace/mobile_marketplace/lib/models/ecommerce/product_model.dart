// lib/models/ecommerce/product_model.dart
class Product {
  final int id;
  final String name;
  final String description;
  final double price;
  final double originalPrice;
  final String imageUrl;
  final int categoryId;
  final String categoryName;
  final String sku;
  final int stockQuantity;
  final double rating;
  final int reviewCount;
  final bool isFeatured;
  final bool isAvailable;
  final String brand;
  final List<String> tags;
  final DateTime createdAt;
  final DateTime updatedAt;

  Product({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    required this.originalPrice,
    required this.imageUrl,
    required this.categoryId,
    required this.categoryName,
    required this.sku,
    required this.stockQuantity,
    required this.rating,
    required this.reviewCount,
    required this.isFeatured,
    required this.isAvailable,
    required this.brand,
    required this.tags,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      price: (json['price'] is int)
          ? (json['price'] as int).toDouble()
          : json['price']?.toDouble() ?? 0.0,
      originalPrice: (json['original_price'] is int)
          ? (json['original_price'] as int).toDouble()
          : (json['original_price']?.toDouble() ?? 0.0),
      imageUrl: json['image_url'] ?? '',
      categoryId: json['category_id'] ?? 0,
      categoryName: json['category_name'] ?? '',
      sku: json['sku'] ?? '',
      stockQuantity: json['stock_quantity'] ?? 0,
      rating: (json['rating'] is int)
          ? (json['rating'] as int).toDouble()
          : json['rating']?.toDouble() ?? 0.0,
      reviewCount: json['review_count'] ?? 0,
      isFeatured: json['is_featured'] ?? false,
      isAvailable: json['is_available'] ?? true,
      brand: json['brand'] ?? '',
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
      'price': price,
      'original_price': originalPrice,
      'image_url': imageUrl,
      'category_id': categoryId,
      'category_name': categoryName,
      'sku': sku,
      'stock_quantity': stockQuantity,
      'rating': rating,
      'review_count': reviewCount,
      'is_featured': isFeatured,
      'is_available': isAvailable,
      'brand': brand,
      'tags': tags,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}