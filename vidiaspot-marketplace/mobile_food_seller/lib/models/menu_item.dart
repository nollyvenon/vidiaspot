class MenuItem {
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
  final double weight; // in kg
  final String dimensions; // e.g. "10x10x10 cm"
  final String ingredients;
  final List<String> allergens;
  final bool isVegetarian;
  final bool isVegan;
  final bool isGlutenFree;
  final String dietaryInfo;
  final String nutritionalInfo;
  final List<String> tags;
  final DateTime createdAt;
  final DateTime updatedAt;
  final String restaurantId;
  final int viewCount;
  final int orderCount;
  final double avgRating;
  final int numRatings;

  MenuItem({
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
    required this.ingredients,
    required this.allergens,
    required this.isVegetarian,
    required this.isVegan,
    required this.isGlutenFree,
    required this.dietaryInfo,
    required this.nutritionalInfo,
    required this.tags,
    required this.createdAt,
    required this.updatedAt,
    required this.restaurantId,
    required this.viewCount,
    required this.orderCount,
    required this.avgRating,
    required this.numRatings,
  });

  factory MenuItem.fromJson(Map<String, dynamic> json) {
    List<String> images = [];
    if (json['images'] != null) {
      images = (json['images'] as List).map((item) => item.toString()).toList();
    }
    
    List<String> allergens = [];
    if (json['allergens'] != null) {
      allergens = (json['allergens'] as List).map((item) => item.toString()).toList();
    }

    List<String> tags = [];
    if (json['tags'] != null) {
      tags = (json['tags'] as List).map((item) => item.toString()).toList();
    }

    return MenuItem(
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
      weight: (json['weight'] as num?)?.toDouble() ?? 0.0,
      dimensions: json['dimensions'] ?? '',
      ingredients: json['ingredients'] ?? '',
      allergens: allergens,
      isVegetarian: json['is_vegetarian'] ?? false,
      isVegan: json['is_vegan'] ?? false,
      isGlutenFree: json['is_gluten_free'] ?? false,
      dietaryInfo: json['dietary_info'] ?? '',
      nutritionalInfo: json['nutritional_info'] ?? '',
      tags: tags,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      restaurantId: json['restaurant_id'] ?? '',
      viewCount: json['view_count'] ?? 0,
      orderCount: json['order_count'] ?? 0,
      avgRating: (json['avg_rating'] as num?)?.toDouble() ?? 0.0,
      numRatings: json['num_ratings'] ?? 0,
    );
  }
}