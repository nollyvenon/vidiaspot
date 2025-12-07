import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:shimmer/shimmer.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import '../services/translation_service.dart';
import '../services/connectivity_service.dart';
import '../widgets/translation_widget.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class AdModel {
  final int id;
  final String title;
  final String description;
  final double price;
  final String location;
  final String image;
  final String condition;
  final String currency;

  AdModel({
    required this.id,
    required this.title,
    required this.description,
    required this.price,
    required this.location,
    required this.image,
    required this.condition,
    this.currency = 'NGN',
  });
}

class CategoryModel {
  final int id;
  final String name;
  final String icon;
  final String translatedName; // For translated display

  CategoryModel({
    required this.id,
    required this.name,
    required this.icon,
    required this.translatedName,
  });
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;
  bool _isLoading = true;
  bool _isOnline = true;
  final TranslationService _translationService = TranslationService();
  final ConnectivityService _connectivityService = ConnectivityService();
  String _currentLanguage = 'en';

  final List<AdModel> _ads = [
    AdModel(
      id: 1,
      title: 'iPhone 13 Pro',
      description: 'Like new condition, comes with original box',
      price: 450000.0,
      location: 'Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'Like New',
    ),
    AdModel(
      id: 2,
      title: 'Toyota Camry 2018',
      description: 'Well maintained, low mileage',
      price: 12000000.0,
      location: 'Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'Good',
    ),
    AdModel(
      id: 3,
      title: '3-Bedroom House',
      description: 'Spacious house in GRA, Ikeja',
      price: 80000000.0,
      location: 'Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'New',
    ),
    AdModel(
      id: 4,
      title: 'MacBook Pro',
      description: '2021 model, 16GB RAM, 512GB SSD',
      price: 850000.0,
      location: 'Lagos',
      image: 'https://via.placeholder.com/200x200',
      condition: 'Good',
    ),
  ];

  List<CategoryModel> _categories = [];

  @override
  void initState() {
    super.initState();
    _initializeCategories();
    _checkConnectivity();
    _loadCurrentLanguage();
  }

  void _loadCurrentLanguage() {
    // In a real app, this would get the language from settings service
    setState(() {
      _currentLanguage = 'en';
    });
  }

  void _initializeCategories() {
    _categories = [
      CategoryModel(
        id: 1,
        name: 'Electronics',
        icon: '📱',
        translatedName: 'Electronics', // This will be translated in the UI
      ),
      CategoryModel(
        id: 2,
        name: 'Vehicles',
        icon: '🚗',
        translatedName: 'Vehicles',
      ),
      CategoryModel(
        id: 3,
        name: 'Property',
        icon: '🏠',
        translatedName: 'Property',
      ),
      CategoryModel(
        id: 4,
        name: 'Furniture',
        icon: '🛋️',
        translatedName: 'Furniture',
      ),
      CategoryModel(
        id: 5,
        name: 'Fashion',
        icon: '👕',
        translatedName: 'Fashion',
      ),
      CategoryModel(
        id: 6,
        name: 'Jobs',
        icon: '💼',
        translatedName: 'Jobs',
      ),
    ];
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
        title: TranslationWidget(
          text: 'VidiaSpot',
          to: _currentLanguage,
          style: const TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
          ),
        ),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.translate),
            onPressed: () => _showLanguageSelector(),
          ),
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
                    _buildFeaturedAdsSection(),
                  ],
                ),
              ),
      ),
      bottomNavigationBar: _buildBottomNavigationBar(),
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
      height: 180,
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Colors.blue, Colors.indigo],
        ),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          TranslationWidget(
            text: 'Buy and Sell Locally',
            to: _currentLanguage,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          TranslationWidget(
            text: 'Find great deals or sell your items',
            to: _currentLanguage,
            style: TextStyle(
              color: Colors.white70,
              fontSize: 16,
            ),
          ),
          const SizedBox(height: 20),
          ElevatedButton(
            onPressed: () {
              // Navigate to post ad screen
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.white,
              foregroundColor: Colors.blue,
            ),
            child: TranslationWidget(
              text: 'Post Your Ad',
              to: _currentLanguage,
              style: const TextStyle(
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
              TranslationWidget(
                text: 'Popular Categories',
                to: _currentLanguage,
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              TextButton(
                onPressed: () {
                  // Navigate to all categories
                },
                child: TranslationWidget(
                  text: 'View All',
                  to: _currentLanguage,
                  style: const TextStyle(
                    color: Colors.blue,
                    fontSize: 14,
                  ),
                ),
              ),
            ],
          ),
        ),
        SizedBox(
          height: 100,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: _categories.length,
            itemBuilder: (context, index) {
              final category = _categories[index];
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8.0),
                child: Column(
                  children: [
                    Container(
                      width: 70,
                      height: 70,
                      decoration: BoxDecoration(
                        color: Colors.grey[200],
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Center(
                        child: Text(
                          category.icon,
                          style: const TextStyle(fontSize: 30),
                        ),
                      ),
                    ),
                    const SizedBox(height: 4),
                    SizedBox(
                      width: 70,
                      child: TranslationWidget(
                        text: category.name,
                        to: _currentLanguage,
                        style: const TextStyle(fontSize: 12),
                        textAlign: TextAlign.center,
                      ),
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

  Widget _buildFeaturedAdsSection() {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              TranslationWidget(
                text: 'Featured Ads',
                to: _currentLanguage,
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              TextButton(
                onPressed: () {
                  // Navigate to all ads
                },
                child: TranslationWidget(
                  text: 'View All',
                  to: _currentLanguage,
                  style: const TextStyle(
                    color: Colors.blue,
                    fontSize: 14,
                  ),
                ),
              ),
            ],
          ),
        ),
        _buildAdsGrid(),
      ],
    );
  }

  Widget _buildAdsGrid() {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 10,
        mainAxisSpacing: 10,
        childAspectRatio: 0.8,
      ),
      itemCount: _ads.length,
      itemBuilder: (context, index) {
        final ad = _ads[index];
        return _buildAdCard(ad);
      },
    );
  }

  Widget _buildAdCard(AdModel ad) {
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
                    ? ad.image.replaceAll('200x200', '100x100') 
                    : ad.image,
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
                  TranslationWidget(
                    text: ad.title,
                    to: _currentLanguage,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 14,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${ad.currency} ${ad.price.toStringAsFixed(0)}',
                    style: const TextStyle(
                      color: Colors.blue,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(
                        Icons.location_on,
                        size: 12,
                        color: Colors.grey,
                      ),
                      TranslationWidget(
                        text: ad.location,
                        to: _currentLanguage,
                        style: const TextStyle(
                          fontSize: 12,
                          color: Colors.grey,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBottomNavigationBar() {
    final menuItems = [
      'Home',
      'Search',
      'Post',
      'Messages',
      'Profile',
    ];

    return BottomNavigationBar(
      items: menuItems.map((item) {
        return BottomNavigationBarItem(
          icon: _getIconForItem(item),
          label: item,
        );
      }).toList(),
      currentIndex: _selectedIndex,
      selectedItemColor: Colors.blue,
      onTap: (index) {
        setState(() {
          _selectedIndex = index;
        });
        // TODO: Navigate to selected screen
      },
    );
  }

  Widget _getIconForItem(String item) {
    switch (item) {
      case 'Home':
        return const Icon(Icons.home);
      case 'Search':
        return const Icon(Icons.search);
      case 'Post':
        return const Icon(Icons.add_circle);
      case 'Messages':
        return const Icon(Icons.message);
      case 'Profile':
        return const Icon(Icons.person);
      default:
        return const Icon(Icons.home);
    }
  }

  void _showLanguageSelector() {
    final languages = _translationService.getSupportedLanguages();
    
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Select Language'),
          content: SizedBox(
            width: double.maxFinite,
            child: ListView.builder(
              shrinkWrap: true,
              itemCount: languages.length,
              itemBuilder: (context, index) {
                final entry = languages.entries.elementAt(index);
                final code = entry.key;
                final name = entry.value;
                
                return RadioListTile<String>(
                  title: Text(name),
                  value: code,
                  groupValue: _currentLanguage,
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        _currentLanguage = value;
                      });
                      Navigator.of(context).pop();
                    }
                  },
                );
              },
            ),
          ),
        );
      },
    );
  }
}