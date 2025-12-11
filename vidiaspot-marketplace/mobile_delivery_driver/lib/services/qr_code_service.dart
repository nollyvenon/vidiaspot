// lib/services/qr_code_service.dart
import 'dart:typed_data';
import 'dart:ui' as ui;
import 'package:flutter/material.dart';
import 'package:qr_flutter/qr_flutter.dart';

class QrCodeService {
  // Generate QR code as a Widget
  static Widget generateQrCodeWidget({
    required String data,
    double size = 200.0,
    Color foregroundColor = Colors.black,
    Color backgroundColor = Colors.white,
  }) {
    return QrImageView(
      data: data,
      version: QrVersions.auto,
      foregroundColor: foregroundColor,
      backgroundColor: backgroundColor,
      size: size,
      gapless: false,
    );
  }

  // Generate QR code as an image bytes (for sharing or saving)
  static Future<Uint8List?> generateQrCodeImage({
    required String data,
    double size = 200.0,
    Color foregroundColor = Colors.black,
    Color backgroundColor = Colors.white,
  }) async {
    try {
      final ui.PictureRecorder pictureRecorder = ui.PictureRecorder();
      final Canvas canvas = Canvas(pictureRecorder);
      
      // Create QR code painter
      final QrCode qrCode = QrCode(4, QrErrorCorrectLevel.M);
      qrCode.addData(data);
      qrCode.make();
      final QrPainter painter = QrPainter(
        dataModuleStyle: QrDataModuleStyle(
          color: foregroundColor,
          dataModuleShape: QrDataModuleShape.square,
        ),
        eyeStyle: QrEyeStyle(
          color: foregroundColor,
          eyeShape: QrEyeShape.square,
        ),
        qrCode: qrCode,
      );

      // Paint the QR code
      await painter.paintImage(
        canvas,
        Offset.zero & Size(size, size),
      );

      // Convert to image
      final ui.Picture picture = pictureRecorder.endRecording();
      final ui.Image image = await picture.toImage(size.toInt(), size.toInt());
      final ByteData? byteData = await image.toByteData(format: ui.ImageByteFormat.png);
      
      return byteData?.buffer.asUint8List();
    } catch (e) {
      print('Error generating QR code image: $e');
      return null;
    }
  }

  // Generate wallet address QR code with formatted data
  static Widget generateWalletQrCode({
    required String address,
    required String cryptoSymbol,
    double amount = 0.0,
    String label = '',
    String message = '',
    double size = 200.0,
  }) {
    String qrData = _formatWalletQrData(
      address: address,
      cryptoSymbol: cryptoSymbol,
      amount: amount,
      label: label,
      message: message,
    );
    
    return generateQrCodeWidget(
      data: qrData,
      size: size,
    );
  }

  // Format QR data according to cryptocurrency standards
  static String _formatWalletQrData({
    required String address,
    required String cryptoSymbol,
    double amount = 0.0,
    String label = '',
    String message = '',
  }) {
    // For Bitcoin, Ethereum, and other cryptocurrencies, use standard URI format
    // Format: cryptocurrency:address?parameters
    
    String formattedAddress = address;
    String lowerSymbol = cryptoSymbol.toLowerCase();
    
    if (lowerSymbol == 'btc') {
      // Bitcoin URI format: bitcoin:address?amount=x&label=y&message=z
      String params = '';
      if (amount > 0) params += 'amount=$amount';
      if (label.isNotEmpty) {
        params += params.isEmpty ? '' : '&';
        params += 'label=$label';
      }
      if (message.isNotEmpty) {
        params += params.isEmpty ? '' : '&';
        params += 'message=$message';
      }
      return params.isEmpty 
          ? 'bitcoin:$formattedAddress' 
          : 'bitcoin:$formattedAddress?$params';
    } else if (['eth', 'usdt', 'usdc'].contains(lowerSymbol)) {
      // Ethereum and ERC-20 tokens: ethereum:address?value=amount
      String params = '';
      if (amount > 0) params += 'value=${(amount * 1e18).toInt()}'; // Convert to wei
      if (label.isNotEmpty) {
        params += params.isEmpty ? '' : '&';
        params += 'label=$label';
      }
      if (message.isNotEmpty) {
        params += params.isEmpty ? '' : '&';
        params += 'message=$message';
      }
      return params.isEmpty 
          ? 'ethereum:$formattedAddress' 
          : 'ethereum:$formattedAddress?$params';
    } else {
      // For other cryptocurrencies, use a generic format
      return formattedAddress;
    }
  }

  // Generate payment QR code
  static Widget generatePaymentQrCode({
    required String recipientAddress,
    required String cryptoSymbol,
    double amount = 0.0,
    String description = '',
    String referenceId = '',
    double size = 200.0,
  }) {
    String qrData = _formatPaymentQrData(
      recipientAddress: recipientAddress,
      cryptoSymbol: cryptoSymbol,
      amount: amount,
      description: description,
      referenceId: referenceId,
    );
    
    return generateQrCodeWidget(
      data: qrData,
      size: size,
    );
  }

  // Format payment QR data
  static String _formatPaymentQrData({
    required String recipientAddress,
    required String cryptoSymbol,
    double amount = 0.0,
    String description = '',
    String referenceId = '',
  }) {
    // Create a JSON object with payment details
    Map<String, dynamic> paymentData = {
      'recipient': recipientAddress,
      'currency': cryptoSymbol,
      'amount': amount,
      'description': description,
      'reference': referenceId,
      'timestamp': DateTime.now().millisecondsSinceEpoch,
    };
    
    // Convert to JSON string
    String jsonData = _mapToJsonString(paymentData);
    
    // For security, in a real implementation you would sign this data
    return jsonData;
  }

  // Helper to convert map to JSON string (simplified)
  static String _mapToJsonString(Map<String, dynamic> map) {
    List<String> pairs = [];
    map.forEach((key, value) {
      String valueStr;
      if (value is String) {
        valueStr = '"$value"';
      } else if (value is num) {
        valueStr = value.toString();
      } else {
        valueStr = value.toString();
      }
      pairs.add('"$key":$valueStr');
    });
    return '{${pairs.join(',')}}';
  }
}