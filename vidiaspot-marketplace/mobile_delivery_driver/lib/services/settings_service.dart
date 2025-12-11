import 'package:shared_preferences/shared_preferences.dart';

class SettingsService {
  static final SettingsService _instance = SettingsService._internal();
  factory SettingsService() => _instance;
  SettingsService._internal();

  static const String _languageKey = 'app_language';
  static const String _themeKey = 'app_theme';
  static const String _notificationsKey = 'notifications_enabled';

  SharedPreferences? _prefs;

  Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // Language settings
  String getLanguage() {
    return _prefs?.getString(_languageKey) ?? 'en';
  }

  Future<bool> setLanguage(String languageCode) async {
    return await _prefs?.setString(_languageKey, languageCode) ?? false;
  }

  // Theme settings
  String getTheme() {
    return _prefs?.getString(_themeKey) ?? 'system';
  }

  Future<bool> setTheme(String theme) async {
    return await _prefs?.setString(_themeKey, theme) ?? false;
  }

  // Notification settings
  bool getNotificationsEnabled() {
    return _prefs?.getBool(_notificationsKey) ?? true;
  }

  Future<bool> setNotificationsEnabled(bool enabled) async {
    return await _prefs?.setBool(_notificationsKey, enabled) ?? false;
  }
}