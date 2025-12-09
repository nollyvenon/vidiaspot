// lib/widgets/voice_message_widget.dart
import 'package:flutter/material.dart';
import 'package:speech_to_text/speech_to_text.dart' as stt;
import 'package:flutter_tts/flutter_tts.dart';
import 'package:audioplayers/audioplayers.dart';

class VoiceMessageWidget extends StatefulWidget {
  final String? recordedAudioPath;
  final Function(String)? onMessageSent;
  final String currentLanguage;

  const VoiceMessageWidget({
    Key? key,
    this.recordedAudioPath,
    this.onMessageSent,
    this.currentLanguage = 'en',
  }) : super(key: key);

  @override
  _VoiceMessageWidgetState createState() => _VoiceMessageWidgetState();
}

class _VoiceMessageWidgetState extends State<VoiceMessageWidget> {
  final FlutterTts _flutterTts = FlutterTts();
  final stt.SpeechToText _speech = stt.SpeechToText();
  final AudioPlayer _audioPlayer = AudioPlayer();
  
  bool _isListening = false;
  bool _isPlaying = false;
  bool _isAvailable = false;
  String _recognizedText = '';

  @override
  void initState() {
    super.initState();
    _initSpeechRecognizer();
    _initTts();
  }

  Future<void> _initSpeechRecognizer() async {
    _isAvailable = await _speech.initialize();
  }

  Future<void> _initTts() async {
    await _flutterTts.setLanguage(widget.currentLanguage);
    await _flutterTts.setSpeechRate(0.5);
  }

  Future<void> _startListening() async {
    if (!_isAvailable) return;
    
    setState(() {
      _isListening = true;
    });
    
    _recognizedText = '';
    
    bool started = await _speech.listen(
      onResult: (val) {
        setState(() {
          _recognizedText = val.recognizedWords;
        });
      },
    );
    
    if (!started) {
      setState(() {
        _isListening = false;
      });
    }
  }

  void _stopListening() {
    _speech.stop();
    setState(() {
      _isListening = false;
    });
  }

  void _sendVoiceMessage() {
    if (_recognizedText.isNotEmpty) {
      widget.onMessageSent?.call(_recognizedText);
      setState(() {
        _recognizedText = '';
      });
    }
  }

  Future<void> _playAudio(String audioPath) async {
    setState(() {
      _isPlaying = true;
    });
    
    await _audioPlayer.play(UrlSource(audioPath)); // This would be the actual audio file
    
    _audioPlayer.onPlayerComplete.listen((event) {
      setState(() {
        _isPlaying = false;
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Voice Message',
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 16,
            ),
          ),
          SizedBox(height: 12),
          
          // Display recognized text
          if (_recognizedText.isNotEmpty)
            Container(
              padding: EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.grey[300]!),
              ),
              child: Text(
                _recognizedText,
                style: TextStyle(fontSize: 16),
              ),
            ),
          
          SizedBox(height: 16),
          
          // Recording controls
          Row(
            children: [
              Container(
                width: 50,
                height: 50,
                decoration: BoxDecoration(
                  color: _isListening ? Colors.red : Colors.blue,
                  shape: BoxShape.circle,
                ),
                child: GestureDetector(
                  onTapDown: (_) => _startListening(),
                  onTapUp: (_) => _stopListening(),
                  child: Icon(
                    _isListening ? Icons.stop : Icons.mic,
                    color: Colors.white,
                    size: 24,
                  ),
                ),
              ),
              SizedBox(width: 12),
              Expanded(
                child: Text(
                  _isListening ? 'Recording... Release to stop' : 'Tap and hold to record',
                  style: TextStyle(color: _isListening ? Colors.red : Colors.grey),
                ),
              ),
              if (_recognizedText.isNotEmpty) ...[
                SizedBox(width: 8),
                ElevatedButton(
                  onPressed: _sendVoiceMessage,
                  child: Text('Send'),
                ),
              ],
            ],
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _speech.stop();
    _flutterTts.stop();
    _audioPlayer.dispose();
    super.dispose();
  }
}