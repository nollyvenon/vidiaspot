import 'package:flutter_tts/flutter_tts.dart';

class VoiceNavigationService {
  final FlutterTts _flutterTts = FlutterTts();
  bool _isInitialized = false;
  
  Future<bool> initialize() async {
    await _flutterTts.setLanguage("en-US");
    await _flutterTts.setSpeechRate(0.5);
    await _flutterTts.setVolume(1.0);
    await _flutterTts.setPitch(1.0);
    
    _isInitialized = true;
    return true;
  }
  
  Future<void> speak(String text) async {
    if (_isInitialized) {
      await _flutterTts.speak(text);
    }
  }
  
  Future<void> stop() async {
    if (_isInitialized) {
      await _flutterTts.stop();
    }
  }
  
  Future<void> setLanguage(String language) async {
    if (_isInitialized) {
      await _flutterTts.setLanguage(language);
    }
  }
  
  // Speak navigation instructions
  Future<void> speakNavigationInstruction(String instruction, {bool isUrgent = false}) async {
    if (!_isInitialized) return;
    
    // Adjust speech rate for urgent instructions
    if (isUrgent) {
      await _flutterTts.setSpeechRate(0.8);
    } else {
      await _flutterTts.setSpeechRate(0.5);
    }
    
    await _flutterTts.speak(instruction);
  }
  
  // Speak distance-based instructions
  Future<void> speakDistanceInstruction(double distanceInMeters) async {
    String instruction;
    
    if (distanceInMeters <= 50) {
      instruction = "Turn now";
    } else if (distanceInMeters <= 100) {
      instruction = "Turn in 100 meters";
    } else if (distanceInMeters <= 200) {
      instruction = "Turn in 200 meters";
    } else {
      double distanceInKm = distanceInMeters / 1000;
      instruction = "Turn in ${distanceInKm.toStringAsFixed(1)} kilometers";
    }
    
    await speakNavigationInstruction(instruction, isUrgent: distanceInMeters <= 100);
  }
}