import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../ui/providers/farm_provider.dart';
import '../models/farm_product.dart';
import 'product_form_screen.dart';

class ProductsScreen extends StatefulWidget {
  @override
  _ProductsScreenState createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  @override
  Widget build(BuildContext context) {
    final farmProvider = Provider.of<FarmProvider>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('Farm Products'),
        backgroundColor: Colors.green[400],
        foregroundColor: Colors.white,
      ),
      body: farmProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                // Refresh products
              },
              child: _buildProductsList(farmProvider),
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => ProductFormScreen()),
          ).then((value) {
            if (value == true) {
              // Refresh products after adding/updating
            }
          });
        },
        backgroundColor: Colors.green[400],
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildProductsList(FarmProvider farmProvider) {
    final products = farmProvider.products;

    if (products.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.inventory_2,
              size: 80,
              color: Colors.grey[400],
            ),
            SizedBox(height: 20),
            Text(
              'No products yet',
              style: TextStyle(
                fontSize: 18,
                color: Colors.grey[600],
              ),
            ),
            SizedBox(height: 10),
            Text(
              'Add your first farm product to get started',
              style: TextStyle(
                color: Colors.grey[500],
              ),
            ),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => ProductFormScreen()),
                );
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green[400],
              ),
              child: Text(
                'Add Product',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      itemCount: products.length,
      itemBuilder: (context, index) {
        final product = products[index];
        return Card(
          margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: ListTile(
            contentPadding: EdgeInsets.all(16),
            leading: Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(8),
                color: Colors.grey[300],
              ),
              child: product.mainImage.isNotEmpty
                  ? ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: Image.network(
                        product.mainImage,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) {
                          return Icon(
                            Icons.fastfood,
                            size: 30,
                            color: Colors.grey[600],
                          );
                        },
                      ),
                    )
                  : Icon(
                      Icons.fastfood,
                      size: 30,
                      color: Colors.grey[600],
                    ),
            ),
            title: Text(
              product.name,
              style: TextStyle(fontWeight: FontWeight.bold),
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('\$${product.price.toStringAsFixed(2)}'),
                SizedBox(height: 5),
                Text(
                  'In Stock: ${product.inventoryQuantity}',
                  style: TextStyle(
                    color: product.inventoryQuantity > 0 ? Colors.green : Colors.red,
                  ),
                ),
              ],
            ),
            trailing: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                product.isAvailable
                    ? Icon(Icons.check_circle, color: Colors.green)
                    : Icon(Icons.cancel, color: Colors.red),
                SizedBox(width: 10),
                PopupMenuButton(
                  icon: Icon(Icons.more_vert),
                  itemBuilder: (context) => [
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
                      value: 'toggle_availability',
                      child: Row(
                        children: [
                          Icon(
                            product.isAvailable ? Icons.visibility_off : Icons.visibility,
                            size: 16,
                          ),
                          SizedBox(width: 8),
                          Text(
                            product.isAvailable ? 'Hide Product' : 'Show Product',
                          ),
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
                  ],
                  onSelected: (value) {
                    _handleProductAction(context, product, value);
                  },
                ),
              ],
            ),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => ProductFormScreen(product: product),
                ),
              );
            },
          ),
        );
      },
    );
  }

  void _handleProductAction(BuildContext context, FarmProduct product, String action) {
    final farmProvider = Provider.of<FarmProvider>(context, listen: false);

    switch (action) {
      case 'edit':
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => ProductFormScreen(product: product),
          ),
        );
        break;
      case 'toggle_availability':
        farmProvider.updateProduct(product.copyWith(isAvailable: !product.isAvailable));
        break;
      case 'delete':
        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: Text('Delete Product'),
            content: Text('Are you sure you want to delete ${product.name}?'),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: Text('Cancel'),
              ),
              TextButton(
                onPressed: () {
                  farmProvider.removeProduct(product.id);
                  Navigator.pop(context);
                },
                child: Text(
                  'Delete',
                  style: TextStyle(color: Colors.red),
                ),
              ),
            ],
          ),
        );
        break;
    }
  }
}