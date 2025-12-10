// lib/screens/accessibility_settings_screen.dart
import 'package:flutter/material.dart';
import 'package:flutter_tts/flutter_tts.dart';
import '../services/accessibility_service.dart';

class AccessibilitySettingsScreen extends StatefulWidget {
  const AccessibilitySettingsScreen({Key? key}) : super(key: key);

  @override
  _AccessibilitySettingsScreenState createState() => _AccessibilitySettingsScreenState();
}

class _AccessibilitySettingsScreenState extends State<AccessibilitySettingsScreen> {
  final AccessibilityService _accessibilityService = AccessibilityService();
  final FlutterTts _flutterTts = FlutterTts();
  
  bool _isHighContrast = false;
  bool _isLargeText = false;
  bool _reduceMotion = false;
  bool _voiceCommands = false;
  double _textSizeMultiplier = 1.0;

  @override
  void initState() {
    super.initState();
    _loadAccessibilitySettings();
  }

  Future<void> _loadAccessibilitySettings() async {
    _isHighContrast = _accessibilityService.isHighContrast;
    _isLargeText = _accessibilityService.isLargeText;
    _reduceMotion = _accessibilityService.reduceMotion;
    _voiceCommands = _accessibilityService.voiceCommands;
    
    setState(() {});
  }

  Future<void> _toggleHighContrast(bool value) async {
    await _accessibilityService.setHighContrast(value);
    setState(() {
      _isHighContrast = value;
    });
  }

  Future<void> _toggleLargeText(bool value) async {
    await _accessibilityService.setLargeText(value);
    setState(() {
      _isLargeText = value;
    });
  }

  Future<void> _toggleReduceMotion(bool value) async {
    await _accessibilityService.setReduceMotion(value);
    setState(() {
      _reduceMotion = value;
    });
  }

  Future<void> _toggleVoiceCommands(bool value) async {
    await _accessibilityService.setVoiceCommands(value);
    setState(() {
      _voiceCommands = value;
    });
  }

  void _speakText(String text) async {
    if (_voiceCommands) {
      await _flutterTts.speak(text);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Accessibility Settings'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // High contrast toggle
            _buildSettingCard(
              icon: Icons.contrast,
              title: 'High Contrast',
              subtitle: 'Increase contrast between text and background',
              child: Switch(
                value: _isHighContrast,
                onChanged: _toggleHighContrast,
                activeColor: Colors.blue,
              ),
              onTap: () => _toggleHighContrast(!_isHighContrast),
            ),
            
            const SizedBox(height: 12),
            
            // Large text toggle
            _buildSettingCard(
              icon: Icons.text_fields,
              title: 'Large Text',
              subtitle: 'Increase text size throughout the app',
              child: Switch(
                value: _isLargeText,
                onChanged: _toggleLargeText,
                activeColor: Colors.blue,
              ),
              onTap: () => _toggleLargeText(!_isLargeText),
            ),
            
            const SizedBox(height: 12),
            
            // Reduce motion toggle
            _buildSettingCard(
              icon: Icons.motion_photos_off,
              title: 'Reduce Motion',
              subtitle: 'Minimize animations and motion effects',
              child: Switch(
                value: _reduceMotion,
                onChanged: _toggleReduceMotion,
                activeColor: Colors.blue,
              ),
              onTap: () => _toggleReduceMotion(!_reduceMotion),
            ),
            
            const SizedBox(height: 12),
            
            // Voice commands toggle
            _buildSettingCard(
              icon: Icons.record_voice_over,
              title: 'Voice Commands',
              subtitle: 'Enable voice navigation and commands',
              child: Switch(
                value: _voiceCommands,
                onChanged: _toggleVoiceCommands,
                activeColor: Colors.blue,
              ),
              onTap: () => _toggleVoiceCommands(!_voiceCommands),
            ),
            
            const SizedBox(height: 24),
            
            // Text size slider
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Icon(Icons.format_size, color: Colors.blue[600]),
                        const SizedBox(width: 12),
                        const Text(
                          'Text Size Multiplier',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Smaller', style: TextStyle(fontSize: 12)),
                        Expanded(
                          child: Slider(
                            value: _textSizeMultiplier,
                            min: 0.8,
                            max: 1.5,
                            divisions: 7,
                            label: '${(_textSizeMultiplier * 100).round()}%',
                            onChanged: (value) {
                              setState(() {
                                _textSizeMultiplier = value;
                              });
                              _speakText('Text size set to ${(_textSizeMultiplier * 100).round()} percent');
                            },
                          ),
                        ),
                        const Text('Larger', style: TextStyle(fontSize: 12)),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Accessibility information
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Accessibility Features',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildInfoItem(
                      'Screen Reader Support',
                      'Full compatibility with screen readers like TalkBack and VoiceOver',
                    ),
                    _buildInfoItem(
                      'Voice Navigation',
                      'Navigate the app using voice commands',
                    ),
                    _buildInfoItem(
                      'Adjustable Text Size',
                      'Customize text size to your preference',
                    ),
                    _buildInfoItem(
                      'High Contrast Mode',
                      'Enhanced contrast for better visibility',
                    ),
                  ],
                ),
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Test accessibility
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(8),
              ),
              child: Column(
                children: [
                  const Text(
                    'Test Accessibility Features',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  ElevatedButton(
                    onPressed: () {
                      _speakText('This is a test of the accessibility features');
                    },
                    child: const Text('Test Voice Output'),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSettingCard({
    required IconData icon,
    required String title,
    required String subtitle,
    required Widget child,
    required VoidCallback onTap,
  }) {
    return Card(
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Icon(icon, color: Colors.blue[600], size: 24),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      subtitle,
                      style: const TextStyle(
                        fontSize: 14,
                        color: Colors.grey,
                      ),
                    ),
                  ],
                ),
              ),
              child,
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInfoItem(String title, String description) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            description,
            style: const TextStyle(
              fontSize: 12,
              color: Colors.grey,
            ),
          ),
        ],
      ),
    );
  }
}