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
  
  // Cart-related properties
  final int cartQuantity;

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
    this.cartQuantity = 0,
  });

  MenuItem copyWith({
    String? id,
    String? name,
    String? description,
    String? shortDescription,
    String? category,
    String? subcategory,
    double? price,
    double? compareAtPrice,
    String? currency,
    int? inventoryQuantity,
    bool? isAvailable,
    bool? isVisible,
    List<String>? images,
    String? mainImage,
    String? sku,
    String? barcode,
    double? weight,
    String? dimensions,
    String? ingredients,
    List<String>? allergens,
    bool? isVegetarian,
    bool? isVegan,
    bool? isGlutenFree,
    String? dietaryInfo,
    String? nutritionalInfo,
    List<String>? tags,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? restaurantId,
    int? viewCount,
    int? orderCount,
    double? avgRating,
    int? numRatings,
    int? cartQuantity,
  }) {
    return MenuItem(
      id: id ?? this.id,
      name: name ?? this.name,
      description: description ?? this.description,
      shortDescription: shortDescription ?? this.shortDescription,
      category: category ?? this.category,
      subcategory: subcategory ?? this.subcategory,
      price: price ?? this.price,
      compareAtPrice: compareAtPrice ?? this.compareAtPrice,
      currency: currency ?? this.currency,
      inventoryQuantity: inventoryQuantity ?? this.inventoryQuantity,
      isAvailable: isAvailable ?? this.isAvailable,
      isVisible: isVisible ?? this.isVisible,
      images: images ?? this.images,
      mainImage: mainImage ?? this.mainImage,
      sku: sku ?? this.sku,
      barcode: barcode ?? this.barcode,
      weight: weight ?? this.weight,
      dimensions: dimensions ?? this.dimensions,
      ingredients: ingredients ?? this.ingredients,
      allergens: allergens ?? this.allergens,
      isVegetarian: isVegetarian ?? this.isVegetarian,
      isVegan: isVegan ?? this.isVegan,
      isGlutenFree: isGlutenFree ?? this.isGlutenFree,
      dietaryInfo: dietaryInfo ?? this.dietaryInfo,
      nutritionalInfo: nutritionalInfo ?? this.nutritionalInfo,
      tags: tags ?? this.tags,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      restaurantId: restaurantId ?? this.restaurantId,
      viewCount: viewCount ?? this.viewCount,
      orderCount: orderCount ?? this.orderCount,
      avgRating: avgRating ?? this.avgRating,
      numRatings: numRatings ?? this.numRatings,
      cartQuantity: cartQuantity ?? this.cartQuantity,
    );
  }

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