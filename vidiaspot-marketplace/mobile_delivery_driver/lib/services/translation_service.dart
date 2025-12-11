class TranslationService {
  static final TranslationService _instance = TranslationService._internal();
  factory TranslationService() => _instance;
  TranslationService._internal();

  // Supported languages including Nigerian languages
  static const Map<String, String> supportedLanguages = {
    'en': 'English',
    'fr': 'French',
    'pt': 'Portuguese',
    'ar': 'Arabic',
    'es': 'Spanish',
    'de': 'German',
    'zh': 'Chinese',
    'yo': 'Yoruba',
    'ig': 'Igbo',
    'ha': 'Hausa'
  };

  // For demo purposes, we'll return the same text
  // In a real app, you would integrate with a translation API
  Future<String> translateText(String text, {String from = 'auto', String to = 'en'}) async {
    try {
      if (text.isEmpty) return text;
      // This is a mock implementation - in a real app you would call an API
      print('Translation requested: "$text" from $from to $to');
      return text; // Return original text for now
    } catch (e) {
      print('Translation error: $e');
      return text; // Return original text if translation fails
    }
  }

  // Detect language of text (mock implementation)
  Future<String> detectLanguage(String text) async {
    try {
      if (text.isEmpty) return 'en';
      // This is a mock implementation
      return 'en'; // Return English as default
    } catch (e) {
      print('Language detection error: $e');
      return 'en';
    }
  }

  // Get supported languages
  Map<String, String> getSupportedLanguages() {
    return supportedLanguages;
  }

  // Get language code from name
  String getLanguageCode(String languageName) {
    return supportedLanguages.entries
        .firstWhere(
          (entry) => entry.value.toLowerCase() == languageName.toLowerCase(),
          orElse: () => const MapEntry('en', 'English'),
        )
        .key;
  }

  // Get language name from code
  String getLanguageName(String languageCode) {
    return supportedLanguages[languageCode] ?? 'English';
  }
}