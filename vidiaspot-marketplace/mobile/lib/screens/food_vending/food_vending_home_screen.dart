// lib/screens/food_vending/food_vending_home_screen.dart
import 'package:flutter/material.dart';

class FoodVendingHomeScreen extends StatefulWidget {
  const FoodVendingHomeScreen({Key? key}) : super(key: key);

  @override
  _FoodVendingHomeScreenState createState() => _FoodVendingHomeScreenState();
}

class _FoodVendingHomeScreenState extends State<FoodVendingHomeScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Food Vending & Delivery'),
        backgroundColor: Colors.red,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Welcome message
              const Text(
                'Discover Amazing Food Near You',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),
              const Text(
                'Order from your favorite restaurants and get it delivered fast',
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey,
                ),
              ),
              const SizedBox(height: 20),

              // Search bar
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10),
                decoration: BoxDecoration(
                  color: Colors.grey[100],
                  borderRadius: BorderRadius.circular(10),
                ),
                child: TextField(
                  decoration: InputDecoration(
                    hintText: 'Search for restaurants or dishes...',
                    prefixIcon: const Icon(Icons.search),
                    border: InputBorder.none,
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Quick filters
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildFilterChip('Fast Food', true),
                _buildFilterChip('Healthy', false),
                _buildFilterChip('Vegan', false),
              ],
              ),
              const SizedBox(height: 20),

              // Restaurant categories
              const Text(
                'Categories',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),
              _buildCategories(),

              const SizedBox(height: 20),

              // Popular restaurants
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Popular Restaurants',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  TextButton(
                    onPressed: () {
                      // Navigate to all restaurants
                    },
                    child: const Text('View All'),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              _buildRestaurantList(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildFilterChip(String label, bool isSelected) {
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      selectedColor: Colors.red[100],
      checkmarkColor: Colors.red,
      onSelected: (bool selected) {
        // Implement filter selection
      },
    );
  }

  Widget _buildCategories() {
    return Container(
      height: 100,
      child: ListView(
        scrollDirection: Axis.horizontal,
        children: [
          _buildCategoryCard('Pizza', Icons.local_pizza, Colors.orange),
          _buildCategoryCard('Burgers', Icons.restaurant, Colors.red),
          _buildCategoryCard('Sushi', Icons.food_bank, Colors.blue),
          _buildCategoryCard('Desserts', Icons.icecream, Colors.pink),
          _buildCategoryCard('Drinks', Icons.local_drink, Colors.green),
        ],
      ),
    );
  }

  Widget _buildCategoryCard(String name, IconData icon, Color color) {
    return Container(
      width: 100,
      margin: const EdgeInsets.only(right: 10),
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 30),
          const SizedBox(height: 5),
          Text(
            name,
            style: TextStyle(
              color: color,
              fontWeight: FontWeight.w500,
              fontSize: 12,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildRestaurantList() {
    return Column(
      children: [
        _buildRestaurantCard(
          'Pizza Paradise',
          'Italian • Fast food • \$\$',
          4.8,
          'https://via.placeholder.com/150',
        ),
        const SizedBox(height: 10),
        _buildRestaurantCard(
          'Burger Barn',
          'American • Fast food • \$\$',
          4.7,
          'https://via.placeholder.com/150',
        ),
        const SizedBox(height: 10),
        _buildRestaurantCard(
          'Sushi World',
          'Japanese • Seafood • \$\$\$',
          4.9,
          'https://via.placeholder.com/150',
        ),
      ],
    );
  }

  Widget _buildRestaurantCard(String name, String description, double rating, String imageUrl) {
    return Card(
      child: Container(
        padding: const EdgeInsets.all(10),
        child: Row(
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(8),
                image: DecorationImage(
                  image: NetworkImage(imageUrl),
                  fit: BoxFit.cover,
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    name,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Text(
                    description,
                    style: const TextStyle(
                      fontSize: 12,
                      color: Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Row(
                    children: [
                      const Icon(
                        Icons.star,
                        color: Colors.amber,
                        size: 16,
                      ),
                      Text(
                        rating.toString(),
                        style: const TextStyle(
                          fontSize: 12,
                        ),
                      ),
                      const SizedBox(width: 5),
                      const Text(
                        '(120 orders)',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios, size: 16),
          ],
        ),
      ),
    );
  }
}