class ShopData {
  final String id;
  final String name;
  final String description;
  final String logoUrl;
  final String coverImage;
  final String ownerName;
  final String email;
  final String phone;
  final String address;
  final String currency;
  final bool isActive;
  final DateTime createdAt;
  final String category;

  ShopData({
    required this.id,
    required this.name,
    required this.description,
    required this.logoUrl,
    required this.coverImage,
    required this.ownerName,
    required this.email,
    required this.phone,
    required this.address,
    required this.currency,
    required this.isActive,
    required this.createdAt,
    required this.category,
  });

  factory ShopData.fromJson(Map<String, dynamic> json) {
    return ShopData(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      logoUrl: json['logo_url'] ?? '',
      coverImage: json['cover_image'] ?? '',
      ownerName: json['owner_name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      address: json['address'] ?? '',
      currency: json['currency'] ?? 'USD',
      isActive: json['is_active'] ?? true,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      category: json['category'] ?? '',
    );
  }
}