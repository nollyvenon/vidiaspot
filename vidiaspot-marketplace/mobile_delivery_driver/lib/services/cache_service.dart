import 'package:hive/hive.dart';
import 'dart:convert';
import 'package:path_provider/path_provider.dart';
import 'dart:io';

class CacheService {
  static final CacheService _instance = CacheService._internal();
  factory CacheService() => _instance;
  CacheService._internal();

  Box? _cacheBox;

  Future<void> init() async {
    final dir = await getApplicationDocumentsDirectory();
    Hive.init(dir.path);
    
    // Register adapters if needed
    _cacheBox = await Hive.openBox('cache');
  }

  // Cache data with expiration
  Future<void> put(String key, dynamic data, {Duration expiration = const Duration(hours: 1)}) async {
    if (_cacheBox == null) return;
    
    final cacheData = {
      'data': data is String ? data : jsonEncode(data),
      'timestamp': DateTime.now().millisecondsSinceEpoch,
      'expiresIn': expiration.inMilliseconds,
      'isJson': data is! String,
    };
    
    await _cacheBox?.put(key, cacheData);
  }

  // Get cached data
  dynamic get(String key) {
    if (_cacheBox == null) return null;
    
    final cacheData = _cacheBox?.get(key);
    if (cacheData == null) return null;
    
    final timestamp = cacheData['timestamp'] as int;
    final expiresIn = cacheData['expiresIn'] as int;
    final isExpired = DateTime.now().millisecondsSinceEpoch - timestamp > expiresIn;
    
    if (isExpired) {
      delete(key);
      return null;
    }
    
    final data = cacheData['data'];
    final isJson = cacheData['isJson'] as bool;
    
    if (isJson) {
      return jsonDecode(data as String);
    }
    
    return data;
  }

  // Check if key exists and is not expired
  bool contains(String key) {
    if (_cacheBox == null) return false;
    return get(key) != null;
  }

  // Delete specific key
  Future<void> delete(String key) async {
    if (_cacheBox == null) return;
    await _cacheBox?.delete(key);
  }

  // Clear all cache
  Future<void> clear() async {
    if (_cacheBox == null) return;
    await _cacheBox?.clear();
  }

  // Get cache size
  int getCacheSize() {
    if (_cacheBox == null) return 0;
    final dir = Directory(Hive.box('cache').path ?? '');
    if (!dir.existsSync()) return 0;
    
    int size = 0;
    dir.listSync(recursive: true).forEach((file) {
      if (file is File) {
        size += file.lengthSync();
      }
    });
    
    return size;
  }
}