import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'login_screen.dart';
import '../../core/services/auth_service.dart';
import 'shop_owner_dashboard.dart';

class SplashScreen extends StatefulWidget {
  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuthStatus();
  }

  void _checkAuthStatus() async {
    await Future.delayed(Duration(seconds: 2)); // Simulate loading time
    
    final authService = Provider.of<AuthService>(context, listen: false);
    await authService.loadStoredCredentials();
    
    if (authService.isAuthenticated) {
      // Navigate to dashboard if already logged in
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => ShopOwnerDashboard()),
      );
    } else {
      // Navigate to login screen if not authenticated
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => LoginScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.blue,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.store,
              size: 100,
              color: Colors.white,
            ),
            SizedBox(height: 20),
            Text(
              'VidiaSpot Shop Owner',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            SizedBox(height: 20),
            CircularProgressIndicator(
              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
            ),
          ],
        ),
      ),
    );
  }
}