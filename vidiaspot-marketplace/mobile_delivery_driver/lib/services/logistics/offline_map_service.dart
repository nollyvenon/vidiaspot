import 'dart:io';
import 'package:path_provider/path_provider.dart';

class OfflineMapService {
  static const String _mapCacheDirName = 'offline_maps';
  
  late Directory _mapCacheDir;
  
  Future<void> initialize() async {
    final appDir = await getApplicationDocumentsDirectory();
    _mapCacheDir = Directory('${appDir.path}/$_mapCacheDirName');
    
    // Create the directory if it doesn't exist
    if (!_mapCacheDir.existsSync()) {
      await _mapCacheDir.create();
    }
  }
  
  Future<bool> isMapAvailable(String mapId) async {
    if (!await _mapCacheDir.exists()) {
      return false;
    }
    
    final mapFile = File('${_mapCacheDir.path}/$mapId.map');
    return await mapFile.exists();
  }
  
  Future<void> cacheMap(String mapId, List<int> mapData) async {
    final mapFile = File('${_mapCacheDir.path}/$mapId.map');
    await mapFile.writeAsBytes(mapData);
  }
  
  Future<File?> getMapFile(String mapId) async {
    if (await isMapAvailable(mapId)) {
      return File('${_mapCacheDir.path}/$mapId.map');
    }
    return null;
  }
  
  Future<List<String>> getCachedMaps() async {
    if (!await _mapCacheDir.exists()) {
      return [];
    }
    
    final List<FileSystemEntity> files = _mapCacheDir.listSync();
    return files
        .whereType<File>()
        .map((file) => file.path.split('/').last.split('.').first)
        .toList();
  }
  
  Future<void> removeCachedMap(String mapId) async {
    final mapFile = File('${_mapCacheDir.path}/$mapId.map');
    if (await mapFile.exists()) {
      await mapFile.delete();
    }
  }
  
  Future<int> getCacheSize() async {
    if (!await _mapCacheDir.exists()) {
      return 0;
    }
    
    int totalSize = 0;
    final List<FileSystemEntity> files = _mapCacheDir.listSync();
    
    for (final file in files) {
      if (file is File) {
        totalSize += await file.length();
      }
    }
    
    return totalSize;
  }
  
  Future<void> clearCache() async {
    if (await _mapCacheDir.exists()) {
      final List<FileSystemEntity> files = _mapCacheDir.listSync();
      for (final file in files) {
        if (file is File) {
          await file.delete();
        }
      }
    }
  }
}