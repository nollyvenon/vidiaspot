import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../ui/providers/food_seller_provider.dart';
import '../models/menu_item.dart';

class MenuManagementScreen extends StatefulWidget {
  @override
  _MenuManagementScreenState createState() => _MenuManagementScreenState();
}

class _MenuManagementScreenState extends State<MenuManagementScreen> {
  @override
  Widget build(BuildContext context) {
    final foodSellerProvider = Provider.of<FoodSellerProvider>(context);
    final menuItems = foodSellerProvider.menuItems;

    return Scaffold(
      appBar: AppBar(
        title: Text('Menu Management'),
        backgroundColor: Colors.orange[400],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.add),
            onPressed: () {
              _addItem(context);
            },
          ),
        ],
      ),
      body: foodSellerProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : menuItems.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.restaurant_menu_outlined,
                        size: 80,
                        color: Colors.grey,
                      ),
                      SizedBox(height: 20),
                      Text(
                        'No menu items yet',
                        style: TextStyle(fontSize: 18, color: Colors.grey),
                      ),
                      SizedBox(height: 10),
                      Text(
                        'Add your first menu item to get started',
                        style: TextStyle(color: Colors.grey[600]),
                      ),
                      SizedBox(height: 20),
                      ElevatedButton(
                        onPressed: () {
                          _addItem(context);
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.orange[400],
                        ),
                        child: Text(
                          'Add Item',
                          style: TextStyle(color: Colors.white),
                        ),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: () async {
                    // Refresh menu items
                  },
                  child: ListView.builder(
                    itemCount: menuItems.length,
                    itemBuilder: (context, index) {
                      return _buildMenuItemCard(context, menuItems[index]);
                    },
                  ),
                ),
    );
  }

  Widget _buildMenuItemCard(BuildContext context, MenuItem item) {
    return Card(
      margin: EdgeInsets.all(8),
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(8),
                color: Colors.grey[300],
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: item.mainImage.isNotEmpty
                    ? Image.network(
                        item.mainImage,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) {
                          return Icon(
                            Icons.fastfood,
                            size: 40,
                            color: Colors.grey[600],
                          );
                        },
                      )
                    : Icon(
                        Icons.fastfood,
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
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          item.name,
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: item.isAvailable ? Colors.green[100] : Colors.red[100],
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          item.isAvailable ? 'Available' : 'Not Available',
                          style: TextStyle(
                            color: item.isAvailable ? Colors.green[800] : Colors.red[800],
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 5),
                  Text(
                    item.shortDescription,
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 14,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  SizedBox(height: 5),
                  Text(
                    '\$${item.price.toStringAsFixed(2)}',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Colors.green,
                      fontSize: 16,
                    ),
                  ),
                  SizedBox(height: 5),
                  Row(
                    children: [
                      Icon(
                        Icons.inventory_2,
                        size: 14,
                        color: item.inventoryQuantity > 10 ? Colors.green : Colors.orange,
                      ),
                      SizedBox(width: 5),
                      Text(
                        '${item.inventoryQuantity} in stock',
                        style: TextStyle(
                          fontSize: 12,
                          color: item.inventoryQuantity > 10 ? Colors.green : Colors.orange,
                        ),
                      ),
                    ],
                  ),
                  if (item.isVegetarian) ...[
                    SizedBox(height: 5),
                    Chip(
                      label: Text(
                        'Veg',
                        style: TextStyle(fontSize: 11),
                      ),
                      backgroundColor: Colors.green[100],
                      labelStyle: TextStyle(color: Colors.green[800]),
                    ),
                  ],
                ],
              ),
            ),
            Column(
              children: [
                IconButton(
                  icon: Icon(Icons.edit, color: Colors.orange[400]),
                  onPressed: () {
                    _editItem(context, item);
                  },
                ),
                IconButton(
                  icon: Icon(Icons.delete, color: Colors.red),
                  onPressed: () {
                    _deleteItem(context, item.id);
                  },
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  void _addItem(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => MenuItemFormScreen(menuItem: null),
      ),
    ).then((value) {
      // Refresh data if needed
    });
  }

  void _editItem(BuildContext context, MenuItem item) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => MenuItemFormScreen(menuItem: item),
      ),
    ).then((value) {
      // Refresh data if needed
    });
  }

  void _deleteItem(BuildContext context, String itemId) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Delete Item'),
        content: Text('Are you sure you want to delete this menu item?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              // In a real app, you would call the API to delete the item
              Provider.of<FoodSellerProvider>(context, listen: false).removeMenuItem(itemId);
              Navigator.pop(context);
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text('Item deleted successfully')),
              );
            },
            child: Text('Delete', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}

class MenuItemFormScreen extends StatefulWidget {
  final MenuItem? menuItem;

  const MenuItemFormScreen({Key? key, this.menuItem}) : super(key: key);

  @override
  _MenuItemFormScreenState createState() => _MenuItemFormScreenState();
}

class _MenuItemFormScreenState extends State<MenuItemFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _shortDescriptionController = TextEditingController();
  final _priceController = TextEditingController();
  final _categoryController = TextEditingController();
  final _inventoryController = TextEditingController();

  bool _isVegetarian = false;
  bool _isVegan = false;
  bool _isGlutenFree = false;
  bool _isAvailable = true;
  bool _isVisible = true;
  String _currency = 'USD';

  @override
  void initState() {
    super.initState();
    if (widget.menuItem != null) {
      _nameController.text = widget.menuItem!.name;
      _descriptionController.text = widget.menuItem!.description;
      _shortDescriptionController.text = widget.menuItem!.shortDescription;
      _priceController.text = widget.menuItem!.price.toString();
      _categoryController.text = widget.menuItem!.category;
      _inventoryController.text = widget.menuItem!.inventoryQuantity.toString();
      
      _isVegetarian = widget.menuItem!.isVegetarian;
      _isVegan = widget.menuItem!.isVegan;
      _isGlutenFree = widget.menuItem!.isGlutenFree;
      _isAvailable = widget.menuItem!.isAvailable;
      _isVisible = widget.menuItem!.isVisible;
      _currency = widget.menuItem!.currency;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.menuItem != null ? 'Edit Food Item' : 'Add Food Item'),
        backgroundColor: Colors.orange[400],
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              TextFormField(
                controller: _nameController,
                decoration: InputDecoration(
                  labelText: 'Item Name',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter a name for the item';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),
              TextFormField(
                controller: _shortDescriptionController,
                decoration: InputDecoration(
                  labelText: 'Short Description',
                  border: OutlineInputBorder(),
                ),
              ),
              SizedBox(height: 16),
              TextFormField(
                controller: _descriptionController,
                decoration: InputDecoration(
                  labelText: 'Description',
                  border: OutlineInputBorder(),
                ),
                maxLines: 3,
              ),
              SizedBox(height: 16),
              Row(
                children: [
                  Expanded(
                    flex: 2,
                    child: TextFormField(
                      controller: _priceController,
                      decoration: InputDecoration(
                        labelText: 'Price',
                        border: OutlineInputBorder(),
                        prefixText: '\$',
                      ),
                      keyboardType: TextInputType.numberWithOptions(decimal: true),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Please enter a price';
                        }
                        if (double.tryParse(value) == null) {
                          return 'Please enter a valid price';
                        }
                        return null;
                      },
                    ),
                  ),
                  SizedBox(width: 16),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: _currency,
                      decoration: InputDecoration(
                        labelText: 'Currency',
                        border: OutlineInputBorder(),
                      ),
                      items: ['USD', 'EUR', 'GBP', 'NGN', 'KES', 'GHS'].map((currency) {
                        return DropdownMenuItem(
                          value: currency,
                          child: Text(currency),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          _currency = value ?? 'USD';
                        });
                      },
                    ),
                  ),
                ],
              ),
              SizedBox(height: 16),
              TextFormField(
                controller: _categoryController,
                decoration: InputDecoration(
                  labelText: 'Category',
                  border: OutlineInputBorder(),
                ),
              ),
              SizedBox(height: 16),
              TextFormField(
                controller: _inventoryController,
                decoration: InputDecoration(
                  labelText: 'Inventory Quantity',
                  border: OutlineInputBorder(),
                ),
                keyboardType: TextInputType.number,
              ),
              SizedBox(height: 16),
              SwitchListTile(
                title: Text('Available for Order'),
                value: _isAvailable,
                onChanged: (value) {
                  setState(() {
                    _isAvailable = value;
                  });
                },
              ),
              SwitchListTile(
                title: Text('Visible to Customers'),
                value: _isVisible,
                onChanged: (value) {
                  setState(() {
                    _isVisible = value;
                  });
                },
              ),
              SwitchListTile(
                title: Text('Vegetarian'),
                value: _isVegetarian,
                onChanged: (value) {
                  setState(() {
                    _isVegetarian = value;
                  });
                },
              ),
              SwitchListTile(
                title: Text('Vegan'),
                value: _isVegan,
                onChanged: (value) {
                  setState(() {
                    _isVegan = value;
                  });
                },
              ),
              SwitchListTile(
                title: Text('Gluten Free'),
                value: _isGlutenFree,
                onChanged: (value) {
                  setState(() {
                    _isGlutenFree = value;
                  });
                },
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: _saveItem,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.orange[400],
                  padding: EdgeInsets.symmetric(vertical: 15),
                ),
                child: Text(
                  widget.menuItem != null ? 'Update Item' : 'Add Item',
                  style: TextStyle(color: Colors.white, fontSize: 16),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _saveItem() {
    if (_formKey.currentState!.validate()) {
      // Create or update the menu item
      MenuItem newItem;
      
      if (widget.menuItem != null) {
        // Update existing item
        newItem = MenuItem(
          id: widget.menuItem!.id,
          name: _nameController.text,
          description: _descriptionController.text,
          shortDescription: _shortDescriptionController.text,
          category: _categoryController.text,
          subcategory: '', // Could be implemented later
          price: double.parse(_priceController.text),
          compareAtPrice: 0, // Could be implemented later
          currency: _currency,
          inventoryQuantity: int.tryParse(_inventoryController.text) ?? 0,
          isAvailable: _isAvailable,
          isVisible: _isVisible,
          images: widget.menuItem!.images, // Keep existing images
          mainImage: widget.menuItem!.mainImage, // Keep main image
          sku: widget.menuItem!.sku, // Keep existing SKU
          barcode: widget.menuItem!.barcode, // Keep existing barcode
          weight: widget.menuItem!.weight,
          dimensions: widget.menuItem!.dimensions,
          ingredients: widget.menuItem!.ingredients,
          allergens: widget.menuItem!.allergens,
          isVegetarian: _isVegetarian,
          isVegan: _isVegan,
          isGlutenFree: _isGlutenFree,
          dietaryInfo: widget.menuItem!.dietaryInfo,
          nutritionalInfo: widget.menuItem!.nutritionalInfo,
          tags: widget.menuItem!.tags,
          createdAt: widget.menuItem!.createdAt,
          updatedAt: DateTime.now(),
          restaurantId: widget.menuItem!.restaurantId,
          viewCount: widget.menuItem!.viewCount,
          orderCount: widgetItem!.orderCount,
          avgRating: widget.menuItem!.avgRating,
          numRatings: widget.menuItem!.numRatings,
        );
        
        Provider.of<FoodSellerProvider>(context, listen: false)
            .updateMenuItem(widget.menuItem!.id, newItem);
      } else {
        // Create new item
        String newId = 'item_${DateTime.now().millisecondsSinceEpoch}';
        newItem = MenuItem(
          id: newId,
          name: _nameController.text,
          description: _descriptionController.text,
          shortDescription: _shortDescriptionController.text,
          category: _categoryController.text,
          subcategory: '',
          price: double.parse(_priceController.text),
          compareAtPrice: 0,
          currency: _currency,
          inventoryQuantity: int.tryParse(_inventoryController.text) ?? 0,
          isAvailable: _isAvailable,
          isVisible: _isVisible,
          images: [],
          mainImage: '',
          sku: 'SKU${DateTime.now().millisecondsSinceEpoch}',
          barcode: '',
          weight: 0,
          dimensions: '',
          ingredients: '',
          allergens: [],
          isVegetarian: _isVegetarian,
          isVegan: _isVegan,
          isGlutenFree: _isGlutenFree,
          dietaryInfo: '',
          nutritionalInfo: '',
          tags: [],
          createdAt: DateTime.now(),
          updatedAt: DateTime.now(),
          restaurantId: 'restaurant_123', // Get from provider
          viewCount: 0,
          orderCount: 0,
          avgRating: 0,
          numRatings: 0,
        );
        
        Provider.of<FoodSellerProvider>(context, listen: false)
            .addMenuItem(newItem);
      }
      
      // Show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.menuItem != null 
                ? 'Item updated successfully' 
                : 'Item added successfully',
          ),
        ),
      );
      
      // Go back to previous screen
      Navigator.pop(context);
    }
  }
}