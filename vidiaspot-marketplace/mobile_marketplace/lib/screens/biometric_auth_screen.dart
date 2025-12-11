// lib/screens/biometric_auth_screen.dart
import 'package:flutter/material.dart';
import '../services/biometric_auth_service.dart';

class BiometricAuthScreen extends StatefulWidget {
  final Function(bool authenticated)? onAuthenticationComplete;
  
  const BiometricAuthScreen({Key? key, this.onAuthenticationComplete}) : super(key: key);

  @override
  _BiometricAuthScreenState createState() => _BiometricAuthScreenState();
}

class _BiometricAuthScreenState extends State<BiometricAuthScreen> {
  final BiometricAuthService _authService = BiometricAuthService();
  bool _isLoading = false;
  String _statusMessage = 'Checking biometric support...';
  IconData _statusIcon = Icons.fingerprint;
  Color _statusColor = Colors.blue;

  @override
  void initState() {
    super.initState();
    _checkBiometricSupport();
  }

  Future<void> _checkBiometricSupport() async {
    bool isSupported = await _authService.checkBiometricSupport();
    
    if (mounted) {
      setState(() {
        if (isSupported) {
          _statusMessage = 'Biometric authentication available';
          _statusIcon = Icons.lock_open;
          _statusColor = Colors.green;
        } else {
          _statusMessage = 'Biometric authentication not available';
          _statusIcon = Icons.lock;
          _statusColor = Colors.red;
        }
      });
    }
  }

  Future<void> _authenticate() async {
    setState(() {
      _isLoading = true;
      _statusMessage = 'Authenticating...';
      _statusIcon = Icons.hourglass_empty;
      _statusColor = Colors.orange;
    });

    try {
      bool isAuthenticated = await _authService.authenticate(
        localizedReason: 'Authenticate to access your crypto wallet and trading features',
      );

      if (mounted) {
        if (isAuthenticated) {
          setState(() {
            _statusMessage = 'Authentication successful!';
            _statusIcon = Icons.check_circle;
            _statusColor = Colors.green;
          });

          // Notify parent widget of successful authentication
          if (widget.onAuthenticationComplete != null) {
            widget.onAuthenticationComplete!(true);
          }

          // Navigate to main app after successful authentication
          Future.delayed(const Duration(seconds: 1), () {
            Navigator.of(context).pushReplacementNamed('/home');
          });
        } else {
          setState(() {
            _statusMessage = 'Authentication failed. Please try again.';
            _statusIcon = Icons.error;
            _statusColor = Colors.red;
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _statusMessage = 'Authentication error: $e';
          _statusIcon = Icons.error;
          _statusColor = Colors.red;
        });
      }
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [Colors.blue, Colors.purple],
          ),
        ),
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Logo/Icon
                Icon(
                  _statusIcon,
                  size: 100,
                  color: _statusColor,
                ),
                const SizedBox(height: 32),
                
                // Status message
                Text(
                  _statusMessage,
                  style: TextStyle(
                    fontSize: 18,
                    color: Colors.white,
                    fontWeight: FontWeight.w500,
                  ),
                  textAlign: TextAlign.center,
                ),
                
                const SizedBox(height: 48),
                
                // Biometric button
                SizedBox(
                  width: 200,
                  height: 60,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _authenticate,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: _statusColor,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(30),
                      ),
                      elevation: 5,
                    ),
                    child: _isLoading
                        ? const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              SizedBox(
                                width: 16,
                                height: 16,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  valueColor: AlwaysStoppedAnimation<Color>(Colors.blue),
                                ),
                              ),
                              SizedBox(width: 12),
                              Text(
                                'Authenticating...',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          )
                        : Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.fingerprint, size: 24),
                              const SizedBox(width: 8),
                              Text(
                                'Use Biometrics',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                  ),
                ),
                
                const SizedBox(height: 24),
                
                // Alternative authentication method
                TextButton(
                  onPressed: () {
                    // Navigate to PIN/password screen as fallback
                    Navigator.of(context).pushReplacementNamed('/login');
                  },
                  child: const Text(
                    'Use PIN/Password instead',
                    style: TextStyle(
                      color: Colors.white70,
                      decoration: TextDecoration.underline,
                    ),
                  ),
                ),
                
                const SizedBox(height: 48),
                
                // Security info
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Column(
                    children: [
                      Text(
                        'Security Information',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                        ),
                      ),
                      SizedBox(height: 8),
                      Text(
                        'Your biometric data is stored securely on your device and never shared with our servers. Biometric authentication provides an additional layer of security for accessing your cryptocurrency assets.',
                        style: TextStyle(
                          color: Colors.white70,
                          fontSize: 14,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}