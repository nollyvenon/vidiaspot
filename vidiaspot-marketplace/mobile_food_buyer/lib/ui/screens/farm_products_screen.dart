import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/food_buyer_provider.dart';
import '../models/farmer_product.dart';
import '../services/api_service.dart';
import '../services/location_service.dart';

class FarmProductsScreen extends StatefulWidget {
  @override
  _FarmProductsScreenState createState() => _FarmProductsScreenState();
}

class _FarmProductsScreenState extends State<FarmProductsScreen> {
  late FoodBuyerProvider _provider;
  late ApiService _apiService;
  String _token = ''; // In a real app, this would come from authentication
  bool _isLoading = true;
  List<FarmerProduct> _filteredProducts = [];
  String _searchQuery = '';
  bool _useProximitySearch = false;
  double _currentLat = 0.0;
  double _currentLng = 0.0;
  double _searchRadius = 50.0; // Default to 50km radius

  @override
  void initState() {
    super.initState();
    _loadFarmProducts();
  }

  void _loadFarmProducts() async {
    setState(() {
      _isLoading = true;
    });

    _provider = Provider.of<FoodBuyerProvider>(context, listen: false);
    _apiService = Provider.of<ApiService>(context, listen: false);

    // Simulate a token - in a real app, this would come from auth service
    _token = 'sample_token';

    List<FarmerProduct>? products;

    if (_useProximitySearch) {
      // Get user's current location
      final locationData = await LocationService.getCurrentLocation();
      if (locationData != null) {
        _currentLat = locationData.latitude!;
        _currentLng = locationData.longitude!;

        // Load nearby farm products
        products = await _apiService.getNearbyFarmProducts(
          _token,
          _currentLat,
          _currentLng,
          radius: _searchRadius,
        );
      } else {
        // Fallback to regular search if location access denied
        products = await _apiService.getFarmProducts(_token);
        _useProximitySearch = false;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Location access denied. Showing all products.'),
            backgroundColor: Colors.orange,
          ),
        );
      }
    } else {
      // Load all farm products
      products = await _apiService.getFarmProducts(_token);
    }

    if (products != null) {
      _provider.setFarmProducts(products);
      _filteredProducts = products;
    }

    setState(() {
      _isLoading = false;
    });
  }

  void _toggleProximitySearch() {
    setState(() {
      _useProximitySearch = !_useProximitySearch;
    });
    _loadFarmProducts();
  }

  void _updateSearchRadius(double value) {
    setState(() {
      _searchRadius = value;
    });
    // Reload products with new radius if proximity search is enabled
    if (_useProximitySearch) {
      _loadFarmProducts();
    }
  }

  void _searchProducts(String query) {
    setState(() {
      _searchQuery = query;
      if (query.isEmpty) {
        _filteredProducts = _provider.farmProducts;
      } else {
        _filteredProducts = _provider.farmProducts
            .where((product) =>
                product.title.toLowerCase().contains(query.toLowerCase()) ||
                product.description.toLowerCase().contains(query.toLowerCase()) ||
                (product.farmName?.toLowerCase().contains(query.toLowerCase()) ?? false))
            .toList();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Farm Products'),
        centerTitle: true,
        actions: [
          IconButton(
            icon: Icon(Icons.search),
            onPressed: () {
              showSearch(
                context: context,
                delegate: _FarmProductSearchDelegate(
                  products: _provider.farmProducts,
                  onSearch: _searchProducts,
                ),
              );
            },
          ),
        ],
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Proximity search toggle and radius control
                Container(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: ElevatedButton.icon(
                              onPressed: _toggleProximitySearch,
                              icon: Icon(
                                _useProximitySearch ? Icons.location_off : Icons.location_on,
                                color: _useProximitySearch ? Colors.red : Colors.white,
                              ),
                              label: Text(_useProximitySearch ? 'Disable Proximity' : 'Enable Proximity'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: _useProximitySearch ? Colors.red[400] : Colors.green[600],
                                foregroundColor: Colors.white,
                              ),
                            ),
                          ),
                          SizedBox(width: 10),
                          if (_useProximitySearch)
                            Expanded(
                              child: Container(
                                padding: EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: Colors.grey[200],
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Row(
                                  children: [
                                    Icon(Icons.location_pin, size: 16, color: Colors.grey[600]),
                                    SizedBox(width: 4),
                                    Expanded(
                                      child: Text(
                                        '${_currentLat.toStringAsFixed(4)}, ${_currentLng.toStringAsFixed(4)}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[700],
                                        ),
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                        ],
                      ),

                      if (_useProximitySearch)
                      Column(
                        children: [
                          SizedBox(height: 10),
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  'Search Radius: ${_searchRadius.toStringAsFixed(0)} km',
                                  style: TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          Slider(
                            value: _searchRadius,
                            min: 1.0,
                            max: 100.0,
                            divisions: 99,
                            label: '${_searchRadius.round()} km',
                            onChanged: _updateSearchRadius,
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                // Filter options
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Row(
                    children: [
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: 'all',
                          decoration: InputDecoration(
                            labelText: 'Organic Filter',
                            border: OutlineInputBorder(),
                          ),
                          items: [
                            DropdownMenuItem(value: 'all', child: Text('All Products')),
                            DropdownMenuItem(value: 'organic', child: Text('Organic Only')),
                            DropdownMenuItem(value: 'non_organic', child: Text('Non-Organic')),
                          ],
                          onChanged: (value) {
                            setState(() {
                              if (value == 'all') {
                                _filteredProducts = _provider.farmProducts;
                              } else if (value == 'organic') {
                                _filteredProducts = _provider.farmProducts
                                    .where((product) => product.isOrganic)
                                    .toList();
                              } else if (value == 'non_organic') {
                                _filteredProducts = _provider.farmProducts
                                    .where((product) => !product.isOrganic)
                                    .toList();
                              }
                            });
                          },
                        ),
                      ),
                      SizedBox(width: 10),
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: 'all',
                          decoration: InputDecoration(
                            labelText: 'Season',
                            border: OutlineInputBorder(),
                          ),
                          items: [
                            DropdownMenuItem(value: 'all', child: Text('All Seasons')),
                            DropdownMenuItem(value: 'summer', child: Text('Summer')),
                            DropdownMenuItem(value: 'winter', child: Text('Winter')),
                            DropdownMenuItem(value: 'spring', child: Text('Spring')),
                            DropdownMenuItem(value: 'fall', child: Text('Fall')),
                          ],
                          onChanged: (value) {
                            setState(() {
                              if (value == 'all') {
                                _filteredProducts = _provider.farmProducts;
                              } else {
                                _filteredProducts = _provider.farmProducts
                                    .where((product) =>
                                        product.harvestSeason?.toLowerCase() == value?.toLowerCase())
                                    .toList();
                              }
                            });
                          },
                        ),
                      ),
                    ],
                  ),
                ),
                Expanded(
                  child: _filteredProducts.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.local_florist,
                                size: 80,
                                color: Colors.grey[300],
                              ),
                              SizedBox(height: 16),
                              Text(
                                'No farm products found',
                                style: TextStyle(
                                  fontSize: 18,
                                  color: Colors.grey[600],
                                ),
                              ),
                              SizedBox(height: 8),
                              Text(
                                'Check back later for fresh farm products!',
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  color: Colors.grey[500],
                                ),
                              ),
                            ],
                          ),
                        )
                      : ListView.builder(
                          itemCount: _filteredProducts.length,
                          itemBuilder: (context, index) {
                            final product = _filteredProducts[index];
                            return _buildProductCard(product);
                          },
                        ),
                ),
              ],
            ),
    );
  }

  Widget _buildProductCard(FarmerProduct product) {
    return Card(
      margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Padding(
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
                    borderRadius: BorderRadius.circular(8),
                    color: Colors.grey[200],
                  ),
                  child: product.images.isNotEmpty
                      ? ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: Image.network(
                            product.images[0]['image_url'] ?? '',
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) {
                              return Icon(Icons.image_not_supported, color: Colors.grey);
                            },
                          ),
                        )
                      : Icon(Icons.image, color: Colors.grey),
                ),
                SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        product.title,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      SizedBox(height: 4),
                      Text(
                        product.farmName ?? 'Farm Name Unknown',
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.green[700],
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      SizedBox(height: 4),
                      Row(
                        children: [
                          Icon(
                            Icons.eco,
                            size: 16,
                            color: product.isOrganic ? Colors.green : Colors.grey,
                          ),
                          SizedBox(width: 4),
                          Text(
                            product.isOrganic ? 'Organic' : 'Conventional',
                            style: TextStyle(
                              fontSize: 12,
                              color: product.isOrganic ? Colors.green[700] : Colors.grey,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
            SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${product.currency} ${product.price.toStringAsFixed(2)}',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Colors.green[700],
                  ),
                ),
                if (product.harvestDate != null)
                  Text(
                    'Harvested: ${product.harvestDate!.day}/${product.harvestDate!.month}/${product.harvestDate!.year}',
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.grey[600],
                    ),
                  ),
              ],
            ),
            SizedBox(height: 8),
            Text(
              product.description,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[700],
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            SizedBox(height: 12),
            Row(
              children: [
                Icon(Icons.location_on, size: 16, color: Colors.red),
                SizedBox(width: 4),
                Text(
                  product.farmLocation ?? 'Location Not Specified',
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                  ),
                ),
              ],
            ),
            SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () {
                      // Navigate to product details page
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => FarmProductDetailScreen(product: product),
                        ),
                      );
                    },
                    icon: Icon(Icons.info),
                    label: Text('View Details'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green[600],
                    ),
                  ),
                ),
                SizedBox(width: 10),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () {
                      // Add to cart functionality would go here
                      _provider.addToCart(MenuItem(
                        id: product.id,
                        name: product.title,
                        description: product.description,
                        price: product.price,
                        image: product.images.isNotEmpty ? product.images[0]['image_url'] ?? '' : '',
                        category: product.category?['name'] ?? '',
                        isAvailable: true,
                        cartQuantity: 1,
                      ));
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Added to cart'),
                          backgroundColor: Colors.green[600],
                        ),
                      );
                    },
                    icon: Icon(Icons.add_shopping_cart),
                    label: Text('Add to Cart'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.blue[600],
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _FarmProductSearchDelegate extends SearchDelegate<String> {
  final List<FarmerProduct> products;
  final Function(String) onSearch;

  _FarmProductSearchDelegate({required this.products, required this.onSearch});

  @override
  List<Widget> buildActions(BuildContext context) {
    return [
      IconButton(
        icon: Icon(Icons.clear),
        onPressed: () {
          query = '';
        },
      ),
    ];
  }

  @override
  Widget buildLeading(BuildContext context) {
    return IconButton(
      icon: Icon(Icons.arrow_back),
      onPressed: () {
        close(context, '');
      },
    );
  }

  @override
  Widget buildResults(BuildContext context) {
    final filteredProducts = products
        .where((product) =>
            product.title.toLowerCase().contains(query.toLowerCase()) ||
            product.description.toLowerCase().contains(query.toLowerCase()) ||
            (product.farmName?.toLowerCase().contains(query.toLowerCase()) ?? false))
        .toList();

    return ListView.builder(
      itemCount: filteredProducts.length,
      itemBuilder: (context, index) {
        final product = filteredProducts[index];
        return ListTile(
          leading: Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(8),
              color: Colors.grey[200],
            ),
            child: product.images.isNotEmpty
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(
                      product.images[0]['image_url'] ?? '',
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return Icon(Icons.image_not_supported, color: Colors.grey);
                      },
                    ),
                  )
                : Icon(Icons.image, color: Colors.grey),
          ),
          title: Text(product.title),
          subtitle: Text(product.farmName ?? 'Farm Name Unknown'),
          onTap: () {
            close(context, product.title);
          },
        );
      },
    );
  }

  @override
  Widget buildSuggestions(BuildContext context) {
    final filteredProducts = products
        .where((product) =>
            product.title.toLowerCase().contains(query.toLowerCase()) ||
            product.description.toLowerCase().contains(query.toLowerCase()) ||
            (product.farmName?.toLowerCase().contains(query.toLowerCase()) ?? false))
        .toList();

    return ListView.builder(
      itemCount: filteredProducts.length,
      itemBuilder: (context, index) {
        final product = filteredProducts[index];
        return ListTile(
          leading: Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(8),
              color: Colors.grey[200],
            ),
            child: product.images.isNotEmpty
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(
                      product.images[0]['image_url'] ?? '',
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return Icon(Icons.image_not_supported, color: Colors.grey);
                      },
                    ),
                  )
                : Icon(Icons.image, color: Colors.grey),
          ),
          title: Text(product.title),
          subtitle: Text(product.farmName ?? 'Farm Name Unknown'),
          onTap: () {
            query = product.title;
            showResults(context);
          },
        );
      },
    );
  }
}

// Farm Product Detail Screen
class FarmProductDetailScreen extends StatelessWidget {
  final FarmerProduct product;

  const FarmProductDetailScreen({Key? key, required this.product}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(product.title),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Product image carousel
            SizedBox(
              height: 250,
              child: PageView.builder(
                itemCount: product.images.length,
                itemBuilder: (context, index) {
                  final imageUrl = product.images[index]['image_url'] ?? '';
                  return Container(
                    margin: EdgeInsets.symmetric(horizontal: 8),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(8),
                      color: Colors.grey[200],
                    ),
                    child: imageUrl.isNotEmpty
                        ? ClipRRect(
                            borderRadius: BorderRadius.circular(8),
                            child: Image.network(
                              imageUrl,
                              fit: BoxFit.cover,
                              errorBuilder: (context, error, stackTrace) {
                                return Icon(Icons.image_not_supported, color: Colors.grey);
                              },
                            ),
                          )
                        : Icon(Icons.image, color: Colors.grey),
                  );
                },
              ),
            ),
            Padding(
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Product title and price
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          product.title,
                          style: TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      Text(
                        '${product.currency} ${product.price.toStringAsFixed(2)}',
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: Colors.green[700],
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 8),
                  
                  // Farm information
                  Card(
                    child: Padding(
                      padding: EdgeInsets.all(12),
                      child: Row(
                        children: [
                          Icon(Icons.agriculture, color: Colors.green),
                          SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  product.farmName ?? 'Farm Name Unknown',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                                SizedBox(height: 4),
                                Row(
                                  children: [
                                    Icon(Icons.eco, size: 16, color: product.isOrganic ? Colors.green : Colors.grey),
                                    SizedBox(width: 4),
                                    Text(
                                      product.isOrganic ? 'Organic Product' : 'Conventional Product',
                                      style: TextStyle(
                                        fontSize: 14,
                                        color: product.isOrganic ? Colors.green[700] : Colors.grey[600],
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
                  
                  SizedBox(height: 16),
                  
                  // Harvest and seasonal information
                  if (product.harvestDate != null || product.harvestSeason != null)
                    Card(
                      child: Padding(
                        padding: EdgeInsets.all(12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Harvest Information',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 8),
                            if (product.harvestDate != null)
                              Row(
                                children: [
                                  Icon(Icons.event, size: 16, color: Colors.grey),
                                  SizedBox(width: 8),
                                  Text('Harvested: ${product.harvestDate!.day}/${product.harvestDate!.month}/${product.harvestDate!.year}'),
                                ],
                              ),
                            if (product.harvestSeason != null)
                              Row(
                                children: [
                                  Icon(Icons.snowing, size: 16, color: Colors.grey),
                                  SizedBox(width: 8),
                                  Text('Season: ${product.harvestSeason}'),
                                ],
                              ),
                          ],
                        ),
                      ),
                    ),
                  
                  SizedBox(height: 16),
                  
                  // Location information
                  Card(
                    child: Padding(
                      padding: EdgeInsets.all(12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Farm Location',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          SizedBox(height: 8),
                          Row(
                            children: [
                              Icon(Icons.location_on, size: 16, color: Colors.red),
                              SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  product.farmLocation ?? 'Location not specified',
                                  style: TextStyle(fontSize: 14),
                                ),
                              ),
                            ],
                          ),
                          if (product.farmLatitude != null && product.farmLongitude != null)
                            Padding(
                              padding: EdgeInsets.only(top: 8),
                              child: Container(
                                height: 120,
                                decoration: BoxDecoration(
                                  border: Border.all(color: Colors.grey),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(8),
                                  child: Image.network(
                                    'https://maps.googleapis.com/maps/api/staticmap?center=${product.farmLatitude},${product.farmLongitude}&zoom=14&size=400x120&key=YOUR_MAPS_API_KEY',
                                    fit: BoxFit.cover,
                                    errorBuilder: (context, error, stackTrace) {
                                      return Center(child: Text('Map not available'));
                                    },
                                  ),
                                ),
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
                  
                  SizedBox(height: 16),
                  
                  // Description
                  Text(
                    'Description',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  SizedBox(height: 8),
                  Text(
                    product.description,
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey[700],
                      height: 1.5,
                    ),
                  ),
                  
                  SizedBox(height: 24),
                  
                  // Add to cart button
                  ElevatedButton.icon(
                    onPressed: () {
                      // Add to cart functionality
                      // In a real app, you'd add this to the cart
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Added to cart'),
                          backgroundColor: Colors.green[600],
                        ),
                      );
                    },
                    icon: Icon(Icons.add_shopping_cart),
                    label: Text('Add to Cart'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green[600],
                      padding: EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}