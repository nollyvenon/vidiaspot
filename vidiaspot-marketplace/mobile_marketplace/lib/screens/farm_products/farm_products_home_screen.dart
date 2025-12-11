import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:shimmer/shimmer.dart';
import '../services/translation_service.dart';
import '../services/connectivity_service.dart';
import 'package:connectivity_plus/connectivity_plus.dart';

class FarmProductModel {
  final int id;
  final String title;
  final String description;
  final double price;
  final String location;
  final String image;
  final String condition;
  final String currency;
  final String farmName;
  final bool isOrganic;
  final int freshnessDays;
  final double qualityRating;

  FarmProductModel({
    required this.id,
    required this.title,
    required this.description,
    required this.price,
    required this.location,
    required this.image,
    required this.condition,
    this.currency = 'NGN',
    required this.farmName,
    required this.isOrganic,
    required this.freshnessDays,
    required this.qualityRating,
  });
}

class FarmProductsHomeScreen extends StatefulWidget {
  const FarmProductsHomeScreen({Key? key}) : super(key: key);

  @override
  _FarmProductsHomeScreenState createState() => _FarmProductsHomeScreenState();
}

class _FarmProductsHomeScreenState extends State<FarmProductsHomeScreen> {
  bool _isLoading = true;
  bool _isOnline = true;
  final TranslationService _translationService = TranslationService();
  final ConnectivityService _connectivityService = ConnectivityService();
  String _currentLanguage = 'en';

  final List<FarmProductModel> _farmProducts = [
    FarmProductModel(
      id: 1,
      title: 'Fresh Organic Tomatoes',
      description: 'Vine-ripened organic tomatoes from local farm',
      price: 1500.0,
      location: 'Ikeja, Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'Fresh',
      farmName: 'Green Valley Farms',
      isOrganic: true,
      freshnessDays: 1,
      qualityRating: 4.8,
    ),
    FarmProductModel(
      id: 2,
      title: 'Fresh Lettuce',
      description: 'Crisp lettuce, harvested this morning',
      price: 800.0,
      location: 'Surulere, Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'Fresh',
      farmName: 'Sunny Side Farms',
      isOrganic: false,
      freshnessDays: 2,
      qualityRating: 4.5,
    ),
    FarmProductModel(
      id: 3,
      title: 'Farm Fresh Eggs',
      description: 'Free-range eggs from happy chickens',
      price: 2500.0,
      location: 'Ajah, Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'Fresh',
      farmName: 'Happy Hens Farm',
      isOrganic: true,
      freshnessDays: 0,
      qualityRating: 4.9,
    ),
    FarmProductModel(
      id: 4,
      title: 'Organic Carrots',
      description: 'Sweet organic carrots, perfect for juicing',
      price: 1200.0,
      location: 'Ikorodu, Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'Fresh',
      farmName: 'Roots & Shoots Farm',
      isOrganic: true,
      freshnessDays: 1,
      qualityRating: 4.7,
    ),
  ];

  final List<Map<String, dynamic>> _farmCategories = [
    {
      'name': 'Fresh Vegetables',
      'icon': Icons.eco,
      'color': Colors.green,
      'count': 120,
    },
    {
      'name': 'Fresh Fruits',
      'icon': Icons.local_florist,
      'color': Colors.red,
      'count': 85,
    },
    {
      'name': 'Organic Products',
      'icon': Icons.eco,
      'color': Colors.lightGreen,
      'count': 65,
    },
    {
      'name': 'Dairy Products',
      'icon': Icons.local_drink,
      'color': Colors.blue,
      'count': 42,
    },
    {
      'name': 'Poultry & Eggs',
      'icon': Icons.egg,
      'color': Colors.orange,
      'count': 38,
    },
    {
      'name': 'Fresh Herbs',
      'icon': Icons.local_florist,
      'color': Colors.lightGreen,
      'count': 25,
    },
  ];

  @override
  void initState() {
    super.initState();
    _checkConnectivity();
    _loadCurrentLanguage();
  }

  void _loadCurrentLanguage() {
    // In a real app, this would get the language from settings service
    setState(() {
      _currentLanguage = 'en';
    });
  }

  void _checkConnectivity() async {
    final result = await _connectivityService.checkConnectivity();
    setState(() {
      _isOnline = result != ConnectivityResult.none;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Farm Products'),
        backgroundColor: Colors.green[600],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () {
              // Navigate to search screen
            },
          ),
          IconButton(
            icon: const Icon(Icons.notifications),
            onPressed: () {
              // Navigate to notifications
            },
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          // Refresh data
          await Future.delayed(const Duration(seconds: 1));
          setState(() {});
        },
        child: _isLoading
            ? _buildLoadingScreen()
            : SingleChildScrollView(
                child: Column(
                  children: [
                    _buildHeroBanner(),
                    _buildCategoriesSection(),
                    _buildFeaturedProductsSection(),
                    _buildNearbyFarmsSection(),
                  ],
                ),
              ),
      ),
    );
  }

  Widget _buildLoadingScreen() {
    return const Column(
      children: [
        SizedBox(height: 100),
        Center(
          child: CircularProgressIndicator(),
        ),
      ],
    );
  }

  Widget _buildHeroBanner() {
    return Container(
      height: 200,
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Colors.green, Colors.lightGreen],
        ),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Text(
            'Fresh from Local Farms',
            style: TextStyle(
              color: Colors.white,
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Text(
            'Buy directly from farmers',
            style: TextStyle(
              color: Colors.white70,
              fontSize: 16,
            ),
          ),
          const SizedBox(height: 20),
          ElevatedButton(
            onPressed: () {
              // Navigate to post farm product ad
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.white,
              foregroundColor: Colors.green[600],
            ),
            child: const Text(
              'Sell Your Farm Products',
              style: TextStyle(
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoriesSection() {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Farm Categories',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              TextButton(
                onPressed: () {
                  // Navigate to all categories
                },
                child: const Text(
                  'View All',
                  style: TextStyle(
                    color: Colors.green,
                    fontSize: 14,
                  ),
                ),
              ),
            ],
          ),
        ),
        SizedBox(
          height: 120,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: _farmCategories.length,
            itemBuilder: (context, index) {
              final category = _farmCategories[index];
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8.0),
                child: Column(
                  children: [
                    Container(
                      width: 80,
                      height: 80,
                      decoration: BoxDecoration(
                        color: category['color'].withOpacity(0.2),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Center(
                        child: Icon(
                          category['icon'],
                          size: 36,
                          color: category['color'],
                        ),
                      ),
                    ),
                    const SizedBox(height: 4),
                    SizedBox(
                      width: 80,
                      child: Text(
                        category['name'],
                        style: const TextStyle(fontSize: 12),
                        textAlign: TextAlign.center,
                      ),
                    ),
                    Text(
                      '${category['count']} ads',
                      style: const TextStyle(
                        fontSize: 10,
                        color: Colors.grey,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _buildFeaturedProductsSection() {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Fresh from Farms',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              TextButton(
                onPressed: () {
                  // Navigate to all farm products
                },
                child: const Text(
                  'View All',
                  style: TextStyle(
                    color: Colors.green,
                    fontSize: 14,
                  ),
                ),
              ),
            ],
          ),
        ),
        _buildProductsGrid(),
      ],
    );
  }

  Widget _buildProductsGrid() {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 10,
        mainAxisSpacing: 10,
        childAspectRatio: 0.8,
      ),
      itemCount: _farmProducts.length,
      itemBuilder: (context, index) {
        final product = _farmProducts[index];
        return _buildProductCard(product);
      },
    );
  }

  Widget _buildProductCard(FarmProductModel product) {
    final shouldUseLowQuality = !_isOnline ||
        _connectivityService.shouldUseLowQualityImages(
          ConnectivityResult.mobile, // For demo purposes
        );

    return Card(
      elevation: 2,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 2,
            child: ClipRRect(
              borderRadius: const BorderRadius.vertical(
                top: Radius.circular(4),
              ),
              child: CachedNetworkImage(
                imageUrl: shouldUseLowQuality
                    ? product.image.replaceAll('200x200', '100x100')
                    : product.image,
                fit: BoxFit.cover,
                placeholder: (context, url) => Shimmer.fromColors(
                  baseColor: Colors.grey[300]!,
                  highlightColor: Colors.grey[100]!,
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                ),
                errorWidget: (context, url, error) => Container(
                  color: Colors.grey[200],
                  child: const Icon(
                    Icons.image_not_supported,
                    size: 50,
                    color: Colors.grey,
                  ),
                ),
              ),
            ),
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(8.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          product.title,
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      if (product.isOrganic)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 2),
                          decoration: BoxDecoration(
                            color: Colors.lightGreen[100],
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: const Text(
                            'ORG',
                            style: TextStyle(
                              color: Colors.green,
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${product.currency} ${product.price.toStringAsFixed(0)}',
                    style: const TextStyle(
                      color: Colors.green,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      const Icon(
                        Icons.star,
                        size: 12,
                        color: Colors.orange,
                      ),
                      Text(
                        '${product.qualityRating}',
                        style: const TextStyle(
                          fontSize: 12,
                          color: Colors.grey,
                        ),
                      ),
                      const SizedBox(width: 4),
                      const Icon(
                        Icons.timelapse,
                        size: 12,
                        color: Colors.grey,
                      ),
                      Text(
                        '${product.freshnessDays}d old',
                        style: const TextStyle(
                          fontSize: 12,
                          color: Colors.grey,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      const Icon(
                        Icons.location_on,
                        size: 12,
                        color: Colors.grey,
                      ),
                      Expanded(
                        child: Text(
                          product.location,
                          style: const TextStyle(
                            fontSize: 11,
                            color: Colors.grey,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 2),
                  Text(
                    product.farmName,
                    style: const TextStyle(
                      fontSize: 11,
                      color: Colors.green,
                      fontWeight: FontWeight.w500,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNearbyFarmsSection() {
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Nearby Farms',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Container(
            height: 150,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(12),
              color: Colors.grey[100],
            ),
            child: const Center(
              child: Text('Map showing nearby farms'),
            ),
          ),
        ],
      ),
    );
  }
}