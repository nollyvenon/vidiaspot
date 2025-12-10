// lib/services/biometric_auth_service.dart
import 'package:local_auth/local_auth.dart';
import 'package:local_auth/auth_strings.dart';
import 'package:flutter/services.dart';

class BiometricAuthService {
  final LocalAuthentication _localAuth = LocalAuthentication();

  // Check if device supports biometric authentication
  Future<bool> checkBiometricSupport() async {
    try {
      bool canCheckBiometrics = await _localAuth.canCheckBiometrics;
      List<BiometricType> availableBiometrics = await _localAuth.getAvailableBiometrics();
      
      print('Can check biometrics: $canCheckBiometrics');
      print('Available biometrics: $availableBiometrics');
      
      return canCheckBiometrics && availableBiometrics.isNotEmpty;
    } catch (e) {
      print('Error checking biometric support: $e');
      return false;
    }
  }

  // Get list of available biometric types
  Future<List<BiometricType>> getAvailableBiometrics() async {
    try {
      return await _localAuth.getAvailableBiometrics();
    } catch (e) {
      print('Error getting available biometrics: $e');
      return [];
    }
  }

  // Authenticate using biometrics
  Future<bool> authenticateWithBiometrics({
    String localizedReason = 'Authenticate to access your crypto wallet',
    String androidSideTitle = 'Biometric Authentication',
    String androidSideDescription = 'Confirm your fingerprint or face to continue',
  }) async {
    try {
      // Define custom Android strings
      AndroidAuthMessages androidAuthStrings = AndroidAuthMessages(
        biometricHint: '',
        biometricNotRecognized: 'Biometric not recognized. Please try again.',
        biometricRequiredTitle: androidSideTitle,
        biometricSuccess: 'Authentication successful!',
        cancelButton: 'Cancel',
        goToSettingsButton: 'Settings',
        goToSettingsDescription: 'Please enable biometric authentication in settings.',
        deviceCredentialsRequiredTitle: 'Device Credentials Required',
        deviceCredentialsSetupDescription: 'Please set up screen lock on your device.',
        deviceCredentialsTitle: 'Device Credentials',
        deviceCredentialsDescription: 'Enter your device PIN, pattern, or password to continue.',
        fingerprintNotRecognized: 'Fingerprint not recognized. Please try again.',
        goToSettingsDescriptionPrefix: 'Please enable biometric authentication in settings.',
        signInTitle: localizedReason,
        deviceCredentialsSetupDescriptionPrefix: 'Please set up screen lock on your device.',
      );

      bool authenticated = await _localAuth.authenticate(
        localizedReason: localizedReason,
        authMessages: [androidAuthStrings],
        options: const AuthenticationOptions(
          biometricOnly: true, // Only allow biometric authentication, not device credentials
        ),
      );
      
      return authenticated;
    } on PlatformException catch (e) {
      print('Biometric authentication error: $e');
      return false;
    } catch (e) {
      print('Biometric authentication exception: $e');
      return false;
    }
  }

  // Authenticate with device credentials as fallback
  Future<bool> authenticateWithDeviceCredentials({
    String localizedReason = 'Authenticate to access your crypto wallet',
  }) async {
    try {
      bool authenticated = await _localAuth.authenticate(
        localizedReason: localizedReason,
        options: const AuthenticationOptions(
          useErrorDialogs: true,
          stickyAuth: true,
        ),
      );
      
      return authenticated;
    } on PlatformException catch (e) {
      print('Device credentials authentication error: $e');
      return false;
    } catch (e) {
      print('Device credentials authentication exception: $e');
      return false;
    }
  }

  // Authenticate with any available method
  Future<bool> authenticate({
    String localizedReason = 'Authenticate to access your crypto wallet',
  }) async {
    try {
      bool authenticated = await _localAuth.authenticate(
        localizedReason: localizedReason,
        options: const AuthenticationOptions(
          biometricOnly: false, // Allow both biometric and device credentials
          useErrorDialogs: true,
          stickyAuth: true,
        ),
      );
      
      return authenticated;
    } on PlatformException catch (e) {
      print('Authentication error: $e');
      return false;
    } catch (e) {
      print('Authentication exception: $e');
      return false;
    }
  }
}