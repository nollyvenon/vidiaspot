class UserProfile {
  final String id;
  final String name;
  final String email;
  final String phone;
  final String address;
  final String city;
  final String state;
  final String country;
  final String postalCode;
  final String profilePictureUrl;
  final DateTime createdAt;
  final DateTime updatedAt;
  final bool emailVerified;
  final bool phoneVerified;
  final String preferredLanguage;
  final String preferredCurrency;
  final List<String> savedAddresses;
  final List<String> favoriteRestaurants;

  UserProfile({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.address,
    required this.city,
    required this.state,
    required this.country,
    required this.postalCode,
    required this.profilePictureUrl,
    required this.createdAt,
    required this.updatedAt,
    required this.emailVerified,
    required this.phoneVerified,
    required this.preferredLanguage,
    required this.preferredCurrency,
    required this.savedAddresses,
    required this.favoriteRestaurants,
  });

  factory UserProfile.fromJson(Map<String, dynamic> json) {
    List<String> savedAddresses = [];
    if (json['saved_addresses'] != null) {
      savedAddresses = (json['saved_addresses'] as List).map((addr) => addr.toString()).toList();
    }

    List<String> favoriteRestaurants = [];
    if (json['favorite_restaurants'] != null) {
      favoriteRestaurants = (json['favorite_restaurants'] as List).map((id) => id.toString()).toList();
    }

    return UserProfile(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      address: json['address'] ?? '',
      city: json['city'] ?? '',
      state: json['state'] ?? '',
      country: json['country'] ?? '',
      postalCode: json['postal_code'] ?? '',
      profilePictureUrl: json['profile_picture_url'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      emailVerified: json['email_verified'] ?? false,
      phoneVerified: json['phone_verified'] ?? false,
      preferredLanguage: json['preferred_language'] ?? 'en',
      preferredCurrency: json['preferred_currency'] ?? 'USD',
      savedAddresses: savedAddresses,
      favoriteRestaurants: favoriteRestaurants,
    );
  }
}