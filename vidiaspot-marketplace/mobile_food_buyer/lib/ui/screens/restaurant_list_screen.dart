import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../ui/providers/food_buyer_provider.dart';
import '../models/restaurant.dart';

class RestaurantListScreen extends StatefulWidget {
  @override
  _RestaurantListScreenState createState() => _RestaurantListScreenState();
}

class _RestaurantListScreenState extends State<RestaurantListScreen> {
  String _selectedSortOption = 'distance'; // distance, rating, delivery_time
  bool _showOpenOnly = true;

  @override
  Widget build(BuildContext context) {
    final foodBuyerProvider = Provider.of<FoodBuyerProvider>(context);
    final restaurants = foodBuyerProvider.restaurants;

    return Scaffold(
      appBar: AppBar(
        title: Text('Restaurants'),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.filter_list),
            onPressed: () {
              _showFilterDialog();
            },
          ),
        ],
      ),
      body: foodBuyerProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : restaurants.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.restaurant,
                        size: 80,
                        color: Colors.grey[400],
                      ),
                      SizedBox(height: 20),
                      Text(
                        'No restaurants found',
                        style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                      ),
                      SizedBox(height: 10),
                      Text(
                        'Try adjusting your filters',
                        style: TextStyle(color: Colors.grey[500]),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: () async {
                    // Refresh restaurant list
                  },
                  child: CustomScrollView(
                    slivers: [
                      SliverToBoxAdapter(
                        child: _buildSortingAndFilteringOptions(),
                      ),
                      SliverList(
                        delegate: SliverChildBuilderDelegate(
                          (context, index) {
                            return _buildRestaurantCard(restaurants[index]);
                          },
                          childCount: restaurants.length,
                        ),
                      ),
                    ],
                  ),
                ),
    );
  }

  Widget _buildSortingAndFilteringOptions() {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      child: Row(
        children: [
          Expanded(
            child: Container(
              padding: EdgeInsets.symmetric(horizontal: 10),
              decoration: BoxDecoration(
                border: Border.all(color: Colors.grey[300]!),
                borderRadius: BorderRadius.circular(20),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  value: _selectedSortOption,
                  isExpanded: true,
                  items: [
                    DropdownMenuItem(
                      value: 'distance',
                      child: Row(
                        children: [
                          Icon(Icons.directions_walk, size: 16),
                          SizedBox(width: 8),
                          Text('Distance'),
                        ],
                      ),
                    ),
                    DropdownMenuItem(
                      value: 'rating',
                      child: Row(
                        children: [
                          Icon(Icons.star, size: 16, color: Colors.orange),
                          SizedBox(width: 8),
                          Text('Rating'),
                        ],
                      ),
                    ),
                    DropdownMenuItem(
                      value: 'delivery_time',
                      child: Row(
                        children: [
                          Icon(Icons.access_time, size: 16),
                          SizedBox(width: 8),
                          Text('Delivery Time'),
                        ],
                      ),
                    ),
                  ],
                  onChanged: (value) {
                    setState(() {
                      _selectedSortOption = value ?? 'distance';
                    });
                    // In a real app, you would sort the restaurants
                  },
                ),
              ),
            ),
          ),
          SizedBox(width: 10),
          FilterChip(
            label: Text('Open Only'),
            selected: _showOpenOnly,
            onSelected: (selected) {
              setState(() {
                _showOpenOnly = selected;
              });
              // In a real app, you would filter the restaurants
            },
          ),
        ],
      ),
    );
  }

  Widget _buildRestaurantCard(Restaurant restaurant) {
    bool isOpen = _checkRestaurantOpen(restaurant);
    
    return Card(
      margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: InkWell(
        onTap: () {
          // Navigate to restaurant menu
          _navigateToRestaurantMenu(restaurant);
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: EdgeInsets.all(12),
          child: Row(
            children: [
              // Restaurant image
              Container(
                width: 80,
                height: 80,
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
              
              // Restaurant details
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Text(
                            restaurant.name,
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        Container(
                          padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: isOpen ? Colors.green[100] : Colors.red[100],
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            isOpen ? 'Open' : 'Closed',
                            style: TextStyle(
                              color: isOpen ? Colors.green[800] : Colors.red[800],
                              fontSize: 12,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ],
                    ),
                    SizedBox(height: 5),
                    Row(
                      children: [
                        Icon(Icons.star, size: 14, color: Colors.orange),
                        SizedBox(width: 3),
                        Text(
                          '${restaurant.rating.toStringAsFixed(1)} • ${restaurant.numRatings} ratings',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                    SizedBox(height: 5),
                    Wrap(
                      spacing: 5,
                      children: restaurant.categories.map((category) {
                        return Chip(
                          label: Text(
                            category,
                            style: TextStyle(fontSize: 10),
                          ),
                          backgroundColor: Colors.red[50],
                          labelStyle: TextStyle(color: Colors.red[800]),
                        );
                      }).toList(),
                    ),
                    SizedBox(height: 5),
                    Row(
                      children: [
                        Icon(Icons.delivery_dining, size: 14, color: Colors.grey),
                        SizedBox(width: 5),
                        Text(
                          '${restaurant.deliveryTime} min • \$${restaurant.deliveryFee.toStringAsFixed(2)}',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                        SizedBox(width: 10),
                        Icon(Icons.place, size: 14, color: Colors.grey),
                        SizedBox(width: 5),
                        Text(
                          '${restaurant.distance.toStringAsFixed(1)} km',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
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

  bool _checkRestaurantOpen(Restaurant restaurant) {
    // In a real app, this would check the current time against the restaurant's operating hours
    // and return if the restaurant is currently open
    return true; // For now, assume the restaurant is open
  }

  void _navigateToRestaurantMenu(Restaurant restaurant) {
    // In a real app, this would navigate to the restaurant's menu screen
    print('Navigating to restaurant: ${restaurant.name}');
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Filters'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            CheckboxListTile(
              title: Text('Free Delivery'),
              value: false,
              onChanged: (value) {},
            ),
            CheckboxListTile(
              title: Text('Under \$20'),
              value: false,
              onChanged: (value) {},
            ),
            CheckboxListTile(
              title: Text('Rating 4+'),
              value: false,
              onChanged: (value) {},
            ),
            CheckboxListTile(
              title: Text('Offers'),
              value: false,
              onChanged: (value) {},
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              // Apply filters
            },
            child: Text('Apply'),
          ),
        ],
      ),
    );
  }
}