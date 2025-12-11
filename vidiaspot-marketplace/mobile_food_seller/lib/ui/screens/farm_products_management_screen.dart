import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/food_seller_provider.dart';
import '../models/farmer_product.dart';
import '../services/api_service.dart';

class FarmProductsManagementScreen extends StatefulWidget {
  @override
  _FarmProductsManagementScreenState createState() => _FarmProductsManagementScreenState();
}

class _FarmProductsManagementScreenState extends State<FarmProductsManagementScreen> {
  late FoodSellerProvider _provider;
  late ApiService _apiService;
  String _token = '';
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadFarmProducts();
  }

  void _loadFarmProducts() async {
    setState(() {
      _isLoading = true;
    });

    _provider = Provider.of<FoodSellerProvider>(context, listen: false);
    _apiService = Provider.of<ApiService>(context, listen: false);

    // Simulate a token - in a real app, this would come from auth service
    _token = 'sample_token';

    final products = await _apiService.getMyFarmProducts(_token);

    if (products != null) {
      _provider.setFarmProducts(products);
    }

    setState(() {
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Farm Products'),
        backgroundColor: Colors.green[600],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.add),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => AddEditFarmProductScreen(),
                ),
              ).then((_) {
                _loadFarmProducts(); // Refresh after adding
              });
            },
          ),
        ],
      ),
      body: _provider.farmProducts.isEmpty
          ? _buildEmptyState()
          : _buildProductsList(),
    );
  }

  Widget _buildEmptyState() {
    return Center(
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
            'No Farm Products Yet',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Colors.grey[700],
            ),
          ),
          SizedBox(height: 8),
          Text(
            'Add your first farm products to start selling',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.grey[500],
            ),
          ),
          SizedBox(height: 20),
          ElevatedButton(
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => AddEditFarmProductScreen(),
                ),
              ).then((_) {
                _loadFarmProducts(); // Refresh after adding
              });
            },
            child: Text('Add Farm Product'),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.green[600],
              foregroundColor: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildProductsList() {
    return RefreshIndicator(
      onRefresh: _loadFarmProducts,
      child: ListView.builder(
        itemCount: _provider.farmProducts.length,
        itemBuilder: (context, index) {
          final product = _provider.farmProducts[index];
          return Card(
            margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: ListTile(
              contentPadding: EdgeInsets.all(16),
              leading: Container(
                width: 60,
                height: 60,
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
              title: Text(
                product.title,
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
              subtitle: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  SizedBox(height: 4),
                  Text(
                    '${product.currency} ${product.price.toStringAsFixed(2)}',
                    style: TextStyle(
                      color: Colors.green[700],
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                  SizedBox(height: 4),
                  Text(
                    product.farmName ?? 'Farm Name Unknown',
                    style: TextStyle(
                      color: Colors.green[600],
                    ),
                  ),
                  SizedBox(height: 4),
                  Row(
                    children: [
                      Icon(
                        Icons.eco,
                        size: 14,
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
              trailing: PopupMenuButton(
                onSelected: (value) {
                  if (value == 'edit') {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => AddEditFarmProductScreen(
                          existingProduct: product,
                        ),
                      ),
                    ).then((_) {
                      _loadFarmProducts(); // Refresh after editing
                    });
                  } else if (value == 'delete') {
                    _confirmDelete(product);
                  }
                },
                itemBuilder: (context) {
                  return [
                    PopupMenuItem(
                      value: 'edit',
                      child: Row(
                        children: [
                          Icon(Icons.edit, size: 16),
                          SizedBox(width: 8),
                          Text('Edit'),
                        ],
                      ),
                    ),
                    PopupMenuItem(
                      value: 'delete',
                      child: Row(
                        children: [
                          Icon(Icons.delete, size: 16, color: Colors.red),
                          SizedBox(width: 8),
                          Text('Delete', style: TextStyle(color: Colors.red)),
                        ],
                      ),
                    ),
                  ];
                },
              ),
            ),
          );
        },
      ),
    );
  }

  void _confirmDelete(FarmerProduct product) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Delete Product'),
          content: Text('Are you sure you want to delete "${product.title}"? This action cannot be undone.'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('Cancel'),
            ),
            TextButton(
              onPressed: () async {
                Navigator.pop(context);
                final success = await _apiService.deleteFarmProduct(_token, product.id);
                if (success) {
                  _provider.removeFarmProduct(product.id);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Product deleted successfully'),
                      backgroundColor: Colors.green[600],
                    ),
                  );
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Failed to delete product'),
                      backgroundColor: Colors.red[600],
                    ),
                  );
                }
              },
              child: Text('Delete', style: TextStyle(color: Colors.red)),
            ),
          ],
        );
      },
    );
  }
}

class AddEditFarmProductScreen extends StatefulWidget {
  final FarmerProduct? existingProduct;

  const AddEditFarmProductScreen({Key? key, this.existingProduct}) : super(key: key);

  @override
  _AddEditFarmProductScreenState createState() => _AddEditFarmProductScreenState();
}

class _AddEditFarmProductScreenState extends State<AddEditFarmProductScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _priceController = TextEditingController();
  final _farmNameController = TextEditingController();
  final _locationController = TextEditingController();
  final _farmLocationController = TextEditingController();

  bool _isOrganic = false;
  String _harvestSeason = 'all';
  bool _isLoading = false;
  String _status = 'active';

  @override
  void initState() {
    super.initState();
    if (widget.existingProduct != null) {
      _titleController.text = widget.existingProduct!.title;
      _descriptionController.text = widget.existingProduct!.description;
      _priceController.text = widget.existingProduct!.price.toString();
      _farmNameController.text = widget.existingProduct!.farmName ?? '';
      _locationController.text = widget.existingProduct!.location;
      _farmLocationController.text = widget.existingProduct!.farmLocation ?? '';
      _isOrganic = widget.existingProduct!.isOrganic;
      _harvestSeason = widget.existingProduct!.harvestSeason ?? 'all';
      _status = widget.existingProduct!.status;
    }
  }

  @override
  void dispose() {
    _titleController.dispose();
    _descriptionController.dispose();
    _priceController.dispose();
    _farmNameController.dispose();
    _locationController.dispose();
    _farmLocationController.dispose();
    super.dispose();
  }

  Future<void> _saveProduct() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
    });

    final apiService = Provider.of<ApiService>(context, listen: false);
    final provider = Provider.of<FoodSellerProvider>(context, listen: false);

    final productData = {
      'title': _titleController.text,
      'description': _descriptionController.text,
      'price': double.parse(_priceController.text),
      'currency_code': 'NGN',
      'category_id': 1, // Assuming a default category ID for farm products
      'condition': 'new',
      'location': _locationController.text,
      'negotiable': false,
      'status': _status,
      // Farm-specific fields
      'farm_name': _farmNameController.text,
      'is_organic': _isOrganic,
      'harvest_season': _harvestSeason,
      'farm_location': _farmLocationController.text,
      'direct_from_farm': true,
    };

    bool success = false;
    String message = '';

    try {
      if (widget.existingProduct != null) {
        // Update existing product
        success = await apiService.updateFarmProduct(
          'sample_token', // In a real app, this would come from auth service
          widget.existingProduct!.id,
          productData,
        );
        message = success ? 'Product updated successfully' : 'Failed to update product';
      } else {
        // Create new product
        success = await apiService.addFarmProduct(
          'sample_token', // In a real app, this would come from auth service
          productData,
        );
        message = success ? 'Product added successfully' : 'Failed to add product';
      }

      if (success) {
        // Refresh the provider's list
        final products = await apiService.getMyFarmProducts('sample_token');
        if (products != null) {
          provider.setFarmProducts(products);
        }
        
        Navigator.pop(context);
      }
    } catch (e) {
      message = 'Error: $e';
      success = false;
    }

    setState(() {
      _isLoading = false;
    });

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: success ? Colors.green[600] : Colors.red[600],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.existingProduct != null ? 'Edit Product' : 'Add Product'),
        backgroundColor: Colors.green[600],
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              // Title
              TextFormField(
                controller: _titleController,
                decoration: InputDecoration(
                  labelText: 'Product Name',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter product name';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Description
              TextFormField(
                controller: _descriptionController,
                decoration: InputDecoration(
                  labelText: 'Description',
                  border: OutlineInputBorder(),
                ),
                maxLines: 3,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter description';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Price
              TextFormField(
                controller: _priceController,
                decoration: InputDecoration(
                  labelText: 'Price (NGN)',
                  border: OutlineInputBorder(),
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter price';
                  }
                  if (double.tryParse(value) == null) {
                    return 'Please enter a valid number';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Farm Name
              TextFormField(
                controller: _farmNameController,
                decoration: InputDecoration(
                  labelText: 'Farm Name',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter farm name';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Farm Location
              TextFormField(
                controller: _farmLocationController,
                decoration: InputDecoration(
                  labelText: 'Farm Location',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter farm location';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Customer Location
              TextFormField(
                controller: _locationController,
                decoration: InputDecoration(
                  labelText: 'Customer Pickup/Delivery Location',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter location';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Organic Toggle
              Row(
                children: [
                  Checkbox(
                    value: _isOrganic,
                    onChanged: (value) {
                      setState(() {
                        _isOrganic = value ?? false;
                      });
                    },
                  ),
                  Text('Organic Product'),
                ],
              ),

              // Harvest Season Dropdown
              DropdownButtonFormField<String>(
                value: _harvestSeason,
                decoration: InputDecoration(
                  labelText: 'Harvest Season',
                  border: OutlineInputBorder(),
                ),
                items: [
                  DropdownMenuItem(value: 'all', child: Text('All Season')),
                  DropdownMenuItem(value: 'summer', child: Text('Summer')),
                  DropdownMenuItem(value: 'winter', child: Text('Winter')),
                  DropdownMenuItem(value: 'spring', child: Text('Spring')),
                  DropdownMenuItem(value: 'fall', child: Text('Fall')),
                ],
                onChanged: (value) {
                  setState(() {
                    _harvestSeason = value ?? 'all';
                  });
                },
              ),
              SizedBox(height: 16),

              // Status Dropdown
              DropdownButtonFormField<String>(
                value: _status,
                decoration: InputDecoration(
                  labelText: 'Status',
                  border: OutlineInputBorder(),
                ),
                items: [
                  DropdownMenuItem(value: 'active', child: Text('Active')),
                  DropdownMenuItem(value: 'inactive', child: Text('Inactive')),
                  DropdownMenuItem(value: 'sold', child: Text('Sold')),
                  DropdownMenuItem(value: 'pending', child: Text('Pending')),
                ],
                onChanged: (value) {
                  setState(() {
                    _status = value ?? 'active';
                  });
                },
              ),
              SizedBox(height: 32),

              // Save Button
              ElevatedButton(
                onPressed: _isLoading ? null : _saveProduct,
                child: _isLoading
                    ? CircularProgressIndicator(color: Colors.white)
                    : Text(widget.existingProduct != null ? 'Update Product' : 'Add Product'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green[600],
                  foregroundColor: Colors.white,
                  padding: EdgeInsets.symmetric(vertical: 16),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}