// lib/screens/qr_code_scanner_screen.dart
import 'package:flutter/material.dart';
import 'package:qr_code_scanner/qr_code_scanner.dart';
import '../services/qr_code_service.dart';
import '../services/crypto_p2p_service.dart';
import 'dart:io';
import '../models/crypto_p2p/crypto_listing_model.dart';

class QrCodeScannerScreen extends StatefulWidget {
  final Function(Map<String, dynamic> scannedData)? onQrCodeScanned;

  const QrCodeScannerScreen({Key? key, this.onQrCodeScanned}) : super(key: key);

  @override
  State<QrCodeScannerScreen> createState() => _QrCodeScannerScreenState();
}

class _QrCodeScannerScreenState extends State<QrCodeScannerScreen> {
  final GlobalKey qrKey = GlobalKey(debugLabel: 'QR');
  Barcode? result;
  QRViewController? qrController;

  @override
  void reassemble() {
    super.reassemble();
    if (Platform.isAndroid) {
      qrController!.pauseCamera();
    } else if (Platform.isIOS) {
      qrController!.resumeCamera();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Scan QR Code'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Column(
        children: <Widget>[
          Expanded(
            flex: 5,
            child: QRView(
              key: qrKey,
              onQRViewCreated: _onQRViewCreated,
              overlay: QrScannerOverlayShape(
                borderColor: Colors.green,
                borderRadius: 10,
                borderLength: 30,
                borderWidth: 10,
                cutOutSize: 300,
              ),
            ),
          ),
          Expanded(
            flex: 1,
            child: Container(
              padding: const EdgeInsets.all(16),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: <Widget>[
                  if (result != null)
                    Text(
                      'Scanned: ${result!.code.length > 30 ? result!.code.substring(0, 30) + '...' : result!.code}',
                      textAlign: TextAlign.center,
                      style: const TextStyle(fontSize: 12),
                    )
                  else
                    const Text('Point camera at QR code to scan'),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: <Widget>[
                      ElevatedButton(
                        onPressed: () async {
                          await qrController?.toggleFlash();
                          setState(() {});
                        },
                        child: FutureBuilder(
                          future: qrController?.getFlashStatus(),
                          builder: (context, snapshot) {
                            return Text('Flash: ${snapshot.data ?? ''}');
                          },
                        ),
                      ),
                      ElevatedButton(
                        onPressed: () async {
                          await qrController?.pauseCamera();
                        },
                        child: const Text('Pause'),
                      ),
                      ElevatedButton(
                        onPressed: () async {
                          await qrController?.resumeCamera();
                        },
                        child: const Text('Resume'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          )
        ],
      ),
    );
  }

  void _onQRViewCreated(QRViewController controller) {
    this.qrController = controller;
    controller.scannedDataStream.listen((scanData) {
      setState(() {
        result = scanData;
        // Handle scanned QR code
        _handleScannedData(scanData.code);
      });
    });
  }

  void _handleScannedData(String? scannedData) {
    if (scannedData == null) return;

    print('Scanned QR code: $scannedData');

    // Process the scanned data based on its content
    if (scannedData.startsWith('bitcoin:') ||
        scannedData.startsWith('ethereum:') ||
        _isValidCryptoAddress(scannedData)) {
      _processCryptoAddress(scannedData);
    } else if (_isValidJsonData(scannedData)) {
      _processPaymentData(scannedData);
    } else if (scannedData.startsWith('crypto_payment:')) {
      _processCryptoPayment(scannedData);
    } else {
      // Handle as generic data
      _processGenericData(scannedData);
    }
  }

  bool _isValidCryptoAddress(String data) {
    // Simple validation for crypto addresses
    // This could be expanded based on specific requirements
    return data.length >= 26 && data.length <= 64;
  }

  bool _isValidJsonData(String data) {
    // Check if the string is valid JSON
    try {
      return data.startsWith('{') && data.endsWith('}');
    } catch (e) {
      return false;
    }
  }

  void _processCryptoAddress(String address) {
    // Extract address and parameters from standard URI format
    String extractedAddress = address;
    String cryptoSymbol = 'BTC'; // Default

    if (address.startsWith('bitcoin:')) {
      extractedAddress = address.substring(8);
      cryptoSymbol = 'BTC';
    } else if (address.startsWith('ethereum:')) {
      extractedAddress = address.substring(9);
      cryptoSymbol = 'ETH';
    }

    // Extract parameters if they exist
    String cleanAddress = extractedAddress;
    double amount = 0.0;
    String label = '';
    String message = '';

    if (extractedAddress.contains('?')) {
      List<String> parts = extractedAddress.split('?');
      cleanAddress = parts[0];

      // Parse parameters
      List<String> paramPairs = parts[1].split('&');
      for (String pair in paramPairs) {
        List<String> keyValue = pair.split('=');
        if (keyValue.length == 2) {
          String key = keyValue[0].toLowerCase();
          String value = keyValue[1];
          switch (key) {
            case 'amount':
              amount = double.tryParse(value) ?? 0.0;
              break;
            case 'label':
              label = Uri.decodeComponent(value);
              break;
            case 'message':
              message = Uri.decodeComponent(value);
              break;
          }
        }
      }
    }

    // Navigate back with crypto address data
    if (widget.onQrCodeScanned != null) {
      widget.onQrCodeScanned!({
        'type': 'crypto_address',
        'address': cleanAddress,
        'cryptoSymbol': cryptoSymbol,
        'amount': amount,
        'label': label,
        'message': message,
      });
    } else {
      // Navigate to send crypto screen if no callback provided
      Navigator.of(context).pop({
        'type': 'crypto_address',
        'address': cleanAddress,
        'cryptoSymbol': cryptoSymbol,
        'amount': amount,
        'label': label,
        'message': message,
      });
    }
  }

  void _processPaymentData(String jsonData) {
    // Process payment data from JSON QR code
    // This would contain payment details in a structured format
    print('Processing payment data: $jsonData');

    if (widget.onQrCodeScanned != null) {
      widget.onQrCodeScanned!({
        'type': 'payment_data',
        'data': jsonData,
      });
    } else {
      Navigator.of(context).pop({
        'type': 'payment_data',
        'data': jsonData,
      });
    }
  }

  void _processCryptoPayment(String paymentData) {
    // Process crypto payment formatted as: crypto_payment:address:amount:symbol
    if (paymentData.startsWith('crypto_payment:')) {
      List<String> parts = paymentData.substring(15).split(':');
      if (parts.length >= 3) {
        String address = parts[0];
        double amount = double.tryParse(parts[1]) ?? 0.0;
        String symbol = parts[2];

        if (widget.onQrCodeScanned != null) {
          widget.onQrCodeScanned!({
            'type': 'crypto_payment',
            'address': address,
            'amount': amount,
            'cryptoSymbol': symbol,
          });
        } else {
          Navigator.of(context).pop({
            'type': 'crypto_payment',
            'address': address,
            'amount': amount,
            'cryptoSymbol': symbol,
          });
        }
      }
    }
  }

  void _processGenericData(String data) {
    // Handle generic data
    if (widget.onQrCodeScanned != null) {
      widget.onQrCodeScanned!({
        'type': 'generic',
        'data': data,
      });
    } else {
      // Show dialog with the scanned data
      showDialog(
        context: context,
        builder: (BuildContext context) {
          return AlertDialog(
            title: const Text('Scanned Data'),
            content: SelectableText(data),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(),
                child: const Text('OK'),
              ),
            ],
          );
        },
      );
    }
  }

  @override
  void dispose() {
    qrController?.dispose();
    super.dispose();
  }
}