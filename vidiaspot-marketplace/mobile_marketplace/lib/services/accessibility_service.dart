// lib/services/accessibility_service.dart
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:shared_preferences/shared_preferences.dart';

class AccessibilityService extends ChangeNotifier {
  static const String _prefHighContrast = 'high_contrast';
  static const String _prefLargeText = 'large_text';
  static const String _prefReduceMotion = 'reduce_motion';
  static const String _prefVoiceCommands = 'voice_commands';

  bool _isHighContrast = false;
  bool _isLargeText = false;
  bool _reduceMotion = false;
  bool _voiceCommands = false;

  bool get isHighContrast => _isHighContrast;
  bool get isLargeText => _isLargeText;
  bool get reduceMotion => _reduceMotion;
  bool get voiceCommands => _voiceCommands;

  double get textScaleFactor {
    if (_isLargeText) {
      return 1.5; // 150% text size
    }
    return 1.0; // Normal text size
  }

  double get contrastScaleFactor {
    if (_isHighContrast) {
      return 1.2; // Enhanced contrast
    }
    return 1.0; // Normal contrast
  }

  AccessibilityService() {
    _loadPreferences();
  }

  Future<void> _loadPreferences() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();

    _isHighContrast = prefs.getBool(_prefHighContrast) ?? false;
    _isLargeText = prefs.getBool(_prefLargeText) ?? false;
    _reduceMotion = prefs.getBool(_prefReduceMotion) ?? false;
    _voiceCommands = prefs.getBool(_prefVoiceCommands) ?? false;

    notifyListeners();
  }

  Future<void> setHighContrast(bool value) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_prefHighContrast, value);
    _isHighContrast = value;
    notifyListeners();
  }

  Future<void> setLargeText(bool value) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_prefLargeText, value);
    _isLargeText = value;
    notifyListeners();
  }

  Future<void> setReduceMotion(bool value) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_prefReduceMotion, value);
    _reduceMotion = value;
    notifyListeners();
  }

  Future<void> setVoiceCommands(bool value) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_prefVoiceCommands, value);
    _voiceCommands = value;
    notifyListeners();
  }

  // Apply accessibility themes
  ThemeData getAccessibilityTheme(ThemeData baseTheme) {
    if (!_isHighContrast) {
      return baseTheme;
    }

    // Create high contrast theme
    return baseTheme.copyWith(
      brightness: Brightness.light,
      primaryColor: Colors.black,
      primaryColorLight: Colors.black,
      primaryColorDark: Colors.black,
      canvasColor: Colors.white,
      scaffoldBackgroundColor: Colors.white,
      cardColor: Colors.white,
      dividerColor: Colors.black,
      focusColor: Colors.black,
      hoverColor: Colors.grey[300],
      highlightColor: Colors.grey[400],
      splashColor: Colors.grey[500],
      unselectedWidgetColor: Colors.grey[700],
      disabledColor: Colors.grey[500],
      buttonTheme: baseTheme.buttonTheme.copyWith(
        buttonColor: Colors.black,
        textTheme: ButtonTextTheme.primary,
      ),
      textTheme: baseTheme.textTheme.apply(
        bodyColor: Colors.black,
        displayColor: Colors.black,
      ),
    );
  }

  // Create text style for better accessibility
  TextStyle getAccessibleTextStyle(TextStyle originalStyle, {bool isHighContrast = false}) {
    TextStyle modifiedStyle = originalStyle;
    
    if (_isLargeText) {
      modifiedStyle = modifiedStyle.copyWith(
        fontSize: (originalStyle.fontSize ?? 14) * 1.2,
        height: (originalStyle.height ?? 1.2) * 1.1,
      );
    }
    
    if (isHighContrast) {
      modifiedStyle = modifiedStyle.copyWith(
        color: Colors.black,
        fontWeight: FontWeight.bold,
      );
    }
    
    return modifiedStyle;
  }

  // Enhanced button with accessibility features
  Widget buildAccessibleButton({
    required VoidCallback onPressed,
    required Widget child,
    Color? backgroundColor,
    Color? foregroundColor,
    double? elevation,
    EdgeInsetsGeometry? padding,
    ShapeBorder? shape,
  }) {
    return ElevatedButton(
      onPressed: onPressed,
      style: ElevatedButton.styleFrom(
        backgroundColor: backgroundColor ?? Colors.blue,
        foregroundColor: foregroundColor ?? Colors.white,
        elevation: elevation ?? 2,
        padding: padding ?? EdgeInsets.symmetric(horizontal: 20.w, vertical: 12.h),
        shape: shape ?? RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8.r),
        ),
      ),
      child: child,
    );
  }

  // Enhanced text field with accessibility
  Widget buildAccessibleTextField({
    required TextEditingController controller,
    String? hintText,
    String? labelText,
    TextInputType? keyboardType,
    bool obscureText = false,
    Widget? prefixIcon,
    Widget? suffixIcon,
    int? maxLines,
    int? minLines,
    bool? enabled,
    FormFieldValidator<String>? validator,
  }) {
    return TextField(
      controller: controller,
      style: TextStyle(
        fontSize: _isLargeText ? 18.sp : 14.sp,
        color: _isHighContrast ? Colors.black : null,
      ),
      decoration: InputDecoration(
        hintText: hintText,
        labelText: labelText,
        hintStyle: TextStyle(
          fontSize: _isLargeText ? 16.sp : 12.sp,
          color: _isHighContrast ? Colors.grey[700] : null,
        ),
        labelStyle: TextStyle(
          fontSize: _isLargeText ? 16.sp : 14.sp,
          color: _isHighContrast ? Colors.black : null,
        ),
        filled: true,
        fillColor: _isHighContrast ? Colors.grey[100] : null,
        border: _isHighContrast 
          ? OutlineInputBorder(
              borderSide: BorderSide(color: Colors.black, width: 2.w),
            )
          : null,
        enabledBorder: _isHighContrast 
          ? OutlineInputBorder(
              borderSide: BorderSide(color: Colors.black, width: 2.w),
            )
          : null,
        focusedBorder: _isHighContrast 
          ? OutlineInputBorder(
              borderSide: BorderSide(color: Colors.black, width: 3.w),
            )
          : null,
        prefixIcon: prefixIcon,
        suffixIcon: suffixIcon,
      ),
      keyboardType: keyboardType,
      obscureText: obscureText,
      maxLines: maxLines,
      minLines: minLines,
      enabled: enabled,
    );
  }

  // Enhanced slider with accessibility
  Widget buildAccessibleSlider({
    required double value,
    required double min,
    required double max,
    required ValueChanged<double> onChanged,
    Color? activeColor,
    Color? inactiveColor,
  }) {
    return Slider(
      value: value,
      min: min,
      max: max,
      divisions: (max - min).toInt() * 10, // More precise divisions
      label: value.toStringAsFixed(2),
      activeColor: _isHighContrast ? Colors.black : activeColor,
      inactiveColor: _isHighContrast ? Colors.grey : inactiveColor,
      onChanged: onChanged,
    );
  }

  // Accessibility-aware list tile
  Widget buildAccessibleListTile({
    Widget? leading,
    Widget? title,
    Widget? subtitle,
    Widget? trailing,
    GestureTapCallback? onTap,
    bool selected = false,
    bool isThreeLine = false,
    bool dense = false,
  }) {
    return ListTile(
      leading: leading,
      title: title,
      subtitle: subtitle != null 
        ? Text(
            subtitle.toString(),
            style: TextStyle(
              fontSize: _isLargeText ? 16.sp : null,
              color: _isHighContrast ? Colors.black : null,
            ),
          )
        : null,
      trailing: trailing,
      onTap: onTap,
      selected: selected,
      isThreeLine: isThreeLine,
      dense: dense,
      contentPadding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 8.h),
      tileColor: _isHighContrast ? Colors.grey[100] : null,
    );
  }

  // Enhanced dialog for accessibility
  Future<T?> showAccessibleDialog<T>({
    required BuildContext context,
    required WidgetBuilder builder,
  }) {
    return showDialog<T>(
      context: context,
      builder: (BuildContext context) {
        return Dialog(
          backgroundColor: _isHighContrast ? Colors.white : null,
          shape: _isHighContrast
              ? RoundedRectangleBorder(
                  side: BorderSide(color: Colors.black, width: 2.w),
                  borderRadius: BorderRadius.circular(8.r),
                )
              : null,
          child: Builder(
            builder: (BuildContext context) {
              return MediaQuery(
                data: MediaQuery.of(context).copyWith(
                  textScaleFactor: textScaleFactor,
                ),
                child: builder(context),
              );
            },
          ),
        );
      },
    );
  }

  // Function to check if system accessibility settings are enabled
  bool isSystemAccessibilityEnabled() {
    // This would normally check system settings
    // For now, we'll return true if any of our settings are enabled
    return _isHighContrast || _isLargeText || _reduceMotion || _voiceCommands;
  }

  // Get appropriate accessibility text scale
  double getAccessibilityTextScale() {
    if (_isLargeText) {
      return 1.5;
    }
    return 1.0;
  }

  // Get appropriate contrast level
  double getContrastLevel() {
    if (_isHighContrast) {
      return 1.2;
    }
    return 1.0;
  }
}