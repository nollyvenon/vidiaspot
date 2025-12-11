import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/farm_product.dart';
import '../ui/providers/farm_provider.dart';

class ProductFormScreen extends StatefulWidget {
  final FarmProduct? product;

  const ProductFormScreen({Key? key, this.product}) : super(key: key);

  @override
  _ProductFormScreenState createState() => _ProductFormScreenState();
}

class _ProductFormScreenState extends State<ProductFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _priceController = TextEditingController();
  final _compareAtPriceController = TextEditingController();
  final _inventoryController = TextEditingController();
  final _weightController = TextEditingController();
  final _dimensionsController = TextEditingController();
  final _ingredientsController = TextEditingController();
  final _certificationController = TextEditingController();
  final _productionDateController = TextEditingController();
  final _expiryDateController = TextEditingController();
  final _nutritionalInfoController = TextEditingController();

  String _selectedCategory = 'vegetables';
  String _selectedUnit = 'kg';
  bool _isAvailable = true;
  bool _isVisible = true;
  bool _isOrganic = false;
  bool _isFresh = true;
  bool _isSeasonal = false;
  bool _isLocal = true;
  
  List<String> _images = [];
  String _mainImage = '';

  @override
  void initState() {
    super.initState();
    
    if (widget.product != null) {
      // Editing existing product
      final product = widget.product!;
      _nameController.text = product.name;
      _descriptionController.text = product.description;
      _priceController.text = product.price.toString();
      _compareAtPriceController.text = product.compareAtPrice.toString();
      _inventoryController.text = product.inventoryQuantity.toString();
      _weightController.text = product.weight.toString();
      _dimensionsController.text = product.dimensions;
      _ingredientsController.text = product.ingredients;
      _certificationController.text = product.certification;
      _productionDateController.text = product.productionDate;
      _expiryDateController.text = product.expiryDate;
      _nutritionalInfoController.text = product.nutritionalInfo;
      
      _selectedCategory = product.category;
      _selectedUnit = product.unit;
      _isAvailable = product.isAvailable;
      _isVisible = product.isVisible;
      _isOrganic = product.isOrganic;
      _isFresh = product.isFresh;
      _isSeasonal = product.isSeasonal;
      _isLocal = product.isLocal;
      
      _images = List.from(product.images);
      _mainImage = product.mainImage;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.product != null ? 'Edit Product' : 'Add Product'),
        backgroundColor: Colors.green[400],
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Product name
                TextFormField(
                  controller: _nameController,
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
                
                // Category dropdown
                DropdownButtonFormField<String>(
                  value: _selectedCategory,
                  decoration: InputDecoration(
                    labelText: 'Category',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    'vegetables', 'fruits', 'grains', 'livestock', 'dairy', 'poultry', 'herbs'
                  ].map((category) {
                    return DropdownMenuItem(
                      value: category,
                      child: Text(
                        category[0].toUpperCase() + category.substring(1),
                      ),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() {
                      _selectedCategory = value ?? 'vegetables';
                    });
                  },
                ),
                SizedBox(height: 16),
                
                // Unit dropdown
                DropdownButtonFormField<String>(
                  value: _selectedUnit,
                  decoration: InputDecoration(
                    labelText: 'Unit of Measurement',
                    border: OutlineInputBorder(),
                  ),
                  items: ['kg', 'lb', 'piece', 'dozen', 'bundle', 'liter', 'gallon'].map((unit) {
                    return DropdownMenuItem(
                      value: unit,
                      child: Text(unit.toUpperCase()),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() {
                      _selectedUnit = value ?? 'kg';
                    });
                  },
                ),
                SizedBox(height: 16),
                
                // Price and compare at price
                Row(
                  children: [
                    Expanded(
                      child: TextFormField(
                        controller: _priceController,
                        decoration: InputDecoration(
                          labelText: 'Price (\$)',
                          border: OutlineInputBorder(),
                        ),
                        keyboardType: TextInputType.numberWithOptions(decimal: true),
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'Please enter price';
                          }
                          if (double.tryParse(value) == null) {
                            return 'Please enter a valid price';
                          }
                          return null;
                        },
                      ),
                    ),
                    SizedBox(width: 10),
                    Expanded(
                      child: TextFormField(
                        controller: _compareAtPriceController,
                        decoration: InputDecoration(
                          labelText: 'Compare at Price (\$)',
                          border: OutlineInputBorder(),
                        ),
                        keyboardType: TextInputType.numberWithOptions(decimal: true),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 16),
                
                // Inventory
                TextFormField(
                  controller: _inventoryController,
                  decoration: InputDecoration(
                    labelText: 'Inventory Quantity',
                    border: OutlineInputBorder(),
                  ),
                  keyboardType: TextInputType.number,
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter inventory quantity';
                    }
                    if (int.tryParse(value) == null) {
                      return 'Please enter a valid number';
                    }
                    return null;
                  },
                ),
                SizedBox(height: 16),
                
                // Weight and dimensions
                Row(
                  children: [
                    Expanded(
                      child: TextFormField(
                        controller: _weightController,
                        decoration: InputDecoration(
                          labelText: 'Weight (kg)',
                          border: OutlineInputBorder(),
                        ),
                        keyboardType: TextInputType.numberWithOptions(decimal: true),
                      ),
                    ),
                    SizedBox(width: 10),
                    Expanded(
                      child: TextFormField(
                        controller: _dimensionsController,
                        decoration: InputDecoration(
                          labelText: 'Dimensions',
                          border: OutlineInputBorder(),
                        ),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 16),
                
                // Availability switches
                Wrap(
                  spacing: 10,
                  children: [
                    FilterChip(
                      label: Text('Available'),
                      selected: _isAvailable,
                      onSelected: (selected) {
                        setState(() {
                          _isAvailable = selected;
                        });
                      },
                    ),
                    FilterChip(
                      label: Text('Visible'),
                      selected: _isVisible,
                      onSelected: (selected) {
                        setState(() {
                          _isVisible = selected;
                        });
                      },
                    ),
                    FilterChip(
                      label: Text('Organic'),
                      selected: _isOrganic,
                      onSelected: (selected) {
                        setState(() {
                          _isOrganic = selected;
                        });
                      },
                    ),
                    FilterChip(
                      label: Text('Fresh'),
                      selected: _isFresh,
                      onSelected: (selected) {
                        setState(() {
                          _isFresh = selected;
                        });
                      },
                    ),
                    FilterChip(
                      label: Text('Seasonal'),
                      selected: _isSeasonal,
                      onSelected: (selected) {
                        setState(() {
                          _isSeasonal = selected;
                        });
                      },
                    ),
                    FilterChip(
                      label: Text('Local'),
                      selected: _isLocal,
                      onSelected: (selected) {
                        setState(() {
                          _isLocal = selected;
                        });
                      },
                    ),
                  ],
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
                ),
                SizedBox(height: 16),
                
                // Ingredients
                TextFormField(
                  controller: _ingredientsController,
                  decoration: InputDecoration(
                    labelText: 'Ingredients',
                    border: OutlineInputBorder(),
                  ),
                  maxLines: 2,
                ),
                SizedBox(height: 16),
                
                // Certification
                TextFormField(
                  controller: _certificationController,
                  decoration: InputDecoration(
                    labelText: 'Certification',
                    hintText: 'e.g. Organic, Fair Trade, etc.',
                    border: OutlineInputBorder(),
                  ),
                ),
                SizedBox(height: 16),
                
                // Production and expiry dates
                Row(
                  children: [
                    Expanded(
                      child: TextFormField(
                        controller: _productionDateController,
                        decoration: InputDecoration(
                          labelText: 'Production Date',
                          border: OutlineInputBorder(),
                        ),
                        readOnly: true,
                        onTap: () async {
                          DateTime? date = await showDatePicker(
                            context: context,
                            initialDate: DateTime.now(),
                            firstDate: DateTime(2000),
                            lastDate: DateTime.now().add(Duration(days: 365)),
                          );
                          if (date != null) {
                            _productionDateController.text = date.toIso8601String().split('T')[0];
                          }
                        },
                      ),
                    ),
                    SizedBox(width: 10),
                    Expanded(
                      child: TextFormField(
                        controller: _expiryDateController,
                        decoration: InputDecoration(
                          labelText: 'Expiry Date',
                          border: OutlineInputBorder(),
                        ),
                        readOnly: true,
                        onTap: () async {
                          DateTime? date = await showDatePicker(
                            context: context,
                            initialDate: DateTime.now().add(Duration(days: 30)),
                            firstDate: DateTime.now(),
                            lastDate: DateTime.now().add(Duration(days: 365)),
                          );
                          if (date != null) {
                            _expiryDateController.text = date.toIso8601String().split('T')[0];
                          }
                        },
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 16),
                
                // Nutritional info
                TextFormField(
                  controller: _nutritionalInfoController,
                  decoration: InputDecoration(
                    labelText: 'Nutritional Information',
                    hintText: 'e.g. Calories: X, Protein: Y, etc.',
                    border: OutlineInputBorder(),
                  ),
                  maxLines: 3,
                ),
                SizedBox(height: 20),
                
                // Save button
                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: _saveProduct,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green[400],
                    ),
                    child: Text(
                      widget.product != null ? 'Update Product' : 'Add Product',
                      style: TextStyle(
                        fontSize: 16,
                        color: Colors.white,
                      ),
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

  void _saveProduct() async {
    if (_formKey.currentState!.validate()) {
      final farmProduct = FarmProduct(
        id: widget.product?.id ?? 'new_${DateTime.now().millisecondsSinceEpoch}',
        name: _nameController.text.trim(),
        description: _descriptionController.text.trim(),
        shortDescription: _descriptionController.text.trim().substring(
          0,
          _descriptionController.text.trim().length > 100
              ? 100
              : _descriptionController.text.trim().length,
        ),
        category: _selectedCategory,
        subcategory: '', // Could implement subcategories
        price: double.parse(_priceController.text),
        compareAtPrice: double.tryParse(_compareAtPriceController.text) ?? 0.0,
        currency: 'USD', // Could make this configurable
        inventoryQuantity: int.parse(_inventoryController.text),
        unit: _selectedUnit,
        isAvailable: _isAvailable,
        isVisible: _isVisible,
        images: _images,
        mainImage: _mainImage.isNotEmpty ? _mainImage : '',
        sku: 'FARM_${DateTime.now().millisecondsSinceEpoch}', // Generate SKU
        barcode: '', // Could implement barcode generation
        weight: double.tryParse(_weightController.text) ?? 0.0,
        dimensions: _dimensionsController.text.trim(),
        ingredients: _ingredientsController.text.trim(),
        allergens: [], // Could implement allergen selection
        isOrganic: _isOrganic,
        isFresh: _isFresh,
        isSeasonal: _isSeasonal,
        isLocal: _isLocal,
        certification: _certificationController.text.trim(),
        productionDate: _productionDateController.text,
        expiryDate: _expiryDateController.text,
        nutritionalInfo: _nutritionalInfoController.text.trim(),
        tags: [], // Could implement tags
        createdAt: widget.product?.createdAt ?? DateTime.now(),
        updatedAt: DateTime.now(),
        farmId: 'current_farm_id', // Would come from provider
        viewCount: widget.product?.viewCount ?? 0,
        orderCount: widget.product?.orderCount ?? 0,
        avgRating: widget.product?.avgRating ?? 0.0,
        numRatings: widget.product?.numRatings ?? 0,
      );

      final farmProvider = Provider.of<FarmProvider>(context, listen: false);
      
      if (widget.product != null) {
        // Update existing product
        await farmProvider.updateProduct(farmProduct);
      } else {
        // Add new product
        await farmProvider.addProduct(farmProduct);
      }
      
      // Show success message and go back
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.product != null ? 'Product updated successfully' : 'Product added successfully',
          ),
        ),
      );
      
      Navigator.pop(context, true);
    }
  }
}