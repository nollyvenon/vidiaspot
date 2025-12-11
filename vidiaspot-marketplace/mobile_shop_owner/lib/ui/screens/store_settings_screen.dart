import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/shop_owner_provider.dart';
import '../../models/shop_data.dart';

class StoreSettingsScreen extends StatefulWidget {
  @override
  _StoreSettingsScreenState createState() => _StoreSettingsScreenState();
}

class _StoreSettingsScreenState extends State<StoreSettingsScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();
  final _currencyController = TextEditingController();

  ShopData? _shopData;

  @override
  void initState() {
    super.initState();
    _loadShopData();
  }

  void _loadShopData() {
    // In a real app, this would load shop data from the provider or API
    setState(() {
      _shopData = ShopData(
        id: 'shop123',
        name: 'My Shop',
        description: 'Welcome to my shop!',
        logoUrl: '',
        coverImage: '',
        ownerName: 'Shop Owner',
        email: 'owner@myshop.com',
        phone: '+1234567890',
        address: '123 Main St, City, Country',
        currency: 'USD',
        isActive: true,
        createdAt: DateTime.now(),
        category: 'General',
      );
      
      _nameController.text = _shopData?.name ?? '';
      _descriptionController.text = _shopData?.description ?? '';
      _emailController.text = _shopData?.email ?? '';
      _phoneController.text = _shopData?.phone ?? '';
      _addressController.text = _shopData?.address ?? '';
      _currencyController.text = _shopData?.currency ?? 'USD';
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Store Settings'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: _shopData == null
          ? Center(child: CircularProgressIndicator())
          : Padding(
              padding: EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: ListView(
                  children: [
                    // Store logo and cover
                    _buildStoreImagesSection(),
                    SizedBox(height: 20),
                    
                    // Store information form
                    _buildStoreInfoForm(),
                    SizedBox(height: 20),
                    
                    // Business information
                    _buildBusinessInfoSection(),
                    SizedBox(height: 20),
                    
                    // Store preferences
                    _buildStorePreferencesSection(),
                    
                    SizedBox(height: 30),
                    
                    // Save button
                    ElevatedButton(
                      onPressed: _saveSettings,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blue,
                        padding: EdgeInsets.symmetric(vertical: 15),
                      ),
                      child: Text(
                        'Save Settings',
                        style: TextStyle(color: Colors.white, fontSize: 16),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStoreImagesSection() {
    return Card(
      elevation: 3,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Store Images',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 15),
            Row(
              children: [
                // Logo
                Container(
                  width: 100,
                  height: 100,
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: _shopData?.logoUrl.isNotEmpty == true
                      ? Image.network(
                          _shopData!.logoUrl,
                          fit: BoxFit.cover,
                        )
                      : Icon(
                          Icons.store,
                          size: 50,
                          color: Colors.grey[400],
                        ),
                ),
                SizedBox(width: 15),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Store Logo',
                        style: TextStyle(fontWeight: FontWeight.w500),
                      ),
                      SizedBox(height: 5),
                      ElevatedButton.icon(
                        onPressed: () {
                          // Add logo functionality
                        },
                        icon: Icon(Icons.add_a_photo),
                        label: Text('Change Logo'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.grey[200],
                          foregroundColor: Colors.black,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            SizedBox(height: 15),
            Container(
              width: double.infinity,
              height: 100,
              decoration: BoxDecoration(
                border: Border.all(color: Colors.grey),
                borderRadius: BorderRadius.circular(10),
              ),
              child: _shopData?.coverImage.isNotEmpty == true
                  ? Image.network(
                      _shopData!.coverImage,
                      fit: BoxFit.cover,
                    )
                  : Icon(
                      Icons.wallpaper,
                      size: 50,
                      color: Colors.grey[400],
                    ),
            ),
            SizedBox(height: 5),
            ElevatedButton.icon(
              onPressed: () {
                // Add cover image functionality
              },
              icon: Icon(Icons.add_a_photo),
              label: Text('Change Cover Image'),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.grey[200],
                foregroundColor: Colors.black,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStoreInfoForm() {
    return Card(
      elevation: 3,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Store Information',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 15),
            TextFormField(
              controller: _nameController,
              decoration: InputDecoration(
                labelText: 'Store Name',
                border: OutlineInputBorder(),
              ),
              validator: (value) {
                if (value == null || value.isEmpty) {
                  return 'Please enter store name';
                }
                return null;
              },
            ),
            SizedBox(height: 15),
            TextFormField(
              controller: _descriptionController,
              decoration: InputDecoration(
                labelText: 'Store Description',
                border: OutlineInputBorder(),
              ),
              maxLines: 3,
            ),
            SizedBox(height: 15),
            Row(
              children: [
                Expanded(
                  child: TextFormField(
                    controller: _emailController,
                    decoration: InputDecoration(
                      labelText: 'Contact Email',
                      border: OutlineInputBorder(),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter email';
                      }
                      if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
                        return 'Please enter a valid email';
                      }
                      return null;
                    },
                  ),
                ),
                SizedBox(width: 10),
                Expanded(
                  child: TextFormField(
                    controller: _phoneController,
                    decoration: InputDecoration(
                      labelText: 'Phone Number',
                      border: OutlineInputBorder(),
                    ),
                  ),
                ),
              ],
            ),
            SizedBox(height: 15),
            TextFormField(
              controller: _addressController,
              decoration: InputDecoration(
                labelText: 'Store Address',
                border: OutlineInputBorder(),
              ),
            ),
            SizedBox(height: 15),
            TextFormField(
              controller: _currencyController,
              decoration: InputDecoration(
                labelText: 'Currency',
                border: OutlineInputBorder(),
              ),
              validator: (value) {
                if (value == null || value.isEmpty) {
                  return 'Please enter currency code';
                }
                return null;
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBusinessInfoSection() {
    return Card(
      elevation: 3,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Business Information',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 15),
            _buildSettingRow('Business Registration', 'Reg123456'),
            _buildSettingRow('Tax ID', 'TAX789012'),
            _buildSettingRow('Business Category', _shopData?.category ?? ''),
          ],
        ),
      ),
    );
  }

  Widget _buildStorePreferencesSection() {
    return Card(
      elevation: 3,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Store Preferences',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 15),
            SwitchListTile(
              title: Text('Enable Store'),
              subtitle: Text('Make your store visible to customers'),
              value: _shopData?.isActive ?? true,
              onChanged: (value) {
                setState(() {
                  _shopData = _shopData!.copyWith(isActive: value);
                });
              },
            ),
            Divider(),
            SwitchListTile(
              title: Text('Accept Orders'),
              subtitle: Text('Allow customers to place orders'),
              value: true, // This would be a separate field in a real app
              onChanged: (value) {
                // Update order acceptance setting
              },
            ),
            Divider(),
            SwitchListTile(
              title: Text('Enable Reviews'),
              subtitle: Text('Allow customers to leave product reviews'),
              value: true, // This would be a separate field in a real app
              onChanged: (value) {
                // Update reviews setting
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSettingRow(String label, String value) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 8),
      child: Row(
        children: [
          Expanded(
            flex: 1,
            child: Text(
              label,
              style: TextStyle(
                fontWeight: FontWeight.w500,
                color: Colors.grey[700],
              ),
            ),
          ),
          Expanded(
            flex: 2,
            child: Text(
              value,
              style: TextStyle(
                color: Colors.black87,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _saveSettings() {
    if (_formKey.currentState!.validate()) {
      // In a real app, this would save settings to the backend
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Store settings saved successfully'),
        ),
      );
      
      // Update shop data in provider
      _shopData = _shopData!.copyWith(
        name: _nameController.text,
        description: _descriptionController.text,
        email: _emailController.text,
        phone: _phoneController.text,
        address: _addressController.text,
        currency: _currencyController.text,
      );
      
      // Update provider
      Provider.of<ShopOwnerProvider>(context, listen: false).setShopData(_shopData);
    }
  }
}

// Extension to make ShopData copyWith method available
extension ShopDataExtension on ShopData {
  ShopData copyWith({
    String? id,
    String? name,
    String? description,
    String? logoUrl,
    String? coverImage,
    String? ownerName,
    String? email,
    String? phone,
    String? address,
    String? currency,
    bool? isActive,
    DateTime? createdAt,
    String? category,
  }) {
    return ShopData(
      id: id ?? this.id,
      name: name ?? this.name,
      description: description ?? this.description,
      logoUrl: logoUrl ?? this.logoUrl,
      coverImage: coverImage ?? this.coverImage,
      ownerName: ownerName ?? this.ownerName,
      email: email ?? this.email,
      phone: phone ?? this.phone,
      address: address ?? this.address,
      currency: currency ?? this.currency,
      isActive: isActive ?? this.isActive,
      createdAt: createdAt ?? this.createdAt,
      category: category ?? this.category,
    );
  }
}