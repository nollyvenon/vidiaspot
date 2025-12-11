import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../ui/providers/food_buyer_provider.dart';
import '../models/restaurant.dart';

class SearchScreen extends StatefulWidget {
  @override
  _SearchScreenState createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final TextEditingController _searchController = TextEditingController();
  String _currentQuery = '';
  List<Restaurant> _searchResults = [];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: TextField(
          controller: _searchController,
          decoration: InputDecoration(
            hintText: 'Search for restaurants or dishes...',
            border: InputBorder.none,
          ),
          onChanged: _performSearch,
          onSubmitted: _performSearch,
        ),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.mic),
            onPressed: () {
              // TODO: Implement voice search
            },
          ),
        ],
      ),
      body: _currentQuery.isEmpty
          ? _buildSearchSuggestions()
          : _buildSearchResults(),
    );
  }

  Widget _buildSearchSuggestions() {
    return SingleChildScrollView(
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Trending searches
            Text(
              'Trending Searches',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            Wrap(
              spacing: 8,
              children: [
                _buildSuggestionChip('Pizza'),
                _buildSuggestionChip('Burger'),
                _buildSuggestionChip('Sushi'),
                _buildSuggestionChip('Chicken'),
                _buildSuggestionChip('Healthy'),
                _buildSuggestionChip('Vegan'),
                _buildSuggestionChip('Breakfast'),
                _buildSuggestionChip('Dessert'),
              ],
            ),
            SizedBox(height: 20),
            
            // Popular categories
            Text(
              'Popular Categories',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 15),
            GridView.count(
              crossAxisCount: 2,
              crossAxisSpacing: 15,
              mainAxisSpacing: 15,
              shrinkWrap: true,
              physics: NeverScrollableScrollPhysics(),
              children: [
                _buildCategoryItem('Pizza', Icons.local_pizza, Colors.orange[300]!),
                _buildCategoryItem('Burgers', Icons.restaurant, Colors.red[300]!),
                _buildCategoryItem('Asian', Icons.ramen_dining, Colors.green[300]!),
                _buildCategoryItem('Healthy', Icons.eco, Colors.lightGreen[300]!),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSuggestionChip(String suggestion) {
    return ActionChip(
      label: Text(suggestion),
      backgroundColor: Colors.grey[200],
      onPressed: () {
        _searchController.text = suggestion;
        _performSearch(suggestion);
      },
    );
  }

  Widget _buildCategoryItem(String name, IconData icon, Color color) {
    return Container(
      decoration: BoxDecoration(
        color: color,
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        onTap: () {
          _searchController.text = name;
          _performSearch(name);
        },
        borderRadius: BorderRadius.circular(12),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 40, color: Colors.white),
            SizedBox(height: 10),
            Text(
              name,
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSearchResults() {
    final foodBuyerProvider = Provider.of<FoodBuyerProvider>(context);
    // In a real app, these would come from the API
    final restaurants = foodBuyerProvider.restaurants;

    return Column(
      children: [
        // Search results header
        Container(
          padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                '${restaurants.length} results for "$_currentQuery"',
                style: TextStyle(
                  fontSize: 16,
                  color: Colors.grey[600],
                ),
              ),
              TextButton(
                onPressed: () {
                  _searchController.clear();
                  setState(() {
                    _currentQuery = '';
                  });
                },
                child: Text('Clear'),
              ),
            ],
          ),
        ),
        
        // Results list
        Expanded(
          child: restaurants.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.search,
                        size: 80,
                        color: Colors.grey[400],
                      ),
                      SizedBox(height: 20),
                      Text(
                        'No results found',
                        style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                      ),
                      SizedBox(height: 10),
                      Text(
                        'Try a different search term',
                        style: TextStyle(color: Colors.grey[500]),
                      ),
                    ],
                  ),
                )
              : ListView.builder(
                  itemCount: restaurants.length,
                  itemBuilder: (context, index) {
                    return _buildRestaurantResultCard(restaurants[index]);
                  },
                ),
        ),
      ],
    );
  }

  Widget _buildRestaurantResultCard(Restaurant restaurant) {
    return Card(
      margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: InkWell(
        onTap: () {
          // Navigate to restaurant details
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => RestaurantDetailsScreen(restaurant: restaurant),
            ),
          );
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: EdgeInsets.all(12),
          child: Row(
            children: [
              // Restaurant image
              Container(
                width: 60,
                height: 60,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(8),
                  color: Colors.grey[300],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: restaurant.logoUrl.isNotEmpty
                      ? Image.network(
                          restaurant.logoUrl,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) {
                            return Icon(
                              Icons.restaurant,
                              size: 24,
                              color: Colors.grey[600],
                            );
                          },
                        )
                      : Icon(
                          Icons.restaurant,
                          size: 24,
                          color: Colors.grey[600],
                        ),
                ),
              ),
              SizedBox(width: 15),
              
              // Restaurant details
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      restaurant.name,
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    SizedBox(height: 5),
                    Row(
                      children: [
                        Icon(Icons.star, size: 14, color: Colors.orange),
                        SizedBox(width: 3),
                        Text(
                          '${restaurant.rating.toStringAsFixed(1)} • ${restaurant.distance.toStringAsFixed(1)} km',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                    SizedBox(height: 5),
                    Text(
                      restaurant.categories.join(' • '),
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _performSearch(String query) {
    if (query.trim().isEmpty) {
      setState(() {
        _currentQuery = '';
      });
      return;
    }

    setState(() {
      _currentQuery = query.trim();
    });

    // In a real app, this would call the API to search for restaurants
    // For now, we'll just use the provider's restaurant list
    final foodBuyerProvider = Provider.of<FoodBuyerProvider>(context, listen: false);
    final allRestaurants = foodBuyerProvider.restaurants;
    
    final results = allRestaurants.where((restaurant) {
      return restaurant.name.toLowerCase().contains(query.toLowerCase()) ||
             restaurant.categories.any((category) => category.toLowerCase().contains(query.toLowerCase()));
    }).toList();

    setState(() {
      _searchResults = results;
    });
  }
}

class RestaurantDetailsScreen extends StatelessWidget {
  final Restaurant restaurant;

  const RestaurantDetailsScreen({Key? key, required this.restaurant}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(restaurant.name),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Restaurant banner
            Container(
              height: 200,
              width: double.infinity,
              child: restaurant.bannerImage.isNotEmpty
                  ? Image.network(
                      restaurant.bannerImage,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return Container(
                          color: Colors.grey[300],
                          child: Icon(
                            Icons.restaurant,
                            size: 80,
                            color: Colors.grey[600],
                          ),
                        );
                      },
                    )
                  : Container(
                      color: Colors.grey[300],
                      child: Icon(
                        Icons.restaurant,
                        size: 80,
                        color: Colors.grey[600],
                      ),
                    ),
            ),
            
            Padding(
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        width: 80,
                        height: 80,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(10),
                          color: Colors.grey[300],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(10),
                          child: restaurant.logoUrl.isNotEmpty
                              ? Image.network(
                                  restaurant.logoUrl,
                                  fit: BoxFit.cover,
                                  errorBuilder: (context, error, stackTrace) {
                                    return Icon(
                                      Icons.restaurant,
                                      size: 40,
                                      color: Colors.grey[600],
                                    );
                                  },
                                )
                              : Icon(
                                  Icons.restaurant,
                                  size: 40,
                                  color: Colors.grey[600],
                                ),
                        ),
                      ),
                      SizedBox(width: 15),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              restaurant.name,
                              style: TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 5),
                            Row(
                              children: [
                                Icon(Icons.star, size: 16, color: Colors.orange),
                                SizedBox(width: 5),
                                Text(
                                  '${restaurant.rating.toStringAsFixed(1)} (${restaurant.numRatings} ratings)',
                                  style: TextStyle(color: Colors.grey[700]),
                                ),
                              ],
                            ),
                            SizedBox(height: 5),
                            Text(
                              '${restaurant.deliveryTime} min • \$$restaurant.deliveryFee delivery',
                              style: TextStyle(color: Colors.grey[700]),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  
                  SizedBox(height: 15),
                  
                  // Restaurant description
                  Text(
                    restaurant.description,
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey[700],
                      height: 1.5,
                    ),
                  ),
                  
                  SizedBox(height: 20),
                  
                  // Categories
                  Text(
                    'Categories',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  Wrap(
                    spacing: 8,
                    children: restaurant.categories.map((category) {
                      return Chip(
                        label: Text(category),
                        backgroundColor: Colors.red[100],
                        labelStyle: TextStyle(color: Colors.red[800]),
                      );
                    }).toList(),
                  ),
                  
                  SizedBox(height: 20),
                  
                  // Operating hours
                  Text(
                    'Operating Hours',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  ...restaurant.operatingHours.map((hours) => Padding(
                        padding: EdgeInsets.symmetric(vertical: 2),
                        child: Text(
                          hours,
                          style: TextStyle(color: Colors.grey[700]),
                        ),
                      )),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}