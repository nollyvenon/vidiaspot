import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/services/auth_service.dart';
import '../ui/providers/food_buyer_provider.dart';
import '../models/user_profile.dart';

class ProfileScreen extends StatefulWidget {
  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  @override
  Widget build(BuildContext context) {
    final foodBuyerProvider = Provider.of<FoodBuyerProvider>(context);
    final userProfile = foodBuyerProvider.userProfile;

    return Scaffold(
      appBar: AppBar(
        title: Text('Profile'),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
      ),
      body: foodBuyerProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                // Refresh user profile
              },
              child: SingleChildScrollView(
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    children: [
                      // Profile header
                      Card(
                        elevation: 4,
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: Column(
                            children: [
                              Center(
                                child: Container(
                                  width: 100,
                                  height: 100,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: Colors.grey[300],
                                    border: Border.all(
                                      color: Colors.red[400]!,
                                      width: 3,
                                    ),
                                  ),
                                  child: ClipOval(
                                    child: userProfile?.profilePictureUrl.isNotEmpty == true
                                        ? Image.network(
                                            userProfile!.profilePictureUrl,
                                            fit: BoxFit.cover,
                                            errorBuilder: (context, error, stackTrace) {
                                              return Icon(
                                                Icons.person,
                                                size: 60,
                                                color: Colors.grey[600],
                                              );
                                            },
                                          )
                                        : Icon(
                                            Icons.person,
                                            size: 60,
                                            color: Colors.grey[600],
                                          ),
                                  ),
                                ),
                              ),
                              SizedBox(height: 15),
                              Text(
                                userProfile?.name ?? 'User Name',
                                style: TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              SizedBox(height: 5),
                              Text(
                                userProfile?.email ?? 'user@example.com',
                                style: TextStyle(
                                  color: Colors.grey[600],
                                ),
                              ),
                              SizedBox(height: 15),
                              ElevatedButton(
                                onPressed: () {
                                  _editProfile(context);
                                },
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.red[400],
                                  padding: EdgeInsets.symmetric(horizontal: 30, vertical: 10),
                                ),
                                child: Text(
                                  'Edit Profile',
                                  style: TextStyle(color: Colors.white),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      SizedBox(height: 20),
                      
                      // Profile options
                      _buildProfileOption(
                        icon: Icons.person,
                        text: 'Account Information',
                        onTap: () => _editProfile(context),
                      ),
                      _buildProfileOption(
                        icon: Icons.location_on,
                        text: 'Delivery Addresses',
                        onTap: () => _manageAddresses(context),
                      ),
                      _buildProfileOption(
                        icon: Icons.favorite,
                        text: 'Favorite Restaurants',
                        onTap: () => _viewFavorites(context),
                      ),
                      _buildProfileOption(
                        icon: Icons.settings,
                        text: 'Settings',
                        onTap: () => _settings(context),
                      ),
                      _buildProfileOption(
                        icon: Icons.help,
                        text: 'Help & Support',
                        onTap: () => _helpSupport(context),
                      ),
                      _buildProfileOption(
                        icon: Icons.logout,
                        text: 'Logout',
                        onTap: () => _logout(context),
                        isLogout: true,
                      ),
                    ],
                  ),
                ),
              ),
            ),
    );
  }

  Widget _buildProfileOption({
    required IconData icon,
    required String text,
    required VoidCallback onTap,
    bool isLogout = false,
  }) {
    return Card(
      margin: EdgeInsets.only(bottom: 8),
      child: ListTile(
        leading: Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            color: isLogout ? Colors.red[100] : Colors.red[50],
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(
            icon,
            color: isLogout ? Colors.red[700] : Colors.red[400],
          ),
        ),
        title: Text(
          text,
          style: TextStyle(
            color: isLogout ? Colors.red[700] : null,
            fontWeight: isLogout ? FontWeight.bold : null,
          ),
        ),
        trailing: Icon(Icons.arrow_forward_ios, size: 16),
        onTap: onTap,
      ),
    );
  }

  void _editProfile(BuildContext context) {
    // In a real app, this would show an edit profile screen
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => EditProfileScreen(),
      ),
    );
  }

  void _manageAddresses(BuildContext context) {
    // In a real app, this would show address management screen
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => AddressManagementScreen(),
      ),
    );
  }

  void _viewFavorites(BuildContext context) {
    // In a real app, this would show favorite restaurants
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => FavoritesScreen(),
      ),
    );
  }

  void _settings(BuildContext context) {
    // In a real app, this would show settings screen
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => SettingsScreen(),
      ),
    );
  }

  void _helpSupport(BuildContext context) {
    // In a real app, this would show help and support screen
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Help & Support'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              leading: Icon(Icons.help),
              title: Text('Help Center'),
              onTap: () {
                // Open help center
                Navigator.pop(context);
              },
            ),
            ListTile(
              leading: Icon(Icons.chat),
              title: Text('Live Chat'),
              onTap: () {
                // Open live chat
                Navigator.pop(context);
              },
            ),
            ListTile(
              leading: Icon(Icons.email),
              title: Text('Email Support'),
              onTap: () {
                // Open email support
                Navigator.pop(context);
              },
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Close'),
          ),
        ],
      ),
    );
  }

  void _logout(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Logout'),
        content: Text('Are you sure you want to logout?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              // Perform logout
              final authService = Provider.of<AuthService>(context, listen: false);
              authService.logout();
              Navigator.of(context).pushReplacement(
                MaterialPageRoute(builder: (context) => LoginScreen()),
              );
            },
            child: Text('Logout', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}

class EditProfileScreen extends StatefulWidget {
  @override
  _EditProfileScreenState createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    final foodBuyerProvider = Provider.of<FoodBuyerProvider>(context);
    final userProfile = foodBuyerProvider.userProfile;

    if (userProfile != null) {
      _nameController.text = userProfile.name;
      _emailController.text = userProfile.email;
      _phoneController.text = userProfile.phone;
      _addressController.text = userProfile.address;
    }

    return Scaffold(
      appBar: AppBar(
        title: Text('Edit Profile'),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              // Profile picture
              Center(
                child: Stack(
                  children: [
                    Container(
                      width: 100,
                      height: 100,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: Colors.grey[300],
                        border: Border.all(
                          color: Colors.red[400]!,
                          width: 2,
                        ),
                      ),
                      child: ClipOval(
                        child: userProfile?.profilePictureUrl.isNotEmpty == true
                            ? Image.network(
                                userProfile!.profilePictureUrl,
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) {
                                  return Icon(
                                    Icons.person,
                                    size: 60,
                                    color: Colors.grey[600],
                                  );
                                },
                              )
                            : Icon(
                                Icons.person,
                                size: 60,
                                color: Colors.grey[600],
                              ),
                      ),
                    ),
                    Positioned(
                      bottom: 0,
                      right: 0,
                      child: Container(
                        width: 30,
                        height: 30,
                        decoration: BoxDecoration(
                          color: Colors.red[400],
                          shape: BoxShape.circle,
                        ),
                        child: Icon(
                          Icons.edit,
                          size: 16,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              SizedBox(height: 20),
              
              // Form fields
              TextFormField(
                controller: _nameController,
                decoration: InputDecoration(
                  labelText: 'Full Name',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter your name';
                  }
                  return null;
                },
              ),
              SizedBox(height: 15),
              TextFormField(
                controller: _emailController,
                decoration: InputDecoration(
                  labelText: 'Email',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter your email';
                  }
                  if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
                    return 'Please enter a valid email';
                  }
                  return null;
                },
              ),
              SizedBox(height: 15),
              TextFormField(
                controller: _phoneController,
                decoration: InputDecoration(
                  labelText: 'Phone Number',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter your phone number';
                  }
                  return null;
                },
              ),
              SizedBox(height: 15),
              TextFormField(
                controller: _addressController,
                decoration: InputDecoration(
                  labelText: 'Delivery Address',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter your delivery address';
                  }
                  return null;
                },
              ),
              SizedBox(height: 20),
              Container(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: _saveProfile,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.red[400],
                  ),
                  child: Text(
                    'Save Changes',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _saveProfile() {
    // In a real app, this would save the profile to the server
    if (_formKey.currentState!.validate()) {
      // Update profile in provider
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Profile updated successfully'),
        ),
      );
    }
  }
}

class AddressManagementScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Delivery Addresses'),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
      ),
      body: Center(
        child: Text('Address Management Screen'),
      ),
    );
  }
}

class FavoritesScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Favorite Restaurants'),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
      ),
      body: Center(
        child: Text('Favorite Restaurants Screen'),
      ),
    );
  }
}

class SettingsScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Settings'),
        backgroundColor: Colors.red[400],
        foregroundColor: Colors.white,
      ),
      body: Center(
        child: Text('Settings Screen'),
      ),
    );
  }
}

class LoginScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Login'),
      ),
      body: Center(
        child: Text('Login Screen'),
      ),
    );
  }
}