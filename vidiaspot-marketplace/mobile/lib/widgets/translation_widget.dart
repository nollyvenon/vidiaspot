import 'package:flutter/material.dart';
import '../services/translation_service.dart';

class TranslationWidget extends StatefulWidget {
  final String text;
  final String? from;
  final String to;
  final TextStyle? style;
  final TextAlign? textAlign;
  final TextOverflow? overflow;
  final int? maxLines;

  const TranslationWidget({
    Key? key,
    required this.text,
    this.from,
    this.to = 'en',
    this.style,
    this.textAlign,
    this.overflow,
    this.maxLines,
  }) : super(key: key);

  @override
  _TranslationWidgetState createState() => _TranslationWidgetState();
}

class _TranslationWidgetState extends State<TranslationWidget> {
  String? _translatedText;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _translateText();
  }

  Future<void> _translateText() async {
    try {
      final service = TranslationService();
      String result = await service.translateText(
        widget.text,
        from: widget.from ?? 'auto',
        to: widget.to,
      );
      setState(() {
        _translatedText = result;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _translatedText = widget.text; // Fallback to original text
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading || _translatedText == null) {
      return Text(
        widget.text, // Show original text while loading
        style: widget.style?.copyWith(color: widget.style?.color?.withOpacity(0.7)) ??
               TextStyle(color: Colors.grey[600]),
        textAlign: widget.textAlign,
        overflow: widget.overflow,
        maxLines: widget.maxLines,
      );
    }

    return Text(
      _translatedText!,
      style: widget.style,
      textAlign: widget.textAlign,
      overflow: widget.overflow,
      maxLines: widget.maxLines,
    );
  }
}