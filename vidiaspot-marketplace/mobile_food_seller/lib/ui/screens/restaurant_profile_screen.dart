import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../ui/providers/food_seller_provider.dart';
import '../models/restaurant_data.dart';

class RestaurantProfileScreen extends StatefulWidget {
  @override
  _RestaurantProfileScreenState createState() => _RestaurantProfileScreenState();
}

class _RestaurantProfileScreenState extends State<RestaurantProfileScreen> {
  @override
  Widget build(BuildContext context) {
    final foodSellerProvider = Provider.of<FoodSellerProvider>(context);
    final restaurantData = foodSellerProvider.restaurantData;

    return Scaffold(
      appBar: AppBar(
        title: Text('Restaurant Profile'),
        backgroundColor: Colors.orange[400],
        foregroundColor: Colors.white,
      ),
      body: foodSellerProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                // Refresh restaurant data
              },
              child: SingleChildScrollView(
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    children: [
                      // Restaurant banner
                      Container(
                        height: 200,
                        width: double.infinity,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          color: Colors.grey[300],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(12),
                          child: restaurantData?.bannerImage.isNotEmpty == true
                              ? Image.network(
                                  restaurantData!.bannerImage,
                                  fit: BoxFit.cover,
                                  errorBuilder: (context, error, stackTrace) {
                                    return Icon(
                                      Icons.restaurant,
                                      size: 80,
                                      color: Colors.grey[600],
                                    );
                                  },
                                )
                              : Icon(
                                  Icons.restaurant,
                                  size: 80,
                                  color: Colors.grey[600],
                                ),
                        ),
                      ),
                      SizedBox(height: 20),
                      
                      // Restaurant logo and name
                      Row(
                        children: [
                          Container(
                            width: 100,
                            height: 100,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: Colors.grey[300],
                              border: Border.all(
                                color: Colors.orange[400]!,
                                width: 3,
                              ),
                            ),
                            child: ClipOval(
                              child: restaurantData?.logoUrl.isNotEmpty == true
                                  ? Image.network(
                                      restaurantData!.logoUrl,
                                      fit: BoxFit.cover,
                                      errorBuilder: (context, error, stackTrace) {
                                        return Icon(
                                          Icons.restaurant,
                                          size: 50,
                                          color: Colors.grey[600],
                                        );
                                      },
                                    )
                                  : Icon(
                                      Icons.restaurant,
                                      size: 50,
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
                                  restaurantData?.name ?? 'Restaurant Name',
                                  style: TextStyle(
                                    fontSize: 24,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                SizedBox(height: 5),
                                Row(
                                  children: [
                                    Icon(
                                      Icons.star,
                                      color: Colors.orange,
                                      size: 18,
                                    ),
                                    SizedBox(width: 5),
                                    Text(
                                      '${restaurantData?.rating?.toStringAsFixed(1) ?? '0.0'} (${restaurantData?.numRatings ?? 0} ratings)',
                                      style: TextStyle(color: Colors.grey[700]),
                                    ),
                                  ],
                                ),
                                SizedBox(height: 5),
                                Wrap(
                                  spacing: 8,
                                  children: restaurantData?.categories.map((category) {
                                    return Chip(
                                      label: Text(
                                        category,
                                        style: TextStyle(fontSize: 12),
                                      ),
                                      backgroundColor: Colors.orange[100],
                                    );
                                  }).toList() ?? [],
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                      SizedBox(height: 20),
                      
                      // Basic info section
                      Card(
                        elevation: 4,
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Basic Information',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              SizedBox(height: 10),
                              _buildInfoRow('Owner', restaurantData?.ownerName ?? 'N/A'),
                              _buildInfoRow('Email', restaurantData?.email ?? 'N/A'),
                              _buildInfoRow('Phone', restaurantData?.phone ?? 'N/A'),
                              _buildInfoRow('Address', restaurantData?.address ?? 'N/A'),
                              _buildInfoRow('Currency', restaurantData?.currency ?? 'USD'),
                              _buildInfoRow(
                                'Delivery Radius',
                                '${restaurantData?.deliveryRadius ?? 'N/A'} km',
                              ),
                              SizedBox(height: 10),
                              SwitchListTile(
                                title: Text('Active Status'),
                                value: restaurantData?.isActive ?? false,
                                onChanged: (value) {
                                  // Update restaurant active status
                                  _updateRestaurantStatus(value);
                                },
                              ),
                              SwitchListTile(
                                title: Text('Accepts Orders'),
                                value: restaurantData?.acceptsOrders ?? true,
                                onChanged: (value) {
                                  // Update restaurant accepts orders status
                                  _updateAcceptsOrders(value);
                                },
                              ),
                            ],
                          ),
                        ),
                      ),
                      SizedBox(height: 20),
                      
                      // Operating hours
                      Card(
                        elevation: 4,
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Operating Hours',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              SizedBox(height: 10),
                              ...restaurantData?.operatingHours.map((hours) => 
                                Padding(
                                  padding: EdgeInsets.symmetric(vertical: 4),
                                  child: Text(
                                    hours,
                                    style: TextStyle(color: Colors.grey[700]),
                                  ),
                                ),
                              ).toList() ?? [],
                              SizedBox(height: 10),
                              ElevatedButton(
                                onPressed: () {
                                  _editOperatingHours(context);
                                },
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.orange[400],
                                  padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                                ),
                                child: Text(
                                  'Edit Hours',
                                  style: TextStyle(color: Colors.white),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      SizedBox(height: 20),
                      
                      // Description
                      Card(
                        elevation: 4,
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Description',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              SizedBox(height: 10),
                              Text(
                                restaurantData?.description ?? 'Add restaurant description',
                                style: TextStyle(
                                  fontSize: 14,
                                  color: Colors.grey[700],
                                ),
                              ),
                              if (restaurantData?.description == null || restaurantData!.description.isEmpty) ...[
                                SizedBox(height: 10),
                                ElevatedButton(
                                  onPressed: () {
                                    _editDescription(context);
                                  },
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: Colors.orange[400],
                                    padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                                  ),
                                  child: Text(
                                    'Add Description',
                                    style: TextStyle(color: Colors.white),
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(
              '$label: ',
              style: TextStyle(
                fontWeight: FontWeight.w500,
                color: Colors.grey[700],
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: TextStyle(
                color: Colors.grey[800],
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _updateRestaurantStatus(bool status) {
    // In a real app, this would update the restaurant status on the server
    print('Restaurant active status updated to: $status');
  }

  void _updateAcceptsOrders(bool acceptsOrders) {
    // In a real app, this would update the accepts orders setting on the server
    print('Accepts orders updated to: $acceptsOrders');
  }

  void _editOperatingHours(BuildContext context) {
    // In a real app, this would show an operating hours editing screen
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Edit Operating Hours'),
          content: Text('Implement operating hours editing functionality'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('OK'),
            ),
          ],
        );
      },
    );
  }

  void _editDescription(BuildContext context) {
    // In a real app, this would show a description editing screen
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Edit Description'),
          content: Text('Implement description editing functionality'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('OK'),
            ),
          ],
        );
      },
    );
  }
}