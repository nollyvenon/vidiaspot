class Product {
  final String id;
  final String name;
  final String description;
  final String shortDescription;
  final String category;
  final String subcategory;
  final double price;
  final double compareAtPrice;
  final String currency;
  final int inventoryQuantity;
  final bool isAvailable;
  final bool isVisible;
  final List<String> images;
  final String mainImage;
  final String sku;
  final String barcode;
  final String weight;
  final String dimensions;
  final String brand;
  final List<String> tags;
  final DateTime createdAt;
  final DateTime updatedAt;
  final String shopId;
  final int viewCount;
  final int orderCount;

  Product({
    required this.id,
    required this.name,
    required this.description,
    required this.shortDescription,
    required this.category,
    required this.subcategory,
    required this.price,
    required this.compareAtPrice,
    required this.currency,
    required this.inventoryQuantity,
    required this.isAvailable,
    required this.isVisible,
    required this.images,
    required this.mainImage,
    required this.sku,
    required this.barcode,
    required this.weight,
    required this.dimensions,
    required this.brand,
    required this.tags,
    required this.createdAt,
    required this.updatedAt,
    required this.shopId,
    required this.viewCount,
    required this.orderCount,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    List<String> images = [];
    if (json['images'] != null) {
      images = (json['images'] as List).map((item) => item.toString()).toList();
    }
    
    List<String> tags = [];
    if (json['tags'] != null) {
      tags = (json['tags'] as List).map((item) => item.toString()).toList();
    }

    return Product(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      shortDescription: json['short_description'] ?? '',
      category: json['category'] ?? '',
      subcategory: json['subcategory'] ?? '',
      price: (json['price'] as num?)?.toDouble() ?? 0.0,
      compareAtPrice: (json['compare_at_price'] as num?)?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      inventoryQuantity: json['inventory_quantity'] ?? 0,
      isAvailable: json['is_available'] ?? true,
      isVisible: json['is_visible'] ?? true,
      images: images,
      mainImage: json['main_image'] ?? '',
      sku: json['sku'] ?? '',
      barcode: json['barcode'] ?? '',
      weight: json['weight'] ?? '',
      dimensions: json['dimensions'] ?? '',
      brand: json['brand'] ?? '',
      tags: tags,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      shopId: json['shop_id'] ?? '',
      viewCount: json['view_count'] ?? 0,
      orderCount: json['order_count'] ?? 0,
    );
  }
}